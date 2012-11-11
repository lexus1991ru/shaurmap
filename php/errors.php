<?php

class ERRORS
{
    const INTERNAL_ERROR = -1;
    const NO_ERROR = 0;
    const MYSQL_CONNECT_ERROR = 1;
    const EMAIL_BAD_FORMAT = 2;
    const EMAIL_ALREADY_USED = 3;
    const EMAIL_ALREADY_USED_REG = 4;
    const LOGIN_BAD_FORMAT = 5;
    const LOGIN_ALREADY_USED = 6;
    const ACTIVATION_REQUEST_MYSQL_ERROR = 7;
    const ACTIVATION_LINK_MYSQL_ERROR = 8;
    const ACTIVATION_LINK_ACTIVATED = 9;
    const ACTIVATION_LINK_NOT_FOUND = 10;
    const ACTIVATION_CONFIRM_MYSQL_ERROR = 11;
    const BAD_PASSWORD_FORMAT = 12;
    const PASSWORDS_NOT_EQUAL = 13;
    const ACTIVATION_CONFIRMED = 14;
    const UNKNOWN_GET_REQUEST = 15;
    const UNKNOWN_POST_REQUEST = 16;

    static public $serverMsg = array
    (
        self::NO_ERROR => array("OK", "OK"),
        self::MYSQL_CONNECT_ERROR => array("Error during connection to the database", "Возникла ошибка во время подключения к базе данных"),
        self::EMAIL_BAD_FORMAT => array("Email has bad format", "Неверный формат email"),
        self::EMAIL_ALREADY_USED => array("Email already used", "Данный email уже используется"),
        self::EMAIL_ALREADY_USED_REG => array("Email already used in registration", "Данный email находится в процессе регистрации и не подтвержден"),
        self::LOGIN_BAD_FORMAT => array("Usename has bad format", "СОСИ ХУЙ С ЛОГИНОМ"),
        self::LOGIN_ALREADY_USED => array("Username already used", "Пользователь с данным именем уже зарегистрирован"),
        self::ACTIVATION_REQUEST_MYSQL_ERROR => array("Mysql error in submitActivationRequest function", ""),
        self::ACTIVATION_LINK_MYSQL_ERROR => array("Mysql error in checkActivationLink function", ""),
        self::ACTIVATION_LINK_ACTIVATED => array("Activation link was activated", "Аккаунт уже был активирован"),
        self::ACTIVATION_LINK_NOT_FOUND => array("Activation link not found", "Неверная ссылка для активации аккаунта"),
        self::ACTIVATION_CONFIRM_MYSQL_ERROR => array("Mysql error in confirmActivation function", ""),
        self::BAD_PASSWORD_FORMAT => array("Bad password format", "СОСИ ХУЙ С ПАРОЛЕМ"),
        self::PASSWORDS_NOT_EQUAL => array("Passwords is not equal", "Пароли не совпадают"),
        self::ACTIVATION_CONFIRMED => array("Activation confirmed", "Активация аккаунта завершена!"),
        self::UNKNOWN_GET_REQUEST => array("Unknown GET request", ""),
        self::UNKNOWN_POST_REQUEST => array("Unknown POST request", "")
    );
}

?>