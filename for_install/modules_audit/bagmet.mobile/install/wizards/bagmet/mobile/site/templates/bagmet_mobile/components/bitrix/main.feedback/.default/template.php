<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<div class="contact_form">
<?if(!empty($arResult["ERROR_MESSAGE"]))
{
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
}
if(strlen($arResult["OK_MESSAGE"]) > 0)
{
	?><div><?=$arResult["OK_MESSAGE"]?></div><?
}
?>

<form action="<?=$APPLICATION->GetCurPage()?>" method="POST">
<?=bitrix_sessid_post()?>
	<div>
		<div>
			<label for="user_name"><?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="star">*</span><?endif?></label>
		</div>
		<input type="text" name="user_name" id="user_name" value="<?=$arResult["AUTHOR_NAME"]?>">
	</div>
	<div>
		<div>
			<label for="user_email"><?=GetMessage("MFT_EMAIL")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?><span class="star">*</span><?endif?></label>
		</div>
		<input type="text" name="user_email"  id="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>">
	</div>

	<div>
		<div>
			<label for="MESSAGE"><?=GetMessage("MFT_MESSAGE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?><span class="star">*</span><?endif?></label>
		</div>
		<textarea name="MESSAGE" id="MESSAGE" rows="5" cols="40"><?=$arResult["MESSAGE"]?></textarea>
	</div>

	<?if($arParams["USE_CAPTCHA"] == "Y"):?>
	<div>
		<br>
		<h4 class="auth_form_title"><?=GetMessage("MFT_CAPTCHA")?></h4>
		<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
		<div>
			<label><?=GetMessage("MFT_CAPTCHA_CODE")?><span class="star">*</span></label>
		</div>
		<input type="text" name="captcha_word" size="30" maxlength="50" value="">
	</div>
	<?endif;?>
	<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
	<br>
	<input type="submit" class="login_button" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>">
</form>
</div>