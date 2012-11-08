<?php
require_once("php/wrapperdb.php");
$a = new WrapperDB();

$a->Connect();

//$a->getCities();
//$a->getMarkets();
//$a->checkUser("Admin", "Qq12345");

print_r($a->getMarketDesc(1));

$a->Disconnect();

?>