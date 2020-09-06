<?php
include_once 'DAO.php';

$gform = new GestionForm('gestion_location_voitures');
$gform->createForm('clients');
echo 'fin form';

class GestionForm{

    private $dao = NULL;
    
    public function __construct($dbname)
    {
        $this->dao = new DAO($dbname);
    }
    private function createFieldName($fieldname)
    {   $str ='';
        if (!ctype_lower($fieldname) || strpos($fieldname,'_')) {
            $len = strlen($fieldname);
            for($i = 0; $i < $len ; $i++)
            {
                if(($fieldname[$i]>= 'A' && $fieldname[$i]<='Z'))
                {
                    $str.=' ';
                }
                $str.=$fieldname[$i];
            }
            return $str;
        }
        else return $fieldname;
    }
    private function createInputType($fieldname)
    {
        if (strpos($fieldname, 'email') !== false)
            return 'email';
        else if (strpos($fieldname, 'password') !== false)
            return 'password';
        else if (strpos($fieldname, 'age') !== false)
            return 'number';
        else if (strpos($fieldname, 'telephone') !== false)
            return 'number';
        else if (strpos($fieldname, 'date') !== false)
            return 'date';
        return 'text';
    }
    public function createForm($table)
    {
        $form = '';
        $modelname =  substr($table,0,strlen($table)-1); 
        $columns=$this->dao->getTableInfos($table);
        foreach ($columns as $field) {
            $form.='<div class="form-group row">
            <label for="'.$field['Field'].'" class="col-sm-2 col-form-label">'.$this->createFieldName($field['Field']).'</label>
            <div class="col-sm-10">
            <input type="'.$this->createInputType($field['Field']).'"  class="form-control" id="'.$field['Field'].'" name="'.$field['Field'].'"  value="{{old(\''.$field['Field'].'\') ??  $'. $modelname.'->'.$field['Field'].'}}">
            @error(\''.$field['Field'].'\') <p style="color:red;"> {{$message}}</p>@enderror
            </div>
        </div>
        '."\n\n";
        }
        $form.="\t@csrf";
        $f=fopen('form'.ucfirst($modelname).'.blade.php','w+');
        fputs($f,$form);
        fclose($f);
    }

}