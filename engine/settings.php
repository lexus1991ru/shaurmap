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
    const showDebugInfo   = true;
    const sessionLiveTime = 2592000;// 60*60*24*30 = 30 days
    const tokenLength     = 80;

    // Registration
    const maxUsernameLength = 32;
    const minUsernameLength = 3;
    const maxPasswordLength = 32;
    const minPasswordLength = 6;
    const maxEmailLength    = 64;
    const loginRegExp       = "|^[a-z0-9-_\.]+$|i";
    const emailRegExp       = "|^[_a-z0-9-\\+]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+(\\.[a-z0-9]+)*(\\.[a-z]{2,})$|i";

    // Comments
    const maxMarketMark        = 5;
    const maxCommentLength     = 2000;
    const minCommentLength     = 0;
    const maxCommentsInRequest = 100;

    // Markets
    const maxMarketNameLength = 32;
    const minMarketNameLength = 6;
}

?>