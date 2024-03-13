<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Authorization;
use App\Entity\Note;
use App\Repository\NoteRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

use function PHPSTORM_META\map;

class NoteController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('Notepad/index.html.twig');
    }

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
            // todo: logging package
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
}
