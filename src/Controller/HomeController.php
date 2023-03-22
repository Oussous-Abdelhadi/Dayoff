<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface; 
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Doctrine\ORM\Mapping\ClassMetadata;


class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $entityManager, Security $security, RouterInterface $router, PaginatorInterface $paginator, HttpRequest $httpRequest): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($router->generate('app_login'));
        }

        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $security->getUser()->getId();
        $user = $entityManager->getRepository(User::class)->find($id);

        $roles = $user->getRoles();

        if (in_array("ROLE_MANAGER", $roles)) {
            $team = $user->getTeam();
        
            $queryBuilder = $entityManager->createQueryBuilder()
                ->select('r')
                ->from(Request::class, 'r')
                ->join('r.user', 'u')
                ->where('u.team = :team')
                ->setParameter('team', $team)
                ->orderBy('r.start_date', 'DESC');

                $users = $entityManager->getRepository(User::class)->findBy(['team' => $user->getTeam()]);
        } else {

            $queryBuilder = $entityManager->createQueryBuilder()
                ->select('r')
                ->from(Request::class, 'r')
                ->where('r.user = :user')
                ->setParameter('user', $user)
                ->orderBy('r.start_date', 'DESC');
        }
        // Paginer les résultats
        $pagination = $paginator->paginate($queryBuilder, $httpRequest->query->getInt('page', 1), 10);
    
        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
            'user' => $user,
            'users' => $users
        ]);
    }
    
    #[Route('/filtrer', name: 'request_filter')]
    public function fitrer(HttpRequest $httpRequest,
     EntityManagerInterface $entityManager,
     Security $security,
     RouterInterface $router, PaginatorInterface $paginator)
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($router->generate('app_login'));
        }
    

        $status = $httpRequest->query->get('status'); // Récupérer le statut depuis l'URL
        $type = $httpRequest->query->get('type'); 
        $date = $httpRequest->query->get('date'); 
        $startDate = $httpRequest->query->get('startDate');
        $endDate = $httpRequest->query->get('endDate');
        $name = $httpRequest->query->get('name');

        
        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $security->getUser()->getId();
        $user = $entityManager->getRepository(User::class)->find($id);
        
        $roles = $user->getRoles();
        $users = $entityManager->getRepository(User::class)->findBy(['team' => $user->getTeam()]);
        
        if (in_array("ROLE_MANAGER", $roles)) {
            $team = $user->getTeam();
            
            $queryBuilder = $entityManager->createQueryBuilder()
            ->select('r')
            ->from(Request::class, 'r')
            ->join('r.user', 'u')
            ->where('u.team = :team')
            ->setParameter('team', $team)
            ->orderBy('r.start_date', 'DESC');
            
        } else {
            $queryBuilder = $entityManager->createQueryBuilder()
                ->select('r')
                ->from(Request::class, 'r')
                ->where('r.user = :user')
                ->setParameter('user', $user)
                ->orderBy('r.start_date', 'DESC');
        }

        if ($name !== null && $name !== '') {
            $queryBuilder->andWhere('u.name = :name')
                ->setParameter('name', $name);
        }

        if ($status !== null && $status !== '') {
            $queryBuilder->andWhere('r.status = :status')
                ->setParameter('status', $status);
        }
        
        if ($type !== null && $type !== '') {
            $queryBuilder->andWhere('r.type = :type')
                ->setParameter('type', $type);
        }

        if ($date !== null && $date !== '') {
            $queryBuilder->orderBy('r.start_date', $date);
        }

        if ($startDate !== null && $startDate !== '') {
            $queryBuilder->andWhere('r.start_date >= :startDate')
                ->setParameter('startDate', $startDate);
        }       

        if ($endDate !== null && $endDate !== '') {
            $queryBuilder->andWhere('r.end_date <= :endDate')
                ->setParameter('endDate', $endDate);
        }  
    
        // Récupération des requêtes avec pagination
        $pagination = $paginator->paginate(
            $queryBuilder,
            $httpRequest->query->getInt('page', 1),
            10
        );

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
            'user' => $user,
            'users' => $users
        ]);
    }
     
}
