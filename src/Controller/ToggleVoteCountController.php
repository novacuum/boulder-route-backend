<?php

namespace App\Controller;

use App\Entity\Route;
use App\Entity\User;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsController]
#[\Symfony\Component\Routing\Annotation\Route('/api/v2/routes/details/toggleUpVotes')]
class ToggleVoteCountController extends AbstractController {
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

        $routeRepo = $entityManager->getRepository(Route::class);
        /** @var Route $routeItem */
        $qb = $routeRepo->createQueryBuilder('Route')->select('r')->from(Route::class, 'r')
            ->innerJoin(User::class, 'u')
            ->where('r.createdAt = :createdAt')->andWhere('u.username = :username');
        $qb->setParameter('username', $request->query->get('username'));
        $qb->setParameter('createdAt', new \DateTime($request->query->get('createdAt')));
        $query = $qb->getQuery();
        $routeItem = $query->getOneOrNullResult();

        $voteRepo = $entityManager->getRepository(Vote::class);
        $voteItem = $voteRepo->findOneBy(['user' => $owner->getId(), 'route' => $routeItem->getId()]);
        
        if($voteItem) {
            $hasVoted = false;
            $entityManager->remove($voteItem);
            $entityManager->flush();
        }
        else {
            $hasVoted = true;
            $voteItem = new Vote();
            $voteItem->setUser($owner);
            $voteItem->setRoute($routeItem);

            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entityManager->persist($voteItem);
            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
        }

        return $this->json((object)[
            'Message' => 'Toggle upvote route success',
            'Item'    => (object)['hasVoted' => $hasVoted]
        ]);
    }
}