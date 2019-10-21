<?php

function assignVar($arr) {
    $tmpStr = checkVar($arr[1], '');
    // echo "remplacement des variables : " . $tmpStr . "\n\n";
    if ($tmpStr == "yes") {
        // echo $GLOBALS["error"];
        // $GLOBALS["error"] = "";
        return;
    } else {
        parseBrakets($tmpStr);
    }
    // echo "on test la valeur de tmpCalc : " . $GLOBALS["tmpCalc"] . "\n";
    if ($GLOBALS["tmpCalc"] !== "error") {
        // echo "on est dans l'assignation de la variable\n";
        $GLOBALS["arrVar"][$arr[0]] = $GLOBALS["tmpCalc"];
        echo $GLOBALS["arrVar"][$arr[0]] . "\n";
    } else {
        // echo "on est dans la gestion d'erreur\n";
        echo $GLOBALS["tmpCalc"] . "\n";
    }

}



function assignMat($arr) {
    // echo "mat\n";
    if (preg_match('/^[a-z]+\([a-z]+\)$/i', $arr[0]) == 1) {
        // function de matrices
    }
    if (preg_match("/\*+/i", $arr[1])) {
        // echo "assign1\n";
        $arr[1] = calcMat($arr[1]);
    }
    if (($arrMat = check_and_parse_mat($arr[1])) != 1) {
        if(preg_match("/^[a-z]+$/i", $arr[0]) == 0) {
            echo "Erreur de nom de matrice.\n";
            return 1;
        } else {
            $GLOBALS["arrVar"][$arr[0]] = $arrMat;
            echo implodeArrMat($arrMat). "\n";
            return 0;
        }

    }
    return 1;
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
    // echo "substr = " . $mat . "\n";

    $iopen = 0;
    $iclose = 0;
    for($i = 0; $i < strlen($mat); $i++) {
        if ($mat[$i] == '[') {
            $iopen++;
        } else if ($mat[$i] == ']') {
            $iclose++;
        }
        if ($iclose > $iopen) {
            // echo "ici\n";
            return 1;
        }
    }
    if ($iclose != $iopen) {
        // echo "la\n";
        return 1;
    }

    $matRows = explode(';', $mat);
    // print_r($matRows);
    foreach ($matRows as $row) {
        // echo $row . "\n";
        if ($row[0] != '[' || $row[strlen($row) - 1] != ']') {
            // echo "nmerde\n";
            return 1;
        }
        $tmp = substr($row, 1, strlen($row) - 2);
        $arr[] = explode(',', $tmp);
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

function assignFct($arr) {
    $var = substr($arr[0], strpos($arr[0], '(') + 1, strpos($arr[0], ')') - strpos($arr[0], '(') - 1);
    // echo $var . "\n";
    $tmp = checkVar($arr[1], $var);
    if ($tmp == "yes") {
        // echo "error\n";
        echo $GLOBALS["error"];
        $GLOBALS["error"] = "";
        return;
    } else {
        // echo "assign fct\n";
        if (strpos($arr[1], $var) !== FALSE) {
            $GLOBALS["arrVar"][$arr[0]] = $tmp;
            echo $GLOBALS["arrVar"][$arr[0]] . "\n";
        } else {
            parseBrakets($tmp);
            if ($GLOBALS["tmpCalc"] !== "error") {
                $GLOBALS["arrVar"][$arr[0]] = $GLOBALS["tmpCalc"];
                echo $GLOBALS["arrVar"][$arr[0]] . "\n";
            }
        }
    }
    
    
}

// function reduceFct($str) {
//     $tmpArr = array();
//     preg_match_all("#\^#", $str, $matches, PREG_OFFSET_CAPTURE);
//     // print_r($matches);
//     // $i = 0;
//     // $j = 0;
//     // while ($i < count($matches[0])) {
//     //     $j = $matches[0][$i][1];
//     //     while ($j >= 0 && strpos("*+-%/", $str[$j]) === false) {
//     //         $j--;
//     //     }
//     //     $tmp1 = $j + 1;
//     //     $j = $matches[0][$i][1];
//     //     while ($j < strlen($str) && strpos("*+-%/", $str[$j]) === false) {
//     //         $j++;
//     //     }
//     //     $tmp2 = $j - 1;
//     //     $j = $matches[0][$i][1];
//     //     array_push($tmpArr, array("puissance" => substr($str, $tmp1, $j - $tmp1), "coeff" => substr($str, $j + 1, $tmp2 - $j), "sign" => ($tmp1 == 0) ? '+' : $str($tmp1 - 1)));
//     //     $i++;
//     // }
//     // print_r($tmpArr);
// }

?>