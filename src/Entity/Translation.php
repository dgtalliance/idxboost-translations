<?php

namespace App\Entity;

use App\Repository\TranslationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TranslationRepository::class)
 */
class Translation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Term::class, inversedBy="translations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $termId;

    /**
     * @ORM\ManyToOne(targetEntity=Language::class, inversedBy="translations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $languageId;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTermId(): ?Term
    {
        return $this->termId;
    }

    public function setTermId(?Term $termId): self
    {
        $this->termId = $termId;

        return $this;
    }

    public function getLanguageId(): ?Language
    {
        return $this->languageId;
    }

    public function setLanguageId(?Language $languageId): self
    {
        $this->languageId = $languageId;

        return $this;
    }
}
