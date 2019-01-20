<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrickRepository")
 */
class Trick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cover;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="trick_id")
     */
    private $comments_id;

    public function __construct()
    {
        $this->comments_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getCommentsId(): Collection
    {
        return $this->comments_id;
    }

    public function addCommentsId(Comments $commentsId): self
    {
        if (!$this->comments_id->contains($commentsId)) {
            $this->comments_id[] = $commentsId;
            $commentsId->setTrickId($this);
        }

        return $this;
    }

    public function removeCommentsId(Comments $commentsId): self
    {
        if ($this->comments_id->contains($commentsId)) {
            $this->comments_id->removeElement($commentsId);
            // set the owning side to null (unless already changed)
            if ($commentsId->getTrickId() === $this) {
                $commentsId->setTrickId(null);
            }
        }

        return $this;
    }
}
