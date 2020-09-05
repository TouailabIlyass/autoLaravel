<?php

//header('Content-Type: application/json');

include_once('../gestionModel/DAO.php');
include_once('./GestionController.php');

$dbname = 'biblioappfx';

$dao_obj = new DAO($dbname);

$tablename = 'achats';

$attributesName = array();

$arr = $dao_obj->getTableInfo($tablename);

//echo json_encode($arr);

foreach($arr as $item){
    array_push($attributesName, $item['Field']);
}

function getVarcharAttributesMaxLenght()
{
    $data = $this->getTableInfo('clients');
    foreach($data as $item)
    {
        if (strpos($item['Type'], 'varchar') !== false) {
            $your_array = explode("(", $item['Type']);
            $your_array = explode(")",$your_array[1]);
        }
    }
}


$obj = new GestionController($tablename, $attributesName, '../');


//echo $obj->makingValidationFunctionString();

$obj->createController();
$obj->createRouteFile();

?>