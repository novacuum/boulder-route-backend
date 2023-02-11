<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\MediaController;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity]
#[Table(name: '`media`')]
#[ApiResource(collectionOperations: [
    'get'
    , 'post' => [
        'controller' => MediaController::class,
        'deserialize' => false, 
        'openapi_context' => [
            'requestBody' => [
                'description' => 'File Upload',
                'required' => true,
                'content' => [
                    'multipart/form-data' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'file' => ['type' => 'string', 'format' => 'binary', 'description' => 'File to be uploaded']
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]],
    iri: 'http://schema.org/ImageObject',
    itemOperations: ['get', 'delete'],
    normalizationContext: ['groups' => ['read']]
)]
class Media {
    #[Column(type: 'integer')]
    #[GeneratedValue]
    #[Id]
    #[Groups(groups: ['read'])]
    private ?int $id = null;

    #[Column(nullable: true, length: 255)]
    #[ApiProperty(iri: 'http://schema.org/contentUrl')]
    #[Groups(groups: ['read'])]
    public ?string $filePath = null;
    
    public function getId(): ?int {
        return $this->id;
    }
}