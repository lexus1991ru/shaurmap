<?php

require_once("ajaxrequest.php");
require_once("wrapperdbregister.php");

$supportedRequests = array(
    "checkuser" => array(
        array("username"),
        true,
        requestCheckuser
    ),
    "checkmail" => array(
        array("email"),
        true,
        requestCheckmail
    ),
    "register" => array(
        array("email", "pass1", "pass2"),
        true,
        requestRegister
    ),
    "confirm" => array(
        array("email", "key", "login"),
        true,
        requestConfirm
    )
);

function requestCheckuser($pars)
{
    $username = $_POST[$pars[0]];
    $dbConn   = new WrapperDBRegister();
    $res      = $dbConn->checkUser($username);
    echo json_response($res);
}

function requestCheckmail($pars)
{
    $email    = $_POST[$pars[0]];
    $dbConn   = new WrapperDBRegister();
    $res      = $dbConn->checkMail($email);
    echo json_response($res);
}

function requestRegister($pars)
{
    $email    = $_POST[$pars[0]];
    $pass1    = $_POST[$pars[1]];
    $pass2    = $_POST[$pars[2]];
    $dbConn   = new WrapperDBRegister();
    $result   = $dbConn->submitActivationRequest($email, $pass1, $pass2);
    echo json_response($result);
    // TODO: Send mail to user with activation link
}

function requestConfirm($pars) {
    $email    = $_POST[$pars[0]];
    $key      = $_POST[$pars[1]];
    $login    = $_POST[$pars[2]];
    $dbConn   = new WrapperDBRegister();
    $res      = $dbConn->confirmActivation($email, $key, $login);
    echo json_response($res);
}

$ajaxRequest = new AjaxRequest($supportedRequests);
$res = $ajaxRequest->executeRequest($_GET, $_POST);
if($res != ERRORS::NO_ERROR)
{
    echo json_response($res);
}

?>