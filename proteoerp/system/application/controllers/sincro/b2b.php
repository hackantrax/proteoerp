<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
class b2b extends validaciones {

	function b2b(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id(135,1);
	}

	function index(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');
		$bSPRV=$this->datasis->modbus($mSPRV);

		$mGRUP=array(
			'tabla'   =>'grup',
			'columnas'=>array(
				'grupo' =>'Grupo',
				'nom_grup'=>'Nombre'),
			'filtro'  =>array('grupo'=>'Grupo','nom_grup'=>'Nombre'),
			'retornar'=>array('grupo'=>'grupo'),
			'titulo'  =>'Buscar Grupo');
		$bGRUP=$this->datasis->modbus($mGRUP);

		$atts = array(
				'width'      => '400',
				'height'     => '300',
				'scrollbars' => 'yes',
				'status'     => 'yes',
				'resizable'  => 'yes',
				'screenx'   => "'+((screen.availWidth/2)-200)+'",
				'screeny'   => "'+((screen.availHeight/2)-150)+'"
			);

		$filter = new DataFilter('Filtro de b2b');
		$filter->db->select(array('a.id','a.proveed','a.usuario','a.depo AS depo','a.tipo','a.url','a.grupo','b.nombre','c.ubides'));
		$filter->db->from('b2b_config AS a');
		$filter->db->join('sprv AS b','b.proveed=a.proveed','left');
		$filter->db->join('caub AS c','c.ubica=a.depo','left');

		$filter->proveed = new inputField('Proveedor', 'proveed');
		$filter->proveed->append($bSPRV);
		$filter->proveed->size=25;

		$filter->depo = new dropdownField('Almac&eacute;n','depo');
		$filter->depo->option("","Seleccionar");
		$filter->depo->options("SELECT ubica, ubides FROM caub ORDER BY ubica");

		$filter->tipo = new dropdownField('Tipo','tipo');
		$filter->tipo->option('' ,'Selecione un tipo');
		$filter->tipo->option('I','Inventario');
		$filter->tipo->option('G','Gastos');

		$filter->grupo = new inputField('Grupo', 'grupo');
		$filter->grupo->append($bGRUP);
		$filter->grupo->size=25;
		
		$filter->buttons('reset','search');
		$filter->build();

		$acti=anchor_popup('/sincro/b2b/traecompra/<#id#>','Traer Transacciones',$atts);
		$link=anchor('/sincro/b2b/dataedit/show/<#id#>','<#id#>');
		$grid = new DataGrid('b2b');
		$grid->order_by('id','asc');
		$grid->per_page = 15;

		$grid->column_orderby('N&uacute;mero' ,$link    ,'id');
		$grid->column_orderby('Proveedor'     ,'nombre' ,'proveed');
		$grid->column_orderby('Url'           ,'url'    ,'url');
		$grid->column_orderby('Usuario'       ,'usuario','usuario');
		$grid->column_orderby('Tipo'          ,'tipo'   ,'tipo');
		$grid->column_orderby('Almac&eacute;n','ubides' ,'depo');
		$grid->column_orderby('Grupo'         ,'grupo'  ,'grupo');
		$grid->column('Sincronizar' ,$acti);
		$grid->add('sincro/b2b/dataedit/create');
		$grid->build();

		$smenu['link']=barra_menu('921');
		$data['content'] =$filter->output. $grid->output;
		$data['title']   = '<h1>B2B</h1>';
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$mGRUP=array(
			'tabla'   =>'grup',
			'columnas'=>array(
				'grupo' =>'Grupo',
				'nom_grup'=>'Nombre'),
			'filtro'  =>array('grupo'=>'Grupo','nom_grup'=>'Nombre'),
			'retornar'=>array('grupo'=>'grupo'),
			'titulo'  =>'Buscar Grupo');
		$bGRUP=$this->datasis->modbus($mGRUP);

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');
		$bSPRV=$this->datasis->modbus($mSPRV);

		$script='
		<script language="javascript" type="text/javascript">
			$(function(){ $(".inputnum").numeric("."); });
		</script>';

		$edit = new DataEdit('B2B', 'b2b_config');
		$edit->back_url = site_url('sincro/b2b/index/');

		$edit->proveed = new inputField('Proveedor', 'proveed');
		$edit->proveed->size      =  15;
		$edit->proveed->maxlength =  15;
		$edit->proveed->rule      = 'required';
		$edit->proveed->append($bSPRV);

		$edit->url = new inputField('Direcci&oacute;n Url', 'url');
		$edit->url->size      =  50;
		$edit->url->maxlength =  50;
		$edit->url->rule      = 'required';

		$edit->puerto = new inputField('Puerto', 'puerto');
		$edit->puerto->inserValue=  80;
		$edit->puerto->size      =  5;
		$edit->puerto->maxlength =  20;
		$edit->puerto->rule      = 'required|numeric';

		$edit->proteo = new inputField('Proteo', 'proteo');
		$edit->proteo->inserValue=  80;
		$edit->proteo->size      =  20;
		$edit->proteo->maxlength =  20;

		$edit->usuario = new inputField('Usuario', 'usuario');
		$edit->usuario->size      =  20;
		$edit->usuario->maxlength =  20;
		$edit->usuario->rule      = 'required';

		$edit->clave = new inputField('Clave', 'clave');
		$edit->clave->size      =  10;
		$edit->clave->maxlength =  10;
		$edit->clave->rule      = 'required';

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('' ,'Seleccione un tipo');
		$edit->tipo->option('I','Inventario');
		$edit->tipo->option('G','Gastos');
		$edit->tipo->style ='50px';
		$edit->tipo->rule  ='required';

		$edit->depo = new dropdownField('Almac&eacute;n','depo');
		$edit->depo->option('','Seleccionar');
		$edit->depo->options('SELECT ubica,ubides FROM caub');
		$edit->depo->style ='250px';
		$edit->depo->rule  ='required';

		for($i=1;$i<=5;$i++){
			$obj='margen'.$i;
			$edit->$obj = new inputField('Margen '.$i, $obj);
			$edit->$obj->size      = 15;
			$edit->$obj->maxlength = 15;
			$edit->$obj->css_class = 'inputnum';
			$edit->$obj->rule      = 'callback_chporcent';
			$edit->$obj->group = 'Margenes de ganancia';
			if($i==5) $edit->$obj->append('Solo aplica a supermercados');
		}

		$edit->grupo = new inputField('Grupo', 'grupo');
		$edit->grupo->size      =  10;
		$edit->grupo->maxlength =  6;
		$edit->grupo->rule      = 'required';
		$edit->grupo->append($bGRUP);

		$edit->buttons('modify', 'save','undo', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head().$script;
		$data['title']   = '<h1>Editar b2b</h1>';
		$this->load->view('view_ventanas', $data);
	}

	//Para visualizar las transacciones descargadas
	function scstfilter(){
		$this->rapyd->load('datagrid','datafilter');
		$this->rapyd->uri->keep_persistence();

		$atts = array(
				'width'      => '800',
				'height'     => '600',
				'scrollbars' => 'yes',
				'status'     => 'yes',
				'resizable'  => 'yes',
				'screenx'   => "'+((screen.availWidth/2)-400)+'",
				'screeny'   => "'+((screen.availHeight/2)-300)+'"
			);
		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');
		$boton=$this->datasis->modbus($modbus);

		$filter = new DataFilter('Filtro de Compras');
		$filter->db->select=array('numero','fecha','vence','nombre','montoiva','montonet','proveed','control');
		$filter->db->from('b2b_scst');

		$filter->fechad = new dateonlyField('Desde', 'fechad','d/m/Y');
		$filter->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause ='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator=">="; 
		$filter->fechah->operator="<=";

		$filter->numero = new inputField('Factura', 'numero');
		$filter->numero->size=20;

		$filter->proveedor = new inputField('Proveedor','proveed');
		$filter->proveedor->append($boton);
		$filter->proveedor->db_name = 'proveed';
		$filter->proveedor->size=20;

		$filter->buttons('reset','search');
		$filter->build();

		$uri = anchor('sincro/b2b/scstedit/show/<#id#>','<#numero#>');

		$grid = new DataGrid();
		$grid->order_by('fecha','desc');
		$grid->per_page = 15;
		$grid->column_orderby('Factura',$uri,'control');
		$grid->column_orderby('Fecha'  ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha',"align='center'");
		$grid->column_orderby('Vence'  ,'<dbdate_to_human><#vence#></dbdate_to_human>','vence',"align='center'");
		$grid->column_orderby('Nombre' ,'nombre','nombre');
		$grid->column_orderby('IVA'    ,'montoiva' ,'montoiva',"align='right'");
		$grid->column_orderby('Monto'  ,'montonet' ,'montonet',"align='right'");
		$grid->column_orderby('Control','pcontrol' ,'pcontrol',"align='right'");
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] =$filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   ='<h1>Compras de B2B</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function scstedit(){
		$this->rapyd->load('dataedit','datadetalle','fields','datagrid');
		$this->rapyd->uri->keep_persistence();

		function exissinv($cen,$id=0){
			if(empty($cen)){
				$id--;
				$rt =form_button('create' ,'Crear','onclick="pcrear('.$id.');"');
				$rt.=form_button('asignar','Asig.','onclick="pasig('.$id.');"');
			}else{
				$rt='--';
			}
			return $rt;
		}

		$edit = new DataEdit('Compras','b2b_scst');
		$edit->back_url = 'sincro/b2b/scstfilter/';

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->mode ='autohide';
		$edit->fecha->size = 10;

		$edit->numero = new inputField("N&uacute;mero", "numero");
		$edit->numero->size = 15;
		$edit->numero->rule= 'required';
		$edit->numero->mode= 'autohide';
		$edit->numero->maxlength=8;

		$edit->proveedor = new inputField("Proveedor", "proveed");
		$edit->proveedor->size = 10;
		$edit->proveedor->maxlength=5;

		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size = 50;
		$edit->nombre->maxlength=40;

		$edit->almacen = new inputField("Almac&eacute;n", "depo");
		$edit->almacen->size = 15;
		$edit->almacen->maxlength=8;

		$edit->tipo = new dropdownField("Tipo", "tipo_doc");
		$edit->tipo->option("FC","FC");
		$edit->tipo->rule = "required";
		$edit->tipo->size = 20;
		$edit->tipo->style='width:150px;';

		$edit->subt  = new inputField("Sub-total", "montotot");
		$edit->subt->size = 20;
		$edit->subt->css_class='inputnum';

		$edit->iva  = new inputField("Impuesto", "montoiva");
		$edit->iva->size = 20;
		$edit->iva->css_class='inputnum';

		$edit->total  = new inputField("Total global", "montonet");
		$edit->total->size = 20;
		$edit->total->css_class='inputnum';

		$edit->pcontrol  = new inputField('Control', 'pcontrol');
		$edit->pcontrol->size = 12;

		//$numero =$edit->_dataobject->get('control');
		$id =$edit->_dataobject->get('id');
		$proveed=$this->db->escape($edit->_dataobject->get('proveed'));

		$atts = array(
			'width'     => '250',
			'height'    => '250',
			'scrollbars'=> 'no',
			'status'    => 'no',
			'resizable' => 'no',
			'screenx'   => "'+((screen.availWidth/2)-175)+'",
			'screeny'   => "'+((screen.availHeight/2)-175)+'"
		);
		$llink=anchor_popup('sincro/b2b/reasignaprecio/modify/<#id#>', '<b><#precio1#></b>', $atts);

		//Campos para el detalle
		$tabla=$this->db->database;
		$detalle = new DataGrid('');
		$select=array('a.*','b.descrip AS sinvdesc','a.codigo AS barras','a.costo AS pond','a.codigolocal  AS sinv','a.codigolocal');
		$detalle->db->select($select);
		$detalle->db->from('b2b_itscst AS a');
		$detalle->db->where('a.id_scst',$id);
		$detalle->db->join('sinv AS b','a.codigolocal=b.codigo','LEFT');
		$detalle->use_function('exissinv');
		$detalle->column('Codigo sistema'    ,'<sinulo><#codigolocal#>|No tiene</sinulo>' );
		$detalle->column('Codigo prov.'      ,'<#codigo#>'   );
		$detalle->column('Descrip. Proveedor','<#descrip#>'  );
		$detalle->column('Descrip. Sistema'  ,'<#sinvdesc#>' );
		$detalle->column('Cantidad'          ,'<#cantidad#>' ,"align='right'");
		$detalle->column('PVP'               ,$llink         ,"align='right'");
		$detalle->column('Costo'             ,'<#ultimo#>'   ,"align='right'");
		$detalle->column('Importe'           ,'<#importe#>'  ,"align='right'");
		$detalle->build();
		//echo $detalle->db->last_query();

		$script='
		function pcrear(id){
			var pasar=["barras","descrip","ultimo","iva","codigo","pond","precio1","precio2","precio3","precio4"];
			var url  = "'.site_url('farmacia/sinv/dataedit/create').'";
			form_virtual(pasar,id,url);
		}
		function pasig(id){
			var pasar=["barras","proveed","descrip"];
			var url  = "'.site_url('farmacia/scst/asignardataedit/create').'";
			form_virtual(pasar,id,url);
		}
		function form_virtual(pasar,id,url){
			var data='.json_encode($detalle->data).';
			var w = window.open("'.site_url('farmacia/scst/dummy').'","asignar","width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx="+((screen.availWidth/2)-400)+",screeny="+((screen.availHeight/2)-300)+"");
			var fform  = document.createElement("form");
			fform.setAttribute("target", "asignar");
			fform.setAttribute("action", url );
			fform.setAttribute("method", "post");
			for(i=0;i<pasar.length;i++){
				Val=eval("data[id]."+pasar[i]);
				iinput = document.createElement("input");
				iinput.setAttribute("type", "hidden");
				iinput.setAttribute("name", pasar[i]);
				iinput.setAttribute("value", Val);
				fform.appendChild(iinput);
			}
			var cuerpo = document.getElementsByTagName("body")[0];
			cuerpo.appendChild(fform);
			fform.submit();
			w.focus();
			cuerpo.removeChild(fform);
		}';

		$edit->detalle=new freeField("detalle", 'detalle',$detalle->output);
		$accion="javascript:window.location='".site_url('sincro/b2b/cargacompra'.$edit->pk_URI())."'";
		$pcontrol=$edit->_dataobject->get('pcontrol');
		if(is_null($pcontrol)) $edit->button_status('btn_cargar','Cargar',$accion,'TR','show');
		$edit->buttons('save','undo','back');

		$edit->script($script,'show');
		$edit->build();

		$this->rapyd->jquery[]='$("#dialog").dialog({
			autoOpen: false,
			show: "blind",
			hide: "explode"
		});
		$( "#opener" ).click(function() {
			$( "#dialog" ).dialog( "open" );
			return false;
		});';

