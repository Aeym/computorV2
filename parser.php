<?php 

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
         echo "Erreur de syntaxe\n";
         $error = 1;
     }
     if (preg_match("#[a-z]+[0-9]+#i", $line)) {
        echo "Erreur char et chiffre collés\n";
        $error = 1;
    }
    if (preg_match("#[0-9]+[a-z]+#i", $line)) {
        echo "Erreur chiffre et char collés\n";
        $error = 1;
    }
     if (preg_match("#[\+\*\-\/\%\.][\+\/\%\.]+#", $line)) {
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
    $tmpStr = preg_replace("/\s+/", '', $line);
    $tmpArr = explode('=', $tmpStr);
    if ($tmpArr[0] == '' || $tmpArr[1] == '') {
        echo "Erreur de syntaxe\n";
        return 1;
    }
    // on check si on a un polynome de degre <= 2
    if (preg_match("/[0-9\+\-\^\*\/\%]/", $tmpArr[0]) == 1 && preg_match("/[0-9\+\-\*\^\/\%]/", $tmpArr[1]) == 1) {
        preg_match_all("/[a-z]+/", $tmpStr, $matches, PREG_OFFSET_CAPTURE);
        if (count($matches[0]) == 0) {
            echo "Erreur, pas de variables.\n";
            return 1;
        }
        checkPoly($tmpArr, $matches, $tmpStr);
    }
    // on check si on demande le resultat d'un calcul :
     else if (strpos($tmpArr[1], '?') !== FALSE) {
        if (strlen($tmpArr[1]) == 1) {
            $GLOBALS["info"] = 0;
            $tmp = checkVar($tmpArr[0], '');
            if (preg_match("/\[+/", $tmpArr[0]) != 0) {
                $ret = calcMat($tmpArr[0]);
                echo $ret . "\n";
                return 0;
            } else if($tmp != "ok" && $tmp != "yes") {
                 if ($tmp != "nope") {
                     if (preg_match("/\[/", $tmp)) {
                         $ret = calcMat($tmp);
                         echo $ret . "\n";
                        } else {
                            parseBrakets($tmp);
                            echo $GLOBALS["tmpCalc"] . "\n";
                            return 0;
                        }
                    }
            } else if ($tmp == "ok") {
                return 0;
            } else  {
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
        // assignations de variables et de fonctions
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
            echo "erreur de syntaxe.\n";
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
    parseArg($str, $tmp);
}

function imgFct($str) {
    $var = substr($str, strpos($str, '(') + 1, strpos($str, ')') - strpos($str, '(') - 1);
    $str = substr($str, 0, strpos($str, '('));
    foreach($GLOBALS["arrVar"] as $key => $value) {
        if (strpos($key, '(') !== false) {
            $tmp = substr($key, 0, strpos($key, '('));
            $var2 = substr($str, strpos($str, '(') + 1, strpos($str, ')') - strpos($str, '(') - 1);
            if ($str == $tmp) {
                $tmpvar = substr($key, strpos($key, '(') + 1, strpos($key, ')') - strpos($key, '(') - 1);
                $tmpcalc = $value;
                $tmpcalc = str_replace($tmpvar, $var, $tmpcalc);
                return checkVar($tmpcalc, '');
            }
        }
    }
    return "error";
}

function checkVar($str, $var) {
    preg_match_all('/[a-z]+\(?[0-9]*[a-z]*\.?[0-9]*\)?+/i', $str, $matches, PREG_OFFSET_CAPTURE);
    $i = 0;
    $error = "none";
    while ($i < count($matches[0])) {
        if ($matches[0][$i][0] != 'i' && $matches[0][$i][0] != $var){
            if (array_key_exists($matches[0][$i][0], $GLOBALS["arrVar"])) {
                if (is_array($GLOBALS["arrVar"][$matches[0][$i][0]])) {
                    $tmpVal = implodeArrMat($GLOBALS["arrVar"][$matches[0][$i][0]]);
                    $str = str_replace($matches[0][$i][0], $tmpVal, $str);
                }
                if (strpos($matches[0][$i][0], "(") === false) {
                    $tmpVal = $GLOBALS["arrVar"][$matches[0][$i][0]];
                    $str = str_replace($matches[0][$i][0], $tmpVal, $str);
                } else {
                    $var = substr($matches[0][$i][0], strpos($matches[0][$i][0], '(') + 1, strpos($matches[0][$i][0], ')') - strpos($matches[0][$i][0], '(') - 1);
                    if (array_key_exists($var, $GLOBALS["arrVar"])) {
                        $tmpVal = $GLOBALS["arrVar"][$var];
                        $str = str_replace($var, $tmpVal, $str);
                        return checkVar($str, '');
                    }
                    if (count($matches[0]) == 1 && $GLOBALS["info"] === 0) {
                        echo  $GLOBALS["arrVar"][$matches[0][$i][0]] . "\n";
                        unset($GLOBALS["info"]);
                        return "ok";
                    } else {
                        $tmpVal = $GLOBALS["arrVar"][$matches[0][$i][0]];
                        $str = str_replace($matches[0][$i][0], $tmpVal, $str);
                        return checkVar($str, '');
                    }
                }
            } else if (($tmpVal = imgFct($matches[0][$i][0])) != "error") {
                $str = str_replace($matches[0][$i][0], $tmpVal, $str);
            } else {
                // retour error
                $error = "yes";
                $GLOBALS["error"] = "La variable " . $matches[0][$i][0] . " est inconnue.\n";
                echo $GLOBALS["error"];
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
            parseCalc(substr($str, $tmpi + 1, $i - $tmpi - 1) . "\n");
            if ($GLOBALS["tmpCalc"] !== "error") {
                $newStr = substr($str, 0, $tmpi) . $GLOBALS["tmpCalc"] . substr($str, $i + 1);
            } else {
                echo "erreur\n";
                return;
            }
        }
        $i++;
    }
    if ($nbBrakets != 0) {
        parseBrakets($newStr);
    } else {
        parseCalc($str);
    }
}

function implodeArrMat($arr) {
    $str = "[";
    foreach($arr as $rows) {
        $tmp = '[' . implode(",", $rows) . '];';
        $str .= $tmp;
    }
    $str = substr($str, 0, strlen($str) - 1);
    $str .= ']';
    return $str;
}


function check_and_parse_mat($str) {
    $arr = array();
    $ret = isole($str);
    if ($ret == 1) {
        return 1;
    }
    $mat = substr($str, $ret[0] + 1, $ret[1] - $ret[0] - 1);
    $iopen = 0;
    $iclose = 0;
    for($i = 0; $i < strlen($mat); $i++) {
        if ($mat[$i] == '[') {
            $iopen++;
        } else if ($mat[$i] == ']') {
            $iclose++;
        }
        if ($iclose > $iopen) {
            return 1;
        }
    }
    if ($iclose != $iopen) {
        return 1;
    }

    $matRows = explode(';', $mat);
    $c = 0;
    foreach ($matRows as $row) {
        if ($row[0] != '[' || $row[strlen($row) - 1] != ']') {
            return 1;
        }
        $tmp = explode(',', substr($row, 1, strlen($row) - 2));
        if($c == 0) {
            $c = count($tmp);
        } else {
            if ($c != count($tmp)) {
                return 1;
            }
        }
        $arr[] = $tmp;
    }
    return $arr;
}

function isole($str) {
    $ifirstopen = -1;
    $ilastclose = -1;
    for ($i = 0; $i < strlen($str); $i++) {
        if ($str[$i] == '[' && $ifirstopen == -1) {
            $ifirstopen = $i;
        } 
        if ($str[$i] == ']') {
            $ilastclose = $i;
        }
    }
    if ($ifirstopen > $ilastclose) {
        return 1;
    }
    return array($ifirstopen, $ilastclose);
}

?>