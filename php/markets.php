<?php
require_once("errors.php");
require_once("wrapperdbcomments.php");
require_once("ajaxrequest.php");
require_once("common.php");

$supportedRequests = array(
    "citymarkets" => array(
        array("cityid", "start", "count"),
        true,
        requestCityMarkets
    ),
    "locationmarkets" => array(
        array("x", "y", "w", "h", "start", "count"),
        true,
        requestLocationMarkets
    ),
    "usermarkets" => array(
        array("userid", "start", "count"),
        true,
        requestUserComments
    ),
    "getmarket" => array(
        array("marketid"),
        true,
        requestGetMarket
    ),
    "marketdesc" => array(
        array("commentid"),
        true,
        requestMarketDesc
    ),
    "addmarket" => array(
        array("marketname", "cityid", "latitude", "longitude", "marketphoto",
            "markettext", "addedby", "addeddate", "pig", "chicken", "gloves",
            "lemonade", "cigarettes", "beer", "coffee", "token", "userid"),
        true,
        requestAddMarket
    ),
    "editmarketdesc" => array(
        array("marketid", "marketphoto", "markettext", "addedby", "pig", "chicken", "gloves",
            "lemonade", "cigarettes", "beer", "coffee", "token", "userid"),
        true,
        requestEditMarketDesc
    ),
    "editmarket" => array(
        array("marketid", "marketname", "cityid", "latitude", "longitude", "closed", "token", "userid"),
        true,
        requestEditMarket
    ),
    "deletemarket" => array(
        array("marketid", "token", "userid"),
        true,
        requestDeleteMarket
    ),
);

function requestCityMarkets($pars)
{
    $cityID = $_POST[$pars[0]];
    $start = $_POST[$pars[1]];
    $count = $_POST[$pars[2]];
    $dbConn = new WrapperDBMarkets();
    $res = $dbConn->getMarketsByCity($cityID, $start, $count);
    if($res == ERRORS::NO_ERROR)
    {
        echo json_response($res, $dbConn->getData());
    }
    else
    {
        echo json_response($res);
    }
}

function requestLocationMarkets($pars)
{
    $x = $_POST[$pars[0]];
    $y = $_POST[$pars[1]];
    $w = $_POST[$pars[2]];
    $h = $_POST[$pars[3]];
    $start = $_POST[$pars[4]];
    $count = $_POST[$pars[5]];
    $dbConn = new WrapperDBMarkets();
    $res = $dbConn->getMarketsByLocation($x, $y, $w, $h, $start, $count);
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
    $dbConn = new WrapperDBMarkets();
    $res = $dbConn->getMarketsByUser($userID, $start, $count);
    if($res == ERRORS::NO_ERROR)
    {
        echo json_response($res, $dbConn->getData());
    }
    else
    {
        echo json_response($res);
    }
}

function requestGetMarket($pars)
{
    $marketID = $_POST[$pars[0]];
    $dbConn = new WrapperDBMarkets();
    $res = $dbConn->getMarketByID($marketID);
    if($res == ERRORS::NO_ERROR)
    {
        echo json_response($res, $dbConn->getData());
    }
    else
    {
        echo json_response($res);
    }
}

function requestMarketDesc($pars)
{
    $marketID = $_POST[$pars[0]];
    $dbConn = new WrapperDBMarkets();
    $res = $dbConn->getMarketDesc($marketID);
    if($res == ERRORS::NO_ERROR)
    {
        echo json_response($res, $dbConn->getData());
    }
    else
    {
        echo json_response($res);
    }
}

function requestAddMarket($pars)
{
    $marketName = $_POST[$pars[0]];
    $cityID = $_POST[$pars[1]];
    $latitude = $_POST[$pars[2]];
    $longitude = $_POST[$pars[3]];
    $marketPhoto = $_POST[$pars[4]];
    $marketText = $_POST[$pars[5]];
    $addedBy = $_POST[$pars[6]];
    $addedDate = $_POST[$pars[7]];
    $pig = $_POST[$pars[8]];
    $chicken = $_POST[$pars[9]];
    $gloves = $_POST[$pars[10]];
    $lemonade = $_POST[$pars[11]];
    $cigarettes = $_POST[$pars[12]];
    $beer = $_POST[$pars[13]];
    $coffee = $_POST[$pars[14]];
    $token = $_POST[$pars[15]];
    $userID = $_POST[$pars[16]];

    $dbConn = new WrapperDBMarkets();
    $res = $dbConn->addMarket($marketName, $cityID, $latitude, $longitude, $marketPhoto,
        $marketText, $addedBy, $addedDate, $pig, $chicken, $gloves,
        $lemonade, $cigarettes, $beer, $coffee, $token, $userID);
    echo json_response($res);
}

function requestEditMarketDesc($pars)
{
    $marketID = $_POST[$pars[0]];
    $marketPhoto = $_POST[$pars[1]];
    $marketText = $_POST[$pars[2]];
    $addedBy = $_POST[$pars[3]];
    $addedDate = $_POST[$pars[4]];
    $pig = $_POST[$pars[5]];
    $chicken = $_POST[$pars[6]];
    $gloves = $_POST[$pars[7]];
    $lemonade = $_POST[$pars[8]];
    $cigarettes = $_POST[$pars[9]];
    $beer = $_POST[$pars[10]];
    $coffee = $_POST[$pars[11]];
    $token = $_POST[$pars[12]];
    $userID = $_POST[$pars[13]];

    $dbConn = new WrapperDBMarkets();
    $res = $dbConn->editMarketDesc($marketID, $marketPhoto, $marketText, $addedBy,
        $addedDate, $pig, $chicken, $gloves, $lemonade,
        $cigarettes, $beer, $coffee, $token, $userID);
    echo json_response($res);
}

function requestEditMarket($pars)
{
    $marketID = $_POST[$pars[0]];
    $marketName = $_POST[$pars[1]];
    $cityID = $_POST[$pars[2]];
    $latitude = $_POST[$pars[3]];
    $longitude = $_POST[$pars[4]];
    $closed = $_POST[$pars[5]];
    $token = $_POST[$pars[6]];
    $userID = $_POST[$pars[7]];
    $dbConn = new WrapperDBMarkets();
    $res = $dbConn->editMarket($marketID, $marketName, $cityID, $latitude, $longitude, $closed, $token, $userID);
    echo json_response($res);
}

function requestDeleteMarket($pars)
{
    $marketID = $_POST[$pars[0]];
    $token = $_POST[$pars[1]];
    $userID = $_POST[$pars[2]];
    $dbConn = new WrapperDBMarkets();
    $res = $dbConn->deleteMarket($marketID, $token, $userID);
    echo json_response($res);
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