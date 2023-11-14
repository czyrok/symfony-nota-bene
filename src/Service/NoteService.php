<?php

namespace App\Service;

use App\Entity\Note;
use App\Entity\User;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class NoteService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly NoteRepository $noteRepository
    ) {}

    public function getAllNotes(bool $onlyPublicNotes = false): array {
        if ($onlyPublicNotes) {
            return $this->noteRepository->getPublicNotes();
        }

        return $this->noteRepository->findAll();
    }

    public function getNote(int $noteId): ?Note {
        return $this->noteRepository->findOneBy(array('id' => $noteId));
    }

    public function saveNote(Note $note): void {
        $this->entityManager->persist($note);
        $this->entityManager->flush();
    }

    public function addNoteToUser(Note $note, int $categoryId, User $currentUser): void {
        $note->setAuthor($currentUser);
        $note->setIsPublic(false);

        $noteCategory = null;

        foreach ($currentUser->getCategories() as &$userCategory) {
            if ($userCategory->getId() === $categoryId) {
                $noteCategory = $userCategory;
            }
        }

        if (!$noteCategory) {
            throw new NotFoundHttpException("Category not found");
        }

        $note->setCategory($noteCategory);

        $this->saveNote($note);
    }

    public function likeNote(int $noteId, User $currentUser): void {
        $note = $this->noteRepository->findOneBy(array('id' => $noteId));

        if (!$note) {
            throw new NotFoundHttpException("Note not found");
        }

        if (!$note->isPublic()) {
            throw new AccessDeniedHttpException("You can't like a private note");
        }

        $hasAlreadyLiked = $this->hasLikedNote($note, $currentUser);

        if ($hasAlreadyLiked) {
            $note->removeUsersLike($currentUser);
        } else {
            $note->addUsersLike($currentUser);
        }
        
        $this->saveNote($note);
    }

    public function toggleIsPublicField(int $noteId, ?User $currentUser = null): void {
        $note = $this->noteRepository->findOneBy(array('id' => $noteId));

        if (!$note) {
            throw new NotFoundHttpException("Note not found");
        }

        if ($currentUser && $currentUser->getId() !== $note->getAuthor()->getId()) {
            throw new AccessDeniedHttpException("You can't edit a note of a different user");
        }

        if ($note->isPublic()) {
            $note->setIsPublic(false);
        } else {
            $note->setIsPublic(true);
        }
        
        $this->saveNote($note);
    }

    public function hasLikedNote(Note $note, User $currentUser): bool {
        foreach ($note->getUsersLike() as &$userLike) {
            if ($userLike->getId() === $currentUser->getId()) {
                return true;
            }
        }

        return false;
    }
}