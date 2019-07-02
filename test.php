<?php

// reduceFct($argv[1]);

$tmpStr = preg_replace("/\s+/", '', $argv[1]);
$tmpArr = explode('=', $tmpStr);

// checkFct($tmpArr);
$tmp= +5 +-2;
echo $tmp;

function checkFct($arr) {
    print_r($arr);
    $var = substr($arr[0], strpos($arr[0], '(') + 1, strpos($arr[0], ')') - strpos($arr[0], '(') - 1);
    echo $var . "\n";
}

function reduceFct($str) {
    $tmpArr = array();
    $nbBracket = 0;
    preg_match_all("#\^#", $str, $matches, PREG_OFFSET_CAPTURE);
    $i = 0;
    $j = 0;
    while ($i < count($matches[0])) {
        $j = $matches[0][$i][1];
        while ($j >= 0 && strpos("*+-%/", $str[$j]) === false) {
            if ($str[$j] == ')') {
                $nbBracket += 1;
                $j--;
                while ($nbBracket > 0 && $j >= 0) {
                    if ($str[$j] == ')') {
                        $nbBracket += 1;
                    } else if ($str[$j] == '(') {
                        $nbBracket -= 1;
                    }
                    $j--;
                } 
            } else {
                $j--;
            }
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