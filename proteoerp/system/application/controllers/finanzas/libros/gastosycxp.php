<?php
class gastosycxp{

	function genegastos($mes){
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		//Procesando Compras gser
		$this->db->simple_query("UPDATE gser SET cajachi='N' WHERE cajachi='' or cajachi IS NULL");
		$this->db->simple_query("DELETE FROM siva WHERE EXTRACT(YEAR_MONTH FROM fechal) = $mes AND fuente='GS' ");

		// REVISA GSER A VER SI HAY PROBLEMAS
		$this->db->simple_query("UPDATE gser SET exento=totbruto WHERE exento<>totbruto and totiva=0");

		if($this->db->field_exists('serie', 'gser')){
			$iserie =',serie';
			$serie =',a.serie';
		}else{
			$iserie =$serie='';
		}

		$fciva=$this->datasis->dameval("SELECT MAX(fecha) FROM civa");
		$fciva = str_replace('-', '', $fciva);
		// Procesando Gastos
		$mSQL = "INSERT INTO siva
			(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
			referen, planilla, clipro, nombre, contribu, rif, registro,
			nacional, exento, general, geneimpu,
			adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
			gtotal, reiva, fechal, fafecta $iserie)
			SELECT 0 AS id,
			'C' AS libro,
			a.tipo_doc AS tipo,
			'GS' AS fuente,
			'00' AS sucursal,
			a.ffactura,
			a.numero,
			' ' AS numhasta,
			' ' AS caja,
			a.nfiscal,
			'  ' AS nhfiscal,
			IF(a.tipo_doc='ND', a.afecta,'  ') AS referen,
			'  ' AS planilla,
			a.proveed AS clipro,
			a.nombre,
			'CO' AS contribu,
			c.rif,
			IF(a.ffactura >= $fciva  ,'01','05') AS registro,
			'S' AS nacional,
			a.exento  AS exento,
			a.montasa AS general,   a.tasa      AS geneimpu,
			a.monadic AS adicional, a.sobretasa  AS adicimpu,
			a.monredu AS reducida,  a.reducida AS reduimpu,
			a.totpre   AS stotal,
			a.totiva   AS impuesto,
			a.totbruto AS gtotal,
			a.reteiva  AS reiva,
			".$mes."01 AS fechal,
			a.fafecta AS fafecta
			$serie
			FROM gser AS a
			LEFT JOIN sprv AS c ON a.proveed=c.proveed
			WHERE a.fecha BETWEEN $fdesde AND $fhasta
			AND a.cajachi='N' AND tipo_doc<>'XX'
			AND (c.tipo NOT IN ('5') OR a.totiva<>0 )
			ORDER BY a.fecha, a.proveed, a.numero ";
		$flag=$this->db->simple_query($mSQL);
		if(!$flag) memowrite($mSQL,'genegastos');

		// GASTOS DE  CAJACHICA
		$mATASAS = $this->datasis->ivaplica($mes.'02');
		$tolerancia=0.03;
		$mSQL = "INSERT INTO siva
			(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
			referen, planilla, clipro, nombre, contribu, rif, registro,
			nacional, exento, general, geneimpu,
			adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
			gtotal, reiva, fechal, fafecta)
			SELECT 0 AS id,
			'C' AS libro,
			'FC' AS tipo,
			'GS' AS fuente,
			'00' AS sucursal,
			a.fechafac,
			a.numfac,
			' ' AS numhasta,
			' ' AS caja,
			a.nfiscal,
			'  ' AS nhfiscal,
			'  ' AS referen,
			'  ' AS planilla,
			'  ' AS clipro,
			a.proveedor,
			'CO' AS contribu,
			a.rif,
			IF(a.iva=0,'01',IF((abs((a.iva*100/a.precio)-".$mATASAS['tasa'].")<".$mATASAS['tasa']*$tolerancia.") or (abs((a.iva*100/a.precio)-".$mATASAS['sobretasa'].")<".$mATASAS['sobretasa']*$tolerancia.") or (abs((a.iva*100/a.precio)-".$mATASAS['redutasa'].")<".$mATASAS['redutasa']*$tolerancia."),'01','05')) AS registro,
			'S' AS nacional,
			IF(a.iva=0,1,0)*a.importe AS exento,
			IF(abs((a.iva*100/a.precio)-".$mATASAS['tasa']     .")<".$mATASAS['tasa']*$tolerancia     .",1,0)*a.precio AS general,
			IF(abs((a.iva*100/a.precio)-".$mATASAS['tasa']     .")<".$mATASAS['tasa']*$tolerancia     .",1,0)*a.iva    AS geneimpu,
			IF(abs((a.iva*100/a.precio)-".$mATASAS['sobretasa'].")<".$mATASAS['sobretasa']*$tolerancia.",1,0)*a.precio AS adicional,
			IF(abs((a.iva*100/a.precio)-".$mATASAS['sobretasa'].")<".$mATASAS['sobretasa']*$tolerancia.",1,0)*a.iva    AS adicimpu,
			IF(abs((a.iva*100/a.precio)-".$mATASAS['redutasa'] .")<".$mATASAS['redutasa']*$tolerancia .",1,0)*a.precio AS reducida,
			IF(abs((a.iva*100/a.precio)-".$mATASAS['redutasa'] .")<".$mATASAS['redutasa']*$tolerancia .",1,0)*a.iva    AS reduimpu,
			a.precio AS stotal,
			a.iva AS impuesto,
			a.importe AS gtotal,
			0 AS reiva,
			".$mes."01 AS fechal,
			0 AS fafecta
			FROM gitser AS a JOIN gser AS b ON
			a.fecha=b.fecha AND a.proveed=b.proveed AND a.numero=b.numero
			WHERE b.fecha BETWEEN $fdesde AND $fhasta
			AND b.tipo_doc='FC' AND b.cajachi='S'
			ORDER BY a.fecha";
		$flag=$this->db->simple_query($mSQL);
		if(!$flag) memowrite($mSQL,'genegastoscchi');

		$mSQL = "UPDATE siva SET gtotal=exento+general+geneimpu+adicional+reduimpu+reducida+adicimpu
			WHERE fuente='GS' AND libro='C' AND registro!='05'";
		$this->db->simple_query($mSQL);
	}

