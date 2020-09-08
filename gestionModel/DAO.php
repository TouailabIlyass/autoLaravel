<?php

include_once 'connectionDB.php';

class DAO{

    //Class vars
    private $pdo = NULL;
    private $dbname = NULL;

    //Class Constructor
    public function __construct($db)
    {
        $this->dbname = $db;
        $this->pdo=ConnectionDB::getConnectionDB($db);
    }

    /*
    Input: take a table name and a filter as input
    Role: get table infos : Attributes, infos ( isNull, isPrimaryKey ...)
    Output: an array of table Informations
    */
    public function getTableInfos($table, $filtre=false)
    {
        $stmt=$this->pdo->prepare("show columns from $table where Field not in ('created_at' , 'updated_at' , 'deleted_at')");
        $stmt->execute();
        //if the filter is true we return all tables informations
        if($filtre === true)
            return $stmt->fetchAll();
        //else we ignore the auto_increment table attribute row
        $array = [];
        foreach($stmt->fetchAll() as $item)
        {
            if(strtolower($item['Extra']) === 'auto_increment') continue;
            $array[] = $item;
        }
        return $array;
    }

    /*
    Input: None
    Role: show the selected database tables
    Output: return an array of tables in the database
    */
    public function getTablesNames()
    {
        $stmt=$this->pdo->prepare("SHOW tables from $this->dbname");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}