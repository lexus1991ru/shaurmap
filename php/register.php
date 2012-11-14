<?php
require_once("errors.php");
require_once("ajaxrequest.php");
require_once("common.php");
require_once("wrapperdbregister.php");

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

function requestCheckuser($pars)
{
    $username = $_POST[$pars[0]];
    $dbConn = new WrapperDBRegister();
    $res = $dbConn->checkUser($username);
    echo json_response($res);
}

function requestCheckmail($pars)
{
    $mail = $_POST[$pars[0]];
    $dbConn = new WrapperDBRegister();
    $res = $dbConn->checkMail($mail);
    echo json_response($res);
}

function requestRegister($pars)
{
    $mail = $_POST[$pars[0]];
    $pass1 = $_POST[$pars[1]];
    $pass2 = $_POST[$pars[2]];
    $dbConn = new WrapperDBRegister();
    $result = $dbConn->submitActivationRequest($mail, $pass1, $pass2);
    echo json_response($result);
    // TODO: Send mail to user with activation link
}

function requestConfirm($pars) {
    $mail = $_GET[$pars[0]];
    $key = $_GET[$pars[1]];
    $login = $_GET[$pars[2]];
    $dbConn = new WrapperDBRegister();
    $res = $dbConn->confirmActivation($mail, $key, $login);
    echo json_response($res);
}

$ajaxRequest = new AjaxRequest($supportedRequests);
if (count($_GET)) {
    if(isset($_GET['req']))
    {
        $requestType = $_GET['req'];
        if ($ajaxRequest->isValid())
        {
            $ajaxRequest->executeRequest($requestType);
        }
        else
        {
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
        if ($ajaxRequest->isValid())
        {
            $ajaxRequest->executeRequest($requestType);
        }
        else
        {
            echo json_response(ERRORS::UNKNOWN_POST_REQUEST);
        }
    }
    else
    {
        echo json_response(ERRORS::UNKNOWN_POST_REQUEST);
    }
}

?>