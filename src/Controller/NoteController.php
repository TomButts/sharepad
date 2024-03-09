<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Note;
use App\Repository\NoteRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class NoteController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('Notepad/index.html.twig');
    }

    public function notes(NoteRepository $noteRepository): JsonResponse
    {
        $notes = $noteRepository->findBy(['owner' => $this->getUser()], ['updated_at' => 'DESC']);

        return $this->json(['notes' => $notes], Response::HTTP_OK, [], ['groups' => 'note']);
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
    public function saveNote(Request $request, NoteRepository $noteRepository, EntityManagerInterface $em): JsonResponse
    {
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
            $note = $noteRepository->findOneBy(['id' => $noteId, 'owner' => $this->getUser()]);
        }

        if (null === $note) {
            // todo: set up monolog

            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        $note->setBody($body);
        $note->setUpdatedAt(new DateTimeImmutable());

        $em->persist($note);
        $em->flush();

        return $this->json(['note' => $note], Response::HTTP_OK, [], ['groups' => 'note']);
    }
}
