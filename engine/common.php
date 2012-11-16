<?php
require_once("settings.php");
require_once("errors.php");

function generatePassword($email, $pass)
{
    $email = trim($email);
    $pass = trim($pass);
    $emailSha1 = sha1($email);
    $passSha1 = sha1($pass);

    $salt = $passSha1[hexdec($emailSha1[0])].$passSha1[hexdec($emailSha1[1])].
            $passSha1[hexdec($emailSha1[2])].$passSha1[hexdec($emailSha1[3])];

    return sha1($passSha1.$salt);
}

function generateSession($mail, $pass)
{
    $mail = trim($mail);
    $pass = trim($pass);
    $rnd  = rand();
    return sha1($mail.$rnd).sha1($pass.$rnd);
}

function generateActivationKey()
{
   return sha1(time().md5(rand()));
}

function undefinedToBool($var)
{
    if($var == NULL)
    {
        return "NULL";
    }
    else
    {
        if($var)
            return "1";
        else
            return "0";
    }
}

function SendMail($subject, $text, $to)
{
    $from = "admin@shaurmap.ru";
    $header = 'From: shaurmap <'.$from.'>' . "\r\n".
              'Content-type: text/plain; charset="UTF-8"'."\r\n".
              'Reply-To: '.$from.' '. "\r\n" .
              'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $text, $header);
}

function json_response($errcode, $data = NULL)
{
    if($errcode == ERRORS::NO_ERROR)
    {
        echo json_encode(array("status" => $errcode, "data" => $data));
    }
    else if ($errcode == ERRORS::INTERNAL_ERROR && ServerSettings::showDebugInfo)
    {
        echo json_encode(array("status" => $errcode, "data" => $data));
    }
    else
    {
        if(ServerSettings::showDebugInfo)
        {
            echo json_encode(array("status" => $errcode, "data" => ERRORS::$serverMsg[$errcode][0]));
        }
        else
        {
            echo json_encode(array("status" => $errcode, "data" => ERRORS::$serverMsg[$errcode][1]));
        }
    }
}


?>