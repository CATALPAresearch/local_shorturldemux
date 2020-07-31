<?php
    require_once(dirname(__FILE__) . '/../../config.php');   
    $context = context_system::instance();
    global $USER, $PAGE;
    $PAGE->set_context($context);  
    $PAGE->set_url($CFG->wwwroot.'/local/short/index.php');   
    try{        
        require_login();
        if(!isset($USER->id)) throw new Exception("User id not found.");
        if(!isset($_GET['c']) && !isset($_GET['s'])) throw new Exception("No shortener or course specified.");
        if(isset($_GET['c'])){
            $mc = enrol_get_my_courses();
            
            /*$course = $_GET['c'];
            $uid = $USER->id;     
            
            $courses = Array();
            foreach($mc as $course){
                $courses[] = $course->id;
            }  
            var_dump($courses);*/
        } else {
            $short = $_GET['s'];
        }       
    } catch(Exception $ex){ 
        $url = new moodle_url('/');
        redirect($url);
    }
?>