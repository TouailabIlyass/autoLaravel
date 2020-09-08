<?php
include_once 'DAO.php';

class GestionForm{

    private $dao = NULL;
    private $filePath = NULL;
    
    public function __construct($dbname, $filePath)
    {
        $this->dao = new DAO($dbname);
        $this->filePath = $filePath;
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
        $tableHead = '';
        $tableBody = '';
        $primaryKey = '';
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
        ////generate table for showing data
        $tableHead.= "<th>{$field['Field']}</th>\n\t";
        $tableBody .= "<td>{{\$$modelname->{$field['Field']}}}</td>\n\t";
        if ($field['Key']=='PRI') {
            $primaryKey = $field['Field'];
            }
        }
        $form.="\t@csrf";
        if(!is_dir("$this->filePath./resources/views/$table"))
            mkdir("$this->filePath./resources/views/$table");
        $f=fopen("$this->filePath/resources/views/$table/form".ucfirst($modelname).'.blade.php','w+');
        fputs($f,$form);
        fclose($f);
        /////writing table
        $form = "
        <a href=\"{{url('$table/create')}}\" >Add</a>
        <table>
            <thead>
            $tableHead
            <th>Action</th>
            </thead>
            <tbody>
            @foreach(\$$table as \$$modelname)
                <tr>
                $tableBody
                <td>
                <form action=\"{{url('clients/'.\${$modelname}->{$primaryKey})}}\" method=\"POST\">
                    <a href=\"\">Update</a>
                    {{method_field('delete')}}
                @csrf
                <button type=\"submit\">Delete</button>
                </form>
            </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        ";
        $f=fopen("$this->filePath/resources/views/$table/index.blade.php",'w+');
        fputs($f,$form);
        fclose($f);
        ////////// writing create file
        $form = "
        <div>
        <form action=\"{{url('$table')}}\" method=\"POST\">
            @include('$table.formClient')
            <button type=\"submit\" >Add</button>
        </form>
        </div>
";
        $f=fopen("$this->filePath/resources/views/$table/create.blade.php",'w+');
        fputs($f,$form);
        fclose($f);
    }

}