<?php
require_once("wrapperdb.php");
require_once("ajaxrequest.php");
require_once("common.php");

$supportedRequests = array(
    "checkuser" => array(
        array("username"),
        true,
        function ($pars) {
            $username = $_POST[$pars[0]];
            $dbConn = new WrapperDB();
            $res = $dbConn->checkUser($username);
            echo json_response(200, $res);
        }
    ),
    "checkmail" => array(
        array("mail"),
        true,
        function ($pars) {
            $mail = $_POST[$pars[0]];
            $dbConn = new WrapperDB();
            $res = $dbConn->checkMail($mail);
            echo json_response(200, $res);
        }
    ),
    "register" => array(
        array("mail", "pass1", "pass2"),
        true,
        function ($pars) {
            $mail = $_POST[$pars[0]];
            $pass1 = $_POST[$pars[1]];
            $pass2 = $_POST[$pars[2]];
            if (md5($pass1) == md5($pass2)) {
                if(validatePass($pass1)){
                    if (!validateMail($mail))
                    {
                        echo json_response(500, "Mail is incorrect");
                    }
                    else
                    {
                        $dbConn = new WrapperDB();
                        $result = $dbConn->submitActivationRequest($mail, $pass1);
                        if ($result)
                        {
                            echo json_response(500, "Result is fail: " . $result);
                        }
                        else
                        {
                            echo json_response(200, "Result is ok");
                        }
                    }
                }
                else
                {
                    echo json_response(500, "Password is incorrect!");
                }

            } else {
                echo json_response(500, "Passwords is not equal");
            }
            // send mail
        }
    ),
    "confirm" => array(
        array("mail", "key", "login"),
        false,
        function ($pars) {
            $mail = $_GET[$pars[0]];
            $key = $_GET[$pars[1]];
            $login = $_GET[$pars[2]];
            $dbConn = new WrapperDB();
            if ($dbConn->checkActivationLink($mail, $key))
            {
                 if ($dbConn->checkUser($login))
                 {
                    echo json_response(500, "Login already used");
                 }
                 $result = $dbConn->confirmActivation($mail, $key, $login);
                 if ($result)
                 {
                    echo json_response(500, "Confirm activation fail: ".$result);
                 }
                 else
                 {
                    echo json_response(200, "Activation confirmed!");
                 }
            }
            else
            {
                echo json_response(500, "Incorrect activation email or key");
            }

        }
    )

);

$ajaxRequest = new AjaxRequest($supportedRequests);
if (count($_GET)) {
    $requestType = $_GET['req'];
    if ($ajaxRequest->isValid()) {
        $ajaxRequest->executeRequest($requestType);
    } else {
        echo json_response(500, "Unknown GET request");
    }
}
else if (count($_POST)) {
    $requestType = $_POST['req'];
    if ($ajaxRequest->isValid()) {
        $ajaxRequest->executeRequest($requestType);
    } else {
        echo json_response(500, "Unknown POST request");
    }
}

?>