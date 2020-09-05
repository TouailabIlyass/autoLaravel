<?php

class GestionController {

    private $pluralTableName;
    private $tableAttributes = array();
    private $filePath;

    public function __construct($tn, $ta, $fp) {
        $this->pluralTableName = $tn;
        $this->tableAttributes = $ta;
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
        array_push($routes_arr, "Route::get('/".$pluralTableName."', '".$singularUcfTableName."Controller@index');");
        array_push($routes_arr, "Route::get('/".$pluralTableName."/create', '".$singularUcfTableName."Controller@create');");
        array_push($routes_arr, "Route::post('/".$pluralTableName."', '".$singularUcfTableName."Controller@store');");
        array_push($routes_arr, "Route::get('/".$pluralTableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@show');");
        array_push($routes_arr, "Route::get('/".$pluralTableName."/{".$singularTableName."}/edit', '".$singularUcfTableName."Controller@edit');");
        array_push($routes_arr, "Route::patch('/".$pluralTableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@update');");
        array_push($routes_arr, "Route::delete('/".$pluralTableName."/{".$singularTableName."}', '".$singularUcfTableName."Controller@destroy');");

        /*test print*/
        foreach($routes_arr as $route){
            echo ''.$route;
            echo '<br>';
        }
        
        return $routes_arr;
    }

    function createRouteFile()
    {
        //opening the route file name in Laravel -> 'web.php'
        $file = fopen($this->filePath."web.php", "a");
        //Creating an array of lines to append in the file
        $lines = $this->makeRoutes($this->pluralTableName);
        //lopping on the file and appending
        foreach($lines as $line){
            fwrite($file, $line."\n");
        }
        //closing the file
        fclose($file);
    }

    function makingValidationFunctionString()
    {
        $str = "public function validateData()
            {
            \treturn request()->validate(["."\n";
        
        foreach($this->tableAttributes as $ta)
        {
            $str .= "\t\t\t\t'$ta'"."=> 'required',\n";
        }

        $str .= "\t\t\t]);\n\t\t}";

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
        $validationDataString = $this->makingValidationFunctionString($this->tableAttributes);
        
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