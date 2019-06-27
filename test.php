<?php

reduceFct($argv[1]);

function reduceFct($str) {
    $tmpArr = array();
    preg_match_all("#\^#", $str, $matches, PREG_OFFSET_CAPTURE);
    $i = 0;
    $j = 0;
    while ($i < count($matches[0])) {
        $j = $matches[0][$i][1];
        while ($j >= 0 && strpos("*+-%/", $str[$j]) === false) {
            $j--;
        }
        $tmp1 = $j + 1;
        $j = $matches[0][$i][1];
        while ($j < strlen($str) && strpos("*+-%/", $str[$j]) === false) {
            $j++;
        }
        $tmp2 = $j - 1;
        $j = $matches[0][$i][1];
        array_push($tmpArr, array("coeff" => substr($str, $tmp1, $j - $tmp1), "puissance" => substr($str, $j + 1, $tmp2 - $j), "sign" => ($tmp1 == 0) ? '+' : $str[$tmp1 - 1]));
        $i++;
    }
    print_r($tmpArr);
}
// PENSER AUX PARENTHESES : Ignorer les signes +-*/ etc quand on est dans la parenthese :)
?>