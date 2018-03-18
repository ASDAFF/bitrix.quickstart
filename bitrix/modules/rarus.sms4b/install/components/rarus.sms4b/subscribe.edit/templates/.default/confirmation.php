<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//*************************************
//show confirmation form
//*************************************
?>
<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
<table width="100%" border="0" cellpadding="10" cellspacing="0">
<thead><tr><td colspan="2"><?=!$arResult["SHOW_SMS_FORM"] ? GetMessage('CONFIRM_MESS_1'):GetMessage('CONFIRM_MESS_1_SMS')?> <b><?=!$arResult["SHOW_SMS_FORM"] ? $arResult['SUBSCRIPTION']['EMAIL'] : kill_post_fix($arResult['SUBSCRIPTION']['EMAIL'])?></b><?=GetMessage('CONFIRM_MESS_2')?> <br />
<?=GetMessage('CONFIRM_MESS_3')?></td></tr></thead>
<tr>
	<td>
		<?=GetMessage("subscr_conf_code")?><span class="starrequired">*</span>
		<input type="text" name="CONFIRM_CODE" value="<?=$arResult["REQUEST"]["CONFIRM_CODE"];?>" size="20" />
		<input type="submit" name="confirm" value="<?=GetMessage("subscr_conf_button")?>" />
		<input type="hidden" name="auth_type" value="<?=!$arResult["SHOW_SMS_FORM"] ? "email" : "sms" ?>" />
	</td>
</tr>
<tr>
	<td>
		<?=GetMessage("subscr_conf_note1")?> <a title="<?=GetMessage("adm_send_code")?>" href="<?=$arResult["FORM_ACTION"]?>?ID=<?=$arResult["ID"]?>&amp;action=sendcode&amp;<?=bitrix_sessid_get()?>"><?=GetMessage("subscr_conf_note2")?></a>.
	</td>
</tr>
</table>
<input type="hidden" name="ID" value="<?=$arResult["ID"];?>" />	
<?=bitrix_sessid_post();?>
</form>
<br />
