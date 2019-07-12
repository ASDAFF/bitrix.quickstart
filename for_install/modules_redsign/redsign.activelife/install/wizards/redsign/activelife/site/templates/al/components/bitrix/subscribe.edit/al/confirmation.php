<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//*************************************
//show confirmation form
//*************************************
?>
<form class="col-xs-12" action="<?=$arResult["FORM_ACTION"]?>" method="get">
<h2><?echo GetMessage("subscr_title_confirm")?></h2>
<p><?echo GetMessage("subscr_conf_note1")?> <a title="<?echo GetMessage("adm_send_code")?>" href="<?echo $arResult["FORM_ACTION"]?>?ID=<?echo $arResult["ID"]?>&amp;action=sendcode&amp;<?echo bitrix_sessid_get()?>"><?echo GetMessage("subscr_conf_note2")?></a>.</p>
<div class="form-group">
    <input class="form-control" type="text" name="CONFIRM_CODE" value="<?echo $arResult["REQUEST"]["CONFIRM_CODE"];?>" size="20" placeholder="<?echo GetMessage("subscr_conf_code")?>*">
</div>
<p><?echo GetMessage("subscr_conf_date")?>: <?echo $arResult["SUBSCRIPTION"]["DATE_CONFIRM"];?></p>
		
<input class="btn btn1" type="submit" name="confirm" value="<?echo GetMessage("subscr_conf_button")?>">

<input type="hidden" name="ID" value="<?echo $arResult["ID"];?>" />
<?echo bitrix_sessid_post();?>
</form>