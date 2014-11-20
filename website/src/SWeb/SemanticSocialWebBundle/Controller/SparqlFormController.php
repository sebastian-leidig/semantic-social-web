<?php

namespace SWeb\SemanticSocialWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SparqlFormController extends Controller
{
    public function indexAction()
    {
        return $this->render('SWebSemanticSocialWebBundle:SparqlForm:index.html.twig');
    }
}
