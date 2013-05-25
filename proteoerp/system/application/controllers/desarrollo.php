<?php
/***********************************************************************
 *
 *
 *
 *
 *
 *
 *
 *
*/

class Desarrollo extends Controller{

	function Desarrollo(){
		parent::Controller();
	}

	function index(){

		$styles  = "\n<!-- Estilos -->\n";
		//$styles .= style('rapyd.css');
		//$styles .= style('ventanas.css');
		$styles .= style('themes/proteo/proteo.css');
		$styles .= style("themes/ui.jqgrid.css");
		//$styles .= style("themes/ui.multiselect.css");

		$styles .= '
<style>
html, body {margin: 0;padding: 0;overflow: hidden;font-size: 90%;}
#LeftPane {overflow: auto;}
#RightPane {padding: 2px;overflow: auto;}
.ui-tabs-nav li {position: relative;}
.ui-tabs-selected a span {padding-right: 10px;}
.ui-tabs-close {display: none;position: absolute;top: 3px;right: 0px;z-index: 800;width: 16px;height: 14px;font-size: 10px; font-style: normal;cursor: pointer;}
.ui-tabs-selected .ui-tabs-close {display: block;}
.ui-layout-west .ui-jqgrid tr.jqgrow td { border-bottom: 0px none;}
.ui-datepicker {z-index:1200;}
.rotate { -webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg); filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);}
</style>		
		';

		$script  = "\n<!-- JQUERY -->\n";
		$script .= script('jquery-min.js');
		$script .= script('jquery-migrate-min.js'); 
		$script .= script('jquery-ui.custom.min.js');

		$script .= script("jquery.layout.js");
		$script .= script("i18n/grid.locale-sp.js");

		$script .= '
<script type="text/javascript">
	$.jgrid.no_legacy_api = true;
	$.jgrid.useJSON = true;
</script>
';
		$script .= script("ui.multiselect.js");
		$script .= script("jquery.jqGrid.min.js");
		$script .= script("jquery.tablednd.js");
		$script .= script("jquery.contextmenu.js");

		$script .= '
<script type="text/javascript">

