<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="subscribe form">
<form action="<?=$arResult["FORM_ACTION"]?>">

	<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
		<label for="sf_RUB_ID_<?=$itemValue["ID"]?>">
			<input type="checkbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?=$itemValue["ID"]?>" class="inputcheckbox" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> />
			<?=$itemValue["NAME"]?>
		</label>
	<?endforeach;?>

	<label>E-mail:</label>
	<div class="field">
		<input type="email" name="sf_EMAIL" value="<?=$arResult["EMAIL"]?>" title="<?=GetMessage("subscr_form_email_title")?>" class="inputtext" />
	</div>
	
	<div class="form_footer">
		<input type="submit" name="OK" value="<?=GetMessage("subscr_form_button")?>" class="btn" />
	</div>
	
</form>
</div>
