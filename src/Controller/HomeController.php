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

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'home')]
    public function index(Security $security, 
    RouterInterface $router): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($router->generate('app_login'));
        }

        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $security->getUser()->getId();

        $requests = $this->entityManager->getRepository(Request::class)->findBy(['user' => $id]);
        // dd($requests);
        return $this->render('home/index.html.twig', [
            'requests' => $requests,
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
}
