<?php

namespace SWeb\SemanticSocialWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use semsol\arc2\ARC2;

class PredefinedQueriesController extends Controller
{
    public function indexAction()
    {
        return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:index.html.twig');
    }
    
    public function formAction($index)
    {
        return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:form'.$index.'.html.twig');
    }
    
    public function handleAction()
    {
        $prefix = "PREFIX : <http://www.semanticweb.org/sebastian/ontologies/2014/social-web-ontology#> ";
        $form = $this->get('request')->request->get('form');
        switch($form)
        {
            case 1:
	              $INTEREST=$this->get('request')->request->get('interest');
	              $query = $prefix.'
	              SELECT DISTINCT ?label
	              WHERE
	              {
	                ?a :hasInterest :'.$INTEREST.' .
	                ?a :hasLabel ?label.
	              }
	              ';
	              $f=1;
	              //print "People sharing an interest in <i>{{ interest }}</i>";
	              $r = $this->sparql($query);
	              return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:results1.html.twig', array( "interest" => $INTEREST, "results" => $r));
	              break;
	              
            case 2:
	              $PERSON=$this->get('request')->request->get('pname');
	              $query = $prefix.'
	              SELECT DISTINCT ?label
	              WHERE
	              {
		              :'.$PERSON.' :hasInterest ?I1	.
		              ?I1 :hasLabel ?label.
	              }
	              ';
	              $f=2;
	              //print "Interests of a Person ".$PERSON." are\n";
	              $r = $this->sparql($query);
	              return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:results2.html.twig', array( "person" => $PERSON, "results" => $r));
	              break;
	              
            case 3:
	              $PERSON=$this->get('request')->request->get('pname');
	              if ($this->get('request')->request->get('course'))
	              {	
		              $COURSE=$this->get('request')->request->get('course');
		              $query = $prefix.'
		              SELECT DISTINCT ?label 
		              WHERE
		              {
			              ?p1 :attends :'.$COURSE.'.
			              ?p1 :hasLabel ?label.
		              }
		              ';
		              $f=3;
		              //print "People attending the course ".$COURSE." are :\n";
	                $r = $this->sparql($query);
	                return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:results3.html.twig', array( "person" => $PERSON, "course" => $COURSE, "results" => $r));
	              }
	              else
	              {
		              $query = $prefix.'
		              SELECT DISTINCT ?course ?label
		              WHERE
		              {
			              :'.$PERSON.' :attends ?c1.
			              ?p1 :attends ?c1.
			              ?p1 :hasLabel ?label.
			              ?c1 :hasLabel ?course.
		              }
		              ';
		              $f=4;
		              //print "People sharing their courses with ".$PERSON." are\n";
	                $r = $this->sparql($query);
	                return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:results3.html.twig', array( "person" => $PERSON, "results" => $r));
	              }
                break;
                
            case 4:
	            $PERSON=$this->get('request')->request->get('pname');
	            $query = $prefix.'
	            SELECT DISTINCT ?label
	            WHERE
	            {
		            :'.$PERSON.' :attends ?e1.
		            ?e1 :hasLabel ?label.
	            }
	            ';
	            $f=5;
	            //print "Events attended by ".$PERSON." are :\n";
              $r = $this->sparql($query);
              return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:results4.html.twig', array( "person" => $PERSON, "results" => $r));
	            break;
	            
        case 5:
	          $PERSON1=$this->get('request')->request->get('pname1');
	          $PERSON2=$this->get('request')->request->get('pname2');
	          $query = $prefix.'
	          SELECT DISTINCT ?r1 ?r2
	          WHERE
	          {
		          :'.$PERSON1.' ?r11 ?p.
		          ?p ?r22 :'.$PERSON2.'.
		          ?r11 :hasLabel ?r1.
		          ?r22 :hasLabel ?r2.
	          }
	          ';
	          $f=6;
	          //print "Two Step relationship between ".$PERSON1." and ".$PERSON2." is :\n";
            $r = $this->sparql($query);
            return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:results5.html.twig', array( "person1" => $PERSON1, "person2" => $PERSON2, "results" => $r));
	          break;
	          
        }
        
        return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:index.html.twig');
    }
    
    function sparql($query)
    {
        $config = array( 'remote_store_endpoint' => 'http://localhost:3030/data/query', );
        $store = \ARC2::getRemoteStore($config);
        
        $res = $store->query($query);
        $r = [];
        if($res['result'] !== "")
            $r = $res['result']['rows'];
            
        return $r;
    }
}
