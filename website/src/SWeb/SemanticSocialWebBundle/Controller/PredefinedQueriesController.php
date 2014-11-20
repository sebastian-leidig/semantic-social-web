<?php

namespace SWeb\SemanticSocialWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PredefinedQueriesController extends Controller
{
    public function indexAction()
    {
        return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:index.html.twig');
    }
}
