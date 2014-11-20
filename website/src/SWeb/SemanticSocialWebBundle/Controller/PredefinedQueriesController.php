<?php

namespace SWeb\SemanticSocialWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use semsol\arc2\ARC2;

class PredefinedQueriesController extends Controller
{
    public function indexAction()
    {
        $config = array( 'remote_store_endpoint' => 'http://localhost:3030/data/query', );
        $store = \ARC2::getRemoteStore($config);

        $query = '
          PREFIX : <http://www.semanticweb.org/sebastian/ontologies/2014/social-web-ontology#>
          PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
          SELECT ?name
          WHERE { ?s rdf:type :Student .
                  ?s :hasLabel ?name . }
        ';

        $res = $store->query($query);
        $r = $res['result']['rows'];
        
        return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:index.html.twig', array( "results" => $r));
    }
}
