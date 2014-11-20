<?php

namespace SWeb\SemanticSocialWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OntologyController extends Controller
{
    public function indexAction()
    {
        return $this->render('SWebSemanticSocialWebBundle:Ontology:index.html.twig');
    }
}
