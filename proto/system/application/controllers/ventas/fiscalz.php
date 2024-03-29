<?php require_once(BASEPATH.'application/controllers/validaciones.php');
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
		$select=array('serial','hora','numero','caja','fecha','fecha1','(exento+base+iva+base1+iva1+base2+iva2-ncexento-ncbase-nciva-ncbase1-nciva1-ncbase2-nciva2) AS total');
		$filter->db->select($select);
		$filter->db->from('fiscalz');
		
		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechad->clause  ="where";
		$filter->fechad->db_name ="fecha";
		$filter->fechad->insertValue = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30, date("Y")));
		$filter->fechad->operator=">=";
 		$filter->fechad->group='Fecha';
 		$filter->fechad->append(' Busca en base a la fecha final');
		
		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechah->clause="where";
		$filter->fechah->db_name="fecha";
		$filter->fechah->insertValue = date("Y-m-d");
		$filter->fechah->operator="<=";
		$filter->fechah->group='Fecha';
		$filter->fechah->append(' Busca en base a la fecha final');
		
		$filter->serial = new inputField("Serial","serial");
		$filter->serial->size=20;
		
		$filter->numero= new inputField("Numero","numero");
		$filter->numero->size=20;
		
		$filter->caja= new inputField("Caja","caja");
		$filter->caja->size=5;
		
		$filter->manual = new dropdownField("Manual", "manual");
		$filter->manual->option("","Todos");
		$filter->manual->option("N","N");
		$filter->manual->option("S","S");
		$filter->manual->style = "width:70px";	
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('ventas/fiscalz/dataedit/show/<#serial#>/<#numero#>','<#serial#>');
		$uri_2 = anchor('ventas/fiscalz/dataedit/create/<#serial#>/<#numero#>','Duplicar');
		$uri3 = anchor('reportes/ver/fiscalz','Imprimir');
		$grid = new DataGrid("Lista de Cierre Z");
		//$grid->order_by("serial","asc");
		$grid->per_page=15;

		
		$grid->column("Serial",$uri);
		$grid->column("Numero","numero");
		$grid->column_orderby("Caja","caja",'caja');
		$grid->column_orderby("Fecha Inicial","<dbdate_to_human><#fecha1#></dbdate_to_human>",'fecha' ,"align='center'");
		$grid->column_orderby("Fecha Final"  ,"<dbdate_to_human><#fecha#></dbdate_to_human>" ,'fecha1',"align='center'");
		$grid->column("Hora"  ,"hora","align='center'");
		$grid->column("Total","<number_format><#total#>|2|,|.</number_format>",'align=right');
		$grid->column("Duplicar"                    ,$uri_2    ,"align='center'");
		
		$grid->add("ventas/fiscalz/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$uri3.$grid->output;
		$data['title']   = "<h1>Cierre Z</h1>";
		$data["head"]    = $this->rapyd->get_head();	
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($status='',$id='',$id2=''){ 
		$this->rapyd->load("dataobject","dataedit");
		
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
			$("#banco1").change(function () { acuenta(); }).change();
			$("#banco2").change(function () { acuenta(); }).change();
		});';
			
		$do = new DataObject("fiscalz");
		if(($status=="create") && !empty($id) && !empty($id2)){
			$do->load(array("serial"=> $id,"numero"=> $id2));
			$do->set('numero', '');
			$do->set('hora', '');
			$do->set('base', '');
			$do->set('iva', '');
			$do->set('base1', '');
			$do->set('iva1', '');
			$do->set('iva2', '');
			$do->set('base2', '');
			$do->set('exento', '');
			$do->set('ncexento', '');
			$do->set('ncbase', '');
			$do->set('nciva', '');
			$do->set('ncbase1', '');
			$do->set('nciva1', '');
			$do->set('ncbase2', '');
			$do->set('nciva2', '');
			$do->set('ncnumero', '');
			
		}
		
		$edit = new DataEdit("Cierre Z",$do);
		$edit->back_url = site_url("ventas/fiscalz/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->caja = new inputField("Caja", "caja");
		$edit->caja->size = 6;
		$edit->caja->maxlength =4;
		$edit->caja->rule="trim";
		
		$edit->serial = new inputField("Serial","serial");
		$edit->serial->size =15;
		$edit->serial->maxlength =12;
		$edit->serial->mode = "autohide"; 
		$edit->serial->rule="trim";

		$edit->numero = new inputField("Numero","numero");
		$edit->numero->size = 6;
		$edit->numero->maxlength =4;
		$edit->numero->mode = "autohide";
		$edit->numero->rule="trim";		
		
		$edit->fecha1 = new DateonlyField("Fecha Inicial","fecha1","d/m/Y");
		$edit->fecha1->insertValue = date("Y-m-d");
		$edit->fecha1->size = 12;
		
		$edit->fecha = new DateonlyField("Fecha Final","fecha","d/m/Y");
		$edit->fecha->insertValue = date("Y-m-d");
		$edit->fecha->size = 12;
		
		$edit->factura = new inputField("Factura","factura");
		$edit->factura->size =10;
		$edit->factura->maxlength =8;
		$edit->factura->rule="trim";
		
		$edit->hora = new inputField("Hora","hora");
		$edit->hora->size =8;
		$edit->hora->rule='trim|callback_chhora';
		$edit->hora->append('hh:mm:ss');
		$edit->hora->rule="trim";
		
		$edit->base = new inputField("Ventas Base","base");
		$edit->base->size = 15;
		$edit->base->css_class='inputnum';
		$edit->base->maxlength =12;
		$edit->base->group='Seg&uacute;n Alicuota General';
		$edit->base->rule="trim";
		
		$edit->iva = new inputField("Ventas Iva","iva");
		$edit->iva->size = 15;
		$edit->iva->css_class='inputnum';
		$edit->iva->maxlength =12;
		$edit->iva->group='Seg&uacute;n Alicuota General';
		$edit->iva->rule="trim";
		
		$edit->base1 = new inputField("Ventas Base","base1");
		$edit->base1->size = 15;
		$edit->base1->css_class='inputnum';
		$edit->base1->maxlength =12;
		$edit->base1->group='Seg&uacute;n Alicuota Reducida';
		$edit->base1->rule="trim";
		
		$edit->iva1 = new inputField("Ventas Iva","iva1");
		$edit->iva1->size = 15;
		$edit->iva1->css_class='inputnum';
		$edit->iva1->maxlength =12;
		$edit->iva1->group='Seg&uacute;n Alicuota Reducida';
		$edit->iva1->rule="trim";
		
		$edit->base2 = new inputField("Ventas Base","base2");
		$edit->base2->size = 15;
		$edit->base2->css_class='inputnum';
		$edit->base2->maxlength =12;
		$edit->base2->group='Seg&uacute;n Alicuota Sobretasa';
		$edit->base2->rule="trim";
		
		$edit->iva2 = new inputField("Ventas Iva","iva2");
		$edit->iva2->size = 15;
		$edit->iva2->css_class='inputnum';
		$edit->iva2->maxlength =12;
		$edit->iva2->group='Seg&uacute;n Alicuota Sobretasa';
		$edit->iva2->rule="trim";

		$edit->exento = new inputField("Monto exento","exento");
		$edit->exento->size = 15;
		$edit->exento->css_class='inputnum';
		$edit->exento->maxlength =12;
		$edit->exento->rule="trim";
		
		$edit->ncexento = new inputField("Nota de Cr&eacute;dito exento","ncexento");
		$edit->ncexento->size = 15;
		$edit->ncexento->css_class='inputnum';
		$edit->ncexento->maxlength =12;
		$edit->ncexento->rule="trim";
		
		$edit->ncbase = new inputField("Nota de Cr&eacute;dito base","ncbase");
		$edit->ncbase->size = 15;
		$edit->ncbase->css_class='inputnum';
		$edit->ncbase->maxlength =12;
		$edit->ncbase->group='Seg&uacute;n Alicuota General';
		$edit->ncbase->rule="trim";
		
		$edit->ncsiva = new inputField("Nota de Cr&eacute;dito iva","nciva");
		$edit->ncsiva->size = 15;
		$edit->ncsiva->css_class='inputnum';
		$edit->ncsiva->maxlength =12;
		$edit->ncsiva->group='Seg&uacute;n Alicuota General';
		$edit->ncsiva->rule="trim";
		
		$edit->ncbase1 = new inputField("Nota de Cr&eacute;dito base","ncbase1");
		$edit->ncbase1->size = 15;
		$edit->ncbase1->css_class='inputnum';
		$edit->ncbase1->maxlength =12;
		$edit->ncbase1->group='Seg&uacute;n Alicuota Reducida';
		$edit->ncbase1->rule="trim";
		
		$edit->ncsiva1 = new inputField("Nota de Cr&eacute;dito iva","nciva1");
		$edit->ncsiva1->size = 15;
		$edit->ncsiva1->css_class='inputnum';
		$edit->ncsiva1->maxlength =12;
		$edit->ncsiva1->group='Seg&uacute;n Alicuota Reducida';
		$edit->ncsiva1->rule="trim";
		
		$edit->ncbase2 = new inputField("Nota de Cr&eacute;dito base","ncbase2");
		$edit->ncbase2->size = 15;
		$edit->ncbase2->css_class='inputnum';
		$edit->ncbase2->maxlength =12;
		$edit->ncbase2->group='Seg&uacute;n Alicuota Sobretasa';
		$edit->ncbase2->rule="trim";
		
		$edit->ncsiva2 = new inputField("Nota de Cr&eacute;dito iva","nciva2");
		$edit->ncsiva2->size = 15;
		$edit->ncsiva2->css_class='inputnum';
		$edit->ncsiva2->maxlength =12;
		$edit->ncsiva2->group='Seg&uacute;n Alicuota Sobretasa';
		$edit->ncsiva2->rule="trim";
		
		$edit->ncnumero = new inputField("Nota de Cr&eacute;dito numero","ncnumero");
		$edit->ncnumero->size =10;
		$edit->ncnumero->maxlength =8;
		$edit->ncnumero->rule="trim";
		
		//$edit->manual = new dropdownField("Manual","manual");
		//$edit->manual->option("N","N");
		//$edit->manual->option("S","S");
		//$edit->manual->style = "width:70px"; 
		//$edit->manual->group='Otros';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output; 		
		$data['title']   = "<h1>Cierre Z</h1>";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	function _post_insert($do){
		$fecha=$do->get('fecha');
		$numero=$do->get('numero');
		logusu('fiscalz',"CIERRE $numero CREADO, FECHA $fecha");
		$this->db->query("UPDATE fiscalz SET manual='S' WHERE numero='$numero'");
	}
	function _post_update($do){
		$fecha=$do->get('fecha');
		$numero=$do->get('numero');
		logusu('fiscalz',"CIERRE $numero MODIFICADO, FECHA $fecha");
		$this->db->query("UPDATE fiscalz SET manual='S' WHERE numero='$numero'");
	}
	function _post_delete($do){
		$fecha=$do->get('fecha');
		$numero=$do->get('numero');
		logusu('fiscalz',"CIERRE $numero ELIMINADO, FECHA $fecha");
	}
	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `fiscalz` (
		  `caja` char(4) default NULL,
		  `serial` char(12) NOT NULL default '',
		  `numero` char(4) NOT NULL default '',
		  `fecha` date default NULL,
		  `factura` char(8) default NULL,
		  `fecha1` date default NULL,
		  `hora` time default NULL,
		  `exento` decimal(12,2) unsigned default NULL,
		  `base` decimal(12,2) unsigned default NULL,
		  `iva` decimal(12,2) unsigned default NULL,
		  `base1` decimal(12,2) unsigned default NULL,
		  `iva1` decimal(12,2) unsigned default NULL,
		  `base2` decimal(12,2) unsigned default NULL,
		  `iva2` decimal(12,2) unsigned default NULL,
		  `ncexento` decimal(12,2) unsigned default NULL,
		  `ncbase` decimal(12,2) unsigned default NULL,
		  `nciva` decimal(12,2) unsigned default NULL,
		  `ncbase1` decimal(12,2) unsigned default NULL,
		  `nciva1` decimal(12,2) unsigned default NULL,
		  `ncbase2` decimal(12,2) unsigned default NULL,
		  `nciva2` decimal(12,2) unsigned default NULL,
		  `ncnumero` char(8) default NULL,
		  PRIMARY KEY  (`serial`,`numero`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `fiscalz` ADD `manual` CHAR(1)DEFAULT 'N' NULL";
    $this->db->simple_query($mSQL);
	}
}
?>