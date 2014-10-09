<?php

define('ORACLE10_MAGIC', '0123456789ABCDEF');

/**
 * Oracle 10g password hash
 * This class implements the hash algorithm used by the 
 * Oracle Database up to version 10g.
 *
 * @author Vitor Martins
 */
class Oracle10
{
    function __construct()
    {
        if (!function_exists('mcrypt_encrypt'))
        {
            die('cannot find mcrypt extension.');
        }
    }
    
    /*
     * Converts all characters to uppercase and concatenates
     * the username and the password.
     * Then the function stores all characters using 2 bytes
     * per character, zeroing the high bytes.
     *
     */
    function prepare($user, $pass)
    {
        $concat = strToUpper($user.$pass);

        $hex='';
        for ($i=0; $i < strlen($concat); $i++)
        {
            $hex .= '\x00\x'.dechex(ord($concat[$i]));
        }

        return $hex;
    }

    function escape_hex($str)
    {
        $parts = str_split($str, 2);
        $hex = '';
        foreach ($parts as $part)
        {
            $hex .= '\x'.$part;
        }

        return $hex;
    }

    function des_cbc_encrypt($key, $value)
    {
        $hash = $iv = str_repeat('\x00', 8); //start things off
       
        $blocks = str_split($value, 32); //sliced into blocks of 8 bytes
        foreach ($blocks as $block)
        {
            $block = str_pad($block, 32, '\x00', STR_PAD_RIGHT); //padded with zeros if needed

            $chunk = stripcslashes($hash) ^ stripcslashes($block); //XOR

            $hash = mcrypt_encrypt(MCRYPT_DES, stripcslashes($key), $chunk, MCRYPT_MODE_CBC, stripcslashes($iv));
        }
        
        return strToUpper(bin2hex($hash));
    }


    function encrypt($user, $pass)
    {
        $input = $this->prepare($user, $pass);

        $hash = $this->escape_hex(ORACLE10_MAGIC);
        $hash = $this->des_cbc_encrypt($hash, $input);

        $hash = $this->escape_hex($hash);
        $hash = $this->des_cbc_encrypt($hash, $input);

        return $hash;
    }

}
