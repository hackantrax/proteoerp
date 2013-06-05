<?php
class HTMLReporte {
	var $fcount=0;
	var $DBquery;
	var $DBfieldsName;
	var $DBfieldsType;
	var $DBfieldsMax_lengt;
	var $workbook;
	var $worksheet;
	var $fname;
	var $cols;
	var $ccols;
	var $crows;
	var $Titulo;
	var $Acumulador=array();
	var $SubTitulo;
	var $SobreTabla;
	var $tituHeader;
	var $tituSubHeader;
	var $centrar=array();
	var $wstring=array('string','char');
	var $wnumber=array('real','int','decimal');
	var $wdate=array('date');
	var $fc=5;
	var $cc=0;
	var $ii=0;
	var $fi=0;
	var $totalizar=array();
	var $ctotalizar;
	var $grupo=array();
	var $cgrupo;
	//var $cgrupos=array();
	var $dRep=TRUE;
	var $grupoLabel;
	var $colum=0;
	var $rows=array();
	var $fCols=array();

	function HTMLReporte($mSQL=''){
		$this->ccols=0;
		if(!empty($mSQL)){
			$CI = & get_instance();
			$this->DBquery  = $CI->db->query($mSQL);
			$data=$this->DBquery->field_data();
			foreach ($data as $field){
				$this->DBfieldsName[]                 =$field->name;
				$this->DBfieldsType[$field->name]     =$field->type;
				$this->DBfieldsMax_lengt[$field->name]=$field->max_length;
			}
		}
	}
	function tcols(){
		$this->dRep=false;
		foreach ($this->DBfieldsName as $row){
			$this->AddCol($row,20,$row);
		}
		//$this->grupo=$this->grupos;
		//$this->cgrupo=TRUE;
	}

	function AddCol($DBnom,$width=-1,$TInom ,$align='L',$size=''){
		//Add a column to the table
		if (in_array($DBnom, $this->DBfieldsName)){
			if(is_array($TInom)) $TInom=implode(' ',$TInom);
			$this->cols[]=array('titulo'=>$TInom,'campo'=>$DBnom,'align'=>$align);
			$this->centrar[]='';
			$this->ccols++;

			$this->colum++;
		}
	}

