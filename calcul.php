<?php 
$tmpCalc = 0;
// parseBrakets(preg_replace("/\s+/", '', $argv[1]));
// parseCalc(preg_replace("/\s+/", '', $argv[1]), $numbers, $operators);


function myPow($x, $pow) {
    // echo "POWWWW : " . $pow . "\n";
    if($pow == 0){
        return 1;
    } else {
        $i = 1;
        $tmpx = $x;
        while ($i < $pow) {
            $x *= $tmpx;
            $i++;
        }
        return $x;
    }
}

function calcPrio1(& $operators, & $numbers) {
    $nbOpBefore = count($operators);
    $i = 1;
    $error = 0;
    while ($i < count($operators)) {
        if($operators[$i] == '^') {
            $tmp = myPow($numbers[$i], $numbers[$i + 1]);
            $numbers[$i] = $tmp;
            unset($operators[$i], $numbers[$i + 1]);
            $tmp2 = array_values($numbers);
            $numbers = array();
            $numbers = $tmp2;
            $tmp3 = array_values($operators);
            $operators = array();
            $operators = $tmp3;
        }
        if (strpos("-+", $operators[$i]) !== false && strpos("*/%", $operators[$i - 1]) !== false) {
            if ($operators[$i - 1] == '*') {
                $tmp = $numbers[$i - 1] * $numbers[$i];
            } else if ($operators[$i - 1] == '/') {
                if ($numbers[$i] != 0) {
                    $tmp = $numbers[$i - 1] / $numbers[$i];
                } else {
                    $error = 1;
                }
            } else if ($operators[$i - 1] == '%') {
                if ($numbers[$i] != 0) {
                    $tmp = $numbers[$i - 1] % $numbers[$i];
                } else {
                    $error = 1;
                }
            }
            if ($error == 0) {
                $numbers[$i - 1] = $tmp;
                unset($operators[$i - 1], $numbers[$i]);
                $tmp2 = array_values($numbers);
                $numbers = array();
                $numbers = $tmp2;
                $tmp3 = array_values($operators);
                $operators = array();
                $operators = $tmp3;
            } else {
                break;
            }
        }
        $i++;
    }
    if ($error == 1) {
        echo "Erreur division par 0\n";
        $GLOBALS["tmpCalc"] = "error";
    } else {
        // print_r($operators);
        // print_r($numbers);
        $nbOpAfter = count($operators);
        if ($nbOpBefore != $nbOpAfter) {
            // echo "again!\n";
            calcPrio1($operators, $numbers);
        } else {
            if ($operators[count($operators) - 1] != '-' && $operators[count($operators) - 1] != '+') {
                // echo "last operators check\n";
                array_push($operators, '+');
                array_push($numbers, 0);
                calcPrio1($operators, $numbers);
            } else {
                // echo "finito!\n";
                calcPrio2($operators, $numbers);
            }
        }
    }
}

function calcPrio2($operators, $numbers) {
    $i = 0;
    $result = $numbers[0];
    while ($i < count($operators)) {
        if ($operators[$i] == '-'){
            $result -= $numbers[$i + 1];
        } else {
            $result += $numbers[$i + 1];
        }
        $i++;
    }
    // echo $result . "\n" ;
    $GLOBALS["tmpCalc"] = $result;
}

?>