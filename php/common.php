<?php

function generatePassword($mail, $pass)
{
    $mail = trim($mail);
    $pass = trim($pass);
    $mailSha1 = sha1($mail);
    $passSha1 = sha1($pass);

    $salt = $passSha1[hexdec($mailSha1[0])].$passSha1[hexdec($mailSha1[1])].
            $passSha1[hexdec($mailSha1[2])].$passSha1[hexdec($mailSha1[3])];

    return sha1($passSha1.$salt);
}

 function generateActivationKey()
 {
    return sha1(time().md5(rand()));
 }

function validateMail($email)
{
    $email = trim($email);
    $pattern = "|^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$|";
    if (preg_match($pattern, $email)){
        return true;
    }
    return false;
}

function validateUser($username)
{
    // TODO: use approved symbols for checking username
    $username = trim($username);
    if ((strlen($username) < ServerSettings::getMinUsernameLenght()) || (strlen($username) > ServerSettings::getMaxUsernameLength()))
    {
        return false;
    }
    return true;
}

function validatePass($pass)
{
    $pass = trim($pass);
    if ((strlen($pass) < ServerSettings::getMinPasswordLenght()) || (strlen($pass) > ServerSettings::getMaxPasswordLenght()))
    {
        return false;
    }
    return true;
}

function json_response($status, $data)
{
    return json_encode(array("status" => $status, "data" => $data));
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

?>