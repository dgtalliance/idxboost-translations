<?php

namespace App\Entity;

use App\Repository\ApplicationTermRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApplicationTermRepository::class)
 */
class ApplicationTerm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Application::class, inversedBy="applicationTerms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $applicationId;

    /**
     * @ORM\ManyToOne(targetEntity=Term::class, inversedBy="applicationTerms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $termId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplicationId(): ?Application
    {
        return $this->applicationId;
    }

    public function setApplicationId(?Application $applicationId): self
    {
        $this->applicationId = $applicationId;

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
}
