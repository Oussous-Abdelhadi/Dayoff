<?php 

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;

class Mailer
{
    private $mailer;
    private $entityManager;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    public function sendEmail($usermail, $token)
    {
        $email = (new TemplatedEmail())
            ->from('dadi94230@hotmail.fr')
            ->to(new Address($usermail))
            ->subject('[DayOff] Confirmation de compte !')
            ->htmlTemplate('emails/registration.html.twig')
            ->context([
                'token' => $token,
            ])
        ;
        $this->mailer->send($email);
    }

    public function sendRequestNotificationEmail($managers, $user, $request, $requestType)
    {
    
        $email = (new TemplatedEmail())
            ->from('dadi94230@hotmail.fr')
            ->subject('[DayOff] Nouvelle demande de ' . $requestType)
            ->htmlTemplate('emails/request_notification.html.twig')
            ->context([
                'user' => $user,
                'request' => $request,
            ]);
    
        // Add all the managers as recipients
        foreach ($managers as $manager) {
            $email->addTo($manager->getEmail());
        }
    
        $this->mailer->send($email);
    }

    public function sendInvitationEmail($usermail, $manager, $team)
    {

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $usermail,
        ]);

    
        $email = (new TemplatedEmail())
            ->from('dadi94230@hotmail.fr')
            ->to(new Address($usermail))
            ->subject('[DayOff] Invitation à rejoindre mon équipe')
            ->htmlTemplate('emails/invitation.html.twig')
            ->context([
                'manager' => $manager,
                'team' => $team,
                'user' => $user,
            ]);

        $this->mailer->send($email);
    }
    

    public function sendAgreedRequestEmail($usermail, $request, $requestType)
    {
    
        $email = (new TemplatedEmail())
            ->from('dadi94230@hotmail.fr')
            ->to(new Address($usermail))
            ->subject('[DayOff] Votre demande de ' . $requestType . ' à été accepté !')
            ->htmlTemplate('emails/request_agreed.html.twig')
            ->context([
                'request' => $request,
            ]);

        $this->mailer->send($email);
    }

    public function sendDeniedRequestEmail($usermail, $request, $requestType)
    {
    
        $email = (new TemplatedEmail())
            ->from('dadi94230@hotmail.fr')
            ->to(new Address($usermail))
            ->subject('[DayOff] Votre demande de ' . $requestType . ' à été refusé.')
            ->htmlTemplate('emails/request_denied.html.twig')
            ->context([
                'request' => $request,
            ]);

        $this->mailer->send($email);
    }
}

?>