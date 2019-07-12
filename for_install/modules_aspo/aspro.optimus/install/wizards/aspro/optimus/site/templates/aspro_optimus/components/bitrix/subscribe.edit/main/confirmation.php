<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//*************************************
//show confirmation form
//*************************************
?>
<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table">
<thead><tr><td colspan="2"><h4><?echo GetMessage("subscr_title_confirm")?></h4></td></tr></thead>
<tr valign="top">
	<td width="40%">
		<div class="form_control">
			<label><?echo GetMessage("subscr_conf_code")?><span class="star">*</span></label>
			<input type="text" name="CONFIRM_CODE" value="<?echo $arResult["REQUEST"]["CONFIRM_CODE"];?>" size="20" /></p>
		</div>
		<?echo GetMessage("subscr_conf_date")?><br/>
		<?echo $arResult["SUBSCRIPTION"]["DATE_CONFIRM"];?>
	</td>
	<td width="60%">
		<div class="more_text_small">
		<?echo GetMessage("subscr_conf_note1")?> <a title="<?echo GetMessage("adm_send_code")?>" href="<?echo $arResult["FORM_ACTION"]?>?ID=<?echo $arResult["ID"]?>&amp;action=sendcode&amp;<?echo bitrix_sessid_get()?>"><?echo GetMessage("subscr_conf_note2")?></a>.
		</div>
	</td>
</tr>
<tfoot><tr><td colspan="2"><br/><input type="submit" class="button vbig_btn" name="confirm" value="<?echo GetMessage("subscr_conf_button")?>" /></td></tr></tfoot>
</table>
<input type="hidden" name="ID" value="<?echo $arResult["ID"];?>" />
<?echo bitrix_sessid_post();?>
</form>
<br />
