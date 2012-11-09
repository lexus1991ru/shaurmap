<?php

function generatePassword($login, $pass)
{
    $loginSha1 = sha1($login);
    $passSha1 = sha1($pass);

    $salt = $passSha1[hexdec($loginSha1[0])].$passSha1[hexdec($loginSha1[1])].
            $passSha1[hexdec($loginSha1[2])].$passSha1[hexdec($loginSha1[3])];

    return sha1($passSha1.$salt);
}



?>