<?php require_once(BASEPATH . 'application/controllers/validaciones.php');
class pfac extends validaciones{
	var $genesal=true;

	function pfac(){
		parent :: Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id(120,1);
	}

	function index(){
		redirect('ventas/pfac/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datagrid', 'datafilter');

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$scli = array(
			'tabla' => 'scli',
			'columnas' => array(
				'cliente' => 'C&oacute;digo Cliente',
				'nombre' => 'Nombre',
				'contacto' => 'Contacto'),
			'filtro' => array('cliente' => 'C&oacute;digo Cliente', 'nombre' => 'Nombre'),
			'retornar' => array('cliente' => 'cod_cli'),
			'titulo' => 'Buscar Cliente');

		$boton = $this->datasis->modbus($scli);

		$filter = new DataFilter('Filtro de Pedidos Clientes', 'pfac');

		$filter->fechad = new dateonlyField('Desde', 'fechad');
		$filter->fechah = new dateonlyField('Hasta', 'fechah');
		$filter->fechad->clause = $filter->fechah->clause   = 'where';
		$filter->fechad->db_name = $filter->fechah->db_name = 'fecha';
		//$filter->fechad->insertValue = date('Y-m-d');
		//$filter->fechah->insertValue = date('Y-m-d');
		$filter->fechah->size = $filter->fechad->size = 10;
		$filter->fechad->operator = '>=';
		$filter->fechah->operator = '<=';
		$filter->fechad->group = "uno";
		$filter->fechah->group = "uno";

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size = 10;
		$filter->numero->group = "dos";

		$filter->cliente = new inputField('Cliente', 'cod_cli');
		$filter->cliente->size = 8;
		$filter->cliente->append($boton);
		$filter->cliente->group = "dos";

		$filter->buttons('reset', 'search');
		$filter->build('dataformfiltro');

		$uri = anchor('ventas/pfac/dataedit/show/<#numero#>', '<#numero#>');
		$uri2 = anchor_popup('formatos/verhtml/PFAC/<#numero#>', 'Ver HTML', $atts);
		$mtool  = "<table background='#554455'><tr>";
		$mtool .= "<td>&nbsp;</td>";

		$mtool .= "<td>&nbsp;<a href='".base_url()."ventas/pfac/dataedit/create'>";
		$mtool .= img(array('src' => 'images/agregar.jpg', 'alt' => 'Agregar Registro', 'title' => 'Agregar Registro','border'=>'0','height'=>'32'));
		$mtool .= "</a>&nbsp;</td>";

		$mtool .= "<td>&nbsp;<a href='javascript:void(0);' ";
		$mtool .= 'onclick="window.open(\''.base_url()."reportes/index/pfac', '_blank', 'width=800, height=600, scrollbars=Yes, status=Yes, resizable=Yes, screenx='+((screen.availWidth/2)-400)+',screeny='+((screen.availHeight/2)-300)+'');".'" heigth="600" width="900" '.'>';
		$mtool .= img(array('src' => 'images/reportes.gif', 'alt' => 'Reportes', 'title' => 'Reportes','border'=>'0','height'=>'32'));
		$mtool .= "</a>&nbsp;</td>";

		$mtool .= "</tr></table>";


		$grid = new DataGrid($mtool);
		$grid->order_by('numero', 'desc');
		$grid->per_page = 50;

		//$grid->column('Vista'    , $uri2, "align='center'");
		$grid->column_orderby('N&uacute;mero', $uri,'numero');
		$grid->column_orderby("Fecha"        , '<dbdate_to_human><#fecha#></dbdate_to_human>','fecha', "align='center'");
		$grid->column_orderby("Cliente"      , 'cod_cli','cod_cli');
		$grid->column_orderby("Nombre"       , 'nombre','nombre');
		$grid->column_orderby('Sub.Total'    , '<nformat><#totals#></nformat>', "totals", "align=right");
		$grid->column_orderby('IVA'          , '<nformat><#iva#></nformat>'   , "iva",    "align=right");
		$grid->column_orderby('Total'        , '<nformat><#totalg#></nformat>', "totalg", "align=right");
		$grid->column_orderby("Referencia"   , 'referen','referen');
		$grid->column_orderby("Factura"      , 'factura','factura');
		$grid->column_orderby("Status"       , 'status', 'status');

		//$grid->add('ventas/pfac/dataedit/create');
		$grid->build('datagridST');

// *************************************
//
//       Para usar SuperTable
//
// *************************************
		$extras = '
<script type="text/javascript">
//<![CDATA[
(function() {
	var mySt = new superTable("demoTable", {
	cssSkin : "sSky",
	fixedCols : 1,
	headerRows : 1,
	onStart : function () {	this.start = new Date();},
	onFinish : function () {document.getElementById("testDiv").innerHTML += "Finished...<br>" + ((new Date()) - this.start) + "ms.<br>";}
	});
})();
//]]>
</script>
';
		$style ='
<style type="text/css">
.fakeContainer { /* The parent container */
    margin: 5px;
    padding: 0px;
    border: none;
    width: 740px; /* Required to set */
    height: 320px; /* Required to set */
    overflow: hidden; /* Required to set */
}
</style>';

		$data['content'] = $grid->output;
		$data['filtro']  = $filter->output;

		$data['script']  = script('jquery.js');
		$data['script'] .= script('jquery-ui.js');
		$data['script'] .= script('jquery.alerts.js');
		$data['script'] .= script('plugins/jquery.numeric.pack.js');
		$data['script'] .= script('plugins/jquery.floatnumber.js');
		$data['script'] .= script('superTables.js');

		$data['style']   = $style;
		$data['style']  .= style('superTables.css');
		$data['style']	.= style("jquery.alerts.css");

		$data['extras']  = $extras;

		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Pedidos Clientes');
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataobject', 'datadetails');

		$modbus = array(
			'tabla' => 'sinv',
			'columnas'=>array(
				'codigo'  =>'C&oacute;digo',
				'descrip' =>'Descripci&oacute;n',
				'precio1' =>'Precio 1',
				'precio2' =>'Precio 2',
				'precio3' =>'Precio 3',
				'existen' =>'Existencia',
				),
			'filtro' => array('codigo' => 'C&oacute;digo', 'descrip' => 'Descripci&oacute;n'),
			'retornar'  => array(
				'codigo'  => 'codigoa_<#i#>',
				'descrip' => 'desca_<#i#>',
				'base1'   => 'precio1_<#i#>',
				'base2'   => 'precio2_<#i#>',
				'base3'   => 'precio3_<#i#>',
				'base4'   => 'precio4_<#i#>',
				'iva'     => 'itiva_<#i#>',
				'tipo'    => 'sinvtipo_<#i#>',
				'peso'    => 'sinvpeso_<#i#>',
				'precio1' => 'itpvp_<#i#>',
				'pond'    => 'itcosto_<#i#>',
			),
			'p_uri' => array(4 => '<#i#>'),
			'titulo' => 'Buscar Articulo',
			'where' => '`activo` = "S"',
			'script' => array('post_modbus_sinv(<#i#>)')
		);
		$btn = $this->datasis->p_modbus($modbus, '<#i#>');

		$mSCLId = array(
			'tabla'    => 'scli',
			'columnas' => array(
				'cliente' => 'C&oacute;digo Cliente',
				'nombre'  => 'Nombre',
				'cirepre' => 'Rif/Cedula',
				'dire11'  => 'Direcci&oacute;n',
				'tipo' => 'Tipo'),
			'filtro'   => array('cliente' => 'C&oacute;digo Cliente', 'nombre' => 'Nombre'),
			'retornar' => array('cliente' => 'cod_cli', 'nombre' => 'nombre', 'rifci' => 'rifci',
				'dire11' => 'direc', 'tipo' => 'sclitipo'),
			'titulo' => 'Buscar Cliente',
			'script' => array('post_modbus_scli()'));
		$boton = $this->datasis->modbus($mSCLId);

		$inven=array();
		$query=$this->db->query('SELECT TRIM(codigo) AS codigo ,TRIM(descrip) AS descrip,tipo,base1,base2,base3,base4,iva,peso,precio1,pond FROM sinv WHERE activo=\'S\'');
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$ind='_'.$row->codigo;
				$inven[$ind]=array($row->descrip,$row->tipo,$row->base1,$row->base2,$row->base3,$row->base4,$row->iva,$row->peso,$row->precio1,$row->pond);
			}
		}
		$jinven=json_encode($inven);

		$do = new DataObject('pfac');
		$do->rel_one_to_many('itpfac', 'itpfac', array('numero' => 'numa'));
		$do->pointer('scli' , 'scli.cliente=pfac.cod_cli', 'tipo AS sclitipo', 'left');
		$do->rel_pointer('itpfac', 'sinv', 'itpfac.codigoa=sinv.codigo', 'sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo,sinv.precio1 As sinvprecio1,sinv.pond AS sinvpond');

		$edit = new DataDetails('Pedidos', $do);
		$edit->back_url = site_url('ventas/pfac/filteredgrid');
		$edit->set_rel_title('itpfac', 'Producto <#o#>');

		$edit->pre_process('insert' , '_pre_insert');
		$edit->pre_process('update' , '_pre_update');
		$edit->pre_process('delete' , '_pre_delete');
		$edit->post_process('insert', '_post_insert');
		$edit->post_process('update', '_post_update');
		$edit->post_process('delete', '_post_delete');

		$edit->fecha = new DateonlyField('Fecha', 'fecha', 'd/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;

		$edit->vd = new dropdownField ('Vendedor', 'vd');
		$edit->vd->options('SELECT vendedor, CONCAT(vendedor,\' \',nombre) nombre FROM vend ORDER BY vendedor');
		$edit->vd->style = 'width:200px;';
		$edit->vd->size = 5;

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode = 'autohide';
		$edit->numero->maxlength = 8;
		$edit->numero->apply_rules = false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when = array('show', 'modify');

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->readonly = true;
		$edit->peso->size = 10;

		$edit->cliente = new inputField('Cliente', 'cod_cli');
		$edit->cliente->size = 6;
		$edit->cliente->rule = 'required';
		$edit->cliente->maxlength = 5;
		$edit->cliente->append($boton);

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 30;
		$edit->nombre->maxlength = 40;
		$edit->nombre->rule = 'required';

		$edit->rifci = new inputField('RIF/CI', 'rifci');
		$edit->rifci->autocomplete = false;
		$edit->rifci->size = 15;

		$edit->direc = new inputField('Direcci&oacute;n', 'direc');
		$edit->direc->size = 40;

		$edit->observa = new inputField('Observaciones', 'observa');
		$edit->observa->size = 50;

		$edit->observ1 = new inputField('Observaciones', 'observ1');
		$edit->observ1->size = 50;

		// Para saber que precio se le va a dar al cliente
		$edit->sclitipo = new hiddenField('', 'sclitipo');
		$edit->sclitipo->db_name = 'sclitipo';
		$edit->sclitipo->pointer = true;
		$edit->sclitipo->insertValue = 1;

		// Campos para el detalle
		$edit->codigoa = new inputField('C&oacute;digo <#o#>', 'codigoa_<#i#>');
		$edit->codigoa->size = 12;
		$edit->codigoa->db_name = 'codigoa';
		//$edit->codigoa->readonly = true;
		$edit->codigoa->rel_id = 'itpfac';
		$edit->codigoa->rule = 'required|callback_chcodigoa';
		$edit->codigoa->onkeyup = 'OnEnter(event,<#i#>)';
		$edit->codigoa->append($btn);

		$edit->desca = new inputField('Descripci&oacute;n <#o#>', 'desca_<#i#>');
		$edit->desca->size = 32;
		$edit->desca->db_name = 'desca';
		$edit->desca->maxlength = 50;
		$edit->desca->readonly = true;
		$edit->desca->rel_id = 'itpfac';

		$edit->cana = new inputField('Cantidad <#o#>', 'cana_<#i#>');
		$edit->cana->db_name = 'cana';
		$edit->cana->css_class = 'inputnum';
		$edit->cana->rel_id = 'itpfac';
		$edit->cana->maxlength = 10;
		$edit->cana->size = 6;
		$edit->cana->rule = 'required|positive';
		$edit->cana->autocomplete = false;
		$edit->cana->onkeyup = 'importe(<#i#>)';

		$edit->preca = new inputField('Precio <#o#>', 'preca_<#i#>');
		$edit->preca->db_name = 'preca';
		$edit->preca->css_class = 'inputnum';
		$edit->preca->rel_id = 'itpfac';
		$edit->preca->size = 10;
		$edit->preca->rule = 'required|positive|callback_chpreca[<#i#>]';
		$edit->preca->readonly = true;

		$edit->tota = new inputField('importe <#o#>', 'tota_<#i#>');
		$edit->tota->db_name = 'tota';
		$edit->tota->size = 10;
		$edit->tota->css_class = 'inputnum';
		$edit->tota->rel_id = 'itpfac';
		
		for($i = 1;$i <= 4;$i++){
			$obj = 'precio' . $i;
			$edit->$obj = new hiddenField('Precio <#o#>', $obj . '_<#i#>');
			$edit->$obj->db_name = 'sinv' . $obj;
			$edit->$obj->rel_id = 'itpfac';
			$edit->$obj->pointer = true;
		}

		$edit->itiva = new hiddenField('', 'itiva_<#i#>');
		$edit->itiva->db_name = 'iva';
		$edit->itiva->rel_id = 'itpfac';

		$edit->itpvp = new hiddenField('', 'itpvp_<#i#>');
		$edit->itpvp->db_name = 'pvp';
		$edit->itpvp->rel_id = 'itpfac';

		$edit->itcosto = new hiddenField('', 'itcosto_<#i#>');
		$edit->itcosto->db_name = 'costo';
		$edit->itcosto->rel_id = 'itpfac';

		$edit->sinvpeso = new hiddenField('', 'sinvpeso_<#i#>');
		$edit->sinvpeso->db_name = 'sinvpeso';
		$edit->sinvpeso->rel_id = 'itpfac';
		$edit->sinvpeso->pointer = true;

		$edit->sinvtipo = new hiddenField('', 'sinvtipo_<#i#>');
		$edit->sinvtipo->db_name = 'sinvtipo';
		$edit->sinvtipo->rel_id = 'itpfac';
		$edit->sinvtipo->pointer = true;
		// fin de campos para detalle

		$edit->ivat = new hiddenField('Impuesto', 'iva');
		$edit->ivat->css_class = 'inputnum';
		$edit->ivat->readonly = true;
		$edit->ivat->size = 10;

		$edit->totals = new hiddenField('Sub-Total', 'totals');
		$edit->totals->css_class = 'inputnum';
		$edit->totals->readonly = true;
		$edit->totals->size = 10;

		$edit->totalg = new hiddenField('Monto Total', 'totalg');
		$edit->totalg->css_class = 'inputnum';
		$edit->totalg->readonly = true;
		$edit->totalg->size = 10;

		$edit->usuario = new autoUpdateField('usuario', $this->session->userdata('usuario'), $this->session->userdata('usuario'));

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back', 'add_rel');

		if($this->genesal){
			$edit->build();

			$conten['inven'] = $jinven;
			$conten['form']  = & $edit;
			$data['content'] = $this->load->view('view_pfac', $conten, true);
			$data['title']   = heading('Pedidos No. '.$edit->numero->value);

			$data['head']    = script('jquery.js');
			$data['head']   .= script('jquery-ui.js');
			$data['head']   .= script('plugins/jquery.numeric.pack.js');
			$data['head']   .= script('plugins/jquery.floatnumber.js');
			$data['head']   .= phpscript('nformat.js');
			$data['head']   .= style('redmond/jquery-ui-1.8.1.custom.css');
			$data['head']   .= $this->rapyd->get_head();


			$this->load->view('view_ventanas', $data);
		}else{
			$edit->on_save_redirect=false;
			$edit->build();

			if($edit->on_success()){
				echo 'Pedido Guardado';
			}elseif($edit->on_error()){
				echo html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
		}
	}

	function pos(){
		$this->rapyd->load('dataobject','datadetails');

		$mSCLId=array(
		'tabla'   =>'scli',
		'columnas'=>array(
			'cliente' =>'C&oacute;digo Cliente',
			'nombre'=>'Nombre', 
			'cirepre'=>'Rif/Cedula',
			'dire11'=>'Direcci&oacute;n',
			'tipo'=>'Tipo'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cod_cli','nombre'=>'nombre','rifci'=>'rifci',
					'dire11'=>'direc'),
		'titulo'  =>'Buscar Cliente',
		'script'  => array('post_modbus_scli()'));
		$boton =$this->datasis->modbus($mSCLId);

		$query = $this->db->query("SELECT tipo,nombre FROM tarjeta ORDER BY tipo");
		foreach ($query->result() as $row){
			$sfpa[$row->tipo]=$row->nombre;
		}

		$tban['']='Banco';
		$query = $this->db->query("SELECT cod_banc,nomb_banc FROM tban WHERE cod_banc<>'CAJ' ORDER BY nomb_banc");
		foreach ($query->result() as $row){
			$tban[$row->cod_banc]=$row->nomb_banc;
		}

		$conten=array();
		$conten['sfpa']  = $sfpa;
		$conten['tban']  = $tban;
		$data['content'] = $this->load->view('view_pos_pfac', $conten,true);
		$data['title']   = '';
		$data['head']    = style('redmond/jquery-ui-1.8.1.custom.css');
		$data['head']   .= style('ui.jqgrid.css');
		$data['head']   .= style('ui.multiselect.css');
		$data['head']   .= script('jquery.js');
		$data['head']   .= script('interface.js');
		$data['head']   .= script('jquery-ui.js');
		$data['head']   .= script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		$data['head']   .= script('jquery.layout.js');
		$data['head']   .= script('i18n/grid.locale-sp.js');
		$data['head']   .= script('ui.multiselect.js');
		$data['head']   .= script('jquery.jqGrid.min.js');
		$data['head']   .= script('jquery.tablednd.js');
		$data['head']   .= script('jquery.contextmenu.js');
		$data['head']   .= script('plugins/jquery.numeric.pack.js');
		$data['head']   .= script('plugins/jquery.floatnumber.js');
		$data['head']   .= phpscript('nformat.js');

		$this->load->view('view_ventanas_sola', $data);
	}

	function creapfac(){
		foreach($_POST as $ind=>$val){
			$matches=array();
			$_POST['fecha']=date('d/m/Y');
			
			if(preg_match('/codigoa_(?P<id>\d+)/', $ind, $matches) > 0){
				$id     = $matches['id'];
				$precio = $_POST['precio_'.$id];
				$iva    = $_POST['itiva_'.$id];
				$_POST['preca_'.$id] = round($precio*100/(100+$iva),2);
			}
		}
		//print_r($_POST);
		$this->genesal=false;
		$rt=$this->dataedit();
		echo $rt;
	}

	function _pre_insert($do){
		$numero = $this->datasis->fprox_numero('npfac');
		$do->set('numero', $numero);
		$transac = $this->datasis->fprox_numero('ntransa');
		$do->set('transac', $transac);
		$fecha = $do->get('fecha');
		$vd = $do->get('vd');

		$iva = $totals = 0;
		$cana = $do->count_rel('itpfac');
		for($i = 0;$i < $cana;$i++){
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			$itpreca = $do->get_rel('itpfac', 'preca', $i);
			$itiva   = $do->get_rel('itpfac', 'iva', $i);
			$ittota  = $itpreca * $itcana;
			$do->set_rel('itpfac', 'tota' , $ittota, $i);
			$do->set_rel('itpfac', 'fecha' , $fecha , $i);
			$do->set_rel('itpfac', 'vendedor', $vd , $i);

			$iva    += $ittota * ($itiva / 100);
			$totals += $ittota;
			$do->set_rel('itpfac', 'mostrado', $iva + $ittota, $i);
		}
		$totalg = $totals + $iva;

		$do->set('totals' , round($totals , 2));
		$do->set('totalg' , round($totalg , 2));
		$do->set('iva'    , round($iva    , 2));
		return true;
	}
	
	function _pre_update($do){
		$codigo = $do->get('numero');
		$fecha  = $do->get('fecha');
		$vd     = $do->get('vd');

		$iva = $totals = 0;
		$cana = $do->count_rel('itpfac');
		for($i = 0;$i < $cana;$i++){
			$itcana  = $do->get_rel('itpfac', 'cana' , $i);
			$itpreca = $do->get_rel('itpfac', 'preca', $i);
			$itiva   = $do->get_rel('itpfac', 'iva'  , $i);
			$ittota  = $itpreca * $itcana;
			$do->set_rel('itpfac', 'tota'    , $ittota, $i);
			$do->set_rel('itpfac', 'fecha'   , $fecha , $i);
			$do->set_rel('itpfac', 'vendedor', $vd    , $i);

			$iva    += $ittota*$itiva/100;
			$totals += $ittota;
			$do->set_rel('itpfac', 'mostrado', $iva + $ittota, $i);
		}
		$totalg = $totals + $iva;

		$do->set('totals' , round($totals , 2));
		$do->set('totalg' , round($totalg , 2));
		$do->set('iva'    , round($iva    , 2));

		$mSQL='UPDATE sinv JOIN itpfac ON sinv.codigo=itpfac.codigoa SET sinv.exdes=sinv.exdes-itpfac.cana WHERE itpfac.numa='.$this->db->escape($codigo);
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'pfac'); }
		return true;
	}

	function chcodigoa($codigo){
		$cana=$this->datasis->dameval('SELECT COUNT(*) FROM sinv WHERE activo=\'S\' AND codigo='.$this->db->escape($codigo));
		if(empty($cana) || $cana==0){
			$this->validation->set_message('chcodigoa', 'El campo %s contiene un codigo no v&aacute;lido o inactivo');
			return false;
		}
		return true;
	}

	function _post_insert($do){
		$cana = $do->count_rel('itpfac');
		for($i = 0;$i < $cana;$i++){
			$itcodigo= $do->get_rel('itpfac', 'codigoa', $i);
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			$mSQL = "UPDATE sinv SET exdes=exdes+$itcana WHERE codigo=".$this->db->escape($itcodigo);

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'pfac'); }
		}

		$codigo = $do->get('numero');
		logusu('pfac', "Pedido $codigo CREADO");
	}

	function chpreca($preca, $ind){
		$codigo = $this->input->post('codigoa_' . $ind);
		$precio4 = $this->datasis->dameval('SELECT base4 FROM sinv WHERE codigo=' . $this->db->escape($codigo));
		if($precio4 < 0) $precio4 = 0;

		if($preca < $precio4){
			$this->validation->set_message('chpreca', 'El art&iacute;culo ' . $codigo . ' debe contener un precio de al menos ' . nformat($precio4));
			return false;
		}else{
			return true;
		}
	}
	
	function _post_update($do){
		$cana = $do->count_rel('itpfac');
		for($i = 0;$i < $cana;$i++){
			$itcodigo= $do->get_rel('itpfac', 'codigoa', $i);
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			$mSQL = "UPDATE sinv SET exdes=exdes+$itcana WHERE codigo=".$this->db->escape($itcodigo);

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'pfac'); }
		}
		$codigo = $do->get('numero');
		logusu('pfac', "Pedido $codigo MODIFICADO");
	}
	
	function _pre_delete($do){
		$codigo = $do->get('numero');
		$mSQL='UPDATE sinv JOIN itpfac ON sinv.codigo=itpfac.codigoa SET sinv.exdes=sinv.exdes-itpfac.cana WHERE itpfac.numa='.$this->db->escape($codigo);
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'pfac'); }
		return true;
	}
	
	function _post_delete($do){
		$codigo = $do->get('numero');
		logusu('pfac', "Pedido $codigo ELIMINADO");
	}
}