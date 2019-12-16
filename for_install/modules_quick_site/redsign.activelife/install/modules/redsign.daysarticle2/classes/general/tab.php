<?
IncludeModuleLangFile(__FILE__);

class CRSDA2Tab
{
	function MyOnAdminTabControlBegin( &$form )
	{
		global $DB, $DBType, $APPLICATION;
		
		if(self::NeedAddTab())
		{
		// *************************************************************** add tab for element *************************************************************** //
$ELEMENT_ID = IntVal($_REQUEST["ID"]);
$arCurrencyList = array();
$rsCurrencies = CCurrency::GetList(($by2='sort'),($order2='asc'));
while($arCurrency = $rsCurrencies->Fetch())
{$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];}
$res = CRSDA2Elements::GetByElementID($ELEMENT_ID);
if($data = $res->Fetch())
{
	$data = $data;
} else {
	$data = array();
}
ob_start();
?><script>
function AddCutomDinamic()
{
	var index = document.getElementById('redsign_daysarticle2_counter').value;
	var list = document.getElementById('redsign_daysarticle2_dinamics_custom');
	var li = document.createElement('LI');
	li.innerHTML = '<input type="text" name="redsign_daysarticle2_dinamics_custom_percent['+index+']" value="" /> &nbsp; ' +
						'<input type="text" name="redsign_daysarticle2_dinamics_custom_time['+index+']" value="" />';
	list.appendChild(li);
	document.getElementById('redsign_daysarticle2_counter').value = (parseInt(index)+1);
	return false;
}
function SwitchDinamicaType(val)
{
	if(val=="evenly")
	{
		document.getElementById('redsign_daysarticle2_dinamics_custom').style.display = "none";
	} else {
		document.getElementById('redsign_daysarticle2_dinamics_custom').style.display = "block";
	}
}
</script><?
?><input type="hidden" name="redsign_daysarticle2_id" value="<?=$data["ID"]?>" /><?
?><input type="hidden" name="redsign_daysarticle2_element_id" value="<?=$ELEMENT_ID?>" /><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage("RSDA2.ACTIVE")?></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="redsign_daysarticle2_active" value="Y"<?if($data["ID"]>0):?> checked="checked"<?endif;?> /></td><?
?></tr><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><span class="adm-required-field"><?=GetMessage("RSDA2.DATE_FROM")?></span></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=CAdminCalendar::CalendarDate("redsign_daysarticle2_date_from", $data["DATE_FROM"], 10, true)?></td><?
?></tr><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><span class="adm-required-field"><?=GetMessage("RSDA2.DATE_TO")?></span></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=CAdminCalendar::CalendarDate("redsign_daysarticle2_date_to", $data["DATE_TO"], 10, true)?></td><?
?></tr><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><span class="adm-required-field"><?=GetMessage("RSDA2.DISCOUNT")?></span></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?
		?><input type="text" name="redsign_daysarticle2_discount" value="<?=$data['DISCOUNT']?>" /><?
		?><select name="redsign_daysarticle2_discount_type"><?
			?><option value="P"<?if($data['VALUE_TYPE']=='P'):?> selected <?endif;?>><?=GetMessage('RSDA2.VALUE_TYPE_P')?></option><?
			?><option value="F"<?if($data['VALUE_TYPE']=='F' || IntVal($data['ID'])<1):?> selected <?endif;?>><?=GetMessage('RSDA2.VALUE_TYPE_F')?></option><?
			?><option value="S"<?if($data['VALUE_TYPE']=='S'):?> selected <?endif;?>><?=GetMessage('RSDA2.VALUE_TYPE_S')?></option><?
		?></select><?
	?></td><?
?></tr><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><span class="adm-required-field"><?=GetMessage('RSDA2.CURRENCY')?></span></span></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?
		?><select name="redsign_daysarticle2_currency"><?
			foreach($arCurrencyList as $key => $val)
			{
				?><option value="<?=$key?>"<?if($data['CURRENCY']==$key):?> selected <?endif;?>><?=$val?></option><?
			}
		?></select><?
	?></td><?
?></tr><?
$bitrix_default_quantity_trace = COption::GetOptionString('catalog', 'default_quantity_trace', 'N');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage("RSDA2.QUANTITY")?></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?
		?><input type="text" name="redsign_daysarticle2_quantity" value="<?=$data['QUANTITY']?>" <?if($bitrix_default_quantity_trace=='N'):?>disabled <?endif;?>/><?
		if($bitrix_default_quantity_trace=='N')
		{
			?>&nbsp;<?=GetMessage('RSDA2.QUANTITY_NOTE_0')?><?
		}
	?></td><?
?></tr><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage("RSDA2.AUTO_RENEWAL")?></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="redsign_daysarticle2_auto_renewal" value="Y"<?if($data["AUTO_RENEWAL"]=="Y"):?> checked="checked"<?endif;?> /></td><?
?></tr><?
?><tr class="heading"><?
	?><td colspan="2"><?=GetMessage("RSDA2.HEAD_DINAMIC")?></td><?
?></tr><?
?><tr valign="top"><?
	?><td colspan="2"><?
		?><!-- dinamics --><?
		$data["DINAMICA"]=="custom" ? $dinamica = "custom" : $dinamica = "evenly";
		?><label><input type="radio" name="redsign_daysarticle2_dinamic" value="evenly"<?if($dinamica=="evenly"):?> checked="checked"<?endif;?> onclick="SwitchDinamicaType(this.value);" /><?
			?><?=GetMessage("RSDA2.DINAMIC_EVENLY")?></label><br /><?
		?><label><input type="radio" name="redsign_daysarticle2_dinamic" value="custom"<?if($dinamica=="custom"):?> checked="checked"<?endif;?> onclick="SwitchDinamicaType(this.value);" /><?
			?><?=GetMessage("RSDA2.DINAMIC_CUSTOM")?></label><br /><?
		?><ul id="redsign_daysarticle2_dinamics_custom" style="display:<?if($dinamica=="custom"):?>block<?else:?>none<?endif;?>;"><?
			?><li><?
				?><?=GetMessage("RSDA2.DINAMIC_CUSTOM_PERSENT")?> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?
				?> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?
				?><?=GetMessage("RSDA2.DINAMIC_CUSTOM_TIME")?> &nbsp; <input type="button" onclick="AddCutomDinamic();" value="+" /><?
			?></li><?
			$key = 0;
			$arrDinamicData = unserialize( $data["DINAMICA_DATA"] );
			foreach($arrDinamicData as $persent => $time)
			{
				if($persent!="" && $time!="")
				{
					?><li><?
						?><input type="text" name="redsign_daysarticle2_dinamics_custom_percent[<?=$key?>]" value="<?=$persent?>" /> &nbsp;<?
						?><input type="text" name="redsign_daysarticle2_dinamics_custom_time[<?=$key?>]" value="<?=$time?>" /><?
					?></li><?
				}
				$key++;
			}
			for($i=($key+1);$i<($key+6);$i++)
			{
				?><li><?
					?><input type="text" name="redsign_daysarticle2_dinamics_custom_percent[<?=$i?>]" value="" /> &nbsp;<?
					?><input type="text" name="redsign_daysarticle2_dinamics_custom_time[<?=$i?>]" value="" /><?
				?></li><?
			}
		?></ul><?
		?><input type="hidden" name="redsign_daysarticle2_counter" id="redsign_daysarticle2_counter" value="<?=($key+6)?>" /><?
		?><!-- /dinamics --><?
	?></td><?
?></tr><?
$context = ob_get_contents();
ob_end_clean();

			$form->tabs[] = array(
				'DIV' => 'redsign_daysarticle2_edit_element',
				'TAB' => GetMessage('RSDA2.TAB_NAME'),
				'ICON' => 'main_user_edit',
				'TITLE' => GetMessage('RSDA2.TAB_TITLE'),
				'CONTENT' => $context
			);
		}
	}
	
	function NeedAddTab()
	{
		global $APPLICATION;
		
		$return = false;
		if(
			// ---- edit iblock element in admin_section ---- //
			(
				$APPLICATION->GetCurPage() == '/bitrix/admin/iblock_element_edit.php' &&
				IntVal($_REQUEST['ID'])>0 &&
				$_REQUEST['bxpublic']!='Y'
			) ||
			// ---- edit product ---- //
			(
				$APPLICATION->GetCurPage() == '/bitrix/admin/cat_product_edit.php' &&
				IntVal($_REQUEST['ID'])>0 &&
				$_REQUEST['bxpublic']!='Y'
			) ||
			// ---- edit iblock element in work area ---- //
			(
				$APPLICATION->GetCurPage() == '/bitrix/admin/cat_product_edit.php' &&
				IntVal($_REQUEST['ID'])>0 &&
				$_REQUEST['bxpublic']=='Y'
			)
		)
		{
			$return = true;
		}
		return $return;
	}
}