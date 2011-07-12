<?php
class Usuarios extends Controller {

	function Usuarios(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('menues');
		$this->datasis->modulo_id(906,1);
	}

	function index(){
		redirect('supervisor/usuarios/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter('Filtro de Usuarios');
		$filter->db->select("a.us_codigo,a.us_nombre,a.supervisor,a.almacen,a.vendedor,a.cajero,
							c.nombre as vendnom,b.ubides as almdes,d.nombre as cajnom");
		$filter->db->from('usuario AS a');
		$filter->db->join('caub AS b','b.ubica=a.almacen'    ,'left');
		$filter->db->join('vend AS c','c.vendedor=a.vendedor','left');
		$filter->db->join('scaj AS d','d.cajero=a.cajero'    ,'left');

		$filter->us_codigo = new inputField('C&oacute;digo Usuario', 'us_codigo');
		$filter->us_codigo->size=15;

		$filter->us_nombre = new inputField('Nombre', 'us_nombre');
		$filter->us_nombre->size=15;

		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('supervisor/usuarios/dataedit/show/<#us_codigo#>','<#us_codigo#>');
		$uri2 = anchor('supervisor/usuarios/cclave/modify/<#us_codigo#>','Cambiar clave');
		$uri3 = anchor('supervisor/usuarios/accesos/<#us_codigo#>','Asignar Accesos');

		$grid = new DataGrid('Lista de Usuarios');
		$grid->order_by('us_codigo','asc');
		$grid->per_page = 10;

		$grid->column_orderby('C&oacute;digo', $uri,'us_codigo');
		$grid->column_orderby('Nombre','us_nombre','us_nombre' );
		$grid->column_orderby('Supervisor'     ,'supervisor','align="center"');
		$grid->column_orderby('Almac&eacute;n' ,'almdes'    ,'align=\'left\'');
		$grid->column_orderby('Vendedor'       ,'<#vendedor#>-<#vendnom#>','vendedor','align=\'center\'');
		$grid->column_orderby('Cajero'         ,'<#cajero#>-<#cajnom#>','cajero'     ,'align=\'center\'');
		$grid->column('Cambio clave'   ,$uri2  ,'align="center"');
		//$grid->column('Asignar Accesos',$uri3       ,'align="center"');

		$grid->add('supervisor/usuarios/dataedit/create','Crear un nuevo usuario');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = heading('Usuarios');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data); 
	}

	function dataedit(){ 
		$this->rapyd->load('dataedit');
		$this->rapyd->uri->keep_persistence();

		$edit = new DataEdit('Usuarios', 'usuario');
		$edit->back_url = site_url('supervisor/usuarios/filteredgrid');

		$edit->pre_process( 'delete','_pre_delete');
		$edit->post_process('delete','_pos_delete');
		$edit->post_process('insert','_pos_insert');
		$edit->post_process('update','_pos_update');

		$edit->us_codigo = new inputField('C&oacute;digo de Usuario', 'us_codigo');
		$edit->us_codigo->rule = 'strtoupper|required';
		$edit->us_codigo->mode = 'autohide';
		$edit->us_codigo->size = 20;
		$edit->us_codigo->maxlength = 15;

		$edit->us_nombre = new inputField('Nombre', 'us_nombre');
		$edit->us_nombre->rule = 'strtoupper|required';
		$edit->us_nombre->size = 45;

		$edit->activo = new dropdownField('Activo', 'activo');
		$edit->activo->rule = 'required';
		$edit->activo->option('S','Si');
		$edit->activo->option('N','No');
		$edit->activo->style='width:80px';

		$edit->almacen = new dropdownField('Almac&eacute;n', 'almacen');
		$edit->almacen->option('','Ninguno');
		$edit->almacen->options("SELECT ubica, CONCAT_WS('-',ubica,ubides) AS descrip FROM caub ORDER BY ubica");

		$edit->vendedor = new dropdownField('Vendedor', 'vendedor');
		$edit->vendedor->option('','Ninguno');
		$edit->vendedor->options("SELECT vendedor, CONCAT(vendedor,'-',nombre) AS nom FROM vend WHERE tipo IN ('V','A') ORDER BY vendedor");

		$edit->cajero = new dropdownField('Cajero', 'cajero');
		$edit->cajero->option('','Todos');
		$edit->cajero->options("SELECT cajero,CONCAT_WS('-',cajero, nombre) AS descri FROM scaj ORDER BY nombre");

		$edit->supervisor = new dropdownField('Es Supervisor', 'supervisor');
		$edit->supervisor->rule = 'required';
		$edit->supervisor->option('N','No');
		$edit->supervisor->option('S','Si');
		$edit->supervisor->style='width:80px';

		$edit->buttons('modify', 'save', 'undo', 'back','delete');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = heading('Usuarios');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data); 
	}

