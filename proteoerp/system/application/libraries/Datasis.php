<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * DataSIS Components
 *
 * @author		Andres Hocevar
 * @version		0.1
 * @filesource

funciones
	//FUNCIONES DE BD
	dameval($mpara,$data=array())
	damerow($mSQL,$data=array())
	damereg($mSQL,$data=array())
	traevalor($nombre,$descrip='')
	ponevalor($nombre, $mvalor)
	prox_sql($mcontador, $pad=0)
	existetabla($tabla)
	istabla($tabla)
	iscampo($tabla,$campo)
	isindice($tabla, $indice)                // SI EXISTE EL INDICE
	agregacol($tabla,$columna,$tipo)         // AGREGA COLUMNA A TABLA
	guardasesion($datos)                     // GUARDA DATOS DE SESION EN MYSQL
	modintramenu( $ancho, $alto, $ejecutar ) // Modifica tamano de ventana Intramenu
	creaintramenu( $opcion = array() )    // Crea opcion en el menu

	//FUNCIONES DE FECHA
	adia()                                // ARREGLO DE DIAS
	ames()                                // ARREGLO DE MESES
	aano()                                // ARREGLO DE ANOS
	calendario($forma,$nombre)
	jscalendario()


	//FUNCIONES DE ACCESO
	essuper()                             // ES SUPERVISOR
	login()                               // MARCA LOGGED EN USERDATA
	puede($id)                            // SI TIENE ACCESO A id
	modulo_id( $modulo, $ventana=0 )      // Identifica el modulo y controla el acceso
	modulo_nombre( $modulo, $ventana=0 )  //
	sidapuede($modulo, $opcion)           //
	puede_ejecuta($nombre)                // si tiene acceso a un modulo por nombre de ejecucion


	consularray($mSQL)                    //Convierte una consulta a un array
	form2uri($clase,$metodo,$parametros)  //
	ivaplica($mfecha=NULL)
	get_uri()
	modbus($modbus,$id='',$width=800,$height=600,$puri='')
	p_modbus($modbus,$puri='',$width=800,$height=600,$id='')
	periodo($mTIPO, $mFECHA )
	nivel()
	formato_cpla()                        // Formato de Contabilidad
	prox_numero($mcontador,$usr=NULL)     // Proximo numero
	prox_imenu($mod='')                   // Proxima opcion de menu
	fprox_numero($mcontador,$long=8)      // Proximo numero
	banprox($codban)                      // Proximo documento bancario

	damesesion($id)
	llenacombo($mSQL)
	llenaopciones($mSQL, $todos=false, $id='' )
	llenajqselect($mSQL, $todos=false )
	actusal($codbanc, $fecha, $monto)     // Actualiza saldo en Bancos

	sinvcarga( $mCODIGO, $mALMA, $mCANTIDAD) // CARGA CANTIDAD ACTUALIZANDO MAESTRO Y DETALLE

	listados($modulo, $tipo = 'E')   // Manda los Reportes disponibles
	otros( $modulo, $tipo = 'E' )    // Manda otras funciones
	menuMod()

	jqdata($mSQL,$data)    //  Convierte un SElect a Data JqGrid

	jqtablawest($nombre, $caption, $colModel,  $mSQL, $alto=200, $ancho=190) // Convierte un SElect a Data JqGrid

	extjsfiltro($filtros, $tabla = '')
	codificautf8($query)
	codificautf81($row){

	extjscampos($tabla)
	extultireg($data)
	jqgcampos($mSQL)

**/

class Datasis {

// FUNCIONES DE BD

	// TRAE EL PRIMER CAMPO DEL PRIMER REGISTRO DE LA CONSULTA
	function dameval($mpara,$data=array()){
		$CI =& get_instance();
		$qq = $CI->db->query($mpara,$data);
		$rr = $qq->row_array();
		$aa = each($rr);
		return $aa[1];
	}
	// TRAE EL REGISTRO COMPLETO EN UN ARREGLO
	function damerow( $mSQL, $data=array() ){
		$CI =& get_instance();
		$query = $CI->db->query($mSQL,$data);
		$row=array();
		if ($query->num_rows() > 0)
			$row = $query->row_array();
		return $row;
	}

	// TRAE UN ARREGLO CON TODOS LOS REGISTROS
	function damereg($mSQL,$data=array()){
		return $this->damerow($mSQL, $data );
	}

	// Trae valor de la table VALORES
	function traevalor($nombre,$descrip=''){
		$CI =& get_instance();
		$dbnombre=$CI->db->escape($nombre);

		$qq = $CI->db->query("SELECT valor,descrip FROM valores WHERE nombre=${dbnombre}");
		if($qq->num_rows() > 0){
			$rr = $qq->row_array();
			$rt = $rr['valor'];
			if(!empty($descrip)){
				if($rr['valor']!=$descrip){
					$dbdescrip = $CI->db->escape($descrip);
					$CI->db->simple_query("UPDATE valores SET descrip=${dbdescrip} WHERE nombre=${dbnombre}");
				}
			}
		}else{
			$dbdescri=$CI->db->escape($descrip);
			$CI->db->simple_query("INSERT INTO valores SET nombre=${dbnombre}, descrip=${dbdescri}");
			$rt = '';
		}

		return $rt;
	}

	// Pone un valor en la tabla Valores
	function ponevalor($nombre, $mvalor){
		$CI =& get_instance();
		$dbnombre=$CI->db->escape($nombre);
		$CI->db->simple_query("REPLACE INTO valores SET nombre=${dbnombre}, valor=".$CI->db->escape($mvalor));
	}

	// DEVUELVE EL SIGUIENTE NUMERO DEL CONTADOR
	function prox_numero($mcontador,$usr=NULL){
		$CI =& get_instance();
		if (empty($usr))
			$usr=$CI->session->userdata('usuario');
		if(!$CI->db->table_exists($mcontador))
			$CI->db->simple_query("CREATE TABLE $mcontador (
			`numero` INT(11) NOT NULL AUTO_INCREMENT,
			`usuario` CHAR(10) NULL DEFAULT NULL,
			`fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`numero`))");
		$CI->db->query("INSERT INTO $mcontador VALUES(null, '$usr', now() )");
		$aa = $CI->db->insert_id();
		return $aa;
	}

	// TRAE EL SIGUIENTE NUMERO FORMATEADO A 8 DIGITOS
	function fprox_numero($mcontador,$long=8){
		$numero=$this->prox_numero($mcontador);
		return str_pad($numero, $long, "0", STR_PAD_LEFT);
	}

	// TRAE EL SIGUIENTE NUMERO DEL CONTADOR FORMATEADO A $pad DIGITOS
	function prox_sql($mcontador, $pad=0){
		$aa = $this->prox_numero($mcontador);
		if ( $pad > 0) $aa = str_pad($aa, $pad, "0", STR_PAD_LEFT);
		return $aa;
	}

	// SI EXISTE UNA TABLA DADA
	function existetabla($tabla){
		$CI =& get_instance();
		return $CI->db->table_exists($tabla);
	}

	// SI EXISTEUNA TABLA DADA
	function istabla($tabla){
		$CI =& get_instance();
		return $CI->db->table_exists($tabla);
	}

	// SI EXISTE UN CAMPO EN UNA TABLA
	function iscampo($tabla,$campo){
		$CI =& get_instance();
		$aa = $this->dameval("SHOW FIELDS FROM $tabla WHERE Field ='$campo'");
		if ($aa==$campo) return true ;
		else return false;
	}

	// SI EXISTE UN INDICE EN UNA TABLA
	function isindice($tabla, $indice){
		$CI =& get_instance();
		$query = $CI->db->query("SHOW INDEX FROM $tabla WHERE Key_name = '$indice'");
		if ($query->num_rows()>0) return true ;
		else return false;
	}

	// AGREGA COLUMNA A UNA TABLA SI NO EXISTE
	function agregacol($tabla,$columna,$tipo){
		$CI =& get_instance();
		$existe  = $CI->db->query("DESCRIBE $tabla $columna");
		if ( $existe->num_rows() == 0  )
			$CI->db->query("ALTER TABLE $tabla ADD COLUMN $columna $tipo");
	}

	// ARREGLO DE DIAS 1...31
	function adia(){
		$dias = array();
		for($i=1;$i<=31;$i++) {
			$ind=str_pad($i, 2, '0', STR_PAD_LEFT);
			$dias[$ind]=$ind;
		}
		return $dias;
	}

	// ARREGLO DE MESES 1...12
	function ames(){
		$mes = array();
		for($i=1;$i<=31;$i++){
			$ind=str_pad($i, 2, '0', STR_PAD_LEFT);
			$mes[$ind]=$ind;
		}
		return $mes;
	}

	// ARREGLO DE ANOS
	function aano(){
		$ano  = array('2004'=>'2004','2005'=>'2005','2006'=>'2006','2007'=>'2007','2008'=>'2008','2009'=>'2009','2010'=>'2010');
		return $ano;
	}


	function login(){
		$CI =& get_instance();
		return $CI->session->userdata('logged_in');
	}

	function essuper(){
		$CI =& get_instance();
		$CI->load->database('default',TRUE);
		if ($CI->session->userdata('logged_in')){
			$usuario = $CI->session->userdata['usuario'];
			$dbusuario= $CI->db->escape($usuario);
			// Prueba si es supervisor
			$existe = $CI->datasis->dameval("SELECT COUNT(*) FROM usuario WHERE us_codigo=$dbusuario AND supervisor='S'");
			if ($existe > 0)
				return  true;
		}
		return false;
	}

	function puede($id){
		$CI =& get_instance();
		$CI->load->database('default',TRUE);
		if ($CI->session->userdata('logged_in')){
			$usuario  = $CI->session->userdata['usuario'];
			$dbusuario= $CI->db->escape($usuario);
			$dbid     = $CI->db->escape($id);
			$existe = $CI->datasis->dameval("SELECT COUNT(*) FROM intrasida WHERE usuario=$dbusuario AND modulo=$dbid"); //Proteo
			if ($existe  > 0 )
				return  true;
		}
		return false;
	}

	// Verifica acceso en tmenus por funcion a ejecutar
	function puede_ejecuta($nombre, $modulo){
		$CI =& get_instance();
		$m = $this->dameval("SELECT codigo FROM tmenus WHERE ejecutar like '".$nombre."%'") ;
		if ( $m > 0 )
			if ($this->dameval("SELECT acceso FROM sida WHERE modulo=$m AND usuario=".$CI->db->escape($CI->session->userdata('usuario'))) == 'S')
				return true;
			else
				return false;
		else
			return false;
	}

	// Coloca un input con calendario
	function calendario($forma,$nombre){
		return "<input type=\"text\" name=\"$nombre\" /><a href=\"#\" onclick=\"return getCalendar(document.$forma.$nombre);\"/><img src='calendar.png' border='0' /></a>";
	}

	// Script con calendario javascript
	function jscalendario(){
		return "<script language=\"Javascript\" src=\"calendar.js\"></script>";
	}

	//Identifica el modulo y controla el acceso
	function modulo_id( $modulo, $ventana=0 ){
		if ($this->essuper()) return true;
		$CI =& get_instance();
		$CI->load->database('default',TRUE);
		$CI->session->set_userdata('last_activity', time());
		if($CI->session->userdata('logged_in')){
			$usr=$CI->session->userdata('usuario');
			//$mSQL   = "SELECT COUNT(*) FROM intrasida WHERE id = '$modulo' AND  usuario='$usr' AND acceso='S'"; //Tortuga
			$mSQL   = "SELECT COUNT(*) FROM intrasida WHERE modulo = '$modulo' AND  usuario='$usr' AND acceso='S'";   //Proteo
			$cursor = $CI->db->query($mSQL);
			$rr    = $cursor->row_array();
			$sal   = each($rr);
			if ($sal[1] > 0)
				return true;
		}
		$CI->session->set_userdata('estaba', $CI->uri->uri_string());
		if($ventana)
			redirect('/bienvenido/ingresarVentana');
		else
			redirect('/bienvenido/ingresar');
	}

	//******************************************************************
	// Identifica el modulo por nombre admite o niega el acceso
	// Si tiene alguna opcion en S en tmenus-sida pemite entrar
	//
	function modulo_nombre( $modulo, $ventana=0 ){
		$CI =& get_instance();
		$CI->load->database( 'default',TRUE );

		if ( empty($modulo)) {
			return false;
		}

		//Arregla las secuencias si estan mal
		$secu = $CI->datasis->dameval("SELECT SUM(secu) FROM tmenus WHERE modulo='MENUINT'");
		if ($secu == 0 ){
			$mSQL  = "UPDATE tmenus SET secu=1 WHERE titulo='Incluye' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=2 WHERE titulo='Modifica' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=3 WHERE titulo='Prox' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=4 WHERE titulo='Ante' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=5 WHERE titulo='Elimina' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=6 WHERE titulo='Busca' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=7 WHERE titulo='Tabla' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=8 WHERE titulo='Lista' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=9 WHERE titulo='Otros' ";
			$CI->db->query($mSQL);
		}

		// Si no existe lo crea
		$mSQL   = "SELECT COUNT(*) FROM tmenus WHERE modulo = '$modulo' ";
		if ( $this->dameval($mSQL) == 0 ) {

			$mSQL  = "DELETE FROM tmenus WHERE modulo='' OR modulo IS NULL ";
			$CI->db->query($mSQL);

			//crea elmodulo en tmenus
			$mSQL  = "INSERT INTO tmenus (modulo, secu, titulo, mensaje, ejecutar, proteo) ";
			$mSQL .= "SELECT '$modulo' modulo, secu, titulo, mensaje, ejecutar, proteo ";
			$mSQL .= "FROM tmenus WHERE modulo='MENUINT'";
			$CI->db->query($mSQL);
			// Crea las entradas en sida
			$mSQL  = "INSERT IGNORE INTO sida ( usuario, modulo, acceso ) ";
			$mSQL .= "SELECT b.us_codigo usuario, a.codigo modulo, 'N' acceso ";
			$mSQL .= "FROM tmenus a JOIN usuario b WHERE modulo='$modulo' ";
			$CI->db->query($mSQL);

		};


		if ($this->essuper()) return true;
		$CI->session->set_userdata('last_activity', time());
		if($CI->session->userdata('logged_in')){
			$usr=$CI->session->userdata('usuario');
			$mSQL   = "SELECT COUNT(*) FROM sida a JOIN tmenus b ON a.modulo=b.codigo WHERE b.modulo = '$modulo' AND  a.usuario='$usr' AND a.acceso='S'";   //Proteo
			$cursor = $CI->db->query($mSQL);
			$rr    = $cursor->row_array();
			$sal   = each($rr);
			if ($sal[1] > 0)
				return true;
		}
		$CI->session->set_userdata('estaba', $CI->uri->uri_string());
		redirect('/bienvenido/noautorizado/'.$modulo);
	}


	/*******************************************************************
	 *
	 *   Integracion con tmenus
	 *
	*/
	function sidapuede($modulo, $opcion){
		if($this->essuper())
			return true;

		//Si Esta Vacio no da opcion
		if(empty($modulo))
			return false;

		if(empty($opcion))
			return false;

		$CI =& get_instance();
		$CI->load->database( 'default',TRUE );
		$CI->session->set_userdata('last_activity', time());

		//Arregla las secuencias si estan mal
		$secu = $CI->datasis->dameval("SELECT SUM(secu) FROM tmenus WHERE modulo='MENUINT'");
		if ($secu == 0 ){
			$mSQL  = "UPDATE tmenus SET secu=1 WHERE titulo='Incluye' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=2 WHERE titulo='Modifica' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=3 WHERE titulo='Prox' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=4 WHERE titulo='Ante' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=5 WHERE titulo='Elimina' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=6 WHERE titulo='Busca' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=7 WHERE titulo='Tabla' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=8 WHERE titulo='Lista' ";
			$CI->db->query($mSQL);
			$mSQL  = "UPDATE tmenus SET secu=9 WHERE titulo='Otros' ";
			$CI->db->query($mSQL);
		}

		if($CI->session->userdata('logged_in')){
			$usuario = $CI->db->escape($CI->session->userdata('usuario'));
			$modulo  = $CI->db->escape( $modulo );
			if(strlen($opcion) > 1){
				if($opcion == 'TODOS' ){
					$mSQL  = "SELECT COUNT(*) ";
					$mSQL .= "FROM sida AS a JOIN tmenus AS b ON b.codigo=a.modulo ";
					$mSQL .= "WHERE a.acceso='S' AND a.usuario=${usuario} AND b.modulo=${modulo}";
					if($CI->datasis->dameval($mSQL) > 0)
						return true;
					else
						return false;
				}else{
					$opcion  = $CI->db->escape( $opcion );
					$mSQL  = "SELECT COUNT(*) ";
					$mSQL .= "FROM sida AS a JOIN tmenus AS b ON b.codigo=a.modulo ";
					$mSQL .= "WHERE a.acceso='S' AND a.usuario=${usuario} AND b.modulo=$modulo ";
					$mSQL .= "AND (TRIM(b.proteo) LIKE ${opcion} OR TRIM(b.ejecutar) LIKE ${opcion})";
					if($CI->datasis->dameval( $mSQL ) > 0)
						return true;
					else
						return false;
				}
			}else{
				$opcion  = $CI->db->escape($opcion);
				$mSQL  = "SELECT COUNT(*) ";
				$mSQL .= "FROM sida AS a JOIN tmenus AS b ON b.codigo=a.modulo ";
				$mSQL .= "WHERE a.acceso='S' AND a.usuario=${usuario} AND b.modulo=${modulo} ";
				$mSQL .= "AND b.secu = ${opcion}";
				if($CI->datasis->dameval($mSQL) > 0)
					return true;
				else
					return false;
			}
		}
	}


	//Convierte una consulta a un array
	function consularray($mSQL){
		$bote = array();
		$ncampo = array();
		$CI =& get_instance();
		$mc = $CI->db->query($mSQL);
		foreach ($mc->list_fields() as $field)
			array_push($ncampo, $field);
		if ($mc->num_rows() > 0){
			foreach( $mc->result_array() as $row )
				$bote[$row[$ncampo[0]]]=$row[$ncampo[1]];
		}
		return $bote;
	}

	function form2uri($clase,$metodo,$parametros){
		$out='';
		if (is_array($parametros)){
			foreach ($parametros as $value) {
			$out .= "+this.form.$value.value+'/'";
			}
		}else
			$out="+this.form.$parametros.value+'/'";
		$out="'".base_url()."$clase/$metodo/'$out";
		return (" location.href=$out;");
	}

	function ivaplica($mfecha=NULL){
		if(empty($mfecha)) $mfecha=date('Ymd');
		$CI =& get_instance();
		$dbmfecha = $CI->db->escape($mfecha);
		$qq = $CI->db->query("SELECT 0 exento, tasa, redutasa, sobretasa FROM civa WHERE fecha < ${dbmfecha} ORDER BY fecha DESC LIMIT 1");
		$rr = $qq->row_array();
		//$aa = each($rr);
		return $rr;
	}

	function get_uri(){
		$CI =& get_instance();
		$arr=array('formatos','reportes');
		if(in_array($CI->router->fetch_class(),$arr))
			$uri=$CI->router->fetch_directory().$CI->router->fetch_class().'/'.$CI->router->fetch_method().'/'.$CI->uri->segment(3);
		else
			$uri=$CI->router->fetch_directory().$CI->router->fetch_class().'/'.$CI->router->fetch_method();
		return $uri;
	}

	function modbus($modbus,$id='',$width=800,$height=600,$puri=''){
		$CI =& get_instance();
		$uri=$this->get_uri();
		//$uri  =$CI->uri->uri_string();
		$tabla=$modbus['tabla'];
		$parametros=serialize($modbus);

		$data=array();
		if (empty($id)) $id=$modbus['tabla'];

		$idt=$this->dameval("SELECT id FROM modbus WHERE idm='$id' AND uri='$uri'");
		if (!empty($idt)){
			$mSQL="UPDATE modbus SET parametros = '$parametros' WHERE idm='$id' AND uri='$uri'";
			$CI->db->query($mSQL);
		}else{
			$CI->db->set('uri', $uri);
			$CI->db->set('idm', $id);
			$CI->db->set('parametros', serialize($modbus));
			$CI->db->insert('modbus');
			$idt=$CI->db->insert_id();
		}

		return("<a href='javascript:void(0);'onclick=\"vent=window.open('".site_url("buscar/index/$idt/$puri")."','ventbuscar$id','width=$width,height=$height,scrollbars=Yes,	status=Yes,resizable=Yes,screenx=5,screeny=5');vent.focus();document.body.setAttribute('onUnload','vent.close();');\">".image('system-search.png',$modbus['titulo'],array('border'=>'0')).'</a>');
	}

	function p_modbus($modbus,$puri='',$width=800,$height=600,$id=''){
		$CI =& get_instance();
		//$uri  =$CI->uri->uri_string();
		$uri=$this->get_uri();
		$tabla=$modbus['tabla'];
		$parametros=serialize($modbus);

		$data=array();
		if (empty($id)) $id=$modbus['tabla'];

		$idt=$this->dameval("SELECT id FROM modbus WHERE idm='$id' AND uri='$uri'");
		if (!empty($idt)){
			$mSQL="UPDATE modbus SET parametros = '$parametros' WHERE idm='$id' AND uri='$uri'";
			$CI->db->query($mSQL);
		}else{
			$CI->db->set('uri', $uri);
			$CI->db->set('idm', $id);
			$CI->db->set('parametros', serialize($modbus));
			$CI->db->insert('modbus');
			$idt=$CI->db->insert_id();
		}
		return(
"<a
	href='javascript:void(0);'
	onclick=\"
		vent=window.open(
			'".site_url("buscar/index/$idt/$puri")."',
			'ventbuscar$id',
			'width=$width,height=$height,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'
		);
		vent.focus();
		document.body.setAttribute(
			'onUnload',
			'if(typeof(vent)==\'object\') vent.close();'
		);

	\"
>".image('system-search.png',$modbus['titulo'],array('border'=>'0')).'</a>');
		//return("<a href='javascript:void(0);' onclick=\"vent=window.open('".site_url("buscar/index/$idt/$puri")."','ventbuscar$id','width=$width,height=$height,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); vent.focus();\">".image('system-search.png',$modbus['titulo'],array('border'=>'0')).'</a>');
	}

	function periodo($mTIPO, $mFECHA ) {
		$perido=array(1 =>$mFECHA);
		$mFECHA=explode('-',$mFECHA);

		switch ($mTIPO) {
			case 'S':
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], $mFECHA[2]-7, $mFECHA[0]));
				break;
			case 'B':
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], $mFECHA[2]-13, $mFECHA[0]));
				break;
			case 'Q':
				if ($mFECHA[1]>15)
					$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], 16, $mFECHA[0]));
				else
					$perido[0]=date("Y-m-d",mktime(0, 0, 0, $mFECHA[1], 1, $mFECHA[0]));
				break;
			case 'M':
				$perido[0]=date("Y-m-d",mktime(0, 0, 0, date("m"), 1, date("Y")));
				break;
			default:
				$perido[0]=$perido[1];
		}
		return $perido;
	}

	//******************************************************************
	//       Total de Niveles en el formato
	//
	function nivel(){
		$formato=$this->dameval('SELECT formato FROM cemp LIMIT 1');
		$formato=explode('.',$formato);
		return count($formato);
	}

	//******************************************************************
	//       Total de caracteres de un nivel
	//
	function lennivel( $n = 1){
		$formato=$this->dameval('SELECT formato FROM cemp LIMIT 1');
		$formato = explode('.',$formato);
		$meco = '';
		if ( $n > count($formato) )
			$n = count($formato);
		$lon = $n;
		if ($n > 1){
			$lon = $lon-1;
			for($i=0; $i < $n ;$i++ ) {
				$lon += strlen($formato[$i]);
				$meco .= $formato[$i].' ';
			}
		} else
			$lon = 1;
		return $lon;
	}


	function formato_cpla(){
		$formato=$this->dameval('SELECT formato FROM cemp LIMIT 0,1');
		$qformato='%';
		for($i=1;$i<substr_count($formato, '.')+1;$i++) $qformato.='.%';
		return $qformato;
	}

	function prox_imenu($mod=''){
		$mSQL  = "SELECT bbb.hexa FROM (";
		$mSQL .= "SELECT max(b.valor+1) siguiente ";
		$mSQL .= "FROM intramenu a JOIN serie b ON a.modulo=b.hexa ";
		if($mod==''){
			$mSQL .= "WHERE length(a.modulo)=1 ) aaa ";
		} else {
			$mSQL .= "WHERE MID(a.modulo,1,1)='$mod' AND length(a.modulo)=3 ) aaa";
		}
		$mSQL .= "JOIN serie bbb WHERE bbb.valor=aaa.siguiente ";

		$return = $this->dameval($mSQL);
	}

	//******************************************************************
	// Proximo Numero de documento en el banco
	//
	function banprox($codban){
		$CI =& get_instance();
		$dbcodban=$CI->db->escape($codban);
		$tipo=$this->dameval("SELECT tbanco FROM banc WHERE codbanc=$dbcodban");
		if($tipo != 'CAJ'){
			$nom='nBAN'.$codban;
			while(true){
				$numero=$this->fprox_numero($nom,12);
				$dbnumero=$CI->db->escape($numero);
				$mSQL = "SELECT COUNT(*) AS n FROM bmov WHERE numero=$dbnumero";
				$query= $CI->db->query($mSQL);
				$row  = $query->first_row('array');
				if($row['n']==0) break;
			}
			return $numero;
		} else {
			$mSQL  = "UPDATE banc SET proxch=LPAD(proxch+1,12,'0')  WHERE codbanc='$codban'";
			$CI->db->simple_query($mSQL);
			$numero = $CI->datasis->dameval("SELECT proxch FROM banc WHERE codbanc='$codban'");
			while(true){
				$mSQL  = "UPDATE banc SET proxch=LPAD(proxch+1,12,'0') WHERE codbanc='$codban'";
				if ( $CI->datasis->dameval("SELECT COUNT(*) FROM bmov WHERE codbanc='$codban' AND numero='$numero'") == 0){
					break;
				}
				$mSQL  = "UPDATE banc SET proxch=LPAD(proxch+1,12,'0')  WHERE codbanc='$codban'";
				$CI->db->simple_query($mSQL);
				$numero = $CI->datasis->dameval("SELECT proxch FROM banc WHERE codbanc='$codban'");
			}
			return $numero;
		}
		return false;
	}

	// GUARDA DATOS DE SESION EN MYSQL
	function guardasesion($datos){
		$CI =& get_instance();

		$mSQL = "CREATE TABLE IF NOT EXISTS data_sesion (
			id INT(11) NULL AUTO_INCREMENT,
			sesionid VARCHAR(40) NULL,
			data1 TEXT NULL, data2 TEXT NULL, data3 TEXT NULL, data4 TEXT NULL,
			fecha TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id), UNIQUE INDEX sesion (sesionid)
			)DEFAULT CHARSET 'latin1' ENGINE=MyISAM ";
		$CI->db->simple_query($mSQL);

		$id = $CI->session->userdata('session_id');

		$mSQL = $CI->db->insert_string("data_sesion", array("sesionid"=>$id));
		$CI->db->simple_query($mSQL);

		$mSQL = $CI->db->update_string('data_sesion', $datos, "sesionid='$id'");
		$CI->db->simple_query($mSQL);
		return $this->dameval("SELECT id FROM data_sesion WHERE sesionid='$id'");
	}

	// GUARDA DATOS DE SESION EN MYSQL
	function damesesion($id = 0){
		$CI =& get_instance();
		$mSQL = "CREATE TABLE IF NOT EXISTS data_sesion (
			id INT(11) NULL AUTO_INCREMENT,
			sesionid VARCHAR(40) NULL,
			data1 TEXT NULL, data2 TEXT NULL, data3 TEXT NULL, data4 TEXT NULL,
			fecha TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id), UNIQUE INDEX sesion (sesionid)
			)DEFAULT CHARSET 'latin1' ENGINE=MyISAM ";

		$CI->db->simple_query($mSQL);

		if ( $id == 0)
			$id = $CI->session->userdata('session_id');

		$mSQL = "SELECT data1, data2, data3, data4 FROM data_sesion WHERE sesionid='$id'";
		$query = $CI->db->query($mSQL);
		return $query->row_array();
	}

	function llenacombo($mSQL){
		$CI =& get_instance();
		$query = $CI->db->query($mSQL);
		$coma = '';
		$opciones = '';
		if ($query->num_rows() > 0){
			$colu = array();
			foreach( $query->list_fields() as $campo ) {
				$colu[] = $campo;
			}
			foreach ($query->result_array() as $row){
				$opciones .= $coma."['".trim($row[$colu[0]])."','".trim($row[$colu[1]])."']";
				$coma = ', ';
			}
		}
		$query->free_result();
		return $opciones;
	}

	function llenaopciones($mSQL, $todos=false, $id='' ){
		$CI =& get_instance();
		$query = $CI->db->query($mSQL);
		$opciones = '';
		$colu = array();
		$select = '<select>';
		if ( !empty($id)) $select = '<select id="'.$id.'" name="'.$id.'">';
		foreach( $query->list_fields() as $campo ) {
			$colu[] = $campo;
		}
		if ($query->num_rows() > 0){
			foreach ($query->result_array() as $row){
				$opciones .= "<option value=\"". htmlentities($row[$colu[0]])."\">". htmlentities (utf8_encode(trim($row[$colu[1]])))."</option>";
			}
		}
		$query->free_result();
		if ( $todos ){
			return $select.'<option value="-">Seleccione</option>'.$opciones.'</select>';
		} else {
			return $select.$opciones.'</select>';
		}
	}

	function llenajqselect($mSQL, $todos=false ){
		$CI =& get_instance();
		$query = $CI->db->query($mSQL);
		$colu = array();
		if ( $todos )
			$arreglo = '{ "":"SELECCIONE", ';
		else
			$arreglo = '{ ';
		foreach( $query->list_fields() as $campo ) {
			$colu[] = $campo;
		}
		if ($query->num_rows() > 0){
			foreach ($query->result_array() as $row){
				$arreglo .= "\"".$row[$colu[0]]."\":\"".utf8_encode(trim($row[$colu[1]]))."\", ";
			}
		}
		$query->free_result();
		$arreglo .= " }";
		return $arreglo;
	}

	function controladores(){
		$CI =& get_instance();
		$CI->load->helper('directory');
		$map = directory_map('./system/application/controllers/', FALSE);
		return $map;

	}

	function actusal($codbanc, $fecha, $monto){
		$CI =& get_instance();

		$fecha = str_replace('-','',$fecha);
		$fecha = str_replace('/','',$fecha);
		$dbcodbanc = $CI->db->escape($codbanc);

		// Actualiza el saldo
		$mSQL = "UPDATE banc SET saldo=saldo+$monto WHERE codbanc=${dbcodbanc}";
		$CI->db->simple_query($mSQL);

		// SI NO EXISTE LO CREA
		$mSQL = "SELECT COUNT(*) FROM bsal WHERE codbanc=${dbcodbanc} AND ano=".substr($fecha,0,4);
		if ( $this->dameval($mSQL) == 0 ) {
			$mSQL = "INSERT INTO bcaj SET codbanc=${dbcodbanc} AND ano=".substr($fecha,0,4);
			$CI->db->simple_query($mSQL);

			//SALDO INICIAL
			$mSQL   = "SELECT saldo+saldo01+ saldo02+ saldo03+ saldo04+ saldo05+ saldo06+saldo07+ saldo08+ saldo09+ saldo10+ saldo11+ saldo12 ";
			$mSQL  .= "FROM bsal WHERE codbanc=${dbcodbanc} ORDER BY ano DESC";
			$mSALDO = $this->dameval($mSQL);
		}
		$nomsal = 'saldo'.substr($fecha,6,2);
		$mSQL   = "UPDATE bsal SET $nomsal=$nomsal+$monto WHERE codbanc=${dbcodbanc} AND ano=".substr($fecha,0,4);
		$CI->db->simple_query($mSQL);
		//$sql='CALL sp_actusal('.$CI->db->escape($banco).",'$fecha',$monto)";
	}



	//****************************************************
	//
	//  CARGA CANTIDAD ACTUALIZANDO MAESTRO Y DETALLE
	//
	//****************************************************
	function sinvcarga( $mCODIGO, $mALMA, $mCANTIDAD){
		$CI =& get_instance();
		if (empty($mALMA)) $mALMA = $this->traevalor('ALMACEN');
		if (empty($mALMA)) $mALMA = $this->dameval("SELECT ubica FROM caub WHERE gasto='N' ORDER BY ubica");
		$dbmALMA=$CI->db->escape($mALMA);
		$mGASTO  = $this->dameval("SELECT gasto FROM caub WHERE ubica=${dbmALMA}",1);
		if ($mGASTO == 'S') {
			$mSQL = "DELETE  FROM itsinv WHERE alma=${dbmALMA}";
			$CI->db->simple_query($mSQL);
			return;
		};

		$codigoesc = $CI->db->escape($mCODIGO);

		// VERIFICA SI EL ARTICULO ES SERVICIO
		$mSQL = "SELECT SUBSTRING(tipo,1,1) tipo, enlace, fracci, derivado FROM sinv WHERE codigo=".$codigoesc;
		$query     = $CI->db->query($mSQL);
		if($query->num_rows() <= 0) return;
		$mREG      = $query->row_array();
		$mTIPO     = $mREG['tipo'];
		$mENLACE   = $mREG['enlace'];
		$mFRACCI   = $mREG['fracci'];
		$mDERIVADO = $mREG['derivado'];

		// SERVICIO NO DESCUENTA
		if($mTIPO == 'S' ) return;

		$mSQL = "UPDATE sinv SET existen=existen+$mCANTIDAD WHERE codigo=".$codigoesc;
		$CI->db->simple_query($mSQL);

		// REVISA SI EXISTE EN ITSINV
		$mHAY = $this->dameval("SELECT COUNT(*) FROM itsinv WHERE codigo=$codigoesc AND alma=$dbmALMA");
		if ( $mHAY == 0 ){
			$mSQL = $CI->db->query("INSERT INTO itsinv SET codigo=$codigoesc, alma=$dbmALMA, existen=0");
			$CI->db->simple_query($mSQL);
		}

		// ACTUALIZA ITSINV
		$mSQL = "UPDATE itsinv SET existen=existen+$mCANTIDAD WHERE codigo=$codigoesc AND alma=$dbmALMA";
		$CI->db->simple_query($mSQL);
		//echo $mSQL;

		// VERIFICA SI ES MENOR QUE 0
		if ( $mTIPO == 'F' and !empty($mENLACE) and $mCANTIDAD < 0 ){
			// SI EXISTE EL ENLACE
			if ($this->dameval("SELECT COUNT(*) FROM sinv WHERE codigo='$mENLACE'") == 1) {
				$mSQL = "SELECT existen FROM itsinv WHERE codigo=".$codigoesc." AND alma='$mALMA'";
				$mEXISTEN = $this->dameval($mSQL);
				// SI ES MENOR QUE 0 CALCULA LA NECESIDAD
				if ( $mEXISTEN < 0 ){
					// Cuantas necesita?
					if ( $mFRACCI > 0 ){
						$mNECE = round(abs($mEXISTEN)/$mFRACCI,0);
						if ( $mNECE*$mFRACCI < abs( $mEXISTEN) ) $mNECE += 1;
					} else {
						$mNECE = round(abs($mEXISTEN)*abs($mFRACCI),0);
					}

					// SUMA AL DETALLE
					$mSQL = "UPDATE itsinv SET existen=existen+$mCANTIDAD WHERE codigo=$codigoesc AND alma=$dbmALMA";
					//CMNJ(STR(mNECE)+STR(mEXISTEN)+STR(mFRACCI))
					if ( $mFRACCI > 0 ){
						$descu = $mNECE*$mFRACCI;
						$mSQL = "UPDATE itsinv SET existen=existen+$descu WHERE codigo=$codigoesc AND alma=$dbmALMA";
						$CI->db->simple_query($mSQL);
					} else {
						$descu = $mNECE/abs($mFRACCI);
						$mSQL = "UPDATE itsinv SET existen=existen+$descu WHERE codigo=$codigoesc AND alma=$dbmALMA";
						$CI->db->simple_query($mSQL);
					}

					// DESCUENTA DEL MAYOR
					$mSQL = "UPDATE itsinv SET existen=existen-$mNECE WHERE codigo=$codigoesc AND alma=$dbmALMA";
					$CI->db->simple_query($mSQL);

					// FALTA ACTUALIZAR LOS MAESTROS
					if ( $mFRACCI > 0 ){
						$descu = $mNECE*$mFRACCI;
						$mSQL = "UPDATE sinv SET existen=existen+$descu WHERE codigo=$codigoesc";
						$CI->db->simple_query($mSQL);
					} else {
						$descu = $mNECE/ABS($mFRACCI);
						$mSQL = "UPDATE sinv SET existen=existen+$descu WHERE codigo=".$codigoesc;
						$CI->db->simple_query($mSQL);
					}
					$mSQL = "UPDATE sinv SET existen=existen+$mNECE WHERE codigo=$mENLACE";
					$CI->db->simple_query($mSQL);

					// GUARDA EL MOVIMIENTO
					if ( $mFRACCI > 0 ) {
						$descu = $mNECE*$mFRACCI;
						$mSQL = "INSERT INTO trafrac SET id=0, fecha=now(), codigo=$codigoesc, enlace='$mENLACE', cantidad=$mNECE, fraccion=$descu, alma=$dbmALMA";
						$CI->db->simple_query($mSQL);
					} else {
						$descu = $mNECE/abs($mFRACCI);
						$mSQL = "INSERT INTO trafrac SET id=0, fecha=now(), codigo=$codigoesc, enlace='$mENLACE', cantidad=$mNECE, fraccion=$descu, alma=$dbmALMA";
						$CI->db->simple_query($mSQL);
					}
				}
			}
		}
	}

	//*******************************
	//
	//      Manda los Reportes
	//
	//*******************************
	function listados($modulo, $tipo = 'E'){
		$CI         =& get_instance();
		$usuario    =  $CI->session->userdata('usuario');
		$reposcript =  '';

		if ( !$this->sidapuede($modulo,'LISTADO%') ) return '';

		$mSQL="UPDATE tmenus SET ejecutar=REPLACE(ejecutar,"."'".'( "'."','".'("'."') WHERE modulo LIKE '%LIS'";
		$CI->db->simple_query($mSQL);

		$mSQL="UPDATE tmenus SET ejecutar=REPLACE(ejecutar,"."'".'" )'."','".'")'."') WHERE modulo LIKE '%LIS'";
		$CI->db->simple_query($mSQL);

		$listados = '';

		if($modulo) {
			$modulo=strtoupper($modulo);

			$CI->db->_escape_char='';
			$CI->db->_protect_identifiers=false;

			if ( $this->essuper() ) {
				$CI->db->select("a.secu, a.titulo, a.mensaje, REPLACE(MID(a.ejecutar,10,30),"."'".'")'."','')  nombre");
				$CI->db->from("tmenus a" );
				$CI->db->join("reportes  d","REPLACE(MID(a.ejecutar,10,30),"."'".'")'."','')=d.nombre");
				$CI->db->like("a.ejecutar","REPOSQL", "after");
				$CI->db->where('a.modulo',$modulo."LIS");
				$CI->db->orderby("a.secu");
			} else {
				$CI->db->select("a.secu, a.titulo, a.mensaje, REPLACE(MID(a.ejecutar,10,30),"."'".'")'."','')  nombre");
				$CI->db->from("tmenus    a" );
				$CI->db->join("sida      b","a.codigo=b.modulo");
				$CI->db->join("reportes  d","REPLACE(MID(a.ejecutar,10,30),"."'".'")'."','')=d.nombre");
				$CI->db->where('b.acceso','S');
				$CI->db->where('b.usuario',$CI->session->userdata('usuario') );
				$CI->db->like("a.ejecutar","REPOSQL", "after");
				$CI->db->where('a.modulo',$modulo."LIS");
				$CI->db->orderby("a.secu");
			}

			$i = 0;
			$query = $CI->db->get();
			if ( $tipo=='E') { // EXTJ SENCHA
				if ($query->num_rows() > 0) {
					foreach ($query->result_array() as $row)
					{
						$listados .= "\t\t{ id:'".$row['secu']."', titulo:'".$row['titulo']."', nombre:'".$row['nombre']."' },\n";
						$i = $row['secu'];
					}
				} else {
					$listados .= "";
				}

				$query->free_result();
				$CI->db->_protect_identifiers=false;
				$CI->db->select("a.titulo, a.mensaje, a.nombre");
				$CI->db->from("intrarepo a" );
				$CI->db->join("tmenus    b","CONCAT(a.modulo,'LIS')=b.modulo AND b.ejecutar LIKE CONCAT('%',a.nombre,'%') ","left");
				$CI->db->where("b.codigo IS NULL");
				$CI->db->where("a.modulo",$modulo );
				$CI->db->where("a.activo","S");
				$CI->db->orderby("a.titulo");
				$query = $CI->db->get();

				if ($query->num_rows() > 0) {
					foreach ($query->result_array() as $row)
					{
						$i++;
						$listados .= "{ id:'".$i."', titulo:'".$row['titulo']."', nombre:'".$row['nombre']."' },";
					}
				}
				$query->free_result();

				$reposcript = "
	var storeListado = Ext.create('Ext.data.ArrayStore', {autoDestroy: true,storeId: 'listadoStore',idIndex: 0,fields: [ 'numero', 'nombre', 'reporte' ],	data: [".$listados."]});
	function renderRepo(value, p, record) {var mreto='';if ( record.data.numero == '-' ){ mreto = '<div style=\'background-color:#BCEFBC;text-weight:bold;align:center;\'>{0}</div>';} else { mreto = '<a href=\'javascript:void(0);\' onclick=\"window.open(\''+urlApp+'reportes/ver/{1}\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx='+mxs+',screeny='+mys+'\');\" heigth=\"600\">{0}</a>';}return Ext.String.format(mreto,value,record.data.reporte);}
	var gridListado = Ext.create('Ext.grid.Panel', {title: 'Listados',store: storeListado,width: '199',columns: [{ header: 'Nro.',   dataIndex: 'numero', width:  30 },{ header: 'Nombre de los Reportes', dataIndex: 'nombre', width: 169, renderer: renderRepo },{ header: 'Rep.',   dataIndex: 'reporte', hidden:  true }]});
";
			} else {  //JQGRID
				if ($query->num_rows() > 0) {
					foreach ($query->result_array() as $row)
					{
						$listados .= "\t\t{ id:'".$row['secu']."', titulo:'".$row['titulo']."', nombre:'".$row['nombre']."' },\n";
						$i = $row['secu'];
					}
				} else {
					$listados .= "";
				}

				$query->free_result();
				$CI->db->_protect_identifiers=false;
				$CI->db->select("a.titulo, a.mensaje, a.nombre");
				$CI->db->from("intrarepo a" );
				$CI->db->join("tmenus    b","CONCAT(a.modulo,'LIS')=b.modulo AND b.ejecutar LIKE CONCAT('%',a.nombre,'%') ","left");
				$CI->db->where("b.codigo IS NULL");
				$CI->db->where("a.modulo",$modulo );
				$CI->db->where("a.activo","S");
				$CI->db->orderby("a.titulo");
				$query = $CI->db->get();

				if ($query->num_rows() > 0) {
					foreach ($query->result_array() as $row)
					{
						$i++;
						$listados .= "\t\t{ id:'".$i."', titulo:'".$row['titulo']."', nombre:'".$row['nombre']."' },\n";
					}
				}
				if ( !empty($listados)) {
					$reposcript = "var datalis = [\n".$listados."\n\t];";
				} else {
					$reposcript = "";
				}
				$query->free_result();
			}
		}
		return $reposcript;

	}


	//*******************************
	//
	//      Manda Otras Funciones
	//
	//*******************************
	function otros( $modulo, $tipo = 'E' ){
		$CI =& get_instance();
		$usuario    =  $CI->session->userdata('usuario');

		if ( !$this->sidapuede($modulo,'OTROS%') ) return '';

		if ( ! $this->iscampo('tmenus','proteo') ) {
			$CI->db->simple_query('ALTER TABLE tmenus ADD COLUMN proteo TEXT NULL');
		}

		$mSQL="UPDATE tmenus SET ejecutar=REPLACE(ejecutar,"."'".'( "'."','".'("'."') WHERE modulo LIKE '%OTR'";
		$CI->db->simple_query($mSQL);

		$mSQL="UPDATE tmenus SET ejecutar=REPLACE(ejecutar,"."'".'" )'."','".'")'."') WHERE modulo LIKE '%OTR'";
		$CI->db->simple_query($mSQL);

		$Otros = '';
		$Otros1 = '';

		if($modulo){
			$modulo=strtoupper($modulo);
			$CI->db->_escape_char='';
			$CI->db->_protect_identifiers=false;

			if ( $this->essuper() ) {
				$mSQL  = "SELECT a.secu, a.titulo, a.mensaje, a.proteo ";
				$mSQL .= "FROM tmenus a ";
				$mSQL .= "WHERE a.modulo='".$modulo."OTR' AND CHAR_LENGTH(a.proteo)>1 ORDER BY a.secu";
			} else {
				$mSQL  = "SELECT a.secu, a.titulo, a.mensaje, a.proteo ";
				$mSQL .= "FROM tmenus a JOIN sida b ON a.codigo=b.modulo ";
				$mSQL .= "WHERE b.acceso='S' AND b.usuario='".$CI->session->userdata('usuario')."' ";
				$mSQL .= "AND a.modulo='".$modulo."OTR' AND CHAR_LENGTH(a.proteo)>1 ORDER BY a.secu";
			}
			$query = $CI->db->query($mSQL);

			if ( $tipo == 'JQ' ) {
				if ($query->num_rows() > 0) {
					foreach ($query->result_array() as $row)
					{
						$Otros .= "\t\t{ id:'".$row['secu']."', titulo:'".trim($row['titulo'])."', proteo: '".trim($row['proteo'])."'},\n";
					}
					$Otros1 = "var dataotr = [\n".$Otros."\t];";
				} else {
					$Otros .= "\t\t{ id:'0',titulo:'No tiene Funciones',proteo:'' }\n";
					$Otros1 = "";
				}
				$query->free_result();

			} else { // extj
				$Otros1 = '<table>';
				if ($query->num_rows() > 0) {
					foreach ($query->result_array() as $row)
					{
						$Otros .= "[ '".$row['secu']."', '".trim($row['titulo'])."', '".trim($row['proteo'])."' ],";
						if ( $row['proteo'] != 'N/A'){
							$Otros1 .= "<tr><td>";
							if ( empty($row['proteo'])) {
								$Otros1 .= trim($row['titulo']);
							} else {
								$Otros1 .= trim($row['proteo']);
							}
							$Otros1 .="</td></tr>";
						}
					}
				} else {
					$Otros .= "{ id:'0', titulo:'No tiene Funciones', proteo:'' }";
					$Otros1 .= "<tr><td>No hay Opciones</td></tr>";
				}
				$query->free_result();
				$Otros1 .= "</table>";
			}
		}
		return $Otros1;
	}

	//****************************************************
	// Genera un jqgrid Completo
	//
	function jqgridsimplegene($tabla, $contro, $directo, $id){
		$CI =& get_instance();
		$query = $CI->db->query("DESCRIBE $tabla");
		$i = 0;
		if ($query->num_rows() > 0){
			$str  = 'jQuery("#'.$id.'").jqGrid({ '."\n";

			$str .= '	url:\''.$directo.'/'.$contro.'/'.$id.'/g\','."\n";
			$str .= '	ajaxGridOptions: { type: "POST"}, '."\n";
			$str .= '	jsonReader: { root: "data", repeatitems: false}, '."\n";
			$str .= '	datatype: "json", '."\n";
			$str .= '	hiddengrid: true,'."\n";
			$str .= '	width: 190,'."\n";
			$str .= '	height: 100,'."\n";
			$str .= '	colNames:[';
			$tieneid = false;
			$cols = '		{name:\'id\',index:\'id\', width:10}, '."\n";
			$str .= "'id'";
			$long = 40;
			foreach ($query->result() as $row){
				if ( $row->Field == 'id')
					$tieneid = false;
				else {
					$str .= ", '".$row->Field."'";
					//Calcula la Longitud
					if ( $row->Type == 'date' or $row->Type == 'timestamp' ) {
						$long = '70'."\n";
					} elseif ( substr($row->Type,0,7) == 'decimal' or substr($row->Type,0,3) == 'int'  ) {
						$long = '70'."\n";
					} elseif ( substr($row->Type,0,7) == 'varchar' or substr($row->Type,0,4) == 'char'  ) {
						$long = str_replace(array('varchar(','char(',')'),"", $row->Type)*7;
						if ( $long > 200 ) $long = 200;
						if ( $long < 40 ) $long = 40;
					} elseif ( $row->Type == 'text' ) {
						$long = 250;
					}
					//Llena las Columnas
					$cols .= '		{name:\''.$row->Field.'\',index:\''.$row->Field.'\', width:'.$long.', editable:true},'."\n";
				}
			}
			$str .= '],'."\n";
			$str .= '	colModel:['."\n";
			$str .= $cols;
			$str .= '	],'."\n";

			$str .= '	rowNum:10,'."\n";
			$str .= '	rowList:[10,20,30],'."\n";
			$str .= '	pager: \'#p'.$id.'\', '."\n";

			$str .= '	sortname: \'id\', '."\n";
			$str .= '	viewrecords: true, '."\n";
			$str .= '	sortorder: "desc", '."\n";
			$str .= '	editurl: \''.$directo.'/'.$contro.'/'.$id.'/s\','."\n";
			$str .= '	caption: "Using navigator" '."\n";
			$str .= '}); '."\n";

			$str .= 'jQuery("#'.$id.'").jqGrid(\'navGrid\',"#p'.$id.'",{edit:false,add:false,del:false}); '."\n";
			$str .= 'jQuery("#'.$id.'").jqGrid(\'inlineNav\',"#p'.$id.'");'."\n";

			return $str;
		}
	}


	//*******************************
	//
	// Modulos del Menu de DataSIS
	//
	//*******************************
	function menuMod(){
		$CI =& get_instance();
		$mSQL = "SELECT (`a`.`codigo` + 10000) AS `id`, concat(substr(`a`.`modulo`,1,4), replace(replace(substr(`a`.`modulo`,5,16),'OTR',''),'LIS',''))  `modulo`, -(1) AS `secu`, (select `b`.`mensaje` from `tmenus` `b` where ((`b`.`modulo` regexp '^[1-9][0-9]*$') and (`b`.`ejecutar` like concat('%',substr(`a`.`modulo`,1,4),replace(replace(substr(`a`.`modulo`,5,16),'OTR',''),'LIS',''),'%'))) limit 1) AS `nombre` from `tmenus` `a` where ((`a`.`modulo` <> 'MENUINT') and (not((`a`.`modulo` regexp '^[1-9][0-9]*$')))) group by concat(substr(`a`.`modulo`,1,4),replace(replace(substr(`a`.`modulo`,5,16),'OTR',''),'LIS',''))  order by `modulo`,`secu`";
		$query = $CI->db->query($mSQL);
		$Salida  = '';
		$Salida .= "\t\t{ id:'10000', modulo:'MENUDTS', nombre:'MENUS PRINCIPAL DATASIS' },\n";
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row)
			{
				if (trim($row['modulo']) == 'TMENUS' )
					$Salida .= "\t\t{ id:'".$row['id']."', modulo:'".trim($row['modulo'])."', nombre:'OPCIONES DEL MENUS' },\n";
				elseif (trim($row['modulo']) == 'USERS' )
					$Salida .= "\t\t{ id:'".$row['id']."', modulo:'".trim($row['modulo'])."', nombre:'USUARIOS DEL SISTEMA' },\n";
				else
					$Salida .= "\t\t{ id:'".$row['id']."', modulo:'".trim($row['modulo'])."', nombre:'".trim($row['nombre'])."' },\n";
			}
			$Salida = "var datamenu = [\n".$Salida."\t];";
		}
		$query->free_result();
		return $Salida;
	}


	//******************************************************************
	//  Convierte un Select a Data JqGrid
	//  mSQL : SQL SELECT
	//  data : Variable para colocar local data
	//
	function jqdata($mSQL,$data) {
		$CI =& get_instance();

		$colNames = '';
		$colModel = '';
		$Salida   = '';
		$Tempo    = '';

		$CI->db->_escape_char='';
		$CI->db->_protect_identifiers=false;

		$query = $CI->db->query($mSQL);

		if ($query->num_rows() > 0) {
			$i = 0;
			foreach ($query->result_array() as $row)
			{
				$titulos = array_keys($row);
				$valores = array_values($row);
				if ( empty($colNames) ) {
					$colNames = "colNames: ['id','".implode("','",$titulos)."'],";
				}
				$Tempo .= "\t\t{ id:'".$i."'";
				$m = 0;
				foreach($titulos as $tt) {

					$Tempo .= ", $tt:'".$valores[$m]."'";
					$m++;
				}
				$Tempo .= " },\n";
				$i++;
			}
			$Salida = "var $data = [\n".$Tempo."\t];";
		} else {
			$Salida = "";
		}
		$query->free_result();
		return array('data' => $Salida, 'colNames' => $colNames, 'i' => $i+2 ) ;
	}


	//******************************************************************
	//      Convierte un Select a Data JqGrid
	//
	function jqtablawest($nombre, $caption, $colModel,  $mSQL, $alto=200, $ancho=190) {

		//colNames:[\'\',\'Reporte\',\'Nombre\'],

		$columnas = $this->jqdata($mSQL,$nombre."dat");

		//'.$columnas['colNames'].'
		$Salida = '
	jQuery("#'.$nombre.'").jqGrid({
		datatype: "local",
		height: \''.$alto.'\',
		colModel:[{name:\'id\',index:\'id\', hidden:true},'.$colModel.'],
		multiselect: false,
		shrinkToFit: false,
		hiddengrid: true,
		width: '.$ancho.',
		rowNum:'.$columnas['i'].',
		caption: "'.$caption.'",
	});
	'.$columnas['data'].'
	for(var i=0;i<='.$nombre."dat".'.length;i++) jQuery("#'.$nombre.'").jqGrid(\'addRowData\',i+1,'.$nombre.'dat[i]);
	';
		return $Salida;
	}

/*
	function extjsfiltro($filtros, $tabla = ''){
		if ( !empty($tabla)) $tabla = trim($tabla).".";
		$where = "";
		//Buscar posicion 0 Cero
		$filter = json_decode($filtros, true);
		if (is_array($filter)) {
			$where = "";
			//Dummy Where.
			$qs = "";
			for ($i=0;$i<count($filter);$i++){
				switch($filter[$i]['type']){
				case 'string' : $qs .= " AND  $tabla".$filter[$i]['field']." LIKE '%".$filter[$i]['value']."%'";
					Break;
				case 'list' :
					if (strstr($filter[$i]['value'],',')){
						$fi = explode(',',$filter[$i]['value']);
						for ($q=0;$q<count($fi);$q++){
							$fi[$q] = "'".$fi[$q]."'";
						}
						$filter[$i]['value'] = implode(',',$fi);
							$qs .= " AND  $tabla".$filter[$i]['field']." IN (".$filter[$i]['value'].")";
					}else{
						$qs .= " AND $tabla".$filter[$i]['field']." = '".$filter[$i]['value']."'";
					}
					Break;
				case 'boolean' : $qs .= " AND $tabla".$filter[$i]['field']." = ".($filter[$i]['value']);
					Break;
				case 'numeric' :
					switch ($filter[$i]['comparison']) {
						case 'ne' : $qs .= " AND $tabla".$filter[$i]['field']." != ".$filter[$i]['value'];
							Break;
						case 'eq' : $qs .= " AND $tabla".$filter[$i]['field']." = ".$filter[$i]['value'];
							Break;
						case 'lt' : $qs .= " AND $tabla".$filter[$i]['field']." < ".$filter[$i]['value'];
							Break;
						case 'gt' : $qs .= " AND $tabla".$filter[$i]['field']." > ".$filter[$i]['value'];
							Break;
					}
					Break;
				case 'date' :
					switch ($filter[$i]['comparison']) {
						case 'ne' : $qs .= " AND $tabla".$filter[$i]['field']." != '".date('Y-m-d',strtotime($filter[$i]['value']))."'";
							Break;
						case 'eq' : $qs .= " AND $tabla".$filter[$i]['field']." = '".date('Y-m-d',strtotime($filter[$i]['value']))."'";
							Break;
						case 'lt' : $qs .= " AND $tabla".$filter[$i]['field']." < '".date('Y-m-d',strtotime($filter[$i]['value']))."'";
							Break;
						case 'gt' : $qs .= " AND $tabla".$filter[$i]['field']." > '".date('Y-m-d',strtotime($filter[$i]['value']))."'";
							Break;
					}
					Break;
				}
			}
			$where .= $qs;
		}
		return LTRIM(substr($where,4,1000));
	}
*/
	function codificautf8($query){
		$CI =& get_instance();
		if ( $CI->db->char_set == 'utf8' ) {
			$arr = array();
			foreach ( $query as $row )
			{
				$meco = array();
				foreach( $row as $idd=>$campo ) {
					$meco[$idd] = $campo;
				}
				$arr[] = $meco;
			}
		} else {
			$arr = array();
			foreach ( $query as $row)
			{
				$meco = array();
				foreach( $row as $idd=>$campo ) {
					$meco[$idd] = utf8_encode($campo);
				}
				$arr[] = $meco;
			}
		}
		return $arr;
	}

	function codificautf81($row){
			$meco = array();
			foreach( $row as $idd=>$campo ) {
				$meco[$idd] = utf8_encode($campo);
			}
		return $meco;
	}
/*
	function extjscampos($tabla){
		$CI =& get_instance();
		$query = $CI->db->query("DESCRIBE $tabla");
		$i = 0;
		$campos = '';
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if ( $i == 0 ) {
					$campos = "'".$row->Field."'";
					$i = 1;
				} else {
					$campos .= ",'".$row->Field."'";
				}
			}
		}
		return $campos;
	}

	function extultireg($data){
		if ( array_key_exists( '0', $data['data']) ) {
			$campos = $data['data'][count($data)-1];
		} else {
			$campos = $data['data'];
		}
		return $campos;
	}
*/
	//******************************************************************
	//
	//
	function jqgcampos($mSQL){
		$CI =& get_instance();
		$query = $CI->db->query($mSQL);
		$i = 0;
		$campos = '';
		if ($query->num_rows() > 0){
			foreach ($query->result_array() as $row){
				if ( $i == 0 ) {
					$campos = implode(':',$row);
					$i = 1;
				} else {
					$campos .= ";".implode(':',$row);
				}
			}
		}
		return utf8_encode($campos);
	}

	//**************************************************
	// Modifica Intramenu
	//
	function modintramenu( $ancho, $alto, $ejecutar, $modulo = '', $nombre = '' ){
		$CI =& get_instance();
		$mSQL = 'UPDATE intramenu SET ancho='.$ancho.', alto='.$alto.' WHERE ejecutar="'.$ejecutar.'" OR ejecutar="/'.$ejecutar.'"';
		$CI->db->query($mSQL);
		if ( $modulo != '' ){
			$tablas = $CI->db->list_tables();
			if(!in_array('modulos',$tablas))
			$CI->db->query("CREATE TABLE modulos (modulo varchar(20) NULL ,nombre varchar(50) NULL, PRIMARY KEY (modulo) ) ENGINE=MyISAM DEFAULT CHARSET=latin1");
			$mSQL = 'REPLACE INTO modulos SET modulo='.$CI->db->escape($modulo).', nombre='.$CI->db->escape($nombre);
			$CI->db->query($mSQL);

		}
	}


	//**************************************************
	// Inserta Intramenu
	//
	// creaintramenu( $data = array('modulo'=>'148','titulo'=>'Punto de Ventas','mensaje'=>'Punto de Ventas','panel'=>'TRANSACCIONES','ejecutar'=>'ventas/pos','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>800,'alto'=>600)
	function creaintramenu( $data = array() ) {
		$CI =& get_instance();
		if ( !empty($data) ){
			if ( $this->dameval('SELECT COUNT(*) FROM intramenu WHERE modulo='.$CI->db->escape($data['modulo'])) == 0 )
				$CI->db->insert('intramenu', $data);
		}
	}


	//**************************************************
	// Lee Intramenu
	//
	function getintramenu( $ejecutar ){
		$CI =& get_instance();
		$mSQL = 'SELECT ancho, alto FROM intramenu WHERE ejecutar="'.$ejecutar.'" OR ejecutar="/'.$ejecutar.'"';
		$query = $CI->db->query($mSQL);
		if ( $query->num_rows() > 0 ){
			$row = $query->row();
			return array( $row->ancho, $row->alto );
		} else
			return array();
	}



	//**************************************************
	// Pop up Ventana de javascript
	//
	function jwinopen($url, $ancho=800, $alto=600){
		return  'window.open(\''.$url.', \'_blank\', \'width='.$ancho.',height='.$alto.',scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-'.($ancho/2).'), screeny=((screen.availWidth/2)-'.($alto/2).')\')';
	}

	//**************************************************
	// Modifica Intramenu
	//
	function sinvrecalcular( $mTIPO = 'P', $mcodigo='' ){
		$CI =& get_instance();

		if ( $mTIPO == 'P' ){

			$mSQL = "
			UPDATE sinv SET
				precio1=ROUND(pond*(100+iva)/(100-margen1),2),
				precio2=ROUND(pond*(100+iva)/(100-margen2),2),
				precio3=ROUND(pond*(100+iva)/(100-margen3),2),
				precio4=ROUND(pond*(100+iva)/(100-margen4),2),
				base1=ROUND(pond*100/(100-margen1),2),
				base2=ROUND(pond*100/(100-margen2),2),
				base3=ROUND(pond*100/(100-margen3),2),
				base4=ROUND(pond*100/(100-margen4),2)
			WHERE formcal='P' ";
			
			if ( $mcodigo != '' ) $mSQL .= " AND codigo = ".$mcodigo." " ;
			$CI->db->query($mSQL);


			$mSQL = "
			UPDATE sinv SET
				precio1=ROUND(ultimo*(100+iva)/(100-margen1),2),
				precio2=ROUND(ultimo*(100+iva)/(100-margen2),2),
				precio3=ROUND(ultimo*(100+iva)/(100-margen3),2),
				precio4=ROUND(ultimo*(100+iva)/(100-margen4),2),
				base1=ROUND(ultimo*100/(100-margen1),2),
				base2=ROUND(ultimo*100/(100-margen2),2),
				base3=ROUND(ultimo*100/(100-margen3),2),
				base4=ROUND(ultimo*100/(100-margen4),2)
			WHERE formcal='U' ";
			if ( $mcodigo != '' ) $mSQL .= " AND codigo = ".$mcodigo." " ;
			$CI->db->query($mSQL);


			$mSQL = "
			UPDATE sinv SET
				precio1=ROUND(GREATEST(ultimo,pond)*(100+iva)/(100-margen1),2),
				precio2=ROUND(GREATEST(ultimo,pond)*(100+iva)/(100-margen2),2),
				precio3=ROUND(GREATEST(ultimo,pond)*(100+iva)/(100-margen3),2),
				precio4=ROUND(GREATEST(ultimo,pond)*(100+iva)/(100-margen4),2),
				base1=ROUND(GREATEST(ultimo,pond)*100/(100-margen1),2),
				base2=ROUND(GREATEST(ultimo,pond)*100/(100-margen2),2),
				base3=ROUND(GREATEST(ultimo,pond)*100/(100-margen3),2),
				base4=ROUND(GREATEST(ultimo,pond)*100/(100-margen4),2)
			WHERE formcal='M' ";
			if ( $mcodigo != '' ) $mSQL .= " AND codigo = ".$mcodigo." " ;
			$CI->db->query($mSQL);

			$mSQL = "
			UPDATE sinv SET
				precio1=ROUND(standard*(100+iva)/(100-margen1),2),
				precio2=ROUND(standard*(100+iva)/(100-margen2),2),
				precio3=ROUND(standard*(100+iva)/(100-margen3),2),
				precio4=ROUND(standard*(100+iva)/(100-margen4),2),
				base1=ROUND(standard*100/(100-margen1),2),
				base2=ROUND(standard*100/(100-margen2),2),
				base3=ROUND(standard*100/(100-margen3),2),
				base4=ROUND(standard*100/(100-margen4),2)
			WHERE formcal='S' ";
			if ( $mcodigo != '' ) $mSQL .= " AND codigo = ".$mcodigo." " ;
			$CI->db->query($mSQL);

		} else if ($mTIPO == 'M') {

			$mSQL = "
			UPDATE sinv SET
				margen1 = 100-ROUND(pond*100/base1,2),
				margen2 = 100-ROUND(pond*100/base2,2),
				margen3 = 100-ROUND(pond*100/base3,2),
				margen4 = 100-ROUND(pond*100/base4,2)
			WHERE formcal = 'P' ";
			if ( $mcodigo != '' ) $mSQL .= " AND codigo = ".$mcodigo." " ;
			$CI->db->query($mSQL);

			$mSQL = "
			UPDATE sinv SET
				margen1=100-ROUND(ultimo*100/base1,2),
				margen2=100-ROUND(ultimo*100/base2,2),
				margen3=100-ROUND(ultimo*100/base3,2),
				margen4=100-ROUND(ultimo*100/base4,2)
			WHERE formcal='U' ";
			if ( $mcodigo != '' ) $mSQL .= " AND codigo = ".$mcodigo." " ;
			$CI->db->query($mSQL);

			$mSQL = "
			UPDATE sinv SET
				margen1=100-ROUND(GREATEST(ultimo,pond)*100/base1,2),
				margen2=100-ROUND(GREATEST(ultimo,pond)*100/base2,2),
				margen3=100-ROUND(GREATEST(ultimo,pond)*100/base3,2),
				margen4=100-ROUND(GREATEST(ultimo,pond)*100/base4,2)
			WHERE formcal='M' ";
			if ( $mcodigo != '' ) $mSQL .= " AND codigo = ".$mcodigo." " ;
			$CI->db->query($mSQL);

			$mSQL = "
			UPDATE sinv SET
				margen1 = 100-ROUND(standard*100/base1,2),
				margen2=100-ROUND(standard*100/base2,2),
				margen3=100-ROUND(standard*100/base3,2),
				margen4=100-ROUND(standard*100/base4,2)
			WHERE formcal='S' ";
			if ( $mcodigo != '' ) $mSQL .= " AND codigo = ".$mcodigo." " ;
			$CI->db->query($mSQL);

		}

	}

	//******************************************************************
	// Redondear Precios en SINV
	//
	function sinvredondear(){
		$CI =& get_instance();

		$mSQL = "
		UPDATE sinv SET
			precio1=TRUNCATE(precio1/100,0)*100 +IF(MOD(precio1,100)>70,100,IF(MOD(precio1,100)>30,50,0)),
			precio2=TRUNCATE(precio2/100,0)*100 +IF(MOD(precio2,100)>70,100,IF(MOD(precio2,100)>30,50,0)),
			precio3=TRUNCATE(precio3/100,0)*100 +IF(MOD(precio3,100)>70,100,IF(MOD(precio3,100)>30,50,0)),
			precio4=TRUNCATE(precio4/100,0)*100 +IF(MOD(precio4,100)>70,100,IF(MOD(precio4,100)>30,50,0))
		WHERE redecen='C' ;";
		$CI->db->simple_query($mSQL);

		$mSQL = "
		UPDATE sinv SET
			precio1=TRUNCATE(precio1/10,0)*10 +IF(MOD(precio1,10)>7,10,IF(MOD(precio1,10)>3,5,0)),
			precio2=TRUNCATE(precio2/10,0)*10 +IF(MOD(precio2,10)>7,10,IF(MOD(precio2,10)>3,5,0)),
			precio3=TRUNCATE(precio3/10,0)*10 +IF(MOD(precio3,10)>7,10,IF(MOD(precio3,10)>3,5,0)),
			precio4=TRUNCATE(precio4/10,0)*10 +IF(MOD(precio4,10)>7,10,IF(MOD(precio4,10)>3,5,0))
		WHERE redecen='D' ;";
		$CI->db->simple_query($mSQL);

		$mSQL = "
		UPDATE sinv SET
			precio1=ROUND(precio1,0),
			precio2=ROUND(precio2,0),
			precio3=ROUND(precio3,0),
			precio4=ROUND(precio4,0)
		WHERE redecen='F' ;";
		$CI->db->simple_query($mSQL);

		$mSQL = "
		UPDATE sinv SET
			precio1=ROUND(precio1,1),
			precio2=ROUND(precio2,1),
			precio3=ROUND(precio3,1),
			precio4=ROUND(precio4,1)
		WHERE redecen='M' ;";
		$CI->db->simple_query($mSQL);

		$mSQL = "
		UPDATE sinv SET
			base1=ROUND(precio1*100/(100+iva),2),
			base2=ROUND(precio2*100/(100+iva),2),
			base3=ROUND(precio3*100/(100+iva),2),
			base4=ROUND(precio4*100/(100+iva),2) ;";
		$CI->db->simple_query($mSQL);

		$mSQL = "
		UPDATE sinv SET
			margen1=100-ROUND(pond*100/base1,2),
			margen2=100-ROUND(pond*100/base2,2),
			margen3=100-ROUND(pond*100/base3,2),
			margen4=100-ROUND(pond*100/base4,2)
		WHERE formcal='P' ;";
		$CI->db->simple_query($mSQL);

		$mSQL = "
		UPDATE sinv SET
			margen1=100-ROUND(ultimo*100/base1,2),
			margen2=100-ROUND(ultimo*100/base2,2),
			margen3=100-ROUND(ultimo*100/base3,2),
			margen4=100-ROUND(ultimo*100/base4,2)
		WHERE formcal='U' ;";
		$CI->db->simple_query($mSQL);

		$mSQL = "
		UPDATE sinv SET
			margen1=100-ROUND(GREATEST(ultimo,pond)*100/base1,2),
			margen2=100-ROUND(GREATEST(ultimo,pond)*100/base2,2),
			margen3=100-ROUND(GREATEST(ultimo,pond)*100/base3,2),
			margen4=100-ROUND(GREATEST(ultimo,pond)*100/base4,2)
		WHERE formcal='M' ;";
		$CI->db->simple_query($mSQL);

		$mSQL = "
		UPDATE sinv SET
			margen1=100-ROUND(standard*100/base1,2),
			margen2=100-ROUND(standard*100/base2,2),
			margen3=100-ROUND(standard*100/base3,2),
			margen4=100-ROUND(standard*100/base4,2)
		WHERE formcal='S' ;";
		$CI->db->simple_query($mSQL);

	}


	//******************************************************************
	// Script para revisar RIF y CI
	//
	function validarif( $funcion='chrif'){
		$mandale = '
			function chrif(rif){
				rif.toUpperCase();
				var patt=/[EJPGV][0-9]{10} */g;

				if(patt.test(rif)){
					var factor = new Array(4,3,2,7,6,5,4,3,2);
					var v = 0;
					if( rif[0] == "V"){
						v=1;
					}else if(rif[0]=="E"){
						v=2;
					}else if(rif[0]=="J"){
						v=3;
					}else if(rif[0]=="P"){
						v=4;
					}else if(rif[0]=="G"){
						v=5;
					}
					acum=v*factor[0];
					for(i=1;i<9;i++){
						acum = acum+parseInt(rif[i])*factor[i];
					}
					acum=11-acum%11;
					if(acum >= 10 || acum <= 0){
						acum = 0;
					}
					return (acum==parseInt(rif[9]));
				}else{
					return true;
				}
			}
		';

		return $mandale;
	}


	//******************************************************************
	// Unidad Tributaria
	//
	function utri(){
		$valor = $this->dameval("SELECT valor FROM utributa ORDER BY fecha DESC LIMIT 1");
		return $valor;
	}

}
