<?php

namespace App\Entity;

use App\Repository\TermRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TermRepository::class)
 */
class Term
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $termKey;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;


    /**
     * @ORM\OneToMany(targetEntity=Translation::class, mappedBy="termId", orphanRemoval=true)
     */
    private $translations;

    /**
     * @ORM\OneToMany(targetEntity=ApplicationTerm::class, mappedBy="termId", orphanRemoval=true)
     */
    private $applicationTerms;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->applicationTerms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTermKey(): ?string
    {
        return $this->termKey;
    }

    public function setTermKey(string $termKey): self
    {
        $this->termKey = $termKey;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }



    /**
     * @return Collection<int, Translation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(Translation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTermId($this);
        }

        return $this;
    }

    public function removeTranslation(Translation $translation): self
    {
        if ($this->translations->removeElement($translation)) {
            // set the owning side to null (unless already changed)
            if ($translation->getTermId() === $this) {
                $translation->setTermId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ApplicationTerm>
     */
    public function getApplicationTerms(): Collection
    {
        return $this->applicationTerms;
    }

    public function addApplicationTerm(ApplicationTerm $applicationTerm): self
    {
        if (!$this->applicationTerms->contains($applicationTerm)) {
            $this->applicationTerms[] = $applicationTerm;
            $applicationTerm->setTermId($this);
        }

        return $this;
    }

    public function removeApplicationTerm(ApplicationTerm $applicationTerm): self
    {
        if ($this->applicationTerms->removeElement($applicationTerm)) {
            // set the owning side to null (unless already changed)
            if ($applicationTerm->getTermId() === $this) {
                $applicationTerm->setTermId(null);
            }
        }

        return $this;
    }
}
