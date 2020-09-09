<?php

//header('Content-Type: application/json');


include_once('./GestionController.php');
//including dao module
include_once('../gestionModel/DAO.php');
include_once('../gestionModel/gestionModel.php');
include_once('../gestionModel/gestionForm.php');
//instantiation from the module

$dbname = $_POST['dbname'];
$filepath = $_POST['filepath'];


echo "$dbname<br>";
echo "$filepath<br>";


$dao_obj = new DAO($dbname);
$tablesNames = $dao_obj->getTablesNames();
$objModel = new GestionModel($dbname, $filepath);
$objForm =  new GestionForm($dbname, $filepath);
$obj = new GestionController($dbname, $filepath);
foreach($tablesNames as $tablename){
    $obj->createController($tablename["Tables_in_$dbname"]);
    $obj->createRouteFile($tablename["Tables_in_$dbname"]);
    $objModel->createModel($tablename["Tables_in_$dbname"]);
    $objForm->createFormWithVueJS($tablename["Tables_in_$dbname"]);
}

echo "done!";

?>