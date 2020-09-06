<?php

include_once 'connectionDB.php';

class DAO{


    private $pdo = NULL;
    private $dbname = NULL;

    public function __construct($db)
    {
        $this->dbname = $db;
        $this->pdo=ConnectionDB::getConnectionDB($db);
    }

    public function getTableInfos($table, $filtre=false)
    {
        $stmt=$this->pdo->prepare("show columns from $table where Field not in ('created_at' , 'updated_at' , 'deleted_at')");
        $stmt->execute();
        if($filtre === true)
            return $stmt->fetchAll();
        $array = [];
        foreach($stmt->fetchAll() as $item)
        {
            if(strtolower($item['Extra']) === 'auto_increment') continue;
            $array[] = $item;
        }
        return $array;
    }
    public function getTablesNames()
    {
        $stmt=$this->pdo->prepare("SHOW tables from $this->dbname");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}