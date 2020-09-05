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
    {   $str='';
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
    public function createForm($table)
    {
        $form = '';
        $modelname =  substr($table,0,strlen($table)-1); 
        $ColumnName=$this->dao->getColumn($table);
        foreach ($ColumnName as $field) {
            $form.='<div class="form-group row">
            <label for="'.$field.'" class="col-sm-2 col-form-label">'.$this->createFieldName($field).'</label>
            <div class="col-sm-10">
            <input type="text"  class="form-control" id="'.$field.'" name="'.$field.'"  value="{{old(\''.$field.'\') ??  $'. $modelname.'->'.$field.'}}">
            @error(\''.$field.'\') <p style="color:red;"> {{$message}}</p>@enderror
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