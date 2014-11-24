<?php

namespace SWeb\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SWebUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
