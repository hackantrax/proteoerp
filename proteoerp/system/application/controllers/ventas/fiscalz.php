<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Fiscalz extends Controller {
	var $mModulo = 'FISCALZ';
	var $titp    = 'Cierre Z';
	var $tits    = 'Cierre Z';
	var $url     = 'ventas/fiscalz/';

	function Fiscalz(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'FISCALZ', $ventana=0, $this->titp  );
	}

	function index(){
		$this->instalar();
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	// Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"funcion",   "img"=>"images/engrana.png",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('FISCALZ', 'JQ');
		$param['otros']       = $this->datasis->otros('FISCALZ', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	// Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('fiscalz', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'fiscalz', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'fiscalz', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('fiscalz', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '500', '400' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '500', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '500', '400' );

		$bodyscript .= '});';
		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid o Tabla
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'        => 'true',
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

		$grid->addField('caja');
		$grid->label('Caja');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('serial');
		$grid->label('Serial');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('fecha1');
		$grid->label('Fecha Inicial');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('factura');
		$grid->label('U.Factura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('exento');
		$grid->label('Exento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('base');
		$grid->label('Base Tasa G');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('iva');
		$grid->label('Iva Tasa G');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('base1');
		$grid->label('Base Tasa R');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('iva1');
		$grid->label('Iva Tasa R');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('base2');
		$grid->label('Base Tasa A');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('iva2');
		$grid->label('Iva Tasa A');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('ncexento');
		$grid->label('NC.Exento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('ncbase');
		$grid->label('NC. Base Tasa G');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('nciva');
		$grid->label('NC. Iva Tasa G');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('ncbase1');
		$grid->label('NC.Base Tasa R');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('nciva1');
		$grid->label('NC.Iva Tasa R');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('ncbase2');
		$grid->label('NC.Base Tasa A');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('nciva2');
		$grid->label('NC.Iva Tasa A');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('ncnumero');
		$grid->label('U.NotaCredito');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('FISCALZ','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('FISCALZ','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('FISCALZ','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('FISCALZ','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: fiscalzadd, editfunc: fiscalzedit, delfunc: fiscalzdel, viewfunc: fiscalzshow');

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

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('fiscalz');

		$response   = $grid->getData('fiscalz', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$mcodp  = "??????";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM fiscalz WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('fiscalz', $data);
					echo "Registro Agregado";

					logusu('FISCALZ',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM fiscalz WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM fiscalz WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE fiscalz SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("fiscalz", $data);
				logusu('FISCALZ',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('fiscalz', $data);
				logusu('FISCALZ',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {

		};
	}

	//******************************************************************
	// Edicion
	function dataedit($status='',$id='',$id2=''){
		$this->rapyd->load('dataobject','dataedit');

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
			$("#banco1").change(function () { acuenta(); }).change();
			$("#banco2").change(function () { acuenta(); }).change();
		});';

		$do = new DataObject('fiscalz');
		if(($status=='create') && !empty($id) && !empty($id2)){
			$do->load(array('serial'=> $id,'numero'=> $id2));
			$do->set('numero'  , '');
			$do->set('hora'    , '');
			$do->set('base'    , '');
			$do->set('iva'     , '');
			$do->set('base1'   , '');
			$do->set('iva1'    , '');
			$do->set('iva2'    , '');
			$do->set('base2'   , '');
			$do->set('exento'  , '');
			$do->set('ncexento', '');
			$do->set('ncbase'  , '');
			$do->set('nciva'   , '');
			$do->set('ncbase1' , '');
			$do->set('nciva1'  , '');
			$do->set('ncbase2' , '');
			$do->set('nciva2'  , '');
			$do->set('ncnumero', '');
		}

		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit('',$do);
		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->caja = new inputField('Caja', 'caja');
		$edit->caja->size = 6;
		$edit->caja->maxlength =4;
		$edit->caja->rule='trim|required';

		$edit->serial = new inputField('Serial de la impresora fiscal','serial');
		$edit->serial->size =15;
		$edit->serial->maxlength =12;
		$edit->serial->mode = 'autohide';
		$edit->serial->rule='trim|required';

		$edit->numero = new inputField('N&uacute;mero del cierre Z','numero');
		$edit->numero->size = 6;
		$edit->numero->maxlength =4;
		$edit->numero->mode = 'autohide|required';
		$edit->numero->rule='trim';

		$edit->fecha1 = new DateonlyField('Fecha Inicial','fecha1','d/m/Y');
		$edit->fecha1->insertValue = date('Y-m-d');
		$edit->fecha1->rule='required';
		$edit->fecha1->size = 12;
		$edit->fecha1->calendar=false;

		$edit->fecha = new DateonlyField('Fecha Final','fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule='required';
		$edit->fecha->size = 12;
		$edit->fecha->append('Si el cierre se saco el mismo d&iacute;a de las ventas la fecha final es igual a la fecha inicial');
		$edit->fecha->calendar=false;

		$edit->hora = new inputField('Hora del cierre','hora');
		$edit->hora->size =8;
		$edit->hora->rule='trim|callback_chhora|required';
		$edit->hora->append('hh:mm:ss');
		$edit->hora->rule='trim';

		$edit->factura = new inputField('N&uacute;mero de la &uacute;ltima Factura','factura');
		$edit->factura->size =10;
		$edit->factura->maxlength =8;
		$edit->factura->rule='trim|required';
		$edit->factura->append('ULT.FACTURA');

		$edit->ncnumero = new inputField('N&uacute;mero de la &uacute;ltima Nota de Cr&eacute;dito numero','ncnumero');
		$edit->ncnumero->size =10;
		$edit->ncnumero->maxlength =8;
		$edit->ncnumero->rule='trim|required';
		$edit->ncnumero->append('ULT.NOTA.CREDITO');

		$edit->exento = new inputField('Montos de Facturas exentas','exento');
		$edit->exento->size = 15;
		$edit->exento->css_class='inputnum';
		$edit->exento->maxlength =12;
		$edit->exento->rule="trim";
		$edit->exento->group='Montos exentos';
		$edit->exento->append('EXENTO');

		$edit->ncexento = new inputField("Monto de notas de Cr&eacute;dito exentas","ncexento");
		$edit->ncexento->size = 15;
		$edit->ncexento->css_class='inputnum';
		$edit->ncexento->maxlength =12;
		$edit->ncexento->rule='trim';
		$edit->ncexento->group='Montos exentos';
		$edit->ncexento->append('NC. EXENTO');

		$edit->base = new inputField('Ventas Base imponible','base');
		$edit->base->size = 15;
		$edit->base->css_class='inputnum';
		$edit->base->maxlength =12;
		$edit->base->group='Seg&uacute;n Alicuota General';
		$edit->base->rule='trim';

		$edit->iva = new inputField('Ventas Iva','iva');
		$edit->iva->size = 15;
		$edit->iva->css_class='inputnum';
		$edit->iva->maxlength =12;
		$edit->iva->group='Seg&uacute;n Alicuota General';
		$edit->iva->rule='trim';
		$edit->iva->append('IVA G');

		$edit->base1 = new inputField('Ventas Base imponible','base1');
		$edit->base1->size = 15;
		$edit->base1->css_class='inputnum';
		$edit->base1->maxlength =12;
		$edit->base1->group='Seg&uacute;n Alicuota Reducida';
		$edit->base1->rule='trim';

		$edit->iva1 = new inputField('Ventas Iva','iva1');
		$edit->iva1->size = 15;
		$edit->iva1->css_class='inputnum';
		$edit->iva1->maxlength =12;
		$edit->iva1->group='Seg&uacute;n Alicuota Reducida';
		$edit->iva1->rule='trim';

		$edit->base2 = new inputField('Ventas Base imponible','base2');
		$edit->base2->size = 15;
		$edit->base2->css_class='inputnum';
		$edit->base2->maxlength =12;
		$edit->base2->group='Seg&uacute;n Alicuota Adicional';
		$edit->base2->rule='trim';

		$edit->iva2 = new inputField('Ventas Iva','iva2');
		$edit->iva2->size = 15;
		$edit->iva2->css_class='inputnum';
		$edit->iva2->maxlength =12;
		$edit->iva2->group='Seg&uacute;n Alicuota Adicional';
		$edit->iva2->rule='trim';

		$edit->ncbase = new inputField('Nota de Cr&eacute;dito base imponible','ncbase');
		$edit->ncbase->size = 15;
		$edit->ncbase->css_class='inputnum';
		$edit->ncbase->maxlength =12;
		$edit->ncbase->group='Seg&uacute;n Alicuota General';
		$edit->ncbase->rule='trim';
		$edit->ncbase->append('NC. BI G');

		$edit->ncsiva = new inputField('Nota de Cr&eacute;dito iva','nciva');
		$edit->ncsiva->size = 15;
		$edit->ncsiva->css_class='inputnum';
		$edit->ncsiva->maxlength =12;
		$edit->ncsiva->group='Seg&uacute;n Alicuota General';
		$edit->ncsiva->rule='trim';
		$edit->ncsiva->append('NC. IVA G');

		$edit->ncbase1 = new inputField('Nota de Cr&eacute;dito base imponible','ncbase1');
		$edit->ncbase1->size = 15;
		$edit->ncbase1->css_class='inputnum';
		$edit->ncbase1->maxlength =12;
		$edit->ncbase1->group='Seg&uacute;n Alicuota Reducida';
		$edit->ncbase1->rule='trim';

		$edit->ncsiva1 = new inputField('Nota de Cr&eacute;dito iva','nciva1');
		$edit->ncsiva1->size = 15;
		$edit->ncsiva1->css_class='inputnum';
		$edit->ncsiva1->maxlength =12;
		$edit->ncsiva1->group='Seg&uacute;n Alicuota Reducida';
		$edit->ncsiva1->rule="trim";

		$edit->ncbase2 = new inputField('Nota de Cr&eacute;dito base imponible','ncbase2');
		$edit->ncbase2->size = 15;
		$edit->ncbase2->css_class='inputnum';
		$edit->ncbase2->maxlength =12;
		$edit->ncbase2->group='Seg&uacute;n Alicuota Adicional';
		$edit->ncbase2->rule="trim";

		$edit->ncsiva2 = new inputField('Nota de Cr&eacute;dito iva','nciva2');
		$edit->ncsiva2->size = 15;
		$edit->ncsiva2->css_class='inputnum';
		$edit->ncsiva2->maxlength =12;
		$edit->ncsiva2->group='Seg&uacute;n Alicuota Adicional';
		$edit->ncsiva2->rule='trim';

		$edit->ncsiva1->append('NC. IVA R');
		$edit->ncsiva2->append('NC. IVA A');
		$edit->ncbase1->append('NC. BI R');
		$edit->ncbase2->append('NC. BI A');
		$edit->iva1->append('IVA R');
		$edit->iva2->append('IVA A');
		$edit->base->append('BI G');
		$edit->base1->append('BI R');
		$edit->base2->append('BI A');

		$edit->manual  = new autoUpdateField('manual','S', 'S');

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			echo $edit->output;
		}
	}
	function _post_insert($do){
		$fecha=$do->get('fecha');
		$numero=$do->get('numero');
		logusu('fiscalz',"CIERRE Z ${numero} CREADO, FECHA ${fecha}");
	}

	function _post_update($do){
		$fecha=$do->get('fecha');
		$numero=$do->get('numero');
		logusu('fiscalz',"CIERRE Z ${numero} MODIFICADO, FECHA ${fecha}");
	}

	function _post_delete($do){
		$fecha=$do->get('fecha');
		$numero=$do->get('numero');
		logusu('fiscalz',"CIERRE Z ${numero} ELIMINADO, FECHA ${fecha}");
	}

	function instalar(){
		if (!$this->db->table_exists('fiscalz')) {
			$mSQL="CREATE TABLE `fiscalz` (
				`caja` CHAR(4) NULL DEFAULT NULL,
				`serial` CHAR(12) NOT NULL DEFAULT '',
				`numero` CHAR(4) NOT NULL DEFAULT '',
				`fecha` DATE NULL DEFAULT NULL,
				`factura` CHAR(8) NULL DEFAULT NULL,
				`fecha1` DATE NULL DEFAULT NULL,
				`hora` TIME NULL DEFAULT NULL,
				`exento` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`base` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`iva` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`base1` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`iva1` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`base2` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`iva2` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`ncexento` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`ncbase` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`nciva` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`ncbase1` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`nciva1` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`ncbase2` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`nciva2` DECIMAL(12,2) UNSIGNED NULL DEFAULT NULL,
				`ncnumero` CHAR(8) NULL DEFAULT NULL,
				`manual` CHAR(1) NULL DEFAULT 'N',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `unico` (`serial`, `numero`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('fiscalz');
		if(!in_array('manual',$campos)){
			$mSQL="ALTER TABLE `fiscalz` ADD `manual` CHAR(1)DEFAULT 'N' NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('id',$campos)){
			$mSQL="ALTER TABLE `fiscalz`
			ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `manual`,
			DROP PRIMARY KEY,
			ADD UNIQUE INDEX `unico` (`serial`, `numero`),
			ADD PRIMARY KEY (`id`)";
			$this->db->simple_query($mSQL);
		}
	}

}


/*
require_once(BASEPATH.'application/controllers/validaciones.php');
class fiscalz extends Controller{
	function fiscalz(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		redirect("ventas/fiscalz/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("Filtro de Cierre Z");
		$select=array('serial','hora','manual','numero','caja','fecha','factura','fecha1','(exento+base+iva+base1+iva1+base2+iva2-ncexento-ncbase-nciva-ncbase1-nciva1-ncbase2-nciva2) AS total');
		$filter->db->select($select);
		$filter->db->from('fiscalz');

		$filter->fecha1d = new dateonlyField("Fecha inicial", "fecha1d",'d/m/Y');
		$filter->fecha1d->clause  ='where';
		$filter->fecha1d->size =10;
		$filter->fecha1d->db_name ='fecha1';
		//$filter->fecha1d->insertValue = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30, date("Y")));
		$filter->fecha1d->operator=">=";
		$filter->fecha1d->group='Fechas';

		$filter->fecha1h = new dateonlyField('fhasta','fecha1h','d/m/Y');
		$filter->fecha1h->clause='where';
		$filter->fecha1h->size =10;
		$filter->fecha1h->db_name='fecha1';
		//$filter->fecha1h->insertValue = date("Y-m-d");
		$filter->fecha1h->operator='<=';
		$filter->fecha1h->group='Fechas';
		$filter->fecha1h->in='fecha1d';

		$filter->fechad = new dateonlyField("Fecha final", "fechad",'d/m/Y');
		$filter->fechad->clause  ="where";
		$filter->fechad->db_name ="fecha";
		$filter->fechad->size =10;
		//$filter->fechad->insertValue = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30, date("Y")));
		$filter->fechad->operator=">=";
		$filter->fechad->group='Fechas';

		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechah->clause="where";
		$filter->fechah->size =10;
		$filter->fechah->db_name="fecha";
		//$filter->fechah->insertValue = date("Y-m-d");
		$filter->fechah->operator="<=";
		$filter->fechah->group='Fechas';
		$filter->fechah->in='fechad';

		$filter->serial = new inputField('Serial','serial');
		$filter->serial->size=20;

		$filter->numero= new inputField('Numero','numero');
		$filter->numero->size=20;

		$filter->caja= new inputField('Caja','caja');
		$filter->caja->size=5;

		$filter->manual = new dropdownField("Manual", "manual");
		$filter->manual->option('','Todos');
		$filter->manual->option('N','N');
		$filter->manual->option('S','S');
		$filter->manual->style = 'width:70px';

		$filter->buttons('reset','search');
		$filter->build();

		$uri   = anchor('ventas/fiscalz/dataedit/show/<#serial#>/<#numero#>','<#serial#>');
		$uri_2 = anchor('ventas/fiscalz/dataedit/create/<#serial#>/<#numero#>','Duplicar');
		$uri3  = anchor('reportes/ver/fiscalz','Imprimir');
		$grid  = new DataGrid('Lista de Cierre Z');
		//$grid->order_by("serial","asc");
		$grid->per_page=15;

		$grid->column_orderby('Serial',$uri,'serial');
		$grid->column_orderby('Numero','numero','numero');
		$grid->column_orderby('Caja','caja','caja');
		$grid->column_orderby('Fecha Inicial','<dbdate_to_human><#fecha1#></dbdate_to_human>','fecha' ,'align=\'center\'');
		$grid->column_orderby('Fecha Final'  ,'<dbdate_to_human><#fecha#></dbdate_to_human>' ,'fecha1','align=\'center\'');
		$grid->column_orderby('U. Factura','factura','factura');
		$grid->column('Hora'    ,'hora','align=\'center\'');
		$grid->column('Total'   ,'<b><nformat><#total#></nformat></b>','align=\'right\'');
		$grid->column('Manual','manual' ,'align=\'center\'');
		$grid->column('Duplicar',$uri_2 ,'align=\'center\'');

		$grid->add('ventas/fiscalz/dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$uri3.$grid->output;
		$data['title']   = '<h1>Cierre Z</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
*/
