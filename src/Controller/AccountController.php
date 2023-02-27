<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditeUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\ChangePasswordFormType;


class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function edit(Request $request, Security $security,
     EntityManagerInterface $entityManager,
     UserPasswordHasherInterface $userPasswordHasher
     )
    {
        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $security->getUser()->getId();
        
        
        $user = $entityManager->getRepository(User::class)->find($id);
        $form = $this->createForm(EditeUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // // Enregistrement de la modification en base de données
            $entityManager->flush();
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
            $entityManager->flush();
        } 
        
        
        return $this->render('account/index.html.twig', [
            'formEditAccount' => $form->createView(),
            'resetForm' => $resetForm->createView(),
        ]);
    }

    #[Route('/delete/account', name: 'account_delete')]
    public function delete(Security $security, EntityManagerInterface $entityManager,)
    {
        // Récupération de l'identifiant de l'utilisateur connecté
        $id = $security->getUser()->getId();
        
        $user = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_login');
        
    }
    
    

}
