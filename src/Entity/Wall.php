<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']], itemOperations: ['get', 'delete'])]
#[Table(name: 'wall')]
#[HasLifecycleCallbacks]
class Wall {
    #[Column(type: 'integer')]
    #[GeneratedValue]
    #[Id]
    private int $id;

    #[Column(length: 120)]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    public string $name;

    #[ManyToOne(targetEntity: Gym::class)]
    #[Groups(groups: ['read', 'write'])]
    public ?Gym $gym;

    #[ManyToOne(targetEntity: User::class)]
    private ?User $user;

    #[ApiProperty(iri: 'http://schema.org/image')]
    #[OneToOne(targetEntity: 'Media', cascade: ['persist', 'remove'])]
    #[Groups(groups: ['read', 'write'])]
    public ?Media $image;

    #[Column(type: 'json')]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    public array $boxList;

    #[Groups(groups: ['read'])]
    public function getId() {
        return $this->id;
    }

    public function setUser(?User $user): static {
        $this->user = $user;
        return $this;
    }

    #[Groups(groups: ['read'])]
    public function getUsername():string {
        return $this->user->getUserIdentifier();
    }
}