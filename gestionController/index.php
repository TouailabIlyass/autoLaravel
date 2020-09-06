<?php

//header('Content-Type: application/json');


include_once('./GestionController.php');

$dbname = 'data';

$tablename = 'clients';

$obj = new GestionController($tablename, $dbname, '../');


//echo $obj->makingValidationFunctionString();

$obj->createController();
$obj->createRouteFile();

?>