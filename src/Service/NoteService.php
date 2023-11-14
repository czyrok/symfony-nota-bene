<?php

namespace App\Service;

use App\Entity\Note;
use App\Entity\User;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class NoteService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly NoteRepository $noteRepository
    ) {}

    public function getNote(int $noteId): ?Note {
        return $this->noteRepository->findOneBy(array('id' => $noteId));
    }

    public function addNoteToUser(Note $note, int $categoryId, User $currentUser): void {
        $note->setAuthor($currentUser);

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

        $this->entityManager->persist($note);
        $this->entityManager->flush();
    }
}