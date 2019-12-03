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
<div class="mfeedback">
<?if(!empty($arResult["ERROR_MESSAGE"]))
{
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
}
if(strlen($arResult["OK_MESSAGE"]) > 0)
{
	?><div class="mf-ok-text"><?=$arResult["OK_MESSAGE"]?></div><?
}
?>
<div class="span8"><h2><?=GetMessage("SEND_ME")?></h2>
<div class="wpcf7" id="wpcf7-f208-p14-o1">
<form action="<?=POST_FORM_ACTION_URI?>" method="POST" novalidate="novalidate" class="wpcf7-form">
<?=bitrix_sessid_post()?>
		 <div class="row-fluid">
			<p class="span4 field"><span class="your-name"><input name="user_name" value="" size="40" class="" aria-required="true" placeholder="<?=GetMessage("MFT_NAME")?>:" type="text"></span> </p>
			<p class="span4 field"><span class="your-email"><input name="user_email" value="" size="40" class="" aria-required="true" placeholder="<?=GetMessage("MFT_EMAIL")?>:" type="email"></span> </p>
			<p class="span4 field"><span class="your-phone"><input name="phone" value="" size="40" class="" placeholder="<?=GetMessage("MFT_PHONE")?>:" type="text"></span> </p>
		</div>
		<p class="field"><span class="your-message"><textarea name="MESSAGE" cols="40" rows="10" class="" placeholder="<?=GetMessage("MFT_MESSAGE")?>:"></textarea></span> </p>

	<?if($arParams["USE_CAPTCHA"] == "Y"):?>
	<div class="mf-captcha">
		<div class="mf-text"><?=GetMessage("MFT_CAPTCHA")?></div>
		<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
		<div class="mf-text"><?=GetMessage("MFT_CAPTCHA_CODE")?><span class="mf-req">*</span></div>
		<input type="text" name="captcha_word" size="30" maxlength="50" value="">
	</div>
	<?endif;?>
	<p class="submit-wrap">
		<input value="<?=GetMessage("MFT_CLEAR")?>" class="btn btn-primary" type="reset">
		<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
		<input value="<?=GetMessage("MFT_SUBMIT")?>" class="btn btn-primary" name="submit" type="submit">
		<div class="ajax-loader"></div>
	</p>
	<div class=""></div>
	<?/*<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
	<input type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>"> */?>
</form>
</div>