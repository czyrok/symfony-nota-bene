<?php

namespace App\Controller;

use App\Entity\Note;
use App\Service\NoteService;
use App\Type\NoteCreationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    #[Route(path: '/note/add', name: 'app_note_add')]
    public function addNote(#[MapQueryParameter] int $categoryId, Request $request, NoteService $noteService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $note = new Note();

        $noteCreationForm = $this->createForm(NoteCreationType::class, $note);
        $noteCreationForm->handleRequest($request);

        if ($noteCreationForm->isSubmitted() && $noteCreationForm->isValid()) {
            $noteService->addNoteToUser($note, $categoryId, $currentUser);

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('note/add-form.html.twig', [
            'noteCreationForm' => $noteCreationForm
        ]);
    }

    #[Route('/note/{noteId}', 'app_note_one')]
    public function view(int $noteId, NoteService $noteService): Response {
        $note = $noteService->getNote($noteId);

        if (!$note) {
            throw new NotFoundHttpException("Note not found");
        }

        return $this->render('note/view.html.twig', [
            'note' => $note
        ]);
    }

    #[Route('/private/{privateLink}', 'app_private_one')]
    public function private(int $privateLink): Response {
        return $this->render('note/view.html.twig');
    }
}
