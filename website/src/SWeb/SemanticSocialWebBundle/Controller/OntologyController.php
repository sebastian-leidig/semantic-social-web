<?php

namespace SWeb\SemanticSocialWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class OntologyController extends Controller
{
    public function indexAction()
    {
        return $this->render('SWebSemanticSocialWebBundle:Ontology:index.html.twig');
    }
    
    public function dataAction()
    {
        //$kernel = $container->getService('kernel');
        $kernel = $this->get('kernel');
        $path = $kernel->locateResource('@SWebSemanticSocialWebBundle/Resources/ontology.json');
        $content = file_get_contents($path);

        // create a JSON-response with a 200 status code
        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
