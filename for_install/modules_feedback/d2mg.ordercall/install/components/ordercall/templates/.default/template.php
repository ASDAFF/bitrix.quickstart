<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<div class="mfeedback">
<?if(!empty($arResult["ERROR_MESSAGE"])):
{
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
}
elseif (empty($arResult["ERROR_MESSAGE"]) && $arResult['RESULT']=="SUCCESS"):
?>
<div style="display:block;" class="mf-ok-text"><?=$arParams["OK_TEXT"]?></div>
<?endif;?>
<form class="order_call_form" action="<?=$APPLICATION->GetCurPage()?>" method="POST">
<?=bitrix_sessid_post()?>
	<h1><?=GetMessage("MFT_TITLE")?></h1>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
		</div>
		<input class="order_call user_name" type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>">
	</div>
	<div class="mf-phone">
		<div class="mf-text">
			<?=GetMessage("MFT_PHONE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
		</div>
		<input class="order_call user_phone" type="text" name="user_phone" value="<?=$arResult["AUTHOR_PHONE"]?>">
	</div>	
	<div class="mf-message">
		<div class="mf-text">
			<?=GetMessage("MFT_TIME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
		</div>
		<textarea class="user_message" name="MESSAGE" rows="4" cols="40"><?=$arResult["MESSAGE"]?></textarea>
	</div>

	<?if($arParams["USE_CAPTCHA"] == "Y"):?>
		<div class="mf-captcha" style='text-align:center;'>
			<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
			<div class="mf-text"><?=GetMessage("MFT_CAPTCHA_CODE")?><span class="mf-req">*</span></div>
			<input class="user_captcha" type="text" name="captcha_word" size="30" maxlength="50" value="">
		</div>	
	<?endif;?>
	<div style="width: 100%; text-align: center;">
		<input type="submit" class="order_call_button" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>">
	</div>
</form>
</div>