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
// NS: Finde Heraus, wie man in VS Code den Programmcode so formatieren kann, dass die Einrückungen und Leerzeichen korrekt sind. -->ERLEDIGT

//Sparche wird zu Beginn inital festgelegt und später per Auswahl
// NS: Finde heraus wie man relative Pfade angeben kann. Die folgende Pfade gibt es bei mir nicht. -->ERLEDIGT


if ($lang = locale_get_default() == 'en_utf8') { 
  include(dirname(__FILE__) . '/../../local/shorturldemux/lang/en/local_shorturldemux.php');
} else {
  include (dirname(__FILE__) . '/../../local/shorturldemux/lang/de/local_shorturldemux.php');
}

require_once dirname(__FILE__) . '/../../config.php';


$context = context_system::instance();
global $USER, $PAGE, $DB, $tabelle, $wert, $count, $getcourse;
$PAGE->set_context($context);
$PAGE->set_url($CFG->wwwroot . '/local/shorturldemux/edit.php');

// NS: Schreibe Kommentare auf englisch! -->ERLEDIGT
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

echo $OUTPUT->header();


// Join two datasheets using sql command
// NS: Das ist nicht dir korrekte Art, um sich mit der Moodle-Datenbank zu verbinden. Stelle dir vor, jemand betreibt eine MariaDB, 
// NS: statt einer Postgres-DB für Moodle. Das geht so nicht. Schaue dir die Moodle Data API an! -> ERLEDIGT
$arr = $DB->get_records_sql(
  'SELECT DISTINCT myl_shorturldemux_courses.course_id, 
           myl_course.fullname
  FROM myl_shorturldemux_courses
  FULL OUTER JOIN myl_course
  ON myl_shorturldemux_courses.course_id=myl_course.id 
  '
);

?>

<!-- Decision field/selection list with module and language selection-->
<!-- NS: Nein, die Sprache stellt man Systemweit in Moodle ein, nicht auf jeder einzelnen Seite! ==> ERLEDIGT sihe Zeile 23 Aber ich kann nur englsich in moodle wählen-->  
<form name = "coursSelect" action = "edit.php" method = "get">
<select name="Course">
  <option selected disabled>
    <?php echo $string['course_ID'] ?> 
  </option>

  <?php
  // NS: Variablen bitte in englischer Sprache benennen. --> ERLEDIGT
  $odd = 1;
  foreach ($arr as $key => $inside) {
    foreach ($inside as $innerer_key => $value0) {
      // Here the course_id column is output together with the fullname column in the selection list
      if ($odd % 2 != 0) {
        // Here is the output
        $next = next($inside);
        ?>
        <option value="<?php echo $value0 ?>"><?php echo $value0;
           echo " / " . $next ?></option>
        <?php
      }
      $odd = $odd + 1;
    }
    ?>

    <?php
  }
  ?>
</select>
<input type="submit" name="submit" value=<?php echo $string['course_button'] ?> />
</form>

<?php

// OUTPUT of the module selection
if (isset($_GET['Course'])) {

  if ($getcourse = $_GET['Course'] > 0) {
    echo "<br> ";

    // Connection to the database to output the parameters selected in the dropdown
    // Connection to database for join
    // NS: Hier hast du Moodle Data API korrekt genutzt. Einzige das direkte einfügen der Variable course öffnet Tütr un dTor für SQL-Injektionen. Wir macht man das besser?
    // -> Erledigt aus meinen dafürhalten gibt es hier 3 Mglk 1) Quotes setzen "'.$_GET['Course'].'"' 2)  direkt auf einen String prüfen  if ((string)((int)$_GET['Course']) !== $_GET['Course']) {  exit('Fehlerhafter Wert!'); } $getcourse= $_GET['id']; 
    // 3) direkt filtern (int)
  $id = $_GET['id']; 
    $course = $DB->get_records_sql(
      'SELECT  myl_shorturldemux_courses.*, 
               myl_course.fullname
      FROM myl_shorturldemux_courses
      FULL OUTER JOIN myl_course
      ON myl_shorturldemux_courses.course_id=myl_course.id 
      WHERE myl_shorturldemux_courses.course_id = ' . $getcourse = (int)$_GET['Course'] . '
      '
    );

    echo '<table>';

    echo '<thead>';
    echo '<table border=1>';
    echo '<th> ID </th>';
    echo '<th> short </th>';
    echo '<th> course_id </th>';
    echo '<th> path </th>';
    echo '<th> fullname </th>';
    echo '</thead>';

    foreach ($course as $key1 => $inside1) {

      echo '<tbody>';

      foreach ($inside1 as $innerer_key1 => $value1) {
        echo '<td><a>' . $value1 . ' </a></td>';
      }

    }
    echo '</tbody>';
    echo '</table>';
    echo "<br> ";
  }
  echo "<br> ";

  //Link prüfen
  if (isset($_GET['course_id'])) {
    echo "<br> ";
    echo $_GET['course_id'];
  }

  if ($getcourse == 0) {
    echo "Kurswahl : " . $getcourse;
    echo "<br> ";
  }
}

echo $OUTPUT->footer();
?>