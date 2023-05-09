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


// Hier beginnt die Seite
  
//Sparche wird zu Beginn inital festgelegt und später per Auswahl
  if($lang=$_GET['langID'] == 'en'){
  include ('/xampp/htdocs/mylearn/local/shorturldemux/lang/en/local_shorturldemux.php') ;
  }
  else{
  include ('/xampp/htdocs/mylearn/local/shorturldemux/lang/de/local_shorturldemux.php');  
  }


// Initiierungsfeld der Seite
require_once dirname(__FILE__) . '/../../config.php'; 
  
require_login();

$context = context_system::instance();
global $USER, $PAGE, $DB,$tabelle,$wert,$count,$getcourse;
$PAGE->set_context($context);  
$PAGE->set_url($CFG->wwwroot.'/local/shorturldemux/edit.php');

//Prüft, ob eine Variable als belegt gilt, d.h., ob eine Variable deklariert ist und sich von null unterscheidet.
  if(isset($_GET['submit'])){
    $getcourse=$_GET['Course'];
  }

  //Header Text setzen mit der Hinweis auf den ausgewählten Kurs
  if($getcourse>0)
  {
  $PAGE->set_heading($string['course']." ".$getcourse=$_GET['Course']);
  }
  else
  {
    $PAGE->set_heading($string['überschirft']);
  }

echo $OUTPUT->header();


// Verbinden zweier Datenblätter per sql Befehl
    $dbconn3 = pg_connect("host=localhost port=5432 dbname=mylearn user=mylearn_user password=1234");
    $result = pg_query($dbconn3, 
      "SELECT DISTINCT  myl_shorturldemux_courses.course_id, 
      myl_course.fullname
      FROM myl_shorturldemux_courses
      FULL OUTER JOIN myl_course
      ON myl_shorturldemux_courses.course_id=myl_course.id 
    ");
    // Hier wird geprüft ob es einen Fehlter beim DB aufbau gibt
    if (!$result) {
        echo "Ein Fehler ist aufgetreten.\n";
      exit;
    }
    $arr = pg_fetch_all($result);
 ?>

<!-- Entschiedungsfeld/Auswahlliste mit Modul-, und Sprachauswahl-->
<form name = "coursSelect" action = "edit.php" method = "get">
  
  <!--Hier wird die Sprache ausgewählt -->
  <select name = "langID" id = "langID" >   
      <option selected disabled>-- <?php echo $string['choose'] ?> --</option>
      <option value = "de" > <?php echo $string['language1'] ?> </option > 
      <option value = "en"> <?php echo $string['language2'] ?> </option > 
  </select> 
  <select name="Course">
<option selected disabled>-- <?php echo $string['course_ID'] ?> --</option>

<?php
$ungerade=1;
foreach($arr as $schluessel => $innen) {
  foreach($innen as $innerer_schluessel => $wert) {
    //Hier wird die Spalte course_id mit der Spalte fullname in der Auswahlliste zusammen ausgegeben
    if($ungerade %2 !=0){
    //Hier erfolgt die Ausgabe
    $next=next($innen);
    ?> <option value="<?php echo $wert ?>" ><?php echo $wert; echo " / ".$next ?></option><?php
  }
  $ungerade=$ungerade+1;
  }
  ?> 

  <?php
}
  ?>
</select>
<input type="submit" name="submit" value=<?php echo $string['course_button'] ?>/>
</form>

<?php

//AUSGABE der Modulauswahl
if(isset($_GET['Course'])){

    if($getcourse=$_GET['Course'] >0 ){
    echo "<br> ";

     //Verbindung zur Datenbank zur Ausgabe der im Dropdown ausgewählten Parameter
     //Verbindung zur Datenbank für join
     $course = $DB->get_records_sql(
      'SELECT  myl_shorturldemux_courses.*, 
               myl_course.fullname
      FROM myl_shorturldemux_courses
      FULL OUTER JOIN myl_course
      ON myl_shorturldemux_courses.course_id=myl_course.id 
      WHERE myl_shorturldemux_courses.course_id = '.$getcourse=$_GET['Course'].'
      ');

  echo '<table>';
          
  echo '<thead>';
  echo '<table border=1>';
          echo'<th> ID </th>';
          echo'<th> short </th>';
          echo'<th> course_id </th>';
          echo'<th> path </th>';
          echo'<th> fullname </th>';
  echo '</thead>';
 
 foreach($course as $schluessel => $innen) {

  echo '<tbody>';

  foreach($innen as $innerer_schluessel => $wert) {
      echo '<td><a>'.$wert.' </a></td>';
  }
 
    }
    echo '</tbody>';
    echo '</table>';
    echo "<br> ";
  }
    echo "<br> ";
    
    //Link prüfen
    if(isset($_GET['course_id'])){
      echo "<br> ";
        echo $_GET['course_id'];
    }

  if($getcourse == 0){
    echo "Kurswahl : ".$getcourse;
    echo "<br> ";
  }
}

echo $OUTPUT->footer();
?>

