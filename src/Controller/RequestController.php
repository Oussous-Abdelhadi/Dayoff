<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Request;
use App\Form\RequestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;


class RequestController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/demande', name: 'app_request')]
    public function index(HttpRequest $httpRequest, Security $security)
    {
        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $security->getUser()->getId();
        
        $user = $this->entityManager->getRepository(User::class)->find($id);

        $myRequest = new Request();
        $requestForm = $this->createForm(RequestType::class, $myRequest);
        $requestForm->handleRequest($httpRequest);
        if ($requestForm->isSubmitted()) {

            $myRequest->setUser($user);
            $myRequest->setStatus('en attente');
            $this->entityManager->persist($myRequest);
            $this->entityManager->flush();

        }

        return $this->render('request/index.html.twig', [
            'requestForm' => $requestForm->createView(),
        ]);
    }
    
    #[Route('/edit/{id}/request', name: 'edit_request')]
    public function edit(HttpRequest $httpRequest, int $id): Response
    {

        $myRequest = $this->entityManager->getRepository(Request::class)->find($id);
        if (!$myRequest) {
            throw $this->createNotFoundException('Unable to find request with id ' . $id);
        }
        if ($myRequest->getStatus() == 'en attente')  {
            $form = $this->createForm(RequestType::class, $myRequest);
            $form->handleRequest($httpRequest);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->flush();
    
                return $this->redirectToRoute('home');
            }
        }else{
            return $this->redirectToRoute('home');
        }

        return $this->render('request/edit.html.twig', [
            'requestForm' => $form->createView()
        ]);
    }    
}