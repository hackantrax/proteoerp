<?php
class Smov extends Controller {
	var $mModulo='SMOV';
	var $titp='Movimiento de Clientes';
	var $tits='Movimiento de Clientes';
	var $url ='finanzas/smov/';

	function Smov(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_id('502',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('smov','id') ) {
			$this->db->simple_query('ALTER TABLE smov DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE smov ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE smov ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$readyLayout = $grid->readyLayout2( 212, 140, $param['grids'][0]['gridname']);


		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'bimpri', 'img'=>'images/pdf_logo.gif', 'alt' => 'Formato PDF'    , 'label'=>'Reimprimir Documento'));
		$grid->wbotonadd(array('id'=>'cobro' , 'img'=>'images/dinero.png'  , 'alt' => 'Cobro a cliente', 'label'=>'Cobro a Cliente'     ));
		$WestPanel = $grid->deploywestp();

		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'] );

		$adic = array(
			array("id"=>"fedita"  ,  "title"=>"Agregar/Editar Banco o Caja"),
			array("id"=>"fsclisel",  "title"=>"Seleccionar cliente")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);


		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('SMOV', 'JQ');
		$param['otros']        = $this->datasis->otros('SMOV', 'JQ');
		$param['centerpanel']  = $centerpanel;
		//$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$this->load->view('jqgrid/crud2',$param);

	}

