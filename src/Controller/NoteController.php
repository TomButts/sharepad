<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class NoteController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('Notepad/index.html.twig');
    }

    /**
     * Load all notes that are editable for the logged in user.
     *
     * @param Request       $request
     * @param Authorization $authorisation
     *
     * @return JsonResponse
     */
    public function notes(Request $request, Authorization $authorisation): JsonResponse
    {
        $notes = $this->getUser()->getAllEditableNotes();

        $clientNoteURIs = [];

        foreach ($notes as $note) {
            $clientNoteURIs[] = sprintf('/note/%d', $note->getId());
        }

        // set client to be authorised to editable notes only, using mecure cookie auth
        // @see https://symfony.com/doc/current/mercure.html#programmatically-setting-the-cookie
        $authorisation->setCookie($request, $clientNoteURIs, $clientNoteURIs);

        return $this->json(['notes' => $notes], Response::HTTP_OK, [], ['groups' => ['note']]);
    }

    /**
     * Save a new or existing note.
     *
     * @param Request                $request
     * @param NoteRepository         $noteRepository
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     */
    public function saveNote(
        Request $request,
        NoteRepository $noteRepository,
        EntityManagerInterface $em,
        HubInterface $hub
    ): JsonResponse {
        $data = $request->getContent();

        if (!empty($data)) {
            $data = json_decode($data);
        }

        $noteId = filter_var($data->id, FILTER_VALIDATE_INT);
        $body = filter_var($data->body, FILTER_UNSAFE_RAW);

        if (0 === $noteId) {
            $note = new Note();
            $note->setOwner($this->getUser());
        } else {
            if (!$this->getUser()->hasAccessToNote($noteId)) {
                return $this->json(['message' => 'Could not update resource.'], Response::HTTP_FORBIDDEN);
            }

            $note = $noteRepository->find(['id' => $noteId]);
        }

        if (null === $note) {
            // todo: Feature - install monolog
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        $note->setBody($body);
        $note->setUpdatedAt(new DateTimeImmutable());

        $em->persist($note);
        $em->flush();

        $updatedNoteEvent = new Update(
            sprintf('/note/%d', $note->getId()),
            json_encode([
                'id' => $note->getId(),
                'body' => $note->getBody()
            ]),
            true
        );

        $hub->publish($updatedNoteEvent);

        return $this->json(['note' => $note], Response::HTTP_OK, [], ['groups' => 'note']);
    }

    /**
     * Add a participant to a note and push the new participants to all users who can edit that note.
     *
     * @param Request                      $request
     * @param NoteRepository               $noteRepository
     * @param UserRepository               $userRepository
     * @param EntityManagerInterface       $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param HubInterface                 $hub
     *
     * @return JsonResponse
     */
    public function addParticipant(
        Request $request,
        NoteRepository $noteRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        HubInterface $hub
    ): JsonResponse {
        $data = $request->getContent();

        if (!empty($data)) {
            $data = json_decode($data);
        }

        $noteId = filter_var($data->note_id, FILTER_VALIDATE_INT);
        $participantEmail = filter_var($data->email, FILTER_SANITIZE_EMAIL);

        if (!$this->getUser()->isNoteOwner($noteId)) {
            // For errors that would be expected to involve malicious intent do not return message information
            return $this->json([], Response::HTTP_FORBIDDEN);
        }

        $note = $noteRepository->find($noteId);

        if (null === $note) {
            return $this->json(['message' => 'Could not find note!'], Response::HTTP_NOT_FOUND);
        }

        $participant = $userRepository->findOneBy(['email' => $participantEmail]);

        if (null === $participant) {
            // todo: Feature - Create temporary account with email and link to anon login
            $participant = new User();
            $participant->setEmail($participantEmail);
            $participant->setRoles(['ROLE_USER']);

            $temporaryPassword = $passwordEncoder->encodePassword($participant, 'password');

            $participant->setPassword($temporaryPassword);

            $em->persist($participant);
        } elseif ($this->getUser()->getId() === $participant->getId()) {
            return $this->json(['message' => 'The note owner can not be added as a participant.'], Response::HTTP_BAD_REQUEST);
        }

        $note->addParticipant($participant);

        $em->flush();

        $noteEventPayload = json_encode([
            'id' => $note->getId(),
               'participants' => $note->getParticipantEmails(),
        ]);

           $updatedNoteEvent = new Update(sprintf('/note/%d', $note->getId()), $noteEventPayload, true);

          $hub->publish($updatedNoteEvent);

        return $this->json(['message' => 'Participant added successfully'], Response::HTTP_OK);
    }

    /**
     * Remove participants from a note and publish update.
     *
     * @param Request                $request
     * @param NoteRepository         $noteRepository
     * @param UserRepository         $userRepository
     * @param EntityManagerInterface $em
     * @param HubInterface           $hub
     *
     * @return JsonResponse
     */
    public function removeParticipant(
        Request $request,
        NoteRepository $noteRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        HubInterface $hub
    ): JsonResponse {
        $data = $request->getContent();

        if (!empty($data)) {
            $data = json_decode($data);
        }

        $noteId = filter_var($data->note_id, FILTER_VALIDATE_INT);
        $participantEmail = filter_var($data->email, FILTER_SANITIZE_EMAIL);

        if (!$this->getUser()->isNoteOwner($noteId)) {
            return $this->json(['message' => ''], Response::HTTP_FORBIDDEN);
        }

        $note = $noteRepository->find($noteId);

        if (null === $note) {
            return $this->json(['message' => 'Could not find note!'], Response::HTTP_NOT_FOUND);
        }

        $participant = $userRepository->findOneBy(['email' => $participantEmail]);

        if (null === $participant) {
            // todo: Potential bug - Make sure foreign keys are in place so that user deletion cascades and avoids orphaned share pivot table issues
            // todo: log this!
            return $this->json([], Response::HTTP_OK);
        }

        $note->removeParticipant($participant);

        $em->flush();

        $updatedNoteEvent = new Update(
            sprintf('/note/%d', $note->getId()),
            json_encode([
                'id' => $note->getId(),
                'participants' => $note->getParticipantEmails(),
            ]),
            true
        );

        $hub->publish($updatedNoteEvent);

        return $this->json(['message' =>  'Participant removed successfully!'], Response::HTTP_OK);
    }
}
