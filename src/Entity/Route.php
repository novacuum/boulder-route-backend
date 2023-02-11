<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\RouteController;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`route`')]
#[ApiFilter(SearchFilter::class, properties: ['gym.gymLocation' => 'exact', 'user.username' => 'exact', 'createdAt'=>'exact'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiResource(
    itemOperations: ['get', 'patch', 'delete', 'put'],
    attributes: [
        'pagination_enabled'        => true,
        'pagination_items_per_page' => 200,
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[UniqueEntity(fields: ['routeName'], message: "There is already an route with this routeName")]
class Route {
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    #[Groups(groups: ['read'])]
    private ?int $id = null;

    #[Column(length: 70)]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    public string $routeName;

    #[Column(type:'integer')]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    public int $ownerGrade;

    #[ManyToOne(targetEntity: Gym::class, inversedBy: "routes")]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    private ?Gym $gym;

    #[ManyToOne(targetEntity: User::class, inversedBy: "routes")]
    private ?User $user;
    
    #[Column(type: 'datetime')]
    #[Groups(groups: ['read'])]
    public ?\DateTime $createdAt;
    
    #[ApiProperty(iri: 'http://schema.org/image')]
    #[OneToOne(targetEntity: 'Media', cascade: ['persist', 'remove'])]
    #[Groups(groups: ['read', 'write'])]
    public ?Media $routeImage;

    #[Column(type:'integer')]
    #[NotBlank]
    #[Groups(groups: ['read', 'write'])]
    public int $footholdType;

    #[Column(type: "text")]
    #[Groups(groups: ['read', 'write'])]
    public string $description;

    #[OneToMany(mappedBy: "route", targetEntity: PublicGradeSubmission::class, cascade: ['remove'])]
    private Collection $publicGradeSubmission;

    #[OneToMany(mappedBy: "route", targetEntity: Vote::class, cascade: ['remove'])]
    private Collection $voteSubmission;

    //comments are fetched in a sub request from the app -> set fetch to lazy
    #[OneToMany(mappedBy: "route", targetEntity: Comment::class, cascade: ['remove'], fetch: 'LAZY')]
    private Collection $comment;
    
    
    private string $publicGrade;
    private string $username;
    private int $voteCount;
    private int $hasVoted;

    /******** METHODS ********/

    public function getId() {
        return $this->id;
    }

    public function getGym(): ?Gym {
        return $this->gym;
    }

    public function setGym(?Gym $gym): static {
        $this->gym = $gym;
        return $this;
    }
    
    public function getUser(): ?User {
        return $this->user;
    }
    
    public function setUser(?User $user): static {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection|PublicGradeSubmission[]
     */
    public function getPublicGradeSubmission(): Collection {
        return $this->publicGradeSubmission;
    }
    
    #[Groups(groups: ['read'])]
    public function getPublicGrade(): int {
        $c = $this->publicGradeSubmission->count();
        return $c>0 ? array_reduce($this->publicGradeSubmission->toArray(), fn($carry, PublicGradeSubmission $pgs)=>$carry + $pgs->grade, 0) / $c : 0;
    }

    #[Groups(groups: ['read'])]
    public function getVoteCount(): int {
        return $this->voteSubmission->count();
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVoteSubmission(): Collection {
        return $this->voteSubmission;
    }

    #[Groups(groups: ['read'])]
    public function getUsername():string {
        return $this->user->getUserIdentifier();
    }
    
    /**
     * Prepersist gets triggered on Insert
     */
    #[PrePersist]
    public function updatedTimestamps() {
        if(!isset($this->createdAt)) {
            $this->createdAt = new \DateTime('now');
        }
    }

    public function __toString() {
        return $this->routeName . '(@' . $this->user->getUserIdentifier() . ')';
    }
}