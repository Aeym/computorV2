<?php 

$numbers = array();
$operators = array();
calc(preg_replace("/\s+/", '', $argv[1]), $numbers, $operators);

function calc($str, & $numbers, & $operators) {
    
    if ($str[0] == '-') {
        $str = "0" . $str;
    }
    $i = 0;
    // $tmp = preg_split("/[\+\-\*\%]/", $argv[1]);
    while($i < strlen($str)) {
        if (strpos("-+*/%", $str[$i]) !== false) {
           array_push($operators, $str[$i]);
        } else {
            $j = $i;
            while ($j < strlen($str)) {
                if(strpos("-+*/%", $str[$j]) === false) {
                    $j++;
                } else {
                    break;
                }
            }
            $length = $j - $i;
            array_push($numbers, floatval(substr($str, $i, $length)));
            $i += $length - 1;
        }
        $i++;
    }
    print_r($operators);
    print_r($numbers);
    calc2($operators, $numbers);
}

function calc2(& $operators, & $numbers) {
    $nbOpBefore = count($operators);
    $i = 1;
    $error = 0;
    while ($i < count($operators)) {
        if (strpos("-+", $operators[$i]) !== false && strpos("*/%", $operators[$i - 1]) !== false) {
            if ($operators[$i - 1] == '*') {
                $tmp = $numbers[$i - 1] * $numbers[$i];
            } else if ($operators[$i - 1] == '/') {
                if ($numbers[$i + 1] != 0) {
                    $tmp = $numbers[$i] / $numbers[$i + 1];
                } else {
                    $error = 1;
                }
            } else if ($operators[$i - 1] == '%') {
                if ($numbers[$i + 1] != 0) {
                    $tmp = $numbers[$i] % $numbers[$i + 1];
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
        echo "Erreur division par 0";
    } else {
        print_r($operators);
        print_r($numbers);
        $nbOpAfter = count($operators);
        if ($nbOpBefore != $nbOpAfter) {
            echo "again!\n";
            calc2($operators, $numbers);
        } else {
            if ($operators[count($operators) - 1] != '-' && $operators[count($operators) - 1] != '+') {
                echo "last operators check\n";
                array_push($operators, '+');
                array_push($numbers, 0);
                calc2($operators, $numbers);
            } else {
                echo "finito!\n";
                calc3($operators, $numbers);
            }
        }
    }
}

function calc3($operators, $numbers) {
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
    echo $result ;
    $result;
}

?>