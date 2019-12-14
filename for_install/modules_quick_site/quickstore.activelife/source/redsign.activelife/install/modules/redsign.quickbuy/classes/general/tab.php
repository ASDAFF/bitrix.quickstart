<?
IncludeModuleLangFile(__FILE__);

class CRSQUICKBUYTab
{
	function MyOnAdminTabControlBegin( &$form )
	{
		global $DB, $DBType, $APPLICATION;
		
		if(self::NeedAddTab())
		{
		// *************************************************************** add tab for element *************************************************************** //
$ELEMENT_ID = IntVal($_REQUEST['ID']);
$arCurrencyList = array();
$rsCurrencies = CCurrency::GetList(($by2='sort'),($order2='asc'));
while($arCurrency = $rsCurrencies->Fetch())
{$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];}
$res = CRSQUICKBUYElements::GetByElementID($ELEMENT_ID);
if($data = $res->Fetch())
{
	$data = $data;
} else {
	$data = array();
}
ob_start();
?><input type="hidden" name="redsign_quickbuy_id" value="<?=$data['ID']?>" /><?
?><input type="hidden" name="redsign_quickbuy_element_id" value="<?=$ELEMENT_ID?>" /><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RSQB.ACTIVE')?></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="redsign_quickbuy_active" value="Y"<?if($data['ID']>0):?> checked="checked"<?endif;?> /></td><?
?></tr><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><span class="adm-required-field"><?=GetMessage('RSQB.DATE_FROM')?></span></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=CAdminCalendar::CalendarDate('redsign_quickbuy_date_from', $data['DATE_FROM'], 10, true)?></td><?
?></tr><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><span class="adm-required-field"><?=GetMessage('RSQB.DATE_TO')?></span></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=CAdminCalendar::CalendarDate('redsign_quickbuy_date_to', $data['DATE_TO'], 10, true)?></td><?
?></tr><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><span class="adm-required-field"><?=GetMessage('RSQB.DISCOUNT')?></span></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?
		?><input type="text" name="redsign_quickbuy_discount" value="<?=$data['DISCOUNT']?>" /><?
		?><select name="redsign_quickbuy_discount_type"><?
			?><option value="P"<?if($data['VALUE_TYPE']=='P'):?> selected <?endif;?>><?=GetMessage('RSQB.VALUE_TYPE_P')?></option><?
			?><option value="F"<?if($data['VALUE_TYPE']=='F' || IntVal($data['ID'])<1):?> selected <?endif;?>><?=GetMessage('RSQB.VALUE_TYPE_F')?></option><?
			?><option value="S"<?if($data['VALUE_TYPE']=='S'):?> selected <?endif;?>><?=GetMessage('RSQB.VALUE_TYPE_S')?></option><?
		?></select><?
	?></td><?
?></tr><?
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><span class="adm-required-field"><?=GetMessage('RSQB.CURRENCY')?></span></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?
		?><select name="redsign_quickbuy_currency"><?
			foreach($arCurrencyList as $key => $val)
			{
				?><option value="<?=$key?>"<?if($data['CURRENCY']==$key):?> selected <?endif;?>><?=$val?></option><?
			}
		?></select><?
	?></td><?
?></tr><?
$bitrix_default_quantity_trace = COption::GetOptionString('catalog', 'default_quantity_trace', 'N');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RSQB.QUANTITY')?></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?
		?><input type="text" name="redsign_quickbuy_quantity" value="<?=$data['QUANTITY']?>" <?if($bitrix_default_quantity_trace=='N'):?>disabled <?endif;?>/><?
		if($bitrix_default_quantity_trace=='N')
		{
			?>&nbsp;<?=GetMessage('RSQB.QUANTITY_NOTE_0')?><?
		}
	?></td><?
?></tr><?

?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RSQB.AUTO_RENEWAL')?></td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="redsign_quickbuy_auto_renewal" value="Y"<?if($data['AUTO_RENEWAL']=='Y'):?> checked="checked"<?endif;?> /></td><?
?></tr><?
$context = ob_get_contents();
ob_end_clean();

			$form->tabs[] = array(
				'DIV' => 'redsign_quickbuy_edit_element',
				'TAB' => GetMessage('RSQB.TAB_NAME'),
				'ICON' => 'main_user_edit',
				'TITLE' => GetMessage('RSQB.TAB_TITLE'),
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