<?php

include_once 'DAO.php';

class GestionModel{
    
    //Class Vars
    private $dao = NULL;
    private $filePath = NULL;
    
    //Class Constructor
    public function __construct($dbname, $fp)
    {
        $this->dao = new DAO($dbname);
        $this->filePath = $fp;
    }

    /*
    Input: take a tablename as input
    Role: genrate the model file for the table name
    Output: None
    */
    public  function createModel($table)
    {
            
        $modelname = ucfirst(substr($table,0,strlen($table)-1));
        $columns=$this->dao->getTableInfos($table);

        $attr='protected $fillable = ['."\n\t";
        $pr='';
        foreach($columns as $columnName)
        {
            $attr.="'{$columnName['Field']}',\n\t";
            if ($columnName['Key']=='PRI') {
                $pr.='protected $primaryKey = \''.$columnName['Field'].'\';';
                    if (strpos($columnName['Type'], 'varchar') !== false) {
                        $pr.="\npublic \$incrementing = false;";
                    }
                }
        }
        $attr.="];";
    
  
    $model="<?php\n\n namespace App;\n\nuse Illuminate\Database\Eloquent\Model;\n

class $modelname extends Model {\n\t
    $pr
    $attr
}";

    $f=fopen($this->filePath.'/app/'.$modelname.'.php','w+');
    fputs($f,$model);
    fclose($f);
    }

}