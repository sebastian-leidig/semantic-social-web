<?php
/**
  * This class defines xPaths to extract relevant information from the usebackpack.com HTML source code.
  */

class BackpackExtractor
{
    const BACKPACK_ROOT = "http://www.usebackpack.com";

    public $xpath;

    function __construct($url) {
        $content = file_get_contents($url);

        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $dom->preserveWhiteSpace = false; 
        $this->xpath = new DOMXPath($dom);
        
        $this->prefix = $idPrefix;
    }
    
    
    /**
      * Returns a (list of) matching DOMNodes.
      * @see http://php.net/manual/en/class.domnode.php
      */
    function parse($key) {
        return $this->xpath->query($this->xPatterns[$key]);
    }
    
    /**
      * Returns an array of arrays with each inner array describing one course with all its parsed information.
      */
    public function getCoursesData()
    {
        $ret = array();
        
        $courses = $this->parse('courses');
        foreach ($courses as $c) 
        {
            $cid = trim($c->getAttribute('class'));
            $id = $cid;
            $url = $c->getAttribute('href');
        	$label = trim($c->nodeValue);
            
            $courseParser = new BackpackExtractor($url."/students", "");
			$instr = $courseParser->getCourseInstructors('instructors');
			$ta = $courseParser->getCourseInstructors('assistants');
			$students = $courseParser->getCourseStudents();
			
            $courseInfo = array(
                'id'    => $id,
                'url'   => $url,
                'label' => $label,
                'instructors'   => $instr,
                'assistants'    => $ta,
                'students'      => $students 
            );
            
            $ret[] = $courseInfo;
        }
        
        return $ret;
    }
    
    
    public function getCourseInstructors($type)
    {
        $ret = array();
    
        $instructors = $this->parse($type);
        foreach ($instructors as $i)
        {
            $ret[] = ($this->parseInstructor($i));
        }
        
        return $ret;
    }
    
    function parseInstructor($node)
    {
    	$img = $node->getAttribute('src'); //e.g. "/photos/145/M.jpg?1376307767"
    	$userNumber = $this->getUserIdFromImg($img);
        
        return array(
            'id'    => $userNumber,
            'name'  => $node->getAttribute('title'),
            'img'   => self::BACKPACK_ROOT . $img
        );
    }
    
    public function getCourseStudents()
    {
        $ret = array();
        
        $students = $this->parse('students');
        foreach ($students as $i)
        {
            $ret[] = ($this->parseStudent($i));
        }
        
        return $ret;
    }
    
    function parseStudent($node)
    {
    	$img = $this->xpath->evaluate($this->xPatterns['students_img'], $node)->item(0)->getAttribute('src');   
        $userNumber = $this->getUserIdFromImg($img);
        $name = trim($this->xpath->evaluate($this->xPatterns['students_name'], $node)->item(0)->nodeValue);
        $roll = trim($this->xpath->evaluate($this->xPatterns['students_roll'], $node)->item(0)->nodeValue);
        $email = trim($this->xpath->evaluate($this->xPatterns['students_email'], $node)->item(0)->nodeValue);
        
        return array(
            'id'    => $userNumber,
            'name'  => $name,
            'img'   => self::BACKPACK_ROOT . $img,
            'roll'  => $roll,
            'email' => $email
        );
    }
    
    function getUserIdFromImg($img)
    {
    	if (strpos($img,'default') === false)
    		return explode("/", $img)[2];
    	else
    		return "-1";
    }
    
    private $xPatterns = array(
    
        /* ### COURSE LIST ###
        <span style="display: block; max-height: 40px; overflow: hidden; word-wrap: break-word;" class="course_link"><a class="cse694f" data-push="true" data-target="#yield" href="http://www.usebackpack.com/iiitd/w2014/cse694f">
        Media Security
        </a></span> */
        // courseID      =   attribute:class
        // website       =   attribute:href
        // courseName    =   textContent
        'courses' => "//span[contains(@class,'course_link')]/a",
        
        
        /* ### COURSE DETAILS: /* ###
        <div class="col-xs-6 col-sm-3 add-info" style="color:#fff">
        <p><strong>Instructor</strong></p>
            <a href="/users/145" class="ajaxlink" data-remote="true" data-target="#user_profile_modal" data-toggle="modal">
                <img data-original-title="Anupama Mallik" title="" data-toggle="tooltip" data-placement="top" src="/photos/145/M.jpg?1376307767" style="width:30px;height:30px;margin-bottom:5px;margin-right:10px;" class="img-circle tooltip_class  badge-icon ta-photo">
              
                Anupama Mallik
              </a>
            <br>
        <br>
        </div>*/
        // name  =   attribute:title
        // image =   attribute:src (prefix http://www.usebackpack.com/)
        'instructors' => "//*[text()[contains(.,'Instructor')]]/../..//img[contains(@class, 'ta-photo')]",
        
        
        /* ### COURSE DETAILS: /* ###
        <div class="col-xs-6 col-sm-3 add-info">
            <p><strong>Teaching Assistant</strong></p>
                
                  <a href="/users/497" class="ajaxlink" data-remote="true" data-target="#user_profile_modal" data-toggle="modal">
                    <img data-original-title="Adarsh Dubey" title="" src="/assets/default_avatar.png" data-toggle="tooltip" data-placement="top" style="width:30px;height:30px;margin-bottom:5px;margin-right:10px;" class="img-circle  tooltip_class badge-icon ta-photo">
                 Adarsh Dubey
                 </a>
                <br>
        </div>*/
        //
        'assistants' => "//*[text()[contains(.,'Teaching Assistant')]]/../..//img[contains(@class, 'ta-photo')]",
        
        
        /* ### COURSE DETAILS: /students
        <table class="table table-striped">
        <tr>
            <td class="table-picture">
              <a href="/users/37"
                 data-remote="true"
                 data-target="#user_profile_modal"
                 data-toggle="modal"
                 class="ajaxlink">
                <img src="/photos/37/M.jpg?1375902579" style="height:30px;width:30px;max-width:none;" class="img-circle">
              </a>
            </td>
            <td class="table-question">
                <a href="/users/37"  
                   data-remote="true"
                   data-target="#user_profile_modal"
                   data-toggle="modal"
                   class="ajaxlink">
                  Aditya Gupta
                </a>
            </td>
            <td class="table-time">2011009</td>
            <td class="table-time">aditya11009@iiitd.ac.in </td>
        </tr>
        <tr>*/
        'students'  => "//table/tr",
            //append to 'students':
            'students_img'  => "td[contains(@class, 'table-picture')]//img",
            'students_name' => "td[contains(@class, 'table-question')]",
            'students_roll' => "td[3]",
            'students_email' => "td[4]"
    );
}



