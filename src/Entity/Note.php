<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 50,
        maxMessage: 'The title cannot be longer than {{ limit }} characters',
    )]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The content cannot be longer than {{ limit }} characters',
    )]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secretLink = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'likedNotes')]
    private Collection $usersLike;

    #[ORM\Column]
    private ?bool $isPublic = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'notes')]
    private Collection $tags;

    public function __construct()
    {
        $this->usersLike = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSecretLink(): ?string
    {
        return $this->secretLink;
    }

    public function setSecretLink(?string $secretLink): static
    {
        $this->secretLink = $secretLink;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersLike(): Collection
    {
        return $this->usersLike;
    }

    public function addUsersLike(User $usersLike): static
    {
        if (!$this->usersLike->contains($usersLike)) {
            $this->usersLike->add($usersLike);
            $usersLike->addLikedNote($this);
        }

        return $this;
    }

    public function removeUsersLike(User $usersLike): static
    {
        if ($this->usersLike->removeElement($usersLike)) {
            $usersLike->removeLikedNote($this);
        }

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