		$conten['form']  =&  $edit;
		$data['content'] = $this->load->view('view_b2b_compras', $conten,true); 
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = '<h1>Compras Descargadas</h1>';
		$this->load->view('view_ventanas', $data);
	}

//****************************************************
// Metodos para gestionar transacciones como compras
//****************************************************
	function traecompra($par,$ultimo=null){
		$rt=$this->_trae_compra($par,$ultimo);
		if($rt!==false){
			$str='Transacciones descargadas';
		}else{
			$str='Hubo problemas durante la  descarga, se generaron centinelas';
		}
		$data['content'] = $str;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = '<h1>Compras Descargadas</h1>';
		$this->load->view('view_ventanas_sola', $data);
	}

	function _trae_compra($id=null,$ultimo=null){
		if(is_null($id)) return false; else $id=$this->db->escape($id);

		$config=$this->datasis->damerow("SELECT proveed,grupo,puerto,proteo,url,usuario,clave,tipo,depo,margen1,margen2,margen3,margen4,margen5 FROM b2b_config WHERE id=$id");
		if(count($config)==0) return false;

		$er=0;
		$this->load->helper('url');
		$server_url = reduce_double_slashes($config['url'].'/'.$config['proteo'].'/'.'rpcserver');

		$this->load->library('xmlrpc');
		$this->xmlrpc->xmlrpc_defencoding=$this->config->item('charset');
		//$this->xmlrpc->set_debug(TRUE);
		$puerto= (empty($config['puerto'])) ? 80 : $config['puerto'];

		$this->xmlrpc->server($server_url , $puerto);
		$this->xmlrpc->method('cea');

		if(is_null($ultimo)){
			$ufac=$this->datasis->dameval('SELECT MAX(numero) FROM b2b_scst WHERE proveed='.$this->db->escape($config['proveed']));
			if(empty($ufac)) $ufac=0;
		}elseif(is_numeric($ultimo)){
			$ufac=$ultimo;
		}else{
			$ufac=0;
		}

		$request = array($ufac,$config['proveed'],$config['usuario'],$config['clave']);
		$this->xmlrpc->request($request);

		if (!$this->xmlrpc->send_request()){
			echo $this->xmlrpc->display_error();
		}else{
			$res=$this->xmlrpc->display_response();
			foreach($res AS $ind=>$compra){
				$arr=unserialize($compra);
				foreach($arr['scst'] AS $in => $val) $arr[$in]=base64_decode($val);

				$proveed=$config['proveed'];
				$pnombre=$this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$this->db->escape($proveed));

				$data['proveed']  = $proveed;
				$data['nombre']   = $pnombre;
				$data['tipo_doc'] = 'FC';
				$data['depo']     = $config['depo'];
				$data['fecha']    = $arr['fecha'];
				$data['vence']    = $arr['vence'];
				$data['numero']   = $arr['numero'];
				$data['serie']    = $arr['nfiscal'];
				$data['montotot'] = $arr['totals'];
				$data['montoiva'] = $arr['iva'];
				$data['montonet'] = $arr['totalg'];
				$mSQL=$this->db->insert_string('b2b_scst',$data);

				$rt=$this->db->simple_query($mSQL);
				if(!$rt){
					memowrite($mSQL,'B2B');
					$maestro=false;
				}else{
					$id_scst=$this->db->insert_id();
					$maestro=true;
				} $er+= !$rt;

				if($maestro){
					$itscst =& $arr['itscst'];
					foreach($itscst AS $in => $aarr){
						foreach($aarr AS $i=>$val) $arr[$in][$i]=base64_decode($val);

						$barras=trim($arr[$in]['barras']);
						$ddata['id_scst']  = $id_scst;
						$ddata['proveed']  = $proveed;
						$ddata['fecha']    = $data['fecha'];
						$ddata['numero']   = $data['numero'];
						$ddata['depo']     = $data['depo'];
						$ddata['codigo']   = $arr[$in]['codigoa'];
						$ddata['descrip']  = $arr[$in]['desca'];
						$ddata['cantidad'] = $arr[$in]['cana'];
						$ddata['costo']    = $arr[$in]['preca'];
						$ddata['importe']  = $arr[$in]['tota'];
						$ddata['garantia'] = 0;
						$ddata['ultimo']   = $arr[$in]['preca'];
						$ddata['precio1']  = $arr[$in]['precio1'];
						$ddata['precio2']  = $arr[$in]['precio1'];
						$ddata['precio3']  = $arr[$in]['precio2'];
						$ddata['precio4']  = $arr[$in]['precio3'];
						$ddata['montoiva'] = $arr[$in]['tota']*($arr[$in]['iva']/100);
						$ddata['iva']      = $arr[$in]['iva'];
						$ddata['barras']   = $barras;

						//procedimiento de determinacion del codigo del articulo en sistema local
						$codigolocal=false;
						if(!empty($barras)){
							$mSQL_p = 'SELECT codigo FROM sinv';
							$bbus   = array('codigo','barras','alterno');
							$query=$this->_gconsul($mSQL_p,$barras,$bbus);
							if($query){
								$row = $query->row();
								$codigolocal=$row->codigo;
							}
						}
						//if($codigolocal===false AND $this->db->table_exists('sinvprov')){
						//	$codigolocal=$this->datasis->dameval('SELECT codigo FROM sinvprov WHERE proveed='.$this->db->escape($proveed).' AND codigop='.$this->db->escape($arr[$in]['codigoa']));
						//}

						//Si no existe lo crea
						if(empty($codigolocal)){
							$base1 = ($arr[$in]['precio1']*100)/(100+$arr[$in]['iva']);
							$base2 = ($arr[$in]['precio2']*100)/(100+$arr[$in]['iva']);
							$base3 = ($arr[$in]['precio3']*100)/(100+$arr[$in]['iva']);
							$base4 = ($arr[$in]['precio4']*100)/(100+$arr[$in]['iva']);
							$invent['codigo']   = $barras;
							$invent['grupo']    = $config['grupo'];
							$invent['prov1']    = $proveed;
							$invent['descrip']  = $arr[$in]['desca'];
							$invent['existen']  = $arr[$in]['cana'];
							$invent['pond']     = $arr[$in]['preca'];
							$invent['ultimo']   = $arr[$in]['preca'];
							$invent['unidad']   = $arr[$in]['unidad'];
							$invent['tipo']     = $arr[$in]['tipo'];
							$invent['tdecimal'] = $arr[$in]['tdecimal'];
							$invent['margen1']  = round(100-($arr[$in]['preca']*100/$base1),2);
							$invent['margen2']  = round(100-($arr[$in]['preca']*100/$base2),2);
							$invent['margen3']  = round(100-($arr[$in]['preca']*100/$base3),2);
							$invent['margen4']  = round(100-($arr[$in]['preca']*100/$base4),2);
							$invent['base1']    = round($base1,2);
							$invent['base2']    = round($base2,2);
							$invent['base3']    = round($base3,2);
							$invent['base4']    = round($base4,2);
							$invent['precio1']  = $arr[$in]['precio1'];
							$invent['precio2']  = $arr[$in]['precio2'];
							$invent['precio3']  = $arr[$in]['precio3'];
							$invent['precio4']  = $arr[$in]['precio4'];
							$invent['iva']      = $arr[$in]['iva'];
							$invent['redecen']  = 'N';
							$invent['activo']   = 'S';
							$invent['formcal']  = 'U';
							$invent['clase']    = 'C';
							$invent['garantia'] = 0;

							$mSQL=$this->db->insert_string('sinv',$invent);
							$rt=$this->db->simple_query($mSQL);
							if(!$rt){
								memowrite($mSQL,'B2B');
							}else{
								$codigolocal=$barras;
							}$er+= !$rt;
						}
						$ddata['codigolocal'] = $codigolocal;

						$mSQL=$this->db->insert_string('b2b_itscst',$ddata);
						$rt=$this->db->simple_query($mSQL);
						if(!$rt){
							memowrite($mSQL,'B2B');
						}$er+= !$rt;
					}
					$this->_cargacompra($id_scst);

					//Carga el inventario
					/*$ddata=array();
					$sinv=&$arr['sinv'];
					foreach($sinv as $in => $aarr){
						foreach($aarr AS $i=>$val)
							$sinv[$in][$i]=base64_decode($val);
						$sinv[$in]['proveed']  = $proveed;
						$mSQL=$this->db->insert_string('b2b_sinv',$sinv[$in]);
						$mSQL.=' ON DUPLICATE KEY UPDATE precio1='.$sinv[$in]['precio1'].',precio2='.$sinv[$in]['precio2'].',precio3='.$sinv[$in]['precio3'].',precio4='.$sinv[$in]['precio4'];
						$rt=$this->db->simple_query($mSQL);
						if(!$rt) memowrite($mSQL,'B2B');
					}*/
				}
			}
		}
		return $er;
	}

	function cargacompra($id){
		$this->_cargacompra($id);
		redirect('sincro/b2b/scstedit/show/'.$id);
	}

	function _cargacompra($id){
		$pcontrol=$this->datasis->dameval('SELECT pcontrol FROM b2b_scst WHERE  id='.$this->db->escape($id));

		$cana=$this->datasis->dameval('SELECT COUNT(*) FROM b2b_itscst AS a LEFT JOIN sinv AS b ON a.codigolocal=b.codigo WHERE a.numero IS NULL AND id_scst='.$this->db->escape($id));
		if($cana==0 AND empty($pcontrol)){
			$control=$this->datasis->fprox_numero('nscst');
			$transac=$this->datasis->fprox_numero('ntransac');
			//$tt['montotot']=$tt['montoiva']=$tt['montonet']=0;

			$query = $this->db->query('SELECT fecha,numero,proveed,depo,codigolocal AS codigo,descrip,cantidad,devcant,devfrac,costo,importe,iva,montoiva,garantia,ultimo,precio1,precio2,precio3,precio4,licor FROM b2b_itscst WHERE id_scst=?',array($id));
			if ($query->num_rows() > 0){
				foreach ($query->result_array() as $itrow){
					$itrow['estampa'] = date('Y-m-d');
					$itrow['hora']    = date('h:m:s');
					$itrow['control'] = $control;
					$itrow['transac'] = $transac;

					//$tt['montotot']+=$itrow['importe'];
					//$tt['montoiva']+=$itrow['montoiva'];
					//$tt['montonet']+=$itrow['importe']+$itrow['montoiva'];
					$mSQL=$this->db->insert_string('itscst',$itrow);
					$rt=$this->db->simple_query($mSQL);
					if(!$rt){
						memowrite($mSQL,'B2B');
					}
				}
			}

			$query = $this->db->query('SELECT fecha,numero,depo,proveed,nombre,montotot,montoiva,montonet,vence,tipo_doc,peso,usuario,nfiscal,exento,sobretasa,reducida,tasa,montasa,monredu,monadic,serie FROM b2b_scst WHERE id=?',array($id));
			if ($query->num_rows() > 0){
				$row = $query->row_array();
				$row['estampa'] = date('Y-m-d');
				$row['hora']    = date('h:m:s');
				$row['control'] = $control;
				$row['transac'] = $transac;
				$row['usuario'] = $this->session->userdata('usuario');
				//$row['montotot'] =$tt['montotot'];
				//$row['montoiva'] =$tt['montoiva'];
				//$row['montonet'] =$tt['montonet'];

				$mSQL=$this->db->insert_string('scst',$row);
				$rt=$this->db->simple_query($mSQL);
				if(!$rt){
					memowrite($mSQL,'B2B');
				}
			}

			$mSQL="UPDATE b2b_scst SET pcontrol='$control' WHERE id=".$this->db->escape($id);
			$rt=$this->db->simple_query($mSQL);
			if(!$rt){
				memowrite($mSQL,'B2B');
			}

		}
	}

