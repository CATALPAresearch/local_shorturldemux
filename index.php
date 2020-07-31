<?php
    require_once(dirname(__FILE__) . '/../../config.php');   
    $context = context_system::instance();
    global $USER, $PAGE, $DB;
    $PAGE->set_context($context);  
    $PAGE->set_url($CFG->wwwroot.'/local/short/index.php');   
    $url = new moodle_url('/');
    try{        
        require_login();
        if(!isset($USER->id)) throw new Exception("User id not found.");
        if(!isset($_GET['c']) && !isset($_GET['s'])) throw new Exception("No shortener or course specified.");
        if(isset($_GET['c'])){        
            $course = $DB->get_records_sql('SELECT c.course_id, c.path FROM '.$CFG->prefix.'cassign_courses AS c LEFT JOIN 
            '.$CFG->prefix.'cassign_shorts AS s ON s.id = c.short_id WHERE s.short = ?', array(strtolower($_GET['c'])));
            if(!is_array($course) || count($course) < 1) throw new Exception("No course found.");
            $mc = enrol_get_my_courses();      
            if(!is_array($mc) || count($mc) < 1) throw new Exception("No enrolled course found.");
            $found = null;            
            foreach($mc as $ec){
                foreach($course as $sc){                    
                    if(+$sc->course_id === +$ec->id){
                        if(!is_null($found)){
                            if($ec->startdate > $found->startdate){
                                $found = new stdClass();
                                $found->startdate = $ec->startdate;
                                $found->path = $sc->path;
                            }
                        } else {
                            $found = new stdClass();
                            $found->startdate = $ec->startdate;
                            $found->path = $sc->path;
                        }
                    }
                }
            }
            if(is_null($found)) throw new Exception("No matched course found.");  
            echo "Redirect to: ".$found->path;         
            $url = new moodle_url($found->path);           
        } else {            
            $link = $DB->get_records_sql('SELECT extern, link FROM '.$CFG->prefix.'cassign_links WHERE id = ? LIMIT 1', array($_GET['s']));
            var_dump($link);
        }       
    } catch(Exception $ex){
        echo $ex->getMessage();
    }
    //var_dump($url);
    //redirect($url);
?>