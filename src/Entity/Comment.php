<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']], itemOperations: ['get', 'delete'])]
#[Table(name: 'comment')]
#[HasLifecycleCallbacks]
class Comment {
    #[Column(type: 'integer')]
    #[GeneratedValue]
    #[Id]
    private int $id;

    #[ManyToOne(targetEntity: User::class)]
    private ?User $user;

    #[Column(type: 'datetime')]
    #[Groups(groups: ['read'])]
    public ?\DateTime $timestamp;

    #[ManyToOne(targetEntity: Route::class)]
    #[Groups(groups: ['write'])]
    private Route $route;

    #[Column(length: 1000)]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    public string $comment;

    #[Groups(groups: ['read'])]
    public function getTimestamp(): ?\DateTime {
        return $this->timestamp;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): static {
        $this->user = $user;
        return $this;
    }

    #[Groups(groups: ['read'])]
    public function getId():int {
        return $this->id;
    }
    

    #[Groups(groups: ['read'])]
    public function getUsername():string {
        return $this->user->getUserIdentifier();
    }

    #[Groups(groups: ['write'])]
    public function setRoute(Route $route): void {
        $this->route = $route;
    }

    /**
     * Prepersist gets triggered on Insert
     */
    #[PrePersist]
    public function updatedTimestamps() {
        if(!isset($this->timestamp)) {
            $this->timestamp = new \DateTime('now');
        }
    }
}