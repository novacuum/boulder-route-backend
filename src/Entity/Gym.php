<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']], itemOperations: ['get', 'delete'])]
#[ApiFilter(SearchFilter::class, properties: ['countryCode' => 'exact'])]
#[Table(name: 'gym')]
class Gym {
    #[Column(type: 'integer')]
    #[GeneratedValue]
    #[Id]
    #[Groups(groups: ['read'])]
    private ?int $id = null;

    #[Column(length: 5)]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    public string $countryCode;

    #[Column(length: 255)]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    public string $gymLocation;

    #[Column(length: 70)]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    public string $gymName;

    #[Column(type: 'datetime')]
    #[Groups(groups: ['read'])]
    public ?\DateTime $createdAt = null;
    
    #[OneToMany(mappedBy: "gym", targetEntity: Route::class)]
    private Collection $routes;

    public function __construct() {
        $this->routes = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Prepersist gets triggered on Insert
     */
    #[PrePersist]
    public function updatedTimestamps() {
        if($this->createdAt == null) {
            $this->createdAt = new \DateTime('now');
        }
    }
}