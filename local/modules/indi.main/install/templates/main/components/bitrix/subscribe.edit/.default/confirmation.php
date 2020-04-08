<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//*************************************
//show confirmation form
//*************************************
?>

<form action="<?=$arResult["FORM_ACTION"]?>" method="get">

	<h2><?=GetMessage("subscr_title_confirm")?></h2>
	
	<p>
		<?=GetMessage("subscr_conf_note1")?>
		<a title="<?echo GetMessage("adm_send_code")?>" href="<?echo $arResult["FORM_ACTION"]?>?ID=<?echo $arResult["ID"]?>&amp;action=sendcode&amp;<?echo bitrix_sessid_get()?>"><?echo GetMessage("subscr_conf_note2")?></a>.
	</p>
	
	
	<label><?=GetMessage("subscr_conf_code")?><span class="starrequired">*</span></label>
	<div class="field">
		<input type="text" name="CONFIRM_CODE" value="<?echo $arResult["REQUEST"]["CONFIRM_CODE"];?>" class="inputtext" />
	</div>
	
	<p><?=GetMessage("subscr_conf_date")?></p>
	<p><?=$arResult["SUBSCRIPTION"]["DATE_CONFIRM"];?></p>

	<div class="form_footer">
		<input type="submit" name="confirm" value="<?echo GetMessage("subscr_conf_button")?>" class="btn" />
	</div>
	
	<input type="hidden" name="ID" value="<?echo $arResult["ID"];?>" />
	
	<?echo bitrix_sessid_post();?>
</form>
