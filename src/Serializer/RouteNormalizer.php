<?php

namespace App\Serializer;

use App\Entity\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class RouteNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface {
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'BOOK_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    public function __construct(private TokenStorageInterface $tokenStorage) {
        
    }

    public function normalize($object, $format = null, array $context = []) {
        assert($object instanceof Route);
        
        $context[self::ALREADY_CALLED] = true;
        $data = $this->normalizer->normalize($object, $format, $context);
        
        $data['hasVoted'] = $this->computeHasVoted($object);
        $data['graded'] = $this->getCurrentUsersGrade($object);
        
        return $data;
    }

    public function supportsNormalization($data, $format = null, array $context = []) {
        // Make sure we're not called twice
        if(isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Route;
    }
    
    private function getUser():?User {
        $token = $this->tokenStorage->getToken();
        if(!$token) {
            return null;
        }

        $owner = $token->getUser();
        if(!$owner instanceof User) {
            return null;
        }
        
        return $owner;
    }
    
    private function getCurrentUsersGrade(Route $route): int {
        $user = $this->getUser();
        if($user){
            foreach($route->getPublicGradeSubmission() as $grade){
                if($grade->getUsername() === $user->getUserIdentifier()){
                    return $grade->grade;
                }
            }
        }
        return -1;
    }
    
    private function computeHasVoted(Route $route): bool {
        $user = $this->getUser();
        if($user){
            foreach($route->getVoteSubmission() as $vote){
                if($vote->getUser()->getId() === $user->getId()){
                    return true;
                }
            }
        }
        
        return false;
    }
}