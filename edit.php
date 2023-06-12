
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


// Language is initially set at the beginning and later by selection
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
global $USER, $PAGE, $DB, $tabelle, $wert, $count, $getcourse, $shortURL;
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
      <option selected disabled><?php echo $string['course_ID'] ?> </option>
        <?php
          foreach ($erg as $key => $inside) {
              ?>
                <option value="<?php echo ($erg[$key]->id) ?>"><?php echo( $erg[$key]->id) ." / ". ( $erg[$key]->fullname) ." / ". ( $erg[$key]->shortname) ?></option>
              <?php
          }
        ?>
    </select>
    <!-- Input field Short URL -->
    <input type="text" name= "short-URL" placeholder="<?php echo $string['Kurz-URL'] ?>">
    <!-- Send button -->
    <input type="submit" name="submit" value=<?php echo $string['course_button'] ?> />
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
        <option selected disabled><?php echo $string['task'] ?> </option>
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
     <input type="submit" value=<?php echo $string['LINK'] ?> >
    </form>
  <?php
}

      // add the prefixn
      $trennen = explode('_',$_GET['Task']);
      
  if($trennen[0] >0){
    ?>
      <form name = "SaveSelect" method = "get">
        <!-- output LINK -->
        <?php if($trennen[0] ==1){ ?>
        <?php echo " Der ausgewählte und neue Pfad : <a href= 'https://e.feu.de/'.$trennen[2] > https://e.feu.de/$trennen[2] </a> <br> <br>" ?> 
        <?php } ?>
        <?php if($trennen[0] ==2){ ?>
        <?php echo " Der ausgewählte und neue Pfad : <a href= 'https://aple.fernuni-hagen.de'.$trennen[4] > https://aple.fernuni-hagen.de$trennen[4] </a> <br> <br>" ?> 
        <?php } ?>
        <!-- preparation for entry in the DB -->
        <select name="Save">
          <option selected disabled><?php echo $string['task'] ?> </option>
          <option  value = <?php echo "1"."_".$trennen[0]."_".$trennen[1]."_".$trennen[2]."_".$trennen[3]."_".$trennen[4] ?>>
          <?php echo "nächsten Eintrag erzeugen"  ?></option>
          <option  value = <?php echo "0"."_".$trennen[0]."_".$trennen[1]."_".$trennen[2]."_".$trennen[3]."_".$trennen[4] ?>>
          <?php echo"alten Eintrag überschreiben" ?></option>
        </select>
        <!-- Send Button -->
        <input type="submit" value=<?php echo $string['LINK-save'] ?> >
      </form>
    <?php
      }

// Separate variables for storage
$trennen2 = explode('_',$_GET['Save']);

  // create new entry
  if( $trennen2[0] == 1 ){

    // write new Domaine
    if($trennen2[1] == 1){
      $ins = new stdClass();
      $ins->short = 'https://e.feu.de/'.$trennen2[3];
      $ins->course_id = $trennen2[4];
      $ins->path= $trennen2[5];
      $DB -> insert_record('shorturldemux_courses', $ins);
    }

    // write new path
    if($trennen2[1] == 2){
      $ins = new stdClass();
      $ins->short = $trennen2[3];
      $ins->course_id = $trennen2[4];
      $ins->path= 'https://aple.fernuni-hagen.de'.$trennen2[5];
      $DB -> insert_record('shorturldemux_courses', $ins);
    }

  }

  // overwrite content
  if( $trennen2[0] == 0){

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
        if($trennen2[1] == 1){
          $ins = new stdClass();
          $ins->id = $trennen2[2];
          $ins->short = 'https://e.feu.de/'.$trennen2[3];
          $ins->course_id = $trennen2[4];
          $ins->path= $trennen2[5];
          $DB -> update_record('shorturldemux_courses', $ins);
        }
      
        // overwrite path
        if($trennen2[1] == 2){
          $ins = new stdClass();
          $ins->id = $trennen2[2];
          $ins->short = $trennen2[3];
          $ins->course_id = $trennen2[4];
          $ins->path= 'https://aple.fernuni-hagen.de'.$trennen2[5];
          $DB -> update_record('shorturldemux_courses', $ins);
        }
    }
  }
  
echo $OUTPUT->footer();
?>