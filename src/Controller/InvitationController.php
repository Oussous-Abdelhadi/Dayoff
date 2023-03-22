<?php

namespace App\Controller;

use App\Service\Mailer;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse; 
use Symfony\Component\Routing\RouterInterface;

class InvitationController extends AbstractController
{
    
    private $security;
    private $entityManager;
    
    public function __construct(Security $security, EntityManagerInterface $entityManager){
        $this->security = $security;
        $this->entityManager = $entityManager;
    }
    
    #[Route('/invitation', name: 'app_invitation')]
    public function index(HttpRequest $httpRequest, Mailer $mailer, RouterInterface $router): Response
    {

        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($router->generate('app_login'));
        }

        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $this->security->getUser()->getId();
        $manager = $this->entityManager->getRepository(User::class)->find($id);

        $roles = $manager->getRoles();
        $email = $httpRequest->request->get('email');

        if ($email && in_array("ROLE_MANAGER", $roles)) {
            $team = $manager->getTeam()->getId();
            $this->addFlash("success",
            "Votre invitation à bien été envoyer !");
            $mailer->sendInvitationEmail($email, $manager, $team);
        }
        return $this->render('invitation/index.html.twig', [
            'controller_name' => 'InvitationController',
        ]);
    }
}
