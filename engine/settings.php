<?php

class DBSettings
{
    const host = "localhost";
    const login = "root";
    const password = "Qq12345";
    const base = "shaurmap";
}

class ServerSettings
{
    // Server
    const showDebugInfo = true;
    const sessionLiveTime = 2592000;// 60*60*24*30 = 30 days
    const tokenLength = 80;

    // Registration
    const maxUsernameLength = 32;
    const minUsernameLenght = 3;
    const maxPasswordLenght = 32;
    const minPasswordLenght = 6;
    const loginRegExp = "|[a-z]+|";
    const emailRegExp = "|^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$|";

    // Comments
    const maxMarketMark = 5;
    const maxCommentLength = 2000;
    const minCommentLength = 0;

    // Markets
    const maxMarketNameLenght = 32;
    const minMarketNameLenght = 6;
}

?>