<?php

namespace App\Controller;

use App\Entity\PublicGradeSubmission;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsController]
#[\Symfony\Component\Routing\Annotation\Route('/api/v2/public_grade_submissions/update')]
class UpdatePublicGrade  extends AbstractController{
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
        
        $params = json_decode($request->getContent());

        $gradeRepo = $entityManager->getRepository(PublicGradeSubmission::class);
        /** @var PublicGradeSubmission $gradeItem */
        $gradeItem = $gradeRepo->findOneBy(['user' => $owner->getId(), 'route' =>$params->route]);
        
        if($gradeItem) {
            $gradeItem->grade = $params->grade;
            $entityManager->persist($gradeItem);
            $entityManager->flush();
        }
        else {
            return $this->json((object)['Message'=>'No grade submission found for: ' . var_export( $params, true)], Response::HTTP_NOT_FOUND);
        }

        return $this->json((object)[
            'id' => $gradeItem->getId()
        ]);
    }
}