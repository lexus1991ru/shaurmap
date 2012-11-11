<?php

require_once("settings.php");
require_once("common.php");
require_once("errors.php");

class AjaxRequest
{
    private $requests;

    private function validateRequests($reqs)
    {
        if(ServerSettings::$showDebugInfo)
        {
            if(is_array($reqs))
            {
                foreach($reqs as $key => $val)
                {
                    if(is_string($key))
                    {
                        if(is_array($val))
                        {
                            if(count($val) == 3)
                            {
                                if(is_array($val[0]))
                                {
                                    foreach($val[0] as $par)
                                    {
                                        if(!is_string($par))
                                        {
                                            json_response(ERRORS::INTERNAL_ERROR, "Parameter ".$par." must be a string");
                                            return false;
                                        }
                                    }
                                }
                                else
                                {
                                    echo json_response(ERRORS::INTERNAL_ERROR, "Request parameters in body must be in array");
                                    return false;
                                }
                                if(!is_bool($val[1]))
                                {
                                    echo json_response(ERRORS::INTERNAL_ERROR, "HTTP method type for request ". $key ." is not bool");
                                    return false;
                                }
                                if(!is_callable($val[2]))
                                {
                                    echo json_response(ERRORS::INTERNAL_ERROR, "Request function in body is not a function");
                                    return false;
                                }
                            }
                            else
                            {
                                echo json_response(ERRORS::INTERNAL_ERROR, "Body for request ".$key." array must have size 2");
                                return false;
                            }
                        }
                        else
                        {
                            echo json_response(ERRORS::INTERNAL_ERROR, "Body for request ".$key." must be an array");
                            return false;
                        }
                    }
                    else
                    {
                        echo json_response(ERRORS::INTERNAL_ERROR, "Request name be a string");
                        return false;
                    }
                }
            }
            else
            {
                echo json_response(ERRORS::INTERNAL_ERROR, "Requests array is not an array");
                return false;
            }
        }
        return true;
    }

    function __construct($_requests)
    {
        if($this->validateRequests($_requests))
        {
            $this->requests = $_requests;
        }
        else
        {
            $this->requests = NULL;
        }
    }

    function isValid()
    {
        if($this->requests != NULL)
        {
            return true;
        }
        return false;
    }

    function executeRequest($requestName)
    {
        if(in_array($requestName, array_keys($this->requests)))
        {
            $pars   = $this->requests[$requestName][0];
            $isPost = $this->requests[$requestName][1];
            $func   = $this->requests[$requestName][2];

            $req = $isPost ? $_POST : $_GET;
            foreach($pars as $par)
            {
                if(!isset($req[$par]))
                    {
                        echo json_response(ERRORS::INTERNAL_ERROR, "Parameter '".$par."' not found for request '".$requestName."'");
                        return;
                    }
            }
            $func($pars);
        }
        else
        {
            echo json_response(ERRORS::INTERNAL_ERROR, "Unknown request ".$requestName);
        }
    }
}

?>