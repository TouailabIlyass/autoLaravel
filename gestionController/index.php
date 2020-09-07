<?php

//header('Content-Type: application/json');


include_once('./GestionController.php');
//including dao module
include_once('../gestionModel/DAO.php');
//instantiation from the module

$dbname = $_POST['dbname'];
$filepath = $_POST['filepath'];


echo "$dbname<br>";
echo "$filepath<br>";


$dao_obj = new DAO($dbname);
$tablesNames = $dao_obj->getTablesNames();

foreach($tablesNames as $tablename){
    $obj = new GestionController($tablename["Tables_in_$dbname"], $dbname, $filepath);
    $obj->createController();
    $obj->createRouteFile();
}

echo "done!";

?>