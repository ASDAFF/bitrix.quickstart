<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//***********************************
//setting section
//***********************************
?>

<form action="<?=$arResult["FORM_ACTION"]?>" method="post">
	<?echo bitrix_sessid_post();?>

	<h2><?=GetMessage("subscr_title_settings")?></h2>
	
	<p><?=GetMessage("subscr_settings_note1")?></p>
	<p><?=GetMessage("subscr_settings_note2")?></p>
	

	<label>
		<?=GetMessage("subscr_email")?><span class="starrequired">*</span>
	</label>
	<div class="field">
		<input type="email" name="EMAIL" value="<?=$arResult["SUBSCRIPTION"]["EMAIL"]!=""?$arResult["SUBSCRIPTION"]["EMAIL"]:$arResult["REQUEST"]["EMAIL"];?>" maxlength="255" class="inputtext" />
	</div>
	
	<label>
		<?=GetMessage("subscr_rub")?><span class="starrequired">*</span>
	</label>
	<div class="field">
		<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
			<label><input type="checkbox" name="RUB_ID[]" class="inputcheckbox" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> /><?=$itemValue["NAME"]?></label>
		<?endforeach;?>
	</div>
	
	<label><?=GetMessage("subscr_fmt")?></label>
	<div class="field">
		<label><input type="radio" class="inputradio" name="FORMAT" value="text"<?if($arResult["SUBSCRIPTION"]["FORMAT"] == "text") echo " checked"?> /><?echo GetMessage("subscr_text")?></label>
		<label><input type="radio" class="inputradio" name="FORMAT" value="html"<?if($arResult["SUBSCRIPTION"]["FORMAT"] == "html") echo " checked"?> />HTML</label>
	</div>

	<div class="form_footer">
		<input type="submit" name="Save" value="<?echo ($arResult["ID"] > 0? GetMessage("subscr_upd"):GetMessage("subscr_add"))?>" class="btn" />
		<input type="reset" value="<?echo GetMessage("subscr_reset")?>" name="reset" class="btn" />
	</div>
	
	<input type="hidden" name="PostAction" value="<?echo ($arResult["ID"]>0? "Update":"Add")?>" />
	<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
	<?if($_REQUEST["register"] == "YES"):?>
		<input type="hidden" name="register" value="YES" />
	<?endif;?>
	<?if($_REQUEST["authorize"]=="YES"):?>
		<input type="hidden" name="authorize" value="YES" />
	<?endif;?>
	
</form>
