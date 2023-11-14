<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TagService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly TagRepository $tagRepository
    ) {}

    public function getAllTags(): array {
        return $this->tagRepository->findAll();
    }

    public function saveTag(Tag $tag) {
        $this->entityManager->persist($tag);
        $this->entityManager->flush();
    }
}