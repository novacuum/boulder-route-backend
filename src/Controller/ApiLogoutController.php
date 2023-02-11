<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsController]
#[Route('/api/v2/logout')]
class ApiLogoutController extends AbstractController {
    public function __invoke(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response {
        // maybe these extra null checks are not even needed
        $token = $tokenStorage->getToken();
        if(!$token) {
            return $this->json((object)['Message'=>'Token not found'], Response::HTTP_UNAUTHORIZED);
        }

        $owner = $token->getUser();
        if(!$owner instanceof User) {
            return $this->json((object)['Message'=>'Invalid User'], Response::HTTP_UNAUTHORIZED);
        }

        $refreshTokenRepo = $entityManager->getRepository(RefreshToken::class);
        $refreshTokens = $refreshTokenRepo->findBy(['username'=>$owner->getUserIdentifier()]);
        if(!empty($refreshTokens)){
            foreach($refreshTokens as $token){
                $entityManager->remove($token);
            }
        }
        $entityManager->flush();
        
        return $this->json((object)[
            'Message' => 'Logout success',
            'deletedTokens' => count($refreshTokens)
        ]);
    }
}