	//***************************
	//Funciones de los Botones
	//fuera del doc ready
	//***************************
	function bodyscript( $grid0 ){

		$bodyscript  = '<script type="text/javascript">';

		$bodyscript .= '
		function selscli() {
			$.post("'.site_url($this->url.'selscli/').'/"+id,
				function(data){
					$("#fsclisel").html(data);
					$("#fsclisel").dialog( "open" );
				}
			);
		};';

		$bodyscript .= '$(function() { ';

		$bodyscript .= '
		jQuery("#bimpri").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url($this->url.'smovprint').'/\'+id, \'_blank\', \'width=400,height=4200,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			}else{
				$.prompt("<h1>Por favor Seleccione un Movimiento</h1>");
			}
		});
		';

		$bodyscript .= '
		jQuery("#cobro").click( function(){
			$.post("'.site_url($this->url.'selscli/').'",
				function(data){
					$("#fsclisel").html(data);
					$("#fsclisel").dialog("open");
				}
			);
		});';

		$bodyscript .= '
			$("#fedita").dialog({
				autoOpen: false, height: 500, width: 800, modal: true,
				buttons: {
					"Guardar": function() {
						var bValid = true;
						var murl = $("#df1").attr("action");
						$.ajax({
							type: "POST",
							dataType: "html",
							async: false,
							url: murl,
							data: $("#df1").serialize(),
							success: function(r,s,x){
								try{
									var json = JSON.parse(r);
									if ( json.status == "A" ) {
										$( "#fedita" ).dialog( "close" );
										jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
										window.open(\''.site_url($this->url.'/smovprint').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
										return true;
									} else {
										apprise(json.mensaje);
									}
								}catch(e){
									$("#fedita").html(r);
								}
							}
						})
					},
					"Cancelar": function() {
						$("#fedita").html("");
						$( this ).dialog( "close" );
					}
				},
				close: function() {
					$("#fedita").html("");
				}
			});';

		$bodyscript .= '
			$("#fsclisel").dialog({
				autoOpen: false, height: 430, width: 540, modal: true,
				buttons: {
					"Seleccionar": function() {
						$.get("'.site_url($this->url.'ccli').'"+"/"+$("#id_scli").val()+"/create", function(data) {
							$("#fedita").html(data);
							$("#fedita").dialog("open");
							$("#fsclisel").html("");
							$("#fsclisel").dialog("close");
						});
					},
					Cancel: function() {
						$("#fcobroser").html("");
						$("#fsclisel").dialog( "close" );
					}
				},
				close: function() {
					$("#fsclisel").html("");
				}
			});';

		$bodyscript .= '});';

		$bodyscript .= "\n</script>";
		return $bodyscript;
	}


	//************************************
	//
	//Definicion del Grid y la Forma
	//
	//************************************
	function defgrid( $deployed = false ){
		$i = 1;

		$grid  = new $this->jqdatagrid;

		$grid->addField('cod_cli');
		$grid->label('Cliente');
		$grid->params(array(
			'align'    => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 50
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 200,
			'edittype'      => "'text'",
		));


		$grid->addField('tipo_doc');
		$grid->label('Tipo');
		$grid->params(array(
			'align'        => "'center'",
			'search'       => 'true',
			'editable'     => 'false',
			'width'        => 40,
			'edittype'     => "'text'",
		));


		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'align'        => "'center'",
			'search'       => 'true',
			'editable'     => 'false',
			'width'        => 70,
			'edittype'     => "'text'",
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('monto');
		$grid->label('Monto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('impuesto');
		$grid->label('Impuesto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('abonos');
		$grid->label('Abonos');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('vence');
		$grid->label('Vence');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('tipo_ref');
		$grid->label('Tipo/Ref');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('num_ref');
		$grid->label('Num. Refer');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'edittype'      => "'text'",
		));


		$grid->addField('observa1');
		$grid->label('Observa1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 200,
			'edittype'      => "'text'",
		));


		$grid->addField('observa2');
		$grid->label('Observa2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 200,
			'edittype'      => "'text'",
		));

		$grid->addField('banco');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('tipo_op');
		$grid->label('Tipo_op');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('fecha_op');
		$grid->label('Fecha_op');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('num_op');
		$grid->label('Num_op');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 120,
			'edittype'      => "'text'",
		));


		$grid->addField('ppago');
		$grid->label('Ppago');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('reten');
		$grid->label('Reten');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 60,
			'edittype'      => "'text'",
		));


		$grid->addField('descrip');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 200,
			'edittype'      => "'text'",
		));


		$grid->addField('control');
		$grid->label('Control');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'edittype'      => "'text'",
		));


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));


		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 60,
			'edittype'      => "'text'",
		));


		$grid->addField('transac');
		$grid->label('Transac');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));


		$grid->addField('origen');
		$grid->label('Origen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('cambio');
		$grid->label('Cambio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('mora');
		$grid->label('Mora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('reteiva');
		$grid->label('Reteiva');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('vendedor');
		$grid->label('Vendedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 50,
			'edittype'      => "'text'",
		));


		$grid->addField('nfiscal');
		$grid->label('Nfiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 80,
			'edittype'      => "'text'",
		));


		$grid->addField('montasa');
		$grid->label('Montasa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('monredu');
		$grid->label('Monredu');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('monadic');
		$grid->label('Monadic');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('tasa');
		$grid->label('Tasa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('reducida');
		$grid->label('Reducida');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('sobretasa');
		$grid->label('Sobretasa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('exento');
		$grid->label('Exento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('fecdoc');
		$grid->label('Fecdoc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('nroriva');
		$grid->label('Nroriva');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 120,
			'edittype'      => "'text'",
		));


		$grid->addField('emiriva');
		$grid->label('Emiriva');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('codcp');
		$grid->label('Codcp');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 50,
			'edittype'      => "'text'",
		));


		$grid->addField('depto');
		$grid->label('Depto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('maqfiscal');
		$grid->label('Maqfiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 100,
			'edittype'      => "'text'",
		));


		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 130,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('ningreso');
		$grid->label('Ningreso');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));


		$grid->addField('ncredito');
		$grid->label('Ncredito');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));

		$grid->addField('ncredito');
		$grid->label('Ncredito');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 50,
			'editable' => 'false',
			'search'   => 'false'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('240');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setonSelectRow('
			function(id){
				$.ajax({
					url: "'.base_url().$this->url.'tabla/"+id,
					success: function(msg){
						$("#radicional").html(msg);
					}
				});
			}
		');

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
	function getdata(){
		$grid  = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('smov');
		//if ( !empty($mWHERE)) print_r($mWHERE);

		$response   = $grid->getData('smov', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				//$this->db->insert('smov', $data);
			}
			return "Registro Agregado";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$monto =  $this->datasis->dameval("SELECT monto FROM smov WHERE id='$id' ");
			$data['abonos'] = abs($data['abonos']);
			if ( $data['abonos'] > $monto  ) $data['abonos'] = $monto;
			$this->db->where('id', $id);
			$this->db->update('smov', $data);
			return "Movimiento Modificado";

		} elseif($oper == 'del') {
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM smov WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				//$this->db->simple_query("DELETE FROM smov WHERE id=$id ");
				//logusu('smov',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function selscli(){
		$this->rapyd->load('dataform');

		$script="$('#cod_cli').autocomplete({
			source: function( req, add){
				$.ajax({
					url:  '".site_url('ajax/buscascli')."',
					type: 'POST',
					dataType: 'json',
					data: 'q='+req.term,
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$('#id_scli').val('');

								$('#nombre').val('');
								$('#nombre_val').text('');

								$('#rifci').val('');
								$('#rifci_val').text('');

								$('#direc').val('');
								$('#direc_val').text('');
							}else{
								$.each(data,
									function(i, val){
										sugiere.push( val );
									}
								);
							}
							add(sugiere);
						},
				})
			},
			minLength: 2,
			select: function( event, ui ) {
				$('#cod_cli').attr('readonly', 'readonly');

				$('#id_scli').val(ui.item.id);

				$('#nombre').val(ui.item.nombre);
				$('#nombre_val').text(ui.item.nombre);

				$('#rifci').val(ui.item.rifci);
				$('#rifci_val').text(ui.item.rifci);

				$('#cod_cli').val(ui.item.cod_cli);

				$('#direc').val(ui.item.direc);
				$('#direc_val').text(ui.item.direc);
				setTimeout(function() {  $('#cod_cli').removeAttr('readonly'); }, 1500);
			}
		});";

		$form = new DataForm($this->url.'sclise/process');
		$form->script($script);

		$form->cliente = new inputField('Cliente', 'cod_cli');
		//$form->cliente->rule = "trim|required|max_length[20]";

		$form->id = new hiddenField('', 'id_scli');
		$form->id->in='cliente';
		//$form->body = new textareaField("Body", "body");
		//$form->body->rule = "required";
		//$form->body->rows = 10;

		$form->build_form();

		echo $form->output;
	}

	function smovprint($id){
		$dbid = $this->db->escape($id);
		$tipo = $this->datasis->dameval('SELECT tipo_doc FROM smov WHERE id='.$dbid);
		switch($tipo){
			case 'NC':
				redirect('formatos/descargar/CCLINC/'.$id);
				break;
			case 'AB':
				redirect('formatos/descargar/CCLIAB/'.$id);
				break;
			case 'GI':
				redirect('formatos/descargar/CCLIGI/'.$id);
				break;
			case 'FC':
				redirect('ventas/sfac/dataprint/modify/'.$id);
				break;
			case 'ND':
				redirect('formatos/descargar/CCLIND/'.$id);
				break;
		}
	}

	function ccli($id_scli){
		$cliente  = $this->datasis->dameval("SELECT cliente FROM scli WHERE id=$id_scli");
		if(!$this->_exitescli($cliente)) redirect($this->url.'filteredgrid');
		$dbcliente=$this->db->escape($cliente);
		$scli_nombre=$this->datasis->dameval("SELECT nombre FROM scli WHERE cliente=$dbcliente");
		$scli_rif   =$this->datasis->dameval("SELECT rifci FROM scli WHERE cliente=$dbcliente");


		$cajero=$this->secu->getcajero();
		if(empty($cajero)) show_error('El usuario debe tener registrado un cajero para poder usar este modulo');

		$this->rapyd->load('dataobject','datadetails');
		$this->rapyd->uri->keep_persistence();

		$do = new DataObject('smov');
		$do->rel_one_to_many('itccli', 'itccli', array(
			'tipo_doc'=>'tipoccli',
			'numero'  =>'numccli',
			'cod_cli' =>'cod_cli',
			'fecha'   =>'fecha')
		);
		$do->rel_one_to_many('sfpa'  , 'sfpa'  , array(
			'transac' =>'transac',
			'numero'  =>'numero',
			'tipo_doc'=>'tipo_doc',
			'fecha'   =>'fecha')
		);
		$do->order_by('itccli','itccli.fecha');

		$edit = new DataDetails('Cobro a cliente', $do);
		$edit->on_save_redirect=false;
		$edit->back_url = site_url('finanzas/ccli/filteredgrid');
		$edit->set_rel_title('itccli', 'Producto <#o#>');
		$edit->set_rel_title('itccli', 'Forma de pago <#o#>');

		$edit->pre_process('insert' , '_pre_insert');
		$edit->pre_process('update' , '_pre_update');
		$edit->pre_process('delete' , '_pre_delete');
		$edit->post_process('insert', '_post_insert');
		$edit->post_process('update', '_post_update');
		$edit->post_process('delete', '_post_delete');

		$edit->cod_cli = new hiddenField('Cliente','cod_cli');
		$edit->cod_cli->rule ='max_length[5]';
		$edit->cod_cli->size =7;
		$edit->cod_cli->insertValue=$cliente;
		$edit->cod_cli->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[40]';
		$edit->nombre->size =42;
		$edit->nombre->maxlength =40;

		$edit->tipo_doc = new  dropdownField('Tipo doc.', 'tipo_doc');
		$edit->tipo_doc->option('AB','Abono');
		$edit->tipo_doc->option('NC','Nota de credito');
		$edit->tipo_doc->option('AN','Anticipo');
		$edit->tipo_doc->style='width:140px;';
		$edit->tipo_doc->rule ='enum[AB,NC,AN]|required';

		$edit->codigo = new  dropdownField('Motivo', 'codigo');
		$edit->codigo->option('','Ninguno');
		$edit->codigo->options('SELECT TRIM(codigo) AS cod, nombre FROM botr WHERE tipo=\'C\' ORDER BY nombre');
		$edit->codigo->style='width:200px;';
		$edit->codigo->rule ='';

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->fecdoc = new dateonlyField('Fecha','fecdoc');
		$edit->fecdoc->size =10;
		$edit->fecdoc->maxlength =8;
		$edit->fecdoc->insertValue=date('Y-m-d');
		$edit->fecdoc->rule ='chfecha|required';

		$edit->monto = new inputField('Total','monto');
		$edit->monto->rule='max_length[17]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =19;
		$edit->monto->maxlength =17;
		$edit->monto->type='inputhidden';

		$edit->observa1 = new  textareaField('Concepto:','observa1');
		$edit->observa1->cols = 70;
		$edit->observa1->rows = 2;
		$edit->observa1->style='width:100%;';

		$edit->observa2 = new  textareaField('','observa2');
		$edit->observa2->cols = 70;
		$edit->observa2->rows = 2;
		$edit->observa2->style='width:100%;';
		$edit->observa2->when=array('show');

		$edit->usuario = new autoUpdateField('usuario' ,$this->secu->usuario(),$this->secu->usuario());
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora'    ,date('H:i:s'), date('H:i:s'));
		$edit->fecha   = new autoUpdateField('fecha'   ,date('Ymd'), date('Ymd'));

		//************************************************
		//inicio detalle itccli
		//************************************************
		$i=0;
		$edit->detail_expand_except('itccli');
		$sel=array('a.tipo_doc','a.numero','a.fecha','a.monto','a.abonos','a.monto - a.abonos AS saldo');
		$this->db->select($sel);
		$this->db->from('smov AS a');
		$this->db->where('a.cod_cli',$cliente);
		$transac=$edit->get_from_dataobjetct('transac');
		if($transac!==false){
			$tipo_doc =$edit->get_from_dataobjetct('tipo_doc');
			$dbtransac=$this->db->escape($transac);
			$this->db->join('itccli AS b','a.tipo_doc = b.tipoccli AND a.numero=b.numccli AND a.transac='.$dbtransac);
			$this->db->where('a.tipo_doc',$tipo_doc);
		}else{
			$this->db->where('a.monto > a.abonos');
			$this->db->where_in('a.tipo_doc',array('FC','ND','GI'));
		}
		$this->db->order_by('a.fecha');
		$query = $this->db->get();
		//echo $this->db->last_query();
		foreach ($query->result() as $row){
			$obj='cod_cli_'.$i;
			$edit->$obj = new autoUpdateField('cod_cli',$cliente,$cliente);
			$edit->$obj->rel_id  = 'itccli';
			$edit->$obj->ind     = $i;

			$obj='tipo_doc_'.$i;
			$edit->$obj = new inputField('Tipo_doc',$obj);
			$edit->$obj->db_name='tipo_doc';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='max_length[2]';
			$edit->$obj->insertValue=$row->tipo_doc;
			$edit->$obj->size =4;
			$edit->$obj->maxlength =2;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='numero_'.$i;
			$edit->$obj = new inputField('Numero',$obj);
			$edit->$obj->db_name='numero';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='max_length[8]';
			$edit->$obj->insertValue=$row->numero;
			$edit->$obj->size =10;
			$edit->$obj->maxlength =8;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='fecha_'.$i;
			$edit->$obj = new dateonlyField('Fecha',$obj);
			$edit->$obj->db_name='fecha';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='chfecha';
			$edit->$obj->insertValue=$row->fecha;
			$edit->$obj->size =10;
			$edit->$obj->maxlength =8;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='monto_'.$i;
			$edit->$obj = new inputField('Monto',$obj);
			$edit->$obj->db_name='monto';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='max_length[18]|numeric';
			$edit->$obj->css_class='inputnum';
			$edit->$obj->size =20;
			$edit->$obj->insertValue=$row->monto;
			$edit->$obj->maxlength =18;
			$edit->$obj->ind       = $i;
			$edit->$obj->showformat='decimal';
			$edit->$obj->type='inputhidden';

			$obj='saldo_'.$i;
			$edit->$obj = new freeField($obj,$obj,nformat($row->saldo));
			$edit->$obj->ind = $i;

	        $obj='abono_'.$i;
			$edit->$obj = new inputField('Abono',$obj);
			$edit->$obj->db_name      = 'abono';
			$edit->$obj->rel_id       = 'itccli';
			$edit->$obj->rule         = "max_length[18]|numeric|positive|callback_chabono[$i]";
			$edit->$obj->css_class    = 'inputnum';
			$edit->$obj->showformat   = 'decimal';
			$edit->$obj->autocomplete = false;
			$edit->$obj->disable_paste= true;
			$edit->$obj->size         = 15;
			$edit->$obj->maxlength    = 18;
			$edit->$obj->ind          = $i;
			$edit->$obj->onfocus      = 'itsaldo(this,'.round($row->saldo,2).');';

	        $obj='ppago_'.$i;
			$edit->$obj = new inputField('Pronto Pago',$obj);
			$edit->$obj->db_name      = 'ppago';
			$edit->$obj->rel_id       = 'itccli';
			$edit->$obj->rule         = "max_length[18]|numeric|positive|callback_chppago[$i]";
			$edit->$obj->css_class    = 'inputnum';
			$edit->$obj->showformat   = 'decimal';
			$edit->$obj->autocomplete = false;
			$edit->$obj->disable_paste= true;
			$edit->$obj->size         = 15;
			$edit->$obj->maxlength    = 18;
			$edit->$obj->ind          = $i;
			$edit->$obj->onchange     = "itppago(this,'$i');";

			$i++;
		}
		//************************************************
		//fin de campos para detalle,inicio detalle2 sfpa
		//************************************************
		$edit->tipo = new  dropdownField('Tipo <#o#>', 'tipo_<#i#>');
		$edit->tipo->option('','Ninguno');
		$edit->tipo->options('SELECT tipo, nombre FROM tarjeta WHERE activo=\'S\' ORDER BY nombre');
		$edit->tipo->db_name  = 'tipo';
		$edit->tipo->rel_id   = 'sfpa';
		$edit->tipo->style    = 'width:160px;';
		$edit->tipo->rule     = 'condi_required|callback_chsfpatipo[<#i#>]';
		$edit->tipo->insertValue='EF';
		$edit->tipo->onchange   = 'sfpatipo(<#i#>)';

		$edit->sfpafecha = new dateonlyField('Fecha','sfpafecha_<#i#>');
		$edit->sfpafecha->rel_id   = 'sfpa';
		$edit->sfpafecha->db_name  = 'fecha';
		$edit->sfpafecha->size     = 10;
		$edit->sfpafecha->maxlength= 8;
		$edit->sfpafecha->rule ='condi_required|chitfecha|callback_chtipo[<#i#>]';

		$edit->numref = new inputField('Numero <#o#>', 'num_ref_<#i#>');
		$edit->numref->size     = 12;
		$edit->numref->db_name  = 'num_ref';
		$edit->numref->rel_id   = 'sfpa';
		$edit->numref->rule     = 'condi_required|callback_chtipo[<#i#>]';

		$edit->banco = new dropdownField('Banco <#o#>', 'banco_<#i#>');
		$edit->banco->option('','Ninguno');
		$edit->banco->options('SELECT cod_banc,nomb_banc
			FROM tban
			WHERE cod_banc<>\'CAJ\'
		UNION ALL
			SELECT codbanc,CONCAT_WS(\' \',TRIM(banco),numcuent)
			FROM banc
			WHERE tbanco <> \'CAJ\' ORDER BY nomb_banc');
		$edit->banco->db_name='banco';
		$edit->banco->rel_id ='sfpa';
		$edit->banco->style  ='width:200px;';
		$edit->banco->rule   = 'condi_required|callback_chtipo[<#i#>]';

		$edit->itmonto = new inputField('Monto <#o#>', 'itmonto_<#i#>');
		$edit->itmonto->db_name     = 'monto';
		$edit->itmonto->css_class   = 'inputnum';
		$edit->itmonto->rel_id      = 'sfpa';
		$edit->itmonto->size        = 10;
		$edit->itmonto->rule        = 'condi_required|positive|callback_chmontosfpa[<#i#>]';
		$edit->itmonto->showformat  = 'decimal';
		$edit->itmonto->autocomplete= false;
		//************************************************
		// Fin detalle 2 (sfpa)
		//************************************************

		$edit->buttons('add_rel');
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);

			echo json_encode($rt);
		}else{
			$conten['cana']  = $i;
			$conten['form']  = & $edit;
			$conten['title'] = heading("Cobro a cliente: ($cliente) $scli_nombre $scli_rif");

			$data['content'] = $this->load->view('view_ccli.php', $conten);
		}
	}

	function _exitescli($cliente){
		$dbscli= $this->db->escape($cliente);
		$mSQL  = "SELECT COUNT(*) AS cana FROM scli WHERE cliente=$dbscli";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if( $row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function tabla() {
		$id = $this->uri->segment($this->uri->total_segments());

		$row = $this->datasis->damereg("SELECT cod_cli, tipo_doc, numero, estampa, transac FROM smov WHERE id=$id");

		$transac  = $row['transac'];
		$cod_cli  = $row['cod_cli'];
		$numero   = $row['numero'];
		$tipo_doc = $row['tipo_doc'];
		$estampa  = $row['estampa'];

		$td1  = "<td style='border-style:solid;border-width:1px;border-color:#78FFFF;' valign='top' align='center'>\n";
		$td1 .= "<table width='98%'>\n<caption style='background-color:#5E352B;color:#FFFFFF;font-style:bold'>";

		// Movimientos Relacionados en Proveedores SPRM
		$mSQL = "SELECT cod_prv, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
			FROM sprm WHERE transac='$transac' ORDER BY cod_prv ";
		$query = $this->db->query($mSQL);
		$salida = '<table width="100%"><tr>';
		$saldo  = 0;
		if ( $query->num_rows() > 0 ){
			$salida .= $td1;
			$salida .= "Movimiento en Proveedores</caption>";
			$salida .= "<tr bgcolor='#E7E3E7'><td>Nombre</td><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				if ( $row['tipo_doc'] == 'FC' ) {
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= "<tr>";
				$salida .= "<td>".$row['cod_prv'].'-'.$row['nombre']."</td>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			if ($saldo <> 0)
				$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo). "</td></tr>";
			$salida .= "</table></td>";
		}

		// Movimientos Relacionados en SMOV
		$mSQL = "SELECT cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
			FROM smov WHERE transac='$transac' AND id<>$id ORDER BY cod_cli ";
		$query = $this->db->query($mSQL);
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida .= $td1;
			$salida .= "Movimiento en Clientes</caption>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Nombre</td><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				if ( $row['tipo_doc'] == 'FC' ) {
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= "<tr>";
				$salida .= "<td>".$row['cod_cli'].'-'.$row['nombre']."</td>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table></td>";
		}

		//Retencion de IVA RIVC
		$mSQL = "
			SELECT b.periodo, b.nrocomp, a.reiva
			FROM itrivc a JOIN rivc b ON a.idrivc=b.id WHERE a.tipo_doc=IF('$tipo_doc'='FC','F','D') AND a.numero='$numero'
			UNION ALL
			SELECT b.periodo, b.nrocomp, a.reiva
			FROM itrivc a JOIN rivc b ON a.idrivc=b.id WHERE a.transac='$transac'
			";
		$query = $this->db->query($mSQL);
		if ( $query->num_rows() > 0 ){
			$salida .= $td1;
			$salida .= "Retenciones de IVA</caption>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Periodo</td><td align='center'>Numero</td><td align='center'>Monto</tr>";
			foreach ($query->result_array() as $row){
				$salida .= "<tr>";
				$salida .= "<td>".$row['periodo']."</td>";
				$salida .= "<td>".$row['nrocomp'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['reiva']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table></td>";
		}

		// Factura Relacionada SFAC
		if ( $tipo_doc <> 'FC' ){
			$mSQL = "SELECT tipo_doc, numero, totalg FROM sfac a WHERE a.transac='$transac'	";
			$query = $this->db->query($mSQL);
			if ( $query->num_rows() > 0 ){
				$salida .= $td1;
				$salida .= "Factura Relacionada</caption>";
				$salida .= "<tr bgcolor='#e7e3e7'><td>Tipo</td><td align='center'>Numero</td><td align='center'>Monto</tr>";
				foreach ($query->result_array() as $row){
					$salida .= "<tr>";
					$salida .= "<td>".$row['tipo_doc']."</td>";
					$salida .= "<td>".$row['numero'].  "</td>";
					$salida .= "<td align='right'>".nformat($row['totalg']).   "</td>";
					$salida .= "</tr>";
				}
				$salida .= "</table></td>";
			}
		}

		// Movimientos Relacionados ITCCLI
		$mSQL = "SELECT tipo_doc, numero, monto, abono FROM itccli WHERE transac='$transac' AND estampa='$estampa'";
		$query = $this->db->query($mSQL);
		if ( $query->num_rows() == 0 ){
			$mSQL = "SELECT tipoccli tipo_doc, numccli numero, monto, abono FROM itccli WHERE tipo_doc='$tipo_doc' AND numero='$numero' ";
			$query = $this->db->query($mSQL);
		}
		if($query->num_rows() > 0){
			$saldo = 0;
			$salida .= $td1;
			$salida .= "Movimientos Relacionados</caption>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td><td align='center'>Abono</td></tr>";
			foreach ($query->result_array() as $row){
				$saldo += $row['abono'];
				$salida .= "<tr>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "<td align='right'>".nformat($row['abono']).   "</td>";
				$salida .= "</tr>";
			}
			if ($saldo <> 0)
				$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'><b>Saldo : ".nformat($saldo). "</b></td></tr>";
			$salida .= "</table></td>";
		}

		// FORMA DE PAGO SFPA
		$mSQL = "SELECT CONCAT(a.tipo,' ', b.nombre) tipo, a.num_ref, a.banco, a.monto
			FROM sfpa a JOIN tarjeta b ON a.tipo=b.tipo WHERE a.transac='$transac' AND a.monto<>0";
		$query = $this->db->query($mSQL);
		$codcli = 'XXXXXXXXXXXXXXXX';
		$saldo = 0;
		if ($query->num_rows() > 0){
			$salida .= $td1;
			$salida .= "Formas de Pago</caption>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Forma de Pago</td><td align='center'>Numero</td><td align='center'>Banco</td> <td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				$salida .= "<tr>";
				$salida .= "<td>".$row['tipo']."</td>";
				$salida .= "<td>".$row['num_ref']."</td>";
				$salida .= "<td>".$row['banco']. "</td>";
				$salida .= "<td align='right'>".nformat($row['monto'])."</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table></td>";
		}

		// Prestamos PRMO
		$mSQL = "SELECT if(observa2='',observa1,observa2) observa, monto FROM prmo WHERE transac='$transac' AND clipro='$cod_cli' AND monto<>0";
		$query = $this->db->query($mSQL);
		$saldo = 0;
		if ($query->num_rows() > 0){
			$salida .= $td1;
			$salida .= "Prestamos</caption>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Observacion</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				$salida .= "<tr>";
				$salida .= "<td>".$row['observa']."</td>";
				$salida .= "<td align='right'>".nformat($row['monto'])."</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table></td>";
		}

		//Cruce de Cuentas ITCRUC
		$mSQL = "
			SELECT b.tipo tipo, b.proveed codcp, MID(b.nombre,1,25) nombre, a.onumero, a.monto, b.numero, b.fecha
			FROM itcruc AS a JOIN cruc AS b ON a.numero=b.numero
			WHERE b.proveed='$cod_cli' AND b.transac='$transac' AND a.onumero!='$tipo_doc$numero'
			UNION ALL
			SELECT b.tipo tipo, b.cliente codcp, MID(b.nomcli,1,25) nombre, a.onumero, a.monto, b.numero, b.fecha
			FROM itcruc AS a JOIN cruc AS b ON a.numero=b.numero
			WHERE b.cliente='$cod_cli' AND b.transac='$transac' ORDER BY onumero
			";
		$query = $this->db->query($mSQL);
		$saldo = 0;
		if($query->num_rows() > 0){
			$salida .= $td1;
			$salida .= "Cruce de Cuentas</caption>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Nombre</td><td>Codigo</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				$salida .= "<tr>";
				$salida .= "<td>(".$row['tipo'].') '.$row['nombre']."</td>";
				$salida .= "<td>".$row['codcp']."</td>";
				$salida .= "<td>".$row['onumero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table></td>";
		}
		echo $salida.'</tr></table>';
	}

	function _pre_insert($do){
		$cliente  =$do->get('cod_cli');
		$estampa = $do->get('estampa');
		$hora    = $do->get('hora');
		$usuario = $do->get('usuario');
		$cod_cli = $do->get('cod_cli');
		$tipo_doc= $do->get('tipo_doc');
		$fecha   = $do->get('fecha');
		$concepto= $do->get('observa1');
		$itabono=$sfpamonto=$ppagomonto=0;

		$rrow    = $this->datasis->damerow('SELECT nombre,rifci,dire11,dire12 FROM scli WHERE cliente='.$this->db->escape($cliente));
		if($rrow!=false){
			$do->set('nombre',$rrow['nombre']);
			$do->set('dire1' ,$rrow['dire11']);
			$do->set('dire2' ,$rrow['dire12']);
		}

		//Totaliza el abonado
		$rel='itccli';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$itabono += $do->get_rel($rel, 'abono', $i);
			$pppago   = $do->get_rel($rel, 'ppago', $i);
			if(empty($pppago)){
				$do->set_rel($rel,'ppago',0,$i);
			}else{
				$ppagomonto += $do->get_rel($rel, 'ppago', $i);
			}
		}
		$itabono=round($itabono,2);

		//Totaliza lo pagado
		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpamonto+=$do->get_rel($rel, 'monto', $i);
		}
		$sfpamonto=round($sfpamonto,2);

		//Realiza las validaciones
		$cajero=$this->secu->getcajero();
		$this->load->library('validation');
		$rt=$this->validation->cajerostatus($cajero);
		if(!$rt){
			$do->error_message_ar['pre_ins']='El cajero usado ('.$cajero.') esta cerrado para esta fecha';
			return false;
		}

		if($tipo_doc=='NC'){
			$do->truncate_rel('sfpa');
			if($itabono==0){
				$do->error_message_ar['pre_ins']='Si crea una nota de credito debe relacionarla con algun movimiento';
				return false;
			}
		}elseif($tipo_doc=='AN'){
			$do->truncate_rel('itccli');
			if($itabono!=0){
				$do->error_message_ar['pre_ins']='Un anticipo no puede estar relacionado con algun efecto, en tal caso seria un abono';
				return false;
			}else{
				$itabono=$sfpamonto;
			}
		}else{
			if(abs($sfpamonto-$itabono)>0.01){
				$do->error_message_ar['pre_ins']='El monto cobrado no coincide con el monto de la la transacci&oacute;n';
				return false;
			}
		}
		//fin de las validaciones
		$do->set('monto',$itabono);

		$dbcliente= $this->db->escape($cliente);
		$rowscli  = $this->datasis->damerow('SELECT nombre,dire11,dire12 FROM scli WHERE cliente='.$dbcliente);
		$do->set('nombre', $rowscli['nombre']);
		$do->set('dire1' , $rowscli['dire11']);
		$do->set('dire2' , $rowscli['dire12']);

		$transac  = $this->datasis->fprox_numero('ntransa');

		if($tipo_doc=='AB'){
			$mnum = $this->datasis->fprox_numero('nabcli');
		}elseif($tipo_doc=='GI'){
			$mnum = $this->datasis->fprox_numero('ngicli');
		}elseif($tipo_doc=='NC'){
			$mnum = $this->datasis->fprox_numero('nccli');
		}else{
			$mnum = $this->datasis->fprox_numero('nancli');
		}
		$do->set('vence'  , $fecha);
		$do->set('numero' , $mnum);
		$do->set('transac', $transac);

		$rel='itccli';
		$observa=array();
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$itabono = $do->get_rel($rel, 'abono'   , $i);
			$ittipo  = $do->get_rel($rel, 'tipo_doc', $i);
			$itnumero= $do->get_rel($rel, 'numero'  , $i);
			if(empty($itabono) || $itabono==0){
				$do->rel_rm($rel,$i);
			}else{
				$observa[]=$ittipo.$itnumero;
				$do->set_rel($rel, 'tipoccli', $tipo_doc, $i);
				$do->set_rel($rel, 'cod_cli' , $cod_cli , $i);
				$do->set_rel($rel, 'estampa' , $estampa , $i);
				$do->set_rel($rel, 'hora'    , $hora    , $i);
				$do->set_rel($rel, 'usuario' , $usuario , $i);
				$do->set_rel($rel, 'transac' , $transac , $i);
				$do->set_rel($rel, 'mora'    , 0, $i);
				$do->set_rel($rel, 'reten'   , 0, $i);
				$do->set_rel($rel, 'cambio'  , 0, $i);
				$do->set_rel($rel, 'reteiva' , 0, $i);
			}
		}

		if(empty($concepto)){
			if(count($observa)>0){
				$observa='PAGA '.implode(',',$observa);
				$do->set('observa1' , substr($observa,0,50));
				if(strlen($observa)>50) $do->set('observa2' , substr($observa,50));
			}
		}else{
			$do->set('observa1' , substr($concepto,0,50));
			if(strlen($concepto)>50) $do->set('observa2' , substr($concepto,50));
		}

		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpatipo=$do->get_rel($rel, 'tipo_doc', $i);
			if($sfpatipo=='EF') $do->set_rel($rel, 'fecha' , $fecha , $i);

			$do->set_rel($rel,'estampa'  , $estampa , $i);
			$do->set_rel($rel,'hora'     , $hora    , $i);
			$do->set_rel($rel,'usuario'  , $usuario , $i);
			$do->set_rel($rel,'transac'  , $transac , $i);
			$do->set_rel($rel,'f_factura', $fecha   , $i);
			$do->set_rel($rel,'cod_cli'  ,$cliente  , $i);
			$do->set_rel($rel,'cobro'    ,$fecha    , $i);
			$do->set_rel($rel,'vendedor' ,$this->secu->getvendedor(),$i);
			$do->set_rel($rel,'cobrador' ,$this->secu->getcajero()  ,$i);
			$do->set_rel($rel,'almacen'  ,$this->secu->getalmacen() ,$i);
		}
		$this->ppagomonto=$ppagomonto;

		$do->set('mora'    ,0);
		$do->set('reten'   ,0);
		$do->set('cambio'  ,0);
		$do->set('reteiva' ,0);
		$do->set('ppago'   ,$ppagomonto);
		$do->set('codigo'  ,'NOCON');
		$do->set('descrip' ,'NOTA DE CONTABILIDAD');
		$do->set('vendedor', $this->secu->getvendedor());
		return true;
	}

	function _post_insert($do){
		$cliente  =$do->get('cod_cli');
		$dbcliente=$this->db->escape($cliente);

		$rel_id='itccli';
		$cana = $do->count_rel($rel_id);
		if($cana>0){
			if($this->ppagomonto>0){
				//Crea la NC por Pronto pago
				$mnumnc = $this->datasis->fprox_numero('nccli');

				$dbdata=array();
				$dbdata['cod_cli']    = $cliente;
				$dbdata['nombre']     = $do->get('nombre');
				$dbdata['dire1']      = $do->get('dire1');
				$dbdata['dire2']      = $do->get('dire2');
				$dbdata['tipo_doc']   = 'NC';
				$dbdata['numero']     = $mnumnc;
				$dbdata['fecha']      = $do->get('fecha');
				$dbdata['monto']      = $this->ppagomonto;
				$dbdata['impuesto']   = 0;
				$dbdata['abonos']     = $this->ppagomonto;
				$dbdata['vence']      = $do->get('fecha');
				$dbdata['tipo_ref']   = 'AB';
				$dbdata['num_ref']    = $do->get('numero');
				$dbdata['observa1']   = 'DESCUENTO POR PRONTO PAGO';
				$dbdata['estampa']    = $do->get('estampa');
				$dbdata['hora']       = $do->get('hora');
				$dbdata['transac']    = $do->get('transac');
				$dbdata['usuario']    = $do->get('usuario');
				$dbdata['codigo']     = 'DEPPC';
				$dbdata['descrip']    = 'DESCUENTO PRONTO PAGO';
				$dbdata['fecdoc']     = $do->get('fecha');
				$dbdata['nroriva']    = '';
				$dbdata['emiriva']    = '';
				$dbdata['reten']      = 0;
				$dbdata['cambio']     = 0;
				$dbdata['mora']       = 0;

				$mSQL = $this->db->insert_string('smov', $dbdata);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'ccli'); }

				$itdbdata=array();
				$itdbdata['cod_cli']  = $cliente;
				$itdbdata['numccli']  = $mnumnc;
				$itdbdata['tipoccli'] = 'NC';
				$itdbdata['estampa']  = $do->get('estampa');
				$itdbdata['hora']     = $do->get('hora');
				$itdbdata['transac']  = $do->get('transac');
				$itdbdata['usuario']  = $do->get('usuario');
				$itdbdata['fecha']    = $do->get('fecha');
				$itdbdata['monto']    = $this->ppagomonto;
				$itdbdata['reten']    = 0;
				$itdbdata['cambio']   = 0;
				$itdbdata['mora']     = 0;

				unset($dbdata);
			}

			foreach($do->data_rel[$rel_id] AS $i=>$data){
				$tipo_doc = $data['tipo_doc'];
				$numero   = $data['numero'];
				$fecha    = $data['fecha'];
				$monto    = $data['abono'];
				$ppago    = (empty($data['ppago']))? 0: $data['ppago'];

				$dbtipo_doc = $this->db->escape($tipo_doc);
				$dbnumero   = $this->db->escape($numero  );
				$dbfecha    = $this->db->escape($fecha   );
				$dbmonto    = $monto+$ppago;

				$mSQL="UPDATE smov SET abonos=abonos+$dbmonto WHERE tipo_doc=$dbtipo_doc AND numero=$dbnumero AND cod_cli=$dbcliente LIMIT 1";

				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'ccli'); }

				if($ppago > 0 ){
					$itdbdata['tipo_doc'] = $tipo_doc;
					$itdbdata['numero']   = $numero;
					$itdbdata['abono']    = $ppago;

					$mSQL = $this->db->insert_string('itccli', $itdbdata);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'ccli'); }
				}
			}
		}

		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpatipo = $do->get_rel($rel, 'tipo', $i);
			$codbanc  = $do->get_rel($rel,'banco',$i);
			$dbcodbanc= $this->db->escape($codbanc);
			$monto    = $do->get_rel($rel,'monto',$i);
			//Si es deposito en banco o transferencia crea el movimiento
			if($sfpatipo=='DE' || $sfpatipo=='NC'){
				$sql ='SELECT tbanco,moneda,banco,saldo,depto,numcuent FROM banc WHERE codbanc='.$dbcodbanc;
				$fila=$this->datasis->damerow($sql);

				$ffecha  = $do->get_rel($rel,'fecha',$i);
				$itdbdata=array();
				$itdbdata['codbanc']  = $codbanc;
				$itdbdata['moneda']   = $fila['moneda'];
				$itdbdata['numcuent'] = $fila['numcuent'];
				$itdbdata['banco']    = $fila['banco'];
				$itdbdata['saldo']    = $fila['saldo']+$monto;
				$itdbdata['tipo_op']  = $do->get_rel($rel,'tipo',$i);
				$itdbdata['numero']   = $do->get_rel($rel,'num_ref',$i);
				$itdbdata['fecha']    = $ffecha;
				$itdbdata['clipro']   = 'C';
				$itdbdata['codcp']    = $cliente;
				$itdbdata['nombre']   = $do->get('nombre');
				$itdbdata['monto']    = $monto;
				$itdbdata['concepto'] = 'INGRESO POR COBRANZA';
				$itdbdata['status']   = 'P';
				$itdbdata['liable']   = 'S';
				$itdbdata['transac']  = $do->get('transac');
				$itdbdata['usuario']  = $do->get('usuario');
				$itdbdata['estampa']  = $do->get('estampa');
				$itdbdata['hora']     = $do->get('hora');
				$itdbdata['anulado']  = 'N';
				$mSQL = $this->db->insert_string('bmov', $itdbdata);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'ccli'); }

				$sfecha=str_replace('-','',$ffecha);
				$mSQL="CALL sp_actusal($dbcodbanc,'$sfecha',$monto)";
				$ban=$this->db->simple_query($mSQL);
				if($ban==false) memowrite($mSQL,'ccli');
			}
		}
	}

	function _pre_update($do){
		return false;
	}

	function _pre_delete($do){
		return false;
	}

	function instalar(){
		$campos=$this->db->list_fields('smov');
		if (!in_array('id',$campos)){
			$mSQL="ALTER TABLE `smov`
				ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT,
				DROP PRIMARY KEY,
				ADD UNIQUE INDEX `unic` (`cod_cli`, `tipo_doc`, `numero`, `fecha`),
				ADD PRIMARY KEY (`id`)";
			$this->db->simple_query();
		}
	}
}
