
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

// the data manipulation API is available in global scope right after including the config.php file
require_once dirname(__FILE__) . '/../../config.php';

// A context is combined with role permissions to define a user's abilities on any page in Moodle
$context = context_system::instance();

//make the DB object available in your local scope
global $USER, $PAGE, $DB, $tabelle, $wert, $count, $getcourse;
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
$erg = $DB->get_records_sql(
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
          foreach ($erg as $key => $inside) {
              ?>
                <option value="<?php echo ($erg[$key]->id) ?>"><?php echo( $erg[$key]->id) ." / ". ( $erg[$key]->fullname) ." / ". ( $erg[$key]->shortname) ?></option>
              <?php
          }
        ?>
    </select>
    <!-- Input field Short URL -->
    <input type="text" name= "short-URL" placeholder="<?php echo get_string ('Kurz-URL', 'local_shorturldemux')  ?>"/>
    <!-- Send button -->
    <input type="submit" name="submit" value=<?php echo get_string ('course_button', 'local_shorturldemux')  ?> />
  </form>

<?php

  // check whether selection field can be displayed
  if(empty((int)$_GET['Course']) != true and empty($_GET['short-URL']) != true){

      //Query data from the DB
      $getcourse = (int)$_GET['Course'] ;
      $getshortURL = $_GET['short-URL'];

      $course = $DB->get_records_sql(
        "SELECT DISTINCT {shorturldemux_courses}.*,
        {course}.fullname
        FROM {shorturldemux_courses}
        LEFT JOIN {course} 
        ON {shorturldemux_courses}.course_id={course}.id 
        WHERE {shorturldemux_courses}.course_id = $getcourse
        AND {shorturldemux_courses}.short = '$getshortURL'
        UNION
        SELECT DISTINCT {shorturldemux_courses}.*,
        {course}.fullname
        FROM {shorturldemux_courses}
        RIGHT JOIN {course} 
        ON {shorturldemux_courses}.course_id={course}.id 
        WHERE {shorturldemux_courses}.course_id = $getcourse
        AND {shorturldemux_courses}.short = '$getshortURL'
        ");
    
        $Domain_Präfix="https://e.feu.de/";
        $Pfad_Präfix="https://aple.fernuni-hagen.de"; 

?>

    <!-- Decision field/selection list with module selection-->
    <form name = "taskSelect" method = "get">
      <select name="Task">
        <option selected disabled><?php echo get_string ('task', 'local_shorturldemux') ?> </option>
          <?php
           foreach ($course as $key => $inside) {
            ?>
              <option value = ""> <?php echo "<br>"; ?>  </optin> 
              <option value = "<?php echo ("1"."_".$course[$key]->id)."_".($course[$key]->short)."_".($course[$key]->course_id)."_".($course[$key]->path)  ?>"><?php echo"Domäne : ".( $course[$key]->short) ?></option>
              <option value = "<?php echo ("2"."_".$course[$key]->id)."_".($course[$key]->short)."_".($course[$key]->course_id)."_".($course[$key]->path)  ?>"><?php echo"Path   : ".( $course[$key]->path ) ?></option>
            <?php
           }
          ?>
      </select>
      <!-- Sende Button -->
     <input type="submit" value=<?php echo get_string ('LINK', 'local_shorturldemux')  ?> >
    </form>
  <?php
}

      // add the prefixn
      $separate = explode('_',$_GET['Task']);
      
  if($separate[0] >0){
    ?>
      <form name = "SaveSelect" method = "get">
        <!-- output LINK -->
        <?php if($separate[0] ==1){ ?>
        <?php echo get_string ('selected_path', 'local_shorturldemux')." : <a href= 'https://e.feu.de/'.$separate[2] > https://e.feu.de/$separate[2] </a> <br> <br>" ?> 
        <?php } ?>
        <?php if($separate[0] ==2){ ?>
        <?php echo get_string ('selected_path', 'local_shorturldemux')."  : <a href= 'https://aple.fernuni-hagen.de'.$separate[4] > https://aple.fernuni-hagen.de$separate[4] </a> <br> <br>" ?> 
        <?php } ?>
        <!-- preparation for entry in the DB -->
        <select name="Save">
          <option selected disabled><?php echo get_string ('task', 'local_shorturldemux') ?> </option>
          <option  value = <?php echo "1"."_".$separate[0]."_".$separate[1]."_".$separate[2]."_".$separate[3]."_".$separate[4] ?>>
          <?php echo get_string ('next_entry', 'local_shorturldemux'); ?> </option>
          <option  value = <?php echo "0"."_".$separate[0]."_".$separate[1]."_".$separate[2]."_".$separate[3]."_".$separate[4] ?>>
          <?php echo get_string ('old_entry', 'local_shorturldemux');  ?> </option>
        </select>
        <!-- Send Button -->
        <input type="submit" value=<?php echo get_string ('LINK-save', 'local_shorturldemux')   ?> >
      </form>
    <?php
      }

// Separate variables for storage
$separate2 = explode('_',$_GET['Save']);

  // create new entry
  if( $separate2[0] == 1 ){

    // write new Domaine
    if($separate2[1] == 1){
      $ins = new stdClass();
      $ins->short = 'https://e.feu.de/'.$separate2[3];
      $ins->course_id = $separate2[4];
      $ins->path= $separate2[5];
      $DB -> insert_record('shorturldemux_courses', $ins);
    }

    // write new path
    if($separate2[1] == 2){
      $ins = new stdClass();
      $ins->short = $separate2[3];
      $ins->course_id = $separate2[4];
      $ins->path= 'https://aple.fernuni-hagen.de'.$separate2[5];
      $DB -> insert_record('shorturldemux_courses', $ins);
    }

  }

  // overwrite content
  if( $separate2[0] == 0){

    // Refresh database
    $course2 = $DB->get_records_sql(
        "SELECT DISTINCT {shorturldemux_courses}.*,
        {course}.fullname
        FROM {shorturldemux_courses}
        LEFT JOIN {course} 
        ON {shorturldemux_courses}.course_id={course}.id 
        UNION
        SELECT DISTINCT {shorturldemux_courses}.*,
        {course}.fullname
        FROM {shorturldemux_courses}
        RIGHT JOIN {course} 
        ON {shorturldemux_courses}.course_id={course}.id 
      ");

    foreach ($course2 as $key => $inside) {
      
        // overwrite Domaine
        if($separate2[1] == 1){
          $ins = new stdClass();
          $ins->id = $separate2[2];
          $ins->short = 'https://e.feu.de/'.$separate2[3];
          $ins->course_id = $separate2[4];
          $ins->path= $separate2[5];
          $DB -> update_record('shorturldemux_courses', $ins);
        }
      
        // overwrite path
        if($separate2[1] == 2){
          $ins = new stdClass();
          $ins->id = $separate2[2];
          $ins->short = $separate2[3];
          $ins->course_id = $separate2[4];
          $ins->path= 'https://aple.fernuni-hagen.de'.$separate2[5];
          $DB -> update_record('shorturldemux_courses', $ins);
        }
    }
  }
  
echo $OUTPUT->footer();
?>