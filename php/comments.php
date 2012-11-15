<?php

require_once("errors.php");
require_once("wrapperdbcomments.php");
require_once("ajaxrequest.php");
require_once("common.php");

$supportedRequests = array(
    "marketcomments" => array(
        array("marketid", "start", "count", "token", "userid"),
        true,
        requestMarketComments
    ),
    "usercomments" => array(
        array("userid", "start", "count", "token"),
        true,
        requestUserComments
    ),
    "getcomment" => array(
        array("commentid", "token", "userid"),
        true,
        requestComment
    ),
    "postcomment" => array(
        array("marketid", "userid", "mark", "text", "token"),
        true,
        requestPostComment
    ),
    "rankcomment" => array(
        array("commentid", "thumbsup", "token", "userid"),
        true,
        requestRankComment
    ),
    "editcomment" => array(
        array("commentid", "token", "userid"),
        true,
        requestEditComment
    ),
    "deletecomment" => array(
        array("commentid", "token", "userid"),
        true,
        requestDeleteComment
    ),
);

function requestUserComments($pars)
{
    ;
}

function requestComment($pars)
{
    ;
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

function requestRankComment($pars)
{
    $commentID = $_POST[$pars[0]];
    $isThumbsUp = $_POST[$pars[1]];
    $token = $_POST[$pars[2]];
    $userID = $_POST[$pars[3]];
    $dbConn = new WrapperDBComments();
    $res = $dbConn->rankComment($commentID, $isThumbsUp, $token, $userID);
    echo json_response($res);
}

function requestEditComment($pars)
{
    ;
}

function requestDeleteComment($pars)
{
    ;
}

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