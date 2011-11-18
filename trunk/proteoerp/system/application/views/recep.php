<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:

$campos=$form->template_details('seri');
$scampos  ='<tr id="tr_seri_<#i#>">';
$scampos .='<td class="littletablerow" align="left"  >'.$campos['itbarras']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left"  >'.$campos['itcodigo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left"  >'.$campos['itdescri']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left"  >'.$campos['itserial']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right" >'.$campos['itcant']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center"><a href=# onclick="del_seri(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';
//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
var seri_cont = <?php echo $form->max_rel_count['seri'] ?>;
var apuntador = '';
var tipos     = eval(<?php echo $jtipo      ?>);
var origen    = eval(<?php echo $jorigen    ?>);
var tipos_ref = eval(<?php echo $jtipos_ref ?>);

function ttras(v,obj){
	if(v.length>0){
		eval('val=obj.'+v);
	}else{
		val='';
	}
	return val;
}

function leer(){
	campo=apuntador.substr(0,9);
	i=apuntador.substr(10,100);
	valor=$("#"+apuntador).val();

	if(campo=='it_barras'){
		$.post("<?php echo site_url('inventario/common/get_cant'); ?>",{ barras:valor },function(data){
			if(data==1){
				$.post("<?php echo site_url('inventario/common/get_codigo') ?>",{ barras:valor },function(data){
					$("#it_codigo_"+i).val(data);
					$("#it_codigo_"+i+"_val").text(data);
				});
				$.post("<?php echo site_url('inventario/common/get_descrip');?>",{ barras:valor },function(data){
					$("#it_descri_"+i).val(data);
					$("#it_descri_"+i+"_val").text(data);
				});
				$("#it_serial_"+i).focus();
			}

			if(data==0){
				a=0;
				ii=parseFloat(i)-1;
				if(ii>=0){
					codigo=$("#it_codigo_"+ii).val();
					a=codigo.length;
				}

				if(a>0){
					barras=$("#it_barras_"+ii).val();
					descri=$("#it_descri_"+ii).val();

					$("#it_codigo_"+i).val(codigo);
					$("#it_barras_"+i).val(barras);
					$("#it_descri_"+i).val(descri);
					$("#it_serial_"+i).val(valor);

					$("#it_codigo_"+i+"_val").text(codigo);
					$("#it_descri_"+i+"_val").text(descri);
					add_seri();
				}else{
					$("#it_barras_"+i).val('');
					$("#it_barras_"+i).focus();
				}
			}
		});
	}

	if(campo=='it_serial'){
		add_seri();
	}
}

$(function(){
	$("input[name^='it_']").focus(function(){
		apuntador=this.name;
	});
	com=false;
	$(document).keydown(function(e){
		if (13 == e.which) {
			leer();
			return false;
		}
		if(18 == e.which) {
			com=true;
			return false;
		}
		if(com && (e.which == 61 || e.which == 107)) {
			add_seri();
			a=itcasi_cont-1;
			$("#barras_"+a).focus();
			com=false;
			return false;
		}else if (com && e.which != 16 && e.which == 17){
			com=false;
		}
		return true;
	});
	$(".inputnum").numeric(".");

	$('#refe').autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('inventario/common/buscasfacscst'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
				success:
					function(data){
						var sugiere = [];
						$.each(data,
							function(i, val){
								sugiere.push( val );
							}
						);
						add(sugiere);
					},
			})
		},
		minLength: 1,
		select: function( event, ui ) {
			$('#tipo').val(ui.item.tipo);
			$('#clipro').val(ui.item.clipro);
			$('#tipo_refe').val(ui.item.tipo_ref);
			$('#origen').val(ui.item.origen);
			$('#nombre').val(ui.item.nombre);
			human_traslate();
		}
	});
	human_traslate();
});

function _post_modbus(){
	$('#refe').val('');
	$('#tipo_refe').val('');
	$('#tipo').val('R');
	$('#origen').val('scst');
	$('#observa').val('RECEPCION SIN REFERENCIA PARA ASIGNARSE DESPUES');
	human_traslate();
}

function human_traslate(){
	var val=$('#tipo_refe').val();
	$('#tipo_refe_val').text(ttras(val,tipos_ref));

	var val=$('#origen').val();
	$('#origen_val').text(ttras(val,origen));

	var val=$('#tipo').val();
	$('#tipo_val').text(ttras(val,tipos));

	var val=$('#clipro').val();
	$('#clipro_val').text(val);

	var val=$('#nombre').val();
	$('#nombre_val').text(val);
}

