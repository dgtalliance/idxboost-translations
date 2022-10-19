<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApplicationRepository::class)
 */
class Application
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=ApplicationTerm::class, mappedBy="applicationId", orphanRemoval=true)
     */
    private $applicationTerms;



    public function __construct()
    {
        $this->applicationTerms = new ArrayCollection();
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

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
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
            $applicationTerm->setApplicationId($this);
        }

        return $this;
    }

    public function removeApplicationTerm(ApplicationTerm $applicationTerm): self
    {
        if ($this->applicationTerms->removeElement($applicationTerm)) {
            // set the owning side to null (unless already changed)
            if ($applicationTerm->getApplicationId() === $this) {
                $applicationTerm->setApplicationId(null);
            }
        }

        return $this;
    }

}
