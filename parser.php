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
    if (!preg_match("#^[a-zA-Z0-9\[\]\(\)\+\-\;\,\/\%\*\^\?\=\. ]+$#", $line)) {
        echo "Les noms de variables/fonctions doivent uniquement comporter des lettres\n";
        echo "Les opérateurs gérés sont : +, -, *, /, %, ^, ainsi que ? pour connaitre le résultat d'un calcul.\n";
        $error = 1;
     }
     if (preg_match("#[a-z]+ +[a-z]+#i", $line)) {
         echo "Erreur de syntaxe2\n";
         $error = 1;
     }
     if (preg_match("#[a-z][0-9]#i", $line)) {
        echo "Erreur de syntaxe3\n";
        $error = 1;
    }
     if (preg_match("#[\+\*\-\/\%\.][\+\/\%\.]+#", $line)) {
        echo "Erreur de syntaxe1\n";
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
    $tmpStr = preg_replace("/\s+/", '', $line);
    $tmpArr = explode('=', $tmpStr);
    if ($tmpArr[0] == '' || $tmpArr[1] == '') {
        echo "Erreur de syntaxe\n";
        return 1;
    }
    // on check si on a un polynome de degre <= 2
    if (preg_match("/[0-9\+\-\^\*\/\%]/", $tmpArr[0]) == 1 && preg_match("/[0-9\+\-\*\^\/\%]/", $tmpArr[1]) == 1) {
        preg_match_all("/[a-z]+/", $tmpStr, $matches, PREG_OFFSET_CAPTURE);
        // print_r($matches);
        if (count($matches[0]) == 0) {
            echo "Erreur, pas de variables.\n";
            return 1;
        }
        checkPoly($tmpArr, $matches, $tmpStr);
    }
    // on check si on demande le resultat d'un calcul :
     else if (strpos($tmpArr[1], '?') !== FALSE) {
        if (strlen($tmpArr[1]) == 1) {
            if (preg_match("/\[+/", $tmpArr[0]) != 0) {
                $ret = calcMat($tmpArr[0]);
                echo $ret . "\n";
            }
             else if(($tmp = checkVar($tmpArr[0], '')) != "yes") {
                 if ($tmp != "nope") {
                     parseBrakets($tmp);
                     echo $GLOBALS["tmpCalc"] . "\n";
                 }
            } else {
                echo "Erreur\n";
                return 1;
            }
        } else {
            echo "Erreur, pour le résultat d'un calcul utilisez \"=?\" seul.\n";
            return 1;
        }
    } else if (preg_match('/[\[\]]/i', $tmpArr[1]) == 1) {
        assignMat($tmpArr);
    } else {
        // on commence par faire les assignations de variables et de fonctions
        if (preg_match('/^[a-z]+$/i', $tmpArr[0]) == 1) {
            if ($tmpArr[0] == 'i') {
                $error = 1;
                echo "Vous ne pouvez pas nommer une variable 'i' (utilisé pour les complexes).\n";
                return 1;
            }
            assignVar($tmpArr);
        } else if (preg_match('/^[a-z]+\([a-z]+\)$/i', $tmpArr[0]) == 1) {
            assignFct($tmpArr);
        } else {
            echo "erreur de syntaxe5.\n";
            return 1;
        }
    }
    return 0;
}

function checkPoly($arr, $matches, $str) {
    $tmp = $matches[0][0][0];
    foreach ($matches[0] as $value) {
        if ($value[0] != $tmp) {
            echo "Erreur plusieurs variables\n";
            return 1;
        }
    }
    // preg_match_all('/\^[0-9]+/i', $arr[0], $matchesbis, PREG_OFFSET_CAPTURE);
    // print_r($matchesbis);
    parseArg($str, $tmp);
}

function imgFct($str) {
    $var = substr($str, strpos($str, '(') + 1, strpos($str, ')') - strpos($str, '(') - 1);
    // echo "var de fn = " . $var . "\n";
    $str = substr($str, 0, strpos($str, '('));
    // echo "str = " . $str . "\n";
    foreach($GLOBALS["arrVar"] as $key => $value) {
        if (strpos($key, '(') !== false) {
            $tmp = substr($key, 0, strpos($key, '('));
            // echo "tmp = str = " . $tmp . "\n";
            $var2 = substr($str, strpos($str, '(') + 1, strpos($str, ')') - strpos($str, '(') - 1);
            if ($str == $tmp) {
                $tmpvar = substr($key, strpos($key, '(') + 1, strpos($key, ')') - strpos($key, '(') - 1);
                $tmpcalc = $value;
                $tmpcalc = str_replace($tmpvar, $var, $tmpcalc);
                parseBrakets(checkVar($tmpcalc, ''));
                return $GLOBALS["tmpCalc"];
            }
        }
    }
    return "error";
}

function checkVar($str, $var) {
    // echo $str . "\n";
    preg_match_all('/[a-z]+\(?[0-9]*[a-z]*\.?[0-9]*\)?+/i', $str, $matches, PREG_OFFSET_CAPTURE);
    // print_r($matches);
    $i = 0;
    $error = "none";
    while ($i < count($matches[0])) {
        if ($matches[0][$i][0] != 'i' && $matches[0][$i][0] != $var){
            if (array_key_exists($matches[0][$i][0], $GLOBALS["arrVar"])) {
                if (is_array($GLOBALS["arrVar"][$matches[0][$i][0]])) {
                    $str = implodeArrMat($GLOBALS["arrVar"][$matches[0][$i][0]]);
                    $GLOBALS["tmpCalc"] =  $str . "\n";
                    break;
                }
                if (strpos($matches[0][$i][0], "(") === false) {
                    $tmpVal = $GLOBALS["arrVar"][$matches[0][$i][0]];
                    $str = str_replace($matches[0][$i][0], $tmpVal, $str);
                    // echo $str . "\n";
                } else {
                    echo "Erreur\n";
                    $error = "yes";
                }
            } else if (($tmpVal = imgFct($matches[0][$i][0])) != "error") {
                // echo "osdghldg\n";
                $str = str_replace($matches[0][$i][0], $tmpVal, $str);
            } else {
                // retour error
                $error = "yes";
                $GLOBALS["error"] .= "La variable " . $matches[0][$i][0] . " est inconnue.\n";
                echo $GLOBALS["error"];
                // return;
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
    $bool = 0;
    if ($str[0] == '-' || $str[0] == '+') {
        $str = "0" . $str;
    }
    $i = 0;
    // $tmp = preg_split("/[\+\-\*\%]/", $argv[1]);
    while($i < strlen($str)) {
        if (strpos("-+*/%^", $str[$i]) !== false) {
            if (strpos("-+*/%^", $str[$i - 1]) === false) {
                array_push($operators, $str[$i]);
            } else {
                $bool = 1;
            }
        } else {
            if ($bool == 0) {
                $j = $i;
            } else {
                $j = $i + 1;
            }
            while ($j < strlen($str)) {
                if(strpos("-+*/%^", $str[$j]) === false) {
                    $j++;
                } else {
                    break;
                }
            }
            $length = $j - $i;
            $tmp = substr($str, $i, $length);
            // echo "tmp = " .$tmp . "\n";
            if($tmp == 'i') {
                array_push($numbers, $tmp);
            } else {
                if ($bool == 1) {
                    $tmp *= -1;
                    $bool = 0;
                }
                array_push($numbers, floatval($tmp));
            }
            $i += $length - 1;
        }
        $i++;
    }
    // print_r($operators);
    // print_r($numbers);
    calcPrio1($operators, $numbers, []);
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