function add_seri(){
	var htm = <?php echo $campos ?>;
	can = seri_cont.toString();
	con = (seri_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__FTPL__").after(htm);
	seri_cont=seri_cont+1;

	$("input[name^='it_']").focus(function(){
		apuntador=this.name;
	});
	$("#it_cant_"+can).val(1);
	$("#it_barras_"+can).focus();
}

function del_seri(id){
	id = id.toString();
	seri_cont=seri_cont-1;
	$('#tr_seri_'+id).remove();
}
</script>

<?php  }?>
<table align='center'width="98%" >
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td align=left>&nbsp;</td>
					<td align=right><?php echo $container_tr?></td>
				</tr>
			</table>
		</td>
	</tr><tr>
		<td>
			<table width="100%"  style="margin:0;width:100%;">
			<tr>
				<td class="littletablerowth"><?php echo $form->refe->label   ?>&nbsp;</td>
				<td class="littletablerow"  ><?php echo $form->tipo_refe->output.$form->refe->output  ?>&nbsp;</td>
				<td class="littletablerowth"><?php echo $form->tipo->label   ?>&nbsp;</td>
				<td class="littletablerow"  ><?php echo $form->tipo->output  ?>&nbsp;</td>
			</tr><tr>
				<td class="littletablerowth"><?php echo $form->fecha->label  ?>*&nbsp;</td>
				<td class="littletablerow"  ><?php echo $form->fecha->output ?>&nbsp;</td>
				<td class="littletablerowth"><?php echo $form->origen->label ?>&nbsp;</td>
				<td class="littletablerow"  ><?php echo $form->origen->output?>&nbsp;</td>
			</tr><tr>
				<td class="littletablerowth"><?php echo $form->clipro->label ?>*&nbsp;</td>
				<td class="littletablerow" colspan=3 ><?php echo $form->clipro->output?>&nbsp;<?php echo $form->nombre->output?></td>
			</tr><tr>
				<td class="littletablerowth">         <?php echo $form->observa->label  ?>&nbsp;</td>
				<td class="littletablerow" colspan=3 ><?php echo $form->observa->output ?>&nbsp;</td>
			</tr>
		</table >
		<table class="table_detalle" width="100%">
			<tr id='__FTPL__'>
				<th bgcolor='#7098D0'>Barras            </th>
				<th bgcolor='#7098D0'>C&oacute;digo     </th>
				<th bgcolor='#7098D0'>Descripci&oacute;n</th>
				<th bgcolor='#7098D0'>Serial            </th>
				<th bgcolor='#7098D0'>Cantidad          </th>
				<?php if($form->_status!='show') {?>
				<th bgcolor='#7098D0' >&nbsp;</td>
				<?php } ?>
			</tr>
			<?php
			for($i=0;$i<$form->max_rel_count['seri'];$i++) {
				$obj0="itbarras_$i";
				$obj1="itcodigo_$i";
				$obj2="itdescri_$i";
				$obj3="itserial_$i";
				$obj4="itcant_$i";
			?>
			<tr id='tr_seri_<?=$i ?>'>
				<td class="littletablerow"><?=$form->$obj0->output ?></td>
				<td class="littletablerow"><?=$form->$obj1->output ?></td>
				<td class="littletablerow"><?=$form->$obj2->output ?></td>
				<td class="littletablerow"><?=$form->$obj3->output ?></td>
				<td class="littletablerow" align="right"><?=$form->$obj4->output ?></td>
				<?php if($form->_status!='show') {?>
				<td class="littletablerow" align='center'><a href=# onclick='del_seri(<?=$i ?>);return false;'><?php echo  img("images/delete.jpg",'Eliminar elemento',array("border"=>0));?></a></td>
				<?php } ?>
			</tr>
			<?php } ?>
			<tr id='__UTPL__'>
				<td class="littletablefooterb" align='right' colspan="5">&nbsp;</td>
				<?php if($form->_status!='show') {?>
				<td class="littletablefooterb">&nbsp;</td>
				<?php } ?>
			</tr>
	</table>
		<?php echo $form_end     ?>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>