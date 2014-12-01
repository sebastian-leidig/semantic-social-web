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
	$uname = $this->getUser()->getUsername();
	$query_findName='PREFIX : <http://www.semanticweb.org/sebastian/ontologies/2014/social-web-ontology#>
			SELECT DISTINCT ?n
			WHERE
			{
				?e :hasEmail "'.$uname.'".
				?e :hasLabel ?n.
			}
			';
	$x=$this->sparql($query_findName);
	$query='PREFIX : <http://www.semanticweb.org/sebastian/ontologies/2014/social-web-ontology#>
		PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
		SELECT DISTINCT ?p
		WHERE
		{
			?i rdf:type :Person.
		        ?i :hasLabel ?p.
			?i :hasEmail ?e.
			FILTER (?e!="'.$uname.'")

		}';
	$a = $this->sparql($query);
	$course_query='PREFIX : <http://www.semanticweb.org/sebastian/ontologies/2014/social-web-ontology#>
			PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
		SELECT DISTINCT ?s
		WHERE
		{
			?c rdf:type :Subject.
			?c :hasLabel ?s.

		}';
	$b = $this->sparql($course_query);
        return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:form'.$index.'.html.twig', array("result_name" => $a , "result_course" => $b, "user_name" => $uname, "result_findName"=> $x));
	//return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:form'.$index.'.html.twig');    
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
	              SELECT DISTINCT ?name ?img ?mail ?uid
	              WHERE
	              {
	                ?id :hasInterest :'.$INTEREST.' ;
	                    :hasLabel ?name .
                      OPTIONAL 
                      {
                        ?id :hasImage ?img ;
                            :hasEmail ?mail ;
                            :hasUserId ?uid .
                      }
	              }
	              ';
	              $f=1;
	              //print "People sharing an interest in <i>{{ interest }}</i>";
	              $r = $this->sparql($query);
	              $r = $this->distinct($r, 'name');
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
		              SELECT DISTINCT ?name ?img ?mail ?uid
		              WHERE
		              {
			              ?p1 :attends :'.$COURSE.' ;
	                      :hasLabel ?name .
                        OPTIONAL 
                        {
                          ?p1 :hasImage ?img ;
                              :hasEmail ?mail ;
                              :hasUserId ?uid .
                        }
		              }
		              ';
		              $f=3;
		              //print "People attending the course ".$COURSE." are :\n";
	                $r = $this->sparql($query);
	                $r = $this->distinct($r, 'name');
	                return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:results3.html.twig', array( "person" => $PERSON, "course" => $COURSE, "results" => $r));
	              }
	              else
	              {
		              $query = $prefix.'
		              SELECT DISTINCT ?course ?name ?img ?mail ?uid
		              WHERE
		              {
			              :'.$PERSON.' :attends ?c1.
			              ?p1 :attends ?c1.
			              ?c1 :hasLabel ?course.
			              ?p1 :hasLabel ?label ;
			                  :hasLabel ?name .
                        OPTIONAL 
                        {
                          ?p1 :hasImage ?img ;
                              :hasEmail ?mail ;
                              :hasUserId ?uid .
                        }
		              }
		              ';
		              $f=4;
		              //print "People sharing their courses with ".$PERSON." are\n";
	                $r = $this->sparql($query);
	                $r = $this->distinct($r, 'name');
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
	          
	case 6:
		$PERSON1=$this->get('request')->request->get('pname1');
	        $PERSON2=$this->get('request')->request->get('pname2');
	        $query = $prefix.'
		SELECT ?l
		WHERE
		{
			:'.$PERSON1.' :hasInterest ?i.
			:'.$PERSON2.' :hasInterest ?i.
			?i :hasLabel ?l.
		}
		';
		 $r = $this->sparql($query);
            return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:results6.html.twig', array( "person1" => $PERSON1, "person2" => $PERSON2, "results" => $r));
	          break;			
	  }
        
        return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:index.html.twig');
    }
    
    function sparql($query)
    {
        $config = array( 'remote_store_endpoint' => 'http://localhost:3030/data/query', );
        $store = \ARC2::getRemoteStore($config);
        
        $res = $store->query($query);
        $this->container->get('logger')->info('Local variables', get_defined_vars());
        
        if($res['result'] === "")
            return [];
            
        return $res['result']['rows'];
    }
    
    function distinct($arr, $uniqueColumn)
    {
        $added = array();
        $r = array();
        foreach($arr as $item)
        {
            if(!in_array($item[$uniqueColumn], $added))
            {
                $r[] = $item;
                $added[] = $item[$uniqueColumn];
            }
        }
        return $r;
    }

    function topTenInterestAction()
	{
		$prefix = "PREFIX : <http://www.semanticweb.org/sebastian/ontologies/2014/social-web-ontology#> ";
		$query = $prefix.'
		SELECT (?i AS ?Interest) (COUNT(?i) AS ?People_with_this_Interest) 
		WHERE
		{
			?p :hasInterest ?i.
		}
		GROUP BY ?i
                ORDER BY DESC(?People_with_this_Interest)
                LIMIT 10
		';
		$r = $this->sparql($query);
		return $this->render('SWebSemanticSocialWebBundle:PredefinedQueries:topTenInterest.html.twig', array( "Interests" => "Interests", 			"People_with_this_Interest" => "People with this Interest", "results" => $r));	
	}

}
