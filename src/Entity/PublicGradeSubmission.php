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
#[ApiResource(
    itemOperations: ['get', 'delete', 'patch']
    , denormalizationContext: ['groups' => ['write']]
    , normalizationContext: ['groups' => ['read']]
)]
#[Table(name: 'public_grade_submission')]
class PublicGradeSubmission {
    #[Column(type: 'integer')]
    #[GeneratedValue]
    #[Id]
    #[Groups(groups: ['read'])]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Route::class, inversedBy: "publicGradeSubmission")]
    #[NotBlank]
    #[Groups(groups: ['write'])]
    private Route $route;
    
    #[ManyToOne(targetEntity: User::class)]
    private User $user;

    #[Column(type: 'integer')]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    public int $grade;

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

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

    #[Groups(groups: ['read'])]
    public function getUsername():string {
        return $this->user->getUserIdentifier();
    }
}