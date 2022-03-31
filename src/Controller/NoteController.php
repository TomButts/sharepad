<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Note;
use App\Repository\NoteRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Node\BodyNode;

class NoteController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('Notepad/index.html.twig');
    }

    public function notes(NoteRepository $noteRepository): JsonResponse
    {
        $notes = $noteRepository->findBy(['user' => $this->getUser()]);

        return $this->json(['notes' => $notes], Response::HTTP_OK, [], ['groups' => 'note']);
    }

    public function addNote(EntityManagerInterface $em): JsonResponse
    {
        $note = new Note();
        $note->setUser($this->getUser());

        $em->persist($note);
        $em->flush();

        return $this->json(['note' => [
            'id' => $note->getId(),
            'title' => '',
            'body' => '',
            'updated_at' => $note->getUpdatedAt()->format('H:m:i d-m-Y'),
        ]]);
    }

    public function saveNote(Request $request, NoteRepository $noteRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = $request->getContent();

        if (!empty($data)) {
            $data = json_decode($data);
        }

        $id = filter_var($data->id, FILTER_VALIDATE_INT);
        $local = filter_var($data->local, FILTER_VALIDATE_BOOL);
        $body = filter_var($data->body, FILTER_UNSAFE_RAW);

        if (true === $local) {
            // todo CRUD 'maker' class
            $note = new Note();

            $note->setBody($body);
            $note->setUpdatedAt(new DateTimeImmutable());
            $note->setUser($this->getUser());

            $em->persist($note);
            $em->flush();

            return $this->json(['note' => $note], Response::HTTP_OK, [], ['groups' => 'note']);
        }

        $note = $noteRepository->find($id);

        if (null === $note) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        $note->setBody($body);
        $note->setUpdatedAt(new DateTimeImmutable());

        $em->persist($note);
        $em->flush();
        
        return $this->json(['note' => $note], Response::HTTP_OK, [], ['groups' => 'note']);
    }
}
