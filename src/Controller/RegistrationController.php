<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Entity\Team;
use App\Service\Mailer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class RegistrationController extends AbstractController
{
    private $mailer;
    private $userRepository;
    
    public function __construct(Mailer $mailer, private UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }
    
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

        if ($existingUser) {
            $this->addFlash('email_error', 'Un utilisateur avec ce mail existe déjà');
        } else {
            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                        )
                    );
                $user->setRoles(
                    ['ROLE_MANAGER']
                );
                $team = new Team();
                $entityManager->persist($team);
                $user->setTeam($team);
                $user->setToken($this->generateToken());

                $entityManager->persist($user);
                $entityManager->flush();

                try {
                    $this->mailer->sendEmail($user->getEmail(), $user->getToken());
                    $this->addFlash("success",
                        "Inscription réussi ! Vous allez recevoir un email de confirmation, vérifiez également vos spam.");
                } catch (\Exception $e) {
                    $this->addFlash("error", "Une erreur s'est produite lors de l'envoi de l'email de confirmation.");
                }
            

                // Effacer les données du formulaire
                $user = new User();
                $form = $this->createForm(RegistrationFormType::class, $user);
                // return new RedirectResponse($this->urlGenerator->generate('app_login'));
            }
        }


        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }


    #[Route('/confirmer-mon-compte/{token}', name: 'confirm_account')]
    public function confirmAccount($token, EntityManagerInterface $entityManager)
    {
        try {
            $user = $this->userRepository->findOneBy(['token' => $token]);
            if ($user) {
                $user->setToken(null);
                $user->setIsVerified(true);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash("success",
                "Votre compte est maintenant activé !");
                return $this->redirectToRoute('app_login');
            }else{
                $this->addFlash("error",
                "Ce compte n'existe pas !");
                return $this->redirectToRoute('app_login');
            }
        } catch (\Exception $e) {
            $this->addFlash("error", "Une erreur s'est produite lors de la confirmation de l'adresse email.");
        }
        
    }
}