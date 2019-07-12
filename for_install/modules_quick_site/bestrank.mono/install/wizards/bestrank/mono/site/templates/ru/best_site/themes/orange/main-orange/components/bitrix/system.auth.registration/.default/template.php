<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<table class="registarton">
	<tr>
    	<td>
<?ShowMessage($arParams["~AUTH_RESULT"]);
if($arResult["USE_EMAIL_CONFIRMATION"] === "Y" && is_array($arParams["AUTH_RESULT"]) &&  $arParams["AUTH_RESULT"]["TYPE"] === "OK")
{
?>
	<div class="field"><?echo GetMessage("AUTH_EMAIL_SENT")?></div>
<?
}
else
{
?>
		<?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>
			<div class="field"><?echo GetMessage("AUTH_EMAIL_WILL_BE_SENT")?></div>
		<?endif?>
		<!--noindex-->
		<form method="post" action="<?=$arResult["AUTH_URL"]?>" name="bform">
		<?
		if (strlen($arResult["BACKURL"]) > 0)
		{
		?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?
		}
		?>
			<input type="hidden" name="AUTH_FORM" value="Y" />
			<input type="hidden" name="TYPE" value="REGISTRATION" />
				<?=GetMessage("AUTH_NAME")?><br>
				<input type="text" name="USER_NAME" maxlength="50" value="<?=$arResult["USER_NAME"]?>" /><br/><br/>
				<?=GetMessage("AUTH_LAST_NAME")?><br>
				<input type="text" name="USER_LAST_NAME" maxlength="50" value="<?=$arResult["USER_LAST_NAME"]?>" /> <br/><br/>

				<?=GetMessage("AUTH_LOGIN_MIN")?><span class="star">*</span><br>
				<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" /><br>
				<span class="description">&mdash; <?=GetMessage("LOGIN_REQUIREMENTS")?></span> <br/><br/>

				<?=GetMessage("AUTH_PASSWORD_REQ")?><span class="star">*</span><br>
				<input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" /><br>
				<span class="description">&mdash; <?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></span> <br/><br/>

				<?=GetMessage("AUTH_CONFIRM")?><span class="star">*</span><br>
				<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" /><br/><br/>

				E-Mail<span class="star">*</span><br>
				<input type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult["USER_EMAIL"]?>" /><br/><br/>
			<?// ********************* User properties ***************************************************?>
			<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
				<div class="field"><?=strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></div>
				<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
					<?=$arUserField["EDIT_FORM_LABEL"]?><?if ($arUserField["MANDATORY"]=="Y"):?><span class="star">*</span><?endif;?><br>
					<?$APPLICATION->IncludeComponent(
						"bitrix:system.field.edit",
						$arUserField["USER_TYPE"]["USER_TYPE_ID"],
						array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "bform"), null, array("HIDE_ICONS"=>"Y"));?>
					<br><br>
				<?endforeach;?>
			<?endif;?>
			<?// ******************** /User properties ***************************************************

			/* CAPTCHA */
			if ($arResult["USE_CAPTCHA"] == "Y")
			{
				?>
					<?=GetMessage("CAPTCHA_REGF_PROMT")?><span class="star">*</span><br>
					<input type="text" name="captcha_word" maxlength="50" value="" />
					<p style="clear: left;"><input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></p>
				<?
			}
			/* CAPTCHA */
			?>
			<input type="submit" class="bt3"  name="Register" value="<?=GetMessage("AUTH_REGISTER")?>" />
		</form>
		<!--/noindex-->
	</td>
	<td style="padding-left:70px">
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR."include/reg_info.php",
				"AREA_FILE_RECURSIVE" => "N",
				"EDIT_MODE" => "html",
			),
			false,
			Array('HIDE_ICONS' => 'Y')
		);?>
		<script type="text/javascript">
			document.bform.USER_NAME.focus();
			$(".workarea").css({"width":"100%"})
		</script>
	</td>
<?
}
?>
	</tr>
</table>