<?php
class Tmenus extends Controller {
	var $mModulo='TMENUS';
	var $titp='Menu de Modulos';
	var $tits='Menu de Modulos';
	var $url ='supervisor/tmenus/';

	function Tmenus(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = '';

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$WestPanel = '';

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'));

		//$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['WestSize']    = 1;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('TMENUS', 'JQ');
		$param['otros']       = $this->datasis->otros('TMENUS', 'JQ');
		$param['tema1']       = 'darkness';
		$param['anexos']      = 'anexos1';
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "true";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'true',
			'hidden'        => 'true',
			'align'         => "'center'",
			'edittype'      => "'text'",
			'width'         => 60,
			'key'           => 'true'
		));


		$grid->addField('modulo');
		$grid->label('Modulo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 20 }',
		));


		$grid->addField('secu');
		$grid->label('Secuencia');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('titulo');
		$grid->label('Titulo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 160,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 20 }',
		));


		$grid->addField('mensaje');
		$grid->label('Mensaje');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 60 }',
		));

		$grid->addField('ejecutar');
		$grid->label('UI Texto Datasis');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 80 }',
		));

		$grid->addField('wejecutar');
		$grid->label('UI Windows');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:30, maxlength: 120 }',
		));

		$grid->addField('proteo');
		$grid->label('UI ProteoERP');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:30, maxlength: 120 }',
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('390');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('tmenus');

		$response   = $grid->getData('tmenus', array(array()), array(), false, $mWHERE, 'modulo, secu' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData()
	{
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('tmenus', $data);
				echo "Registro Agregado";
				logusu('TMENUS',"Registro Cod:".$data['codigo']." Mod:".$data['modulo']." INCLUIDO");
			} else
			echo "Fallo Inclusion!!!";

		} elseif($oper == 'edit') {
			$codigo     = $this->input->post('codigo');
			unset($data['codigo']);
			$this->db->where('codigo', $codigo);
			$this->db->update('tmenus', $data);
			logusu('TMENUS',"Registro Cod:".$codigo." Mod:".$data['modulo']." MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tmenus WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				//$this->db->simple_query("DELETE FROM tmenus WHERE id=$id ");
				logusu('TMENUS',"Registro ????? ELIMINADO");
				echo "Registro No Eliminado";
			}
		};
	}


