<?php

namespace App\Repository;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 *
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function getPublicNotes(): array {
        return $this->createQueryBuilder('n')
            ->andWhere('n.isPublic = true')
            ->leftJoin('n.usersLike', 'ul')
            ->addSelect('COUNT(ul.id) AS HIDDEN likesCount')
            ->groupby('n.id')
            ->orderBy('likesCount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getFilteredPublicNotes(?string $authorUsername, ?string $title, ArrayCollection $tags, ?bool $onlyLikedByCurrentUser, ?User $currentUser): array {
        $query = $this->createQueryBuilder('n')
            ->andWhere('n.isPublic = true')
            ->leftJoin('n.usersLike', 'ul')
            ->addSelect('COUNT(ul.id) AS HIDDEN likesCount')
            ->groupby('n.id')
            ->orderBy('likesCount', 'DESC');

        if ($authorUsername !== null) {
            $query = $query
                ->innerJoin('n.author', 'a')
                ->andWhere('a.username LIKE :authorUsername')
                ->setParameter('authorUsername', '%' . $authorUsername . '%');
        }

        if ($title !== null) {
            $query = $query
                ->andWhere('n.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }

        if ($tags->count() > 0) {
            $query = $query
                ->innerJoin('n.tags', 't')
                ->andWhere('t IN (:tags)')
                ->setParameter('tags', $tags);
        }

        if ($onlyLikedByCurrentUser === true && $currentUser !== null) {
            $query = $query
                ->andWhere('ul.id = :currentUserId')
                ->setParameter('currentUserId', $currentUser->getId());
        }
        
        return $query->getQuery()
            ->getResult();
    }
}
