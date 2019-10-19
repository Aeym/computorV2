<?php

function parseArg($str, $var) {
    $tmpStr = preg_replace("/\s+/", '', $str);
    
    
    $testArr = preg_split("/[\+\-\=\/\%]/", $tmpStr);
    print_r($testArr);
    for ($i = 0; $i < count($testArr); $i++) {
        if (strpos($testArr[$i], $var) === false) {
            // $tmpStr = str_replace($testArr[$i], $testArr[$i] . "*" . $var . "^0", $tmpStr);
            $testArr[$i] .= "*" . $var . "^0";
        } else {
            $tmp = strpos($testArr[$i], $var);
            $tmp2 = preg_split("#" . $var . "#", $testArr[$i]);
            if ($testArr[$i][$tmp + strlen($var)] != "^") {
                // $tmpStr = str_replace($testArr[$i], $tmp2[0] . $var . "^1" . $tmp2[1], $tmpStr);
                if ($testArr[$i] == $var) {
                    $testArr[$i] = "1*" . $tmp2[0] . $var . "^1" . $tmp2[1]; 
                } else {
                    $testArr[$i] = $tmp2[0] . $var . "^1" . $tmp2[1];
                }
            }
        }
    }
    // $str = implode('', $testArr);
    // echo $tmpStr ."\n";
    print_r($testArr);
    $j = 0;
    $strfinal = "";
    for ($i = 0; $i < strlen($tmpStr); $i++) {
        if (strpos("+-=", $tmpStr[$i]) !== false) {
            $strfinal .= $testArr[$j] . $tmpStr[$i];
            $j++;
        }
    }
    $strfinal .= $testArr[$j] . $tmpStr[$i];
    echo "final str = " . $strfinal ."\n";
    $tmpArr = explode('=', $strfinal);

    $left = preg_split("#" . $var . "\^#", $tmpArr[0], 0, PREG_SPLIT_NO_EMPTY);
    $right = preg_split("#" . $var . "\^#", $tmpArr[1], 0, PREG_SPLIT_NO_EMPTY);


    print_r($left);
    print_r($right);
    $leftCoeff = array();
    $rightCoeff = array();

    $i = 0;
    $pow = 'pow';
    while ($i < (count($left) - 1)) {
        $pow .= $left[$i + 1][0];
        if ($i == 0) {
        $leftCoeff[$pow] = substr($left[$i], 0, -1);
        } else {
            $leftCoeff[$pow] = substr($left[$i], 1, -1);
        }
        $pow = substr($pow, 0, -1);
        $i++;
    }

    $i = 0;
    $pow = 'pow';
    while ($i < (count($right) - 1)) {
        $pow .= $right[$i + 1][0];
        if ($i == 0) {
            $rightCoeff[$pow] = substr($right[$i], 0, -1);
        } else {
            $rightCoeff[$pow] = substr($right[$i], 1, -1);
        }
        $pow = substr($pow, 0, -1);
        $i++;
    }
    reduceEqu($leftCoeff, $rightCoeff, $var);
}

function reduceEqu($leftCoeff, $rightCoeff, $var) {
    print_r($leftCoeff);
    print_r($rightCoeff);
    $coeffs = array();
    $c = (count($leftCoeff) > count($rightCoeff)) ? count($leftCoeff) : count($rightCoeff);
    $i = 0;
    while ($i < $c) {
        $l = 0;
        $r = 0;
        if (array_key_exists('pow' . $i, $leftCoeff)) {
            $l = floatval($leftCoeff['pow' . $i]);
        }
        if (array_key_exists('pow' . $i, $rightCoeff)) {
            $r = floatval($rightCoeff['pow' . $i]);
        }
        $coeffs[$i] = $l - $r;
        $i++;
    }
    $reduceStr = "Reduced form : ";
    $polyDegree = 0;
    $p = 0;
    $i = 0;
    while ($i < count($coeffs)) {
        if ($coeffs[$i] != 0) {
            $polyDegree = $i;
            if ($coeffs[$i] < 0) {
                if ($reduceStr != "Reduced form : ") {
                    $reduceStr .= " - ";
                } else {
                    $reduceStr .= "-";
                }
                $coeffs[$i] *= -1;
                $p = 1;
            } else if ($reduceStr != "Reduced form : ") {
                $reduceStr .= " + ";
            }
            $reduceStr .= $coeffs[$i] . " * " . $var . "^" . $i;
            if ($p == 1) {
                $coeffs[$i] *= -1;
                $p = 0;
            }
        }
        $i++;
    }
    if ($reduceStr == "Reduced form : ") {
        $reduceStr .= '0';
    }
    $reduceStr .= " = 0\n";
    echo $reduceStr;
    echo "Polynomial degree: " . $polyDegree . "\n";
    solution($coeffs, $polyDegree);
}

function solution($coeffs, $polyDegree) {
    $result = '';
    if ($polyDegree == 0) {
        if ($coeffs[0] != 0) {
            $result .= "There is no solution.\n";
        } else {
            $result .= "All real numbers are solution.\n";
        }
    } else if ($polyDegree == 1) {
        $result .= "The solution is :\n";
        if ($coeffs[0] == 0) {
            $result .= "0\n";
        } else {
            $result .= ($coeffs[0] / $coeffs[1]) * (-1) . "\n";
        }
    } else if ($polyDegree == 2) {
        $disc = $coeffs[1] * $coeffs[1] - 4.0 * $coeffs[2] * $coeffs[0];
        if ($disc < 0.0) {
            $result .= "Discriminant is strictly negative, the two complexes solutions are:\n";
            if ($coeffs[1] != 0.0) {
                $result .= '(-' .  $coeffs[1] . ' - i√(' . (-1) * $disc . ')) / ' . 2 * $coeffs[2] . "\n"; 
                $result .= '(-' .  $coeffs[1] . ' + i√(' . (-1) * $disc . ')) / ' . 2 * $coeffs[2] . "\n"; 
            } else {
                $result .= '(-i√(' . (-1) * $disc . ')) / (' . 2 * $coeffs[2] . ")\n"; 
                $result .= '(i√(' . (-1) * $disc . ')) / (' . 2 * $coeffs[2] .")\n"; 
            }
        } else if ($disc == 0.0) {
            $result .= "Discriminant is 0, the unique solution is:\n";
            $result .= (-1) * $coeffs[1] / (2 * $coeffs[0]) . "\n";
        } else if ($disc > 0.0) {
            $result .= "Discriminant is strictly positive, the two solutions are:\n";
            $result .= ((-1) * $coeffs[1] - sqrtMy($disc)) / (2 * $coeffs[2]) . "\n";
            $result .= ((-1) * $coeffs[1] + sqrtMy($disc)) / (2 * $coeffs[2]) . "\n";            
        }
    } else {
        $result .= "The polynomial degree is stricly greater than 2, I can't solve.\n";
    }
    echo $result;


}


function sqrtMy($f)
{
   $i = 0; 
   while( ($i * $i) <= $f)
          $i++;
    $i--; 
    $d = $f - $i * $i; 
    $p = $d/(2*$i); 
    $a = $i + $p; 
    return $a-($p*$p)/(2*$a);
}   

?>