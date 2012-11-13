<?php
require_once("errors.php");
require_once("newdbwrapper.php");
require_once("ajaxrequest.php");
require_once("common.php");

function requestCheckuser($pars)
{
    $username = $_POST[$pars[0]];
    if(strlen($username > 3))
    {
        $dbConn = new WrapperDB();
        $res = $dbConn->checkUser($username);
        echo json_response($res);
    }
    else
    {
        echo json_response(ERRORS::LOGIN_BAD_FORMAT);
    }
}

function requestCheckmail($pars)
{
    $mail = $_POST[$pars[0]];
    if(strlen($mail) > 6)
    {
        $dbConn = new WrapperDB();
        $res = $dbConn->checkMail($mail);
        echo json_response($res);
    }
    else
    {
        echo json_response(ERRORS::EMAIL_BAD_FORMAT);
    }
}

function requestRegister($pars)
{
    $mail = $_POST[$pars[0]];
    $pass1 = $_POST[$pars[1]];
    $pass2 = $_POST[$pars[2]];
    if (md5($pass1) == md5($pass2)) {
        if(validatePass($pass1)){
            $dbConn = new WrapperDB();
            $result = $dbConn->submitActivationRequest($mail, $pass1);
            echo json_response($result);
        }
        else
        {
            echo json_response(ERRORS::BAD_PASSWORD_FORMAT);
        }
    }
    else
    {
        echo json_response(ERRORS::PASSWORDS_NOT_EQUAL);
    }
    // TODO: Send mail to user with activation link
}

function requestConfirm($pars) {
    $mail = $_GET[$pars[0]];
    $key = $_GET[$pars[1]];
    $login = $_GET[$pars[2]];
    $dbConn = new WrapperDB();
    $res = $dbConn->checkActivationLink($mail, $key);
    if($res == ERRORS::NO_ERROR)
    {
        $res = $dbConn->confirmActivation($mail, $key, $login);
        echo json_response($res);
    }
    else
    {
        echo json_response($res);
    }
}

$supportedRequests = array(
    "checkuser" => array(
        array("username"),
        true,
        requestCheckuser
    ),
    "checkmail" => array(
        array("mail"),
        true,
        requestCheckmail
    ),
    "register" => array(
        array("mail", "pass1", "pass2"),
        true,
        requestRegister
    ),
    "confirm" => array(
        array("mail", "key", "login"),
        false,
        requestConfirm
    )
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