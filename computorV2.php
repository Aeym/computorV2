<?php 

require("./calcul.php");
require("./parser.php");
$arrRat = array();
$arrImg = array();
$arrFct = array();
$arrMat = array();
$lines = array();
// fwrite(STDIN, "vara = 2");

while (1) {
    $line = strtolower(stream_get_line(STDIN, 1024, PHP_EOL));
    if ($line == "close") {
        exit;
    } else if ($line == "histo") {
        print_r($GLOBALS["lines"]);
    } else {
        if (check_entry($line) == 0) {
            array_push($lines, $line);
            parse($line);
        }
    }
}

function parse($line) {
    // on commence par faire les assignations de variables et de fonctions

    assign($line);
}



function checkVar($str) {
    // $matches = array();
    preg_match_all('#[a-z]+#i', $str, $matches, PREG_OFFSET_CAPTURE);
    print_r($matches);
    $i = 0;
    $error = "none";
    while ($i < count($matches[0])) {
        if (array_key_exists($matches[0][$i][0], $GLOBALS["arrRat"])) {
            $tmpVal = $GLOBALS["arrRat"][$matches[0][$i][0]];
            $str = str_replace($matches[0][$i][0], $tmpVal, $str);
        } else {
            // retour error
            $error = "yes";
            $GLOBALS["error"] = "La variable " . $matches[0][$i][0] . " est inconnue.\n";
            break;
        }
        $i++;
    }
    if ($error == "none") {
        return $str;
    } else {
        return $error;
    }
}

function assign($line) {
    $tmpStr = preg_replace("/\s+/", '', $line);
    $tmpArr = explode('=', $tmpStr);
    // print_r($tmpArr);
    // echo (strpos($tmpArr[1], "i"));

    // $GLOBALS["arrRat"][$tmpArr[0]] = $tmpArr[1];
    if (preg_match('/^[a-z]+$/i', $tmpArr[0]) == 1 || preg_match('/^[a-z]+\([a-z]\)$/i', $tmpArr[0])) {
        echo "ok\n";
        if (strpos($tmpArr[1], "i") !== false) {
            parseBrakets(preg_replace("/\s+/", '', $tmpArr[1]));
            $GLOBALS["arrImg"][$tmpArr[0]] = calc($tmpArr[1]);
        } else if (strpos($tmpArr[1], "[") !== false) {
            $GLOBALS["arrMar"][$tmpArr[0]] = calc($tmpArr[1]);
        } else if (strpos($tmpArr[0], "(") !== false) {
            $GLOBALS["arrFct"][$tmpArr[0]] = calc($tmpArr[1]);
        } else {
            $tmpStr = checkVar($tmpArr[1]);
            echo "remplacement des variables : " . $tmpStr . "\n\n";
            if ($tmpStr == "yes") {
                echo $GLOBALS["error"];
            } else {
                parseBrakets($tmpStr);
            }
            if ($GLOBALS["tmpCalc"] != "error") {
                $GLOBALS["arrRat"][$tmpArr[0]] = $GLOBALS["tmpCalc"];
            }
        }
        echo "Tableau des rationnels : \n";
        print_r($GLOBALS["arrRat"]);
        // print_r($GLOBALS["lines"]);
    } else {
        echo "nok\n";
    }
}

// function calc($str) {
//     $i = 0;
//     $numbers = array();
//     $operators = array();
//     // $tmp = preg_split("/[\+\-\*\%]/", $argv[1]);
//     while($i < strlen($str)) {
//         if (strpos("-+*/%", $str[$i]) !== false) {
//             array_push($operators, $str[$i]);
//         }
//         // else if ($str[$i] == '(') {

//         // }
//         else {
//             $j = $i;
//             while (strpos("-+*/%", $str[$j]) === false) {
//                 $j++;
//             }
//             $length = $j - $i;
//             array_push($numbers, substr($str, $i, $length));
//             $i += $length - 1;
//         }
//         $i++;
//     }

//     print_r($operators);
//     print_r($numbers);
// }

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