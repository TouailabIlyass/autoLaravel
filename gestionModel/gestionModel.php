<?php

include_once 'DAO.php';

$m = new GestionModel('gestion_location_voitures');
$m->createModel('clients');
echo 'fin';

class GestionModel{
    
    private $dao = NULL;
    
    public function __construct($dbname)
    {
        $this->dao = new DAO($dbname);
        
    }

/////////////////
   public  function createModel($table)
    {
            
    $modelname = ucfirst(substr($table,0,strlen($table)-1));
    $columns=$this->dao->getTableInfos($table);

        $attr='protected $fillable = ['."\n\t";
        foreach($columns as $columnName)
        {
            $attr.="'{$columnName['Field']}',\n\t";
        }
        $attr.="];";
    $pr='';
    foreach ($columns as $value) {
        if ($value['Key']=='PRI') {
        $pr.='protected $primaryKey = \''.$value['Field'].'\';';
        }
    }
    $model="<?php\n\n namespace App;\n\nuse Illuminate\Database\Eloquent\Model;\n

class $modelname extends Model {\n\t
    $pr
    $attr
}";

    $f=fopen($modelname.'.php','w+');
    fputs($f,$model);
    fclose($f);
    }
////////////////
}