<?php
/**
 * TODO
 * The URL parameter s and c need to become more self explaining. 
 * For some reason we mixed them up so that c is used for the shortener string, 
 * instead for the course id.
 * 
 * @category Moodle
 * @package  Local_ShortURLdemux
 * @author   Niels Seidel <niels.seidel@fernuni-hagen.de>
 * @license  GPL https://www.gnu.org/licenses/gpl-3.0.de.html
 * @link     URL shortener, short URL
 */

require_once dirname(__FILE__) . '/../../config.php';   
$context = context_system::instance();
global $USER, $PAGE, $DB;
$PAGE->set_context($context);  
$PAGE->set_url($CFG->wwwroot.'/local/shorturldemux/index.php');   
$PAGE->set_url($CFG->wwwroot.'/local/shorturldemux/index.php', array('c' => $_GET['c'])); 

$url = new moodle_url('/');
try{        
    require_login();
    if (!isset($USER->id)) {
        throw new Exception(get_string('userid_not_found', 'local_shorturldemux'));
    }
    if (!isset($_GET['c']) && !isset($_GET['s'])) {
        throw new Exception(get_string('nothing_specified', 'local_shorturldemux'));
    }
    if (isset($_GET['c'])) {
        if (is_string($_GET['c']) == false || strlen($_GET['c']) > 200) {
            exit;
        }
        
        $course = $DB->get_records_sql(
            'SELECT c.course_id, c.path 
            FROM '.$CFG->prefix.'shorturldemux_courses AS c 
            WHERE c.short = ?',
            array(strtolower($_GET['c']))
        ); 
        if (!is_array($course) || count($course) < 1) {
            throw new Exception(
                get_string('no_course_found', 'local_shorturldemux')
            );
        } 
        $mc = enrol_get_my_courses();      
        if (!is_array($mc) || count($mc) < 1) {
            throw new Exception(
                get_string('no_course_enrolled', 'local_shorturldemux')
            );
        } 
        $found = null;            
        foreach ($mc as $ec) {
            foreach ($course as $sc) {                    
                if (+$sc->course_id === +$ec->id) {
                    if (!is_null($found)) {
                        if ($ec->startdate > $found->startdate) {
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
        if (is_null($found)) {
            throw new Exception(
                get_string('no_matching_course', 'local_shorturldemux')
            );  
        }
        $url = new moodle_url($found->path);
        header('Location: '.$url);
        
    }
} catch(Exception $ex) {
    echo $ex->getMessage();
    exit;
}
    
?>