<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Request;
use App\Form\RequestType;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;


class RequestController extends AbstractController
{

    private $entityManager;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, Mailer $mailer){
        $this->mailer = $mailer;
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
            $startDate = $myRequest->getStartDate();
            $endDate = $myRequest->getEndDate();
        
            if ($startDate > $endDate) {
                $requestForm->get('endDate')->addError(new FormError("La date de fin doit être postérieure à la date de début"));
            } else {
                $myRequest->setUser($user);
                $myRequest->setStatus('En attente');
                $this->entityManager->persist($myRequest);
                $this->entityManager->flush();

                $managers = $this->entityManager->getRepository(User::class)->createQueryBuilder('u')
                ->where('u.roles LIKE :roles')
                ->andWhere('u.team = :team')
                ->setParameter('roles', '%"ROLE_MANAGER"%')
                ->setParameter('team', $user->getTeam())
                ->getQuery()
                ->getResult();
            
                $requestType = $myRequest->getType();
                try {
                    $this->mailer->sendRequestNotificationEmail($managers, $user, $myRequest, $requestType);
                    $this->addFlash("success",
                        "Demande réussie ! Vos managers seront notifiés par email de votre demande.");
                } catch (\Exception $e) {
                    $this->addFlash("error", "Une erreur s'est produite lors de l'envoi de votre demande.");
                }
        
                return $this->redirectToRoute('home');
            }
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
        if ($myRequest->getStatus() == 'En attente')  {
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

    #[Route('/requests/{id}/delete', name: 'delete_request')]
    public function deleteRequest(HttpRequest $httpRequest, Request $request, int $id): Response
    {

        $myRequest = $this->entityManager->getRepository(Request::class)->find($id);
        if (!$myRequest) {
            throw $this->createNotFoundException('Unable to find request with id ' . $id);
        }

        $submittedToken = $httpRequest->request->get('_token');
        $type = $myRequest->getType();
        if ($this->isCsrfTokenValid('delete_request', $submittedToken)) {
            $this->entityManager->remove($myRequest);
            $this->entityManager->flush();

            $this->addFlash('success', "Votre demande de $type a été supprimée avec succès.");

            return $this->redirectToRoute('home');
        }

        // Afficher la boîte de dialogue de confirmation
        return $this->render('request/confirm_delete.html.twig', [
            'request' => $myRequest,
        ]);
    }
}

?>