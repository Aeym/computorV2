<?php

function assignVar($arr) {
    $tmpStr = checkVar($arr[1], '');
    // echo "remplacement des variables : " . $tmpStr . "\n\n";
    if ($tmpStr == "yes") {
        echo $GLOBALS["error"];
        $GLOBALS["error"] = "";
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
    // echo "Tableau des rationnels : \n";
    // print_r($GLOBALS["arrVar"]);
    // print_r($GLOBALS["lines"]);
}

// function assignImg () {
//     echo "img\n";

// }

// function assignMat() {
//     echo "mat\n";

// }


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
        echo "assign fct\n";
        if (strpos($arr[1], $var) !== FALSE) {
            $GLOBALS["arrVar"][$arr[0]] = $tmp;
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