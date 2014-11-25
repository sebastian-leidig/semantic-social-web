<?php

namespace SWeb\SemanticSocialWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class HomeController extends Controller
{
    public function indexAction()
    {
        $this->devLogin();
        // get the user e-mail with
        // in PHP:        $this->getUser()->getUsername();
        // in Templates:  {{ app.user.username }}


        return $this->render('SWebSemanticSocialWebBundle:Home:index.html.twig');
    }
    
    private function devLogin()
    {
        // Fake login for local development as Google OAuth requires live server
        $user = new OAuthUser("sebastian14002@iiitd.ac.in");
        $this->container->get('security.context')->setToken(
            new UsernamePasswordToken(
                $user, null, 'main', $user->getRoles()
            )
        );
    }
}
