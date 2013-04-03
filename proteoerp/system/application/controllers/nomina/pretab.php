<?php
class Pretab extends Controller {
	var $mModulo = 'PRETAB';
	var $titp    = 'PRENOMINA';
	var $tits    = 'PRENOMINA';
	var $url     = 'nomina/pretab/';

	function Pretab(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PRETAB', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('pretab','id') ) {
			$this->db->simple_query('ALTER TABLE pretab DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE pretab ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE pretab ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->creaintramenu(array('modulo'=>'716','titulo'=>'Prenomina','mensaje'=>'Prenomina','panel'=>'TRANSACCIONES','ejecutar'=>'nomina/pretab','target'=>'popu','visible'=>'S','pertenece'=>'7','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 900, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	//  Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'genepre',  'img'=>'images/star.png', 'alt'=>'Genera Prenomina',     'label'=>'Genera Prenomina'));
		$grid->wbotonadd(array('id'=>'respalda', 'img'=>'images/star.png', 'alt'=>'Respaldar Prenominas', 'label'=>'Respaldar/Recuperar'));
		$grid->wbotonadd(array('id'=>'genenom',  'img'=>'images/star.png', 'alt'=>'Actualiar Nomina',        'label'=>'Genera Nomina'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita', 'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' , 'title'=>'Mostrar Registro'),
			array('id'=>'fborra', 'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('PRETAB', 'JQ');
		$param['otros']       = $this->datasis->otros('PRETAB', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	//  Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		// Prepara Prenomina
		$bodyscript .= '
		$("#genepre").click( function() {
			$.post("'.base_url().'nomina/prenom/",
			function(data){
				$("#fedita").dialog( {height: 230, width: 450, title: "Generar Pre-Nomina"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';

		// Guarda Nomina
/*
		$bodyscript .= '
		$("#genenom").click( function() {
			$.prompt("Generar Nomina", {
				buttons: { Generar: true, Cancelar: false },
				submit: function(e,v,m,f){
					if (v) {
						$.post("'.site_url($this->url.'nomina').'/", 
							function(data){
								//jQuery.prompt.getStateContent(\'state1\').find(\'#in_prom\').text(data);									
								//jQuery.prompt.goToState(\'state1\');
								//jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
								alert(data);
						});
					}
				}
			});
		});
		';
*/


		// Respaldar y Recuperar Nomina
		$bodyscript .= '
		var mguardanom = 
		{
			state0: {
				html: "<h1>Guarda la Nomina</h1>Guarda la nomina al historico y genera los movimientos correspondientes en cuentas por cobrar y pagar.",
				buttons: { Generar: true, Cancelar: false },
				submit: function(e,v,m,f){
					mnuevo = f.mcodigo;
					if (v) {
						$.post("'.site_url($this->url.'nomina').'/", 
							function(data){
								jQuery.prompt.getStateContent(\'state1\').find(\'#in_prom1\').text(data);
								jQuery.prompt.goToState(\'state1\');
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
						});
						return false;
					} 
				}
			},
			state1: { 
				html: "<h1>Resultado</h1><span id=\'in_prome1\'></span>",
				focus: 1,
				buttons: { Ok:true }
			}		
		};

		$("#genenom").click( function() 
		{
			$.prompt(mguardanom);
		});
		';


		//Busca nominas respaldadas
		$query = $this->db->query("SHOW TABLES LIKE 'PRENOM%'");
		$respaldos = '';
		if ( $query->num_rows() > 0 ) {
			$respaldos .= '<center><select id=\'mtabla\' name=\'mtabla\'>';
			$respaldos .= '<option value=\'\'>Seleccione un Respaldo</option>';
			foreach( $query->result_array() as $row ) {
				$aa    = each($row);
				$tabla = substr($aa[1],6);
				$respaldos .= '<option value=\''.$aa[1].'\'>';
				$respaldos .= dbdate_to_human($this->datasis->dameval("SELECT fecha FROM ".$aa[1]." limit 1"))." ";
				$respaldos .= $this->datasis->dameval("SELECT CONCAT(codigo,' ',nombre) nomina FROM noco WHERE codigo='$tabla'");
				$respaldos .= '</option>';
			}
			$respaldos .= '</select></center>';
		}

		// Respaldar y Recuperar Nomina
		$bodyscript .= '
		var mcome = "<h1>Respaldar y Recuperar Pre-Nominas</h1>"+
			"<p>Si va a respaldar solo presione el boton de RESPALDAR.</p>"+
			"<p>Para traer un respaldo seleccionelo y presione el boton RECUPERAR </p>'.$respaldos.'";

		var mcontenido = 
		{
			state0: {
				html: mcome,
				buttons: { Respaldar:1, Recuperar:2, salir:false},
				submit: function(e,v,m,f){
					mnuevo = f.mcodigo;
					if (v == 1 ) {
						$.post("'.site_url($this->url.'respalda').'/", 
							function(data){
								jQuery.prompt.getStateContent(\'state1\').find(\'#in_prom\').text(data);									
								jQuery.prompt.goToState(\'state1\');
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
						});
						return false;
						
					} else if ( v == 2 ) {
						$.post("'.site_url($this->url.'recupera').'/"+f.mtabla,
							function(data){ 
								$.prompt.getStateContent(\'state1\').find(\'#in_prome\').text(data);									
								$.prompt.goToState(\'state1\');
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
						});
						return false;
					}
				}
			},
			state1: { 
				html: "<h1>Resultado</h1><span id=\'in_prome\'></span>",
				focus: 1,
				buttons: { Ok:true }
			}		
		};

		$("#respalda").click( function() 
		{
			$.prompt(mcontenido);
		});
		';


		$bodyscript .= '
		function frecibo( id ){
			$.ajax({
				url: "'.base_url().$this->url.'recibo/"+id,
				success: function(msg){
					$("#ladicional").html(msg);
				}
			});
		};
		';


		$bodyscript .= '
		function pretabadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function pretabedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function pretabshow(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function pretabdel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								apprise("Registro eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
							}else{
								apprise("Registro no se puede eliminado");
							}
						}catch(e){
							$("#fborra").html(data);
							$("#fborra").dialog( "open" );
						}
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		//Wraper de javascript
		$bodyscript .= '
		$(function(){
			$("#dialog:ui-dialog").dialog( "destroy" );
			var mId = 0;
			var montotal = 0;
			var ffecha = $("#ffecha");
			var grid = jQuery("#newapi'.$grid0.'");
			var s;
			var allFields = $( [] ).add( ffecha );
			var tips = $( ".validateTips" );
			s = grid.getGridParam(\'selarrrow\');
			';


		$titulo = $this->datasis->dameval("SELECT concat_ws(' ',b.codigo, b.nombre, 'Fecha:', a.fecha) contrato FROM prenom a JOIN noco b ON a.contrato=b.codigo LIMIT 1");

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 500, width: 700, modal: true, title: "'.$titulo.'",
			buttons: {
				"Guardar": function() {
					var bValid = true;
					var murl = $("#df1").attr("action");
					allFields.removeClass( "ui-state-error" );
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url: murl,
						data: $("#df1").serialize(),
						success: function(r,s,x){
							try{
								var json = JSON.parse(r);
								if (json.status == "A"){
									$.post("'.base_url().'nomina/prenom/calcula/"+json.pk);
									apprise("Registro Guardado");
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									'.$this->datasis->jwinopen(site_url('formatos/ver/PRETAB').'/\'+res.id+\'/id\'').';
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
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fshow").html("");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				$("#fshow").html("");
			}
		});';

		$bodyscript .= '
		$("#fborra").dialog({
			autoOpen: false, height: 300, width: 400, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fborra").html("");
					jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
				$("#fborra").html("");
			}
		});';

		$bodyscript .= '});'."\n";

		$bodyscript .= "\n</script>\n";
		$bodyscript .= "";
		return $bodyscript;
	}


	//******************************************************************
	//  Resumen rapido
	//
	function recibo( $id ) {

		$row = $this->datasis->damereg("SELECT a.codigo, a.nombre, CONCAT(b.nacional,b.cedula) ci, b.enlace FROM pretab a JOIN pers b ON a.codigo=b.codigo WHERE a.id=$id");
		$codigo  = $row['codigo'];
		$nombre  = $row['nombre'];
		$cedula  = $row['ci'];
		$cod_cli = $row['enlace'];
		$fecha   = date('Y-m-d');
		$mPRESTA = 0;
		$mSALDO  = 0;


		$mSQL = "SELECT a.concepto, b.descrip, a.tipo, a.monto, a.valor, a.fecha FROM prenom a JOIN conc b ON a.concepto=b.concepto WHERE MID(a.concepto,1,1)<>'9' AND a.valor<>0 AND a.codigo=".$this->db->escape($codigo)." ORDER BY tipo, codigo ";
		$query = $this->db->query($mSQL);

		//$data = $query->row();
		$salida = '';
		$salida  .= '<table width="90%" style="background:#FBEC88;" align="center">';
		$salida  .= '<tr><td>Cod: '.$codigo.'</td><td>C.I. '.$cedula.'</td></tr>';
		$salida  .= '<tr><td colspan="2">'.$nombre.'</td></tr>';
		$salida  .= '</table>';
		
		$salida  .= '<table width="90%" border="1" align="center" cellspacing="0" cellpadding="0">';
		$total = 0;

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if ( $row->valor < 0 )
					$salida .= "<tr><td>".$row->descrip."</td><td align='right' style='color:red;'>".nformat($row->valor)."</td></tr>\n";
				else
					$salida .= "<tr><td>".$row->descrip."</td><td align='right'>".nformat($row->valor)."</td></tr>\n";

				$total += $row->valor;
				$fecha = $row->fecha;
			}
		}
		$salida .= "<tr style='background:#BAF202;'><td>Total a Pagar</td><td align='right'>".nformat($total)."</td></tr>\n";
		$salida .= "</table>\n";


		// PRESTAMOS
		if ( !empty($cod_cli) ){ 
			$mSQL  = "SELECT a.tipo_doc, a.numero, b.monto, b.abonos, a.cuota, b.monto-b.abonos saldo ";
			$mSQL .= "FROM pres a JOIN smov b ON a.cod_cli=b.cod_cli AND a.tipo_doc=b.tipo_doc AND a.numero=b.numero ";
			$mSQL .= "WHERE a.cod_cli='".$cod_cli."' AND b.monto>b.abonos AND a.apartir<='".$fecha."'";
			$query = $this->db->query($mSQL);

			if ($query->num_rows() > 0){
				$salida .= '<table width="90%" border="0" align="center" style="border:1px solid; background:#E4E4E4;">';
				$salida .= '<tr><td colspan="3" align="center">PRESTAMOS</td></tr>';
				foreach ($query->result() as $row){
					$salida .= '<tr><td>'.$row->tipo_doc.$row->numero.'</td><td>'.nformat($row->saldo).'</td><td>'.nformat($row->cuota).'</td></tr>';
					$mSALDO += $row->cuota;
				}
				$salida .= "<tr style='background:#BAF202;'><td>Neto a Pagar</td><td align='right' colspan='2'>".nformat($total-$mSALDO)."</td></tr>\n";
				$salida .= "</table>\n";

			}

		}


		//$salida .= '<table width="90%" border="0" align="center" style="border:1px solid; background:#E4E4E4;"><tr>';
		//$salida .= '<td align="center"><a href="#" onclick="fresumen('.$id.','.$anterior.')"> '.img('images/arrow_left.png').'</a></td>';
		//$salida .= '<td align="center"><a href="#" onclick="crecalban()" >RECALCULAR</a></td>';
		//$salida .= '<td align="center"><a href="#" onclick="fresumen('.$id.','.$proximo.')">'.img('images/arrow_right.png').'</a>';
		//$salida .= "</td></tr></table>\n";

		echo $salida;
	}


	//******************************************************************
	// Respalda Nomina Activa
	function respalda(){
		$mCONTRATO = $this->datasis->dameval("SELECT TRIM(contrato) contrato FROM prenom LIMIT 1");
		$mTABLA    = strtoupper("PRENOM".$mCONTRATO);
		$this->db->query("DROP TABLE IF EXISTS ".$mTABLA);
		$this->db->query("CREATE TABLE ".$mTABLA." SELECT * FROM prenom");
		echo "Nomina Respaldada ".$mCONTRATO;
		
	}


	//******************************************************************
	// Recupera Nomina Guardada
	function recupera($nomina){
		$this->load->library('pnomina');
		$this->db->query('TRUNCATE TABLE prenom');
		$this->db->query("INSERT INTO prenom SELECT * FROM $nomina");
		$this->pnomina->creapretab();
		$this->pnomina->llenapretab();
		$mreg = $this->datasis->damereg("SELECT contrato, fecha FROM prenom LIMIT 1");
		echo "Nomina Restaurada para el Contrato ".$mreg['contrato']." de Fecha ".dbdate_to_human($mreg["fecha"]);
	}


	//******************************************************************
	//  Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('frec');
		$grid->label('Frec');
		$grid->params(array(
			'search'        => 'true',
			'hidden'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('total');
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

		$query = $this->db->query("DESCRIBE pretab");
		$i = 0;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if ( substr($row->Field,0,1) == 'c' && $row->Field != 'codigo' ) {

					$etiq = $this->datasis->dameval("SELECT CONCAT(TRIM(encab1), ' ', encab2 ) encabeza FROM conc WHERE concepto=".$this->db->escape(substr($row->Field,1,4)));
					$grid->addField($row->Field);
					$grid->label($etiq);
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
				}
			}
		}

		$titulo = $this->datasis->dameval("SELECT concat_ws(' ',b.codigo, b.nombre, 'Fecha:', a.fecha) contrato FROM prenom a JOIN noco b ON a.contrato=b.codigo LIMIT 1");
		$cana = $this->datasis->dameval("SELECT count(*) FROM pretab");

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($titulo.' Trabajadores:'.$cana);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
		function(id){
			if (id){
				var ret = jQuery(gridId1).jqGrid(\'getRowData\',id);
				frecibo(id);
			}
		}'
		);


		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('PRETAB','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('PRETAB','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PRETAB','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PRETAB','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: pretabadd, editfunc: pretabedit, delfunc: pretabdel, viewfunc: pretabshow");

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
		$mWHERE = $grid->geneTopWhere('pretab');

		$response   = $grid->getData('pretab', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion
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
				$check = $this->datasis->dameval("SELECT count(*) FROM pretab WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('pretab', $data);
					echo "Registro Agregado";

					logusu('PRETAB',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM pretab WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM pretab WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE pretab SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("pretab", $data);
				logusu('PRETAB',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('pretab', $data);
				logusu('PRETAB',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM pretab WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pretab WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM pretab WHERE id=$id ");
				logusu('PRETAB',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataform');


		if ( $this->uri->segment($this->uri->total_segments()) != 'process')
			$id = $this->uri->segment($this->uri->total_segments());
		else
			$id = $this->uri->segment($this->uri->total_segments()-1);


		$edit = new DataForm('nomina/pretab/dataedit/'.$id.'/process');


		$mReg = $this->datasis->damereg("SELECT codigo, frec, fecha, nombre, total FROM pretab WHERE id=$id");
		$codigo = $mReg['codigo'];


		$edit->back_url = site_url('nomina/pretab/index');

		//$edit->on_save_redirect=false;

		
		if ( empty($mReg) ){
			echo 'Registro no encontrado '.$id;
			return true;
		}

		$edit->id = new hiddenField('ID','id');
		$edit->id->insertValue = $id;

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule        = '';
		$edit->codigo->size        = 10;
		$edit->codigo->maxlength   = 15;
		$edit->codigo->insertValue = $codigo;

		$edit->frec = new inputField('Frecuencia','frec');
		$edit->frec->rule        = '';
		$edit->frec->size        =  3;
		$edit->frec->maxlength   =  1;
		$edit->frec->insertValue = $mReg['frec'];

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule      = 'chfecha';
		$edit->fecha->size      = 10;
		$edit->fecha->maxlength =  8;
		$edit->fecha->calendar  = false;
		$edit->fecha->insertValue = $mReg['fecha'];

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule      = '';
		$edit->nombre->size      = 30;
		$edit->nombre->maxlength = 30;
		$edit->nombre->insertValue = $mReg['nombre'];

		$edit->total = new inputField('Total','total');
		$edit->total->rule      = 'numeric';
		$edit->total->css_class = 'inputnum';
		$edit->total->size      = 12;
		$edit->total->maxlength = 12;
		$edit->total->insertValue = $mReg['total'];

		$query = $this->db->query("DESCRIBE pretab");
		$i = 0;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){

				if ( substr($row->Field,0,1) == 'c' && $row->Field != 'codigo' && substr($row->Field,1,1) != '9' ) {
					$reg     = $this->datasis->damereg('SELECT descrip, formula FROM conc WHERE concepto="'.substr($row->Field,1,4).'"');
					$nombre  = $reg['descrip'];
					$formula = $reg['formula'];

					if ( strpos($formula, 'MONTO') ) {
						$dReg = $this->datasis->damereg('SELECT monto, valor FROM prenom WHERE codigo="'.$codigo.'" AND concepto="'.substr($row->Field,1,4).'"');

						$obj = $row->Field;
						$edit->$obj = new inputField($nombre, $obj);
						$edit->$obj->rule      = 'numeric';
						$edit->$obj->css_class = 'inputnum';
						$edit->$obj->size      = 10;
						$edit->$obj->maxlength = 10;
						$edit->$obj->insertValue = $dReg['monto'];
					}
				}
			}
		}

		//$edit->build();
		$edit->build_form();

		if($edit->on_success()){

			$codigo = $edit->codigo->newValue;

			$query = $this->db->query("DESCRIBE pretab");
			$i = 0;
			if ($query->num_rows() > 0){
				foreach ($query->result() as $row){
					if ( substr($row->Field,0,1) == 'c' && $row->Field != 'codigo' && substr($row->Field,1,1) != '9' ) {
						$reg     = $this->datasis->damereg('SELECT descrip, formula FROM conc WHERE concepto="'.substr($row->Field,1,4).'"');
						$nombre  = $reg['descrip'];
						$formula = $reg['formula'];

						if ( strpos($formula, 'MONTO') ) {
							$obj = $row->Field;
							$this->db->query('UPDATE prenom SET monto='.$edit->$obj->newValue.' WHERE codigo="'.$codigo.'" AND concepto="'.substr($row->Field,1,4).'"');
							memowrite('UPDATE prenom SET monto='.$edit->$obj->newValue.' WHERE codigo="'.$codigo.'" AND concepto="'.substr($row->Field,1,4).'"','meco');
						}
					}
				}
			}
			
			$rt=array(
				'status'  => 'A',
				'mensaje' => 'Registro guardado',
				'pk'      => $codigo
			);
			echo json_encode($rt);

		}else{
			$conten['form'] =&  $edit;
			$this->load->view('view_pretab', $conten);
			
		}
	}


	//******************************************************************
	// GENERA LA NOMINA 
	//
	function nomina(){

/*	
LOCAL mTEMPO, mC, mREG, mTRANSAC, mNOMINA, mPRESTAMO := {}
PRIVATE XFECHA, XCONTRATO, XTRABAJA, XFECHAP, XPPROME := 3

RECUADRO(5,10,20,74,,' /W',' Nomina ')
IF CMNJ(" ¨ Desea Guardar la Nomina y hacer los enlaces; administrativos Correspondientes ? ",{'Guardar','Cancelar'}) = 2
   PRETABLA()
   RETURN .t.
ENDIF

IF DAMEVAL("SELECT COUNT(*) FROM prenom",,'N') = 0
   CMNJ("No hay ninguna Nomina Generada; generela una primero")
   RETURN .T.
ENDIF
*/

		$mreg     = $this->datasis->damereg("SELECT fecha, contrato, trabaja, fechap FROM prenom LIMIT 1");

		$fecha    = $mreg['fecha'];
		$contrato = $mreg['contrato'];
		$trabaja  = $mreg['trabaja'];
		$fechap   = $mreg['fechap'];
		$tipo     = $this->datasis->dameval("SELECT tipo FROM noco WHERE codigo='".$contrato."' ");
		$existe   = $this->datasis->dameval("SELECT count(*) FROM nomina WHERE contrato='".$contrato."' AND fecha='".$fecha."' AND trabaja='".$trabaja."' ");

		if ( $existe > 0 AND $tipo <> 'O' ) {
			echo "Nomina ya Guardada debe eliminarla primero!!";
			return false;
		}

/*
// RECALCULAR PRENOM
RECUADRO(7,20,13,60)
@ 8,25 SAY "RECALCULANDO MONTOS....."
mC := DAMECUR("SELECT a.*, CONCAT(RTRIM(b.nombre),' ',RTRIM(b.apellido)) FROM pretab a JOIN pers b ON a.codigo=b.codigo GROUP BY codigo ")
DO WHILE !mC:EOF()
	@ 10,25 SAY PADR(mC:FieldGet('NOMBRE'),30)
	TABSUMA( .T., mC:FieldGet('CODIGO') )
	mC:Skip()
ENDDO
*/

		$mNOMINA  = $this->datasis->fprox_numero("nnomina");
		$mGSERNUM = $this->datasis->fprox_numero("ngser");
		$mFREC    = $tipo;

		$transac = $this->datasis->fprox_numero("ntransa");
		$estampa = date('Ymd');
		$hora    = date('H:i:s');
		$usuario = $this->session->userdata('usuario');

		// GENERAR ITEMS GITSER
		$mGSER = $this->datasis->fprox_numero('ngser');
		$mSQL= "INSERT INTO gitser (fecha, numero, proveed, codigo, descrip, precio,   iva, importe, unidades, fraccion, almacen, departa, sucursal, usuario, estampa, transac) 
				SELECT fechap, '".$mNOMINA."',ctaac, ctade,   CONCAT(RTRIM(b.descrip),' ',d.depadesc), SUM(valor), 0, SUM(valor), 0,        0,        '',     d.enlace, c.sucursal, '".$usuario."', '".$estampa."','".$transac."' 
				FROM prenom a JOIN conc b ON a.concepto=b.concepto 
					JOIN pers c ON a.codigo=c.codigo 
					JOIN depa d ON c.depto=d.departa 
				WHERE valor<>0 AND tipod='G' 
				GROUP BY ctade, d.enlace ";
		$this->db->query($mSQL);

		// CALCULA LAS DEDUCCIONES
		$mSQL = "SELECT sum(valor) total 
				FROM prenom a JOIN conc b ON a.concepto=b.concepto 
					JOIN pers c ON a.codigo=c.codigo 
				WHERE valor<>0 AND tipod!='G' ";
		
		$mDEDU = $this->datasis->dameval($mSQL);

		// CALCULA LOS PRESTAMOS
		$mSQL = "SELECT SUM(IF(b.monto-b.abonos-a.cuota>0,a.cuota,b.monto-b.abonos)) 
				FROM pres a JOIN smov b ON a.cod_cli=b.cod_cli AND a.tipo_doc=b.tipo_doc AND a.numero=b.numero 
				WHERE a.codigo IN (SELECT codigo FROM prenom GROUP BY codigo) 
				AND b.monto>b.abonos AND a.apartir<='".$fecha."' ";

		$mPRE   = $this->datasis->dameval($mSQL);
		$mDEDU  = abs($mDEDU)+$mPRE;
		$mNOMI  = $this->datasis->dameval("SELECT ctaac FROM conc WHERE tipo='A' LIMIT 1");

		// GENERA EL ENCABEZADO DE GSER
		$mSQL = "INSERT INTO gser (fecha, numero, proveed, nombre, vence, totpre,   totiva, totbruto, reten, totneto,    codb1, tipo1, cheque1, monto1, credito, anticipo, orden, tipo_doc, usuario, estampa, transac) 
				SELECT a.fechap, '".$mNOMINA."', b.ctaac, d.nombre, a.fechap, SUM(a.valor), 0, SUM(a.valor), 0, SUM(a.valor), '', '' , '', ".$mDEDU."*(b.ctaac='".$mNOMI."'), sum(a.valor)-".$mDEDU."*(b.ctaac='".$mNOMI."'), 0, '', 'GA','".$usuario."', '".$estampa."', '".$transac."' 
				FROM prenom a JOIN conc b ON a.concepto=b.concepto 
					JOIN pers c ON a.codigo=c.codigo JOIN sprv d ON ctaac=d.proveed 
				WHERE valor<>0 AND tipod='G' 
				GROUP BY ctaac ";
		$this->db->query($mSQL);

		// GENERA CXP
		$mNUMCXP = $mNOMINA;
		$mSQL ="INSERT INTO sprm (tipo_doc, fecha, numero, cod_prv, nombre, vence, monto, impuesto, tipo_ref, num_ref, codigo, descrip, usuario, estampa, transac, observa1 )
				SELECT 'ND' tipo_doc, fecha, CONCAT('N',MID(numero,2,7)), proveed, nombre, vence, credito, 0, 'GA', '' ,'NOCON', 'NOMINA', '".$usuario."', '".$estampa."', '".$transac."', 'NOMINA ' 
				FROM gser 
				WHERE tipo_doc='GA' AND numero='".$mNOMINA."' AND transac='".$transac."'";
		$this->db->query($mSQL);

		// GENERA LAS ND EN PROVEEDORES SPRM
		$mSQL= "SELECT b.ctaac, a.fechap fecha, sum(valor) valor, d.nombre, b.descrip 
				FROM prenom a JOIN conc b ON a.concepto=b.concepto 
					JOIN pers c ON a.codigo=c.codigo 
					JOIN sprv d ON ctaac=d.proveed 
				WHERE valor<>0 AND tipod='P' AND tipoa='P' 
				GROUP BY ctaac ";
		$query  = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$mCONTROL = $this->datasis->fprox_numero("nsprm");
				$mNOTADEB = $this->datasis->fprox_numero("num_nd");

				$data = array();
				$data["cod_prv"]  = $row->ctaac;
				$data["nombre"]   = $row->nombre; 
				$data["tipo_doc"] = 'ND';
				$data["numero"]   = $mNOTADEB;
				$data["fecha"]    = $row->fecha;
				$data["monto"]    = abs($row->valor);
				$data["impuesto"] = 0;
				$data["vence"]    = $row->fecha;
				$data["abonos"]   = 0;
				$data["tipo_ref"] = 'GA';
				$data["num_ref"]  = $mNOMINA;
				$data["observa1"] = $row->descrip;
				$data["observa2"] = 'NOMINA';
				$data["control"]  = $mCONTROL;
				$data["reteiva"]  = 0;
				$data["codigo"]   = 'NOCON';
				$data["descrip"]  = 'NOMINA';

				$data["usuario"]  = $usuario;
				$data["estampa"]  = $estampa;
				$data["hora"]     = $hora;
				$data["transac"]  = $transac;

				$this->db->insert('sprm',$data);

				//$mSQL = "REPLACE INTO sprm SET "
	
			}
		}

		$mPRESTAMO = array();

		// PRESTAMOS
		$mSQL= "SELECT b.cod_cli, b.nombre, b.tipo_doc, b.numero, b.fecha, a.codigo, a.nombre, 
						IF(b.monto-b.abonos-a.cuota>0,a.cuota,b.monto-b.abonos) cuota, b.monto 
				FROM pres a JOIN smov b ON a.cod_cli=b.cod_cli AND a.tipo_doc=b.tipo_doc AND a.numero=b.numero 
				WHERE a.codigo IN (SELECT codigo FROM prenom GROUP BY codigo) 
				AND b.monto>b.abonos AND a.apartir<='".$fecha."' ";
		$query  = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){

				$mCONTROL = $this->datasis->fprox_numero("nsmov");
				$mNOTACRE = $this->datasis->fprox_numero("nccli");

				$data = array();
				$data["cod_cli"]  = $row->cod_cli;
				$data["nombre"]   = $row->nombre; 
				$data["tipo_doc"] = 'NC';
				$data["numero"]   = $mNOTACRE;
				$data["fecha"]    = $fechap;
				$data["monto"]    = abs( $row->cuota );
				$data["impuesto"] = 0;
				$data["vence"]    = $fechap;
				$data["abonos"]   = abs( $row->cuota );
				$data["tipo_ref"] = 'GA';
				$data["num_ref"]  = $mNOMINA;
				$data["observa1"] = 'PAGO A '.$row->tipo_doc.$row->numero;
				$data["observa2"] = 'POR DESCUENTO DE NOMINA';
				$data["control"]  = $mCONTROL;
				$data["codigo"]   = 'NOCON';
				$data["descrip"]  = 'NOMINA';
				$data["usuario"]  = $usuario;
				$data["estampa"]  = $estampa;
				$data["hora"]     = $hora;
				$data["transac"]  = $transac;

				$this->db->insert('smov',$data);

				// ACTUALIZA EL DOCUMENTO ORIGEN
				$mSQL = "UPDATE smov SET abonos=abonos+".abs($row->cuota)."
				WHERE tipo_doc='".$row->tipo_doc."' AND numero='".$row->numero."' 
				AND cod_cli='".$row->cod_cli."' AND fecha='".$row->fecha."' LIMIT 1";
				$this->db->query($mSQL);   // { ABS(mC:FieldGet('CUOTA')),mC:FieldGet('TIPO_DOC'), mC:FieldGet('NUMERO'), mC:FieldGet('COD_CLI'), mC:FieldGet('FECHA')  })
		
				// CARGA EL MOVIMIENTO EN ITCCLI
				//$mSQL = "INSERT INTO itccli (numccli, tipoccli, cod_cli, tipo_doc, numero, fecha, monto, abono, transac, estampa, hora, usuario ) "
				//mSQL = "VALUES             (  ?,       'NC',    ?,       ?,        ?,      ?,     ?,     ?,      ?,     now(),     ?,     ?     )"

				$data = array();
				$data['numccli']  = $mNOTACRE;
				$data['tipoccli'] = 'NC';
				$data['cod_cli']  = $row->cod_cli;
				$data['tipo_doc'] = $row->tipo_doc;
				$data['numero']   = $row->numero;
				$data['fecha']    = $row->fecha;
				$data['monto']    = $row->monto;
				$data['abono']    = abs($row->cuota);
				$data["usuario"]  = $usuario;
				$data["estampa"]  = $estampa;
				$data["hora"]     = $hora;
				$data["transac"]  = $transac;
				$this->db->insert('itccli',$data);
				$mPRESTAMO[] = array( $row->codigo, $row->nombre, $row->cuota );
			}
		}

		// MANDA LA NOMINA AL HISTORICO
		$mSQL= "INSERT INTO nomina (numero, frecuencia,               contrato,   depto,   codigo,   nombre,   concepto,   tipo,   descrip,   grupo,   formula,   monto,   fecha,   valor, estampa, usuario,          transac,      hora, fechap, trabaja ) 
				SELECT '".$mNOMINA."' numa, '".$mFREC."' frecu, a.contrato, b.depto, a.codigo, a.nombre, a.concepto, a.tipo, a.descrip, a.grupo, a.formula, a.monto, a.fecha, a.valor, now(), '".$usuario."', '".$transac."', CURTIME(), a.fechap,'".$trabaja."'
				FROM prenom a JOIN pers b ON a.codigo=b.codigo 
				WHERE a.valor<>0 ";
		$this->db->query($mSQL);


		$mVALOR = "SUM(IF(b.monto-b.abonos-a.cuota>0,a.cuota,b.monto-b.abonos))";

		// MANDA LOS PRESTAMOS
		foreach ( $mPRESTAMO as $meco ) {
			//$mSQL = "INSERT INTO nomina SET ";
			//          1         2            3           4       5         6                                                                                               7       8
			$mDEPTO = $this->datasis->dameval("SELECT depto FROM pers WHERE codigo='".$mPRESTAMO[0]."'");

			$data = array();
			$data["numero"]     = $mNOMINA; 
			$data["frecuencia"] = $mFREC; 
			$data["contrato"]   = $contrato; 
			$data["depto"]      = $mDEPTO; 
			$data["codigo"]     = $meco[0]; 
			$data["nombre"]     = $meco[1];
			$data["concepto"]   = 'PRES'; 
			$data["tipo"]       = 'D'; 
			$data["descrip"]    = 'PAGO DE PRESTAMO'; 
			$data["grupo"]      = ''; 
			$data["formula"]    = ''; 
			$data["monto"]      = 0; 
			$data["fecha"]      = $fecha; 
			$data["valor"]      = -$meco[2]; 
			$data["estampa"]    = $estampa; 
			$data["usuario"]    = $usuario; 

			$data["transac"]    = $transac; 
			$data["hora"]       = $hora; 
			$data["fechap"]     = $fechap; 
			$data["trabaja"]    = $trabaja; 
			$this->db->insert('nomina', $data);

			//              1       2       3          4            5           6               7            8              9       10
			//mVALORES := {, , XCONTRATO, mDEPTO, mPRESTAMO[i,1], mPRESTAMO[i,2],DTOS(XFECHA),-1*mPRESTAMO[i,3], ms_CODIGO, mTRANSAC, XFECHAP, XTRABAJA }
			//EJECUTASQL(mSQL,mVALORES)
		}

		$this->db->query("TRUNCATE prenom");
		$this->db->query("TRUNCATE pretab");
		
		echo "Nomina Guardada";

	}




/*
	function _pre_insert($do){
		$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
		return false;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}
*/
	function instalar(){
		if (!$this->db->table_exists('pretab')) {


		}
	}
}

?>
