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
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="articles")
     *
     * Grace à la ligne de commande "bin/console make:entity" on update l'entitée Article pour ajouter un élément
     * "category.id" qui va faire le lien entre la table "article" et la table "category"
     * /!\ ne pas oublier qu'après avoir créé une entitée, il faut écrire en command line "make:migration" pour
     * enregistrer le changement pour "doctrine:migrations:migrate" pour l'envoyer en BDD (fonctionnement identique
     * à git)
     *
     * Le "ManyToOne" représente la cardinalité qui lie les tables, ici il faut comprendre : "il peut y avoir plsrs
     * article pour UNE seule categorie
     * /!\ c'est toujours "ManyToOne" qui sera la table "maitre", à comprendre : que l'on va sélectionner dans le
     * "make:entity" + dans laquelle sera inséré l'id de la seconde table
     *
     * Le premier élément du mapping est la route vers l'entitée "Category" qui indique la table avec laquelle on
     * désire faire une jointure;
     *
     * Le deuxième élément représente le nom de la nouvelle propriété créée dans l'entitée Category (ici "articles")
     * et le inversedby la relation par rapport à l'entitée Article
     * => " Pour l'entitée Article, c'est une relation many to one (il peut y avoir MANY articles TO ONE category)
     * mais pour l'entitée Category c'est L'INVERSE, une relation one to many (ONE category TO MANY articles)
     */
    private $category;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

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


    /*
     * Les getters et setters de la jointure sont identiques aux autres ici, mais pas dans l'entitée Category
     */
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
