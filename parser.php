<?php 

// check_entry($argv[1]);

function check_entry($line) {
    $error = 0;
    // check nb of =
    if (substr_count($line, "=") != 1) {
        echo "Nombre de '=' invalide (seulement un '=' est accepté)\n";
        $error = 1;
    }
    // check char type
    if (!preg_match("#^[a-zA-Z0-9\[\]\(\)\+\-\/\%\*\^\=\. ]+$#", $line)) {
        echo "Les noms de variables/fonctions doivent uniquement comporter des lettres\n";
        echo "Les opérateurs gérés sont : +, -, *, /, %, ^\n";
        $error = 1;
     }
     if (preg_match("#[a-z]+ +[a-z]+#i", $line)) {
         echo "Erreur de syntaxe\n";
         $error = 1;
     }
     if (preg_match("#[\+\-\*\/\%\.][\+\-\*\/\%\.]#", $line)) {
        echo "Erreur de syntaxe\n";
        $error = 1;
     }
     if (substr_count($line, '[') != substr_count($line, ']')) {
         echo "Nombre de crochets ouvrants et fermants incorrect.\n";
         $error = 1;
     }
     if ($error == 0) {
        //  echo "OK!\n";
     }
     return $error;
}

function parse($line) {
    $error = 0;
    $tmpStr = preg_replace("/\s+/", '', $line);
    $tmpArr = explode('=', $tmpStr);
    if ($tmpArr[0] == '' || $tmpArr[1] == '') {
        $error = 1;
        echo "Erreur de syntaxe\n";
    }
    // on commence par faire les assignations de variables et de fonctions
    if (preg_match('/^[a-z]+$/i', $tmpArr[0]) == 1) {
        if ($tmpArr[0] == 'i') {
            $error = 1;
            echo "Vous ne pouvez pas nommer une variable i (utilisé pour les complexes).\n";
        }
        assignVar($tmpArr);
    } else if (preg_match('/^[a-z]+\([a-z]+\)$/i', $tmpArr[0]) == 1) {
        assignFct($tmpArr);
    } else {
        $error = 1;
    }
    return $error;
}

function checkVar($str) {
    preg_match_all('#[a-z]+#i', $str, $matches, PREG_OFFSET_CAPTURE);
    // print_r($matches);
    $i = 0;
    $error = "none";
    while ($i < count($matches[0])) {
        if ($matches[0][$i][0] != 'i'){
            if (array_key_exists($matches[0][$i][0], $GLOBALS["arrVar"])) {
                $tmpVal = $GLOBALS["arrVar"][$matches[0][$i][0]];
                $str = str_replace($matches[0][$i][0], $tmpVal, $str);
            } else {
                // retour error
                $error = "yes";
                $GLOBALS["error"] = "La variable " . $matches[0][$i][0] . " est inconnue.\n";
                break;
            }
        }
        $i++;
    }
    if ($error == "none") {
        return $str;
    } else {
        return $error;
    }
}

function parseCalc($str) {
    $numbers = array();
    $operators = array();
    if ($str[0] == '-' || $str[0] == '+') {
        $str = "0" . $str;
    }
    $i = 0;
    // $tmp = preg_split("/[\+\-\*\%]/", $argv[1]);
    while($i < strlen($str)) {
        if (strpos("-+*/%^", $str[$i]) !== false) {
           array_push($operators, $str[$i]);
        } else {
            $j = $i;
            while ($j < strlen($str)) {
                if(strpos("-+*/%^", $str[$j]) === false) {
                    $j++;
                } else {
                    break;
                }
            }
            $length = $j - $i;
            array_push($numbers, floatval(substr($str, $i, $length)));
            $i += $length - 1;
        }
        $i++;
    }
    // print_r($operators);
    // print_r($numbers);
    calcPrio1($operators, $numbers);
}

function parseBrakets($str) {
    $i = 0;
    $tmpi = 0;
    $nbBrakets = 0;
    $newStr = "";
    while($i < strlen($str)) {
        if($str[$i] == '(' ) {
            $tmpi = $i;
        }
        if ($str[$i] == ')' && $nbBrakets == 0) {
            $nbBrakets++;
            // echo substr($str, $tmpi + 1, $i - $tmpi - 1) . "\n";
            parseCalc(substr($str, $tmpi + 1, $i - $tmpi - 1) . "\n");
            if ($GLOBALS["tmpCalc"] != "error") {
                $newStr = substr($str, 0, $tmpi) . $GLOBALS["tmpCalc"] . substr($str, $i + 1);
            } else {
                return;
            }
            // $newStr = str_replace($str, "", substr($str, $tmpi, $i - $tmpi + 1));
            // echo $newStr . "\n";
        }
        $i++;
    }
    if ($nbBrakets != 0) {
        parseBrakets($newStr);
    } else {
        parseCalc($str);
    }
}

?>