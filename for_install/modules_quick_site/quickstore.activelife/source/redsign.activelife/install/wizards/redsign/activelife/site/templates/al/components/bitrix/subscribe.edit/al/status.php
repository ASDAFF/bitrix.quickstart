<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//***********************************
//status and unsubscription/activation section
//***********************************
?>
<form class="col-xs-12 col-sm-6" action="<?=$arResult["FORM_ACTION"]?>" method="get">
<div class="panel">
    <div class="panel__head">
        <h2 class="panel__name"><?echo GetMessage("subscr_title_status")?></h2>
    </div>
    <div class="panel__body">
        <p class="<?echo ($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"? "notetext":"errortext")?>"><?echo ($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"? GetMessage("subscr_conf_yes"):GetMessage("subscr_conf_no"));?></p>
        <?if($arResult["SUBSCRIPTION"]["CONFIRMED"] <> "Y"):?>
            <p><?echo GetMessage("subscr_title_status_note1")?></p>
        <?elseif($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"):?>
            <p><?echo GetMessage("subscr_title_status_note2")?></p>
            <p><?echo GetMessage("subscr_status_note3")?></p>
        <?else:?>
            <p><?echo GetMessage("subscr_status_note4")?></p>
            <p><?echo GetMessage("subscr_status_note5")?></p>
        <?endif;?>
        <p class="<?echo ($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"? "notetext":"errortext")?>"><?echo ($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"? GetMessage("subscr_act_yes"):GetMessage("subscr_act_no"));?></p>
        
        <p>
            <?echo GetMessage("adm_id")?> <?echo $arResult["SUBSCRIPTION"]["ID"];?><br>
            <?echo GetMessage("subscr_date_add")?> <?echo $arResult["SUBSCRIPTION"]["DATE_INSERT"];?><br>
            <?echo GetMessage("subscr_date_upd")?> <?echo $arResult["SUBSCRIPTION"]["DATE_UPDATE"];?>
        </p>
    </div>
</div>
    <?if($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"):?>
        <p>
		<?if($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"):?>
			<input class="btn btn1" type="submit" name="unsubscribe" value="<?echo GetMessage("subscr_unsubscr")?>" />
			<input type="hidden" name="action" value="unsubscribe" />
		<?else:?>
			<input class="btn btn1" type="submit" name="activate" value="<?echo GetMessage("subscr_activate")?>" />
			<input type="hidden" name="action" value="activate" />
		<?endif;?>
        </p>
	<?endif;?>
<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
<?echo bitrix_sessid_post();?>
</form>