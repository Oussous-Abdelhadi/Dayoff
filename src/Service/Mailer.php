<?php 

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($usermail, $token)
    {
        $email = (new TemplatedEmail())
            ->from('dadi94230@hotmail.fr')
            ->to(new Address($usermail))
            ->subject('[DayOff] Confirmation de compte !')
        
            // path of the Twig template to render
            ->htmlTemplate('emails/registration.html.twig')
        
            // pass variables (name => value) to the template
            ->context([
                'token' => $token,
            ])
        ;
        $this->mailer->send($email);
    }

    public function sendRequestNotificationEmail($managers, $user, $request, $requestType)
    {
        // Build the email message
        $email = (new TemplatedEmail())
            ->from('dadi94230@hotmail.fr')
            ->subject('Nouvelle demande de ' . $requestType)
            ->htmlTemplate('emails/request_notification.html.twig')
            ->context([
                'user' => $user,
                'request' => $request,
            ]);
    
        // Add all the managers as recipients
        foreach ($managers as $manager) {
            $email->addTo($manager->getEmail());
        }
    
        // Send the email
        $this->mailer->send($email);
    }
    
}

?>