<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']], itemOperations: ['get', 'delete'])]
#[Table(name: 'vote')]
class Vote {
    #[Column(type: 'integer')]
    #[GeneratedValue]
    #[Id]
    #[Groups(groups: ['read'])]
    private ?int $id = null;
    
    #[NotBlank]
    #[ManyToOne(targetEntity: User::class)]
    #[Groups(groups: ['read', 'write'])]
    private User $user;
    
    #[ManyToOne(targetEntity: Route::class)]
    #[Groups(groups: ['read', 'write'])]
    private Route $route;

    /**
     * @param User $user
     */
    public function setUser(User $user): void {
        $this->user = $user;
    }

    /**
     * @param Route $route
     */
    public function setRoute(Route $route): void {
        $this->route = $route;
    }

    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }
}