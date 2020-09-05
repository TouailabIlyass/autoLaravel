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
    $ColumnName=$this->dao->getColumn($table);

        $attr='protected $fillable = ['."\n\t";
        for($i=0;$i<count($ColumnName);$i++)
        {
        $attr.="'$ColumnName[$i]',\n\t";
        }
        $attr.="];";
    $pri=$this->dao->getTableInfo($table);
    $pr='';

    foreach ($pri as $value) {
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