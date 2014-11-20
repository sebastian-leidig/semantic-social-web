<?php

namespace SWeb\SemanticSocialWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('SWebSemanticSocialWebBundle:Admin:index.html.twig');
    }
}
