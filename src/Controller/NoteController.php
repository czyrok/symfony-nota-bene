<?php

namespace App\Controller;

use App\Entity\Note;
use App\Service\NoteService;
use App\Type\NoteEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    #[Route('/explore', name: 'app_explore')]
    public function explore(NoteService $noteService): Response
    {
        $notes = $noteService->getAllNotes(true);

        return $this->render('note/explore.html.twig', [
            'notes' => $notes
        ]);
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

        $noteCreationForm = $this->createForm(NoteEditType::class, $note);
        $noteCreationForm->handleRequest($request);

        if ($noteCreationForm->isSubmitted() && $noteCreationForm->isValid()) {
            $noteService->addNoteToUser($note, $categoryId, $currentUser);

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('note/edit-form.html.twig', [
            'noteCreationForm' => $noteCreationForm
        ]);
    }

    #[Route('/note/{noteId}', 'app_note_one')]
    public function view(int $noteId, NoteService $noteService): Response {
        $note = $noteService->getNote($noteId);

        if (!$note) {
            throw new NotFoundHttpException("Note not found");
        }

        $currentUser = $this->getUser();
        $currentUserHasLikedNote = null;

        if ($currentUser) {
            $currentUserHasLikedNote = $noteService->hasLikedNote($note, $currentUser);
        }

        if (!$note->isPublic() && $currentUser && $note->getAuthor()->getId() !== $currentUser->getId()) {
            throw new AccessDeniedHttpException("You can't view a private note of a different user");
        }

        if (!$note->isPublic() && !$currentUser) {
            throw new AccessDeniedHttpException("You can't view a private note of a different user");
        }

        return $this->render('note/view.html.twig', [
            'note' => $note,
            'currentUserHasLikedNote' => $currentUserHasLikedNote
        ]);
    }

    #[Route('/note/{noteId}/like', 'app_note_one_like')]
    public function like(int $noteId, NoteService $noteService): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $noteService->likeNote($noteId, $currentUser);

        return $this->redirectToRoute('app_note_one', ['noteId' => $noteId]);
    }

    #[Route('/note/{noteId}/public', 'app_note_one_public')]
    public function toggleIsPublicField(int $noteId, NoteService $noteService): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $noteService->toggleIsPublicField($noteId, $currentUser);

        return $this->redirectToRoute('app_note_one', ['noteId' => $noteId]);
    }

    #[Route(path: '/note/{noteId}/edit', name: 'app_note_one_edit')]
    public function editNote(int $noteId, Request $request, NoteService $noteService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $note = $noteService->getNote($noteId);

        if (!$note) {
            throw new NotFoundHttpException("Note not found");
        }

        if ($note->getAuthor()->getId() !== $currentUser->getId()) {
            throw new AccessDeniedHttpException("You can't edit a note of a different user");
        }

        $noteCreationForm = $this->createForm(NoteEditType::class, $note);
        $noteCreationForm->handleRequest($request);

        if ($noteCreationForm->isSubmitted() && $noteCreationForm->isValid()) {
            $noteService->saveNote($note);

            return $this->redirectToRoute('app_note_one', [
                'noteId' => $noteId
            ]);
        }

        return $this->render('note/edit-form.html.twig', [
            'noteCreationForm' => $noteCreationForm,
            'note' => $note
        ]);
    }

    #[Route(path: '/note/{noteId}/delete', name: 'app_note_one_delete')]
    public function deleteNote(int $noteId, NoteService $noteService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $noteService->deleteNote($noteId, $currentUser);

        return $this->redirectToRoute('app_profile');
    }

    #[Route(path: '/note/{noteId}/delete/confirmation', name: 'app_note_one_delete_confirmation')]
    public function deleteNoteConfirmation(int $noteId, NoteService $noteService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $note = $noteService->getNote($noteId);

        if (!$note) {
            throw new NotFoundHttpException("Note not found");
        }

        if ($note->getAuthor()->getId() !== $currentUser->getId()) {
            throw new AccessDeniedHttpException("You can't delete a note of a different user");
        }

        return $this->render('note/delete-confirmation.html.twig', [
            'note' => $note
        ]);
    }

    #[Route('/private/{privateLink}', 'app_private_one')]
    public function private(int $privateLink): Response {
        return $this->render('note/view.html.twig');
    }
}
