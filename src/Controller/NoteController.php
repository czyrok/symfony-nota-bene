<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    #[Route('/explore', name: 'app_explore')]
    public function explore(): Response
    {
        return $this->render('note/explore.html.twig');
    }

    #[Route('/search', name: 'app_search')]
    public function search(): Response
    {
        return $this->render('note/search.html.twig');
    }

    #[Route('/note/{noteId}', 'app_note_one')]
    public function view(int $noteId): Response {
        return $this->render('note/view.html.twig');
    }

    #[Route('/private/{privateLink}', 'app_private_one')]
    public function private(int $privateLink): Response {
        return $this->render('note/view.html.twig');
    }
}
