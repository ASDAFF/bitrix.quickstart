<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
?>
<div class="bx_mfeedback bx-auth">
	<?if(!empty($arResult["ERROR_MESSAGE"]))
	{
		foreach($arResult["ERROR_MESSAGE"] as $v)
			ShowError($v);
	}
	if(strlen($arResult["OK_MESSAGE"]) > 0)
	{
		?><div class="bj-panel">
			<b><?=$arResult["OK_MESSAGE"]?></b>
		</div><hr><?
	}
	?>
	<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
		<?=bitrix_sessid_post()?>
		<div class="form-group">
			<label><?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>:</label>
			<input type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>" class="form-control" required />
		</div>

		<div class="form-group">
			<label><?=GetMessage("MFT_EMAIL")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>:</label>
			<input type="text" name="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>" class="form-control" required />
		</div>

		<div class="form-group">
			<label><?=GetMessage("MFT_MESSAGE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>:</label>
			<textarea name="MESSAGE" rows="5" cols="40" class="form-control" required ><?=$arResult["MESSAGE"]?></textarea>
		</div>

		<?if($arParams["USE_CAPTCHA"] == "Y"):?>
		<div class="form-group">
			<label><?=GetMessage("MFT_CAPTCHA_CODE")?><span class="mf-req">*</span></label>
			<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
			<div class="form-group">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
			</div>
			<input type="text" name="captcha_word" size="30" maxlength="50" value="" class="form-control" required />
		</div>
		<?endif;?>

		<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
		<input type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>" class="bt_blue big shadow btn btn-default">
	</form>
</div>