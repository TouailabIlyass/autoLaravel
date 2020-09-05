<?php

include_once 'connectionDB.php';

class DAO{


    private $pdo = NULL;

    public function __construct($db)
    {
        $this->pdo=ConnectionDB::getConnectionDB($db);
    }

    public function getColumn($table):array
    {
        $stmt=$this->pdo->prepare("select * from $table");
        $stmt->execute();
        
        $ColumnName=array();

        for($i=0;$i<$stmt->columnCount();$i++)
        { if( $stmt->getColumnMeta($i)['name']!='created_at' && $stmt->getColumnMeta($i)['name']!='updated_at' && $stmt->getColumnMeta($i)['name']!='deleted_at')
            $ColumnName[]=$stmt->getColumnMeta($i)['name'];
        }
    return $ColumnName;
    }

    public function getTableInfo($table)
    {
        $stmt=$this->pdo->prepare("show columns from $table");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}