/*
class tmenus extends Controller { 
	function tmenus(){
		parent::Controller(); 
		$this->load->library("rapyd");
		//$this->datasis->modulo_id('91B',1);
	}

	function index(){
		redirect("supervisor/tmenus/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$filter = new DataFilter("Filtro de Menu de Datasis","tmenus");

		$filter->modulo = new inputField("Modulo", "modulo");
		$filter->modulo->db_name='modulo';
		$filter->modulo->size=20;

		$filter->titulo = new inputField("Titulo","titulo");
		$filter->titulo->size=30;
		$filter->titulo->db_name='titulo';

		$filter->ejecutar = new inputField("Ejecutar","ejecutar");
		$filter->ejecutar->size=20;
		$filter->ejecutar->db_name='ejecutar';

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('supervisor/tmenus/dataedit/show/<#codigo#>','<#modulo#>');
		$export = anchor('supervisor/tmenus/xmlexport','Exportar Data');
		$import = anchor_popup('cargasarch/cargaxml','Importar Data',$atts);

		$grid = new DataGrid("Lista de Menu de Datasis");
		$grid->order_by("modulo","asc");
		$grid->per_page = 15;

		$grid->column("Modulo",$uri);
		$grid->column("Titulo","titulo" );
		$grid->column("Ejecutar","ejecutar" );

		$grid->add("supervisor/tmenus/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$export.'  ---->  '.$import.'<form>'.$grid->output.'</form>';
		$data['title']   = "<h1>Menu del Sistema</h1>";
		$data['head']    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function DataEdit(){
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Agregar Menu", "tmenus");
		$edit->back_url = site_url("supervisor/tmenus/filteredgrid");

		$edit->modulo = new inputField("Modulo","modulo");
		$edit->modulo->size=12;
		$edit->modulo->maxlength=10;

		$edit->secu = new inputField("Secuencia","secu");
		$edit->secu->size=6;
		$edit->secu->maxlength=5;

		$edit->titulo = new inputField("Titulo","titulo");
		$edit->titulo->size=25;
		$edit->titulo->maxlength=20;

		$edit->mensaje = new textareaField("Mensaje","mensaje");
		$edit->mensaje->rows = 4;
		$edit->mensaje->cols=90;

		$edit->ejecutar = new inputField("Ejecutar","ejecutar");
		$edit->ejecutar->size=80;
		$edit->ejecutar->maxlength=80; 

		$edit->buttons("modify", "save", "undo","back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Menu de Datasis</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data); 
	}

	function xmlexport(){
		$this->load->helper('download');

		$this->load->library("xmlinex");
		$data[]=array('table'  =>'tmenus');
		$data=$this->xmlinex->export($data);
		$name = 'tmenus.xml';
		force_download($name, $data); 
	}
}
*/


	//***************************
	//Layout en la Ventana
	//
	//***************************
	function accjqdatag(){

		$grid = $this->defgrid();
		$param['grid'] = $grid->deploy();

		$bodyscript = '
<script type="text/javascript">
$(function() {
	$( "input:submit, a, button", ".otros" ).button();
});

jQuery("#a1").click( function(){
	var id = jQuery("#newapi'. $param['grid']['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
	if (id)	{
		var ret = jQuery("#newapi'. $param['grid']['gridname'].'").jqGrid(\'getRowData\',id);
		window.open(\'/proteoerp/formatos/ver/VIEW_TMENUS/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
	} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
});
</script>
';

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$WestPanel = '
<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
<div class="anexos">

<table id="west-grid" align="center">
	<tr>
		<td><div class="tema1"><table id="listados"></table></div></td>
	</tr>
	<tr>
		<td><div class="tema1"><table id="otros"></table></div></td>
	</tr>
</table>

<table id="west-grid" align="center">
	<tr>
		<td></td>
	</tr>
</table>
</div>
'.
//		<td><a style="width:190px" href="#" id="a1">Imprimir Copia</a></td>
'</div> <!-- #LeftPane -->
';

		$SouthPanel = '
<div id="BottomPane" class="ui-layout-south ui-widget ui-widget-content">
<p>'.$this->datasis->traevalor('TITULO1').'</p>
</div> <!-- #BottomPanel -->
';
		$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados'] = $this->datasis->listados('VIEW_TMENUS', 'JQ');
		$param['otros']    = $this->datasis->otros('VIEW_TMENUS', 'JQ');
		$param['tema1']     = 'darkness';
		$param['anexos']    = 'anexos1';
		$param['bodyscript'] = $bodyscript;
		$param['tabs'] = false;
		$param['encabeza'] = $this->titp;
		$this->load->view('jqgrid/crud',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function accdefgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('modulo');
		$grid->label('Modulo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 10 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 60 }',
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function accgetdata()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('view_tmenus');

		$response   = $grid->getData('view_tmenus', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function accsetData()
	{
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				//$this->db->insert('view_tmenus', $data);
				echo "Registro Agregado";

				logusu('VIEW_TMENUS',"Registro ????? INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('view_tmenus', $data);
			logusu('VIEW_TMENUS',"Registro ????? MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM view_tmenus WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				//$this->db->simple_query("DELETE FROM view_tmenus WHERE id=$id ");
				//logusu('VIEW_TMENUS',"Registro ????? ELIMINADO");
				//echo "Registro Eliminado";
			}
		};
	}
	
	function instalar(){
		// Revisar los ejecutar
		$mSQL = "UPDATE intramenu SET ejecutar=MID(ejecutar,2,200) WHERE MID(ejecutar,1,1)='/' ";
		$this->db->simple_query($mSQL);

		// Crea la opcion en el menu
		if ($this->datasis->dameval('SELECT COUNT(*) FROM intramenu WHERE ejecutar="supervisor/tmenus"')==0 ) {
			//Trae el Siguiente Modulo
			$ultimo = $this->datasis->prox_imenu('9');
			$data = array(
				      'modulo'    => $ultimo,
				      'mensaje'   => 'Menus de DataSIS',
				      'titulo'    => 'Menu de DataSIS',
				      'panel'     => 'CONFIGURACION',
				      'ejecutar'  => 'supervisor/tmenus',
				      'target'    => 'popu',
				      'visible'   => 'S',
				      'pertenece' => '9',
				      'ancho'     => '800',
				      'alto'      => '600'
			);
			$this->db->insert('tmenus', $data);
			echo "Creado $ultimo";
		}
		redirect($this->url.'jqdatag');
	}
	
}
?>
