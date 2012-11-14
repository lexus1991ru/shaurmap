<?php

require_once("errors.php");
require_once("newdbwrapper.php");
require_once("ajaxrequest.php");
require_once("common.php");
require_once("settings.php");

function requestPostComment($pars)
{
    $marketID = $_POST[$pars[0]];
    $userID = $_POST[$pars[1]];
    $mark = $_POST[$pars[2]];
    $text = $_POST[$pars[3]];
    $token = $_POST[$pars[4]];
    $dbConn = new WrapperDBComments();
    $res = $dbConn->postComment($marketID, $userID, $mark, $text, $token);
    echo json_response($res);
}

function requestMarketComments($pars)
{
    $marketID = $_POST[$pars[0]];
    $start = $_POST[$pars[1]];
    $count = $_POST[$pars[2]];
    $token = $_POST[$pars[3]];
    $userID = $_POST[$pars[4]];
    $dbConn = new WrapperDBComments();
    $res = $dbConn->getCommentsByMarketID($marketID, $start, $count, $token, $userID);
    if($res == ERRORS::NO_ERROR)
    {
        echo json_response($res, $dbConn->getData());
    }
    else
    {
        echo json_response($res);
    }
}

$supportedRequests = array(
    "postcomment" => array(
        array("marketid", "userid", "mark", "text", "token"),
        true,
        requestPostComment
    ),
    "marketcomments" => array(
        array("marketid", "start", "count", "token", "userid"),
        true,
        requestMarketComments
    ),
);

$ajaxRequest = new AjaxRequest($supportedRequests);
if (count($_GET)) {
    if(isset($_GET['req']))
    {
        $requestType = $_GET['req'];
        if($ajaxRequest->isValid())
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