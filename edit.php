
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

//Sparche wird zu Beginn inital festgelegt und später per Auswahl
// isset to check whether the parameter was really supplied
$lang = $_GET['lang'];
  if (empty($lang) == true) {
    $lang = 'en';
  }
  if ($lang == 'de') { 
    include(dirname(__FILE__) . '/../../local/shorturldemux/lang/de/local_shorturldemux.php');
  } 
  if ($lang  == 'en') {
    include (dirname(__FILE__) . '/../../local/shorturldemux/lang/en/local_shorturldemux.php');
  }

// the data manipulation API is available in global scope right after including the config.php file
require_once dirname(__FILE__) . '/../../config.php';

// A context is combined with role permissions to define a user's abilities on any page in Moodle
$context = context_system::instance();

//make the DB object available in your local scope
global $USER, $PAGE, $DB, $tabelle, $wert, $count, $getcourse;
$PAGE->set_context($context);
$PAGE->set_url($CFG->wwwroot . '/local/shorturldemux/edit.php');



// checks whether a variable is occupied, that means whether a variable is declared and is different from null.
if (isset($_GET['submit'])) {
  $getcourse = $_GET['Course'];
}

// The text for the header is set here. A reference to the selected course is added to the header.
if ($getcourse > 0) {
  $PAGE->set_heading($string['course']." ".$getcourse = $_GET['Course']);
} else {
  $PAGE->set_heading($string['überschirft']);
}

//Header is added
echo $OUTPUT->header();

// List of courses
$erg = $DB->get_records_sql(
  'SELECT DISTINCT {course}.id, {course}.fullname,
  {course}.shortname 
  FROM {course}
  ')or die($db->error);

?>

  <!-- Decision field/selection list with module selection-->
  <form name = "coursSelect" action = "edit.php" method = "get">
    <select name="Course">
      <option selected disabled><?php echo $string['course_ID'] ?> </option>
        <?php
          foreach ($erg as $key => $inside) {
              ?>
                <option value="<?php echo ($erg[$key]->id) ?>"><?php echo( $erg[$key]->id) ." / ". ( $erg[$key]->fullname) ." / ". ( $erg[$key]->shortname) ?></option>
              <?php
          }
        ?>
    </select>
    <!-- Eingabefeld Kurz URL -->
    <input type="text" name= "short-URL" placeholder="<?php echo $string['Kurz-URL'] ?>">
    <!-- Sende Button -->
    <input type="submit" name="submit" value=<?php echo $string['course_button'] ?> />
  </form>

<?php

// check whether selection field can be displayed
if(empty((int)$_GET['Course']) != true and empty($_GET['short-URL']) != true){
    $getcourse = (int)$_GET['Course'] ;
    $getshortURL = $_GET['short-URL'];

     $course = $DB->get_records_sql(
     //$course = $db->query(
      "SELECT DISTINCT mdl_shorturldemux_courses.*,
      mdl_course.fullname
      FROM mdl_shorturldemux_courses
      LEFT JOIN mdl_course 
      ON mdl_shorturldemux_courses.course_id=mdl_course.id 
      WHERE mdl_shorturldemux_courses.course_id = $getcourse
      AND {shorturldemux_courses}.short = '$getshortURL'
      UNION
      SELECT DISTINCT mdl_shorturldemux_courses.*,
      mdl_course.fullname
      FROM mdl_shorturldemux_courses
      RIGHT JOIN mdl_course 
      ON mdl_shorturldemux_courses.course_id=mdl_course.id 
      WHERE mdl_shorturldemux_courses.course_id = $getcourse
      AND {shorturldemux_courses}.short = '$getshortURL'
      ");

  ?>
    <!-- Decision field/selection list with module selection-->
    <form name = "taskSelect" action = "edit.php" method = "get">
      <select name="Task">
        <option selected disabled><?php echo $string['task'] ?> </option>
          <?php
           foreach ($course as $key => $inside) {
            ?>
              <option value="submit"><?php echo( $course[$key]->path) ?></option>
            <?php
           }
          ?>
      </select>
      <!-- Sende Button -->
      <input type="submit" name="submit" value=<?php echo $string['course_button'] ?> />
    </form>
  <?php
}

echo $OUTPUT->footer();
?>