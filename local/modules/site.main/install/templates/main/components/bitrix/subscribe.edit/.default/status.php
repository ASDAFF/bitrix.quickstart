<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//***********************************
//status and unsubscription/activation section
//***********************************
?>

<form action="<?=$arResult["FORM_ACTION"]?>" method="get">

	<h2><?=GetMessage("subscr_title_status")?></h2>
	
	<?if($arResult["SUBSCRIPTION"]["CONFIRMED"] <> "Y"):?>
		<p><?=GetMessage("subscr_title_status_note1")?></p>
	<?elseif($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"):?>
		<p><?=GetMessage("subscr_title_status_note2")?></p>
		<p><?=GetMessage("subscr_status_note3")?></p>
	<?else:?>
		<p><?=GetMessage("subscr_status_note4")?></p>
		<p><?=GetMessage("subscr_status_note5")?></p>
	<?endif;?>
	
	<dl>
		<dt><?=GetMessage("subscr_conf")?></dt>
		<dd class="<?=($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"? "notetext":"errortext")?>" ><?=($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"? GetMessage("subscr_yes"):GetMessage("subscr_no"));?></dd>
		
		<dt><?=GetMessage("subscr_act")?></dt>
		<dd class="<?=($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"? "notetext":"errortext")?>" ><?=($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"? GetMessage("subscr_yes"):GetMessage("subscr_no"));?></dd>
		
		<dt><?=GetMessage("adm_id")?></dt>
		<dd><?=$arResult["SUBSCRIPTION"]["ID"];?></dd>
		
		<dt><?=GetMessage("subscr_date_add")?></dt>
		<dd><?=$arResult["SUBSCRIPTION"]["DATE_INSERT"];?></dd>
		
		<dt><?=GetMessage("subscr_date_upd")?></dt>
		<dd><?=$arResult["SUBSCRIPTION"]["DATE_UPDATE"];?></dd>
	</dl>

	
	<?if($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"):?>
		<div class="form_footer">
			<?if($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"):?>
				<input type="submit" name="unsubscribe" value="<?=GetMessage("subscr_unsubscr")?>" class="btn" />
				<input type="hidden" name="action" value="unsubscribe" />
			<?else:?>
				<input type="submit" name="activate" value="<?=GetMessage("subscr_activate")?>" class="btn" />
				<input type="hidden" name="action" value="activate" />
			<?endif;?>
		</div>
	<?endif;?>
	
	<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
	<?echo bitrix_sessid_post();?>
	
</form>