$(document).ready(function(){
	$(\'body\').layout({
		resizerClass: \'ui-state-default\',
		west__onresize: function (pane, $Pane) {
			$("#west-grid").jqGrid(\'setGridWidth\',$Pane.innerWidth()-2);
		}
	});
	$.jgrid.defaults = $.extend($.jgrid.defaults,{loadui:"enable"});
	var maintab = $(\'#tabs\',\'#RightPane\').tabs({
        add: function(e, ui) {
            // append close thingy
            $(ui.tab).parents(\'li:first\')
                .append(\'<span class="ui-tabs-close ui-icon ui-icon-close" title="Close Tab"></span>\')
                .find(\'span.ui-tabs-close\')
				.show()
                .click(function() {
                    maintab.tabs(\'remove\', $(\'li\', maintab).index($(this).parents(\'li:first\')[0]));
                });
            // select just added tab
            maintab.tabs(\'select\', \'#\' + ui.panel.id);
        }
    });
    
	$("#west-grid").jqGrid({
		ajaxGridOptions : { type: "POST "},
        url: "'.site_url('desarrollo/menu').'/",
        datatype: "xml",
        height: "auto",
        pager: false,
        loadui: "disable",
        colNames: ["id","Herramientas","url"],
        colModel: [
            {name: "id",   width:1,   hidden:true, key:true},
            {name: "menu", width:150, resizable: false, sortable:false},
            {name: "url",   width:1,  hidden:true}
        ],
        treeGrid: true,
		caption: "Desarrollo",
        ExpandColumn: "menu",
        autowidth: true,
        rowNum: 200,
        ExpandColClick: true,
        treeIcons: {leaf:\'ui-icon-document-b\'},
        onSelectRow: function(rowid) {
            var treedata = $("#west-grid").jqGrid(\'getRowData\',rowid);
            if(treedata.isLeaf=="true") {
                //treedata.url
                var st = "#t"+treedata.id;
				if($(st).html() != null ) {
					maintab.tabs(\'select\',st);
				} else {
					maintab.tabs(\'add\',st, treedata.menu);
					//$(st,"#tabs").load(treedata.url);
					$.ajax({
						url: treedata.url,
						type: "GET",
						dataType: "html",
						complete : function (req, err) {
							$(st,"#tabs").append(req.responseText);

							var clck = \'<p style="border: 1px solid; background-color: lemonchiffon; width:654px;height:25px;margin-bottom: 8px;padding-top: 8px;text-align: center">\';
							clck += \'<b>Please, support the jqGrid project by clicking on our sponsors ad! </b></p>\';
 
							var fs = "";

							$(st,"#tabs").append(clck);
							//$(st,"#tabs").append(fs);
						}
					});
				}
            }
        }
    });
	
// end splitter

});
</script>
';

/*
							try { 
								var pageTracker = _gat._getTracker("UA-5463047-4"); 
								pageTracker._trackPageview(); 
							} 
							catch(err) {};



var fs = \'
<iframe src="adds.html"  style="width:336px; height:290px;" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"/>\&nbsp;&nbsp;&nbsp;&nbsp;
<iframe src="adds3.html" style="width:336px; height:290px;" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"/>\<br/>
<iframe src="adds2.html" style="width:728px; height:95px;"  scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"/><br>\&nbsp;&nbsp;&nbsp;&nbsp;
<iframe src="adds4.html" style="width:728px; height:95px;"  scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"/>\
\';
 
*/


		$title = "
<div id='encabe'>
<table width='98%'>
	<tr>
		<td>".heading('Herramientas de Desarrollo')."</td>
		<td align='right' width='40'>".image('cerrar.png','Cerrar Ventana',array('onclick'=>'window.close()','height'=>'20'))."</td>
	</tr>
</table>
</div>
";

		$tabla  = '
	<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
	<table id="west-grid"></table>
	</div> <!-- #LeftPane -->
	<div id="RightPane" class="ui-layout-center ui-helper-reset ui-widget-content" ><!-- Tabs pane -->

	<div id="switcher"></div>
		<div id="tabs" class="jqgtabs">
			<ul>
				<li><a href="#tabs-1">Desarrollo</a></li>
			</ul>
			<div id="tabs-1" style="font-size:10px;">


			</div>
		</div>
	</div> <!-- #RightPane -->
';

/*
				<iframe src="adds_c.html" style="width:728px; height:100px;" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"/>


<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try { var pageTracker = _gat._getTracker("UA-5463047-4"); pageTracker._trackPageview(); } catch(err) {}
</script>
';
*/
		
		$data['content'] = $tabla;
		$data['title']   = $title; 
		$data['head']    = $styles;
		$data['head']   .= $script;
		
		$this->load->view('view_ventanas_lite',$data);

/*

	function camposdb(){
	}

	function lcamposdb(){
	}

	function acamposdb(){
	}

	function ccamposdb(){
	}
*/

	}

	function camposdb(){
		$db=$this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla');
		}
		$query = $this->db->query("DESCRIBE $db");

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$str='$data[\''.$row->Field."']";
				$str=str_pad($str,20);
				echo $str."='';\n";
			}
		}
	}

	function lcamposdb(){
		$db =$this->uri->segment(3);
		$pre=$this->uri->segment(4);
		if($pre!==FALSE)
			$ant="$pre.";
		else
			$ant='';
		if($db===false){
			exit('Debe especificar en la uri la tabla');
		}
		$query = $this->db->query("DESCRIBE $db");

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$str=$row->Field.",";
				echo $ant.$str;
			}
		}
	}

	function acamposdb(){
		$db =$this->uri->segment(3);
		$pre=$this->uri->segment(4);
		if($pre!==FALSE)
			$ant="$pre.";
		else
			$ant='';
		if($db===false){
			exit('Debe especificar en la uri la tabla');
		}
		$query = $this->db->query("DESCRIBE $db");

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$str=$row->Field.'","';
				echo $ant.$str;
			}
		}
	}

	function ccamposdb(){
		$db =$this->uri->segment(3);
		$pre=$this->uri->segment(4);
		if($pre!==FALSE)
			$ant="$pre.";
		else
			$ant='';
		if($db===false){
			exit('Debe especificar en la uri la tabla');
		}
		$query = $this->db->query("DESCRIBE $db");

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$str="'$row->Field',";
				echo $ant.$str;
			}
		}
	}

	function genecrud($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');

		$crud ="\n\t".'function dataedit(){'."\n";
		$crud.="\t\t".'$this->rapyd->load(\'dataedit\');'."\n\n";
		$crud.="\t\t".'$edit = new DataEdit($this->tits, \''.$tabla.'\');'."\n\n";
		$crud.="\t\t".'$edit->back_url = site_url($this->url.\'filteredgrid\');'."\n\n";

		$crud.="\t\t".'$edit->post_process(\'insert\',\'_post_insert\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'update\',\'_post_update\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'delete\',\'_post_delete\');'."\n";
		$crud.="\t\t".'$edit->pre_process(\'insert\',\'_pre_insert\');'."\n";
		$crud.="\t\t".'$edit->pre_process(\'update\',\'_pre_update\');'."\n";
		$crud.="\t\t".'$edit->pre_process(\'delete\',\'_pre_delete\');'."\n";

		$crud.="\n";

		//$fields = $this->db->field_data($tabla);
		$mSQL="DESCRIBE $tabla";
		$query = $this->db->query("DESCRIBE $tabla");
		foreach ($query->result() as $field){

			if($field->Field=='usuario'){
				$crud.="\t\t".'$edit->usuario = new autoUpdateField(\'usuario\',$this->session->userdata(\'usuario\'),$this->session->userdata(\'usuario\'));'."\n\n";
			}elseif($field->Field=='estampa'){
				$crud.="\t\t".'$edit->estampa = new autoUpdateField(\'estampa\' ,date(\'Ymd\'), date(\'Ymd\'));'."\n\n";
			}elseif($field->Field=='hora'){
				$crud.="\t\t".'$edit->hora    = new autoUpdateField(\'hora\',date(\'H:i:s\'), date(\'H:i:s\'));'."\n\n";
			}elseif($field->Field=='id'){
				continue;
			}else{
				preg_match('/(?P<tipo>\w+)(\((?P<length>[0-9\,]+)\)){0,1}/', $field->Type, $matches);
				if(isset($matches['length'])){
					$def=explode(',',$matches['length']);
				}else{
					$def[0]=8;
				}

				if(strrpos($field->Type,'date')!==false){
					$input='date';
				}elseif(strrpos($field->Type,'text')!==false){
					$input= 'textarea';
				}else{
					$input='input';
				}

				$crud.="\t\t".'$edit->'.$field->Field.' = new '.$input."Field('".ucfirst($field->Field)."','$field->Field');\n";

				if(preg_match("/decimal/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='max_length[".$def[0]."]|numeric';\n";
					$crud.="\t\t".'$edit->'.$field->Field."->css_class='inputnum';\n";
				}elseif(preg_match("/integer|int/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='max_length[".$def[0]."]|integer';\n";
					$crud.="\t\t".'$edit->'.$field->Field."->css_class='inputonlynum';\n";
				}elseif(preg_match("/date/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='chfecha';\n";
					$crud.="\t\t".'$edit->'.$field->Field."->calendar=false;\n";
				}else{
					$crud.="\t\t".'$edit->'.$field->Field."->rule='max_length[".$def[0]."]';\n";
				}

				if(strrpos($field->Type,'text')===false){
					$crud.="\t\t".'$edit->'.$field->Field.'->size ='.($def[0]+2).";\n";
					$crud.="\t\t".'$edit->'.$field->Field.'->maxlength ='.($def[0]).";\n";
				}else{
					$crud.="\t\t".'$edit->'.$field->Field."->cols = 70;\n";
					$crud.="\t\t".'$edit->'.$field->Field."->rows = 4;\n";
				}
				$crud.="\n";
			}
		}

		$crud.="\t\t".'$edit->buttons(\'modify\', \'save\', \'undo\', \'delete\', \'back\');'."\n";
		$crud.="\t\t".'$edit->build();'."\n\n";

		$crud.="\t\t".'$script= \'<script type="text/javascript" > '."\n";
		$crud.="\t\t".'$(function() {'."\n";

		$crud.="\t\t\t".'$(".inputnum").numeric(".");'."\n";
		$crud.="\t\t\t".'$(".inputonlynum").numeric();'."\n";

		$crud.="\t\t\t".'$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });'."\n";

		$crud.="\t\t".'});'."\n";
		$crud.="\t\t".'</script>\';'."\n\n";

		$crud.="\t\t".'$data[\'content\'] = $edit->output;'."\n";
		$crud.="\t\t".'$data[\'head\']    = $this->rapyd->get_head();'."\n";
		$crud.="\t\t".'$data[\'script\']  = script(\'jquery.js\').script(\'plugins/jquery.numeric.pack.js\').script(\'plugins/jquery.floatnumber.js\');'."\n";
		$crud.="\t\t".'$data[\'script\'] .= $script;'."\n";
		$crud.="\t\t".'$data[\'title\']   = heading($this->tits);'."\n";
		$crud.="\t\t".'$this->load->view(\'view_ventanas\', $data);'."\n\n";
		$crud.="\t".'}'."\n";

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}


	//******************************************************************
	//
	//   Genera Reporte
	//
	//******************************************************************
	function generepo($tabla=null){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');
		$this->genefilter($tabla, true, true );
	}

	//******************************************************************
	//
	//   Genera la seccion de filtro para el Crud
	//
	//
	//******************************************************************
	function genefilter($tabla=null,$s=true, $repo=false ){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');
		$mt1 = "\n\t";
		$mt2 = "\n\t\t";
		$mt3 = "\n\t\t\t";

		if ( $repo ){
			$mt1   = "\n";
			$mt2   = "\n";
			$mt3   = "\n\t";

			$crud  = '$filter = new DataFilter("Filtro", \''.$tabla.'\');';
			$crud .= $mt1.'$filter->attributes=array(\'onsubmit\'=>\'is_loaded()\');'."\n";

		}else{
			$crud  = $mt1.'function filteredgrid(){';
			$crud .= $mt2.'$this->rapyd->load(\'datafilter\',\'datagrid\');'."\n";
			$crud .= $mt2.'$filter = new DataFilter($this->titp, \''.$tabla.'\');'."\n";
		}

		//$fields = $this->db->field_data($tabla);
		$mSQL="DESCRIBE $tabla";
		$query = $this->db->query("DESCRIBE $tabla");
		$key=array();
		foreach ($query->result() as $field){
				if($field->Key=='PRI')$key[]=$field->Field;

				if($field->Field=='id'){
					continue;
				}

				preg_match('/(?P<tipo>\w+)(\((?P<length>[0-9\,]+)\)){0,1}/', $field->Type, $matches);
				if(isset($matches['length'])){
					$def=explode(',',$matches['length']);
				}else{
					$def[0]=8;
				}

				if(strrpos($field->Type,'date')!==false){
					$input='date';
				}elseif(strrpos($field->Type,'text')!==false){
					$input= 'textarea';
				}else{
					$input='input';
				}

				$crud.=$mt2.'$filter->'.$field->Field.' = new '.$input."Field('".ucfirst($field->Field)."','$field->Field');";

				if(preg_match("/decimal|integer/i",$field->Type)){
					$crud.=$mt2.'$filter->'.$field->Field."->rule      ='max_length[".$def[0]."]|numeric';";
					$crud.=$mt2.'$filter->'.$field->Field."->css_class ='inputnum';";
				}elseif(preg_match("/date/i",$field->Type)){
					$crud.=$mt2.'$filter->'.$field->Field."->rule      ='chfecha';";
				}else{
					$crud.=$mt2.'$filter->'.$field->Field."->rule      ='max_length[".$def[0]."]';";
				}

				if(strrpos($field->Type,'text')===false){
					if($def[0]<80){
						$crud.=$mt2.'$filter->'.$field->Field.'->size      ='.($def[0]+2).";";
					}
					$crud.=$mt2.'$filter->'.$field->Field.'->maxlength ='.($def[0]).";";
				}else{
					$crud.=$mt2.'$filter->'.$field->Field."->cols = 70;";
					$crud.=$mt2.'$filter->'.$field->Field."->rows = 4;";
				}
				$crud.="\n";

		}

		if ( $repo ){
			$crud.=$mt1.'$filter->salformat = new radiogroupField("Formato de salida","salformat");';
			$crud.=$mt1.'$filter->salformat->options($this->opciones);';
			$crud.=$mt1.'$filter->salformat->insertValue =\'PDF\';';
			$crud.=$mt1.'$filter->salformat->clause = "";'."\n";

			$crud.=$mt1.'$filter->buttons("search");';
			$crud.=$mt1.'$filter->build();'."\n\n";

			$crud.=$mt1.'if($this->rapyd->uri->is_set("search")){'."\n";
			$crud.=$mt3.'$mSQL=$this->rapyd->db->_compile_select();';
			$crud.=$mt3.'//echo $mSQL;'."\n";

			$crud.=$mt3.'$sobretabla="";';
			$crud.=$mt3.'//if(!empty($filter->?????->newValue))  $sobretabla.=\'??????:  \'.$filter->?????->description;';
			$crud.=$mt3.'//if(!empty($filter->?????->newValue))  $sobretabla.=\'??????:  \'.$filter->?????->description;'."\n";

			$crud.=$mt3.'$pdf = new PDFReporte($mSQL);';
			$crud.=$mt3.'$pdf->setHeadValores(\'TITULO1\');';
			$crud.=$mt3.'$pdf->setSubHeadValores(\'TITULO2\',\'TITULO3\');';
			$crud.=$mt3.'$pdf->setTitulo("Listado para la Tabla '.strtoupper($tabla).'");';
			$crud.=$mt3.'//$pdf->setSubTitulo("Desde la fecha: ".$_POST[\'fechad\']." Hasta ".$_POST[\'fechah\']);';
			$crud.=$mt3.'$pdf->setSobreTabla($sobretabla);';
			$crud.=$mt3.'$pdf->AddPage();';
			$crud.=$mt3.'$pdf->setTableTitu(11,\'Times\');'."\n";

			$c=0;
			foreach ($query->result() as $field){
				$crud.=$mt3.'$pdf->AddCol(\''.$field->Field.'\', 20,\''.ucfirst($field->Field).'\',\'L\',8);';
			}

			$crud.=$mt3.'$pdf->setTotalizar(\'vtotal\',\'contado\',\'credito\',\'anulado\');';
			$crud.=$mt3.'$pdf->Table();'."\n";
			$crud.=$mt3.'$pdf->Output();'."\n";

			$crud.=$mt1.'}else{'."\n";
			$crud.=$mt3.'$data["filtro"] = $filter->output;';
			$crud.=$mt3.'$data["titulo"] = \'&lt;h2 class="mainheader"&gtListado para la Tabla '.strtoupper($tabla).'&lt;h2&gt;\';';
			$crud.=$mt3.'$data["head"] = $this->rapyd->get_head();';
			$crud.=$mt3.'$this->load->view(\'view_freportes\', $data);';
			$crud.="\n}\n";



		} else {
			$crud.="\t\t".'$filter->buttons(\'reset\', \'search\');'."\n";
			$crud.="\t\t".'$filter->build();'."\n\n";


			$a=$b='';
			foreach($key AS $val){
				$a.='<raencode><#'.$val.'#></raencode>';
				$b.='<#'.$val.'#>';
			}
			$crud.="\t\t".'$uri = anchor($this->url.\'dataedit/show/'.$a.'\',\''.$b.'\');'."\n\n";

			$crud.="\t\t".'$grid = new DataGrid(\'\');'."\n";
			$k=implode(',',$key);
			$crud.="\t\t".'$grid->order_by(\''.$k.'\');'."\n";
			$crud.="\t\t".'$grid->per_page = 40;'."\n\n";

			$c=0;
			foreach ($query->result() as $field){
				if($field->Key=='PRI') $key[]=$field->Field;

				$crud.="\t\t".'$grid->column_orderby(\''.ucfirst($field->Field).'\',';
				if($c==0){
					$crud.='$uri';
					$c++;
					$crud.=',\''.$field->Field.'\',\'align="left"\');'."\n";
				}else{
					$crud.='\'';
					if(strrpos($field->Type,'date')!==false){
						$crud.='<dbdate_to_human><#'.$field->Field.'#></dbdate_to_human>';
						$crud.='\',\''.$field->Field.'\',\'align="center"\');'."\n";
					}elseif(strrpos($field->Type,'double')!==false || strrpos($field->Type,'int')!==false || strrpos($field->Type,'decimal')!==false){
						$crud.='<nformat><#'.$field->Field.'#></nformat>';
						$crud.='\',\''.$field->Field.'\',\'align="right"\');'."\n";
					}else{
						$crud.=$field->Field;
						$crud.='\',\''.$field->Field.'\',\'align="left"\');'."\n";
					}
				}
			}


			$crud.="\n";
			$crud.="\t\t".'$grid->add($this->url.\'dataedit/create\');'."\n";
			$crud.="\t\t".'$grid->build();'."\n";
			$crud.="\n";

			$crud.="\t\t".'$data[\'filtro\']  = $filter->output;'."\n";
			$crud.="\t\t".'$data[\'content\'] = $grid->output;'."\n";
			$crud.="\t\t".'$data[\'head\']    = $this->rapyd->get_head().script(\'jquery.js\');'."\n";
			$crud.="\t\t".'$data[\'title\']   = heading($this->titp);'."\n";
			$crud.="\t\t".'$this->load->view(\'view_ventanas\', $data);'."\n\n";
			$crud.="\t".'}'."\n";
		}
		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			//$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		} else {
			return $crud;
		}
	}

	//******************************************************************
	//
	//   Genera la seccion de funciones post del Crud
	//
	//
	//******************************************************************
	function genepost($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');

		$crud="\n";
		$crud.="\t".'function _post_insert($do){'."\n";
		$crud.="\t\t".'$primary =implode(\',\',$do->pk);'."\n";
		$crud.="\t\t".'logusu($do->table,"Creo $this->tits $primary ");'."\n";
		$crud.="\t".'}'."\n\n";
		$crud.="\t".'function _post_update($do){'."\n";
		$crud.="\t\t".'$primary =implode(\',\',$do->pk);'."\n";
		$crud.="\t\t".'logusu($do->table,"Modifico $this->tits $primary ");'."\n";
		$crud.="\t".'}'."\n\n";
		$crud.="\t".'function _post_delete($do){'."\n";
		$crud.="\t\t".'$primary =implode(\',\',$do->pk);'."\n";
		$crud.="\t\t".'logusu($do->table,"Elimino $this->tits $primary ");'."\n";
		$crud.="\t".'}'."\n";

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}

	//******************************************************************
	//
	//   Genera la seccion de funciones PRE del Crud
	//
	//
	//******************************************************************
	function genepre($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');

		$crud="\n";
		$crud.="\t".'function _pre_insert($do){'."\n";
		$crud.="\t\t".'$do->error_message_ar[\'pre_ins\']=\'\';'."\n";
		$crud.="\t\t".'return true;'."\n";
		$crud.="\t".'}'."\n\n";
		$crud.="\t".'function _pre_update($do){'."\n";
		$crud.="\t\t".'$do->error_message_ar[\'pre_upd\']=\'\';'."\n";
		$crud.="\t\t".'return true;'."\n";
		$crud.="\t".'}'."\n\n";
		$crud.="\t".'function _pre_delete($do){'."\n";
		$crud.="\t\t".'$do->error_message_ar[\'pre_del\']=\'\';'."\n";
		$crud.="\t\t".'return false;'."\n";
		$crud.="\t".'}'."\n";

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}

	function geneinstalar($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');
		$row=$this->datasis->damerow("SHOW CREATE TABLE `$tabla`;");
		//Create Table

		$crud="\n";
		$crud.="\t".'function instalar(){'."\n";
		$crud.="\t\t".'if (!$this->db->table_exists(\''.$tabla.'\')) {'."\n";
		$crud.="\t\t\t".'$mSQL="'.str_replace("\n","\n\t\t\t",$row['Create Table']).'";'."\n";
		$crud.="\t\t\t".'$this->db->simple_query($mSQL);'."\n";
		$crud.="\t\t".'}'."\n";
		$crud.="\t\t".'//$campos=$this->db->list_fields(\''.$tabla.'\');'."\n";
		$crud.="\t\t".'//if(!in_array(\'<#campo#>\',$campos)){ }'."\n";
		$crud.="\t".'}'."\n";

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}

	function genehead($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');

		$crud="\n";
		$crud.='<?php'."\n";
		$crud.="class $tabla extends Controller {"."\n";
		$crud.="\t".'var $titp=\'Titulo Principal\';'."\n";
		$crud.="\t".'var $tits=\'Sub-titulo\';'."\n";
		$crud.="\t".'var $url =\''.$tabla.'/\';'."\n\n";
		$crud.="\t"."function $tabla(){"."\n";
		$crud.="\t\t".'parent::Controller();'."\n";
		$crud.="\t\t".'$this->load->library(\'rapyd\');'."\n";
		$crud.="\t\t".'//$this->datasis->modulo_id(216,1);'."\n";
		$crud.="\t\t".'$this->instalar();'."\n";
		$crud.="\t".'}'."\n\n";
		$crud.="\t".'function index(){'."\n";
		$crud.="\t\t".'redirect($this->url.\'filteredgrid\');'."\n";
		$crud.="\t".'}'."\n\n";

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}

	function genefoot($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla))) show_error('Tabla no existe o faltan parametros');

		$crud="\n";
		$crud.='}'."\n";
		$crud.='?>';

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}

	function genetodo($tabla=null,$s=true){
		$crud='';
		$crud.=$this->genehead($tabla    ,false);
		$crud.=$this->genefilter($tabla  ,false);
		$crud.=$this->genecrud($tabla    ,false);
		$crud.=$this->genepre($tabla     ,false);
		$crud.=$this->genepost($tabla    ,false);
		$crud.=$this->geneinstalar($tabla,false);
		$crud.=$this->genefoot($tabla    ,false);

		$crud=htmlentities($crud);

		if($s){
			$data['content'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('view_ventanas_sola', $data);
		}else{
			return $crud;
		}
	}


	// Genera las columnas para Extjs
	function extjs(){
		$db =$this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla');
		}
		$query = $this->db->query("DESCRIBE $db");
		$i = 0;
		if ($query->num_rows() > 0){
			$fields  = '';
			$columna = '';
			$campos  = '';
			foreach ($query->result() as $row){
				if ($i == 0 ){
					$str="'".$row->Field."'";
					$i = 1;
				} else {
					$str=",'".$row->Field."'";
				}
				$fields .= $str;

				$str = "{ header: ".str_pad("'".$row->Field."'",20).",  width: 60, sortable: true,  dataIndex: ".str_pad("'".$row->Field."'",20).", field: ";

				if ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
					$str .= "{ type: 'date'       }, filter: { type: 'date'    }";
				} elseif ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
					$str = "{ type: 'date'       }, filter: { type: 'date'    }";
				} elseif ( substr($row->Type,0,7) == 'decimal' or substr($row->Type,0,3) == 'int'  ) {
					$str .= "{ type: 'numberfield'}, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')";
				} else {
					$str .= "{ type: 'textfield'  }, filter: { type: 'string'  }";
				}
				$columna .= $str."},<br>";


				$str = "{ fieldLabel: ".$row->Field.",  name: ".$row->Field.", width:100, labelWidth:60, ";

				if ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
					$str .= "{ type: 'date'       }, filter: { type: 'date'    }";
				} elseif ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
					$str = "xtype: 'datefield', format: 'd/m/Y', submitFormat: 'Y-m-d' ";
				} elseif ( substr($row->Type,0,7) == 'decimal' or substr($row->Type,0,3) == 'int'  ) {
					$str .= "xtype: 'numberfield', , hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00')";
				} else {
					$str .= "xtype: 'textfield' ";
				}
				$campos .= $str."},<br>";

			}
			echo "$fields<br>";
			echo "<br>$columna";
			echo "<br>$campos";
		}
	}

	//******************************************************************
	//
	//  Genera Crud para jqGrid
	//
	//******************************************************************
	function jqgrid(){
		$db = $this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/directorio"');
		}
		$contro =$this->uri->segment(4);
		if($contro===false){
			$contro = 'CONTROLADOR';
		}

		$query = $this->db->query("DESCRIBE $db");
		$i = 0;
		if ($query->num_rows() > 0){
			$fields  = '';
			$columna = '<?php'."\n";
			$param   = '';
			$campos  = '';
			$str = '';
			$tab1 = $this->mtab(1);
			$tab2 = $this->mtab(2);
			$tab3 = $this->mtab(3);
			$tab4 = $this->mtab(4);
			$tab5 = $this->mtab(5);
			$tab6 = $this->mtab(6);
			$tab7 = $this->mtab(7);
			$tab8 = $this->mtab(8);

			$str .= $this->jqgridclase($db, $contro);

			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Layout en la Ventana'."\n";
			$str .= $tab1.'//'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function jqdatag(){'."\n\n";
			$str .= $tab2.'$grid = $this->defgrid();'."\n";
			$str .= $tab2.'$param[\'grids\'][] = $grid->deploy();'."\n\n";

			$str .= $tab2."//Funciones que ejecutan los botones\n";
			$str .= $tab2.'$bodyscript = $this->bodyscript( $param[\'grids\'][0][\'gridname\']);'."\n\n";

			$str .= $tab2.'//Botones Panel Izq'."\n";
			$str .= $tab2.'//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));'."\n";
			$str .= $tab2.'$WestPanel = $grid->deploywestp();'."\n\n";

			$str .= $tab2.'$adic = array('."\n";
			$str .= $tab3.'array(\'id\'=>\'fedita\',  \'title\'=>\'Agregar/Editar Registro\'),'."\n";
			$str .= $tab3.'array(\'id\'=>\'fshow\' ,  \'title\'=>\'Mostrar Registro\'),'."\n";
			$str .= $tab3.'array(\'id\'=>\'fborra\',  \'title\'=>\'Eliminar Registro\')'."\n";
			$str .= $tab2.');'."\n";
			$str .= $tab2.'$SouthPanel = $grid->SouthPanel($this->datasis->traevalor(\'TITULO1\'), $adic);'."\n\n";

			//$str .= $tab2.'$SouthPanel = $grid->SouthPanel($this->datasis->traevalor("TITULO1"));'."\n\n";

			$str .= $tab2.'$param[\'WestPanel\']   = $WestPanel;'."\n";
			$str .= $tab2.'//$param[\'EastPanel\'] = $EastPanel;'."\n";
			$str .= $tab2.'$param[\'SouthPanel\']  = $SouthPanel;'."\n";
			$str .= $tab2.'$param[\'listados\']    = $this->datasis->listados(\''.strtoupper($db).'\', \'JQ\');'."\n";
			$str .= $tab2.'$param[\'otros\']       = $this->datasis->otros(\''.strtoupper($db).'\', \'JQ\');'."\n";
			$str .= $tab2.'$param[\'temas\']       = array(\'proteo\',\'darkness\',\'anexos1\');'."\n";
			//$str .= $tab2.'$param[\'anexos\']    = \'anexos1\';'."\n";
			$str .= $tab2.'$param[\'bodyscript\']  = $bodyscript;'."\n";
			$str .= $tab2.'$param[\'tabs\']        = false;'."\n";
			$str .= $tab2.'$param[\'encabeza\']    = $this->titp;'."\n";
			$str .= $tab2.'$param[\'tamano\']      = $this->datasis->getintramenu( substr($this->url,0,-1) );'."\n";

			$str .= $tab2.'$this->load->view(\'jqgrid/crud2\',$param);'."\n";
			$str .= $tab1.'}'."\n\n";

			//**************************************
			//  Funcion de Java del Body
			//
			//
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Funciones de los Botones'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function bodyscript( $grid0 ){'."\n";
			$str .= $tab2.'$bodyscript = \'';
			$str .= $tab2.'&lt;script type="text/javascript"&gt;\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'add(){'."\n";
			$str .= $tab3.'$.post("\'.site_url($this->url'.'.\'dataedit/create\').\'",'."\n";
			$str .= $tab3.'function(data){'."\n";
			$str .= $tab4.'$("#fedita").html(data);'."\n";
			$str .= $tab4.'$("#fedita").dialog( "open" );'."\n";
			$str .= $tab3.'})'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'edit(){'."\n";
			$str .= $tab3.'var id     = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab4.'var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab4.'mId = id;'."\n";
			$str .= $tab4.'$.post("\'.site_url($this->url'.'.\'dataedit/modify\').\'/"+id, function(data){'."\n";
			$str .= $tab5.'$("#fedita").html(data);'."\n";
			$str .= $tab5.'$("#fedita").dialog( "open" );'."\n";
			$str .= $tab4.'});'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$.prompt("&lt;h1&gt;Por favor Seleccione un Registro&lt;/h1&gt;");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'show(){'."\n";
			$str .= $tab3.'var id     = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab4.'var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab4.'mId = id;'."\n";
			$str .= $tab4.'$.post("\'.site_url($this->url'.'.\'dataedit/show\').\'/"+id, function(data){'."\n";
			$str .= $tab5.'$("#fshow").html(data);'."\n";
			$str .= $tab5.'$("#fshow").dialog( "open" );'."\n";
			$str .= $tab4.'});'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$.prompt("&lt;h1&gt;Por favor Seleccione un Registro&lt;/h1&gt;");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'del() {'."\n";
			$str .= $tab3.'var id = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab3.'	if(confirm(" Seguro desea eliminar el registro?")){'."\n";
			$str .= $tab3.'		var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab3.'		mId = id;'."\n";
			$str .= $tab3.'		$.post("\'.site_url($this->url.\'dataedit/do_delete\').\'/"+id, function(data){'."\n";
			$str .= $tab3.'			try{'."\n";
			$str .= $tab3.'				var json = JSON.parse(data);'."\n";
			$str .= $tab3.'				if (json.status == "A"){'."\n";
			$str .= $tab3.'					apprise("Registro eliminado");'."\n";
			$str .= $tab3.'					jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab3.'				}else{'."\n";
			$str .= $tab3.'					apprise("Registro no se puede eliminado");'."\n";
			$str .= $tab3.'				}'."\n";
			$str .= $tab3.'			}catch(e){'."\n";
			$str .= $tab3.'				$("#fborra").html(data);'."\n";
			$str .= $tab3.'				$("#fborra").dialog( "open" );'."\n";
			$str .= $tab3.'			}'."\n";
			$str .= $tab3.'		});'."\n";
			$str .= $tab3.'	}'."\n";
			$str .= $tab3.'}else{'."\n";
			$str .= $tab3.'	$.prompt("<h1>Por favor Seleccione un Registro</h1>");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n";


			$str .= $tab2.'//Wraper de javascript'."\n";
			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$(function(){'."\n";
			$str .= $tab3.'$("#dialog:ui-dialog").dialog( "destroy" );'."\n";
			$str .= $tab3.'var mId = 0;'."\n";
			$str .= $tab3.'var montotal = 0;'."\n";
			$str .= $tab3.'var ffecha = $("#ffecha");'."\n";
			$str .= $tab3.'var grid = jQuery("#newapi\'.$grid0.\'");'."\n";
			$str .= $tab3.'var s;'."\n";
			$str .= $tab3.'var allFields = $( [] ).add( ffecha );'."\n";
			$str .= $tab3.'var tips = $( ".validateTips" );'."\n";
			$str .= $tab3.'s = grid.getGridParam(\\\'selarrrow\\\');'."\n";
			$str .= $tab3.'\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fedita").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 500, width: 700, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Guardar": function() {'."\n";
			$str .= $tab5.'var bValid = true;'."\n";
			$str .= $tab5.'var murl = $("#df1").attr("action");'."\n";
			$str .= $tab5.'allFields.removeClass( "ui-state-error" );'."\n";
			$str .= $tab5.'$.ajax({'."\n";
			$str .= $tab6.'type: "POST", dataType: "html", async: false,'."\n";
			$str .= $tab6.'url: murl,'."\n";
			$str .= $tab6.'data: $("#df1").serialize(),'."\n";
			$str .= $tab6.'success: function(r,s,x){'."\n";

			$str .= $tab7.'try{'."\n";
			$str .= $tab8.'var json = JSON.parse(r);'."\n";
			$str .= $tab8.'if (json.status == "A"){'."\n";
			$str .= $tab8.'	apprise("Registro Guardado");'."\n";
			$str .= $tab8.'	$( "#fedita" ).dialog( "close" );'."\n";
			$str .= $tab8.'	grid.trigger("reloadGrid");'."\n";
			$str .= $tab8.'	\'.$this->datasis->jwinopen(site_url(\'formatos/ver/'.strtoupper($db).'\').\'/\\\'+res.id+\\\'/id\\\'\').\';'."\n";
			$str .= $tab8.'	return true;'."\n";
			$str .= $tab8.'} else {'."\n";
			$str .= $tab8.'	apprise(json.mensaje);'."\n";
			$str .= $tab8.'}'."\n";
			$str .= $tab7.'}catch(e){'."\n";
			$str .= $tab7.'	$("#fedita").html(r);'."\n";
			$str .= $tab7.'}'."\n";

			//$str .= $tab6.'if ( r.length == 0 ) {'."\n";
			//$str .= $tab7.'apprise("Registro Guardado");'."\n";
			//$str .= $tab7.'$( "#fedita" ).dialog( "close" );'."\n";
			//$str .= $tab7.'grid.trigger("reloadGrid");'."\n";
			//$str .= $tab7.'\'.$this->datasis->jwinopen(site_url(\'formatos/ver/'.strtoupper($db).'\').\'/\\\'+res.id+\\\'/id\\\'\').\';'."\n";
			//$str .= $tab7.'return true;'."\n";
			//$str .= $tab6.'} else { '."\n";
			//$str .= $tab7.'$("#fedita").html(r);'."\n";
			//$str .= $tab6.'}'."\n";

			$str .= $tab6.'}'."\n";
			//$str .= $tab4.'}'."\n";
			$str .= $tab5.'})'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab4.'"Cancelar": function() {'."\n";
			$str .= $tab5.'$("#fedita").html("");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'}'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'$("#fedita").html("");'."\n";
			$str .= $tab4.'allFields.val( "" ).removeClass( "ui-state-error" );'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\';'."\n\n";
			//$str .= $tab2.'});'."\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fshow").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 500, width: 700, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Aceptar": function() {'."\n";
			$str .= $tab5.'$("#fshow").html("");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'$("#fshow").html("");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\''.";\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fborra").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 300, width: 400, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Aceptar": function() {'."\n";
			$str .= $tab5.'$("#fborra").html("");'."\n";
			$str .= $tab5.'jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab4.'$("#fborra").html("");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\''.";\n\n";

			$str .= $tab2.'$bodyscript .= \'});\'."\n";'."\n\n";

			$str .= $tab2.'$bodyscript .= "\n&lt;/script&gt;\n";'."\n";
			$str .= $tab2.'$bodyscript .= "";'."\n";
			$str .= $tab2.'return $bodyscript;'."\n";
			$str .= $tab1."}\n\n";

			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Definicion del Grid y la Forma'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function defgrid( $deployed = false ){'."\n";
			$str .= $tab2.'$i      = 1;'."\n";
			$str .= $tab2.'$editar = "false";'."\n\n";
			$str .= $tab2.'$grid  = new $this->jqdatagrid;'."\n\n";
			$columna .= $str;
			$str = '';

			foreach ($query->result() as $row){
				if ( $row->Field == 'id') {
					$str   = $tab2.'$grid->addField(\'id\');'."\n";
					$str  .= $tab2.'$grid->label(\'Id\');'."\n";
					$str  .= $tab2.'$grid->params(array('."\n";
					$str  .= $tab3.'\'align\'         => "\'center\'",'."\n";
					$str  .= $tab3.'\'frozen\'        => \'true\','."\n";
					$str  .= $tab3.'\'width\'         => 40,'."\n";
					$str  .= $tab3.'\'editable\'      => \'false\','."\n";
					$str  .= $tab3.'\'search\'        => \'false\''."\n";
				} else {
					$str  = $tab2.'$grid->addField(\''.$row->Field.'\');'."\n";
					$str .= $tab2.'$grid->label(\''.ucfirst($row->Field).'\');'."\n";

					$str .= $tab2.'$grid->params(array('."\n";
					$str .= $tab3.'\'search\'        => \'true\','."\n";
					$str .= $tab3.'\'editable\'      => $editar,'."\n";

					if ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
						$str .= $tab3.'\'width\'         => 80,'."\n";
						$str .= $tab3.'\'align\'         => "\'center\'",'."\n";
						$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
						$str .= $tab3.'\'editrules\'     => \'{ required:true,date:true}\','."\n";
						$str .= $tab3.'\'formoptions\'   => \'{ label:"Fecha" }\''."\n";

					} elseif ( substr($row->Type,0,7) == 'decimal' or substr($row->Type,0,3) == 'int'  ) {
						$str .= $tab3.'\'align\'         => "\'right\'",'."\n";
						$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
						$str .= $tab3.'\'width\'         => 100,'."\n";
						$str .= $tab3.'\'editrules\'     => \'{ required:true }\','."\n";
						$str .= $tab3.'\'editoptions\'   => \'{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }\','."\n";
						$str .= $tab3.'\'formatter\'     => "\'number\'",'."\n";
						if (substr($row->Type,0,3) == 'int'){
							$str .= $tab3.'\'formatoptions\' => \'{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }\''."\n";
						} else {
							$str .= $tab3.'\'formatoptions\' => \'{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }\''."\n";
						}

					} elseif ( substr($row->Type,0,7) == 'varchar' or substr($row->Type,0,4) == 'char'  ) {
						$long = str_replace(array('varchar(','char(',')'),"", $row->Type)*10;
						$maxlong = $long/10;
						if ( $long > 200 ) $long = 200;
						if ( $long < 40 ) $long = 40;

						$str .= $tab3.'\'width\'         => '.$long.','."\n";
						$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
						$str .= $tab3.'\'editrules\'     => \'{ required:true}\','."\n";
						$str .= $tab3.'\'editoptions\'   => \'{ size:'.$maxlong.', maxlength: '.$maxlong.' }\','."\n";

					} elseif ( $row->Type == 'text' ) {
						$long = 250;
						$str .= $tab3.'\'width\'         => '.$long.','."\n";
						$str .= $tab3.'\'edittype\'      => "\'textarea\'",'."\n";
						$str .= $tab3.'\'editoptions\'   => "\'{rows:2, cols:60}\'",'."\n";

						//$str .= $tab3.'\'formoptions\'   => "\'{rows:"2", cols:"60"}\'",'."\n";


					} else {
						$str .= $tab3.'\'width\'         => 140,'."\n";
						$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
					}
				}
				$str .= $tab2.'));'."\n\n";
				$columna .= $str."\n";
			}

			$str  = $tab2.'$grid->showpager(true);'."\n";
			$str .= $tab2.'$grid->setWidth(\'\');'."\n";
			$str .= $tab2.'$grid->setHeight(\'290\');'."\n";
			$str .= $tab2.'$grid->setTitle($this->titp);'."\n";
			$str .= $tab2.'$grid->setfilterToolbar(true);'."\n";
			$str .= $tab2.'$grid->setToolbar(\'false\', \'"top"\');'."\n\n";

			$str .= $tab2.'$grid->setFormOptionsE(\'closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} \');'."\n";
			$str .= $tab2.'$grid->setFormOptionsA(\'closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} \');'."\n";

			//$str .= $tab2.'$grid->setAfterSubmit("$.prompt(\'Respuesta:\'+a.responseText); return [true, a ];");'."\n\n";

			$str .= $tab2.'$grid->setAfterSubmit("$(\'#respuesta\').html(\'&lt;span style='."\\'font-weight:bold; color:red;\\'&gt;'+a.responseText+'&lt;/span&gt;'); return [true, a ];".'");'."\n\n";
			$str .= $tab2.'$grid->setOndblClickRow(\'\');';

			$str .= $tab2.'#show/hide navigations buttons'."\n";
			$str .= $tab2.'$grid->setAdd(    $this->datasis->sidapuede(\''.strtoupper($db).'\',\'INCLUIR%\' ));'."\n";
			$str .= $tab2.'$grid->setEdit(   $this->datasis->sidapuede(\''.strtoupper($db).'\',\'MODIFICA%\'));'."\n";
			$str .= $tab2.'$grid->setDelete( $this->datasis->sidapuede(\''.strtoupper($db).'\',\'BORR_REG%\'));'."\n";
			$str .= $tab2.'$grid->setSearch( $this->datasis->sidapuede(\''.strtoupper($db).'\',\'BUSQUEDA%\'));'."\n";
			$str .= $tab2.'$grid->setRowNum(30);'."\n";

			$str .= $tab2.'$grid->setShrinkToFit(\'false\');'."\n\n";

			$str .= $tab2.'$grid->setBarOptions("addfunc: '.strtolower($db).'add, editfunc: '.strtolower($db).'edit, delfunc: '.strtolower($db).'del, viewfunc: '.strtolower($db).'show");'."\n\n";

			$str .= $tab2.'#Set url'."\n";
			$str .= $tab2.'$grid->setUrlput(site_url($this->url.\'setdata/\'));'."\n\n";

			$str .= $tab2.'#GET url'."\n";
			$str .= $tab2.'$grid->setUrlget(site_url($this->url.\'getdata/\'));'."\n\n";

			$str .= $tab2.'if ($deployed) {'."\n";
			$str .= $tab2.'	return $grid->deploy();'."\n";
			$str .= $tab2.'} else {'."\n";
			$str .= $tab2.'	return $grid;'."\n";
			$str .= $tab2.'}'."\n";
			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Busca la data en el Servidor por json'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function getdata(){'."\n";

			//$str .= $tab2.'$filters = $this->input->get_post(\'filters\');'."\n";

			$str .= $tab2.'$grid       = $this->jqdatagrid;'."\n\n";

			$str .= $tab2.'// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO'."\n";
			$str .= $tab2.'$mWHERE = $grid->geneTopWhere(\''.$db.'\');'."\n\n";

			$str .= $tab2.'$response   = $grid->getData(\''.$db.'\', array(array()), array(), false, $mWHERE );'."\n";
			$str .= $tab2.'$rs = $grid->jsonresult( $response);'."\n";
			$str .= $tab2.'echo $rs;'."\n";

			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Guarda la Informacion'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function setData(){'."\n";
			$str .= $tab2.'$this->load->library(\'jqdatagrid\');'."\n";
			$str .= $tab2.'$oper   = $this->input->post(\'oper\');'."\n";
			$str .= $tab2.'$id     = $this->input->post(\'id\');'."\n";
			$str .= $tab2.'$data   = $_POST;'."\n";
			$str .= $tab2.'$mcodp  = "??????";'."\n";
			$str .= $tab2.'$check  = 0;'."\n\n";

			$str .= $tab2.'unset($data[\'oper\']);'."\n";
			$str .= $tab2.'unset($data[\'id\']);'."\n";

			$str .= $tab2.'if($oper == \'add\'){'."\n";
			$str .= $tab3.'if(false == empty($data)){'."\n";
			$str .= $tab4.'$check = $this->datasis->dameval("SELECT count(*) FROM '.$db.' WHERE $mcodp=".$this->db->escape($data[$mcodp]));'."\n";
			$str .= $tab4.'if ( $check == 0 ){'."\n";
			$str .= $tab5.'$this->db->insert(\''.$db.'\', $data);'."\n";
			$str .= $tab5.'echo "Registro Agregado";'."\n\n";
			$str .= $tab5.'logusu(\''.strtoupper($db).'\',"Registro ????? INCLUIDO");'."\n";
			$str .= $tab4.'} else'."\n";
			$str .= $tab5.'echo "Ya existe un registro con ese $mcodp";'."\n";

			$str .= $tab3.'} else'."\n";
			//$str .= $tab2.'echo \'\';'."\n";
			$str .= $tab4.'echo "Fallo Agregado!!!";'."\n\n";

			$str .= $tab2.'} elseif($oper == \'edit\') {'."\n";
			$str .= $tab3.'$nuevo  = $data[$mcodp];'."\n";
			$str .= $tab3.'$anterior = $this->datasis->dameval("SELECT $mcodp FROM '.$db.' WHERE id=$id");'."\n";
			$str .= $tab3.'if ( $nuevo <> $anterior ){'."\n";
			$str .= $tab4.'//si no son iguales borra el que existe y cambia'."\n";
			$str .= $tab4.'$this->db->query("DELETE FROM '.$db.' WHERE $mcodp=?", array($mcodp));'."\n";
			$str .= $tab4.'$this->db->query("UPDATE '.$db.' SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));'."\n";
			$str .= $tab4.'$this->db->where("id", $id);'."\n";
			$str .= $tab4.'$this->db->update("'.$db.'", $data);'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");'."\n";
			$str .= $tab4.'echo "Grupo Cambiado/Fusionado en clientes";'."\n";

			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'unset($data[$mcodp]);'."\n";
			$str .= $tab4.'$this->db->where("id", $id);'."\n";
			$str .= $tab4.'$this->db->update(\''.$db.'\', $data);'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"Grupo de Cliente  ".$nuevo." MODIFICADO");'."\n";
			$str .= $tab4.'echo "$mcodp Modificado";'."\n";
			$str .= $tab3.'}'."\n\n";

			$str .= $tab2.'} elseif($oper == \'del\') {'."\n";
			$str .= $tab3.'$meco = $this->datasis->dameval("SELECT $mcodp FROM '.$db.' WHERE id=$id");'."\n";

			$str .= $tab3.'//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM '.$db.' WHERE id=\'$id\' ");'."\n";
			$str .= $tab3.'if ($check > 0){'."\n";
			$str .= $tab4.'echo " El registro no puede ser eliminado; tiene movimiento ";'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$this->db->simple_query("DELETE FROM '.$db.' WHERE id=$id ");'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"Registro ????? ELIMINADO");'."\n";
			$str .= $tab4.'echo "Registro Eliminado";'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};'."\n";
			$str .= $tab1.'}'."\n";

			$str .= $this->genecrudjq($db, false);

			$str  .= $this->genepre($db,  false);
			$str  .= $this->genepost($db, false);
			$str  .= $this->geneinstalar($db, false);

			$str .= '}'."\n";

			$columna .= $str."\n";

			$data['programa']    = $columna.'?>';
			$data['bd']          = $db;
			$data['controlador'] = $contro;
			$this->load->view('editorcm', $data);

		}
	}


	//******************************************************************
	//
	//  Genera Crud Maestro Detalle para jqGrid
	//
	//******************************************************************
	function jqgridmd(){
		$db = $this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla Maestro "/maestro/detalle/directorio"');
		}

		$dbit = $this->uri->segment(4);
		if($db===false){
			exit('Debe especificar en la uri la tabla Detalle "/maestro/detalle/directorio"');
		}

		$contro =$this->uri->segment(5);
		if($contro===false){
			$contro = 'CONTROLADOR';
		}

		$query = $this->db->query("DESCRIBE $db");
		$i = 0;
		if ($query->num_rows() > 0){
			$fields  = '';
			$columna = '<pre>';
			$param   = '';
			$campos  = '';
			$str = '';
			$tab1 = $this->mtab(1);
			$tab2 = $this->mtab(2);
			$tab3 = $this->mtab(3);
			$tab4 = $this->mtab(4);
			$tab5 = $this->mtab(5);
			$tab6 = $this->mtab(6);
			$tab7 = $this->mtab(7);
			$tab8 = $this->mtab(8);

			$str .= $this->jqgridclase($db, $contro);


			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Layout en la Ventana'."\n";
			$str .= $tab1.'//'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function jqdatag(){'."\n\n";

			$str .= $tab2.'$grid = $this->defgrid();'."\n";
			$str .= $tab2.'$grid->setHeight(\'185\');'."\n";
			$str .= $tab2.'$param[\'grids\'][] = $grid->deploy();'."\n\n";

			$str .= $tab2.'$grid1   = $this->defgridit();'."\n";
			$str .= $tab2.'$grid1->setHeight(\'190\');'."\n";
			$str .= $tab2.'$param[\'grids\'][] = $grid1->deploy();'."\n\n";

			$str .= $tab2.'// Configura los Paneles'."\n";
			$str .= $tab2.'$readyLayout = $grid->readyLayout2( 212, 220, $param[\'grids\'][0][\'gridname\'],$param[\'grids\'][1][\'gridname\']);'."\n\n";

			$str .= $tab2.'//Funciones que ejecutan los botones'."\n";
			$str .= $tab2.'$bodyscript = $this->bodyscript( $param[\'grids\'][0][\'gridname\'], $param[\'grids\'][1][\'gridname\'] );'."\n\n";

			$str .= $tab2.'//Botones Panel Izq'."\n";
			$str .= $tab2.'$grid->wbotonadd(array("id"=>"imprime",  "img"=>"assets/default/images/print.png","alt" => \'Reimprimir\', "label"=>"Reimprimir Documento"));'."\n";
			$str .= $tab2.'$WestPanel = $grid->deploywestp();'."\n\n";

			$str .= $tab2.'//Panel Central'."\n";
			$str .= $tab2.'$centerpanel = $grid->centerpanel( $id = "radicional", $param[\'grids\'][0][\'gridname\'], $param[\'grids\'][1][\'gridname\'] );'."\n\n";

			$str .= $tab2.'$adic = array('."\n";
			$str .= $tab3.'array(\'id\'=>\'fedita\',  \'title\'=>\'Agregar/Editar Registro\'),'."\n";
			$str .= $tab3.'array(\'id\'=>\'fshow\' ,  \'title\'=>\'Mostrar Registro\'),'."\n";
			$str .= $tab3.'array(\'id\'=>\'fborra\',  \'title\'=>\'Eliminar Registro\')'."\n";
			$str .= $tab2.');'."\n";

			$str .= $tab2.'$SouthPanel = $grid->SouthPanel($this->datasis->traevalor(\'TITULO1\'), $adic);'."\n\n";

			$str .= $tab2.'$param[\'WestPanel\']    = $WestPanel;'."\n";
			$str .= $tab2.'$param[\'script\']       = script(\'plugins/jquery.ui.autocomplete.autoSelectOne.js\');'."\n";
			$str .= $tab2.'$param[\'readyLayout\']  = $readyLayout;'."\n";
			$str .= $tab2.'$param[\'SouthPanel\']   = $SouthPanel;'."\n";
			$str .= $tab2.'$param[\'listados\']     = $this->datasis->listados(\''.strtoupper($db).'\', \'JQ\');'."\n";
			$str .= $tab2.'$param[\'otros\']        = $this->datasis->otros(\''.strtoupper($db).'\', \'JQ\');'."\n";
			$str .= $tab2.'$param[\'centerpanel\']  = $centerpanel;'."\n";
			$str .= $tab2.'$param[\'temas\']        = array(\'proteo\',\'darkness\',\'anexos1\');'."\n";
			$str .= $tab2.'$param[\'bodyscript\']   = $bodyscript;'."\n";
			$str .= $tab2.'$param[\'tabs\']         = false;'."\n";
			$str .= $tab2.'$param[\'encabeza\']     = $this->titp;'."\n";
			$str .= $tab2.'$param[\'tamano\']       = $this->datasis->getintramenu( substr($this->url,0,-1) );'."\n";

			$str .= $tab2.'$this->load->view(\'jqgrid/crud2\',$param);'."\n\n";
			$str .= $tab1.'}'."\n\n";



			//**************************************
			//  Funcion de Java del Body
			//
			//
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Funciones de los Botones'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function bodyscript( $grid0, $grid1 ){'."\n";
			$str .= $tab2.'$bodyscript = \'';
			$str .= $tab2.'&lt;script type="text/javascript"&gt;\';'."\n\n";


			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'add(){'."\n";
			$str .= $tab3.'$.post("\'.site_url($this->url'.'.\'dataedit/create\').\'",'."\n";
			$str .= $tab3.'function(data){'."\n";
			$str .= $tab4.'$("#fedita").html(data);'."\n";
			$str .= $tab4.'$("#fedita").dialog( "open" );'."\n";
			$str .= $tab3.'})'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'edit(){'."\n";
			$str .= $tab3.'var id     = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab4.'var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab4.'mId = id;'."\n";
			$str .= $tab4.'$.post("\'.site_url($this->url'.'.\'dataedit/modify\').\'/"+id, function(data){'."\n";
			$str .= $tab5.'$("#fedita").html(data);'."\n";
			$str .= $tab5.'$("#fedita").dialog( "open" );'."\n";
			$str .= $tab4.'});'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$.prompt("&lt;h1&gt;Por favor Seleccione un Registro&lt;/h1&gt;");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'show(){'."\n";
			$str .= $tab3.'var id     = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab4.'var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab4.'mId = id;'."\n";
			$str .= $tab4.'$.post("\'.site_url($this->url'.'.\'dataedit/show\').\'/"+id, function(data){'."\n";
			$str .= $tab5.'$("#fshow").html(data);'."\n";
			$str .= $tab5.'$("#fshow").dialog( "open" );'."\n";
			$str .= $tab4.'});'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$.prompt("&lt;h1&gt;Por favor Seleccione un Registro&lt;/h1&gt;");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'function '.strtolower($db).'del() {'."\n";
			$str .= $tab3.'var id = jQuery("#newapi\'.$grid0.\'").jqGrid(\\\'getGridParam\\\',\\\'selrow\\\');'."\n";
			$str .= $tab3.'if(id){'."\n";
			$str .= $tab3.'	if(confirm(" Seguro desea eliminar el registro?")){'."\n";
			$str .= $tab3.'		var ret    = $("#newapi\'.$grid0.\'").getRowData(id);'."\n";
			$str .= $tab3.'		mId = id;'."\n";
			$str .= $tab3.'		$.post("\'.site_url($this->url.\'dataedit/do_delete\').\'/"+id, function(data){'."\n";
			$str .= $tab3.'			try{'."\n";
			$str .= $tab3.'				var json = JSON.parse(data);'."\n";
			$str .= $tab3.'				if (json.status == "A"){'."\n";
			$str .= $tab3.'					apprise("Registro eliminado");'."\n";
			$str .= $tab3.'					jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab3.'				}else{'."\n";
			$str .= $tab3.'					apprise("Registro no se puede eliminado");'."\n";
			$str .= $tab3.'				}'."\n";
			$str .= $tab3.'			}catch(e){'."\n";
			$str .= $tab3.'				$("#fborra").html(data);'."\n";
			$str .= $tab3.'				$("#fborra").dialog( "open" );'."\n";
			$str .= $tab3.'			}'."\n";
			$str .= $tab3.'		});'."\n";
			$str .= $tab3.'	}'."\n";
			$str .= $tab3.'}else{'."\n";
			$str .= $tab3.'	$.prompt("&lt;h1&gt;Por favor Seleccione un Registro&lt;/h1&gt;");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};\';'."\n";


			$str .= $tab2.'//Wraper de javascript'."\n";
			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$(function() {'."\n";
			$str .= $tab3.'$("#dialog:ui-dialog").dialog( "destroy" );'."\n";
			$str .= $tab3.'var mId = 0;'."\n";
			$str .= $tab3.'var montotal = 0;'."\n";
			$str .= $tab3.'var ffecha = $("#ffecha");'."\n";
			$str .= $tab3.'var grid = jQuery("#newapi\'.$grid0.\'");'."\n";
			$str .= $tab3.'var s;'."\n";
			$str .= $tab3.'var allFields = $( [] ).add( ffecha );'."\n";
			$str .= $tab3.'var tips = $( ".validateTips" );'."\n";
			$str .= $tab3.'s = grid.getGridParam(\\\'selarrrow\\\');'."\n";
			$str .= $tab3.'\';'."\n\n";
			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fedita").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 500, width: 700, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Guardar": function() {'."\n";
			$str .= $tab5.'var bValid = true;'."\n";
			$str .= $tab5.'var murl = $("#df1").attr("action");'."\n";
			$str .= $tab5.'allFields.removeClass( "ui-state-error" );'."\n";
			$str .= $tab5.'$.ajax({'."\n";
			$str .= $tab6.'type: "POST", dataType: "html", async: false,'."\n";
			$str .= $tab6.'url: murl,'."\n";
			$str .= $tab6.'data: $("#df1").serialize(),'."\n";
			$str .= $tab6.'success: function(r,s,x){'."\n";

			$str .= $tab7.'try{'."\n";
			$str .= $tab8.'var json = JSON.parse(r);'."\n";
			$str .= $tab8.'if (json.status == "A"){'."\n";
			$str .= $tab8.'	apprise("Registro Guardado");'."\n";
			$str .= $tab8.'	$( "#fedita" ).dialog( "close" );'."\n";
			$str .= $tab8.'	grid.trigger("reloadGrid");'."\n";
			$str .= $tab8.'	\'.$this->datasis->jwinopen(site_url(\'formatos/ver/'.strtoupper($db).'\').\'/\\\'+json.pk.id+\\\'/id\\\'\').\';'."\n";
			$str .= $tab8.'	return true;'."\n";
			$str .= $tab8.'} else {'."\n";
			$str .= $tab8.'	apprise(json.mensaje);'."\n";
			$str .= $tab8.'}'."\n";
			$str .= $tab7.'}catch(e){'."\n";
			$str .= $tab7.'	$("#fedita").html(r);'."\n";
			$str .= $tab7.'}'."\n";

			//$str .= $tab6.'if ( r.length == 0 ) {'."\n";
			//$str .= $tab7.'apprise("Registro Guardado");'."\n";
			//$str .= $tab7.'$( "#fedita" ).dialog( "close" );'."\n";
			//$str .= $tab7.'grid.trigger("reloadGrid");'."\n";
			//$str .= $tab7.'\'.$this->datasis->jwinopen(site_url(\'formatos/ver/'.strtoupper($db).'\').\'/\\\'+res.id+\\\'/id\\\'\').\';'."\n";
			//$str .= $tab7.'return true;'."\n";
			//$str .= $tab6.'} else { '."\n";
			//$str .= $tab7.'$("#fedita").html(r);'."\n";
			//$str .= $tab6.'}'."\n";

			$str .= $tab6.'}'."\n";
			//$str .= $tab4.'}'."\n";
			$str .= $tab5.'})'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab4.'"Cancelar": function() {'."\n";
			$str .= $tab5.'$("#fedita").html("");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'}'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'$("#fedita").html("");'."\n";
			$str .= $tab4.'allFields.val( "" ).removeClass( "ui-state-error" );'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\';'."\n\n";
			//$str .= $tab2.'});'."\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fshow").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 500, width: 700, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Aceptar": function() {'."\n";
			$str .= $tab5.'$("#fshow").html("");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'$("#fshow").html("");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\''.";\n\n";

			$str .= $tab2.'$bodyscript .= \''."\n";
			$str .= $tab2.'$("#fborra").dialog({'."\n";
			$str .= $tab3.'autoOpen: false, height: 300, width: 400, modal: true,'."\n";
			$str .= $tab3.'buttons: {'."\n";
			$str .= $tab4.'"Aceptar": function() {'."\n";
			$str .= $tab5.'$("#fborra").html("");'."\n";
			$str .= $tab5.'jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab5.'$( this ).dialog( "close" );'."\n";
			$str .= $tab4.'},'."\n";
			$str .= $tab3.'},'."\n";
			$str .= $tab3.'close: function() {'."\n";
			$str .= $tab4.'jQuery("#newapi\'.$grid0.\'").trigger("reloadGrid");'."\n";
			$str .= $tab4.'$("#fborra").html("");'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'});\''.";\n\n";
			$str .= $tab2.'$bodyscript .= \'});\'."\n";'."\n\n";

			$str .= $tab2.'$bodyscript .= "\n&lt;/script&gt;\n";'."\n";
			$str .= $tab2.'$bodyscript .= "";'."\n";
			$str .= $tab2.'return $bodyscript;'."\n";
			$str .= $tab1."}\n\n";

			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Definicion del Grid y la Forma'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function defgrid( $deployed = false ){'."\n";
			$str .= $tab2.'$i      = 1;'."\n";
			$str .= $tab2.'$editar = "false";'."\n\n";
			$str .= $tab2.'$grid  = new $this->jqdatagrid;'."\n\n";
			$columna .= $str;
			$str = '';

			$columna .= $this->jqgridcol($db);

			$str  = $tab2.'$grid->showpager(true);'."\n";
			$str .= $tab2.'$grid->setWidth(\'\');'."\n";
			$str .= $tab2.'$grid->setHeight(\'290\');'."\n";
			$str .= $tab2.'$grid->setTitle($this->titp);'."\n";
			$str .= $tab2.'$grid->setfilterToolbar(true);'."\n";
			$str .= $tab2.'$grid->setToolbar(\'false\', \'"top"\');'."\n\n";

			$str .= $tab2.'$grid->setOnSelectRow(\''."\n";
			$str .= $tab3.'function(id){'."\n";
			$str .= $tab4.'if (id){'."\n";
			$str .= $tab5.'jQuery(gridId2).jqGrid("setGridParam",{url:"\'.site_url($this->url.\'getdatait/\').\'/"+id+"/", page:1});'."\n";
			$str .= $tab5.'jQuery(gridId2).trigger("reloadGrid");'."\n";
			$str .= $tab4.'}'."\n";
			$str .= $tab3.'}\''."\n";
			$str .= $tab2.');'."\n";

			$str .= $tab2.'$grid->setFormOptionsE(\'closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} \');'."\n";
			$str .= $tab2.'$grid->setFormOptionsA(\'closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} \');'."\n";

			$str .= $tab2.'$grid->setAfterSubmit("$(\'#respuesta\').html(\'&lt;span style='."\\'font-weight:bold; color:red;\\'&gt;'+a.responseText+'&lt;/span&gt;'); return [true, a ];".'");'."\n\n";

			$str .= $tab2.'#show/hide navigations buttons'."\n";
			$str .= $tab2.'$grid->setAdd(    $this->datasis->sidapuede(\''.strtoupper($db).'\',\'INCLUIR%\' ));'."\n";
			$str .= $tab2.'$grid->setEdit(   $this->datasis->sidapuede(\''.strtoupper($db).'\',\'MODIFICA%\'));'."\n";
			$str .= $tab2.'$grid->setDelete( $this->datasis->sidapuede(\''.strtoupper($db).'\',\'BORR_REG%\'));'."\n";
			$str .= $tab2.'$grid->setSearch( $this->datasis->sidapuede(\''.strtoupper($db).'\',\'BUSQUEDA%\'));'."\n";
			$str .= $tab2.'$grid->setRowNum(30);'."\n";

			$str .= $tab2.'$grid->setShrinkToFit(\'false\');'."\n\n";

			$str .= $tab2.'$grid->setBarOptions("addfunc: '.strtolower($db).'add, editfunc: '.strtolower($db).'edit, delfunc: '.strtolower($db).'del, viewfunc: '.strtolower($db).'show");'."\n\n";


			$str .= $tab2.'#Set url'."\n";
			$str .= $tab2.'$grid->setUrlput(site_url($this->url.\'setdata/\'));'."\n\n";

			$str .= $tab2.'#GET url'."\n";
			$str .= $tab2.'$grid->setUrlget(site_url($this->url.\'getdata/\'));'."\n\n";

			$str .= $tab2.'if ($deployed) {'."\n";
			$str .= $tab2.'	return $grid->deploy();'."\n";
			$str .= $tab2.'} else {'."\n";
			$str .= $tab2.'	return $grid;'."\n";
			$str .= $tab2.'}'."\n";
			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Busca la data en el Servidor por json'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function getdata()'."\n";
			$str .= $tab1.'{'."\n";

			$str .= $tab2.'$grid       = $this->jqdatagrid;'."\n\n";

			$str .= $tab2.'// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO'."\n";
			$str .= $tab2.'$mWHERE = $grid->geneTopWhere(\''.$db.'\');'."\n\n";

			$str .= $tab2.'$response   = $grid->getData(\''.$db.'\', array(array()), array(), false, $mWHERE, \'id\',\'desc\' );'."\n";
			$str .= $tab2.'$rs = $grid->jsonresult( $response);'."\n";
			$str .= $tab2.'echo $rs;'."\n";

			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Guarda la Informacion'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function setData()'."\n";
			$str .= $tab1.'{'."\n";
			$str .= $tab2.'$this->load->library(\'jqdatagrid\');'."\n";
			$str .= $tab2.'$oper   = $this->input->post(\'oper\');'."\n";
			$str .= $tab2.'$id     = $this->input->post(\'id\');'."\n";
			$str .= $tab2.'$data   = $_POST;'."\n";
			$str .= $tab2.'$mcodp  = "??????";'."\n";
			$str .= $tab2.'$check  = 0;'."\n\n";

			$str .= $tab2.'unset($data[\'oper\']);'."\n";
			$str .= $tab2.'unset($data[\'id\']);'."\n";

			$str .= $tab2.'if($oper == \'add\'){'."\n";
			$str .= $tab3.'if(false == empty($data)){'."\n";
			$str .= $tab4.'$check = $this->datasis->dameval("SELECT count(*) FROM '.$db.' WHERE $mcodp=".$this->db->escape($data[$mcodp]));'."\n";
			$str .= $tab4.'if ( $check == 0 ){'."\n";
			$str .= $tab5.'$this->db->insert(\''.$db.'\', $data);'."\n";
			$str .= $tab5.'echo "Registro Agregado";'."\n\n";
			$str .= $tab5.'logusu(\''.strtoupper($db).'\',"Registro ????? INCLUIDO");'."\n";
			$str .= $tab4.'} else'."\n";
			$str .= $tab5.'echo "Ya existe un registro con ese $mcodp";'."\n";

			$str .= $tab3.'} else'."\n";
			//$str .= $tab2.'echo \'\';'."\n";
			$str .= $tab4.'echo "Fallo Agregado!!!";'."\n\n";

			$str .= $tab2.'} elseif($oper == \'edit\') {'."\n";
			$str .= $tab3.'$nuevo  = $data[$mcodp];'."\n";
			$str .= $tab3.'$anterior = $this->datasis->dameval("SELECT $mcodp FROM '.$db.' WHERE id=$id");'."\n";
			$str .= $tab3.'if ( $nuevo <> $anterior ){'."\n";
			$str .= $tab4.'//si no son iguales borra el que existe y cambia'."\n";
			$str .= $tab4.'$this->db->query("DELETE FROM '.$db.' WHERE $mcodp=?", array($mcodp));'."\n";
			$str .= $tab4.'$this->db->query("UPDATE '.$db.' SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));'."\n";
			$str .= $tab4.'$this->db->where("id", $id);'."\n";
			$str .= $tab4.'$this->db->update("'.$db.'", $data);'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");'."\n";
			$str .= $tab4.'echo "Grupo Cambiado/Fusionado en clientes";'."\n";

			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'unset($data[$mcodp]);'."\n";
			$str .= $tab4.'$this->db->where("id", $id);'."\n";
			$str .= $tab4.'$this->db->update(\''.$db.'\', $data);'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"Grupo de Cliente  ".$nuevo." MODIFICADO");'."\n";
			$str .= $tab4.'echo "$mcodp Modificado";'."\n";
			$str .= $tab3.'}'."\n\n";

			$str .= $tab2.'} elseif($oper == \'del\') {'."\n";
			$str .= $tab3.'$meco = $this->datasis->dameval("SELECT $mcodp FROM '.$db.' WHERE id=$id");'."\n";

			$str .= $tab3.'//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM '.$db.' WHERE id=\'$id\' ");'."\n";
			$str .= $tab3.'if ($check > 0){'."\n";
			$str .= $tab4.'echo " El registro no puede ser eliminado; tiene movimiento ";'."\n";
			$str .= $tab3.'} else {'."\n";
			$str .= $tab4.'$this->db->simple_query("DELETE FROM '.$db.' WHERE id=$id ");'."\n";
			$str .= $tab4.'logusu(\''.strtoupper($db).'\',"Registro ????? ELIMINADO");'."\n";
			$str .= $tab4.'echo "Registro Eliminado";'."\n";
			$str .= $tab3.'}'."\n";
			$str .= $tab2.'};'."\n";
			$str .= $tab1.'}'."\n\n";


			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'//Definicion del Grid y la Forma'."\n";
			$str .= $tab1.'//***************************'."\n";
			$str .= $tab1.'function defgridit( $deployed = false ){'."\n";
			$str .= $tab2.'$i      = 1;'."\n";
			$str .= $tab2.'$editar = "false";'."\n\n";
			$str .= $tab2.'$grid  = new $this->jqdatagrid;'."\n\n";
			$columna .= $str;
			$str = '';

			$columna .= $this->jqgridcol($dbit);

			$str  = $tab2.'$grid->showpager(true);'."\n";
			$str  = $tab2.'$grid->setWidth("");'."\n";
			$str  = $tab2.'$grid->setHeight(\'190\');'."\n";
			$str  = $tab2.'$grid->setfilterToolbar(false);'."\n";
			$str  = $tab2.'$grid->setToolbar(\'false\', \'"top"\');'."\n";

			$str  = $tab2.'#show/hide navigations buttons'."\n";
			$str  = $tab2.'$grid->setAdd(false);'."\n";
			$str  = $tab2.'$grid->setEdit(false);'."\n";
			$str  = $tab2.'$grid->setDelete(false);'."\n";
			$str  = $tab2.'$grid->setSearch(true);'."\n";
			$str  = $tab2.'$grid->setRowNum(30);'."\n";
			$str  = $tab2.'$grid->setShrinkToFit(\'false\');'."\n";


			$str .= $tab2.'#Set url'."\n";
			$str .= $tab2.'$grid->setUrlput(site_url($this->url.\'setdatait/\'));'."\n\n";

			$str .= $tab2.'#GET url'."\n";
			$str .= $tab2.'$grid->setUrlget(site_url($this->url.\'getdatait/\'));'."\n\n";

			$str .= $tab2.'if ($deployed) {'."\n";
			$str .= $tab2.'	return $grid->deploy();'."\n";
			$str .= $tab2.'} else {'."\n";
			$str .= $tab2.'	return $grid;'."\n";
			$str .= $tab2.'}'."\n";
			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Busca la data en el Servidor por json'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function getdatait( $id = 0 )'."\n";
			$str .= $tab1.'{'."\n";

			$str .= $tab2.'if ($id === 0 ){'."\n";
			$str .= $tab3.'$id = $this->datasis->dameval("SELECT MAX(id) FROM '.$db.'");'."\n";
			$str .= $tab2.'}'."\n";
			$str .= $tab2.'if(empty($id)) return "";'."\n";
			$str .= $tab2.'$numero   = $this->datasis->dameval("SELECT numero FROM '.$db.' WHERE id=$id");'."\n";

			$str .= $tab2.'$grid    = $this->jqdatagrid;'."\n";
			$str .= $tab2.'$mSQL    = "SELECT * FROM '.$dbit.' WHERE numero=\'$numero\' ";'."\n";
			$str .= $tab2.'$response   = $grid->getDataSimple($mSQL);'."\n";
			$str .= $tab2.'$rs = $grid->jsonresult( $response);'."\n";
			$str .= $tab2.'echo $rs;'."\n";

			$str .= $tab1.'}'."\n\n";

			$str .= $tab1.'/**'."\n";
			$str .= $tab1.'* Guarda la Informacion'."\n";
			$str .= $tab1.'*/'."\n";
			$str .= $tab1.'function setDatait()'."\n";
			$str .= $tab1.'{'."\n";
			$str .= $tab1.'}'."\n\n";


			$str .= $tab1.'//***********************************'."\n";
			$str .= $tab1.'// DataEdit  '."\n";
			$str .= $tab1.'//***********************************'."\n";

			$str .= $this->genecrudjq($db, false);

			$str .= $this->genepre( $db, false);
			$str .= $this->genepost($db, false);
			$str .= $this->geneinstalar($db, false);

			$str .= '}'."\n";

			$columna .= $str."\n";

			echo $columna."</pre>";

		}

	}


	//********************************
	// Genera la clase
	//********************************
	function jqgridclase($db, $contro){
		$tab1 = $this->mtab(1);
		$tab2 = $this->mtab(2);
		$tab3 = $this->mtab(3);

		$str  = '';
		$str .= 'class '.ucfirst($db).' extends Controller {'."\n";
		$str .= $tab1.'var $mModulo = \''.strtoupper($db).'\';'."\n";
		$str .= $tab1.'var $titp    = \'Modulo '.strtoupper($db).'\';'."\n";
		$str .= $tab1.'var $tits    = \'Modulo '.strtoupper($db).'\';'."\n";
		$str .= $tab1.'var $url     = \''.$contro.'/'.$db.'/\';'."\n\n";

		$str .= $tab1.'function '.ucfirst($db).'(){'."\n";
		$str .= $tab2.'parent::Controller();'."\n";
		$str .= $tab2.'$this->load->library(\'rapyd\');'."\n";
		$str .= $tab2.'$this->load->library(\'jqdatagrid\');'."\n";
		$str .= $tab2.'$this->datasis->modulo_nombre( \''.strtoupper($db).'\', $ventana=0 );'."\n";
		$str .= $tab1.'}'."\n\n";

		$str .= $tab1.'function index(){'."\n";
		$str .= $tab2.'/*if ( !$this->datasis->iscampo(\''.$db.'\',\'id\') ) {'."\n";
		$str .= $tab3.'$this->db->simple_query(\'ALTER TABLE '.$db.' DROP PRIMARY KEY\');'."\n";
		$str .= $tab3.'$this->db->simple_query(\'ALTER TABLE '.$db.' ADD UNIQUE INDEX numero (numero)\');'."\n";
		$str .= $tab3.'$this->db->simple_query(\'ALTER TABLE '.$db.' ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)\');'."\n";
		$str .= $tab2.'};*/'."\n";

		$str .= $tab2.'//$this->datasis->creaintramenu(array(\'modulo\'=>\'000\',\'titulo\'=>\'<#titulo#>\',\'mensaje\'=>\'<#mensaje#>\',\'panel\'=>\'<#panal#>\',\'ejecutar\'=>\'<#ejecuta#>\',\'target\'=>\'popu\',\'visible\'=>\'S\',\'pertenece\'=>\'<#pertenece#>\',\'ancho\'=>900,\'alto\'=>600));'."\n";

		$str .= $tab2.'$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );'."\n";
		$str .= $tab2.'redirect($this->url.\'jqdatag\');'."\n";
		$str .= $tab1.'}'."\n\n";

		return $str;

	}


	//************************************
	//
	//Genera las Columnas
	//
	function jqgridcol($db){
		$tab1 = $this->mtab(1);
		$tab2 = $this->mtab(2);
		$tab3 = $this->mtab(3);
		$tab4 = $this->mtab(4);
		$tab5 = $this->mtab(5);
		$tab6 = $this->mtab(6);
		$tab7 = $this->mtab(7);
		$tab8 = $this->mtab(8);

		$query = $this->db->query("DESCRIBE $db");
		$columna = '';
		$str     = '';
		foreach ($query->result() as $row){
			if ( $row->Field == 'id') {
				$str   = $tab2.'$grid->addField(\'id\');'."\n";
				$str  .= $tab2.'$grid->label(\'Id\');'."\n";
				$str  .= $tab2.'$grid->params(array('."\n";
				$str  .= $tab3.'\'align\'         => "\'center\'",'."\n";
				$str  .= $tab3.'\'frozen\'        => \'true\','."\n";
				$str  .= $tab3.'\'width\'         => 40,'."\n";
				$str  .= $tab3.'\'editable\'      => \'false\','."\n";
				$str  .= $tab3.'\'search\'        => \'false\''."\n";
			} else {
				$str  = $tab2.'$grid->addField(\''.$row->Field.'\');'."\n";
				$str .= $tab2.'$grid->label(\''.ucfirst($row->Field).'\');'."\n";

				$str .= $tab2.'$grid->params(array('."\n";
				$str .= $tab3.'\'search\'        => \'true\','."\n";
				$str .= $tab3.'\'editable\'      => $editar,'."\n";

				if ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
					$str .= $tab3.'\'width\'         => 80,'."\n";
					$str .= $tab3.'\'align\'         => "\'center\'",'."\n";
					$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
					$str .= $tab3.'\'editrules\'     => \'{ required:true,date:true}\','."\n";
					$str .= $tab3.'\'formoptions\'   => \'{ label:"Fecha" }\''."\n";

				} elseif ( substr($row->Type,0,7) == 'decimal' or substr($row->Type,0,3) == 'int'  ) {
					$str .= $tab3.'\'align\'         => "\'right\'",'."\n";
					$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
					$str .= $tab3.'\'width\'         => 100,'."\n";
					$str .= $tab3.'\'editrules\'     => \'{ required:true }\','."\n";
					$str .= $tab3.'\'editoptions\'   => \'{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }\','."\n";
					$str .= $tab3.'\'formatter\'     => "\'number\'",'."\n";
					if (substr($row->Type,0,3) == 'int'){
						$str .= $tab3.'\'formatoptions\' => \'{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }\''."\n";
					} else {
						$str .= $tab3.'\'formatoptions\' => \'{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }\''."\n";
					}

				} elseif ( substr($row->Type,0,7) == 'varchar' or substr($row->Type,0,4) == 'char'  ) {
					$long = str_replace(array('varchar(','char(',')'),"", $row->Type)*10;
					$maxlong = $long/10;
					if ( $long > 200 ) $long = 200;
					if ( $long < 40 ) $long = 40;

					$str .= $tab3.'\'width\'         => '.$long.','."\n";
					$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
					$str .= $tab3.'\'editrules\'     => \'{ required:true}\','."\n";
					$str .= $tab3.'\'editoptions\'   => \'{ size:'.$maxlong.', maxlength: '.$maxlong.' }\','."\n";

				} elseif ( $row->Type == 'text' ) {
					$long = 250;
					$str .= $tab3.'\'width\'         => '.$long.','."\n";
					$str .= $tab3.'\'edittype\'      => "\'textarea\'",'."\n";
					$str .= $tab3.'\'editoptions\'   => "\'{rows:2, cols:60}\'",'."\n";

				} else {
					$str .= $tab3.'\'width\'         => 140,'."\n";
					$str .= $tab3.'\'edittype\'      => "\'text\'",'."\n";
				}
			}
			$str .= $tab2.'));'."\n\n";
			$columna .= $str."\n";
		}
		return $columna;
	}


	// Genera un jqgrid simple a partir de una tabla
	function jqgridsimple(){
		$tabla = $this->uri->segment(3);
		if($tabla===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/controlador/directorio/id"');
		}

		$contro =$this->uri->segment(4);
		if($contro===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/controlador/directorio/id"');
		}

		$directo =$this->uri->segment(5);
		if($directo===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/controlador/directorio/id"');
		}
		$id =$this->uri->segment(6);
		if($id==false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/controlador/directorio/id"');
		}
		$str = $this->datasis->jqgridsimplegene($tabla, $contro, $directo, $id);
		echo "<pre>".$str."</pre>";

	}


	function mtab($n = 1){ return str_repeat("\t",$n); }

	//******************************************************************
	// Gener Crud
	function genecrudjq($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla)))
			show_error('Tabla no existe o faltan parametros');

		$crud ="\n\t".'function dataedit(){'."\n";
		$crud.="\t\t".'$this->rapyd->load(\'dataedit\');'."\n";

		$crud.="\t\t".'$script= \''."\n";
		$crud.="\t\t".'$(function() {'."\n";
		$crud.="\t\t\t".'$("#fecha").datepicker({dateFormat:"dd/mm/yy"});'."\n";
		$crud.="\t\t\t".'$(".inputnum").numeric(".");'."\n";
		$crud.="\t\t".'});'."\n";
		$crud.="\t\t".'\';'."\n\n";

		$crud.="\t\t".'$edit = new DataEdit($this->tits, \''.$tabla.'\');'."\n\n";
		$crud.="\t\t".'$edit->script($script,\'modify\');'."\n";
		$crud.="\t\t".'$edit->script($script,\'create\');'."\n";
		$crud.="\t\t".'$edit->on_save_redirect=false;'."\n\n";
		$crud.="\t\t".'$edit->back_url = site_url($this->url.\'filteredgrid\');'."\n\n";

		$crud.="\t\t".'$edit->script($script,\'create\');'."\n\n";
		$crud.="\t\t".'$edit->script($script,\'modify\');'."\n\n";

		$crud.="\t\t".'$edit->post_process(\'insert\',\'_post_insert\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'update\',\'_post_update\');'."\n";
		$crud.="\t\t".'$edit->post_process(\'delete\',\'_post_delete\');'."\n";
		$crud.="\t\t".'$edit->pre_process(\'insert\', \'_pre_insert\' );'."\n";
		$crud.="\t\t".'$edit->pre_process(\'update\', \'_pre_update\' );'."\n";
		$crud.="\t\t".'$edit->pre_process(\'delete\', \'_pre_delete\' );'."\n";
		$crud.="\n";

		$mSQL="DESCRIBE $tabla";
		$query = $this->db->query("DESCRIBE $tabla");
		foreach ($query->result() as $field){

			if($field->Field=='usuario'){
				$crud.="\t\t".'$edit->usuario = new autoUpdateField(\'usuario\',$this->session->userdata(\'usuario\'),$this->session->userdata(\'usuario\'));'."\n\n";
			}elseif($field->Field=='estampa'){
				$crud.="\t\t".'$edit->estampa = new autoUpdateField(\'estampa\' ,date(\'Ymd\'), date(\'Ymd\'));'."\n\n";
			}elseif($field->Field=='hora'){
				$crud.="\t\t".'$edit->hora    = new autoUpdateField(\'hora\',date(\'H:i:s\'), date(\'H:i:s\'));'."\n\n";
			}elseif($field->Field=='id'){
				continue;
			}else{
				preg_match('/(?P<tipo>\w+)(\((?P<length>[0-9\,]+)\)){0,1}/', $field->Type, $matches);
				if(isset($matches['length'])){
					$def=explode(',',$matches['length']);
				}else{
					$def[0]=8;
				}

				if(strrpos($field->Type,'date')!==false){
					$input='dateonly';
				}elseif(strrpos($field->Type,'text')!==false){
					$input= 'textarea';
				}else{
					$input='input';
				}

				$crud.="\t\t".'$edit->'.$field->Field.' = new '.$input."Field('".ucfirst($field->Field)."','$field->Field');\n";

				if(preg_match("/decimal/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='numeric';\n";
					$crud.="\t\t".'$edit->'.$field->Field."->css_class='inputnum';\n";

				}elseif(preg_match("/integer|int/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='integer';\n";
					$crud.="\t\t".'$edit->'.$field->Field."->css_class='inputonlynum';\n";

				}elseif(preg_match("/date/i",$field->Type)){
					$crud.="\t\t".'$edit->'.$field->Field."->rule='chfecha';\n";

				}else{
					$crud.="\t\t".'$edit->'.$field->Field."->rule='';\n";
				}

				if(strrpos($field->Type,'text')===false){
					$crud.="\t\t".'$edit->'.$field->Field.'->size ='.($def[0]+2).";\n";
					$crud.="\t\t".'$edit->'.$field->Field.'->maxlength ='.($def[0]).";\n";
				}else{
					$crud.="\t\t".'$edit->'.$field->Field."->cols = 70;\n";
					$crud.="\t\t".'$edit->'.$field->Field."->rows = 4;\n";
				}
				$crud.="\n";
			}
		}

		$crud.="\t\t".'$edit->build();'."\n\n";

		$crud.="\t\t".'if($edit->on_success()){'."\n";
		$crud.="\t\t".'	$rt=array('."\n";
		$crud.="\t\t".'		\'status\' =>\'A\','."\n";
		$crud.="\t\t".'		\'mensaje\'=>\'Registro guardado\','."\n";
		$crud.="\t\t".'		\'pk\'     =>$edit->_dataobject->pk'."\n";
		$crud.="\t\t".'	);'."\n";
		$crud.="\t\t".'	echo json_encode($rt);'."\n";
		$crud.="\t\t".'}else{'."\n";
		$crud.="\t\t".'	echo $edit->output;'."\n";
		$crud.="\t\t".'}'."\n";

		$crud.="\t".'}'."\n";

		if($s){
			$data['programa'] ='<pre>'.$crud.'</pre>';
			$data['head']    = '';
			$data['title']   =heading('Generador de crud');
			$this->load->view('editorcm', $data);
			//$this->load->view('jqgrid/ventanajq', $data);
		}else{
			return $crud;
		}
	}

	//******************************************************************
	//    Genera el View a partir de la Tabla
	//******************************************************************
	function geneviewjq($tabla=null,$s=true){
		if (empty($tabla) OR (!$this->db->table_exists($tabla)))
			show_error('Tabla no existe o faltan parametros');

		$crud  ="\t".'<?php'."\n";
		$crud .="\t".'echo $form_scripts;'."\n";
		$crud .="\t".'echo $form_begin;'."\n\n";
		$crud .="\t".'if(isset($form->error_string)) echo \'<div class="alert">\'.$form->error_string.\'</div>\';'."\n";
		$crud .="\t".'if($form->_status <> \'show\'){ ?>'."\n\n";
		$crud .="\t".'<script language="javascript" type="text/javascript">'."\n";
		$crud .="\t".'</script>'."\n";
		$crud .="\t".'<?php } ?>'."\n\n";
		$crud .="\t".'<fieldset  style=\'border: 1px outset #FEB404;background: #FFFCE8;\'>'."\n";
		$crud .="\t".'<table width=\'100%\'>'."\n";

		$mSQL ="DESCRIBE $tabla";
		$query = $this->db->query("DESCRIBE $tabla");
		foreach ($query->result() as $field){
			$crud .="\t".'	<tr>'."\n";
			$crud .="\t".'		<td class="littletablerowth"><?php echo $form->'.$field->Field.'->label;  ?></td>'."\n";
			$crud .="\t".'		<td class="littletablerow"  ><?php echo $form->'.$field->Field.'->output; ?></td>'."\n";
			$crud .="\t".'	</tr>'."\n";
		}

		$crud .="\t".'</table>'."\n";
		$crud .="\t".'</fieldset>'."\n";
		$crud .="\t".'<?php echo $form_end; ?>'."\n";

		echo '<html><body><pre>'.htmlentities( $crud).'</pre></body></html>';

	}


	function editor(){
			$this->load->view('editorcm');
	}

	function jqguarda(){
		$code   = $this->input->post('code');
		$db     = $this->input->post('bd');
		$contro = $this->input->post('contro');
		file_put_contents('system/application/controllers/'.$contro.'/'.$db.'.php',$code);
		//redirect(base_url.'desarrollo/jqcargar/'.$db.'/'.$contro);
	}

	function jqcargar(){
		$db = $this->uri->segment(3);
		if($db===false){
			exit('Debe especificar en la uri la tabla y el directorio "/tabla/directorio"');
		}
		$contro =$this->uri->segment(4);
		if($contro===false){
			$contro = '';
		}
		if ( $contro == '' )
			$leer = file_get_contents('system/application/controllers/'.$db.'.php');
		else
			$leer = file_get_contents('system/application/controllers/'.$contro.'/'.$db.'.php');

		$data['programa']    = $leer;
		$data['bd']          = $db;
		$data['controlador'] = $contro;
		$this->load->view('editorcm', $data);

	}

	function ccc(){
		print_r($this->datasis->controladores());
	}


	function menu(){

		
		$arbol = '<?xml version=\'1.0\' encoding="utf-8"?>
<rows>
    <page>1</page>
    <total>1</total>
    <records>1</records>
    <row><cell>1</cell><cell>Listas de Campos</cell><cell></cell><cell>0</cell><cell>1</cell><cell>10</cell><cell>false</cell><cell>false</cell></row>

    <row><cell>2</cell><cell>En arreglo $data</cell><cell>'.site_url('desarrollo/camposdb').'</cell><cell>1</cell><cell>2</cell><cell>3</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>3</cell><cell>Separados x ,   </cell><cell>jsonex.html   </cell><cell>1</cell><cell>4</cell><cell>5</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>4</cell><cell>Separado x ","  </cell><cell>loadoncex.html</cell><cell>1</cell><cell>6</cell><cell>7</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>5</cell><cell>Separado x \',\'</cell><cell>localex.html  </cell><cell>1</cell><cell>8</cell><cell>9</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>6</cell><cell>Manipulating</cell><cell></cell><cell>0</cell><cell>11</cell><cell>18</cell><cell>false</cell><cell>false</cell></row>

    <row><cell>7</cell><cell>Grid Data  </cell><cell>manipex.html</cell><cell>1</cell><cell>12</cell><cell>13</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>8</cell><cell>Get Methods</cell><cell>getex.html  </cell><cell>1</cell><cell>14</cell><cell>15</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>9</cell><cell>Set Methods</cell><cell>setex.html  </cell><cell>1</cell><cell>16</cell><cell>17</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>10</cell><cell>Advanced       </cell><cell></cell><cell>0</cell><cell>19</cell><cell>32</cell><cell>false</cell><cell>false</cell></row>

    <row><cell>11</cell><cell>Multi Select   </cell><cell>multiex.html     </cell><cell>1</cell><cell>20</cell><cell>21</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>12</cell><cell>Master Detail  </cell><cell>masterex.html    </cell><cell>1</cell><cell>22</cell><cell>23</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>13</cell><cell>Subgrid        </cell><cell>subgrid.html     </cell><cell>1</cell><cell>24</cell><cell>25</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>14</cell><cell>Grid as Subgrid</cell><cell>subgrid_grid.html</cell><cell>1</cell><cell>26</cell><cell>27</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>15</cell><cell>Resizing       </cell><cell>resizeex.html    </cell><cell>1</cell><cell>28</cell><cell>28</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>16</cell><cell>Search Big Sets</cell><cell>bigset.html      </cell><cell>1</cell><cell>30</cell><cell>31</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>17</cell><cell>New since beta 3.0</cell><cell></cell><cell>0</cell><cell>33</cell><cell>44</cell><cell>false</cell><cell>false</cell></row>

    <row><cell>18</cell><cell>Custom Multi Select</cell><cell>cmultiex.html </cell><cell>1</cell><cell>34</cell><cell>35</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>19</cell><cell>Subgrid with JSON  </cell><cell>jsubgrid.html </cell><cell>1</cell><cell>36</cell><cell>37</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>20</cell><cell>After Load Callback</cell><cell>loadcml.html  </cell><cell>1</cell><cell>38</cell><cell>39</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>21</cell><cell>Resizable Columns  </cell><cell>resizecol.html</cell><cell>1</cell><cell>40</cell><cell>41</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>22</cell><cell>Hide/Show Columns  </cell><cell>hideex.html   </cell><cell>1</cell><cell>42</cell><cell>43</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>23</cell><cell>Row Editing (new)</cell><cell></cell><cell>0</cell><cell>45</cell><cell>58</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>24</cell><cell>Basic Example</cell><cell>rowedex1.html</cell><cell>1</cell><cell>46</cell><cell>47</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>25</cell><cell>Custom Edit</cell><cell>rowedex2.html</cell><cell>1</cell><cell>48</cell><cell>49</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>26</cell><cell>Using Events</cell><cell>rowedex3.html</cell><cell>1</cell><cell>50</cell><cell>51</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>27</cell><cell>Full Control</cell><cell>rowedex4.html</cell><cell>1</cell><cell>52</cell><cell>53</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>28</cell><cell>Input types</cell><cell>rowedex5.html</cell><cell>1</cell><cell>54</cell><cell>55</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>135</cell><cell>Inline Navigator (new)</cell><cell>43rowedex.html</cell><cell>1</cell><cell>56</cell><cell>57</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>29</cell><cell>Data Mapping</cell><cell></cell><cell>0</cell><cell>59</cell><cell>66</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>30</cell><cell>XML Mapping</cell><cell>xmlmap.html</cell><cell>1</cell><cell>60</cell><cell>61</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>31</cell><cell>JSON Mapping</cell><cell>jsonmap.html</cell><cell>1</cell><cell>62</cell><cell>63</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>32</cell><cell>Data Optimization</cell><cell>jsonopt.html</cell><cell>1</cell><cell>64</cell><cell>65</cell><cell>true</cell><cell>true</cell></row>
	
    <row><cell>33</cell><cell>Integrations</cell><cell></cell><cell>0</cell><cell>67</cell><cell>70</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>34</cell><cell>UI Datepicker</cell><cell>calendar.html</cell><cell>1</cell><cell>68</cell><cell>69</cell><cell>true</cell><cell>true</cell></row>
	
    <row><cell>35</cell><cell>Live Data Manipulation</cell><cell></cell><cell>0</cell><cell>70</cell><cell>81</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>36</cell><cell>Searching Data</cell><cell>searching.html</cell><cell>1</cell><cell>71</cell><cell>72</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>37</cell><cell>Edit row</cell><cell>editing.html</cell><cell>1</cell><cell>73</cell><cell>74</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>38</cell><cell>Add row</cell><cell>adding.html</cell><cell>1</cell><cell>75</cell><cell>76</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>39</cell><cell>Delete row</cell><cell>deleting.html</cell><cell>1</cell><cell>77</cell><cell>78</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>40</cell><cell>Navigator</cell><cell>navgrid.html</cell><cell>1</cell><cell>79</cell><cell>80</cell><cell>true</cell><cell>true</cell></row>
	
    <row><cell>41</cell><cell>New in version 3.1</cell><cell></cell><cell>0</cell><cell>81</cell><cell>90</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>42</cell><cell>Toolbars and userdata</cell><cell>toolbar.html</cell><cell>1</cell><cell>82</cell><cell>83</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>43</cell><cell>New Methods</cell><cell>methods.html</cell><cell>1</cell><cell>84</cell><cell>85</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>44</cell><cell>Post Data</cell><cell>postdata.html</cell><cell>1</cell><cell>86</cell><cell>87</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>45</cell><cell>Common Params</cell><cell>defparams.html</cell><cell>1</cell><cell>88</cell><cell>89</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>46</cell><cell>New in version 3.2</cell><cell></cell><cell>0</cell><cell>91</cell><cell>106</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>47</cell><cell>New Methods 3.2</cell><cell>methods32.html</cell><cell>1</cell><cell>92</cell><cell>93</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>48</cell><cell>Initial hidden grid</cell><cell>hiddengrid.html</cell><cell>1</cell><cell>94</cell><cell>95</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>49</cell><cell>After Insert Row event</cell><cell>afterinsrow.html</cell><cell>1</cell><cell>96</cell><cell>97</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>50</cell><cell>Controling server errors</cell><cell>loaderror.html</cell><cell>1</cell><cell>98</cell><cell>99</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>51</cell><cell>Hide/Show columns</cell><cell>hideshow.html</cell><cell>1</cell><cell>100</cell><cell>101</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>52</cell><cell>Custom Button and Forms</cell><cell>custbutt.html</cell><cell>1</cell><cell>102</cell><cell>103</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>53</cell><cell>Client Validation</cell><cell>csvalid.html</cell><cell>1</cell><cell>104</cell><cell>105</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>54</cell><cell>New in version 3.3</cell><cell></cell><cell>0</cell><cell>107</cell><cell>126</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>55</cell><cell>Dynamic height and width</cell><cell>gridwidth.html</cell><cell>1</cell><cell>108</cell><cell>109</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>56</cell><cell>Tree Grid</cell><cell>treegrid.html</cell><cell>1</cell><cell>110</cell><cell>111</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>57</cell><cell>Cell Editing</cell><cell>celledit.html</cell><cell>1</cell><cell>112</cell><cell>113</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>58</cell><cell>Visible Columns</cell><cell>setcolumns.html</cell><cell>1</cell><cell>114</cell><cell>115</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>59</cell><cell>HTML Table to Grid</cell><cell>tbltogrid.html</cell><cell>1</cell><cell>116</cell><cell>117</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>60</cell><cell>Multiple Toolbar Search</cell><cell>search1.html</cell><cell>1</cell><cell>118</cell><cell>119</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>61</cell><cell>Multiple Form Search</cell><cell>search2.html</cell><cell>1</cell><cell>120</cell><cell>121</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>62</cell><cell>Data type as function</cell><cell>datatype.html</cell><cell>1</cell><cell>122</cell><cell>123</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>63</cell><cell>Row Drag and Drop</cell><cell>tablednd.html</cell><cell>1</cell><cell>124</cell><cell>125</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>64</cell><cell>New in version 3.4</cell><cell></cell><cell>0</cell><cell>127</cell><cell>140</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>65</cell><cell>Formater</cell><cell>formatter.html</cell><cell>1</cell><cell>128</cell><cell>129</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>66</cell><cell>Custom Formater</cell><cell>custfrm.html</cell><cell>1</cell><cell>130</cell><cell>131</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>67</cell><cell>Import Configuration from XML</cell><cell>xmlimp.html</cell><cell>1</cell><cell>132</cell><cell>133</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>68</cell><cell>Autoloading data when scroll</cell><cell>scrgrid.html</cell><cell>1</cell><cell>134</cell><cell>135</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>69</cell><cell>Scroll with dynamic row select</cell><cell>navgrid2.html</cell><cell>1</cell><cell>136</cell><cell>137</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>70</cell><cell>Tree Grid Adjacency model</cell><cell>treegrid2.html</cell><cell>1</cell><cell>138</cell><cell>139</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>71</cell><cell>New in version 3.5</cell><cell></cell><cell>0</cell><cell>141</cell><cell>160</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>72</cell><cell>Autowidth and row numbering</cell><cell>autowidth.html</cell><cell>1</cell><cell>142</cell><cell>143</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>73</cell><cell>Grid view mode</cell><cell>speed.html</cell><cell>1</cell><cell>144</cell><cell>145</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>74</cell><cell>Integrated Search Toolbar</cell><cell>search3.html</cell><cell>1</cell><cell>146</cell><cell>147</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>75</cell><cell>Advanced Searching</cell><cell>search4.html</cell><cell>1</cell><cell>148</cell><cell>149</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>76</cell><cell>Form Improvements</cell><cell>navgrid3.html</cell><cell>1</cell><cell>150</cell><cell>151</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>77</cell><cell>TreeGrid real world example</cell><cell>treegridadv.html</cell><cell>1</cell><cell>152</cell><cell>153</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>78</cell><cell>Form Navigation</cell><cell>navgrid4.html</cell><cell>1</cell><cell>154</cell><cell>155</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>79</cell><cell>Summary Footer Row</cell><cell>summary.html</cell><cell>1</cell><cell>156</cell><cell>157</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>80</cell><cell>View sortable columns</cell><cell>sortcols.html</cell><cell>1</cell><cell>158</cell><cell>159</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>81</cell><cell>New in version 3.6</cell><cell></cell><cell>0</cell><cell>161</cell><cell>186</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>82</cell><cell>New API</cell><cell>36newapi.html</cell><cell>1</cell><cell>162</cell><cell>163</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>83</cell><cell>RTL Support</cell><cell>36rtl.html</cell><cell>1</cell><cell>164</cell><cell>165</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>84</cell><cell>Column Reordering</cell><cell>36colreorder.html</cell><cell>1</cell><cell>166</cell><cell>167</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>85</cell><cell>Column Chooser</cell><cell>36columnchoice.html</cell><cell>1</cell><cell>168</cell><cell>169</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>86</cell><cell>Custom Validation</cell><cell>36custvalid.html</cell><cell>1</cell><cell>170</cell><cell>171</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>87</cell><cell>Create Custom input element</cell><cell>36custinput.html</cell><cell>1</cell><cell>172</cell><cell>173</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>88</cell><cell>Ajax Improvements</cell><cell>36ajaxing.html</cell><cell>1</cell><cell>174</cell><cell>175</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>89</cell><cell>True scrolling Rows</cell><cell>36scrolling.html</cell><cell>1</cell><cell>176</cell><cell>177</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>90</cell><cell>Sortable Rows</cell><cell>36sortrows.html</cell><cell>1</cell><cell>178</cell><cell>179</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>91</cell><cell>Drag and Drop Rows</cell><cell>36draganddrop.html</cell><cell>1</cell><cell>180</cell><cell>181</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>92</cell><cell>Resizing Grid</cell><cell>36resize.html</cell><cell>1</cell><cell>182</cell><cell>183</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>93</cell><cell>New in version 3.7</cell><cell></cell><cell>0</cell><cell>185</cell><cell>200</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>95</cell><cell>Load array data at once</cell><cell>37array.html</cell><cell>1</cell><cell>186</cell><cell>187</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>96</cell><cell>Load at once from server</cell><cell>37server.html</cell><cell>1</cell><cell>188</cell><cell>189</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>97</cell><cell>Single search</cell><cell>37single.html</cell><cell>1</cell><cell>190</cell><cell>191</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>98</cell><cell>Multiple search</cell><cell>37multiple.html</cell><cell>1</cell><cell>192</cell><cell>193</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>99</cell><cell>Virtual scrolling</cell><cell>37scroll.html</cell><cell>1</cell><cell>194</cell><cell>195</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>100</cell><cell>Tooolbar search</cell><cell>37toolbar.html</cell><cell>1</cell><cell>196</cell><cell>197</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>101</cell><cell>Add/edit/delete on local data</cell><cell>37crud.html</cell><cell>1</cell><cell>198</cell><cell>199</cell><cell>true</cell><cell>true</cell></row>
   
    <row><cell>102</cell><cell>Grouping</cell><cell></cell><cell>0</cell><cell>201</cell><cell>229</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>103</cell><cell>Simple grouping with array data</cell><cell>38array.html</cell><cell>1</cell><cell>202</cell><cell>203</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>104</cell><cell>Hide grouping column</cell><cell>38array2.html</cell><cell>1</cell><cell>204</cell><cell>205</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>105</cell><cell>Grouped header row config</cell><cell>38array3.html</cell><cell>1</cell><cell>206</cell><cell>207</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>106</cell><cell>RTL Support</cell><cell>38array4.html</cell><cell>1</cell><cell>208</cell><cell>209</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>107</cell><cell>Grouping row(s) collapsed</cell><cell>38array5.html</cell><cell>1</cell><cell>210</cell><cell>211</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>108</cell><cell>Summary Footers</cell><cell>38array6.html</cell><cell>1</cell><cell>212</cell><cell>213</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>109</cell><cell>Remote Data (sorted)</cell><cell>38remote1.html</cell><cell>1</cell><cell>214</cell><cell>215</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>110</cell><cell>Remote Data (sorted with grandtotals)</cell><cell>38remote2.html</cell><cell>1</cell><cell>216</cell><cell>217</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>111</cell><cell>Dynamically change grouping</cell><cell>38remote4.html</cell><cell>1</cell><cell>218</cell><cell>219</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>112</cell><cell>View Summary Row on Collapse</cell><cell>38remote5.html</cell><cell>1</cell><cell>220</cell><cell>221</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>113</cell><cell>Multi Group all level sums (new)</cell><cell>44remote1.html</cell><cell>1</cell><cell>222</cell><cell>223</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>114</cell><cell>Multi Group one level sum  (new)</cell><cell>44remote2.html</cell><cell>1</cell><cell>224</cell><cell>225</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>115</cell><cell>Multi Group Show sums on header(new)</cell><cell>44remote3.html</cell><cell>1</cell><cell>226</cell><cell>227</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>119</cell><cell>Functionality</cell><cell></cell><cell>0</cell><cell>230</cell><cell>241</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>120</cell><cell>Data colspan</cell><cell>40colspan.html</cell><cell>1</cell><cell>231</cell><cell>232</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>121</cell><cell>Keyboard navigation</cell><cell>40keyboard.html</cell><cell>1</cell><cell>233</cell><cell>234</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>122</cell><cell>Column model templates</cell><cell>40cmtmpl.html</cell><cell>1</cell><cell>235</cell><cell>236</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>123</cell><cell>Add tree node </cell><cell>40addnode.html</cell><cell>1</cell><cell>237</cell><cell>238</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>124</cell><cell>Formatter actions </cell><cell>40frmactions.html</cell><cell>1</cell><cell>239</cell><cell>240</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>131</cell><cell>Searching</cell><cell></cell><cell>0</cell><cell>260</cell><cell>270</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>132</cell><cell>Complex search </cell><cell>40grpsearch.html</cell><cell>1</cell><cell>261</cell><cell>262</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>133</cell><cell>Show query in search </cell><cell>40grpsearch1.html</cell><cell>1</cell><cell>263</cell><cell>264</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>134</cell><cell>Validation in serach </cell><cell>40grpsearch2.html</cell><cell>1</cell><cell>265</cell><cell>266</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>135</cell><cell>Search Templates </cell><cell>40grpsearch3.html</cell><cell>1</cell><cell>267</cell><cell>268</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>140</cell><cell>Hierarchy</cell><cell></cell><cell>0</cell><cell>280</cell><cell>290</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>141</cell><cell>Custom Icons </cell><cell>40subgrid1.html</cell><cell>1</cell><cell>281</cell><cell>282</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>142</cell><cell>Expand all Rows on load </cell><cell>40subgrid2.html</cell><cell>1</cell><cell>283</cell><cell>284</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>143</cell><cell>Load subgrid data only once</cell><cell>40subgrid3.html</cell><cell>1</cell><cell>285</cell><cell>286</cell><cell>true</cell><cell>true</cell></row>

    <row><cell>150</cell><cell>Frozen Cols.Group Header(new)</cell><cell></cell><cell>0</cell><cell>290</cell><cell>300</cell><cell>false</cell><cell>false</cell></row>
    <row><cell>151</cell><cell>Group Header - no colspan style </cell><cell>43groupnc.html</cell><cell>1</cell><cell>291</cell><cell>292</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>152</cell><cell>Group Header - with colspan style </cell><cell>43groupwc.html</cell><cell>1</cell><cell>293</cell><cell>294</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>153</cell><cell>Frozen column</cell><cell>43frozen1.html</cell><cell>1</cell><cell>295</cell><cell>296</cell><cell>true</cell><cell>true</cell></row>
    <row><cell>156</cell><cell>Frozen column with group header</cell><cell>43frozen2.html</cell><cell>1</cell><cell>297</cell><cell>298</cell><cell>true</cell><cell>true</cell></row>	

</rows>';

		echo $arbol;
	}
}
