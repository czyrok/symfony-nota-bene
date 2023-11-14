<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CategoryService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {}

    public function addCategoryToUser(Category $category, User $currentUser) {
        $category->setUser($currentUser);

        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }
}