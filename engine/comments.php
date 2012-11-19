<?php

require_once("ajaxrequest.php");
require_once("wrapperdbcomments.php");

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
    "canpostcomment" => array(
        array("token", "userid"),
        true,
        requestCanPostComment
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
        array("commentid", "text", "token", "userid"),
        true,
        requestEditComment
    ),
    "deletecomment" => array(
        array("commentid", "token", "userid"),
        true,
        requestDeleteComment
    ),
    "getunmoderatedcomments" => array(
        array("start", "count", "token", "userid"),
        true,
        requestGetUnmoderatedComments
    ),
    "approvecomment" => array(
        array("commentid", "action", "token", "userid"),
        true,
        requestApproveComment
    ),
);

function requestMarketComments($pars)
{
    $marketID = $_POST[$pars[0]];
    $start = $_POST[$pars[1]];
    $count = $_POST[$pars[2]];
    $token = $_POST[$pars[3]];
    $userID = $_POST[$pars[4]];
    $dbConn = new WrapperDBComments();
    $res = $dbConn->getCommentsByMarket($marketID, $start, $count, $token, $userID);
    if($res == ERRORS::NO_ERROR)
    {
        echo json_response($res, $dbConn->getData());
    }
    else
    {
        echo json_response($res);
    }
}

function requestUserComments($pars)
{
    $userID = $_POST[$pars[0]];
    $start = $_POST[$pars[1]];
    $count = $_POST[$pars[2]];
    $token = $_POST[$pars[3]];
    $dbConn = new WrapperDBComments();
    $res = $dbConn->getCommentsByUser($userID, $start, $count, $token);
    if($res == ERRORS::NO_ERROR)
    {
        echo json_response($res, $dbConn->getData());
    }
    else
    {
        echo json_response($res);
    }
}

function requestComment($pars)
{
    $userID = $_POST[$pars[0]];
    $start = $_POST[$pars[1]];
    $count = $_POST[$pars[2]];
    $token = $_POST[$pars[3]];
    $dbConn = new WrapperDBComments();
    $res = $dbConn->getCommentByID($commentID, $token, $userID);
    if($res == ERRORS::NO_ERROR)
    {
        echo json_response($res, $dbConn->getData());
    }
    else
    {
        echo json_response($res);
    }
}
function requestCanPostComment($pars)
{
    $token  =  $_POST[$pars[0]];
    $userID = $_POST[$pars[1]];
    $dbConn = new WrapperDBComments();
    $res    = $dbConn->canPostComment($token, $userID);
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
    $commentID = $_POST[$pars[0]];
    $mark = $_POST[$pars[1]];
    $text = $_POST[$pars[2]];
    $token = $_POST[$pars[3]];
    $userID = $_POST[$pars[4]];
    $dbConn = new WrapperDBComments();
    $res = $dbConn->editComment($commentID, $mark, $text, $token, $userID);
    echo json_response($res);
}

function requestDeleteComment($pars)
{
    $commentID = $_POST[$pars[0]];
    $token = $_POST[$pars[1]];
    $userID = $_POST[$pars[2]];
    $dbConn = new WrapperDBComments();
    $res = $dbConn->deleteComment($commentID, $token, $userID);
    echo json_response($res);
}

function requestGetUnmoderatedComments($pars)
{
    $start = $_POST[$pars[0]];
    $count = $_POST[$pars[1]];
    $token = $_POST[$pars[2]];
    $userID = $_POST[$pars[3]];
    $dbConn = new WrapperDBComments();
    $res = $dbConn->getUnmoderatedComments($start, $count, $token, $userID);
    if($res == ERRORS::NO_ERROR)
    {
        echo json_response($res, $dbConn->getData());
    }
    else
    {
        echo json_response($res);
    }
}

function requestApproveComment($pars)
{
    $commentID = $_POST[$pars[0]];
    $action = $_POST[$pars[1]];
    $token = $_POST[$pars[2]];
    $userID = $_POST[$pars[3]];
    $dbConn = new WrapperDBComments();
    $res = $dbConn->approveComment($commentID, $action, $token, $userID);
    echo json_response($res);
}

$ajaxRequest = new AjaxRequest($supportedRequests);
$res = $ajaxRequest->executeRequest($_GET, $_POST);
if($res != ERRORS::NO_ERROR)
{
    echo json_response($res);
}

?>