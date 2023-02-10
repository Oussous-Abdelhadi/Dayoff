<?php 


namespace App\Service;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class AccessControl
{
    private $security;
    private $router;

    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }

    public function checkAccess()
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->router->generate('app_login'));
        }
    }

}
