<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class AddOwnerToEntity implements EventSubscriberInterface {

    public function __construct(private TokenStorageInterface $tokenStorage) {
    }

    public static function getSubscribedEvents() {
        return [
            KernelEvents::VIEW => ['attachOwner', EventPriorities::PRE_WRITE],
        ];
    }

    public function attachOwner(ViewEvent $event) {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        
        if(!(new \ReflectionClass($entity))->hasMethod('setUser') || Request::METHOD_POST !== $method) {
            // Only handle entities with setUser (Event is called on any Api entity)
            return;
        }

        // maybe these extra null checks are not even needed
        $token = $this->tokenStorage->getToken();
        if(!$token) {
            return;
        }

        $owner = $token->getUser();
        if(!$owner instanceof User) {
            return;
        }


        // Attach the user to the not yet persisted Article
        $entity->setUser($owner);
    }
}