<?php

namespace App\EventSubscriber;

use App\Entity\Media;
use App\Service\FileUploader;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;


class MediaSubscriber implements EventSubscriberInterface {
    private FileUploader $fileUploader;
    private LoggerInterface $logger;

    public function __construct(FileUploader $fileUploader, LoggerInterface $logger) {
        $this->fileUploader = $fileUploader;
        $this->logger = $logger;
    }
    
    public function getSubscribedEvents(): array {
        return [
            Events::postRemove
        ];
    }

    public function postRemove(LifecycleEventArgs $args): void {
        $media = $args->getObject();
        if($media instanceof Media){
            if(!@unlink($this->fileUploader->getUploadPath() . DIRECTORY_SEPARATOR . $media->filePath)){
                $this->logger->error('could not delete file for ' . var_export($media, true));
            }
        }
    }
}