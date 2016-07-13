<?php

namespace Sle\Utilities\Security;

/**
 * PasswordCrypt
 *
 * This class offers methods for passwort encraptions, password check and generating
 * random passwords.
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class PasswordCrypt
{

    /**
     * Password encryption with hash_hmac, str_shuffle and crypt
     *
     * @param $salt
     * @param $email
     * @param $password
     * @param string $rounds
     * @return array
     */
    public static function bcrypt($salt, $email, $password, $rounds = '08')
    {
        $storeSalt = substr(str_shuffle($salt), 0, 22);
        $string = hash_hmac('whirlpool', str_pad($password, strlen($password) * 4, sha1($email), STR_PAD_BOTH),
            $storeSalt, true);
        $salt = substr(str_shuffle($salt), 0, 22);

        return array(
            'password' => crypt($string, '$2a$' . $rounds . '$' . $salt),
            'salt'     => $storeSalt,
        );
    }

    /**
     *
     * @param string $salt
     * @param string $email
     * @param string $password
     * @param string $stored
     * @return boolean
     */
    public static function bcryptCheck($salt, $email, $password, $stored)
    {
        $string = hash_hmac('whirlpool', str_pad($password, strlen($password) * 4, sha1($email), STR_PAD_BOTH), $salt,
            true);

        return crypt($string, substr($stored, 0, 30)) == $stored;
    }

    /**
     * Creates a random password
     *
     * @param $letters
     * @param int $chars
     * @return string
     */
    public static function randomPassword($letters, $chars = 8)
    {
        return substr(str_shuffle($letters), 0, $chars);
    }

}
