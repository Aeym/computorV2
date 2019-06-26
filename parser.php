<?php 

check_entry($argv[1]);

function check_entry($line) {
    $error = 0;
    // check nb of =
    if (substr_count($line, "=") != 1) {
        echo "Nombre de '=' invalide (seulement un '=' est accepté)\n";
        $error = 1;
    }
    // check char type
    if (!preg_match("#^[a-zA-Z0-9\[\]\(\)\+\-\/\%\*\^\=]+$#", $line)) {
        echo "Les noms de variables/fonctions doivent uniquement comporter des lettres\n";
        echo "Les opérateurs gérés sont : +, -, *, /, %, ^\n";
        $error = 1;
     }
     if ($error == 0) {
         echo "OK!\n";
     }
     return $error;
}


?>