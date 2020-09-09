<?php

class GestionController {

    private $dbname;
    private $filePath;

    //Class Constructor
    public function __construct($dbn, $fp) {
        $this->dbname = $dbn;
        $this->filePath = $fp;
    }

    /*
    Input: take a table name as input
    Role: making different tableName string forms => 'Table', 'table'
    Output: return an array of string as 'Table', 'table'
    */
    function makeTableNameStrings($tableName)
    {
        //Removing the 's' from the table name to make it singular => 'table'
        $singularTableName = substr($tableName, 0, strlen($tableName)-1);
        //Making the first letter Upper Case => 'Table'
        $singularUcfTableName = ucfirst($singularTableName);

        //creating an array
        $arr = [];
        //appending the vars
        $arr[0] = $singularTableName;
        $arr[1] = $singularUcfTableName;

        return $arr;
    }

    /*
    Input: take a ble name as input
    Role: Making CRUD routes related to the view and REST API CRUD
    Output: an array of routes
    */
    function makeRoutes($tableName)
    {
        $arr = $this->makeTableNameStrings($tableName);
        $singularTableName = $arr[0];
        $singularUcfTableName = $arr[1];

        //Making an array containing all the possible route for A CRUD APP
        $routes_arr = array();
        //appending Routes to the array
        array_push($routes_arr, "Route::get('/".$tableName."', '".$singularUcfTableName."Controller@index');");
        array_push($routes_arr, "Route::get('/".$tableName."/create', '".$singularUcfTableName."Controller@create');");
        array_push($routes_arr, "Route::post('/".$tableName."', '".$singularUcfTableName."Controller@store');");
        array_push($routes_arr, "Route::get('/".$tableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@show');");
        array_push($routes_arr, "Route::get('/".$tableName."/{".$singularTableName."}/edit', '".$singularUcfTableName."Controller@edit');");
        array_push($routes_arr, "Route::patch('/".$tableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@update');");
        array_push($routes_arr, "Route::delete('/".$tableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@destroy');");

        array_push($routes_arr, "\t//----------------Rest for $tableName Table-----------------------");
        array_push($routes_arr, "Route::get('/api/".$tableName."/limit/{limit?}', '".$singularUcfTableName."Controller@restIndex');");
        array_push($routes_arr, "Route::get('/api/".$tableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@restShow');");
        array_push($routes_arr, "Route::post('/api/".$tableName."', '".$singularUcfTableName."Controller@restStore');");
        array_push($routes_arr, "Route::patch('/api/".$tableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@restUpdate');");
        array_push($routes_arr, "Route::delete('/api/".$tableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@restDestroy');");
        
        return $routes_arr;
    }

    /*
    Input: take a table name as input
    Role: genrate the routes in the web.php file
    Output: None
    */
    function createRouteFile($tableName)
    {
        //opening the route file name in Laravel -> 'web.php'
        $file = fopen($this->filePath."\\routes\web.php", "a");
        //Creating an array of lines to append in the file
        $lines = $this->makeRoutes($tableName);
        //lopping on the file and appending
        $str =  "\n\n\n//-------------------$tableName Routes---------------\n";
        foreach($lines as $line){
            $str .= "$line\n";
        }
        $str .=  "//-------------------End $tableName Routes------------";
        fwrite($file, $str);
        //closing the file
        fclose($file);
    }

    /*
    Input: take a table name as input
    Role: generate the validation Function using conditions (required|max|...)
    Output: return a validation Function as String
    */
    function generateValidation($tableName)
    {   
        //including dao module
        include_once('../gestionModel/DAO.php');
        //instantiation from the module
        $dao_obj = new DAO($this->dbname);
        //getting table infos => cols = ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra']
        //Notice: table name should be plural
        $data = $dao_obj->getTableInfos($tableName);
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

    /*
    Input: take a table name as input
    Role: Create Controllers in the specefic laravel folders
    Output: None
    */
    function createController($tableName)
    {
        //getting the AllString 'table', 'Table'
        $arr = $this->makeTableNameStrings($tableName);
        $singularTableName = $arr[0];
        $singularUcfTableName = $arr[1];
        
        //geting the validation string for the table attributes
        //to add them to controlers functions
        $validationDataString = $this->generateValidation($tableName);
        
        //file content
        $fileContent = "<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\\$singularUcfTableName;

class $singularUcfTableName"."Controller extends Controller
{
    public function index()
    {
        \$$tableName = $singularUcfTableName::all();

        return view('$tableName.index', compact('$tableName'));
    }

    public function create()
    {
        \$$singularTableName = new $singularUcfTableName();
        return view('$tableName.create', compact('$singularTableName'));
    }

    public function store()
    {
        \$$tableName = $singularUcfTableName::create(".'$this->validateData()'.");
        return redirect('/$tableName/'.\$$tableName"."->id);
    }

    //Route Model Binding => \App\Customer \$var
    public function show($singularUcfTableName \$$singularTableName)
    {
        return view('$tableName.show', compact('$singularTableName'));
    }

    public function edit($singularUcfTableName \$$singularTableName)
    {
        return view('$tableName.edit', compact('$singularTableName'));
    }

    public function update($singularUcfTableName \$$singularTableName)
    {
        \$$singularTableName"."->update(".'$this->validateData()'.");    

        return redirect('/$tableName/'.\$$tableName"."->id);
    }

    public function destroy($singularUcfTableName \$$singularTableName)
    {
        \$$singularTableName"."->delete();

        return redirect('/$tableName');
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
        $file = fopen($this->filePath."/app/Http/Controllers/".$singularUcfTableName."Controller.php", "w");
        //writing the content the content
        fwrite($file, $fileContent);
        //closing the file
        fclose($file);

    }


//end of Class
}

?>