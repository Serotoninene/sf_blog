<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Length(
     *     min = 4,
     *     max = 255,
     *     minMessage="Le titre est trop court !",
     *     maxMessage="Le titre est trop looooong !",
     *     allowEmptyString= false
     * )
     *
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull (
     *      message = "Vous n'avez inséré aucun contenu !"
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Url(
     *     message = "Ce n'est pas un URL"
     * )
     *
     */
    private $image;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotNull(
     *     message = " Date de publication a indiquer !"
     * )
     */
    private $publicationDate;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull(
     *     message = " Date de créatiion a indiquer !"
     * )
     */
    private $creationDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="articles")
     */
    private $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
