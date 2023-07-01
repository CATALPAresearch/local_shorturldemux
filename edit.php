
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
$PAGE->set_url($CFG->wwwroot . '/local/shorturldemux/edit.php');
require_login();

// checks whether a variable is occupied, that means whether a variable is declared and is different from null.
if (isset($_GET['submit'])) {
  $getcourse = $_GET['Course'];
}

// The text for the header is set here. A reference to the selected course is added to the header.
if ($getcourse > 0) {
  $PAGE->set_heading(get_string ('course', 'local_shorturldemux')." ".$getcourse = $_GET['Course']);
} 

//Header is added
echo $OUTPUT->header();

// edit List of courses
$result = $DB->get_records_sql(
  'SELECT DISTINCT {course}.id, {course}.fullname,
  {course}.shortname 
  FROM {course}
  ');

?>
  <!-- Decision field/selection list with module selection-->
  <form name = "coursSelect" action = "edit.php" method = "get">
    <select name="Course">
      <option selected disabled><?php echo get_string ('course_ID', 'local_shorturldemux') ?> </option>
        <?php
          foreach ($result as $key => $inside) {
              ?>
                <option value="<?php echo ($result[$key]->id) ?>"><?php echo( $result[$key]->id) ." / ". ( $result[$key]->fullname) ." / ". ( $result[$key]->shortname) ?></option>
              <?php
          }
        ?>
    </select>
     <!-- Send button -->
    <input type="submit" name="submit" value=<?php echo get_string ('course_button', 'local_shorturldemux')  ?> />
  </form>
<?php

// OUTPUT of the module selection
if (isset($_GET['Course'])) {

  if ($getcourse = $_GET['Course'] > 0) {
  
  //Query data from the DB
  $getcourse = (int)$_GET['Course'] ;

  $course = $DB->get_records_sql(
    "SELECT DISTINCT a.*,
    b.fullname
    FROM {shorturldemux_courses} a
    LEFT JOIN {course} b
    ON a.course_id=b.id 
    WHERE a.course_id = $getcourse
    
    UNION

    SELECT DISTINCT a.*,
    b.fullname
    FROM {shorturldemux_courses} a
    RIGHT JOIN {course} b
    ON a.course_id=b.id 
    WHERE a.course_id = $getcourse
    ");

  // output of the table
    ?>
    <form method="get" name="Coure">
      <table cellspacing="0" name="Course">
        <caption> <?php echo get_string ('course', 'local_shorturldemux') ?></caption>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">short</th>
          <th scope="col"> <?php echo get_string ('table_change', 'local_shorturldemux') ?> </th>
          <th scope="col">course_id</th>
          <th scope="col">path</th>
          <th scope="col"> <?php echo get_string ('table_change2', 'local_shorturldemux') ?> </th>
          <th scope="col">fullname</th>
          <th scope="col"> <?php echo get_string ('delete_line', 'local_shorturldemux') ?> </th>
        </tr>
        <?php
        foreach ($course as $key1 => $inside1) {
        ?>
          <tr>
            <th scope="row"><?php echo ($course[$key1]->id) ?></th>
            <th scope="row"><?php echo ($course[$key1]->short) ?></th>
            <td><input type="text" name= "<?php echo ($course[$key1]->id) ?>" /></td>
            <th scope="row"><?php echo ($course[$key1]->course_id) ?></th>
            <th scope="row"><?php echo ($course[$key1]->path) ?></th>
            <td><input type="text" name= "<?php echo ($course[$key1]->id)."_" ?>" /></td>
            <th scope="row"><?php echo ($course[$key1]->fullname) ?></th>
            <th scope="row"> <input type="checkbox" name=  "<?php echo ($course[$key1]->id)."box"?>" value='1' > </th>
          </tr>
        <?php
        }
        ?>
      </table>
      <select name="Save">
          <option selected disabled><?php echo get_string ('task', 'local_shorturldemux') ?> </option>
          <option  value = <?php echo "1"."_".$course[$key1]->course_id."_".$course[$key1]->fullname."_" ?>>
          <?php echo get_string ('next_entry', 'local_shorturldemux'); ?></option>
          <option  value = <?php echo "0"."_".$course[$key1]->course_id."_".$course[$key1]->fullname."_" ?>>
          <?php  echo get_string ('old_entry', 'local_shorturldemux');  ?></option>
          <option  value = <?php echo "2"."_".$course[$key1]->course_id."_".$course[$key1]->fullname."_" ?>>
          <?php  echo get_string ('delete_entry', 'local_shorturldemux');  ?></option>
      </select>
         <!-- Send Button -->
         <input type="submit" value=<?php echo get_string ('LINK-save', 'local_shorturldemux')   ?> >
    </form>
    <?php
  }
}
// Separate variables for storage
$separate2 = explode('_',$_GET['Save']);
  // write new line into DB
  if( $separate2[0] == 1 ){
      
          // if short or path has been changed
          foreach ($_GET as $key=>$val) {
            $key2 = $key."_";
            // if short and path has been changed
            if((empty($_GET[$key]) != true && empty($_GET[$key2]) != true  && $key != "Save" )){
              $insert->short = $_GET[$key];
              $insert->course_id = $separate2[1];
              $insert->path =  $_GET[$key2];
              $insert->fullname = $separate2[2];
              $DB -> insert_record('shorturldemux_courses', $insert);
              $_GET[$key2] = null;
            }else{
              // array with line content
              $newline=null;
              // Insert id depending on path or short
              $insert = new stdClass();
              // if short has been changed
              if(strpos($key, '_') == false && empty($_GET[$key]) != true  && $key != "Save" ){
                $newline[2] = $_GET[$key];
              }
              // if path has been changed
              if(strpos($key, '_') != false && empty($_GET[$key]) != true  && $key != "Save"  ){
              $newline[3] = $_GET[$key];
              }
              // write values into DB
              if(empty($_GET[$key]) != true  && $key != "Save"){
                $insert->short = $newline[2];
                $insert->course_id = $separate2[1];
                $insert->path =  $_GET[$key];
                $insert->path = $newline[3];
                $insert->fullname = $separate2[2];
                $DB -> insert_record('shorturldemux_courses', $insert);
              }
            }
          }
  }
  // overwrite content in DB
  if( $separate2[0] == 0){
      // if short has been changed
      foreach ($_GET as $key=>$val) {
        // Insert id depending on path or short
        $insert = new stdClass();
        // set id for change 
        if(strpos($key, '_') == false && empty($_GET[$key]) != true && $key != "Save" ){
          $insert->id = $key;
        }
        if(strpos($key, '_') != false && empty($_GET[$key]) != true && $key != "Save" ){
          $search = '_';
          $replace = '';
          $string = str_replace( $search, $replace, $key );
          $insert->id = $string;
        }
        // if short has been changed
        if(strpos($key, '_') == false && empty($_GET[$key]) != true && $key != "Save" ){
         $insert->short = $_GET[$key];
        }
         // if path has been changed
        if(strpos($key, '_') != false && empty($_GET[$key]) != true && $key != "Save"  ){
          $insert->path = $_GET[$key];
        }
        // write values into DB
        if(empty($_GET[$key]) != true && empty($_GET[$key]) != true && $key != "Save" ){
         $DB -> update_record('shorturldemux_courses', $insert);
        }
      }
  }
  // delete line or lines
  if( $separate2[0] == 2){
    foreach ($_GET as $key=>$val) {
       if(strpos($key, 'box') != false && $_GET[$key] == 1){
          $DB -> delete_records('shorturldemux_courses', array('id'=>$key));
       }
    }
  }
echo $OUTPUT->footer();
?>
