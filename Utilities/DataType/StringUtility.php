<?php

namespace Sle\Utilities\DataType;

/**
 * StringUtility
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class StringUtility
{

    /**
     * Replaces placeholders in a string
     *
     * @param array $placeholder
     * @param string $string
     * @return string
     */
    public static function replacePlaceholder($placeholder, $string)
    {
        foreach ($placeholder as $key => $val) {
            $string = preg_replace('~###+('.$key.')+###~', $val, $string);
        }

        return $string;
    }

    /**
     * Truncate
     *
     * Truncates as given string and returns the result
     *
     * @param string $text
     * @param int $limit
     * @param string $suffix
     * @param string $encoding
     * @return string
     */
    public static function truncate($text, $limit = 200, $suffix = null, $encoding = 'UTF-8')
    {
        $text = strip_tags($text) . ' ';

        if (function_exists('mb_get_info')) {
            // with encoding
            if (mb_strlen($text, $encoding) > $limit) {
                $text = mb_substr($text, 0, $limit, $encoding);
                $text = mb_substr($text, 0, mb_strrpos($text, ' ', 0, $encoding), $encoding);
            }
        } else {
            // without encoding
            if (strlen($text) > $limit) {
                $text = substr($text, 0, $limit);
                $text = substr($text, 0, strrpos($text, ' '));
            }
        }

        return $text . $suffix;
    }

    /**
     * Converts a string into a slug
     *
     * @param string $string
     * @return mixed|null
     */
    public static function slugify($string)
    {
        $slug = null;
        $search = array('ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', 'ß', '&szlig;', '&auml;', '&Auml;', '&ouml;', '&Ouml;', '&uuml;', '&Uuml;',
            ' ', '&');
        $replace = array('ae', 'Ae', 'oe', 'Oe', 'ue', 'Ue', 'ss', 'ss', 'ae', 'Ae', 'oe', 'Oe', 'ue', 'Ue', '-', 'und');
        $slug = str_replace($search, $replace, $string);
        $slug = preg_replace('~[^a-zA-Z0-9\-]~', '', $slug);
        $slug = preg_replace('~[\-]{2,}~', '-', $slug);
        $slug = preg_replace('~-$~', '', $slug);

        return $slug;
    }

}
