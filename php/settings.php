<?php
class DBSettings {
    private $host = 'localhost';
    private $login = 'root';
    private $password = 'Qq12345';
    private $base = 'shaurmap';

    function getHost()
    {
        return $this->host;
    }

    function getLogin()
    {
        return $this->login;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getBase()
    {
        return $this->base;
    }
}

?>