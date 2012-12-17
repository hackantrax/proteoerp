<?php
class Sfac extends Controller {
	var $mModulo='SFAC';
	var $titp='Facturaci&oacute;n ';
	var $tits='Facturaci&oacute;n';
	var $url ='ventas/sfac/';
	var $genesal=true;

	function Sfac(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SFAC', 0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 1000, 650, 'ventas/sfac' );
		redirect($this->url.'jqdatag');
	}

	//Ventana principal de facturacion
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"boton1",  "img"=>"images/pdf_logo.gif","alt" => 'Formato PDF', "label"=>"Reimprimir Documento"));
		$grid->wbotonadd(array("id"=>"precierre","img"=>"images/dinero.png", "alt" => 'Cierre de Caja',"label"=>"Cierre de Caja"));
		$fiscal=$this->datasis->traevalor('IMPFISCAL','Indica si se usa o no impresoras fiscales, esto activa opcion para cierre X y Z');
		if($fiscal=='S'){
			$grid->wbotonadd(array("id"=>"bcierrex","img"=>"assets/default/images/print.png", "alt" => 'Imprimir Cierre X',"label"=>"Cierre X"));
			$grid->wbotonadd(array("id"=>"bcierrez","img"=>"assets/default/images/print.png", "alt" => 'Imprimir Cierre Z',"label"=>"Cierre Z"));
		}

		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array("id"=>"fedita", "title"=>"Agregar/Editar Registro")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);


		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('SFAC', 'JQ');
		$param['otros']        = $this->datasis->otros('SFAC', 'JQ');
		$param['centerpanel']  = $centerpanel;
		//$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);

	}

	//Ventana principal de facturacion de servicios
	function jqmes(){
		$mModulo='SFAC';

		$grid = $this->defgrid( false, 'false' );
		$grid->setAdd(false);
		#Set url
		$grid->setUrlput(site_url($this->url.'setdatam/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatam/'));
		$grid->setTitle("Facturacion de Servicio Mensual");

		//$grid->params['editable'] = 'true';

		$param['grids'][] = $grid->deploy();
		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"cobroser", "img"=>"images/agrega4.png", "alt" => 'Cobro de Servicio',"label"=>"Cobro de Servicio"));
		$grid->wbotonadd(array("id"=>"imptxt",   "img"=>"assets/default/images/print.png", "alt" => 'Imprimir Servicio',"label"=>"Imprimir Factura"));
		$grid->wbotonadd(array("id"=>"precierre","img"=>"images/dinero.png", "alt" => 'Cierre de Caja',"label"=>"Cierre de Caja"));

		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array("id"=>"fcobroser", "title"=>"Cobro de servicio"),
			array("id"=>"fimpser"  , "title"=>"Imprimir Factura")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		//$param['listados']     = $this->datasis->listados('SFAC', 'JQ');
		//$param['otros']        = $this->datasis->otros('SFAC', 'JQ');
		$param['centerpanel']  = $centerpanel;
		//$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = "Cobro de Servicio";
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );

		$this->load->view('jqgrid/crud2',$param);

	}

	//******************************************************************
	//
	//Funciones de los Botones
	//
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '
		<script type="text/javascript">
		jQuery("#boton1").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
			} else { $.prompt("<h1>Por favor Seleccione una Factura</h1>");}
		});';

		$bodyscript .= '
		jQuery("#boton1").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
			} else { $.prompt("<h1>Por favor Seleccione una Factura</h1>");}
		});';

		$bodyscript .= '
		function sfacadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fimpser").html("");
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function sfacedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fborra").html("");
					$("#fimpser").html("");
					$("#fedita").html(data);
					$("#fedita").dialog({ buttons: { Ok: function() { $( this ).dialog( "close" ); } } });
					$("#fedita").dialog( "open" );
				});
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function sfacdel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea anular el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						$("#fedita").html("");
						$("#fimpser").html("");
						$("#fborra").html(data);
						$("#fborra").dialog( "open" );
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		jQuery("#boton2").click( function(){
			window.open(\''.site_url('ventas/sfac/dataedit/create').'\', \'_blank\', \'width=900,height=700,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
		});';

		$fiscal=$this->datasis->traevalor('IMPFISCAL','Indica si se usa o no impresoras fiscales, esto activa opcion para cierre X y Z');
		if($fiscal=='S'){
			$bodyscript .= '
			jQuery("#bcierrex").click( function(){
				window.open(\''.site_url('formatos/descargartxt/CIERREX').'\', \'_blank\', \'width=300,height=300,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			});';

			$bodyscript .= '
			jQuery("#bcierrez").click( function(){
				window.open(\''.site_url('formatos/descargartxt/CIERREZ').'\', \'_blank\', \'width=300,height=300,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			});';
		}

		//Imprime factura a Impresora de texto
		//$bodyscript .= '
		//jQuery("#imptxt").click( function(){
		//	var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
		//	if (id)	{
		//		window.open(\''.site_url('formatos/descargartxt/FACTSER').'/\'+id, \'_blank\', \'width=900,height=700,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
		//	} else { $.prompt("<h1>Por favor Seleccione una Factura</h1>");}
		//});';

		//Precierre
		$bodyscript .= '
		jQuery("#precierre").click( function(){
			//$.prompt("<h1>Seguro que desea hacer cierre?</h1>")
			window.open(\''.site_url('ventas/rcaj/precierre/99/').'/'.$this->secu->getcajero().'\', \'_blank\', \'width=900,height=700,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
		});';

		//Prepara Pago o Abono
		$bodyscript .= '
			$("#cobroser").click(function() {
				$.post("'.site_url('ventas/sfac/fcobroser').'", function(data){
					$("#fcobroser").html(data);
				});
				$( "#fcobroser" ).dialog( "open" );
			});

			$("#imptxt").click(function(){
				var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					$.post("'.site_url('ventas/sfac/dataprintser/modify').'/"+id, function(data){
						$("#fimpser").html(data);
					});
					$("#fimpser").dialog( "open" );
				}else{
					$.prompt("<h1>Por favor Seleccione un Registro</h1>");
				}
			});

			$("#fimpser").dialog({
				autoOpen: false, height: 420, width: 400, modal: true,
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
								if (json.status == "A"){
									$("#fimpser").dialog( "close" );
									jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
									apprise("Registro Guardado");
									return true;
								} else {
									apprise(json.mensaje);
								}
							}catch(e){
								$("#fimpser").html(r);
							}
						}
					})},
				"Imprimir": function() {
						var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
						location.href="'.site_url('formatos/descargartxt/FACTSER').'/"+id;
					},
				"Cancelar": function() { $( this ).dialog( "close" ); }
				},
				close: function() {
					$("#fimpser").html("");
				}
			});

			$("#fedita").dialog({
				autoOpen: false, height: 500, width: 790, modal: true,
				buttons: {
				"Guardar": function() {
					var bValid = true;
					var murl = $("#df1").attr("action");
					//allFields.removeClass( "ui-state-error" );
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
									apprise("Registro Guardado");
									'.$this->datasis->jwinopen(site_url($this->url.'dataprint/modify').'/\'+json.pk.id').';
									return true;
								} else {
									apprise(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
							}
						}
				})},
				"Cancelar": function() { $( this ).dialog( "close" ); }
				},
				close: function() {
					//allFields.val( "" ).removeClass( "ui-state-error" );
				}
			});

			$( "#fcobroser" ).dialog({
				autoOpen: false, height: 430, width: 540, modal: true,
				buttons: {
					"Guardar": function() {
						$.post("'.site_url('ventas/mensualidad/servxmes/insert').'", { cod_cli: $("#fcliente").val(),cana_0: $("#fmespaga").val(),tipo_0: $("#fcodigo").val(),num_ref_0: $("#fcomprob").val(),preca_0: $("#ftarifa").val() },
							function(data) {
								if( data.substr(0,14) == "Venta Guardada"){
									$("#fcobroser").dialog( "close" );
									jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
									apprise(data);
									$("#fcobroser").html("");
									$.post("'.site_url('ventas/sfac/dataprintser/modify').'/"+data.substr(15,10), function(data){
										$("#fimpser").html(data);
									});
									$("#fimpser").dialog( "open" );
									return true;
								}else{
									apprise("<div style=\"font-size:16px;font-weight:bold;background:red;color:white\">Error:</div> <h1>"+data);
								}
							}
						);
					},
					Cancel: function() { $( this ).dialog( "close" ); }
				},
				close: function() {
					//allFields.val( "" ).removeClass( "ui-state-error" );
					//alert("Cerrado");
				}
			});
		';
		$bodyscript .= "\n</script>\n";

		return $bodyscript;
	}

	//Definicion del Grid y la Forma
	function defgrid( $deployed = false, $xmes = 'true' ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('tipo_doc');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 1 }',
		));

		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 65,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 75,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('vence');
		$grid->label('Vence');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 75,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$mSQL = "SELECT vendedor, concat( vendedor, ' ',TRIM(nombre)) nombre FROM vend ORDER BY nombre ";
		$avende  = $this->datasis->llenajqselect($mSQL, true );

		$grid->addField('vd');
		$grid->label('Vende');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 50,
			'edittype'      => "'select'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ value: '.$avende.',  style:"width:220px"}',
			'stype'         => "'text'",
		));

		$grid->addField('cod_cli');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('rifci');
		$grid->label('RIF/CI');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 13 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 170,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));

		$grid->addField('referen');
		$grid->label('Ref.');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));

		$grid->addField('totals');
		$grid->label('Sub Total');
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
		$grid->label('I.V.A.');
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

		$grid->addField('totalg');
		$grid->label('Total');
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


/*
		$grid->addField('direc');
		$grid->label('Direc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));


		$grid->addField('dire1');
		$grid->label('Dire1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));
*/


		$grid->addField('orden');
		$grid->label('Orden');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 10 }',
		));


		$grid->addField('inicial');
		$grid->label('Inicial');
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



		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));


		$grid->addField('devolu');
		$grid->label('Devolu');
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


		$grid->addField('cajero');
		$grid->label('Cajero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('almacen');
		$grid->label('Almacen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));


		$grid->addField('peso');
		$grid->label('Peso');
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


		$grid->addField('factura');
		$grid->label('Factura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

/*
		$grid->addField('pedido');
		$grid->label('Pedido');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));
*/

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('transac');
		$grid->label('Transaccion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));


		$grid->addField('nfiscal');
		$grid->label('No Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:15, maxlength: 12 }',
		));


		$grid->addField('entregado');
		$grid->label('Entregado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 75,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:false, date:true}',
			'formoptions'   => '{ label:"Fecha de Entrega" }'
		));


		$grid->addField('zona');
		$grid->label('Zona');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));


		$grid->addField('ciudad');
		$grid->label('Ciudad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));


		$grid->addField('comision');
		$grid->label('Comision');
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


		$grid->addField('pagada');
		$grid->label('Pagada');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('sepago');
		$grid->label('Sepago');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));


		$grid->addField('dias');
		$grid->label('Dias');
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

/*
		$grid->addField('fpago');
		$grid->label('Fpago');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('comical');
		$grid->label('Comical');
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


		$grid->addField('tasa');
		$grid->label('Tasa');
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


		$grid->addField('reducida');
		$grid->label('Reducida');
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


		$grid->addField('sobretasa');
		$grid->label('Sobretasa');
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


		$grid->addField('montasa');
		$grid->label('Montasa');
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


		$grid->addField('monredu');
		$grid->label('Monredu');
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


		$grid->addField('monadic');
		$grid->label('Monadic');
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


		$grid->addField('notcred');
		$grid->label('Notcred');
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


		$grid->addField('fentrega');
		$grid->label('Fentrega');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('fpagom');
		$grid->label('Fpagom');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('fdespacha');
		$grid->label('Fdespacha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('udespacha');
		$grid->label('Udespacha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('numarma');
		$grid->label('Numarma');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));
*/

		$grid->addField('maqfiscal');
		$grid->label('Maq. Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:15, maxlength: 20 }',
		));


		$grid->addField('dmaqfiscal');
		$grid->label('Devolu.M.Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:15, maxlength: 20 }',
		));

/*
		$grid->addField('nromanual');
		$grid->label('Nromanual');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 14 }',
		));


		$grid->addField('fmanual');
		$grid->label('Fmanual');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('reiva');
		$grid->label('Reiva');
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


		$grid->addField('creiva');
		$grid->label('Creiva');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 20 }',
		));


		$grid->addField('freiva');
		$grid->label('Freiva');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('ereiva');
		$grid->label('Ereiva');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('vexenta');
		$grid->label('Vexenta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));
*/

		$grid->addField('observa');
		$grid->label('Observa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 50 }',
		));

		$grid->addField('observ1');
		$grid->label('Observ1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 50 }',
		));

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