	function Header(){
		$this->ii = 6;

		echo '<html>';
		echo '<head>';
		echo '<title>'.htmlspecialchars($this->Titulo).'</title>';
		echo '<style type="text/css">';
		echo "
body{
font-family:Arial, Helvetica, sans-serif;
}

h1{
font-size:2em;
padding:0px;
margin:0px;
}

h2{
font-size:1.2em;
padding:0px;
margin:0px;
}

h3{
font-size:1em;
padding:0px;
}

h4{
font-size:1em;
padding:0px;
}

table{
width:100%;
border-collapse:collapse;
padding:0;
box-shadow:3px 3px 5px #000000;
border-radius:5px;
}

th{
color:#FFFFFF;
background:#000000;
background: -moz-linear-gradient(top,  #000000 0%, #3F21FF 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#000000), color-stop(100%,#3F21FF));
background: -webkit-linear-gradient(top,#000000 0%,#3F21FF 100%);
background: -o-linear-gradient(top,#000000 0%,#3F21FF 100%);
background: -ms-linear-gradient(top,#000000 0%,#3F21FF 100%);
background: linear-gradient(top,#000000 0%,#3F21FF 100%);
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#000000',endColorstr='#3F21FF',GradientType=0s);
border-bottom-style: solid;
border-width:1px;
border-color:#000000;
font-size:18px;
font-weight:100;
padding:5px;
text-align:center;
vertical-align:top;
}

tr{
color:#000000;
font-size:16px;
font-weight:100;
}

tr:hover td{
background:#FFF952;
}

tr:nth-child(odd) td{
background:#BCC0D1;
}

tr:nth-child(odd):hover td{
background:#FFF952;
}

td{
background:#E8E8E8;
padding:2px 5px;
text-align:left;
vertical-align:top;
}

th:first-child{
border-top-left-radius:5px;
}

th:last-child{
border-top-right-radius:5px;
}

tr:last-child td:first-child{
border-bottom-left-radius:5px;
}

tr:last-child td:last-child{
border-bottom-right-radius:5px;
}";

		echo '</style>';
		echo '</head>';
		echo '<body>';

		$ifilas=implode(' ',$this->tituHeader);
		echo '<h1>'.htmlspecialchars($ifilas).'</h1>';

		$ifilas = implode(' ',$this->tituSubHeader);
		echo '<h2>'.$ifilas.'</h2>';

		echo '<h1 style="text-align:center;">'.htmlspecialchars($this->Titulo).'</h1>';
		if(!empty($this->SubTitulo)){
			echo '<h2 style="text-align:center;">'.htmlspecialchars($this->SubTitulo).'</h2>';
		}

		echo '<h3>'.htmlspecialchars($this->SobreTabla).'</h3>';
	}

	function Table() {
		if($this->dRep)
			$this->Header();//Encabezado
		echo '<table>';

		echo '<tr>';
		//------------campos tabla-------------------------------
		$aalign=array();
		foreach($this->cols AS $cl=>$cols){
			echo '<th>'. htmlspecialchars($cols['titulo']).'</th>';

			if(isset($cols['align'])){
				if($cols['align']=='C'){
					$aalign[$cols['campo']]='center';
				}elseif($cols['align']=='R'){
					$aalign[$cols['campo']]='right';
				}else{
					$aalign[$cols['campo']]='left';
				}
			}
		}
		echo '</tr>';

		$this->ii=$this->ii+2;
		//----------fin campos tabla-----------------------------
		//----------inicializa valores-----------------------------
		if($this->ctotalizar){
			foreach($this->cols  as $i=>$fila ){
				$gtotal[$fila['campo']]= 0;
			}
			$rgtotal=$gtotal;
		}
		$cambio=false;
		if($this->cgrupo){
			foreach($this->grupo as $fila){
				if($this->ctotalizar) $stotal[]=$rstotal[]=$gtotal;
				$bache[$fila] =NULL;
			}
		}
		$one=$this->cgrupo;
		//----------fin inicializa valores--------------------------
		//**--inicio data set, recorre fila a fila --------------------------
		foreach( $this->DBquery->result_array() as $row ){
			//----------Se escriben solo en primer instancia los grupos----------------------
			if($one){
				$one=false;
				foreach($this->grupo as $fila)$bache[$fila]=$row[$fila];
				$this->GroupTableHeader($row,1);
			};
			//----------------------------------------------------------------------
			if($this->cgrupo) $cambio=$this->grupoCambio($bache,$row);
			if($cambio){
				foreach($this->grupo as $fila)$bache[$fila]=$row[$fila];
				if ($this->ctotalizar){
					for($u=0;$u<count($this->grupo)-($cambio-1);$u++){//se recorre por grupos
						echo '<tr>';
						foreach($this->cols AS $h=>$cols){//se recorre por columnas
							$campo=$cols['campo'];
							if(in_array($campo,$this->totalizar)){ //se verifica si la columna fue mandada a totalizar
								//----se escribe los totales de grupos----------------------------
								echo '<td style=\'text-align:'.$aalign[$campo].';border-top-style: solid;border-width:1px;border-color:#000000;\'>'.$stotal[$u][$campo].'</td>';
							}else{
								echo '<td>&nbsp;</td>';
							}
						}
						echo '</tr>';
						foreach($this->cols  as $fila){ //se inicializan totale
							$stotal[$u][$fila['campo']] = 0;
						}
						$this->ii++;
					}
				}
				//------se escribe los titulos de grupos----------------------------
				$this->GroupTableHeader($row,$cambio);
				$cambio=false;
			}

			//------se recorre por columnas para calculo de totales y escritura de datos----------------------------
			echo '<tr>';
			foreach($this->cols AS $o=>$cols){
				$campo=$cols['campo'];
				$nf=$row;
				if (preg_match("/^__cC[0-9]+$/", $campo)>0){
					$sal=$this->_parsePattern($this->fCols[$campo]);
					$val=$this->fCols[$campo];
					if (count($sal)>0){
						foreach($sal as $pasa){
							if(!is_numeric($nf[$pasa])) $nf[$pasa]=0;
							$val=str_replace('<#'.$pasa.'#>',$nf[$pasa],$val);
						}
						$col='$val='.$val.';';
						eval($col);
						$row[$campo]=$val;
					}
				}

				if ($this->ctotalizar){
					if (in_array($campo,$this->totalizar)){
						$gtotal[$campo] +=$row[$campo];
						if($this->cgrupo){
							for($u=0;$u<count($this->grupo);$u++){
								$stotal[$u][$campo]+=$row[$campo];
								$rstotal[$u][$campo] =$stotal[$u][$campo];
							}
						}
						$rgtotal[$campo]=$gtotal[$campo];
						//if (in_array($campo, $this->Acumulador)) $row[$campo]=$stotal[$u-1][$campo];
						if (in_array($campo, $this->Acumulador)){
							if($this->cgrupo)
								$row[$campo]=$stotal[0][$campo];
							else
								$row[$campo]=$gtotal[$campo];
						}
					}else{
						$total[$campo]=$gtotal[$campo]=$rtotal[$campo]=$rgtotal[$campo]=' ';
						for($u=0;$u<count($this->grupo);$u++){
					 		$stotal[$u][$campo]=$rstotal[$u][$campo]=' ';
					 	}
					}
				}
				//------se escribe los datos----------------------------
				$l=$this->ii;

				$this->selectWrite($l-1, $o,$row[$campo],$campo,$aalign[$campo]);

				//------se escribe los datos----------------------------
			}
			echo '</tr>';
			$this->ii++;
		}
		//**--fin data set, recorre fila a fila --------------------------

		//--escritura totales finales --------------------------
		if ($this->ctotalizar){
				if ($this->cgrupo){
					for($u=0;$u<count($this->grupo);$u++){

						echo '<tr style="background:#E89300;">';
						foreach($this->cols AS $h=>$cols){
							$campo=$cols['campo'];
							if(in_array($campo,$this->totalizar))
								//--------escritura totales finales--------------
								echo '<td>'.$rstotal[$u][$campo].'</td>';
							else
								echo '<td></td>';
						}
						echo '</tr>';
						foreach($this->cols  as $i=>$fila ){
							$stotal[$u][$fila['campo']] = 0;
						}
						$this->ii++;
					}

				}
			//--------escritura TOTAL FINAL--------------
			echo '<tr>';
			foreach($this->cols AS $h=>$cols){
				$campo=$cols['campo'];
				if(in_array($campo,$this->totalizar)){
					echo '<td>'.$rgtotal[$campo].'</td>';
				}else{
					echo '<td></td>';
				}
			}
			echo '</tr>';
		}
		//--fin escritura totales finales --------------------------
		if($this->dRep){
			$this->Footer();
		}
	}

	function setType($campo,$tipo){//relleno
		$this->DBfieldsType[$campo]=$tipo;
	}

	function setTitulo($tit='Listado',$size='',$font=''){
		$this->Titulo =$tit;
	}

	function setSubTitulo($tit='',$size='',$font=''){
		if(!empty($tit) ) $this->SubTitulo =$tit;
	}

	function setTableTitu($size='',$font=''){

	}

	function setRow($size='',$font=''){

	}

	function setHead($tituHeader='',$size='',$font=''){
	}

	function setSubHead($tituSubHeader='',$size='',$font=''){
	}

	function setHeadValores($param){
		$CI =& get_instance();
		$data= func_get_args();
		foreach($data as $sale)
			$this->tituHeader[]=$CI->datasis->traevalor($sale);
	}

	function setSubHeadValores($param){
		$CI =& get_instance();
		$data= func_get_args();
		foreach($data as $sale)
			$this->tituSubHeader[]=$CI->datasis->traevalor($sale);
	}

	function setAcumulador($param){
		$data= func_get_args();
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName) OR array_key_exists($sale,$this->fCols)){
				$this->Acumulador[]=$sale;
				if (!in_array($sale, $this->totalizar)){
					$this->totalizar[]=$sale;
					$this->ctotalizar=true;
				}
			}
		}
	}

	function setTotalizar($param){
		$data= func_get_args();
		$i=0;
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName) OR array_key_exists($sale,$this->fCols)){
				$this->totalizar[]=$sale;
				$this->ctotalizar=true;
			}
		}
	}

	function setGrupo($param){
		if(is_array($param))
			$data=$param;
		else
			$data= func_get_args();
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName)){
				$this->grupo[]=$sale;
				$this->cgrupo=True;
			}
		}
	}

	function setSobreTabla($SobreTabla,$size=8,$font='Arial'){
		$this->SobreTabla=$SobreTabla;
	}

	function setHeadGrupo($label='',$campo='',$font='',$size='',$type=''){
	}

	function setGrupoLabel($label){
		if(is_array($label))
			$data=$label;
		else
			$data= func_get_args();
		foreach($data as $sale){
			$correcto=true;
			$sal=$this->_parsePattern($sale);
			if (count($sal)>0){
				foreach($sal as $pasa){
					if (!in_array($pasa, $this->DBfieldsName)){
						$correcto=false;
					}
				}
			}else{
				if (!in_array($sale, $this->DBfieldsName)) $correcto=false;
			}
			if($correcto)
				$this->grupoLabel[]=$sale;
			else
				$this->grupoLabel[]=NULL;
		}
	}

	function GroupTableHeader($row,$n=0){
		for($i=$n-1;$i<count($this->grupo);$i++){
			if (!empty($this->grupoLabel[$i])){

				$sal=$this->_parsePattern($this->grupoLabel[$i]);
				if(count($sal)>0){
					$label=$this->grupoLabel[$i];
					foreach($sal as $pasa){
						if($this->DBfieldsType[$pasa]=='date'){
							if(function_exists('dbdate_to_huma')){
								$row[$pasa]=dbdate_to_human($row[$pasa]);
							}
						}
						$label=str_replace('<#'.$pasa.'#>',$row[$pasa],$label);
					}
				}else
					$label=$this->grupoLabel[$i];
			}else{
				$label=$this->grupo[$i].' '.$row[$this->grupo[$i]];
			}

			echo '<tr><td colspan=\''.$this->colum.'\' style="background:#FFFFFF;font-weight:bold;">'.$label.'</td></tr>';
			$this->ii++;
		}
	}

	function Row($data,$linea=0,$pinta=1) {
	}

	function CalcWidths($width,$align) {
	}

	function add_fila($param){
	}

	function AddPage(){
	}

	function Footer(){
		echo '</table>';
		//$this->centrar[0]=$this->Titulo.' :: Sistema ProteoERP';
		echo '</body></html>';
	}

	function Output(){

	}

	function _parsePattern($pattern){
		$template = $pattern;
		$parsedcount = 0;
		$salida=array();
		while (strpos($template,'#>')>0) {
			$parsedcount++;
			$parsedfield = substr($template,strpos($template,"<#")+2,strpos($template,"#>")-strpos($template,"<#")-2);
			$salida[]=$parsedfield;
			$template = str_replace("<#".$parsedfield ."#>","",$template);
		}
		return $salida;
	}

	function selectWrite($f,$c,$campo,$dbcampo,$align){
		if(isset($this->DBfieldsType[$dbcampo])){
			$tipo=$this->DBfieldsType[$dbcampo];
		}else{
			$tipo='string';
		}

		echo '<td style=\'text-align:'.$align.';\'>';
		if(in_array($tipo,$this->wnumber)){
			if(function_exists('nformat')){
				echo nformat($campo);
			}else{
				echo htmlspecialchars($campo);
			}
		}elseif(in_array($tipo,$this->wstring)){
			echo htmlspecialchars(trim($campo));
		}elseif(in_array($tipo,$this->wdate)){
			if(function_exists('dbdate_to_human')){
				$campo=dbdate_to_human(trim($campo));
			}
			echo htmlspecialchars($campo);
		}else{
			echo htmlspecialchars(trim($campo));
		}
		echo '</td>';
	}

	function grupoCambio($bache,$row){
		$i=0;
		foreach($this->grupo as $fila) {
			$i++;
			if ($bache[$fila]!=$row[$fila])
				return $i;
		}
		return false;
	}

	function AddCof($field=-1,$width=-1,$caption='',$align='L', $tipo=''){//$fontsize=11
		if(is_array($caption))
			$caption=implode(' ',$caption);
		//Add a column to the table
		if($field!=-1){
			$correcto=false;
			$sal=$this->_parsePattern($field);

			if (count($sal)>0){
				$correcto=true;
				foreach($sal as $pasa){
					if (!in_array($pasa, $this->DBfieldsName)){
						$correcto=false;
					}
				}
			}
			if ($correcto){
				$nname='__cC'.$this->fcount;
				$this->cols[]=array( 'campo'=>$nname, 'titulo'=>$caption,'tipo'=>$tipo);//,'w'=>$width, 'a'=>$align,'s'=>$fontsize
				$this->rows[]=$nname;
				$this->fCols[$nname]=$field;
				$this->fcount++;
				//$this->setType($nname,'real');
			}
		}
	}
}

class PDFReporte extends HTMLReporte{
	function PDFReporte($mSQL=''){
		$this->HTMLReporte($mSQL);
	}
}
