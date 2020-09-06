<?php

class GestionController {

    private $pluralTableName;
    private $dbname;
    private $filePath;

    public function __construct($tn, $dbn, $fp) {
        $this->pluralTableName = $tn;
        $this->dbname = $dbn;
        $this->filePath = $fp;
    }

    function makeTableNameStrings()
    {
        //Removing the 's' from the table name to make it singular => 'table'
        $singularTableName = substr($this->pluralTableName, 0, strlen($this->pluralTableName)-1);
        //Making the first letter Upper Case => 'Table'
        $singularUcfTableName = ucfirst($singularTableName);

        //creating an array
        $arr = [];
        //appending the vars
        $arr[0] = $singularTableName;
        $arr[1] = $singularUcfTableName;

        return $arr;
    }

    function makeRoutes()
    {
        $arr = $this->makeTableNameStrings($this->pluralTableName);
        $singularTableName = $arr[0];
        $singularUcfTableName = $arr[1];

        //Making an array containing all the possible route for A CRUD APP
        $routes_arr = array();
        //appending Routes to the array
        array_push($routes_arr, "Route::get('/".$this->pluralTableName."', '".$singularUcfTableName."Controller@index');");
        array_push($routes_arr, "Route::get('/".$this->pluralTableName."/create', '".$singularUcfTableName."Controller@create');");
        array_push($routes_arr, "Route::post('/".$this->pluralTableName."', '".$singularUcfTableName."Controller@store');");
        array_push($routes_arr, "Route::get('/".$this->pluralTableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@show');");
        array_push($routes_arr, "Route::get('/".$this->pluralTableName."/{".$singularTableName."}/edit', '".$singularUcfTableName."Controller@edit');");
        array_push($routes_arr, "Route::patch('/".$this->pluralTableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@update');");
        array_push($routes_arr, "Route::delete('/".$this->pluralTableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@destroy');");

        array_push($routes_arr, "Route::get('/api/".$this->pluralTableName."/limit/{limit?}', '".$singularUcfTableName."Controller@restIndex');");
        array_push($routes_arr, "Route::get('/api/".$this->pluralTableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@restShow');");
        array_push($routes_arr, "Route::post('/api/".$this->pluralTableName."', '".$singularUcfTableName."Controller@restStore');");
        array_push($routes_arr, "Route::patch('/api/".$this->pluralTableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@restUpdate');");
        array_push($routes_arr, "Route::delete('/api/".$this->pluralTableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@restDestroy');");
        
        return $routes_arr;
    }

    function createRouteFile()
    {
        //opening the route file name in Laravel -> 'web.php'
        $file = fopen($this->filePath."web.php", "w");
        //Creating an array of lines to append in the file
        $lines = $this->makeRoutes($this->pluralTableName);
        //lopping on the file and appending
        fwrite($file, "<?php\n");
        foreach($lines as $line){
            fwrite($file, $line."\n");
        }
        //closing the file
        fclose($file);
    }

    function generateValidation()
    {   
        //including dao module
        include_once('../gestionModel/DAO.php');
        //instantiation from the module
        $dao_obj = new DAO($this->dbname);
        //getting table infos => cols = ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra']
        //Notice: table name should be plural
        $data = $dao_obj->getTableInfos($this->pluralTableName);
        //validation function string header
        $str = "public function validateData()
    {
        return request()->validate([";
        //looping on data table
        foreach($data as $row)
        {
            // concat : example 'name' => '
            $str .= "\n\t\t\t'{$row['Field']}' => '";
            //testing if the attribut is AUTO_INCREMENT
            if(strtolower($row['Extra']) == 'auto_increment')
            {
                //concat: example ,
                $str .= "',\n";
                continue;
            }
            //testing if the attribut is Nullable or not
            if(strtolower($row['Null']) == 'no'){
                //concat
                $str .= "required|";
            }
            //taking the max lenght from the type 
            //example => VARCHAR(100) -> 100
            if (strpos(strtolower($row['Type']), 'varchar') !== false) {
                $split_lenght = explode("(", $row['Type']);
                $split_lenght = explode(")",$split_lenght[1]);
                $split_lenght = $split_lenght[0];
                //concat example : max
                $str .= "max:$split_lenght|";
            }
            //concat: example ,
            $str .= "',\n";
        }

        $str .= "\t\t]);\n }";

        return $str;
    }

    function createController()
    {
        //getting the AllString 'table', 'Table'
        $arr = $this->makeTableNameStrings($this->pluralTableName);
        $singularTableName = $arr[0];
        $singularUcfTableName = $arr[1];
        
        //geting the validation string for the table attributes
        //to add them to controlers functions
        $validationDataString = $this->generateValidation();
        
        //file content
        $fileContent = "<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\\$singularUcfTableName;

class $singularUcfTableName"."Controller extends Controller
{
    public function index()
    {
        \$$this->pluralTableName = $singularUcfTableName::all();

        return view('$this->pluralTableName.index', compact('$this->pluralTableName'));
    }

    public function create()
    {
        return view('$this->pluralTableName.create');
    }

    public function store()
    {
        \$$this->pluralTableName = $singularUcfTableName::create(".'$this->validateData()'.");

        return redirect('/$this->pluralTableName/'.\$$this->pluralTableName"."->id);
    }

    //Route Model Binding => \App\Customer \$var
    public function show($singularUcfTableName \$$singularTableName)
    {
        return view('$this->pluralTableName.show', compact('$singularTableName'));
    }

    public function edit($singularUcfTableName \$$singularTableName)
    {
        return view('$this->pluralTableName.edit', compact('$singularTableName'));
    }

    public function update($singularUcfTableName \$$singularTableName)
    {
        \$$singularTableName"."->update(".'$this->validateData()'.");    

        return redirect('/$this->pluralTableName/'.\$$this->pluralTableName"."->id);
    }

    public function destroy($singularUcfTableName \$$singularTableName)
    {
        \$$singularTableName"."->delete();

        return redirect('/$this->pluralTableName');
    }


    ".$validationDataString."


    //----------------------------------------Rest Controllers----------------------
    
    public function restIndex(\$limit = 0)
    {
        return $singularUcfTableName::limit(99)->offset(\$limit)->get();
    }

    public function restStore()
    {
        return $singularUcfTableName::create(".'$this->validateData()'.");
    }

    //Route Model Binding => \App\Customer \$var
    public function restShow($singularUcfTableName \$$singularTableName)
    {
        return \$$singularTableName;
    }

    
    public function restUpdate($singularUcfTableName \$$singularTableName)
    {
        return \$$singularTableName"."->update(".'$this->validateData()'.");
    }

    public function RestDestroy($singularUcfTableName \$$singularTableName)
    {
        return \$$singularTableName"."->delete();
    }
}
        ";

        //opening the route file name in Laravel -> 'web.php'
        $file = fopen($this->filePath.$singularUcfTableName."Controller.php", "w");
        //writing the content the content
        fwrite($file, $fileContent);
        //closing the file
        fclose($file);

    }


//end of Class
}

?>