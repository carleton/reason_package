<?php
reason_include_once( 'minisite_templates/modules/default.php' );
$GLOBALS[ '_module_class_names' ][ basename( __FILE__, '.php' ) ] = 'MobileCafMenuModule';

class MobileCafMenuModule extends DefaultMinisiteModule {
    function init( $args = array() ) {

    }

    function has_content() {
        return true;
    }

    function run() {

        // file example 1: read a text file into a string with fgets
        $filename="https://reasondev.luther.edu/images/luther2010/mobile/WeeklyMenu_old.htm";
        $output="";
        $file = fopen($filename, "r");
        while(!feof($file)) {

            //read file line by line into variable
            $output = $output . fgets($file, 4096);

        }
        fclose ($file);

        $monday='<!-- MONDAY -->';
        $tuesday='<!-- TUESDAY -->';
        $wednesday='<!-- WEDNESDAY -->';
        $thursday='<!-- THURSDAY -->';
        $friday='<!-- FRIDAY -->';
        $saterday='<!-- SATURDAY -->';
        $sunday='<!-- SUNDAY -->';
        $end='<!-- END DAY DATA -->';

        //$handle = fopen($file, "r");
        //$contents = fread($handle, filesize($file));
        //fclose($handle);

        $betweenmon=substr($output, strpos($output, $monday), strpos($output, $tuesday) - strpos($output, $monday));
        $betweentues=substr($output, strpos($output, $tuesday), strpos($output, $wednesday) - strpos($output, $tuesday));
        $betweenwed=substr($output, strpos($output, $wednesday), strpos($output, $thursday) - strpos($output, $wednesday));
        $betweenthurs=substr($output, strpos($output, $thursday), strpos($output, $friday) - strpos($output, $thursday));
        $betweenfri=substr($output, strpos($output, $friday), strpos($output, $saterday) - strpos($output, $friday));
        $betweensat=substr($output, strpos($output, $saterday), strpos($output, $sunday) - strpos($output, $saterday));
        $betweensun=substr($output, strpos($output, $sunday), strpos($output, $end) - strpos($output, $sunday));

        echo $betweenmon;
        echo $betweentues;
        echo $betweenwed;
        echo $betweenthurs;
        echo $betweenfri;
        echo $betweensat;
        echo $betweensun;

        /**

        $do = preg_match("/<!-- MONDAY -->(.*)<!-- TUESDAY -->/", $output, $matches);

        // Check if regex was successful
        if ($do = true) {
            // Matched something, show the matched string
            echo htmlentities($matches['0']);

            // Also how the text in between the tags
            echo '<br />' . $matches['1'];
        } else {
            // No Match
            echo "Couldn't find a match";
        }
        //echo strip_tags($output);
        echo "<p><b>Done</b></p>";

        // Allow <p> and <a>
        //echo strip_tags($text, '<p><a>');

         *
         */

        ?>

        <?php
    }
}
?>
