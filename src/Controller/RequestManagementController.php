<?php

namespace App\Controller;

use App\Entity\Request;
use App\Entity\User;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;


class RequestManagementController extends AbstractController
{
    private $entityManager;
    private $security;
    private $mailer;
    private $requestStack;

    public function __construct(EntityManagerInterface $entityManager,
    Security $security,
    Mailer $mailer, 
    RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->mailer = $mailer;
        $this->requestStack = $requestStack;
    }


    #[Route('/pending-request/{id}', name: 'pending_request')]
    public function markRequestAsPending($id)
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($router->generate('app_login'));
        }

        // Récupération de l'identifiant de l'utilisateur connecté
        $user_id = $this->security->getUser()->getId();
        $user = $this->entityManager->getRepository(User::class)->find($user_id);
        
        $roles = $user->getRoles();

        if (in_array("ROLE_MANAGER", $roles)) {

            $request = $this->entityManager->getRepository(Request::class)->find($id);
            $request->setStatus('En attente');
            $this->entityManager->persist($request);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('home');
    }


    #[Route('/validate-request/{id}/{user_request_id}', name: 'validate_request')]
    public function validateRequest($id)
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($router->generate('app_login'));
        }

        $user_request_id = $this->requestStack->getCurrentRequest()->get('user_request_id');
        $userRequest = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user_request_id]);
        $usermail = $userRequest->getEmail();

        // Récupération de l'identifiant de l'utilisateur connecté
        $user_id = $this->security->getUser()->getId();
        $user = $this->entityManager->getRepository(User::class)->find($user_id);
        
        $roles = $user->getRoles();

        if (in_array("ROLE_MANAGER", $roles)) {
            
            $request = $this->entityManager->getRepository(Request::class)->find($id);
            $request->setStatus('Accepté');
            $this->entityManager->persist($request);
            $this->entityManager->flush();

            $this->mailer->sendAgreedRequestEmail($usermail, $request, $request->getType());
        }
        
        return $this->redirectToRoute('home');
    }


    #[Route('/refuse-request/{id}/{user_request_id}', name: 'refuse_request')]
    public function refuseRequest($id)
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($router->generate('app_login'));
        }

        $user_request_id = $this->requestStack->getCurrentRequest()->get('user_request_id');
        $userRequest = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user_request_id]);
        $usermail = $userRequest->getEmail();

        // Récupération de l'identifiant de l'utilisateur connecté
        $user_id = $this->security->getUser()->getId();
        $user = $this->entityManager->getRepository(User::class)->find($user_id);
        
        $roles = $user->getRoles();
        $request = $this->entityManager->getRepository(Request::class)->find($id);

        if (in_array("ROLE_MANAGER", $roles) ) {

            $request->setStatus('Refusé');
            $this->entityManager->persist($request);
            $this->entityManager->flush();

            $this->mailer->sendDeniedRequestEmail($usermail, $request, $request->getType());

        }

        return $this->redirectToRoute('home');
    }

}