	function accesos($usr){
		$this->rapyd->load('datagrid2');
		$mSQL="SELECT a.modulo,a.titulo, IFNULL(b.acceso,'N') AS acceso,a.panel,MID(a.modulo,1,1) AS pertenece 
			FROM intramenu AS a 
			LEFT JOIN intrasida AS b ON a.modulo=b.modulo AND b.usuario='$usr' 
			WHERE MID(a.modulo,1,1)!=0 ORDER BY MID(a.modulo,1,1), a.panel,a.modulo";
		$select=array('a.modulo','a.titulo', "IFNULL(b.acceso,'N') AS acceso",'a.panel',"MID(a.modulo,1,1) AS pertenece");

		//$grid = new DataGrid2("Accesos del Usuario $usr");
		//$grid->agrupar('Panel: ', 'panel');
		//$grid->use_function('convierte','number_format','str_replace');
		//$grid->db->select($select);
		//$grid->db->from('intramenu AS a');
		//$grid->db->join('intrasida AS b',"a.modulo=b.modulo AND b.usuario='$usr'",'LEFT');
		//$grid->db->where('MID(a.modulo,1,1)!=0');
		//$grid->db->orderby('a.modulo, a.panel');
		////$grid->per_page = 20;
		//$grid->column("Titulo" ,"titulo");
		//$grid->column("Modulo" ,"modulo",'align=left');
		//$grid->column("Acceso" ,"acceso",'align=right');
		//$grid->build();

		$mc = $this->db->query($mSQL);
		$tabla=form_open('accesos/guardar').form_hidden('usuario',$usr).'<div id=\'ContenedoresDeData\'><table width=100% cellspacing="0">';
		$i=0;
		$panel = '';
		foreach( $mc->result() as $row ){
			if(strlen($row->modulo)==1) {
				$tabla .= '<tr><th colspan=2>'.$row->titulo.'</th></tr>';
				$panel = '';
			}

			elseif( strlen($row->modulo)==3 ) {
				if ($panel <> $row->panel ) {
				    $tabla .= '<tr><td colspan=2 bgcolor="#CCDDCC">'.$row->panel.'</td></tr>';
				    $panel = $row->panel ;
				};

				$tabla .= '<tr><td>'.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->modulo,$row->acceso).'</td></tr>';
				$i++;
			}else{
				$tabla .= '<tr><td><b>&nbsp;&nbsp;-&nbsp;</b>'.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->modulo,$row->acceso).'</td></tr>';
				$i++;
			}
		}
		$tabla.='</table></div>';
		$tabla.=form_hidden('usuario',$usr).form_submit('pasa','Guardar').form_close();

		$data['content'] = $tabla;
		$data['title']   = heading('Asignar Accesos');
		$data['head']    = style('estilos.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function cclave(){ 
		$this->rapyd->load('dataedit');
		$this->rapyd->uri->keep_persistence();

		$edit = new DataEdit('Cambio de clave de usuario', 'usuario');
		$edit->back_save   =true;
		$edit->back_cancel =true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/usuarios/filteredgrid');
		$edit->post_process('update','_pos_updatec');

		$edit->us_codigo = new inputField('C&oacute;digo de Usuario','us_codigo');
		$edit->us_codigo->mode = 'autohide';
		$edit->us_codigo->when = array('show');

		$edit->us_clave = new inputField('Clave','us_clave');
		$edit->us_clave->rule = 'required|matches[us_clave1]';
		$edit->us_clave->type = 'password';
		$edit->us_clave->when = array('modify','idle');

		$edit->us_clave1 = new inputField('Confirmar Clave','us_clave1');
		$edit->us_clave1->db_name = 'us_clave';
		$edit->us_clave1->rule    = 'required';
		$edit->us_clave1->type    = 'password';
		$edit->us_clave1->when    = array('modify','idle');

		$edit->us_clave1->size       = $edit->us_clave->size      =12;
		$edit->us_clave1->maxlength  = $edit->us_clave->maxlength =15;

		$edit->buttons('modify', 'save', 'undo', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = heading('Cambio de clave');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data); 
	}

	function _pre_delete($do) {
		$codigo=$do->get('us_codigo');
		if ($codigo==$this->session->userdata('usuario')){
			$do->error_message_ar['pre_del'] = 'No se puede borrar usted mismo';
			return FALSE;
		}
		return TRUE;
	}

	function _pos_delete($do){
		$codigo=$do->get('us_codigo');
		$mSQL="DELETE FROM intrasida WHERE usuario='$codigo'";
		$this->db->query($mSQL);
		logusu('USUARIOS',"BORRADO EL USUARIO $codigo");
		return TRUE;
	}

	function _pos_insert($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"CREADO EL USUARIO $codigo, SUPERVISOR $superv");
		redirect("supervisor/usuarios/cclave/modify/$codigo");
		return TRUE;
	}

	function _pos_update($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"MODIFICADO EL USUARIO $codigo, SUPERVISOR $superv");
		return TRUE;
	}

	function _pos_updatec($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"CAMBIO DE CLAVE DEL USUARIO $codigo");
		return TRUE;
	}

	function soporte(){
		$mSQL="INSERT INTO `usuario` (`us_codigo`, `us_nombre`, `us_clave`,`supervisor`) VALUES ('SOPORTE', 'PERS. DREMANVA', 'DREMANVA','S');";
		$this->db->simple_query($mSQL);
	}

	function instalar(){
		$mSQL="ALTER TABLE `usuario`  ADD COLUMN `almacen` CHAR(4) NULL";
		$this->db->simple_query($mSQL);
		echo "Agregado campo almacen";
	}
}