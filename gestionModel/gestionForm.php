<?php
include_once 'DAO.php';

class GestionForm{

    //Class Vars
    private $dao = NULL;
    private $filePath = NULL;
    
    //Class Constructor
    public function __construct($dbname, $filePath)
    {
        $this->dao = new DAO($dbname);
        $this->filePath = $filePath;
    }

    /*
    Input: take a fieldname (table attribute) as input
    Role: create a label for the input 
    Output: return the label as string
    */
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

    /*
    Input: take a fieldname (table attribute) as input
    Role: Create a input type regarding the attribut type like Email , phone, text, password
    Output: return the input type as string
    */
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

    /*
    Input: take a tablename as input
    Role: Create a form for the table
    Output: None
    */
    public function createForm($table)
    {
        $form = '';
        $tableHead = '';
        $tableBody = '';
        $primaryKey = '';
        $modelname =  substr($table,0,strlen($table)-1);
        $columns=$this->dao->getTableInfos($table);
        foreach ($columns as $field) {
            if ($field['Key']=='PRI') {
                $primaryKey = $field['Field'];
                continue;
                }
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


    public function createFormWithVueJS($table)
    {
        $form = '<template>
        <form class="form-horizontal" v-on:submit.prevent="onSubmit" id="form">
             <fieldset>		
                 <input type="hidden" name="_method" value="put" v-if="editMethod">	';

        $tableHead = '';
        $tableBody = '';
        $primaryKey = '';
        $modelname =  substr($table,0,strlen($table)-1);
        $columns=$this->dao->getTableInfos($table);
        $formObject = '';
        foreach ($columns as $field) {
            if (strtolower($field['Key']) == 'pri' )
                $primaryKey = $field['Field'];
            if (strtolower($field['Extra']) == 'auto_increment') continue;
            $formObject.="\n\t\t\t\t{$field['Field']} : '',";
            $form.="\n\n".'<div class="form-group row">
            <label for="'.$field['Field'].'" class="col-sm-2 col-form-label">'.$this->createFieldName($field['Field']).'</label>
            <div class="col-sm-10">
            <input type="'.$this->createInputType($field['Field']).'"  class="form-control" id="'.$field['Field'].'" name="'.$field['Field'].'" v-model="'.$modelname.'.'.$field['Field'].'" >
            <p style="color:red;" v-if="errors" >{{errors.'.$field['Field'].'}}</p>
            </div>
        </div>
        ';
        ////generate table for showing data
        $tableHead.= "\t\t\t\t\t\t\t\t\t<th scope='col'>{$field['Field']}</th>\n\t";
        $tableBody .= "\t\t\t\t\t\t\t\t\t<td>{{{$modelname}.{$field['Field']}}}</td>\n\t";
        
        }
        $form.="\t
        <input type='hidden' name='_token' :value='token' />

        <button class='btn btn-primary'  @click='add".ucfirst($modelname)."()' name='save'>Save</button>

        </fieldset>
  
    </form>
</template>


<script>
    export default {
    props:[
        'editMethod',
        'old".ucfirst($modelname)."'
    ],
    mounted() {
        console.log('{$modelname}Form mounted.');
        if(this.editMethod)
            this.client = this.oldClient;
        },

    data()
    {
        return {
            token: $('meta[name=\"csrf-token\"]').attr('content'),
            $modelname : {
                $formObject
            },
            errors: '',
            }
        },
        created()
        {
        
        },
    methods: {
        add".ucfirst($modelname)."()
        {   
            var url = 'http://localhost:8000/api/{$table}/';
            var type = 'post';
            if(this.editMethod) {url+=this.{$modelname}.{$primaryKey};type='put'}
            $.ajax({
                url: url,
                type: type,
                datatype: 'json',
                data : $(\"#form\").serialize(),
                success: function(data){
                    console.log(data);
                },
                error: function (request, status, error) {
                    console.log(request);
                    console.log(error);
                    this.errors = request.responseJSON.errors;
                }.bind(this)

               })
        },
        onSubmit(){}
        },
       
        
       
    }
</script>
        ";
        if(!is_dir("$this->filePath./resources/js/components/$table"))
            mkdir("$this->filePath./resources/js/components/$table");
        $f=fopen("$this->filePath/resources/js/components/$table/".ucfirst($modelname).'FormComponent.vue','w+');
        fputs($f,$form);
        fclose($f);
        /////writing table
        
        $form = "<template>
    <div class=\"col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main\" >
        <div class=\"row\"  v-if=\"!editMethod\" >".'
        <div class="col-sm-10">
        <div class="panel panel-default">
            <!-- <div class="panel panel-default hidden"> -->
            <div class="panel-body">
                <form class="form-horizontal" action="" method="post">
                    <fieldset>
                        <!-- Name input-->
                        <div class="form-group">
                            <div class="col-md-4">
                                <input id="name" name="name" type="text" placeholder="Nom/Prenom/Email/Id" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-lg">Recherch</button>
                            </div>
                            <div class="col-md-4 widget-right">
                                <button  class="btn btn-success btn-lg pull-right"  data-toggle="modal" data-target="#CreateNewProduct">
                                <em class="fa fa-plus"></em> Cree Un Client</button>
                            </div>
                        </div>
                        
                    </fieldset>
                </form>
            </div>
        </div>
    </div>

'.'
<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="panel-body tabs">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#tab1" data-toggle="tab">Clients Actif</a></li>
							<li><a href="#tab2" data-toggle="tab">Clients Inactif</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane fade in active" id="tab1">
								<table class="table table-striped">
								  <thead>
								    <tr>
								      '.$tableHead.'
								    </tr>
								  </thead>
								  <tbody>
                                    <tr v-for="'.$modelname.' in '.$table.'" :key="'.$modelname.'.'.$primaryKey.'" >
										'.$tableBody.'
										<td><button  class="btn btn-primary" @click="select'.ucfirst($modelname).'('.$modelname.')"><em class="fa fa-file-o"></em></button></td>
                                    </tr>
                                   
								</tbody>
								</table>
							</div>
						</div>
				</div><!--/.panel-->
			</div><!--/.col-->
		</div>

        <div class="row">
        <!-- Modal -->
        <div class="modal fade" id="CreateNewProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                    <center>
                        <h3 class="modal-title align-middle" id="exampleModalLongTitle">Créer un Client</h3>
                    </center>
              </div>
              <div class="modal-body">

                <'.$modelname.'Form-component></'.$modelname.'Form-component>	

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
    </div>



'."        </div>
<{$modelname}Edit-component  :{$modelname}=\"{$modelname}\" :editMethod=\"editMethod\" @toggleEdit=\"editMethod = !editMethod\" v-if=\"editMethod\" ></{$modelname}Edit-component>

    </div>
</template>

<script>
    export default {
		props: [
			
		],
        mounted() {
            console.log('$modelname Component mounted.')
        },
        data()
        {
            return {
				$table : [],
				$modelname: '',
				editMethod: false,
                errors: '',
            }
        }
        ,
        created()
        {
            axios.get('http://localhost:8000/api/$table'
            ).then(response => 
                this.$table = response.data
            ).catch(error => this.errors = error)
		}
		,
		methods:{
			selectClient($modelname){
				this.editMethod = !this.editMethod;
				this.$modelname = $modelname;
			}
		}
    }
   
</script>
";
        $f=fopen("$this->filePath/resources/js/components/$table/".ucfirst($modelname)."Component.vue",'w+');
        fputs($f,$form);
        fclose($f);
        ////////// writing create file
        
        $form = '<template>

        <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Details '.ucfirst($modelname).'
                </div>
                <div class="panel-body">
                     <!-- Name input-->
                 '."<{$modelname}Form-component :old".ucfirst($modelname)."=\"{$modelname}\" :editMethod=\"editMethod\"></{$modelname}Form-component>
                ".'</div>
            </div>
        </div>
        <div class="col-sm-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                    Editer Produit 
                    <button class="btn btn-md btn-link pull-right" @click="changeValue()" ><em class="fa fa-arrow-left"></em> Retour</button>
            </div>
            <div class="panel-body">
                <div class="row">
        <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
            <div class="panel panel-teal panel-widget border-right">
                <div class="row no-padding"><em class="fa fa-xl fa-shopping-cart color-blue"></em>
                    <h3>CA</h3>
                    <div class="text-muted">CA</div>
                    
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
            <div class="panel panel-blue panel-widget border-right">
                <div class="row no-padding"><em class="fa fa-xl fa-comments color-orange"></em>
                    <h3>Ventes</h3>
                    <div class="text-muted">sales</div>
                    
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
            <div class="panel panel-orange panel-widget border-right">
                <div class="row no-padding"><em class="fa fa-xl fa-users color-teal"></em>
                    <h3 >Fidélité</h3>
                    <div class="text-muted">sales</div>
                    
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-md-3 col-lg-3 no-padding">
            <div class="panel panel-red panel-widget ">
                <div class="row no-padding"><em class="fa fa-xl fa-search color-red"></em>
                    <h3>Dernier achat</h3>
                    <div class="text-muted">lastSale</div>
                    
                </div>
            </div>
        </div>
    </div><!--/.row-->
    <hr>
            </div>
        </div>
    </div>
</div>
</template>
'."
<script>
    export default {
        props: [
            'editMethod',
            '{$modelname}'
        ],
        mounted() {
            console.log('{$modelname} edit Component mounted.')
        },
        methods: {
            changeValue(){
                this.\$emit('toggleEdit');
            },
        }
    }
</script>


      
";
        $f=fopen("$this->filePath/resources/js/components/$table/".ucfirst($modelname).'EditComponent.vue','w+');
        fputs($f,$form);
        fclose($f);
        
    }

}