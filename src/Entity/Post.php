<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(
    fields: 'slug',
    message: 'This title was already used in another blog post, but they must be unique.',
    errorPath: 'title',
)]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Assert\NotBlank(message: 'This field should not be blank.'),
        Assert\Length(min: 50, minMessage: 'Title must be at least 50 characters')
    ]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Assert\NotBlank(message: 'This field should not be blank'),
        Assert\Length(max: 255)
    ]
    private $summary;

    #[ORM\Column(type: 'text')]
    #[
        Assert\NotBlank(message: 'This field should not be blank'),
        Assert\Length(min: 10, minMessage: 'Content must be at least 10 characters')
    ]
    private $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private $publishedAt;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $postViews;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["persist"])]
    #[JoinColumn(nullable: false)]
    private $author;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, cascade: ["persist"], orphanRemoval: true)]
    private $comments;

    public function __construct()
    {
        $this->publishedAt = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

//    #[Assert\Length(min: 50, minMessage: 'Title must be at least 50 characters')]
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): void
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }
    }

    public function removeComment(Comment $comment): void
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getPostViews(): ?int
    {
        return $this->postViews;
    }

    /**
     * @param mixed $postViews
     */
    public function setPostViews(int $postViews): void
    {
        $this->postViews = $postViews;
    }

}
