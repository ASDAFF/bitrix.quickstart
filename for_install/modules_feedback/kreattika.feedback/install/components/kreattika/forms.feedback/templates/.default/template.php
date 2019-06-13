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
<div class="kff-feedback">
<?if(!empty($arResult["ERROR_MESSAGE"])):
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
endif;

if(strlen($arResult["OK_MESSAGE"]) > 0):
	?><div class="kff-ok-text"><?=$arResult["OK_MESSAGE"]?></div><?
else:
?>

<form action="<?=$APPLICATION->GetCurPage()?>" method="POST">
<?=bitrix_sessid_post()?>
	<table class="kff-tab-form">

		<?if ($arParams["USE_FIELD_NAME"]=="Y"):?>
			<tr><td class="kff-field-label"><?=$arParams["FIELD_NAME_TITLE"]?><?if($arParams["CHECK_FIELD_NAME"]=="Y"):?><span class="kff-req">*</span><?endif?></td><td class="kff-name"><input type="text" name="f_name" value="<?=$arResult["NAME"]?>"<?if($arParams["CHECK_FIELD_NAME"]=="Y"):?> required="required"<?endif?>></td></tr>
		<?endif;?>

		<?if ($arParams["USE_FIELD_PHONE"]=="Y"):?>
			<tr><td class="kff-field-label"><?=$arParams["FIELD_PHONE_TITLE"]?><?if($arParams["CHECK_FIELD_PHONE"]=="Y"):?><span class="kff-req">*</span><?endif?></td><td class="kff-phone"><input type="text" name="f_phone" value="<?=$arResult["PHONE"]?>"<?if($arParams["CHECK_FIELD_PHONE"]=="Y"):?> required="required"<?endif?>></td></tr>
		<?endif;?>

		<?if ($arParams["USE_FIELD_EMAIL"]=="Y"):?>
			<tr><td class="kff-field-label"><?=$arParams["FIELD_EMAIL_TITLE"]?><?if($arParams["CHECK_FIELD_EMAIL"]=="Y"):?><span class="kff-req">*</span><?endif?></td><td class="kff-email"><input type="text" name="f_email" value="<?=$arResult["EMAIL"]?>"<?if($arParams["CHECK_FIELD_EMAIL"]=="Y"):?> required="required"<?endif?>></td></tr>
		<?endif;?>

		<?if ($arParams["USE_FIELD_TEXT"]=="Y"):?>
			<tr><td valign="top" class="kff-field-label"><?=$arParams["FIELD_TEXT_TITLE"]?><?if($arParams["CHECK_FIELD_TEXT"]=="Y"):?><span class="kff-req">*</span><?endif?></td><td class="kff-text"><textarea name="f_text" rows="3"<?if($arParams["CHECK_FIELD_TEXT"]=="Y"):?> required="required"<?endif?>><?=$arResult["TEXT"]?></textarea></td></tr>
		<?endif;?>

		<?if($arParams["USE_CAPTCHA"] == "Y"):?>
			<tr><td colspan="2">
				<div class="kff-ctext"><?=GetMessage("KFF_CAPTCHA")?></div>
				<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
				<div class="kff-ctext"><?=GetMessage("KFF_CAPTCHA_CODE")?><span class="kff-req">*</span></div>
				<input type="text" name="captcha_word" size="30" maxlength="50" value="">
			</td></tr>
		<?endif;?>

		<tr><td colspan="2" align="right">
			<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
			<input type="submit" name="submit" value="<?=$arParams["SUBMIT_TITLE"]?>">
		</td></tr>

	</table>

</form>
<?endif;?>
</div>