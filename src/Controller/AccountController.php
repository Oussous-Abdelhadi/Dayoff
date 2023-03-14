<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Team;
use App\Form\EditeUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\ChangePasswordFormType;
use Symfony\Component\HttpFoundation\Request as HttpRequest;



class AccountController extends AbstractController
{


    private $entityManager;
    private $security;


    public function __construct(EntityManagerInterface $entityManager, Security $security){
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/compte', name: 'app_account')]
    public function edit(Request $request,
     UserPasswordHasherInterface $userPasswordHasher
     )
    {
        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $this->security->getUser()->getId();
        
        
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $form = $this->createForm(EditeUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // // Enregistrement de la modification en base de données
            $this->entityManager->flush();
        } 
        

        $resetForm = $this->createForm(ChangePasswordFormType::class);
        $resetForm->handleRequest($request);


        if ($resetForm->isSubmitted() && $resetForm->isValid()) {

            // $user->setPassword($encodedPassword);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $resetForm->get('password')->getData()
                    )
                );
            
            // // Enregistrement de la modification en base de données
            $this->entityManager->flush();
        } 
        
        
        return $this->render('account/index.html.twig', [
            'formEditAccount' => $form->createView(),
            'resetForm' => $resetForm->createView(),
        ]);
    }

    #[Route('/delete/account', name: 'account_delete')]
    public function delete(HttpRequest $HttpRequest, Security $security)
    {
        // Vérification du token CSRF
        if (!$this->isCsrfTokenValid('account_delete', $HttpRequest->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $users = [];
        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $this->security->getUser()->getId();
        $user = $this->entityManager->getRepository(User::class)->find($id);

        $userRepository = $this->entityManager->getRepository(User::class)->findAll();
        $teamUser = $user->getTeam();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // foreach ($userRepository as $userbdd) {
        //     if ($userbdd->getTeam() == $teamUser && $user != $userbdd) {
        //         $users[] = $userbdd;
        //     }
        // }

        // if (count($users) == 0 ) {
        //     $this->entityManager->remove($teamUser);
        //     $this->entityManager->flush();
        // }

        return $this->redirectToRoute('app_login');
    }
    
}

?>