//****************************************************
// Metodos para gestionar transacciones como gasto
//****************************************************

	function cargagasto(){
		
	}

	function _cargagasto(){
		
	}

	function reasignaprecio(){
		$this->rapyd->load('dataedit');
		$edit = new DataEdit('Cambios de precios','b2b_itscst');
		$edit->descrip  = new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->mode = 'autohide';

		for($i=1;$i<5;$i++){
			$obj='precio'.$i;
			$edit->$obj = new inputField('Precio '.$i, $obj);
			$edit->$obj->css_class='inputnum';
			$edit->$obj->rule ='numeric';
			$edit->$obj->size = 10;
		}

		$edit->buttons('modify','save');
		$edit->build();
		$this->rapyd->jquery[]='$(window).unload(function() { window.opener.location.reload(); });';
		$data['content'] =$edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   ='';
		$this->load->view('view_ventanas_sola', $data);
	}

	function sprvexits($proveed){
		$mSQL='SELECT COUNT(*) FROM sprv WHERE proveed='.$this->db->escape($proveed);
		$cana=$this->datasis->dameval($mSQL);
		if($cana==0){
			$error="El proveedor dado no exite";
			$this->validation->set_message('sprvexits',$error);
			return false;
		}
		return true;
	}

	function noexiste($barras){
		$mSQL='SELECT COUNT(*) FROM sinv WHERE codigo='.$this->db->escape($barras);
		$cana=$this->datasis->dameval($mSQL);
		if($cana!=0){
			$error="El c&oacute;digo de barras '$barras' existe en el iventario, la equivalencia se debe aplicar en un producto que no exista";
			$this->validation->set_message('noexiste',$error);
			return false;
		}
		return true;
	}

	function siexiste($barras){
		$mSQL='SELECT COUNT(*) FROM sinv WHERE codigo='.$this->db->escape($barras);
		$cana=$this->datasis->dameval($mSQL);
		if($cana==0){
			$error="El c&oacute;digo de barras '$barras' no existe en el iventario";
			$this->validation->set_message('siexiste',$error);
			return false;
		}
		return true;
	}


	function dummy(){
		echo "<p aling='center'>Redirigiendo la p&aacute;gina</p>";
	}

	function _gconsul($mSQL_p,$cod_bar,$busca,$suple=null){
		if(!empty($suple) AND $this->db->table_exists('suple')){
			$mSQL  ="SELECT codigo FROM suple WHERE suplemen='${cod_bar}' LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				$row = $query->row();
				$busca  =array($suple);
				$cod_bar=$row->codigo;
			}
		}
		foreach($busca AS $b){
			$mSQL  =$mSQL_p." WHERE ${b}='${cod_bar}' LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				return $query;
			}
		}
		if ($this->db->table_exists('barraspos')) {
			$mSQL  ="SELECT codigo FROM barraspos WHERE suplemen=".$this->db->escape($cod_bar)." LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				$row = $query->row();
				$cod_bar=$row->codigo;

				$mSQL  =$mSQL_p." WHERE codigo='${cod_bar}' LIMIT 1";
				$query = $this->db->query($mSQL);
				if($query->num_rows() == 0)
					return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
		return $query;
	}

	function instala(){
		$mSQL="CREATE TABLE `b2b_config` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `proveed` char(5)   NOT NULL COMMENT 'Codigo del proveedor',
		  `url` varchar(100)  NOT NULL,
		  `puerto` int(5) NOT NULL DEFAULT '80',
		  `proteo` varchar(20) NOT NULL DEFAULT 'proteoerp',
		  `usuario` varchar(100) NOT NULL COMMENT 'Codigo de cliente en el proveedor',
		  `clave` varchar(100) NOT NULL,
		  `tipo` char(1) DEFAULT NULL COMMENT 'I para inventario G para gasto',
		  `depo` varchar(4) DEFAULT NULL COMMENT 'Almacen',
		  `margen1` decimal(6,2) DEFAULT NULL COMMENT 'Margen para el precio1',
		  `margen2` decimal(6,2) DEFAULT NULL COMMENT 'Margen para el precio 2',
		  `margen3` decimal(6,2) DEFAULT NULL COMMENT 'Margen para el precio3',
		  `margen4` decimal(6,2) DEFAULT NULL COMMENT 'Margen para el precio4',
		  `margen5` decimal(6,2) DEFAULT NULL COMMENT 'Margen para el precio5 (solo supermercado)',
		  `grupo` varchar(5) DEFAULT NULL COMMENT 'Grupo por defecto',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 COMMENT='Configuracion para los b2b'";
		var_dump($this->db->simple_query($mSQL));

		$mSQL="CREATE TABLE `b2b_scst` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `fecha` date DEFAULT NULL,
		  `numero` varchar(8) DEFAULT NULL,
		  `proveed` varchar(5) DEFAULT NULL,
		  `nombre` varchar(30) DEFAULT NULL,
		  `depo` varchar(4) DEFAULT NULL,
		  `montotot` decimal(17,2) DEFAULT NULL,
		  `montoiva` decimal(17,2) DEFAULT NULL,
		  `montonet` decimal(17,2) DEFAULT NULL,
		  `vence` date DEFAULT NULL,
		  `tipo_doc` char(2) DEFAULT NULL,
		  `control` varchar(8) NOT NULL DEFAULT '',
		  `peso` decimal(12,2) DEFAULT NULL,
		  `estampa` date DEFAULT NULL,
		  `hora` varchar(8) DEFAULT NULL,
		  `usuario` varchar(12) DEFAULT NULL,
		  `nfiscal` varchar(12) DEFAULT NULL,
		  `exento` decimal(17,2) NOT NULL DEFAULT '0.00',
		  `sobretasa` decimal(17,2) NOT NULL DEFAULT '0.00',
		  `reducida` decimal(17,2) NOT NULL DEFAULT '0.00',
		  `tasa` decimal(17,2) NOT NULL DEFAULT '0.00',
		  `montasa` decimal(17,2) DEFAULT NULL,
		  `monredu` decimal(17,2) DEFAULT NULL,
		  `monadic` decimal(17,2) DEFAULT NULL,
		  `serie` char(12) DEFAULT NULL,
		  `pcontrol` char(8) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `proveednum` (`proveed`,`numero`),
		  KEY `proveedor` (`proveed`)
		) ENGINE=MyISAM AUTO_INCREMENT=1";
		var_dump($this->db->simple_query($mSQL));

		$mSQL="CREATE TABLE `b2b_itscst` (
		  `id_scst` int(11) DEFAULT NULL,
		  `fecha` date DEFAULT NULL,
		  `numero` varchar(8) DEFAULT NULL,
		  `proveed` varchar(5) DEFAULT NULL,
		  `depo` varchar(4) DEFAULT NULL,
		  `codigo` varchar(15) DEFAULT NULL,
		  `descrip` varchar(45) DEFAULT NULL,
		  `cantidad` decimal(10,3) DEFAULT NULL,
		  `devcant` decimal(10,3) DEFAULT NULL,
		  `devfrac` int(4) DEFAULT NULL,
		  `costo` decimal(17,2) DEFAULT NULL,
		  `importe` decimal(17,2) DEFAULT NULL,
		  `iva` decimal(5,2) DEFAULT NULL,
		  `montoiva` decimal(17,2) DEFAULT NULL,
		  `garantia` int(3) DEFAULT NULL,
		  `ultimo` decimal(17,2) DEFAULT NULL,
		  `precio1` decimal(15,2) DEFAULT NULL,
		  `precio2` decimal(15,2) DEFAULT NULL,
		  `precio3` decimal(15,2) DEFAULT NULL,
		  `precio4` decimal(15,2) DEFAULT NULL,
		  `estampa` date DEFAULT NULL,
		  `hora` varchar(8) DEFAULT NULL,
		  `usuario` varchar(12) DEFAULT NULL,
		  `licor` decimal(10,2) DEFAULT '0.00',
		  `barras` varchar(15) DEFAULT NULL,
		  `codigolocal` varchar(15) DEFAULT NULL,
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  PRIMARY KEY (`id`),
		  KEY `id_scst` (`id_scst`),
		  KEY `fecha` (`fecha`),
		  KEY `codigo` (`codigo`),
		  KEY `proveedor` (`proveed`),
		  KEY `numero` (`numero`)
		) ENGINE=MyISAM AUTO_INCREMENT=1";
		var_dump($this->db->simple_query($mSQL));
	}
}
