<?php

function assignVar($arr) {
    $tmpStr = checkVar($arr[1]);
    // echo "remplacement des variables : " . $tmpStr . "\n\n";
    if ($tmpStr == "yes") {
        echo $GLOBALS["error"];
        return;
    } else {
        parseBrakets($tmpStr);
    }
    if ($GLOBALS["tmpCalc"] != "error") {
        $GLOBALS["arrVar"][$arr[0]] = $GLOBALS["tmpCalc"];
        echo $GLOBALS["arrVar"][$arr[0]] . "\n";
    }
    // echo "Tableau des rationnels : \n";
    // print_r($GLOBALS["arrVar"]);
    // print_r($GLOBALS["lines"]);
}

function assignImg () {
    echo "img\n";

}

function assignMat() {
    echo "mat\n";

}

function assignFct($arr) {
    echo "fct\n";

}

function reduceFct($str) {
    $tmpArr = array();
    preg_match_all("#\^#", $str, $matches, PREG_OFFSET_CAPTURE);
    print_r($matches);
    // $i = 0;
    // $j = 0;
    // while ($i < count($matches[0])) {
    //     $j = $matches[0][$i][1];
    //     while ($j >= 0 && strpos("*+-%/", $str[$j]) === false) {
    //         $j--;
    //     }
    //     $tmp1 = $j + 1;
    //     $j = $matches[0][$i][1];
    //     while ($j < strlen($str) && strpos("*+-%/", $str[$j]) === false) {
    //         $j++;
    //     }
    //     $tmp2 = $j - 1;
    //     $j = $matches[0][$i][1];
    //     array_push($tmpArr, array("puissance" => substr($str, $tmp1, $j - $tmp1), "coeff" => substr($str, $j + 1, $tmp2 - $j), "sign" => ($tmp1 == 0) ? '+' : $str($tmp1 - 1)));
    //     $i++;
    // }
    // print_r($tmpArr);
}
?>

3^2-(3+x)^2+3+3x^0-x^0+3x
puissance   coeffs                      sign

2           3    -> 3^2  = 9            +
2           (3+  x) -> (3 + x)^2        -
none        3 -> 3                      +
0           3x -> 3 * 1 = 3             +
0           x -> 1                      -
none        3x                          +


2           (3 + x) -> (3 + x)^2
none        3x 
none        16