/*
		$grid->addField('certificado');
		$grid->label('Certificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 32 }',
		));


		$grid->addField('sprv');
		$grid->label('Sprv');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('maestra');
		$grid->label('Maestra');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));
*/

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('200');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					var ret = $("#titulos").getRowData(id);
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
					$.ajax({
						url: "'.base_url().$this->url.'tabla/"+id,
						success: function(msg){
							$("#ladicional").html(msg);
						}
					});
				}
			}'
		);

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 450, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 450, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons

		$grid->setEdit(false);
		$grid->setAdd(   $this->datasis->sidapuede('SFAC','INCLUIR%' ));
		$grid->setDelete($this->datasis->sidapuede('SFAC','BORR_REG%'));
		$grid->setSearch($this->datasis->sidapuede('SFAC','BUSQUEDA%'));

		$grid->setRowNum(30);
		$grid->setBarOptions("addfunc: sfacadd, editfunc: sfacedit, delfunc: sfacdel");
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

	//Busca la data en el Servidor por json
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('sfac');

		$response   = $grid->getData('sfac', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//Busca la data en el Servidor por json
	function getdatam(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('sfac');
		$mWHERE[] = array('', 'fecha', date('Ymd'), '' );
		$mWHERE[] = array('', 'usuario', $this->session->userdata('usuario'),'');

		$response   = $grid->getData('sfac', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//Guarda la Informacion
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'edit') {
			if ( empty($data['entregado']) )
				unset($data['entregado']);
			$this->db->where('id', $id);
			$this->db->update('sfac', $data);
			logusu('SFAC',"Factura $id MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			/*
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM sfac WHERE id=$id ");
				logusu('SFAC',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
			*/
		};
	}

	//Guarda la Informacion
	function setDatam(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			if ( empty($data['entregado']) )
				unset($data['entregado']);
			$this->db->where('id', $id);
			$this->db->update('sfac', $data);
			logusu('SFAC',"Registro $id MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			$transac = $this->datasis->dameval("SELECT transac FROM sfac WHERE id=$id");
			$upago   = $this->datasis->dameval("SELECT upago   FROM sfac WHERE id=$id");
			$cliente = $this->datasis->dameval("SELECT cod_cli FROM sfac WHERE id=$id");

			$this->db->query("UPDATE sfac   SET tipo_doc='X' WHERE transac='$transac' ");
			$this->db->query("UPDATE sitems SET tipoa='X'    WHERE transac=$id AND fecha=curdate()");
			$this->db->query("UPDATE scli   SET upago=$upago WHERE cliente=".$this->db->escape($cliente));
			logusu('SFAC',"Factura $id ANULADA");

			echo "Factura Anulada";
		};
	}

	//Definicion del Grid y la Forma
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

/*
		$grid->addField('tipoa');
		$grid->label('Tipoa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));


		$grid->addField('numa');
		$grid->label('Numa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));
*/

		$grid->addField('codigoa');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));


		$grid->addField('desca');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));


		$grid->addField('cana');
		$grid->label('Cantidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('preca');
		$grid->label('Precio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 90,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('tota');
		$grid->label('Total');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 90,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('iva');
		$grid->label('Iva');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('vendedor');
		$grid->label('Vendedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('costo');
		$grid->label('Costo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 90,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('comision');
		$grid->label('Comision');
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


		$grid->addField('cajero');
		$grid->label('Cajero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));

/*
		$grid->addField('mostrado');
		$grid->label('Mostrado');
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


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('transac');
		$grid->label('Transac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));
*/

		$grid->addField('despacha');
		$grid->label('Despacha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));

/*
		$grid->addField('flote');
		$grid->label('Flote');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));
*/

		$grid->addField('pvp');
		$grid->label('Precio 1');
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

		$grid->addField('precio4');
		$grid->label('Precio4');
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


		$grid->addField('detalle');
		$grid->label('Detalle');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('fdespacha');
		$grid->label('Fdespacha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('udespacha');
		$grid->label('Udespacha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));

/*
		$grid->addField('combo');
		$grid->label('Combo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));


		$grid->addField('descuento');
		$grid->label('Descuento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('bonifica');
		$grid->label('Bonifica');
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


		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));
*/

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

/*
		$grid->addField('id_sfac');
		$grid->label('Id_sfac');
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
*/

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('190');
		//$grid->setTitle($this->titp);
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		//$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		//$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		//$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(false);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdatait/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatait/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	//Busca la data en el Servidor por json
	function getdatait(){
		$id = $this->uri->segment(4);
		if ($id === false ){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM sfac");
		}
		if(empty($id)) return '';
		$tipo_doc = $this->datasis->dameval("SELECT tipo_doc FROM sfac WHERE id=$id");
		$numero   = $this->datasis->dameval("SELECT numero   FROM sfac WHERE id=$id");

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM sitems WHERE tipoa='$tipo_doc' AND numa='$numero' ";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//Guarda la Informacion
	function setDatait(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;
	}

	//Forma de Cobro de Servicio
	function fcobroser(){
		$mSQL    = "SELECT tipo, CONCAT(tipo, ' ', nombre) descrip FROM tarjeta WHERE tipo NOT IN ('DE','NC','IR') ORDER BY tipo ";
		$tarjeta = $this->datasis->llenaopciones($mSQL, true, 'fcodigo');



		$salida = '
		<script type="text/javascript">

		var totaliza = function (){
			var meses = Number($("#fmespaga").val());
			var monto = Number($("#ftarifa").val());
			var pagado= Number($("#pagado").val());
			var total = meses*monto;
			var vuelto= 0;

			if(pagado>total) vuelto = pagado-total;
			$("#fmonto").val(nformat(total,2));
			$("#montotot").text(nformat(total,2));
			$("#vuelto").text(nformat(vuelto,2));
		}

		$("#fmespaga").keyup(totaliza);
		$("#pagado").keyup(totaliza);

		$("#fcliente").autocomplete({
			source: function( req, add){
				$.ajax({
					url:  "'.site_url('ajax/buscascliser').'",
					type: "POST",
					dataType: "json",
					data: "q="+req.term,
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$("#fnombre").val("");
								$("#fdire11").val("");
								$("#ftelefono").val("");
								$("#ftarifa").val("");
								$("#fupago").val("");
								$("#utribu").text("0,000");
							}else{
								$.each(data,
									function(i, val){
											sugiere.push( val );
										}
									);
								}
								add(sugiere);
								totaliza();
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#fnombre").val(ui.item.nombre);
					$("#ftelefono").val(ui.item.telefono);
					$("#ftarifa").val(ui.item.precio1);
					$("#fcodtar").val(ui.item.codigo);
					$("#fdire11").val(ui.item.direc);
					$("#fupago").val(ui.item.upago);
					$("#utribu").text(nformat(ui.item.utribu,3));
					totaliza();
				}
			});
		</script>

		<div style="background-color:#D0D0D0;font-weight:bold;font-size:14px;text-align:center"><table width="100%"><tr><td>Cobro de Servicios Mensuales</td><td></td><td> </td></tr></table></div>
		<p class="validateTips"></p>
		<form id="formcobroser">
		<fieldset style="border: 2px outset #9AC8DA;background: #FFFDE9;">
		<table width="90%" align="center" border="0">
		<tr>
			<td class="CaptionTD" align="right">Cliente: </td>
			<td>&nbsp;<input name="fcliente" id="fcliente" type="text" value="" maxlengh="12" size="12" /></td>
			<td class="CaptionTD" align="right">Telefono: </td>
			<td>&nbsp;<input name="ftelefono" id="ftelefono" type="text" value="" maxlengh="12" size="12" /></td>
		</tr>
		<tr>
			<td class="CaptionTD" align="right">Nombre: </td>
			<td colspan="3">&nbsp;<input name="fnombre" id="fnombre" value="" size="50" ></td>
		</tr>
		<tr>
			<td class="CaptionTD" align="right">Direccion: </td>
			<td colspan="3">&nbsp;<input name="fdire11" id="fdire11" value="" size="50"></td>
		</tr>
		<tr>
			<td class="CaptionTD" align="right">&nbsp;</td>
			<td colspan="3">&nbsp;<input name="fdire12" id="fdire12" value="" size="50"></td>
		</tr>
		</table>

		</fieldset>
		<fieldset style="border: 2px outset #9AC8DA;background: #FFFDE9;">
		<table width="90%" align="center" border="0">
		<tr>
			<td class="CaptionTD" align="right">Ultimo Pago: </td>
			<td>&nbsp;<input name="fupago" id="fupago" type="text" value="201112" maxlengh="12" size="8" /></td>
			<td  class="CaptionTD"  align="right">Unidades Trub.</td>
			<td>&nbsp;<b id="utribu">0,000</b><input type="hidden" name="fcodtar" id="fcodtar" type="text" value="" maxlengh="12" size="15"  /></td>
			<td  class="CaptionTD"  align="right">Monto</td>
			<td>&nbsp;<input name="ftarifa" id="ftarifa" type="text" value="" maxlengh="12" size="12"  /></td>
		</tr>
		</table>
		</fieldset>

		</fieldset>
		<fieldset style="border: 2px outset #9AC8DA;background: #FFFDE9;">
		<table width="90%" align="center" border="0">
		<tr>
			<td class="CaptionTD" align="right">Nro de meses que paga: </td>
			<td>&nbsp;<input name="fmespaga" id="fmespaga" type="text" value="12" maxlengh="12" size="8" /></td>
		</tr>
		</table>
		</fieldset>

		<fieldset style="border: 2px outset #9AC8DA;background: #FFFDE9;">
		<table width="90%" align="center" border="0">
		<tr>
			<td class="CaptionTD" align="right">Forma de Pago</td>
			<td>&nbsp;'.$tarjeta.'</td>
			<td  class="CaptionTD"  align="right">Numero</td>
			<td>&nbsp;<input name="fcomprob" id="fcomprob" type="text" value="" maxlengh="12" size="12" /></td>
		</tr>
		<tr>
			<td align="right">Paga con:</td>
			<td ><input name="pagado" id="pagado" type="text" value="" maxlengh="12" size="12" /></td>
			<td colspan="2" align="center"><div style="font-size:12px;font-weight:bold">Vuelto: <span id="vuelto">0,00</span></div></td>
		</tr>


		</tr>
		</table>
		</fieldset>

		<input id="fmonto"   name="fmonto"   type="hidden">
		<input id="fsele"    name="fsele"    type="hidden">
		<input id="fid"      name="fid"      type="hidden" value="">
		<input id="fgrid"    name="fgrid"    type="hidden">
		<br>
		<center><table id="abonados"><table></center>
		<table width="100%">
		<tr>
			<td align="center"><div id="grantotal" style="font-size:20px;font-weight:bold">Monto a pagar: <span id="montotot">0,00</span></div></td>
		</tr>
		</table>
		</form>';
		echo $salida;
	}

	//Json para llena la tabla de inventario
	function sfacsitems() {
		$numa  = $this->uri->segment($this->uri->total_segments());
		$tipoa = $this->uri->segment($this->uri->total_segments()-1);

		$mSQL  = 'SELECT a.codigoa, a.desca, a.cana, a.preca, a.tota, a.iva, IF(a.pvp < a.preca, a.preca, a.pvp)  pvp, ROUND(100-a.preca*100/IF(a.pvp<a.preca,a.preca, a.pvp),2) descuento, ROUND(100-ROUND(a.precio4*100/(100+a.iva),2)*100/a.preca,2) precio4, a.detalle, a.fdespacha, a.udespacha, a.bonifica, b.id url ';
		$mSQL .= "FROM sitems a LEFT JOIN sinv b ON a.codigoa=b.codigo WHERE a.tipoa='$tipoa' AND a.numa='$numa' ";
		$mSQL .= "ORDER BY a.codigoa";

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			$retArray = array();
			foreach( $query->result_array() as  $row ) {
				$retArray[] = $row;
			}
			$data = json_encode($retArray);
			$ret = "{data:" . $data .",\n";
			$ret .= "recordType : 'array'}";
		} else {
			$ret = '{data : []}';
		}
		echo $ret;
	}

	//Recibir retencion de IVA
	function sfacreiva(){
		$reinte = $this->uri->segment($this->uri->total_segments());
		$efecha = $this->uri->segment($this->uri->total_segments()-1);
		$fecha  = $this->uri->segment($this->uri->total_segments()-2);
		$numero = $this->uri->segment($this->uri->total_segments()-3);
		$id     = $this->uri->segment($this->uri->total_segments()-4);
		$mdevo  = "Exito";

		//memowrite("efecha=$efecha, fecha=$fecha, numero=$numero, id=$id, reinte=$reinte","sfacreiva");

		// status de la factura
		$fecha  = substr($fecha, 6,4).substr($fecha, 3,2).substr($fecha, 0,2);
		$efecha = substr($efecha,6,4).substr($efecha,3,2).substr($efecha,0,2);

		$tipo_doc = $this->datasis->dameval("SELECT tipo_doc FROM sfac WHERE id=$id");
		$referen  = $this->datasis->dameval("SELECT referen  FROM sfac WHERE id=$id");
		$numfac   = $this->datasis->dameval("SELECT numero   FROM sfac WHERE id=$id");
		$cod_cli  = $this->datasis->dameval("SELECT cod_cli  FROM sfac WHERE id=$id");
		$monto    = $this->datasis->dameval("SELECT ROUND(iva*0.75,2)  FROM sfac WHERE id=$id");
		$factura  = $this->datasis->dameval("SELECT factura  FROM sfac WHERE id=$id");

		$anterior = $this->datasis->dameval("SELECT reiva FROM sfac WHERE id=$id");
		$usuario = addslashes($this->session->userdata('usuario'));

		if ( strlen($numero) == 14 ){
			if (  $anterior == 0 )  {
				$mSQL = "UPDATE sfac SET reiva=round(iva*0.75,2), creiva='$numero', freiva='$fecha', ereiva='$efecha' WHERE id=$id";
				$this->db->simple_query($mSQL);
				//memowrite($mSQL,"sfacreivaSFAC");

				$transac = $this->datasis->prox_sql("ntransa");
				$transac = str_pad($transac, 8, "0", STR_PAD_LEFT);

				if ($referen == 'C') {
					$saldo =  $this->datasis->dameval("SELECT monto-abonos FROM smov WHERE tipo_doc='FC' AND numero='$numfac'");
				}

				if ( $tipo_doc == 'F') {
					if ($referen == 'E') {
						// FACTURA PAGADA AL CONTADO GENERA ANTICIPO
						$mnumant = $this->datasis->prox_sql("nancli");
						$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);

						$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
						SELECT cod_cli, nombre, 'AN' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
							CONCAT('RET/IVA DE ',cod_cli,' A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
							curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
						FROM sfac WHERE id=$id";
						$this->db->simple_query($mSQL);
						$mdevo = "<h1 style='color:green;'>EXITO</h1>Retencion Guardada, Anticipo Generado por factura pagada al contado";
					} elseif ($referen == 'C') {
						// Busca si esta cancelada
						$tiposfac = 'FC';
						if ( $tipo_doc == 'D') $tiposfac = 'NC';
						$mSQL = "SELECT monto-abonos saldo FROM smov WHERE numero='$numfac' AND cod_cli='$cod_cli' AND tipo_doc='$tiposfac'";
						$saldo = $this->datasis->dameval($mSQL);
						if ( $saldo < $monto ) {  // crea anticipo
							$mnumant = $this->datasis->prox_sql("nancli");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
							SELECT cod_cli, nombre, 'AN' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
								CONCAT('APLICACION DE RETENCION A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
								curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
							FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);
							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Anticipo Generado por factura ya pagada";
							memowrite($mSQL,"sfacreivaAN");
						} else {
							$mnumant = $this->datasis->prox_sql("nccli");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, codigo, descrip, nroriva, emiriva )
								SELECT cod_cli, nombre, 'NC' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, reiva abonos, freiva vence,
								CONCAT('APLICACION DE RETENCION A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
								curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario,
								'NOCON 'codigo, 'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
								FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);

							// ABONA A LA FACTURA
							$mSQL = "UPDATE smov SET abonos=abonos+$monto WHERE numero='$numfac' AND cod_cli='$cod_cli' AND tipo_doc='$tiposfac'";
							$this->db->simple_query($mSQL);

							//Crea la relacion en ccli
							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Nota de Credito generada y aplicada a la factura";
						}
					}
					$mnumant = $this->datasis->prox_sql("ndcli");
					$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
					$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, usuario, transac, codigo, descrip, nroriva, emiriva )
						SELECT 'REIVA' cod_cli, 'RETENCION DE I.V.A. POR COMPENSAR' nombre, 'ND' tipo_doc, '$mnumant' numero, freiva fecha,
						reiva monto, 0 impuesto, 0 abonos, freiva vence, CONCAT('RET/IVA DE ',cod_cli,' A ',tipo_doc,numero) observa1,
						IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref, curdate() estampa,
						curtime() hora, '".$usuario."' usuario, '$transac' transac, 'NOCON 'codigo,
						'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
					FROM sfac WHERE id=$id";
					$this->db->simple_query($mSQL);
					memowrite($mSQL,"sfacreivaND");
				} else {
					// DEVOLUCIONES GENERA ND AL CLIENTE
					$mnumant = $this->datasis->prox_sql("ndcli");
					$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);

					$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
					SELECT cod_cli, nombre, 'ND' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
						CONCAT('RET/IVA DE ',cod_cli,' A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
						curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
					FROM sfac WHERE id=$id";
					$this->db->simple_query($mSQL);
					$mdevo = "<h1 style='color:green;'>EXITO</h1>Retencion Guardada, Anticipo Generado por factura pagada al contado";

					// Debe abonar la ND si existe un AN
					/*
					if ($referen == 'E') {
						// DEVOLUCIONES PAGADA AL CONTADO GENERA
						$mnumant = $this->datasis->prox_sql("ndcli");
						$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);

						$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
						SELECT cod_cli, nombre, 'ND' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
							CONCAT('RET/IVA DE ',cod_cli,' A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
							curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
						FROM sfac WHERE id=$id";
						$this->db->simple_query($mSQL);
						$mdevo = "<h1 style='color:green;'>EXITO</h1>Retencion Guardada, Anticipo Generado por factura pagada al contado";
					} elseif ($referen == 'C') {
						// B
						$tiposfac = 'FC';
						if ( $tipo_doc == 'D') $tiposfac = 'NC';
						$mSQL = "SELECT monto-abonos saldo FROM smov WHERE numero='$numfac' AND cod_cli='$cod_cli' AND tipo_doc='$tiposfac'";
						$saldo = $this->datasis->dameval($mSQL);
						if ( $saldo < $monto ) {  // crea anticipo
							$mnumant = $this->datasis->prox_sql("nancli");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
							SELECT cod_cli, nombre, 'AN' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
								CONCAT('APLICACION DE RETENCION A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
								curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
							FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);
							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Anticipo Generado por factura ya pagada";
							memowrite($mSQL,"sfacreivaAN");
						} else {
							$mnumant = $this->datasis->prox_sql("nccli");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, codigo, descrip, nroriva, emiriva )
								SELECT cod_cli, nombre, 'NC' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, reiva abonos, freiva vence,
								CONCAT('APLICACION DE RETENCION A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
								curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario,
								'NOCON 'codigo, 'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
								FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);

							// ABONA A LA FACTURA
							$mSQL = "UPDATE smov SET abonos=abonos+$monto WHERE numero='$numfac' AND cod_cli='$cod_cli' AND tipo_doc='$tiposfac'";
								$this->db->simple_query($mSQL);

							//Crea la relacion en ccli

							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Nota de Credito generada y aplicada a la factura";
						}
					}*/

					//Devoluciones debe crear un NC si esta en el periodo
					$mnumant = $this->datasis->prox_sql("nccli");
					$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
					$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, usuario, transac, codigo, descrip, nroriva, emiriva )
						SELECT 'REIVA' cod_cli, 'RETENCION DE I.V.A. POR COMPENSAR' nombre, 'NC' tipo_doc, '$mnumant' numero, freiva fecha,
						reiva monto, 0 impuesto, 0 abonos, freiva vence, CONCAT('RET/IVA DE ',cod_cli,' A ',tipo_doc,numero) observa1,
						IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref, curdate() estampa,
						curtime() hora, '".$usuario."' usuario, '$transac' transac, 'NOCON 'codigo,
						'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
					FROM sfac WHERE id=$id";
					$this->db->simple_query($mSQL);
					memowrite($mSQL,"sfacreivaND");

				}
			}else{
				$mdevo = "<h1 style='color:red;'>ERROR</h1>Retencion ya aplicada";
			}
		}else
			$mdevo = "<h1 style='color:red;'>ERROR</h1>Longitud del comprobante menor a 14 caracteres, corrijalo y vuelva a intentar";
		echo $mdevo;
	}

	// Reintegrar retencion de IVA
	function sfacreivaef(){
		$id     = $this->uri->segment($this->uri->total_segments());
		$reinte = 0;
		$numero = rawurldecode($this->input->post('numero'));
		$fecha  = rawurldecode($this->input->post('fecha'));
		$efecha = rawurldecode($this->input->post('efecha'));
		$caja   = rawurldecode($this->input->post('caja'));
		$cheque = rawurldecode($this->input->post('cheque'));
		$benefi = rawurldecode($this->input->post('benefi'));

		$mdevo  = "Exito";

		memowrite("efecha=$efecha, fecha=$fecha, numero=$numero, id=$id, caja=$caja, cheque=$cheque, benefi=$benefi ","sfacreivaef");

		// status de la factura
		$fecha  = substr($fecha, 6,4).substr($fecha, 3,2).substr($fecha, 0,2);
		$efecha = substr($efecha,6,4).substr($efecha,3,2).substr($efecha,0,2);

		$tipo_doc = $this->datasis->dameval("SELECT tipo_doc FROM sfac WHERE id=$id");
		$referen  = $this->datasis->dameval("SELECT referen  FROM sfac WHERE id=$id");
		$numfac   = $this->datasis->dameval("SELECT numero   FROM sfac WHERE id=$id");
		$cod_cli  = $this->datasis->dameval("SELECT cod_cli  FROM sfac WHERE id=$id");
		$monto    = $this->datasis->dameval("SELECT ROUND(iva*0.75,2)  FROM sfac WHERE id=$id");
		$anterior = $this->datasis->dameval("SELECT reiva FROM sfac WHERE id=$id");

		$usuario  = addslashes($this->session->userdata('usuario'));
		$codbanc = substr($caja,0,2);
		$verla = 0;

		if ($codbanc == '__') {
			$tbanco  = '';
			$cheque  = '';
		} else {
			$tbanco  = $this->datasis->dameval("SELECT tbanco FROM banc WHERE codbanc='$codbanc'");
			$cheque  = str_pad($cheque, 12, "0", STR_PAD_LEFT);
			$query   = "SELECT count(*) FROM bmov WHERE tipo_op='CH' AND codbanc='$codbanc' AND numero='$cheque' ";
			if ( $tbanco != 'CAJ' ) {
				$verla = $this->datasis->dameval($query);
			}
		}

		if ( $verla == 0 ) {
			if ( strlen($numero) == 14 ){
				if (  $anterior == 0 )  {
					$mSQL = "UPDATE sfac SET reiva=round(iva*0.75,2), creiva='$numero', freiva='$fecha', ereiva='$efecha' WHERE id=$id";
					$this->db->simple_query($mSQL);
					memowrite($mSQL,"sfacreivaSFAC");

					$transac = $this->datasis->prox_sql("ntransa");
					$transac = str_pad($transac, 8, "0", STR_PAD_LEFT);

					if ( $codbanc == '__' ) {   // manda a cxp
						if ( $tipo_doc == 'F' ) {
							// crea un registro en sprm
							$this->db->simple_query($mSQL);
							$mnumant = $this->datasis->prox_sql("num_nd");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO sprm (cod_prv, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, usuario, transac, codigo, descrip )
								SELECT 'REINT' cod_prv, 'REINTEGRO A CLIENTE' nombre, 'ND' tipo_doc, '$mnumant' numero, freiva fecha,
								reiva monto, 0 impuesto, 0 abonos, freiva vence, 'REINTEGRO POR RETENCION A DOCUMENTO $numfac' observa1,
									IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref, curdate() estampa,
								curtime() hora, '".$usuario."' usuario, '$transac' transac, 'NOCON 'codigo,
								'NOTA DE CONTABILIDAD' descrip
							FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);
							memowrite($mSQL,"sfacreivaCXP");

/*
							$mSQL  = "INSERT INTO bmov ( codbanc, moneda, numcuent, banco, saldo, tipo_op, numero,fecha, clipro, codcp, nombre, monto, concepto, benefi, posdata, liable, transac, usuario, estampa, hora, negreso ) ";
							$mSQL .= "SELECT '$codbanc' codbanc, b.moneda, b.numcuent, ";
							$mSQL .= "b.banco, b.saldo, IF(b.tbanco='CAJ','ND','CH') tipo_op, '$cheque' numero, ";
							$mSQL .= "a.freiva, 'C' clipro, a.cod_cli codcp, a.nombre, a.reiva monto, ";
							$mSQL .= "'REINTEGRO DE RETENCION APLICADA A FC $numfac' concepto, ";
							$mSQL .= "'$benefi' benefi, a.freiva posdata, 'S' liable, '$transac' transac, ";
							$mSQL .= "'$usuario' usuario, curdate() estampa, curtime() hora, '$negreso' negreso ";
							$mSQL .= "FROM sfac a JOIN banc b ON b.codbanc='$codbanc' ";
							$mSQL .= "WHERE a.id=$id ";
							memowrite($mSQL,"sfacreivaCH");
*/
							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Nota de Credito generada y ND en CxP por Reintero (REINT) ";
						} else {
							//Devoluciones
						}


					} else {
						if ( $tbanco == 'CAJ' ) {
							$m = 1;
							while ( $m > 0 ) {
								$cheque = $this->datasis->prox_sql("ncaja$codbanc");
								$cheque = str_pad($cheque, 12, "0", STR_PAD_LEFT);
								$m = $this->datasis->dameval("SELECT COUNT(*) FROM bmov WHERE codbanc='$codbanc' AND tipo_op='ND' AND numero='$cheque' ");
							}
						}

						$negreso = $this->datasis->prox_sql("negreso");
						$negreso = str_pad($negreso, 8, "0", STR_PAD_LEFT);

						//$numero = str_pad($numero, 8, "0", STR_PAD_LEFT);
						$saldo = 0;
						if ($referen == 'C') {
							$saldo =  $this->datasis->dameval("SELECT monto-abonos FROM smov WHERE tipo_doc='FC' AND numero='$numfac'");
						}
						if ( $tipo_doc == 'F' ) {
							// crea un registro en bmov
							$mSQL  = "INSERT INTO bmov ( codbanc, moneda, numcuent, banco, saldo, tipo_op, numero,fecha, clipro, codcp, nombre, monto, concepto, benefi, posdata, liable, transac, usuario, estampa, hora, negreso ) ";
							$mSQL .= "SELECT '$codbanc' codbanc, b.moneda, b.numcuent, ";
							$mSQL .= "b.banco, b.saldo, IF(b.tbanco='CAJ','ND','CH') tipo_op, '$cheque' numero, ";
							$mSQL .= "a.freiva, 'C' clipro, a.cod_cli codcp, a.nombre, a.reiva monto, ";
							$mSQL .= "'REINTEGRO DE RETENCION APLICADA A FC $numfac' concepto, ";
							$mSQL .= "'$benefi' benefi, a.freiva posdata, 'S' liable, '$transac' transac, ";
							$mSQL .= "'$usuario' usuario, curdate() estampa, curtime() hora, '$negreso' negreso ";
							$mSQL .= "FROM sfac a JOIN banc b ON b.codbanc='$codbanc' ";
							$mSQL .= "WHERE a.id=$id ";
							memowrite($mSQL,"sfacreivaCH");
							$this->db->simple_query($mSQL);

							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Nota de Credito generada y cargo en caja generado";
						} else {
							//Devoluciones
						}
					}
					if ( $tipo_doc == 'F' ) {
						$this->db->simple_query($mSQL);
						$mnumant = $this->datasis->prox_sql("ndcli");
						$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
						$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, usuario, transac, codigo, descrip, nroriva, emiriva )
							SELECT 'REIVA' cod_cli, 'RETENCION DE IVA POR COMPENSAR' nombre, 'ND' tipo_doc, '$mnumant' numero, freiva fecha,
							reiva monto, 0 impuesto, 0 abonos, freiva vence, 'APLICACION DE RETENCION A DOCUMENTO $numfac' observa1,
								IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref, curdate() estampa,
							curtime() hora, '".$usuario."' usuario, '$transac' transac, 'NOCON 'codigo,
							'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
						FROM sfac WHERE id=$id";
						$this->db->simple_query($mSQL);
						memowrite($mSQL,"sfacreivaND");
					} else {
						//Devoluciones
					}

				} else {
					$mdevo = "<h1 style='color:red;'>ERROR</h1>Retencion ya aplicada";
				}
			} else $mdevo = "<h1 style='color:red;'>ERROR</h1>Longitud del comprobante menor a 14 caracteres, corrijalo y vuelva a intentar";
		} else $mdevo = "<h1 style='color:red;'>ERROR</h1>Un cheque con ese numero ya existe ($cheque) ";
		echo $mdevo;
	}

	// json para llena la tabla de inventario
	function sfacsig() {
		$numa  = $this->uri->segment($this->uri->total_segments());
		$tipoa = $this->uri->segment($this->uri->total_segments()-1);

		$mSQL  = 'SELECT a.codigoa, a.desca, a.cana, a.preca, a.tota, a.iva, IF(a.pvp < a.preca, a.preca, a.pvp)  pvp, ROUND(100-a.preca*100/IF(a.pvp<a.preca,a.preca, a.pvp),2) descuento, ROUND(100-ROUND(a.precio4*100/(100+a.iva),2)*100/a.preca,2) precio4, a.detalle, a.fdespacha, a.udespacha, a.bonifica, b.id url ';
		$mSQL .= "FROM sitems a LEFT JOIN sinv b ON a.codigoa=b.codigo WHERE a.tipoa='$tipoa' AND a.numa='$numa' ";
		$mSQL .= "ORDER BY a.codigoa";

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			$retArray = array();
			foreach( $query->result_array() as  $row ) {
				$retArray[] = $row;
			}
			$data = json_encode($retArray);
			$ret = "{data:" . $data .",\n";
			$ret .= "recordType : 'array'}";
		} else {
			$ret = '{data : []}';
		}
		echo $ret;
	}

	function creadpfacf($numero){
		$this->rapyd->load('dataform');

		$form = new DataForm("ventas/sfac/creadpfac/$numero");
		$form->title('Sellecione el Almacen');

		$form->alma = new dropdownField("Almacen", 'alma');
		$form->alma->options("SELECT ubica,ubides FROM caub WHERE invfis='N' AND gasto='N'");

		$form->submit("btnsubmit","Facturar");
		$form->build_form();

		$data['content'] = $form->output;
		$data['title']   = heading("Convertir Pedido en Factura");
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function creadpfac($numero){
		$alma    =$this->input->post('alma');
		$numeroe =$this->db->escape($numero);
		$user    =$this->session->userdata('usuario');
		$nsfac   =$this->datasis->fprox_numero('nsfac');
		$transac =$this->datasis->fprox_numero('transac');
		$almae   =$this->db->escape($alma);

		/*CREA ENCABEZADO DE LA FACTURA SFAC*/
		$query="
		INSERT INTO sfac (`tipo_doc`,`numero`,`fecha`,`vence`,`vd`,`cod_cli`,`rifci`,`nombre`,`direc`,`dire1`,`referen`,`iva`,`inicial`,`totals`,`totalg`,`observa`,`observ1`,`cajero`,`almacen`,`peso`,`pedido`,`usuario`,`estampa`,`hora`,`transac`,`zona`,`ciudad`,`comision`,`exento`,`tasa`,`reducida`,`sobretasa`,`montasa`,`monredu`,`monadic`)
		SELECT 'F','$nsfac',a.fecha,DATE_ADD(a.fecha, INTERVAL (SELECT b.formap FROM scli b WHERE b.cliente=a.cod_cli) DAY) vence,
		a.vd,a.cod_cli,a.rifci,a.nombre,a.direc,a.dire1,'C' referen,a.iva,0 inicial,a.totals,a.totalg,a.observa,a.observ1,
		a.cajero,$almae,a.peso,a.numero,'$user',now() estampa,CURTIME() hora,'$transac',a.zona,a.ciudad,0,SUM(d.tota)*(d.iva=0) exento,
		ROUND(SUM(d.tota*(SELECT tasa FROM civa e ORDER BY fecha desc LIMIT 1)/100)*(d.iva=(SELECT tasa FROM civa e ORDER BY fecha desc LIMIT 1))) tasa,
		ROUND(SUM(d.tota*(SELECT redutasa FROM civa e ORDER BY fecha desc LIMIT 1)/100)*(d.iva=(SELECT redutasa FROM civa e ORDER BY fecha desc LIMIT 1))) redutasa,
		ROUND(SUM(d.tota*(SELECT sobretasa FROM civa e ORDER BY fecha desc LIMIT 1)/100)*(d.iva=(SELECT sobretasa FROM civa e ORDER BY fecha desc LIMIT 1))) sobretasa,
		ROUND(SUM(d.tota)*(d.iva=(SELECT tasa FROM civa e ORDER BY fecha desc LIMIT 1))) montasa,
		ROUND(SUM(d.tota)*(d.iva=(SELECT redutasa FROM civa e ORDER BY fecha desc LIMIT 1))) monredu,
		ROUND(SUM(d.tota)*(d.iva=(SELECT sobretasa FROM civa e ORDER BY fecha desc LIMIT 1))) monadic
		FROM pfac a
		JOIN itpfac d ON a.numero=d.numa
		WHERE a.numero=$numeroe
		";

		$this->db->query($query);
		$id_sfac=$this->db->insert_id();

		/*CREA ENCABEZADO DE LA FACTURA SFAC*/
		$query="
		INSERT INTO sitems (`tipoa`,`numa`,`codigoa`,`desca`,`cana`,`preca`,`tota`,`iva`,`fecha`,`vendedor`,`costo`,`pvp`,`cajero`,`mostrado`,`usuario`,`estampa`,`hora`,`transac`,`precio4`,`id_sfac`)
		SELECT 'F','$nsfac',d.codigoa,d.desca,d.cana,d.preca,d.tota,d.iva,CURDATE(),d.vendedor,d.costo,d.pvp,
		d.cajero,d.mostrado,'$user' usuario,NOW() estampa,CURTIME(),'$transac',c.precio4,$id_sfac idsfac
		FROM pfac a
		JOIN itpfac d ON a.numero=d.numa
		JOIN sinv c ON d.codigoa=c.codigo
		WHERE a.numero=$numeroe
		";

		$this->db->query($query);

		$query="
		INSERT IGNORE INTO smov ( cod_cli, nombre, dire1, dire2, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, estampa, usuario, hora, transac, tasa, montasa, reducida, monredu, sobretasa, monadic, exento )
		SELECT cod_cli, nombre, direc, dire1, tipo_doc, numero, fecha, totalg, iva,   0 abonos, vence,
		if(tipo_doc='D', 'DEVOLUCION EN VENTAS', 'FACTURA DE CREDITO' ) observa1, estampa, usuario, hora, transac, tasa, montasa, reducida, monredu, sobretasa, monadic, exento
		FROM sfac WHERE transac='$transac' and referen='C'
		LIMIT 1
		";

		$this->db->query($query);

		$query="
		SELECT a.codigoa,b.almacen,-1*a.cana cana
		FROM sitems a
		JOIN sfac b ON a.id_sfac=b.id
		JOIN caub c ON b.almacen=c.ubica
		WHERE b.transac='$transac'
		";

		$query=$this->db->query($query);
		foreach($query->result as $row)
		$this->datasis->sinvcarga($row->codigoa,$row->almacen,$row->cana);

		redirect("ventas/sfac/dataedit/show/$id_sfac");
	}

	function modificar(){
		$js= file_get_contents('php://input');
		$campos = json_decode($js,true);
		//$campos = $data['data'];
		$id        = $campos['id'];
		$nfiscal   = $campos['nfiscal'];
		$maqfiscal = $campos['maqfiscal'];

		//print_r($campos);
		$mSQL = $this->db->update_string("sfac", array('nfiscal'=>$campos['nfiscal'],'maqfiscal'=>$campos['maqfiscal']),"id='$id'" );
		$this->db->simple_query($mSQL);
		logusu('sfac',"FACTURACION ".$campos['id']." MODIFICADO");
		echo "{ success: true, message: 'Factura Modificado '}";
	}

	function tabla() {
		$id = $this->uri->segment($this->uri->total_segments());
		$cliente = $this->datasis->dameval("SELECT cod_cli FROM sfac WHERE id='$id'");
		$transac = $this->datasis->dameval("SELECT transac FROM sfac WHERE id='$id'");
		$salida = '';

		// Revisa formas de pago sfpa
		$mSQL = "SELECT tipo, numero, monto FROM sfpa WHERE transac='$transac' AND monto<>0";
		$query = $this->db->query($mSQL);
		if ( $query->num_rows() > 0 ){
			$salida .= "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td colspan=3>Forma de Pago</td></tr>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tipo</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row)
			{
				$salida .= "<tr>";
				$salida .= "<td>".$row['tipo']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table>";
		}

		// Cuentas por Cobrar
		$mSQL = "SELECT cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos FROM smov WHERE cod_cli='$cliente' AND abonos<>monto AND tipo_doc<>'AB' ORDER BY fecha DESC ";
		$query = $this->db->query($mSQL);
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida .= "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td colspan=3>Movimiento Pendientes en CxC</td></tr>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			$i = 1;
			foreach ($query->result_array() as $row)
			{
				if ( $i < 6 ) {
					$salida .= "<tr>";
					$salida .= "<td>".$row['tipo_doc']."</td>";
					$salida .= "<td>".$row['numero'].  "</td>";
					$salida .= "<td align='right'>".nformat($row['monto']-$row['abonos']).   "</td>";
					$salida .= "</tr>";
				}
				if ( $i == 6 ) {
					$salida .= "<tr>";
					$salida .= "<td colspan=3>Mas......</td>";
					$salida .= "</tr>";
				}
				if ( $row['tipo_doc'] == 'FC' or $row['tipo_doc'] == 'ND' or $row['tipo_doc'] == 'GI' )
					$saldo += $row['monto']-$row['abonos'];
				else
					$saldo -= $row['monto']-$row['abonos'];
				$i ++;
			}
			$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo). "</td></tr>";
			$salida .= "</table>";
		}
		$query->free_result();

		// Revisa movimiento de bancos
		$mSQL = "SELECT codbanc, numero, monto FROM bmov WHERE transac='$transac' ";
		$query = $this->db->query($mSQL);
		if ( $query->num_rows() > 0 ){
			$salida .= "<br><table width='100%' border=1>";
			$salida .= "<tr bgcolor='#e7e3e7'><td colspan=3>Movimiento en Caja o Banco</td></tr>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Bco</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row)
			{
				$salida .= "<tr>";
				$salida .= "<td>".$row['codbanc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "</table>";
		}

		echo $salida;
	}

	function sclibu(){
		$numero = $this->uri->segment(4);
		$id = $this->datasis->dameval("SELECT b.id FROM sfac a JOIN scli b ON a.cod_cli=b.cliente WHERE numero='$numero'");
		redirect('ventas/scli/dataedit/show/'.$id);
	}

	//Forma de facturacion
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$do = new DataObject('sfac');
		$do->rel_one_to_many('sitems', 'sitems', array('id'=>'id_sfac'));
		$do->rel_one_to_many('sfpa'  , 'sfpa'  , array('numero','transac'));
		$do->pointer('scli' ,'scli.cliente=sfac.cod_cli','scli.tipo AS sclitipo','left');
		$do->rel_pointer('sitems','sinv','sitems.codigoa=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');

		$edit = new DataDetails('Facturas', $do);
		$edit->on_save_redirect=false;

		$edit->set_rel_title('sitems','Producto <#o#>');
		$edit->set_rel_title('sfpa'  ,'Forma de pago <#o#>');

		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->sclitipo = new hiddenField('', 'sclitipo');
		$edit->sclitipo->db_name     = 'sclitipo';
		$edit->sclitipo->pointer     = true;
		$edit->sclitipo->insertValue = 1;

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;

		$edit->tipo_doc = new  dropdownField('Documento', 'tipo_doc');
		$edit->tipo_doc->option('F','Factura');
		$edit->tipo_doc->option('D','Devoluci&oacute;n');
		$edit->tipo_doc->style='width:150px;';
		$edit->tipo_doc->size = 5;
		$edit->tipo_doc->rule='required';

		$edit->vd = new  dropdownField ('Vendedor', 'vd');
		$edit->vd->options('SELECT vendedor, CONCAT(vendedor,\' \',nombre) nombre FROM vend ORDER BY vendedor');
		$edit->vd->style='width:150px;';
		$edit->vd->insertValue=$this->secu->getvendedor();

		$edit->almacen= new dropdownField ('Almac&eacute;n', 'almacen');
		$edit->almacen->options('SELECT ubica,ubides FROM caub WHERE gasto="N" ORDER BY ubides');
		$edit->almacen->rule='required';
		$edit->almacen->style='width:150px;';
		$alma = $this->secu->getalmacen();
		if(empty($alma)){
			$alma = $this->datasis->traevalor('ALMACEN');
		}
		$edit->almacen->insertValue=$alma;

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength=8;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->factura = new inputField('Factura', 'factura');
		$edit->factura->size = 10;
		$edit->factura->mode='autohide';
		$edit->factura->maxlength=8;
		$edit->factura->rule='condi_required|callback_chfactura';

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->readonly  = true;
		$edit->peso->size      = 10;

		$edit->cliente = new inputField('Cliente','cod_cli');
		$edit->cliente->size = 6;
		$edit->cliente->autocomplete=false;
		$edit->cliente->rule='required|existescli';

		$edit->nombre = new hiddenField('Nombre', 'nombre');
		$edit->nombre->size = 25;
		$edit->nombre->maxlength=40;
		$edit->nombre->readonly =true;
		$edit->nombre->autocomplete=false;
		$edit->nombre->rule= 'required';

		$edit->upago = new hiddenField('Ultimo pago de servicio', 'upago');
		$edit->upago->readonly =true;
		$edit->upago->autocomplete=false;

		$edit->rifci   = new hiddenField('RIF/CI','rifci');
		$edit->rifci->autocomplete=false;
		$edit->rifci->readonly =true;
		$edit->rifci->size = 15;

		$edit->direc = new hiddenField('Direcci&oacute;n','direc');
		$edit->direc->readonly =true;
		$edit->direc->size = 40;

		$edit->cajero= new dropdownField('Cajero', 'cajero');
		$edit->cajero->options('SELECT cajero,nombre FROM scaj ORDER BY nombre');
		$edit->cajero->rule ='required|cajerostatus';
		$edit->cajero->style='width:150px;';
		$edit->cajero->insertValue=$this->secu->getcajero();

		//***********************************
		//  Campos para el detalle 1 sitems
		//***********************************
		$edit->codigoa = new inputField('C&oacute;digo <#o#>', 'codigoa_<#i#>');
		$edit->codigoa->size     = 12;
		$edit->codigoa->db_name  = 'codigoa';
		$edit->codigoa->rel_id   = 'sitems';
		$edit->codigoa->rule     = 'required';

		$edit->desca = new inputField('Descripci&oacute;n <#o#>', 'desca_<#i#>');
		$edit->desca->size=36;
		$edit->desca->db_name='desca';
		$edit->desca->maxlength=50;
		$edit->desca->readonly  = true;
		$edit->desca->rel_id='sitems';

		$edit->cana = new inputField('Cantidad <#o#>', 'cana_<#i#>');
		$edit->cana->db_name  = 'cana';
		$edit->cana->css_class= 'inputnum';
		$edit->cana->rel_id   = 'sitems';
		$edit->cana->maxlength= 10;
		$edit->cana->size     = 6;
		$edit->cana->rule     = 'required|positive|callback_chcanadev[<#i#>]';
		$edit->cana->autocomplete=false;
		$edit->cana->onkeyup  ='importe(<#i#>)';
		$edit->cana->showformat ='decimal';
		$edit->cana->disable_paste=true;

		$edit->preca = new inputField('Precio <#o#>', 'preca_<#i#>');
		$edit->preca->db_name   = 'preca';
		$edit->preca->css_class = 'inputnum';
		$edit->preca->rel_id    = 'sitems';
		$edit->preca->size      = 10;
		$edit->preca->rule      = 'required|positive|callback_chpreca[<#i#>]';
		$edit->preca->readonly  = true;
		$edit->preca->showformat ='decimal';

		$edit->detalle = new hiddenField('', 'detalle_<#i#>');
		$edit->detalle->db_name  = 'detalle';
		$edit->detalle->rel_id   = 'sitems';

		$edit->tota = new inputField('Importe <#o#>', 'tota_<#i#>');
		$edit->tota->db_name='tota';
		$edit->tota->type      ='inputhidden';
		$edit->tota->size=10;
		$edit->tota->css_class='inputnum';
		$edit->tota->rel_id   ='sitems';
		$edit->tota->showformat ='decimal';

		for($i=1;$i<4;$i++){
			$obj='precio'.$i;
			$edit->$obj = new hiddenField('Precio <#o#>', $obj.'_<#i#>');
			$edit->$obj->db_name   = 'sinv'.$obj;
			$edit->$obj->rel_id    = 'sitems';
			$edit->$obj->pointer   = true;
		}

		$edit->precio4 = new hiddenField('', 'precio4_<#i#>');
		$edit->precio4->db_name   = 'precio4';
		$edit->precio4->rel_id    = 'sitems';

		$edit->itiva = new hiddenField('', 'itiva_<#i#>');
		$edit->itiva->db_name  = 'iva';
		$edit->itiva->rel_id   = 'sitems';

		$edit->sinvpeso = new hiddenField('', 'sinvpeso_<#i#>');
		$edit->sinvpeso->db_name   = 'sinvpeso';
		$edit->sinvpeso->rel_id    = 'sitems';
		$edit->sinvpeso->pointer   = true;

		$edit->sinvtipo = new hiddenField('', 'sinvtipo_<#i#>');
		$edit->sinvtipo->db_name   = 'sinvtipo';
		$edit->sinvtipo->rel_id    = 'sitems';
		$edit->sinvtipo->pointer   = true;

		//************************************************
		//fin de campos para detalle,inicio detalle2 sfpa
		//************************************************
		$edit->tipo = new  dropdownField('Tipo <#o#>', 'tipo_<#i#>');
		$edit->tipo->option('','CREDITO');
		$edit->tipo->options('SELECT tipo, nombre FROM tarjeta WHERE activo=\'S\' ORDER BY nombre');
		$edit->tipo->db_name    = 'tipo';
		$edit->tipo->rel_id     = 'sfpa';
		$edit->tipo->insertValue= 'EF';
		$edit->tipo->style      = 'width:150px;';
		$edit->tipo->onchange   = 'sfpatipo(<#i#>)';
		//$edit->tipo->rule     = 'required';

		$edit->sfpafecha = new dateonlyField('Fecha','sfpafecha_<#i#>');
		$edit->sfpafecha->rel_id   = 'sfpa';
		$edit->sfpafecha->db_name  = 'fecha';
		$edit->sfpafecha->size     = 10;
		$edit->sfpafecha->maxlength= 8;
		$edit->sfpafecha->rule ='condi_required|callback_chtipo[<#i#>]';

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
		$edit->banco->style  ='width:180px;';
		$edit->banco->rule   ='condi_required|callback_chtipo[<#i#>]';

		$edit->monto = new inputField('Monto <#o#>', 'monto_<#i#>');
		$edit->monto->db_name   = 'monto';
		$edit->monto->css_class = 'inputnum';
		$edit->monto->rel_id    = 'sfpa';
		$edit->monto->size      = 10;
		$edit->monto->rule      = 'required|mayorcero';
		$edit->monto->showformat ='decimal';
		//************************************************
		// Fin detalle 2 (sfpa)
		//************************************************

		$edit->ivat = new hiddenField('I.V.A', 'iva');
		$edit->ivat->css_class ='inputnum';
		$edit->ivat->readonly  =true;
		$edit->ivat->size      = 10;

		$edit->totals = new hiddenField('Sub-Total', 'totals');
		$edit->totals->css_class ='inputnum';
		$edit->totals->readonly  =true;
		$edit->totals->size      = 10;

		$edit->totalg = new hiddenField('Total', 'totalg');
		$edit->totalg->css_class ='inputnum';
		$edit->totalg->readonly  =true;
		$edit->totalg->size      = 10;

		$edit->observa   = new inputField('Observacion', 'observa');
		$edit->nfiscal   = new inputField('No.Fiscal', 'nfiscal');
		$edit->observ1   = new inputField('Observacion', 'observ1');
		$edit->zona      = new inputField('Zona', 'zona');
		$edit->ciudad    = new inputField('Ciudad', 'ciudad');
		$edit->exento    = new inputField('Exento', 'exento');
		$edit->maqfiscal = new inputField('Mq.Fiscal', 'maqfiscal');
		$edit->referen   = new inputField('Referencia', 'referen');
		$edit->pfac      = new hiddenField('Presupuesto', 'pfac');

		$edit->reiva     = new inputField('Retencion de IVA', 'reiva');
		$edit->creiva    = new inputField('Comprobante', 'creiva');
		$edit->freiva    = new inputField('Fecha', 'freiva');
		$edit->ereiva    = new inputField('Emision', 'ereiva');

		$edit->usuario   = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa   = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora      = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));

		$edit->buttons('add_rel','add');

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);

			echo json_encode($rt);
		}else{
			if($this->genesal){
				$conten['form']  =& $edit;
				$data['content'] =  $this->load->view('view_sfac_add', $conten);
			}else{
				$rt=array(
					'status' =>'B',
					'mensaje'=> utf8_encode(html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string))),
					'pk'     =>''
				);
				echo json_encode($rt);
			}
		}
	}

	function dataprintser($st,$uid){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Imprimir factura', 'sfac');
		$id=$edit->get_from_dataobjetct('id');
		$urlid=$edit->pk_URI();
		$sfacforma=$this->datasis->traevalor('FORMATOSFAC','Especifica el metodo a ejecutar para descarga de formato de factura en Proteo Ej. descargartxt...');
		if(empty($sfacforma)) $sfacforma='descargartxt';
		$url=site_url('formatos/'.$sfacforma.'/FACTURA'.$urlid);
		if(isset($this->back_url))
			$edit->back_url = site_url($this->back_url);
		else
			$edit->back_url = site_url('ajax/reccierraventana');
			//$edit->back_url = site_url($this->url.'dataedit/show/'.$uid);

		$edit->back_save   = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		//$edit->on_save_redirect   = false;

		//$edit->post_process('update','_post_print_update');
		$edit->pre_process('insert' ,'_pre_print_insert');
		//$edit->pre_process('update' ,'_pre_print_update');
		$edit->pre_process('delete' ,'_pre_print_delete');

		//$edit->container = new containerField('impresion','La descarga se realizara en 5 segundos, en caso de no hacerlo haga click '.anchor('formatos/descargar/FACTURA'.$urlid,'aqui'));

		$edit->nfiscal = new inputField('Control F&iacute;scal','nfiscal');
		$edit->nfiscal->rule='max_length[12]|required';
		$edit->nfiscal->size =14;
		$edit->nfiscal->maxlength =12;
		$edit->nfiscal->autocomplete=false;

		$edit->tipo_doc = new inputField('Factura','tipo_doc');
		$edit->tipo_doc->rule='max_length[1]';
		$edit->tipo_doc->size =3;
		$edit->tipo_doc->mode='autohide';
		$edit->tipo_doc->maxlength =1;

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->mode='autohide';
		$edit->numero->size =10;
		$edit->numero->in='tipo_doc';
		$edit->numero->maxlength =8;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule = 'chfecha';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;
		$edit->fecha->maxlength =8;

		$edit->cod_cli = new inputField('Cliente','cod_cli');
		$edit->cod_cli->rule='max_length[5]';
		$edit->cod_cli->size =7;
		$edit->cod_cli->mode='autohide';
		$edit->cod_cli->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[40]';
		$edit->nombre->size =42;
		$edit->nombre->mode='autohide';
		$edit->nombre->in='cod_cli';
		$edit->nombre->maxlength =40;

		$edit->rifci = new inputField('Rif/Ci','rifci');
		$edit->rifci->rule='max_length[13]';
		$edit->rifci->size =15;
		$edit->rifci->mode='autohide';
		$edit->rifci->maxlength =13;

		$total   = $edit->get_from_dataobjetct('totalg');
		$edit->totalg = new freeField('<b>Monto a pagar</b>','monto','<b id="vh_monto" style="font-size:2em">'.nformat($total).'</b>');

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);

			echo json_encode($rt);
		}else{
			$rt=array(
				'status' =>'B',
				'mensaje'=> utf8_encode(html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string))),
				'pk'     =>''
			);
			//echo json_encode($rt);
			echo $edit->output;
		}


	}


	function dataprint($st,$uid){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Imprimir factura', 'sfac');
		$id=$edit->get_from_dataobjetct('id');
		$urlid=$edit->pk_URI();
		$sfacforma=$this->datasis->traevalor('FORMATOSFAC','Especifica el metodo a ejecutar para descarga de formato de factura en Proteo Ej. descargartxt...');
		if(empty($sfacforma)) $sfacforma='descargar';
		$url=site_url('formatos/'.$sfacforma.'/FACTURA'.$urlid);
		if(isset($this->back_url))
			$edit->back_url = site_url($this->back_url);
		else
			$edit->back_url = site_url('ajax/reccierraventana');
			//$edit->back_url = site_url($this->url.'dataedit/show/'.$uid);

		$edit->back_save   = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		//$edit->on_save_redirect   = false;

		//$edit->post_process('update','_post_print_update');
		$edit->pre_process('insert' ,'_pre_print_insert');
		//$edit->pre_process('update' ,'_pre_print_update');
		$edit->pre_process('delete' ,'_pre_print_delete');

		$edit->container = new containerField('impresion','La descarga se realizara en 5 segundos, en caso de no hacerlo haga click '.anchor('formatos/descargar/FACTURA'.$urlid,'aqui'));

		$edit->nfiscal = new inputField('N&uacute;mero f&iacute;scal','nfiscal');
		$edit->nfiscal->rule='max_length[12]|required';
		$edit->nfiscal->size =14;
		$edit->nfiscal->maxlength =12;
		$edit->nfiscal->autocomplete=false;

		$edit->tipo_doc = new inputField('Factura','tipo_doc');
		$edit->tipo_doc->rule='max_length[1]';
		$edit->tipo_doc->size =3;
		$edit->tipo_doc->mode='autohide';
		$edit->tipo_doc->maxlength =1;

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->mode='autohide';
		$edit->numero->size =10;
		$edit->numero->in='tipo_doc';
		$edit->numero->maxlength =8;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule = 'chfecha';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;
		$edit->fecha->maxlength =8;

		$edit->cod_cli = new inputField('Cliente','cod_cli');
		$edit->cod_cli->rule='max_length[5]';
		$edit->cod_cli->size =7;
		$edit->cod_cli->mode='autohide';
		$edit->cod_cli->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[40]';
		$edit->nombre->size =42;
		$edit->nombre->mode='autohide';
		$edit->nombre->in='cod_cli';
		$edit->nombre->maxlength =40;

		$edit->rifci = new inputField('Rif/Ci','rifci');
		$edit->rifci->rule='max_length[13]';
		$edit->rifci->size =15;
		$edit->rifci->mode='autohide';
		$edit->rifci->maxlength =13;

		$edit->totalg = new inputField('Monto','totalg');
		$edit->totalg->rule='max_length[12]|numeric';
		$edit->totalg->css_class='inputnum';
		$edit->totalg->size =14;
		$edit->totalg->showformat='decimal';
		$edit->totalg->mode='autohide';
		$edit->totalg->maxlength =12;

		$edit->buttons('save', 'undo','back');
		$edit->build();

		$script= '<script type="text/javascript" >
		$(function() {
			setTimeout(\'window.location="'.$url.'"\',5000);
		});
		</script>';

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['script'] .= $script;
		$data['title']   = heading($this->tits);
		$this->load->view('view_ventanas', $data);
	}

	function _pre_print_insert($do){ return false;}
	function _pre_print_delete($do){ return false;}

	//Chequea que el precio de los articulos de la devolucion
	//sean los facturados y que no sean menores al precio 4
	function chpreca($val,$i){
		$tipo_doc = $this->input->post('tipo_doc');
		$codigo   = $this->input->post('codigoa_'.$i);

		if($tipo_doc == 'D'){
			$factura  = $this->input->post('factura');
			$dbfactura= $this->db->escape($factura);

			if(!isset($this->devperca)){
				$this->devpreca=array();
				$mSQL="SELECT b.codigoa,b.preca
				FROM sitems AS b
				WHERE b.numa=$dbfactura AND b.tipoa='F'";
				$query = $this->db->query($mSQL);
				foreach ($query->result() as $row){
					$ind=trim($row->codigoa);
					$this->devpreca[$ind]=$row->preca;
				}
			}

			if(isset($this->devpreca[$codigo])){
				$this->validation->set_message('chpreca', 'El art&iacute;culo '.$codigo.' se esta devolviendo por un monto distinto al facturado que fue de '.nformat($this->devpreca[$codigo]));
				if($this->devpreca[$codigo]-$val==0){
					return true;
				}
			}else{
				$this->validation->set_message('chpreca', 'El art&iacute;culo '.$codigo.' no fue facturado');
				return false;
			}
		}elseif($tipo_doc == 'F'){
			if(!isset($this->sclitipo)){
				$cliente  = $this->input->post('cod_cli');
				$this->sclitipo = $this->datasis->dameval('SELECT tipo FROM scli WHERE cliente='.$this->db->escape($cliente));
			}
			if($this->sclitipo=='5'){
				$precio4 = $this->datasis->dameval('SELECT ultimo FROM sinv WHERE codigo='.$this->db->escape($codigo));
			}else{
				$precio4 = $this->datasis->dameval('SELECT precio4*100/(100+iva) FROM sinv WHERE codigo='.$this->db->escape($codigo));
			}
			$this->validation->set_message('chpreca', 'El art&iacute;culo '.$codigo.' debe contener un precio de al menos '.nformat($precio4));
			if(empty($precio4)) $precio4=0; else $precio4=round($precio4,2);
			if($val>=$precio4){
				return true;
			}
		}
		return false;
	}

	//Chequea que la cantidad devuelta no sea mayor que la facturada
	function chcanadev($val,$i){
		$tipo_doc = $this->input->post('tipo_doc');
		$factura  = $this->input->post('factura');
		$codigo   = $this->input->post('codigoa_'.$i);

		if($tipo_doc=='D'){
			$dbfactura=$this->db->escape($factura);

			if(!isset($this->devitems)){
				$this->devitems=array();
				$mSQL="SELECT b.codigoa,b.cana,SUM(d.cana) AS dev
				FROM sitems AS b
				LEFT JOIN sfac AS c  ON b.numa=c.factura AND c.tipo_doc='D'
				LEFT JOIN sitems AS d ON c.numero=d.numa AND c.tipo_doc=d.tipoa AND b.codigoa=d.codigoa
				WHERE b.numa=$dbfactura AND b.tipoa='F'
				GROUP BY b.codigoa";
				$query = $this->db->query($mSQL);
				foreach ($query->result() as $row){
					$ind=trim($row->codigoa);
					$c=(empty($row->cana))? 0 : $row->cana;
					$d=(empty($row->dev))?  0 : $row->dev;
					$this->devitems[$ind]=$c-$d;
				}
			}
			if(isset($this->devitems[$codigo])){
				if($val <= $this->devitems[$codigo]){
					return true;
				}
				$this->validation->set_message('chcanadev', 'Esta devolviendo m&aacute;s de lo que se facturo del art&iacute;culo '.$codigo.' puede devolver m&aacute;ximo '.$this->devitems[$codigo]);
			}else{
				$this->validation->set_message('chcanadev', 'El art&iacute;culo '.$codigo.' no se puede devolver, nunca fue facturado o ya esta devuelto');
			}
			return false;
		}
		return true;
	}

	//Chequea los campos de numero y fecha en las formas de pago
	//cuando deban corresponder
	function chtipo($val,$i){
		$tipo=$this->input->post('tipo_'.$i);
		if(empty($tipo)) return true;
		$this->validation->set_message('chtipo', 'El campo %s es obligatorio');

		if(empty($val) && ($tipo=='NC' || $tipo=='DP' || $tipo=='DE'))
			return false;
		else
			return true;
	}

	//Chequea que la factura a devolver exista
	function chfactura($factura){
		$tipo_doc=$this->input->post('tipo_doc');
		$this->validation->set_message('chfactura', 'El campo %s debe contener un numero de factura v&aacute;lido');
		if($tipo_doc=='D' && empty($factura)){
			return false;
		}
		return true;
	}

	function _pre_insert($do){
		$cliente= $do->get('cod_cli');
		$tipoa  = $do->get('tipo_doc');
		$con=$this->db->query("SELECT tasa,redutasa,sobretasa FROM civa ORDER BY fecha desc LIMIT 1");
		if($con->num_rows() > 0){
			$t=$con->row('tasa');$rt=$con->row('redutasa');$st=$con->row('sobretasa');
		}else{
			$do->error_message_ar['pre_ins']='Debe cargar la tabla de IVA.';
			return false;
		}

		//Validaciones del pago
		//Totaliza los pagos
		$sfpa=$credito=0;
		$cana=$do->count_rel('sfpa');
		for($i=0;$i<$cana;$i++){
			$sfpa_tipo = $do->get_rel('sfpa','tipo',$i);
			$sfpa_monto= $do->get_rel('sfpa','monto',$i);
			$sfpa+=$sfpa_monto;
			if(empty($sfpa_tipo)) $credito+=$sfpa_monto;
		}
		$sfpa=round($sfpa,2);

		//Totaliza la factura
		$totalg=0;
		$tasa=$montasa=$reducida=$monredu=$sobretasa=$monadic=$exento=0;
		$cana=$do->count_rel('sitems');
		for($i=0;$i<$cana;$i++){
			$itcana    = $do->get_rel('sitems','cana' ,$i);
			$itpreca   = $do->get_rel('sitems','preca',$i);
			$itiva     = $do->get_rel('sitems','iva'  ,$i);
			$itimporte = $itpreca*$itcana;
			$iva       = $itimporte*($itiva/100);

			if($itiva-$t==0) {
				$tasa   +=$iva;
				$montasa+=$itimporte;
			}elseif($itiva-$rt==0) {
				$reducida+=$iva;
				$monredu +=$itimporte;
			}elseif($itiva-$st==0) {
				$sobretasa+=$iva;
				$monadic  +=$itimporte;
			}else{
				$exento+=$itimporte;
			}

			$totalg    +=$itimporte+$iva;
		}
		$totalg = round($totalg,2);
		if(abs($sfpa-$totalg)>0.01){
			$do->error_message_ar['pre_ins']='El monto del pago no coincide con el monto de la factura';
			return false;
		}

		$do->set('exento'   ,$exento   );
		$do->set('tasa'     ,$tasa     );
		$do->set('reducida' ,$reducida );
		$do->set('sobretasa',$sobretasa);
		$do->set('montasa'  ,$montasa  );
		$do->set('monredu'  ,$monredu  );
		$do->set('monadic'  ,$monadic  );

		$fecha  = $do->get('fecha');
		//Validacion del limite de credito del cliente
		if($credito>0 && $tipoa=='F'){
			$dbcliente=$this->db->escape($cliente);
			$rrow    = $this->datasis->damerow("SELECT limite,formap,credito,tolera,TRIM(socio) AS socio FROM scli WHERE cliente=$dbcliente");
			if($rrow!=false){
				if(empty($rrow['tolera']))  $rrow['tolera'] =0;
				if(empty($rrow['limite']))  $rrow['limite'] =0;
				if(empty($rrow['credito'])) $rrow['credito']='N';

				$cdias   = (empty($rrow['formap']))? 0: $rrow['formap'];
				$pcredito= $rrow['credito'];
				$tolera  = (100+$rrow['tolera'])/100;
				$socio   = $rrow['socio'];
				$limite  = $rrow['limite']*$tolera;
			}else{
				$limite  = $cdias  = $tolera = 0;
				$pcredito= 'N';
				$socio   = null;
			}

			//Chequea la cuenta propia
			$mSQL="SELECT SUM(monto*(tipo_doc IN ('FC','GI','ND'))) AS debe, SUM(monto*(tipo_doc IN ('NC','AB','AN'))) AS haber FROM smov WHERE cod_cli=$dbcliente";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				$row = $query->row();
				$saldo=$row->debe-$row->haber;
			}else{
				$saldo=0;
			}

			if($credito > ($limite-$saldo) || $cdias<=0 || $pcredito=='N'){
				$do->error_message_ar['pre_ins']='El cliente no tiene suficiente cr&eacute;dito propio';
				return false;
			}

			//Chequea la cuenta de sus asociados (si es responsables de otros clientes)
			$mSQL="SELECT SUM(a.monto*(a.tipo_doc IN ('FC','GI','ND'))) AS debe, SUM(a.monto*(a.tipo_doc IN ('NC','AB','AN'))) AS haber
				FROM smov AS a
				JOIN scli AS b ON a.cod_cli=b.socio
				WHERE b.socio=$dbcliente";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				$row = $query->row();
				$asaldo=$row->debe-$row->haber;
			}else{
				$asaldo=0;
			}

			if($credito > ($limite-$saldo-$asaldo) || $cdias<=0 || $pcredito=='N'){
				$do->error_message_ar['pre_ins']='El cliente no tiene suficiente cr&eacute;dito de grupo';
				return false;
			}

			//Chequea el credito de su maestro (si es subordinado)
			if(!empty($socio)){
				$dbsocio= $this->db->escape($socio);
				$rrow   = $this->datasis->damerow("SELECT limite,formap,credito,tolera,socio FROM scli WHERE cliente=$dbsocio");
				if($rrow!=false){
					if(empty($rrow['tolera']))  $rrow['tolera'] =0;
					if(empty($rrow['limite']))  $rrow['limite'] =0;
					if(empty($rrow['credito'])) $rrow['credito']='N';

					$mastercdias   = (empty($rrow['formap']))? 0: $rrow['formap'];
					$mastercredito = $rrow['credito'];
					$mastertolera  = (100+$rrow['tolera'])/100;
					$mastersocio   = $rrow['socio'];
					$masterlimite  = $rrow['limite']*$tolera;
				}else{
					$masterlimite = $mastercdias = $mastertolera = 0;
					$mastercredito= 'N';
					$mastersocio  = null;
				}

				$mSQL="SELECT SUM(a.monto*(a.tipo_doc IN ('FC','GI','ND'))) AS debe, SUM(a.monto*(a.tipo_doc IN ('NC','AB','AN'))) AS haber
				FROM smov AS a
				JOIN scli AS b ON a.cod_cli=b.socio
				WHERE b.socio=$dbsocio";
				$query = $this->db->query($mSQL);
				if ($query->num_rows() > 0){
					$row = $query->row();
					$mastersaldo=$row->debe-$row->haber;
				}else{
					$mastersaldo=0;
				}

				if($credito > ($masterlimite-$saldo-$mastersaldo) || $mastercdias<=0 || $mastercredito=='N'){
					$do->error_message_ar['pre_ins']='El fiador del cliente no tiene suficiente saldo';
					return false;
				}
			}
			$objdate = date_create($fecha);
			$objdate->add(new DateInterval('P'.$cdias.'D'));
			$vence   = date_format($objdate, 'Y-m-d');
			$do->set('vence',$vence);
		}else{
			$do->set('vence',$fecha);
		}
		//Fin de las validaciones

		$rrow    = $this->datasis->damerow('SELECT nombre,rifci,dire11,dire12 FROM scli WHERE cliente='.$this->db->escape($cliente));
		if($rrow!=false){
			$do->set('nombre',$rrow['nombre']);
			$do->set('direc' ,$rrow['dire11']);
			$do->set('dire1' ,$rrow['dire12']);
		}

		if($tipoa=='F'){
			$numero  = $this->datasis->fprox_numero('nsfac');
		}else{
			$numero  = $this->datasis->fprox_numero('nccli');
		}
		$transac = $this->datasis->fprox_numero('ntransa');
		$do->set('numero' ,$numero);
		$do->set('transac',$transac);
		$do->set('referen',($credito>0)? 'C': 'E');
		$vd     = $do->get('vd');
		$cajero = $do->get('cajero');
		$almacen= $do->get('almacen');
		$estampa= $do->get('estampa');
		$usuario= $do->get('usuario');
		$hora   = $do->get('hora');

		$iva=$totals=0;
		$cana=$do->count_rel('sitems');
		for($i=0;$i<$cana;$i++){
			$itcana    = $do->get_rel('sitems','cana',$i);
			$itpreca   = $do->get_rel('sitems','preca',$i);
			$itiva     = $do->get_rel('sitems','iva',$i);
			$itimporte = $itpreca*$itcana;
			$do->set_rel('sitems','tota'    ,$itimporte,$i);
			//$do->set_rel('sitems','mostrado',$itimporte*(1+($itiva/100)),$i);
			$do->set_rel('sitems','mostrado',0,$i);

			$iva    +=$itimporte*($itiva/100);
			$totals +=$itimporte;

			$do->set_rel('sitems','numa'    ,$numero ,$i);
			$do->set_rel('sitems','tipoa'   ,$tipoa  ,$i);
			$do->set_rel('sitems','transac' ,$transac,$i);
			$do->set_rel('sitems','fecha'   ,$fecha  ,$i);
			$do->set_rel('sitems','vendedor',$vd     ,$i);
			$do->set_rel('sitems','usuario' ,$usuario,$i);
			$do->set_rel('sitems','estampa' ,$estampa,$i);
			$do->set_rel('sitems','hora'    ,$hora   ,$i);
		}
		$totalg = $totals+$iva;

		$cana=$do->count_rel('sfpa');
		for($i=0;$i<$cana;$i++){
			$sfpatip   = $do->get_rel('sfpa', 'tipo',$i);
			if(!empty($sfpatip)){
				$sfpatipo  = $do->get_rel('sfpa', 'tipo_doc',$i);
				$sfpa_monto= $do->get_rel('sfpa','monto'    ,$i);
				if($tipoa=='D'){
					$sfpa_monto *= -1;
				}

				if($sfpatipo=='EF') $do->set_rel('sfpa', 'fecha' , $fecha , $i);
				$do->set_rel('sfpa','tipo_doc' ,($tipoa=='F')? 'FC':'DE',$i);
				$do->set_rel('sfpa','transac'  ,$transac   ,$i);
				$do->set_rel('sfpa','vendedor' ,$vd        ,$i);
				$do->set_rel('sfpa','cod_cli'  ,$cliente   ,$i);
				$do->set_rel('sfpa','f_factura',$fecha     ,$i);
				$do->set_rel('sfpa','fecha'    ,$fecha     ,$i);
				$do->set_rel('sfpa','cobrador' ,$cajero    ,$i);
				$do->set_rel('sfpa','numero'   ,$numero    ,$i);
				$do->set_rel('sfpa','almacen'  ,$almacen   ,$i);
				$do->set_rel('sfpa','usuario'  ,$usuario   ,$i);
				$do->set_rel('sfpa','estampa'  ,$estampa   ,$i);
				$do->set_rel('sfpa','hora'     ,$hora      ,$i);
				$do->set_rel('sfpa','monto'    ,$sfpa_monto,$i);
			}else{
				$do->rel_rm('sfpa',$i);
			}
		}

		$do->set('inicial',($credito>0)? $totalg-$credito : 0);
		$do->set('totals' ,round($totals ,2));
		$do->set('totalg' ,round($totalg ,2));
		$do->set('iva'    ,round($iva    ,2));

		$this->pfac = $_POST['pfac'];
		$do->rm_get('pfac');

		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='No se pueden modificar facturas';
		return false;
	}

	function _anular($numero,$tipo_doc){
		$mSQL="DELETE FROM sfpa WHERE tipo_doc=$dbtipo_doc AND numero=$dbnumero";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sfac'); }

		$mSQL="UPDATE sfac SET tipo_doc='X' WHERE tipo_doc=$dbtipo_doc AND numero=$dbnumero";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sfac'); }

		$mSQL="UPDATE sitems SET tipoa='X' WHERE tipoa=$dbtipo_doc AND numa=$dbnumero";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sfac'); }

		//Descuenta de inventario
	}

	function _pre_delete($do){
				$do = new DataObject('sfac');
		$do->rel_one_to_many('sitems', 'sitems', array('id'=>'id_sfac'));
		$do->rel_one_to_many('sfpa'  , 'sfpa'  , array('numero','transac'));

		$do->load($id);

		$tipo_doc = $do->get('tipo_doc');
		$numero   = $do->get('numero');
		$fecha    = $do->get('fecha');
		$referen  = $do->get('referen');
		$cajero   = $do->get('cajero');
		$inicial  = $do->get('inicial');

		$dbtipo_doc = $this->db->escape($tipo_doc);
		$dbnumero   = $this->db->escape($numero);
		$dbfecha    = $this->db->escape($fecha);
		$hoy        = date('Y-m-d');

		if($tipo_doc=='F'){
			if($refere=='C' && $inicial==0){
				$mSQL ="SELECT abono FROM smov WHERE tipo_doc=$dbtipo_doc AND numero=$dbnumero AND fecha=$dbfecha";
				$abono=$this->datasis->dameval($mSQL);
				if($abono==0){
					//Anula la factura
					$this->_anular($numero,$tipo_doc);
				}else{
					$do->error_message_ar['pre_del']='No se puede anular la factura por tener abonos.';
					return false;
				}
			}elseif($fecha == $hoy){
				//Anula la factura
				$this->_anular($numero,$tipo_doc);
			}else{
				$do->error_message_ar['pre_del']='No se puede anular una factura pagada que no sea de hoy.';
				return false;
			}
		}
		$do->error_message_ar['pre_del']='Factura '.$numero.' anulada';
		return false;
	}

	function _post_insert($do){
		$numero  = $do->get('numero');
		$fecha   = $do->get('fecha');
		$vence   = $do->get('vence');
		$totneto = $do->get('totalg');
		$hora    = $do->get('hora');
		$usuario = $do->get('usuario');
		$transac = $do->get('transac');
		$nombre  = $do->get('nombre');
		$cod_cli = $do->get('cod_cli');
		$estampa = $do->get('estampa');
		$anticipo= round(floatval($do->get('inicial')),2);
		$referen = $do->get('referen');
		$tipo_doc= $do->get('tipo_doc');
		$iva     = $do->get('iva');
		$direc   = $do->get('direc');
		$dire1   = $do->get('dire1');

		if($referen=='C'){
			$error   = 0;

			if($tipo_doc=='F'){
				//Inserta en smov
				$data=array();
				$data['cod_cli']  = $cod_cli;
				$data['nombre']   = $nombre;
				$data['dire1']    = $direc;
				$data['dire2']    = $dire1;
				$data['tipo_doc'] = 'FC';
				$data['numero']   = $numero;
				$data['fecha']    = $fecha;
				$data['monto']    = $totneto;
				$data['impuesto'] = $iva;
				$data['abonos']   = $anticipo;
				$data['vence']    = $vence;
				$data['tipo_ref'] = '';
				$data['num_ref']  = '';
				$data['observa1'] = 'FACTURA DE CREDITO';
				$data['estampa']  = $estampa;
				$data['hora']     = $hora;
				$data['transac']  = $transac;
				$data['usuario']  = $usuario;
				$data['codigo']   = 'NOCON';
				$data['descrip']  = 'NOTA DE CONTABILIDAD';

				$sql= $this->db->insert_string('smov', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'sfac'); $error++;}

				//Chequea si debe crear el abono

				if($anticipo>0){
					$mnumab = $this->datasis->fprox_numero('nabcli');

					$data=array();
					$data['cod_cli']  = $cod_cli;
					$data['nombre']   = $nombre;
					$data['dire1']    = $direc;
					$data['dire2']    = $dire1;
					$data['tipo_doc'] = 'AB';
					$data['numero']   = $mnumab;
					$data['fecha']    = $fecha;
					$data['monto']    = $anticipo;
					$data['impuesto'] = 0;
					$data['vence']    = $fecha;
					$data['tipo_ref'] = 'FC';
					$data['num_ref']  = $numero;
					$data['observa1'] = 'ABONO POR INCIAL DE FACTURA '.$numero;
					$data['usuario']  = $usuario;
					$data['estampa']  = $estampa;
					$data['hora']     = $hora;
					$data['transac']  = $transac;
					$data['fecdoc']   = $fecha;

					$mSQL = $this->db->insert_string('smov', $data);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'sfac'); }

					//Aplica la AB a la FC
					$data=array();
					$data['numccli']    = $mnumab; //numero abono
					$data['tipoccli']   = 'AB';
					$data['cod_cli']    = $cod_cli;
					$data['tipo_doc']   = ($tipo_doc=='F')? 'FC' : 'DV';
					$data['numero']     = $numero;
					$data['fecha']      = $fecha;
					$data['monto']      = $totneto;
					$data['abono']      = $anticipo;
					$data['ppago']      = 0;
					$data['reten']      = 0;
					$data['cambio']     = 0;
					$data['mora']       = 0;
					$data['transac']    = $transac;
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['usuario']    = $usuario;
					$data['reteiva']    = 0;
					$data['nroriva']    = '';
					$data['emiriva']    = '';
					$data['recriva']    = '';

					$mSQL = $this->db->insert_string('itccli', $data);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'sfac');}
				}
			}else{ //Si es devolucion
				$factura   = $do->get('factura');
				$dbfactura = $factura;
				$debe      = $this->datasis->dameval("SELECT monto-abonos FROM smov WHERE tipo_doc='FC' AND numero=$dbfactura");
				$haber     = $totneto;
				if(empty($debe)) $debe=0;

				$saldo  = $debe-$haber;

				//Si se le debe hace un anticipo
				if($saldo<0){
					$mnumant = $this->datasis->fprox_numero('nancli');

					$data=array();
					$data['cod_cli']    = $cod_cli;
					$data['nombre']     = $nombre;
					$data['dire1']      = $direc;
					$data['dire2']      = $dire1;
					$data['tipo_doc']   = 'AN';
					$data['numero']     = $mnumant;
					$data['fecha']      = $estampa;
					$data['monto']      = abs($saldo);
					$data['impuesto']   = 0;
					$data['abonos']     = 0;
					$data['vence']      = $fecha;
					$data['tipo_ref']   = 'NC';
					$data['num_ref']    = $numero;
					$data['observa1']   = 'POR DEVOLUCION DE FACTURA '.$factura;
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['transac']    = $transac;
					$data['usuario']    = $usuario;
					$data['codigo']     = 'NOCON';
					$data['descrip']    = 'NOTA DE CONTABILIDAD';

					$mSQL = $this->db->insert_string('smov', $data);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'sfac');}
				}

				if($debe>0){
					$data=array();
					$data['cod_cli']    = $cod_cli;
					$data['nombre']     = $nombre;
					$data['dire1']      = $direc;
					$data['dire2']      = $dire1;
					$data['tipo_doc']   = 'NC';
					$data['numero']     = $numero;
					$data['fecha']      = $fecha;
					$data['monto']      = $debe;
					$data['impuesto']   = $iva;
					$data['abonos']     = 0;
					$data['vence']      = $fecha;
					$data['tipo_ref']   = 'DV';
					$data['num_ref']    = $numero;
					$data['observa1']   = 'POR DEVOLUCION DE '.$factura ;
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['transac']    = $transac;
					$data['usuario']    = $usuario;
					$data['codigo']     = 'NOCON';
					$data['descrip']    = 'NOTA DE CONTABILIDAD';

					$mSQL = $this->db->insert_string('smov', $data);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'sfac');}

					//Aplica la NC a la FC
					$data=array();
					$data['numccli']    = $numero; //numero abono
					$data['tipoccli']   = 'NC';
					$data['cod_cli']    = $cod_cli;
					$data['tipo_doc']   = 'FC';
					$data['numero']     = $factura;
					$data['fecha']      = $fecha;
					$data['monto']      = $debe;
					$data['abono']      = $debe;
					$data['ppago']      = 0;
					$data['reten']      = 0;
					$data['cambio']     = 0;
					$data['mora']       = 0;
					$data['transac']    = $transac;
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['usuario']    = $usuario;
					$data['reteiva']    = 0;
					$data['nroriva']    = '';
					$data['emiriva']    = '';
					$data['recriva']    = '';

					$sql= $this->db->insert_string('itccli', $data);
					$ban=$this->db->simple_query($sql);
					if($ban==false){ memowrite($sql,'sfac'); $error++;}
				}

				//Chequea si es una venta vehicular
				if($this->db->table_exists('sinvehiculo')){
					$dbfactura=$this->db->escape($factura);
					$id=$this->datasis->dameval("SELECT id FROM sfac WHERE numero=$dbfactura AND tipo_doc='F'");
					if(!empty($id)){
						$this->db->simple_query("UPDATE sinvehiculo SET id_sfac=NULL WHERE id_sfac=$id");
					}
				}
			}
		}

		//Descuento del inventario
		$almacen=$do->get('almacen');
		$dbalma = $this->db->escape($almacen);
		$cana=$do->count_rel('sitems');
		for($i=0;$i<$cana;$i++){
			$itcana    = $do->get_rel('sitems','cana',$i);
			$itcodigoa = $do->get_rel('sitems','codigoa',$i);
			$dbcodigoa = $this->db->escape($itcodigoa);

			$factor=($tipo_doc=='F')? -1:1;
			$sql="INSERT IGNORE INTO itsinv (alma,codigo,existen) VALUES ($dbalma,$dbcodigoa,0)";
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'sfac'); $error++;}

			$sql="UPDATE itsinv SET existen=existen+$factor*$itcana WHERE codigo=$dbcodigoa AND alma=$dbalma";
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'sfac'); $error++;}

			$sql="UPDATE sinv   SET existen=existen+$factor*$itcana WHERE codigo=$dbcodigoa";
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'sfac'); $error++;}

		}

		//Si viene de pfac
		if(strlen($this->pfac)>7 && $tipo_doc == 'F'){
			$this->db->where('numero', $this->pfac);
			$this->db->update('pfac', array('factura' => $numero,'status' => 'C'));
			$dbpfac=$this->db->escape($this->pfac);

			$sql="UPDATE itpfac AS c JOIN sinv   AS d ON d.codigo=c.codigoa
			SET d.exord=IF(d.exord>c.cana,d.exord-c.cana,0)
			WHERE c.numa = $dbpfac";
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'sfac'); $error++;}
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits ${tipo_doc}${numero}");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$numero   = $do->get('numero');
		$tipo_doc = $do->get('tipo_doc');

		$primary =implode(',',$do->pk);
		logusu($do->table,"Anulo ${tipo_doc}${numero} $this->tits $primary ");
	}

	function creafrompfac($numero,$status=null){
		$this->_url= $this->url.'dataedit/insert';

		$sel=array('a.cod_cli','b.nombre','b.tipo','b.rifci','b.dire11 AS direc'
		,'a.totals','a.iva','a.totalg','TRIM(a.factura) AS factura');
		$this->db->select($sel);
		$this->db->from('pfac AS a');
		$this->db->join('scli AS b','a.cod_cli=b.cliente');
		$this->db->where('a.numero',$numero);
		$this->db->where('a.status','P');
		$query = $this->db->get();

		if ($query->num_rows() > 0 && $status=='create'){
			$row = $query->row();
			if(empty($row->factura)){
				$_POST=array(
					'btn_submit' => 'Guardar',
					'fecha'      => inputDateFromTimestamp(mktime(0,0,0)),
					'cajero'     => $this->secu->getcajero(),
					'vd'         => $this->secu->getvendedor(),
					'almacen'    => $this->secu->getalmacen(),
					'tipo_doc'   => 'F',
					'factura'    => '',
					'cod_cli'    => $row->cod_cli,
					'sclitipo'   => $row->tipo,
					'nombre'     => rtrim($row->nombre),
					'rifci'      => $row->rifci,
					'direc'      => rtrim($row->direc),
					'totals'     => $row->totals,
					'iva'        => $row->iva,
					'totalg'     => $row->totalg,
					'pfac'       => $numero,
				);

				$itsel=array('a.codigoa','b.descrip AS desca','a.cana','a.preca','a.tota','b.iva',
				'b.precio1','b.precio2','b.precio3','b.precio4','b.tipo','b.peso');
				$this->db->select($itsel);
				$this->db->from('itpfac AS a');
				$this->db->join('sinv AS b','b.codigo=a.codigoa');
				$this->db->where('a.numa',$numero);
				$this->db->where('a.cana >','0');
				$qquery = $this->db->get();
				$i=0;

				foreach ($qquery->result() as $itrow){
					$_POST["codigoa_$i"]  = rtrim($itrow->codigoa);
					$_POST["desca_$i"]    = rtrim($itrow->desca);
					$_POST["cana_$i"]     = $itrow->cana;
					$_POST["preca_$i"]    = $itrow->preca;
					$_POST["tota_$i"]     = $itrow->tota;
					$_POST["precio1_$i"]  = $itrow->precio1;
					$_POST["precio2_$i"]  = $itrow->precio2;
					$_POST["precio3_$i"]  = $itrow->precio3;
					$_POST["precio4_$i"]  = $itrow->precio4;
					$_POST["itiva_$i"]    = $itrow->iva;
					$_POST["sinvpeso_$i"] = $itrow->peso;
					$_POST["sinvtipo_$i"] = $itrow->tipo;
					$_POST["detalle_$i"]  = '';
					$i++;
				}

				//sfpa
				$i=0;
				$_POST["tipo_$i"]      = '';
				$_POST["sfpafecha_$i"] = '';
				$_POST["num_ref_$i"]   = '';
				$_POST["banco_$i"]     = '';
				$_POST["monto_$i"]     = 0;

				$this->dataedit();
			}else{
				$url='ventas/pfaclitemayor/filteredgrid';
				$this->rapyd->uri->keep_persistence();
				$persistence = $this->rapyd->session->get_persistence($url, $this->rapyd->uri->gfid);
				$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : $url;

				$data['content'] = 'Pedido ya fue facturado'.br();
				$data['content'].= anchor($back,'Regresar');
				$data['head']    = $this->rapyd->get_head();
				$data['title']   = heading('Actualizar compra');
				$this->load->view('view_ventanas', $data);
			}
		}
	}

	function instalar(){
		$campos = $this->db->list_fields('sfac');

		if(!in_array('ereiva'  ,$campos)){
			$this->db->simple_query("ALTER TABLE sfac ADD ereiva DATE AFTER freiva");
		}
		if(!in_array('entregado'  ,$campos)){
			$this->db->simple_query("ALTER TABLE sfac ADD entregado DATE ");
			$this->db->simple_query("UPDATE sfac SET entregado=fecha");
		}

		if(!in_array('comiadi'  ,$campos)){
			$this->db->simple_query("ALTER TABLE sfac ADD comiadi DECIMAL(10,2) DEFAULT 0 ");
		}


		if(!in_array('upago'  ,$campos)){
			$this->db->query("ALTER TABLE sfac ADD upago INT(10)");
		}
	}
}
