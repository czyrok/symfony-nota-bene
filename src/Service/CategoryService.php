<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CategoryService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly CategoryRepository $categoryRepository
    ) {}

    public function getCategory(int $categoryId): ?Category {
        return $this->categoryRepository->findOneBy(array('id' => $categoryId));
    }

    public function saveCategory(Category $category): void {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function addCategoryToUser(Category $category, User $currentUser) {
        $category->setUser($currentUser);

        $this->saveCategory($category);
    }

    public function deleteCategory(int $categoryId, User $currentUser = null): void {
        $category = $this->getCategory($categoryId);

        if (!$category) {
            throw new NotFoundHttpException("Category not found");
        }

        if ($currentUser->getId() !== $category->getUser()->getId()) {
            throw new AccessDeniedHttpException("You can't delete a category of a different user");
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}