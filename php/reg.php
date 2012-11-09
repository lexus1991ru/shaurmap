<?php
require_once("wrapperdb.php");
$supportedRequests = array(
                        "checkuser" => array(
                                            array("username"),
                                            function ($pars)
                                            {
                                                $username = $_POST[$pars[0]];
                                                $dbConn = new WrapperDB();
                                                $res = $dbConn->checkUser($username);
                                                echo json_encode("RESPONSE ".$res);
                                            }
                                            ),
                        "checkmail" => array("mail"),
                        "register" => array("mail", "pass1", "pass2")
                     );

if(isset($_POST))
{
    $requestType = $_POST['req'];
    if(in_array($requestType, array_keys($supportedRequests)))
    {
        // check parameters for request
        foreach($supportedRequests[$requestType][0] as $par)
        {
            if(!isset($_POST[$par]))
            {
                echo "ERROR";
                return;
            }
        }

        $supportedRequests[$requestType][1]($supportedRequests[$requestType][0]);
    }
    else
    {
        echo "Unknown Request";
        return;
    }
}

?>