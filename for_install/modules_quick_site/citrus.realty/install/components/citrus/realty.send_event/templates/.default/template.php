<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form id="<?=$arResult["FORM_ID"]?>" name="<?=$arResult["FORM_ID"]?>" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data" class="cse-form">

<?
if (!empty($arResult["ERRORS"])):
	ShowError($arResult["ERRORS"]);
endif;

if (!empty($arResult["MESSAGE"]))
{
	echo '<p style="color: #0a0;">' . htmlspecialchars_decode($arResult["MESSAGE"]) . '</p>';
//	return;
}
echo bitrix_sessid_post();
echo '<input type="hidden" name="cse_hash" value="' . $arResult['FORM_HASH'] . '">';

foreach ($arResult["ITEMS"] as $code => $fieldInfo):

	?><div class="cse-field cse-<?=strtolower($code)?>"><?

	$name = "FIELD[" . $code . "]";?>

	<label for="<?=$code?>" class="cse-field-title"><?
	echo $fieldInfo['NAME'] . ':';
	if ($fieldInfo['IS_REQUIRED'])
	{
		?><span class="starrequired cse-field-required">*</span><?
	}
	?></label>

	<span class="cse-field-input"><?
	
	$value = $arResult['OLD_VALUE'][$code];
		
	if ($code == '__CAPTCHA__'):?>
		<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" class="cse-captcha-image" align="left"/>
		<input type="text" name="captcha_word" maxlength="50" value="" class="cse-captcha-input"><?
	elseif ($fieldInfo['IS_EMAIL']):?>
		<input type="text" name="<?=$name?>" size="25" value="<?=$value?>" id="<?=$code?>" /><?
	else:?>
		<textarea cols="25" rows="5" name="<?=$name?>" id="<?=$code?>"><?=$value?></textarea><?
	endif;

	?></span><?
	
	if (strlen($fieldInfo['TOOLTIP']))
	{
		?><small class="cse-field-tooltip"><?=$fieldInfo['TOOLTIP']?></small><?
	}

	?></div><?

endforeach;
?>
	<div class="tooltip-block">
		<span class="required-fields">*</span>
		<span><?=GetMessage('REQUIRED_MESSAGE_LABLE')?></span>
	</div>
	<input type="submit" name="send_submit" value="<?=GetMessage('T_SEND')?>" />
</form>