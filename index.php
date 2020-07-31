<?php
    require_once(dirname(__FILE__) . '/../../config.php');   
    $context = context_system::instance();
    global $USER, $PAGE, $DB;
    $PAGE->set_context($context);  
    $PAGE->set_url($CFG->wwwroot.'/local/short/index.php');   
    try{        
        require_login();
        if(!isset($USER->id)) throw new Exception("User id not found.");
        if(!isset($_GET['c']) && !isset($_GET['s'])) throw new Exception("No shortener or course specified.");
        if(isset($_GET['c'])){
            $mc = enrol_get_my_courses();

            $sql = '
                SELECT c.course_id, c.course_start, c.path 
                FROM '.$CFG->prefix.'cassign_courses AS c 
                LEFT JOIN '.$CFG->prefix.'cassign_shorts AS s
                ON s.id = c.short_id
                WHERE s.short_id = ?
                ';

            //$short = $DB->get_records_sql();




            echo $sql;
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