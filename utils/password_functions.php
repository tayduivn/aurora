<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Refurbished: 17 November 2015 at 13:37:13 GMT, Sheffield UK

 Copyright (c) 2015, Inikoo

 Version 3.0
*/

function generatePassword($length = 9, $strength = 0) {
    $vowels     = 'aeiu23456789';
    $consonants = 'qwertyupasfghjkzxcvbnm';
    if ($strength >= 1) {
        $consonants .= 'QWERTYUPASDFGHJKLZXCVBNM';
        $vowels     .= 'AEU';
    }

    if ($strength >= 3) {
        $vowels     = '12345wertyuiopasdfghjklzxcvbnm';
        $consonants = '67890QWERTYUIOPASDFGHJKLZXCVBNM';
    }

    if ($strength >= 4) {
        $vowels     .= '!=/[]{}~\<>$%^&*()_+@#.,%';
        $consonants .= '!=/[]{}~\<>$%^&*()_+@#.,%';
    }

    if ($strength >= 5) {
        $vowels     = " !\"#$%&'()*+,-./:;<=>?@[\]^_`{|}~qwertyuiopasdfghjklzxcvbnm1234567890";
        $consonants = " !\"#$%&'()*+,-./:;<=>?@[\]^_`{|}~QWERTYUIOPASDFGHJKLZXCVBNM1234567890";
    }


    $password = '';
    $alt      = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(mt_rand() % strlen($consonants))];
            $alt      = 0;
        } else {
            $password .= $vowels[(mt_rand() % strlen($vowels))];
            $alt      = 1;
        }
    }

    return $password;
}