	function genecxp($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		//Procesando Compras scst
		$this->db->simple_query("DELETE FROM siva WHERE EXTRACT(YEAR_MONTH FROM fechal) = $mes AND fuente='MP' ");
		$this->db->simple_query("UPDATE sprv SET nomfis=nombre WHERE nomfis='' OR nomfis IS NULL ");
		$mFECHAF = $this->datasis->dameval("SELECT max(fecha) FROM civa WHERE fecha<=$mes"."01");
		$mFECHAF = preg_replace('/[^0-9]+/','', $mFECHAF);

		$mSQL = "SELECT a.cod_prv, a.tipo_doc, a.numero,a.nfiscal,a.transac,a.montasa,a.tasa,a.monredu,a.reducida,a.monadic,
		a.sobretasa, a.exento, a.impuesto, a.monto, a.reteiva, a.fecha, a.fecapl, b.rif, b.nomfis,
		GROUP_CONCAT(TRIM(c.numero)) AS afecta,a.codigo
		FROM sprm AS a
		LEFT JOIN sprv AS b ON a.cod_prv=b.proveed
		JOIN itppro  AS c ON a.numero=c.numppro AND a.tipo_doc=c.tipoppro AND c.cod_prv=a.cod_prv
		JOIN scst AS d ON c.tipo_doc=d.tipo_doc AND c.numero=d.numero
		WHERE a.fecha BETWEEN $fdesde AND $fhasta AND b.tipo<>'5'
		AND a.tipo_doc='NC' AND  a.codigo NOT IN ('NOCON','')
		GROUP BY cod_prv,tipo_doc,numero";
		$query = $this->db->query($mSQL);

		if ( $query->num_rows() > 0 ){
			foreach( $query->result() as $row ) {
				if($row->impuesto == 0 && empty($row->codigo) ) continue;
				$referen = $this->datasis->dameval("SELECT numero FROM itppro WHERE transac=".$row->transac." LIMIT 1") ;
				$fafecta = $this->datasis->dameval("SELECT fecha  FROM itppro WHERE transac=".$row->transac." LIMIT 1") ;
				$stotal = $row->monto-$row->impuesto;
				$fecha  = ($row->fecapl==null) ? $row->fecha : $row->fecapl;
				$fecha  = preg_replace('/[^0-9]+/', '',$fecha);

				$data=array();
				$data['libro']    = 'C';
				$data['tipo']     = $row->tipo_doc;
				$data['fuente']   = 'MP';
				$data['sucursal'] = '00';
				$data['fecha']    = $fecha;
				$data['numero']   = $row->numero;
				$data['clipro']   = $row->cod_prv;
				$data['nombre']   = $row->nomfis;
				$data['contribu'] = 'CO';
				$data['rif']      = $row->rif;
				$data['registro'] = ($fecha<$mFECHAF)? '04':'01';
				$data['nacional'] =  'S';
				$data['nfiscal']  = $row->nfiscal;
				$data['general']  = (empty($row->montasa  ))? 0: $row->montasa  ;
				$data['geneimpu'] = (empty($row->tasa     ))? 0: $row->tasa     ;
				$data['reducida'] = (empty($row->monredu  ))? 0: $row->monredu  ;
				$data['reduimpu'] = (empty($row->reducida ))? 0: $row->reducida ;
				$data['adicional']= (empty($row->monadic  ))? 0: $row->monadic  ;
				$data['adicimpu'] = (empty($row->sobretasa))? 0: $row->sobretasa;
				$data['exento']   = (empty($row->exento   ))? 0: $row->exento   ;
				$data['impuesto'] = (empty($row->impuesto ))? 0: $row->impuesto ;
				$data['gtotal']   = (empty($row->monto    ))? 0: $row->monto    ;
				$data['stotal']   = (empty($stotal        ))? 0: $stotal        ;
				$data['fechal']   = $mes.'01';
				$data['referen']  = $referen;
				$data['reiva']    = $row->reteiva;
				$data['afecta']   = $row->afecta;
				$data['fafecta']  = $fafecta;

				$mSQL = $this->db->insert_string('siva', $data);
				$flag=$this->db->simple_query($mSQL);
				if(!$flag) memowrite($mSQL,'genecxp');
			}
		}

		//Carga los descuentos por pronto pago
		$mSQL="SELECT a.cod_prv, a.tipo_doc, a.numero,a.nfiscal,a.transac,
			ROUND(IF(d.cstotal  >0, d.cgenera*(c.ppago/c.monto),0),2) AS montasa,
			ROUND(IF(d.montoiva >0, d.civagen*(c.ppago/c.monto),0),2) AS tasa,
			ROUND(IF(d.cstotal  >0, d.creduci*(c.ppago/c.monto),0),2) AS monredu,
			ROUND(IF(d.montoiva >0, d.civared*(c.ppago/c.monto),0),2) AS reducida,
			ROUND(IF(d.cstotal  >0, d.cadicio*(c.ppago/c.monto),0),2) AS monadic,
			ROUND(IF(d.montoiva >0, d.civaadi*(c.ppago/c.monto),0),2) AS sobretasa,
			ROUND(IF(d.cstotal  >0, d.cexento*(c.ppago/c.monto),0),2) AS exento,
			ROUND(IF(d.cimpuesto>0, d.cimpuesto*(c.ppago/c.monto),0),2) AS impuesto,
			c.ppago AS monto,
			c.reteiva, a.fecha, a.fecapl, b.rif, b.nomfis ,TRIM(c.numero) AS afecta,a.codigo
			FROM sprm AS a
			LEFT JOIN sprv AS b ON a.cod_prv=b.proveed
			JOIN itppro AS c ON a.transac=c.transac
			JOIN scst AS d ON c.tipo_doc=d.tipo_doc AND c.numero=d.numero
			WHERE a.fecha BETWEEN $fdesde AND $fhasta AND b.tipo<>'5' AND a.tipo_doc='NC' AND a.codigo='DESPP' AND c.ppago>0";
		$query = $this->db->query($mSQL);

		if ( $query->num_rows() > 0 ){
			foreach( $query->result() as $row ) {
				if($row->impuesto == 0 && empty($row->codigo) ) continue;
				$stotal = $row->monto-$row->impuesto;
				$fecha  = ($row->fecapl==null) ? $row->fecha : $row->fecapl;
				$fecha  = preg_replace('/[^0-9]+/', '',$fecha);

				$data=array();
				$data['libro']    = 'C';
				$data['tipo']     = $row->tipo_doc;
				$data['fuente']   = 'MP';
				$data['sucursal'] = '00';
				$data['fecha']    = $fecha;
				$data['numero']   = $row->numero;
				$data['clipro']   = $row->cod_prv;
				$data['nombre']   = $row->nomfis;
				$data['contribu'] = 'CO';
				$data['rif']      = $row->rif;
				$data['registro'] = ($fecha<$mFECHAF)? '04':'01';
				$data['nacional'] =  'S';
				$data['nfiscal']  = $row->nfiscal;
				$data['general']  = (empty($row->montasa  ))? 0: $row->montasa  ;
				$data['geneimpu'] = (empty($row->tasa     ))? 0: $row->tasa     ;
				$data['reducida'] = (empty($row->monredu  ))? 0: $row->monredu  ;
				$data['reduimpu'] = (empty($row->reducida ))? 0: $row->reducida ;
				$data['adicional']= (empty($row->monadic  ))? 0: $row->monadic  ;
				$data['adicimpu'] = (empty($row->sobretasa))? 0: $row->sobretasa;
				$data['exento']   = (empty($row->exento   ))? 0: $row->exento   ;
				$data['impuesto'] = (empty($row->impuesto ))? 0: $row->impuesto ;
				$data['gtotal']   = (empty($row->monto    ))? 0: $row->monto    ;
				$data['stotal']   = (empty($stotal        ))? 0: $stotal        ;
				$data['fechal']   = $mes.'01';
				$data['referen']  = '';
				$data['reiva']    = $row->reteiva;
				$data['afecta']   = $row->afecta;
				$data['fafecta']  = '';

				$mSQL = $this->db->insert_string('siva', $data);
				$flag=$this->db->simple_query($mSQL);
				if(!$flag) memowrite($mSQL,'genecxp');
			}
		}

		// Procesando Compras scst
		$mSQL = "UPDATE siva SET gtotal=exento+general+geneimpu+adicional+reduimpu+reducida+adicimpu
				WHERE fuente='MP' AND libro='C' ";
		$this->db->simple_query($mSQL);
	}
}