<?php

namespace App\Controller;

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
    
        // Récupération des requêtes avec tri par date de début décroissante
        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('r')
            ->from(Request::class, 'r')
            ->where('r.user = :userId')
            ->setParameter('userId', $id)
            ->orderBy('r.start_date', 'DESC');
    
        // Paginer les résultats
        $pagination = $paginator->paginate($queryBuilder, $httpRequest->query->getInt('page', 1), 10);
    
        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }


    #[Route('/delete/request', name: 'request_delete')]
    public function delete()
    {
        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $security->getUser()->getId();

        $request = $this->entityManager->getRepository(Request::class)->findBy(['user' => $id]);
        $this->entityManager->remove($request);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('home');
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
    
        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $security->getUser()->getId();

        $status = $httpRequest->query->get('status'); // Récupérer le statut depuis l'URL
        $type = $httpRequest->query->get('type'); 
        $date = $httpRequest->query->get('date'); 
        
        // Récupération des requêtes avec tri par date de début décroissante
        $queryBuilder = $entityManager->createQueryBuilder()
        ->select('r')
        ->from(Request::class, 'r')
        ->where('r.user = :userId')
        ->setParameter('userId', $id);
        

        if ($status !== null) {
            $queryBuilder->andWhere('r.status = :status')
                ->setParameter('status', $status);
        }
        
        if ($type !== null) {
            $queryBuilder->andWhere('r.type = :type')
                ->setParameter('type', $type);
        }

        if ($date !== null) {
            $queryBuilder->orderBy('r.start_date', $date);
        }

        // {{ path('request_filter', {'type': 'Télétravail', 'status': app.request.query.get('status')}) }}
    
        // Récupération des requêtes avec pagination
        $pagination = $paginator->paginate(
            $queryBuilder,
            $httpRequest->query->getInt('page', 1),
            10
        );

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
     
}
