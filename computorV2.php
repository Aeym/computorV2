<?php 
$arrRat = array();
$arrImg = array();
$arrFct = array();
$arrMat = array();
$lines = array();

while (1) {
        $line = stream_get_line(STDIN, 1024, PHP_EOL);
        array_push($lines, $line);
        if ($line == "close") {
            exit;
        }
        parse($line);
}

function parse($line) {
    // on commence par faire les assignations de variables et de fonctions

    assign($line);
}

function assign($line) {
    $tmpStr = preg_replace("/\s+/", '', $line);
    $tmpArr = explode('=', $tmpStr);
    // print_r($tmpArr);
    // echo (strpos($tmpArr[1], "i"));

    // $GLOBALS["arrRat"][$tmpArr[0]] = $tmpArr[1];
    if (strpos($tmpArr[1], "i") !== false) {
        $GLOBALS["arrImg"][$tmpArr[0]] = calc($tmpArr[1]);
    } else if (strpos($tmpArr[1], "[") !== false) {
        $GLOBALS["arrMar"][$tmpArr[0]] = calc($tmpArr[1]);
    } else if (strpos($tmpArr[0], "(") !== false) {
        $GLOBALS["arrFct"][$tmpArr[0]] = calc($tmpArr[1]);
    } else {
        $GLOBALS["arrRat"][$tmpArr[0]] = calc($tmpArr[1]);
    }
    // echo "Tableau des rationnels : \n";
    // print_r($GLOBALS["arrRat"]);
    // print_r($GLOBALS["lines"]);
}

function calc($str) {
    $i = 0;
    $numbers = array();
    $operators = array();
    // $tmp = preg_split("/[\+\-\*\%]/", $argv[1]);
    while($i < strlen($str)) {
        if (strpos("-+*/%", $str[$i]) !== false) {
            array_push($operators, $str[$i]);
        }
        // else if ($str[$i] == '(') {

        // }
        else {
            $j = $i;
            while (strpos("-+*/%", $str[$j]) === false) {
                $j++;
            }
            $length = $j - $i;
            array_push($numbers, substr($str, $i, $length));
            $i += $length - 1;
        }
        $i++;
    }

    print_r($operators);
    print_r($numbers);
}

function assignVar() {
    // echo "var\n";

}

function assignImg () {
    echo "img\n";

}

function assignMat() {
    echo "mat\n";

}

function assignFct() {
    echo "fct\n";

}
?>