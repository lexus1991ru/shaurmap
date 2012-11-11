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
        self::MYSQL_CONNECT_ERROR => array("Error during connection to the database", "�������� ������ �� ����� ����������� � ���� ������"),
        self::EMAIL_BAD_FORMAT => array("Email has bad format", "�������� ������ email"),
        self::EMAIL_ALREADY_USED => array("Email already used", "������ email ��� ������������"),
        self::EMAIL_ALREADY_USED_REG => array("Email already used in registration", "������ email ��������� � �������� ����������� � �� �����������"),
        self::LOGIN_BAD_FORMAT => array("Usename has bad format", "���� ��� � �������"),
        self::LOGIN_ALREADY_USED => array("Username already used", "������������ � ������ ������ ��� ���������������"),
        self::ACTIVATION_REQUEST_MYSQL_ERROR => array("Mysql error in submitActivationRequest function", ""),
        self::ACTIVATION_LINK_MYSQL_ERROR => array("Mysql error in checkActivationLink function", ""),
        self::ACTIVATION_LINK_ACTIVATED => array("Activation link was activated", "������� ��� ��� �����������"),
        self::ACTIVATION_LINK_NOT_FOUND => array("Activation link not found", "�������� ������ ��� ��������� ��������"),
        self::ACTIVATION_CONFIRM_MYSQL_ERROR => array("Mysql error in confirmActivation function", ""),
        self::BAD_PASSWORD_FORMAT => array("Bad password format", "���� ��� � �������"),
        self::PASSWORDS_NOT_EQUAL => array("Passwords is not equal", "������ �� ���������"),
        self::ACTIVATION_CONFIRMED => array("Activation confirmed", "��������� �������� ���������!"),
        self::UNKNOWN_GET_REQUEST => array("Unknown GET request", ""),
        self::UNKNOWN_POST_REQUEST => array("Unknown POST request", "")
    );
}

?>