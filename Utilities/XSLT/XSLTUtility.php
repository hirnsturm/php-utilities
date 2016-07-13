<?php

namespace Sle\Utilities\XSLT;

/**
 * XSLTUtility
 *
 * This class offers methods für XSL-Transformation
 * (\XSLTProcessor::registerPHPFunctions()).
 * You can call the static methods out of an XSL file by using php:function
 * ('Sle\Utilities\XSLT\XSLTUtility::[METHOD].
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class XSLTUtility
{

    /**
     *
     * @param string $search
     * @param string $replace
     * @param string $string
     * @return string
     */
    public static function strReplace($search, $replace, $string)
    {
        return str_replace($search, $replace, $string);
    }

    /**
     *
     * @param string $pattern
     * @param string $string
     * @return string
     */
    public static function regexValidation($pattern, $string)
    {
        return preg_match($pattern, $string) ? $string : null;
    }

}
