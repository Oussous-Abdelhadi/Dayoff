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
}