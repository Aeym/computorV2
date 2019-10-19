<?php 
// $tmpCalc = 0;
// parseBrakets(preg_replace("/\s+/", '', $argv[1]));
// parseCalc(preg_replace("/\s+/", '', $argv[1]), $numbers, $operators);


function myPow($x, $pow) {
    // echo "POWWWW : " . $pow . "\n";
    if ($x == 'i') {
        return -1;
    }
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

function calcPrio1(& $operators, & $numbers, $imgPart) {
    $nbOpBefore = count($operators);
    // echo "BITE\n";
    print_r($operators);
    print_r($numbers);
    $i = 1;
    $error = 0;
    while ($i < count($operators)) {
        if($operators[$i - 1] == '^') {
            $tmp = myPow($numbers[$i - 1], $numbers[$i]);
            $numbers[$i - 1] = $tmp;
            unset($operators[$i - 1], $numbers[$i]);
            $tmp2 = array_values($numbers);
            $numbers = array();
            $numbers = $tmp2;
            $tmp3 = array_values($operators);
            $operators = array();
            $operators = $tmp3;
        }
        if (strpos("-+", $operators[$i]) !== false && strpos("*/%", $operators[$i - 1]) !== false) {
            if ($numbers[$i - 1] != 'i' && $numbers[$i] != 'i'){
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
            } else { // gestion de la partie imaginaire quand on a un ou deux 'i'
                echo "teeeeeest33\n"; 
                if ($numbers[$i - 1] == 'i' && $numbers[$i] == 'i') {
                    if ($operators[$i - 1] == '*') {
                        $tmp = -1;
                    } else if ($operators[$i - 1] == '/') {
                        $tmp = 1;
                    } else if ($operators[$i - 1] == '%') {
                        $tmp = 0;
                    }
                }
                else if ($numbers[$i - 1] == 'i' || $numbers[$i] == 'i') {
                    if ($operators[$i - 1] == '*') {
                        $tmp = 0;
                        $sign = '' . ($i < 2) ? '+' : $operators[$i - 2];
                        $imgPart[] = $sign . $numbers[$i - 1] . '*' . $numbers[$i];
                        // print_r($imgPart);
                    } else if ($operators[$i - 1] == '/') {
                        $tmp = 0;
                        $imgPart[] = $numbers[$i - 1] . '/' . $numbers[$i];
                    } else if ($operators[$i - 1] == '%') {
                        if($numbers[$i - 1] == 'i') {
                            $tmp = 'i';
                        } else {
                            $tmp = 0;
                        }
                    }
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
            calcPrio1($operators, $numbers, $imgPart);
        } else {
            if ($operators[count($operators) - 1] != '-' && $operators[count($operators) - 1] != '+') {
                // echo "last operators check\n";
                array_push($operators, '+');
                array_push($numbers, 0);
                calcPrio1($operators, $numbers, $imgPart);
            } else {
                // echo "finito!\n";
                calcPrio2($operators, $numbers, $imgPart);
            }
        }
    }
}

function calcPrio2($operators, $numbers, $imgPart) {
    print_r($imgPart);
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
    // echo $result . " reslult\n" ;
    if (count($imgPart) == 0) {
        $GLOBALS["tmpCalc"] = $result;
    } else {
        // $i = 0;
        // $tmp = '';
        // while ($i < count($imgPart)) {
        //     $tmp .= $imgPart[$i];
        //     $i++;
        // }
        $tmp = reduceImgPart($imgPart);
        $tmp .= "*i";
        if ($result > 0) {
            $GLOBALS["tmpCalc"] = $tmp . '+' . $result;
        } else if ($result == 0) {
            $GLOBALS["tmpCalc"] = $tmp;
        } else if ($result < 0) {
            $GLOBALS["tmpCalc"] = $tmp . $result;
        }
    }
}

function reduceImgPart($imgPart) {
    $i = 0;
    $tmp = 0;
    while ($i < count($imgPart)) {
        $tmp += $imgPart[$i];
        $i++;
    }
    return $tmp;
}


function calcMat($str) {
    $arr = preg_split("/\*+/", $str);
    $j = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        if ($str[$i] == "*") {
            if($str[$i + 1] == "*") {
                //multi matricielle
                if (($tmp1 = check_and_parse_mat($arr[$j])) != 1 && ($tmp2= check_and_parse_mat($arr[$j + 1])) != 1) {
                    if (count($tmp1) != count($tmp2[0]) || count($tmp1[0]) != count($tmp2)) {
                        echo "error taille matrice. \n";
                        return 1;
                    } else {
                        $ret = multiMat($tmp1, $tmp2);
                        // echo "laa\n";
                        // print_r($ret);
                        // $str = str_replace($arr[$j] . "**" . $arr[$j * 1], implodeArrMat($ret), $str);
                        $j += 2;
                    }
                }
            } else {
                $tmp1 = check_and_parse_mat($arr[$j]);
                $tmp2 = check_and_parse_mat($arr[$j + 1]);
                // print_r($tmp1);
                // print_r($tmp2);
                // return;
                if (strpos($arr[$j], "[") === false || strpos($arr[$j + 1], '[') === false) {
                    // scalaire
                    // echo "1\n";
                    // print_r($arr[$j]);
                    // echo "2\n";
                    // print_r($arr[$j + 1]);
                    if (strpos($arr[$j], "[") !== false) {
                        $ret = scalMat($tmp1, $arr[$j + 1]);

                    }
                    if (strpos($arr[$j + 1], "[") !== false) {
                        $ret = scalMat($tmp2, $arr[$j]);                        
                    }
                    if ($ret == 1) {
                        echo "Erreur scalaire inconnu.\n";
                        return 1;
                    }
                    // echo "3\n";
                    // print_r($ret);
                    $str = str_replace($arr[$j] . "*" . $arr[$j + 1], implodeArrMat($ret), $str);
                    $j += 2;
                } else {
                    //terme a terme
                    // echo "ouret\n";
                    if (count($tmp1) == count($tmp2) && count($tmp1[0]) == count($tmp2[0])) {
                        echo "ouret2\n";
                        $ret = tatMat($tmp1, $tmp2);
                        print_r($ret);
                        $str = str_replace($arr[$j] . "*" . $arr[$j + 1], implodeArrMat($ret), $str);
                        $j += 2;
                    } else {
                        echo "erreur taille matrice2.\n";
                    }
            
                }
            }
        }
    }
    if (strpos($str, '*') != false) {
        return calcMat($str);
    } else {
        return $str;
    }
}

function tatMat($mat1, $mat2) {
    // echo "la\n";
    // print_r($mat1);
    $x = 0;
    $newmat = array();
    while ($x < count($mat1)) {
        $y = 0;
        while ($y < count($mat1[0])) {
            $newmat[$x][$y] = $mat1[$x][$y] * $mat2[$x][$y];
            $y++;
        }
        $x++;
    }
    return $newmat;
}

function scalMat($mat, $scl) {
    // echo "scl =  " . $scl . "\n";
   if (preg_match("/[a-z]+/i", $scl) != 0) {
        $ret = checkVar($scl, "");
        // echo $ret . "\n";
        if ($ret == "yes") {
            // echo "slutuu\n";
            return 1;
       } else {
           $scl = $ret;
       }
   }
    $x = 0;
    $newmat = array();
    while ($x < count($mat)) {
        $y = 0;
        while ($y < count($mat[0])) {
            $newmat[$x][$y] = $scl * $mat[$x][$y];
            $y++;
        }
        $x++;
    }
    return $newmat;
}

function multiMat($mat1, $mat2) {
    $x = 0;
    $y = 0;
    $newmat = array();
    while ($x < count($mat1)) {
        $cont = count($mat2[0]);
        $i = 0;
        while ($cont != 0) {
            $tmp = 0;
            $y = 0;
            while ($y < count($mat1[$x])) {
                $tmp += $mat1[$x][$y] * $mat2[$y][$i];
                $y++;
            }
            $newmat[$x][$i] = $tmp;
            $i++;
            $cont--;
        }
        $x++;
        print_r($newmat);
    }
    return $newmat;
}

?>