<?php

//including the controller generator
include_once('./GestionController.php');
//including dao module generator
include_once('../gestionModel/DAO.php');
include_once('../gestionModel/gestionModel.php');
include_once('../gestionModel/gestionForm.php');


//getting the database Name and the file name
$dbname = $_POST['dbname'];
$filepath = $_POST['filepath'];

//ploting request infos
echo "<h4>The database name is : $dbname </h4><br>";
echo "<h4>The database name is : $filepath </h4><br>";


//dao instantiation
$dao_obj = new DAO($dbname);
//getting table names
$tablesNames = $dao_obj->getTablesNames();
//instantiating needed Classes
$objModel = new GestionModel($dbname, $filepath);
$objForm =  new GestionForm($dbname, $filepath);
$objControl = new GestionController($dbname, $filepath);

//looping trought tables in database
/*
foreach($tablesNames as $tablename){
    //creating table controller files
    $objControl->createController($tablename["Tables_in_$dbname"]);
    //creating table Route files
    $objControl->createRouteFile($tablename["Tables_in_$dbname"]);
    //creating table Model files
    $objModel->createModel($tablename["Tables_in_$dbname"]);
    $objForm->createFormWithVueJS($tablename["Tables_in_$dbname"]);

}*/
$objControl->createController('vehicules');
    //creating table Route files
    $objControl->createRouteFile('vehicules');
    //creating table Model files
    $objModel->createModel('vehicules');
//$objForm->createFormWithVueJS('vehicules');

//Success !
echo "<h1>Done successfully!</h1>";

?>