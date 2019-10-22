<?php

function assignVar($arr) {
    $tmpStr = checkVar($arr[1], '');
    if (preg_match("/\[/", $tmpStr)) {
        $arr[1] = $tmpStr;
        assignMat($arr);
        return;
    }
    if ($tmpStr == "yes") {
        return;
    } else {
        parseBrakets($tmpStr);
    }
    if ($GLOBALS["tmpCalc"] !== "error") {
        $GLOBALS["arrVar"][$arr[0]] = $GLOBALS["tmpCalc"];
    }
    echo $GLOBALS["tmpCalc"] . "\n";
}



function assignMat($arr) {
    if (preg_match('/^[a-z]+\([a-z]+\)$/i', $arr[0]) == 1) {
        // function de matrices
    }
    $tmp = checkVar($arr[1], '');
    if ($tmp != "yes") {
        $arr[1] = $tmp;
    }
    if (preg_match("/\*+/i", $arr[1])) {
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
    } else {
        echo "Matrice incorrecte.\n";
        return 1;
    }
    return 1;
}

function assignFct($arr) {
    $var = substr($arr[0], strpos($arr[0], '(') + 1, strpos($arr[0], ')') - strpos($arr[0], '(') - 1);
    $tmp = checkVar($arr[1], $var);
    if ($tmp == "yes") {
        echo $GLOBALS["error"];
        $GLOBALS["error"] = "";
        return;
    } else {
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

?>