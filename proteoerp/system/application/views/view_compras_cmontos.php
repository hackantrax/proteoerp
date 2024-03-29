<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var tasa_general=<?php echo $alicuota['tasa'];     ?>;
var tasa_reducid=<?php echo $alicuota['redutasa']; ?>;
var tasa_adicion=<?php echo $alicuota['sobretasa'];?>;
var priva = <?php echo $priva ?>;

$(function(){
	$(".inputnum").numeric(".");
});

//Calcula los montos que van a CxP
function ctotales(){
	var base=0;
	var impu=0;
	base += Number($("#cexento").val());
	base += Number($("#cgenera").val());
	base += Number($("#creduci").val());
	base += Number($("#cadicio").val());

	impu += Number($("#civaadi").val());
	impu += Number($("#civagen").val());
	impu += Number($("#civared").val());


	$("#reteiva").val(roundNumber(impu*priva,2));
	$("#cstotal").val(roundNumber(base,2));
	$("#ctotal").val(roundNumber(base+impu,2));
	$("#cimpuesto").val(roundNumber(impu,2));

	$("#cimpuesto_val").text(nformat(impu,2));
	$("#ctotal_val").text(nformat(base+impu,2));
	$("#cstotal_val").text(nformat(base,2));
}

function cal_base(iva,obj,imp){
	$("#"+obj).val(roundNumber(imp*100/iva,2));
	ctotales();
}

function cal_iva(iva,obj,base){
	$("#"+obj).val(roundNumber(base*iva/100,2));
	ctotales();
}
</script>
<?php } ?>
<?php echo $container_tr; ?>
<table class="ui-widget ui-widget-content ui-corner-all">
	<tr >
		<th class="ui-widget-header">Alicuota</th>
		<th class="ui-widget-header">Base</th>
		<th class="ui-widget-header">Impuesto</th>
	</tr>
	<tr>
		<td>Exento</td>
		<td align='right'><?php echo $form->cexento->output;   ?></td>
		<td></td>
	</tr>
	<tr>
		<td>General <?php echo $alicuota['tasa'];     ?>%</td>
		<td align='right'><?php echo $form->cgenera->output;   ?></td>
		<td align='right'><?php echo $form->civagen->output;   ?></td>
	</tr>
	<tr>
		<td>Reducida <?php echo $alicuota['redutasa']; ?>%</td>
		<td align='right'><?php echo $form->creduci->output;   ?></td>
		<td align='right'><?php echo $form->civared->output;   ?></td>
	</tr>
	<tr>
		<td>Adicional<?php echo $alicuota['sobretasa'];?>%</td>
		<td align='right'><?php echo $form->cadicio->output;   ?></td>
		<td align='right'><?php echo $form->civaadi->output;   ?></td>
	</tr>
	<tr>
		<td>Totales</td>
		<td align='right'><b id='cstotal_val'  ><?php echo nformat($form->cstotal->value);   ?></b><?php echo $form->cstotal->output;   ?></td>
		<td align='right'><b id='cimpuesto_val'><?php echo nformat($form->cimpuesto->value); ?></b><?php echo $form->cimpuesto->output; ?></td>
	</tr>
	<tr>
		<td><?php echo $form->riva->label;   ?></td>
		<td></td>
		<td align='right'><?php echo $form->riva->output;   ?></td>
	</tr>
	<tr>
		<td>Total global</td>
		<td align='right'><b id='ctotal_val'><?php echo nformat($form->ctotal->value);   ?></b><?php echo $form->ctotal->output;   ?></td>
		<td></td>
	</tr>
</table>

<?php echo $form_end?>
<?php endif; ?>
