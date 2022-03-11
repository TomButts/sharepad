<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NoteRepository;

class NoteController extends AbstractController
{
    /**
     * @Route("/note", name="app_note")
     */
    public function index(): Response
    {
        return $this->render('Notepad/index.html.twig');
    }

    public function notes(Request $request, NoteRepository $noteRepository): Response
    {
        // $notes = $noteRepository->findBy(['user' => $user]);

        return $this->json(['notes' => [
            [
                'id' => 1,
                'title' => 'Title 1',
                'body' => 'This is a character limited version of what is in the note',
                'date' => '2022-03-03 12:00:00'
            ],
            [
                'id' => 2,
                'title' => 'Title 2',
                'body' => 'This is a TEST NOTE',
                'date' => '2022-03-03 12:00:00'
            ],
        ]]);
    }
}
