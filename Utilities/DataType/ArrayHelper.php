<?php

namespace Sle\Utilities\DataType;

/**
 * ArrayHelper
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class ArrayHelper
{

    /**
     * Merges two arrays by pattern ABA or BAB
     *
     * @param $pattern Options: ABA|BAB
     * @param array $arrayA Values for A
     * @param array $arrayB Values for B
     * @return array
     */
    public static function mergeTwoArraysByPattern($pattern, array $arrayA, array $arrayB)
    {
        $result = [];

        if ('BAB' == $pattern) {
            // swap
            $tmp = $arrayA;
            $arrayA = $arrayB;
            $arrayB = $tmp;
        }

        $countA = count($arrayA);
        $countB = count($arrayB);
        $count = ($countA >= $countB) ? $countA : $countB;

        $i = 0;

        while ($i < $count) {
            if (array_key_exists($i, $arrayA)) {
                $result[] = $arrayA[$i];
            }
            if (array_key_exists($i, $arrayB)) {
                $result[] = $arrayB[$i];
            }
            $i++;
        }

        return $result;
    }

}
