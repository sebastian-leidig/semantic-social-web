<?php

namespace SWeb\SemanticSocialWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use semsol\arc2\ARC2;

class VisualizationController extends Controller
{
    public function indexAction()
    {
        $dataUrl = $this->generateUrl('ontology_data');
        return $this->render('SWebSemanticSocialWebBundle:Visualization:index.html.twig', array( "dataUrl" => $dataUrl));
    }
    
    public function dataAction()
    {
        $data = array( "classes" => array(), "properties" => array());
    
        $config = array( 'remote_store_endpoint' => 'http://localhost:3030/data/query', );
        $store = \ARC2::getRemoteStore($config);
        
        $query = "";
        
        $res = $store->query($query);
        if($res['result'] !== "")
        {
            
        }
        
        
        // create a JSON-response with a 200 status code
        $json = json_encode($data);
        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
