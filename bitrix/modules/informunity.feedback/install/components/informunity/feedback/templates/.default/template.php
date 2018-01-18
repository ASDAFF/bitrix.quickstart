<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<div class="mfeed">
<?$auto_focus = 0;
$FL_req = 0;
if(!empty($arResult["ERROR_MESSAGE"])) {
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
	$auto_focus = 1;
}
?>
<?if(strlen($arResult["OK_MESSAGE"]) > 0): $auto_focus = 1;?>
	<div style="color:green; font-weight:bold; padding-bottom: 1em;"><?=$arResult["OK_MESSAGE"]?></div>
<?endif?>
<form method="POST" enctype="multipart/form-data">
<?=bitrix_sessid_post()?>
<?$cp=0;?>
<?foreach($arParams["NEW_EXT_FIELDS"] as $i => $ext_field):?>  
<?if(!($i == "iu_2" && $arParams["USE_IU_PAT"] == "Y")):?>     
<div>
	<label><?=$ext_field[0]?><?if($ext_field[1]): if($FL_req==0)$FL_req=1;?> <span style="color:#f00"><?=GetMessage("MFT_REQ")?></span><?endif?></label>
</div>
<div style="padding-bottom:0.4em;">
	<?if($ext_field[3]):?>
		<input type="hidden" value="<?=($arParams["MAX_SIZE_FILE"] * 1000)?>" name="MAX_FILE_SIZE">
		<input name="<?="file_".$i?>" type="file" size="37">
	<?else:?>
		<?if($ext_field[2]):?>
		<textarea name="custom[<?=$i?>]" rows="5" cols="40" style="width:320px;" <?if($cp==0 && $arResult["custom_$i"]=='' && $auto_focus): $cp=1;?>autofocus <?endif?>><?=$arResult["custom_$i"]?></textarea>
		<?else:?>
		<input type="text" name="custom[<?=$i?>]" value="<?=$arResult["custom_$i"]?>" style="width:200px;" <?if($cp==0 && $arResult["custom_$i"]=='' && $auto_focus): $cp=1;?>autofocus <?endif?>/>
		<?endif?>
	<?endif?>
</div>
<?else: $FL_mes = $ext_field[1];?>
<?endif?>
<?endforeach;?>
<?if(isset($FL_mes)):?>
<div>
	<label><?=GetMessage("MFT_MESS")?><?if($FL_mes): if($FL_req==0)$FL_req=1;?> <span style="color:#f00"><?=GetMessage("MFT_REQ")?></span><?endif?></label>
</div>
<div style="padding-bottom:0.4em;">
	<textarea name="custom[iu_2]" rows="5" cols="40" style="width:320px;" <?if($cp==0 && $auto_focus): $cp=1;?>autofocus <?endif?>><?=$arResult["custom_iu_2"]?></textarea>
</div>
<?endif?>
<?if($arParams["USE_CAPTCHA"] == "Y"): if($FL_req==0)$FL_req=1;?>
<div>
	<div><?=GetMessage("MFT_CAPTCHA")?></div>
	<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
	<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
	<div><?=GetMessage("MFT_CAPTCHA_CODE")?> <span style="color:#f00"><?=GetMessage("MFT_REQ")?></span></div>
	<input type="text" name="captcha_word" size="30" maxlength="50" value="">
</div>
<?endif;?>
<?if($arParams["COPY_LETTER"] == "Y" && isset($arParams['FIELD_FOR_EMAIL']) && $arParams['FIELD_FOR_EMAIL'] !== "iu_none"):?>
	<div><input type="checkbox" name="copy_letter" value="Y" /> <?=GetMessage("MFT_COPY_LETTER")?></div>
<?endif;?>
<?if($FL_req):?>
<div><small style="color:#f00"><?=GetMessage("MFT_REQ")?> <?=GetMessage("MFT_REQUIRED")?></small></div>
<?endif;?>
<br /><input type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>" class="button" />
</form>
</div>