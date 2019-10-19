<?php 

require_once("./calcul.php");
require_once("./parser.php");
require_once("./assign.php");
require_once("./poly.php");

$arrVar = array();
$lines = array();
// fwrite(STDIN, "vara = 2");

while (1) {
    $line = strtolower(stream_get_line(STDIN, 1024, PHP_EOL));
    if ($line == "close") {
        exit;
    } else if ($line == "histo") {
        print_r($GLOBALS["lines"]);
    } else if ($line == "variables") {
        print_r($GLOBALS["arrVar"]);
    } else {
        if (check_entry($line) == 0 && parse($line) == 0) {
            array_push($lines, $line);
        }
    }
}


?>