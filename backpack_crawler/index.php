<?php
function customError($errno, $errstr) {
  echo "<b>Error:</b> [$errno] $errstr<br>";
  //die();
}
set_error_handler("customError", E_ERROR ); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);


include_once("BackpackExtractor.php");


$semester	= $_GET["semester"];
$parsing 	= $_GET["parsing"];
$fileJson 	= "parsed_data_$semester.json";
$fileRdf 	= "data_$semester.ttl";

$rdfPrefixes = "@prefix : <http://www.semanticweb.org/sebastian/ontologies/2014/social-web-ontology#> .
@prefix owl: <http://www.w3.org/2002/07/owl#> .
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix foaf: <http://xmlns.com/foaf/0.1/> .\n\n\n";

echo "<h1>BACKPACK PARSER</h1>";
// PREFIX : http://www.semanticweb.org/sebastian/ontologies/2014/social-web-ontology#

if($parsing)
{
	echo "parsing usebackpack.com: $semester<br><br>";
	$filename = "./website_data/$semester.html";
	$parser = new BackpackExtractor($filename);
	
	$courses = $parser->getCoursesData();
	
	$data = json_encode($courses, JSON_PRETTY_PRINT);
	
	file_put_contents($fileJson, $data);
	//echo $data;
}


echo "loading file: $fileJson<br><br>";
$data = file_get_contents($fileJson);
$courses = json_decode($data);
//echo $data;



$count_courses = count($courses);
$count_students = 0;
$prefix_course = "backpack-course-$semester-";
$prefix_user = "backpack-user-";
$statements = "";
foreach ($courses as $c) {
	$courseId = $prefix_course . $c->id;
    $statements .= ":$courseId
			rdf:type :Subject ;
    		:hasID \"".($c->id)."\" ;
    		:hasLabel \"".($c->label)."\" ;
    		:hasWebsite \"".($c->url)."\".\n\n";
    foreach($c->instructors as $i)
    {
    	//TODO: is this id unique?
    	$id = $prefix_user.name2id($i->name);
    	// basic user information
    	$statements .= ":$id
    			rdf:type :Faculty ;
    			:hasLabel \"".($i->name)."\" ;
    			foaf:name \"".($i->name)."\" ;
	    		:hasImage \"".($i->img)."\" .\n";
		if(($i->id) != "-1")
			$statements .= ":$id :hasID \"".($i->id)."\".\n";
    	// course membership
    	$statements .= ":$id :teaches :$courseId .\n\n";
    }
    foreach($c->assistants as $i)
    {
    	$id = $prefix_user.name2id($i->name);
    	// basic user information
    	$statements .= ":$id
    			rdf:type :Student ;
    			:hasLabel \"".($i->name)."\" ;
    			foaf:name \"".($i->name)."\" ;
	    		:hasImage \"".($i->img)."\" .\n";
		if(($i->id) != "-1")
			$statements .= ":$id :hasID \"".($i->id)."\".\n";
    	// course membership
    	$statements .= ":$id :isTeachingAssistantOf :$courseId .\n\n";
    }
    foreach($c->students as $i)
    {
    	$id = $prefix_user.name2id($i->name);
    	// basic user information
    		//TODO: create rdf properties 'hasID', 'hasImage', 'hasRollNo', 'hasEmail'
    	$statements .= ":$id
				rdf:type :Student ;
	    		:hasLabel \"".($i->name)."\" ;
    			foaf:name \"".($i->name)."\" ;
	    		:hasImage \"".($i->img)."\" ;
	    		:hasRollNo \"".($i->roll)."\" ;
	    		:hasEmail \"".($i->email)."\" .\n";
		if(($i->id) != "-1")
			$statements .= ":$id :hasID \"".($i->id)."\".\n";
    	// course membership
    		//TODO: create rdf property 'isStudent'
    	$statements .= ":$id :isStudent :$courseId .\n\n";
    	$count_students++;
    }
}

function name2id($name)
{
    $repl = array (
        ' ' => '_', 
        '(' => '', 
        ')' => '',
        '.' => '_',
        ',' => '_',
        ';' => '_',
        '@' => '-at-'
    );

    $cname = strtolower(trim($name));
    return strtr($cname, $repl);
}



file_put_contents($fileRdf, $rdfPrefixes.$statements);
//TODO: currently turtle files need to be manually uploaded through the Fuseki Control Panel.

echo "$count_courses courses with $count_students student registrations total<br><br>";
echo $statements;
