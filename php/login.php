<?php

require_once("errors.php");
require_once("newdbwrapper.php");
require_once("ajaxrequest.php");
require_once("common.php");
require_once("settings.php");

function requestLogin($pars)
{
    $login = $_POST[$pars[0]];
    $password = $_POST[$pars[1]];
    if((strlen($login) > 3) && (strlen($password) > 3))
    {
        $dbConn = new WrapperDB();
        $res = $dbConn->loginUser($login, $password);
        if($res == ERRORS::NO_ERROR)
        {
            echo json_response($res, $dbConn->getData());
        }
        else
        {
            echo json_response($res);
        }
    }
    else
    {
        echo json_response(ERRORS::INTERNAL_ERROR, "Fuck you idiot");
    }
}

function requestLogout($pars)
{
    $token = $_POST[$pars[0]];
    if(strlen($token < 80))
    {
        $dbConn = new WrapperDB();
        $res = $dbConn->logout($token);
        if($res == ERRORS::NO_ERROR)
        {
            echo json_response($res);
        }
        else
        {
            echo json_response($res);
        }
    }
    else
    {
        echo json_response(ERRORS::INTERNAL_ERROR, "Fuck you idiot");
    }
}

$supportedRequests = array(
    "login" => array(
        array("login", "password"),
        true,
        requestLogin
    ),
    "logout" => array(
        array("token"),
        true,
        requestLogout
    ),
);

$ajaxRequest = new AjaxRequest($supportedRequests);
if (count($_GET)) {
    if(isset($_GET['req']))
    {
        $requestType = $_GET['req'];
        if ($ajaxRequest->isValid()) {
            $ajaxRequest->executeRequest($requestType);
        } else {
            echo json_response(ERRORS::UNKNOWN_GET_REQUEST);
        }
    }
    else
    {
        echo json_response(ERRORS::UNKNOWN_GET_REQUEST);
    }
}
else if (count($_POST)) {
    if(isset($_POST['req']))
    {
        $requestType = $_POST['req'];
        if ($ajaxRequest->isValid()) {
            $ajaxRequest->executeRequest($requestType);
        } else {
            echo json_response(ERRORS::UNKNOWN_POST_REQUEST);
        }
    }
    else
    {
        echo json_response(ERRORS::UNKNOWN_POST_REQUEST);
    }
}

?>