<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    private $post;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    private $author;

    #[ORM\Column(type: 'text')]
    #[
        Assert\NotBlank(message: 'This field should not be blank.'),
        Assert\Length(
            min: 5,
            max: 1000,
            minMessage: 'Comment must be at least 5 characters',
            maxMessage: 'Comment con not be more than 10000 characters'
        )
    ]
    private $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private $postedAt;

    public function __construct()
    {
        $this->postedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getPostedAt(): ?\DateTimeImmutable
    {
        return $this->postedAt;
    }

    public function setPostedAt(\DateTimeImmutable $postedAt): void
    {
        $this->postedAt = $postedAt;
    }
}
