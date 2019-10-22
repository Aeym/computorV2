<?php 

function myPow($x, $pow) {
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
            if ($numbers[$i - 1] !== 'i' && $numbers[$i] !== 'i'){
                if ($operators[$i - 1] == '*') {
                    $tmp = $numbers[$i - 1] * $numbers[$i];
                } else if ($operators[$i - 1] == '/') {
                    if ($numbers[$i] != '0') {
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
                if ($numbers[$i - 1] === 'i' && $numbers[$i] === 'i') {
                    if ($operators[$i - 1] == '*') {
                        $tmp = -1;
                    } else if ($operators[$i - 1] == '/') {
                        $tmp = 1;
                    } else if ($operators[$i - 1] == '%') {
                        $tmp = 0;
                    }
                }
                else {
                    if ($operators[$i - 1] == '*') {
                        $sign = '' . ($i < 2) ? '+' : $operators[$i - 2];
                        if ($sign === '*' || $sign === '%' || $sign === '/') {
                            $tmp = 'i';
                        } else {
                            $tmp = 0;
                        }
                        if ($numbers[$i] === 'i') {
                            $imgPart[] = $sign === '-' ? '-' : '+' . $numbers[$i - 1];
                        } else {
                            $imgPart[] = $sign === '-' ? '-' : '+' . $numbers[$i];
                        }
                    } else if ($operators[$i - 1] == '/') {
                        $tmp = 0;
                        if ($numbers[$i] !== (float)0) {
                            if ($numbers[$i] === 'i') {
                                $imgPart[] = (-1) * $numbers[$i - 1];
                            } else {
                                $imgPart[] = 1 / $numbers[$i];
                            }
                        } else {
                            $error = 1;
                        }
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
        echo "Division par 0\n";
        $GLOBALS["tmpCalc"] = "error";
    } else {
        $nbOpAfter = count($operators);
        if ($nbOpBefore != $nbOpAfter) {
            calcPrio1($operators, $numbers, $imgPart);
        } else {
            if ($operators[count($operators) - 1] != '-' && $operators[count($operators) - 1] != '+') {
                array_push($operators, '+');
                array_push($numbers, 0);
                calcPrio1($operators, $numbers, $imgPart);
            } else {
                calcPrio2($operators, $numbers, $imgPart);
            }
        }
    }
}

function calcPrio2($operators, $numbers, $imgPart) {
    $i = 0;
    if ($numbers[0] !== 'i')  {
        $result = $numbers[0];
    } else {
        $result = 0;
        $imgPart[] = "1";

    }
    while ($i < count($operators)) {
        if ($operators[$i] == '-'){
            if ($numbers[$i + 1] === 'i') {
                $imgPart[] = "-1";
            } else {
                $result -= $numbers[$i + 1];
            }
        } else {
            if ($numbers[$i + 1] === 'i') {
                $imgPart[] = "1";
            } else {
                $result += $numbers[$i + 1];
            }
        }
        $i++;
    }
    if (count($imgPart) == 0) {
        $GLOBALS["tmpCalc"] = $result;
    } else {
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
                        $str = str_replace($arr[$j] . "**" . $arr[$j * 1], implodeArrMat($ret), $str);
                        $j += 2;
                    }
                }
            } else {
                $tmp1 = check_and_parse_mat($arr[$j]);
                $tmp2 = check_and_parse_mat($arr[$j + 1]);
                if (strpos($arr[$j], "[") === false || strpos($arr[$j + 1], '[') === false) {
                    // scalaire
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
                    $str = str_replace($arr[$j] . "*" . $arr[$j + 1], implodeArrMat($ret), $str);
                    $j += 2;
                } else {
                    //terme a terme
                    if (count($tmp1) == count($tmp2) && count($tmp1[0]) == count($tmp2[0])) {
                        $ret = tatMat($tmp1, $tmp2);
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
   if (preg_match("/[a-z]+/i", $scl) != 0) {
        $ret = checkVar($scl, "");
        if ($ret == "yes") {
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
    }
    return $newmat;
}

?>