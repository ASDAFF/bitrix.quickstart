<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?if($USER->IsAuthorized()):?>

<p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

<?else:?>
<?
if (count($arResult["ERRORS"]) > 0):
	foreach ($arResult["ERRORS"] as $key => $error)
		if (intval($key) == 0 && $key !== 0) 
			$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

	ShowError(implode("<br />", $arResult["ERRORS"]));

elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):
?>
<p><?echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT")?></p>
<?endif?>

<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
<div style="width: 400px">
<?if($arResult["BACKURL"] <> ''):?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?endif;?>

<?foreach ($arResult["SHOW_FIELDS"] as $FIELD):?>
<div style="padding-bottom: 8px;">
<?=GetMessage("REGISTER_FIELD_".$FIELD)?>:<?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><span class="b-star">*</span><?endif?>
<?
	switch ($FIELD)
	{		
		case "PASSWORD":
			$style = (array_key_exists($FIELD, $arResult["ERRORS"])) ? 'b-text m-text-error' : 'b-text';
			?>
			<input style="margin-top: 5px; margin-bottom: 5px;" size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="<?=$style?>" />
			<?
			break;
		case "CONFIRM_PASSWORD":
			$style = (array_key_exists($FIELD, $arResult["ERRORS"])) ? 'b-text m-text-error' : 'b-text';
			?>
			<input  style="margin-top: 5px; margin-bottom: 5px;" size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="<?=$style?>" /><?
			break;

		default:
			$style = (array_key_exists($FIELD, $arResult["ERRORS"])) ? 'b-text m-text-error' : 'b-text';
			?>
			<input  style="margin-top: 5px; margin-bottom: 5px;" size="30" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" class="<?=$style?>" />
			<?
	}?>
</div>
<?endforeach?>

<?
/* CAPTCHA */
if ($arResult["USE_CAPTCHA"] == "Y")
{
	?>
		<b><?=GetMessage("REGISTER_CAPTCHA_TITLE")?></b>
		<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
		<?=GetMessage("REGISTER_CAPTCHA_PROMT")?>:<span class="starrequired">*</span>
		<input type="text" name="captcha_word" maxlength="50" value="" />
	<?
}
/* !CAPTCHA */
?>

<div>
	<input type="submit" name="register_submit_button" value="<?=GetMessage("AUTH_REGISTER")?>" class="b-button" />
</div>

<div>
	<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
	<p><span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p>
</div>

</div>
</form>
<?endif?>