<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="content contenttext bx-auth" id="contactfaq">
	<? ShowMessage($arParams["~AUTH_RESULT"]); ?>
	<? if($arResult["USE_EMAIL_CONFIRMATION"] === "Y" && is_array($arParams["AUTH_RESULT"]) &&  $arParams["AUTH_RESULT"]["TYPE"] === "OK"):?>
		<?echo GetMessage("AUTH_EMAIL_SENT")?>
	<? else: ?>
		<? if($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>
			<?=GetMessage("AUTH_EMAIL_WILL_BE_SENT")?>
		<? endif?>
		<noindex>
			<form method="post" action="<?=$arResult["AUTH_URL"]?>" name="bform">
				<? if (strlen($arResult["BACKURL"]) > 0) { ?>
					<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
				<? } ?>
				<input type="hidden" name="AUTH_FORM" value="Y" />
				<input type="hidden" name="TYPE" value="REGISTRATION" />
				<input type="hidden" name="USER_LOGIN" id="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" />

				<table class="data-table bx-auth-table">
					<tbody>
						<tr>
							<td class="bx-auth-label"><?=GetMessage("AUTH_NAME")?></td>
							<td><input type="text" name="USER_NAME" maxlength="50" value="<?=$arResult["USER_NAME"]?>" /></td>
						</tr>
						<tr>
							<td class="bx-auth-label"><?=GetMessage("AUTH_LAST_NAME")?></td>
							<td><input type="text" name="USER_LAST_NAME" maxlength="50" value="<?=$arResult["USER_LAST_NAME"]?>" /></td>
						</tr>
						<tr>
							<td class="bx-auth-label"><?=GetMessage("AUTH_EMAIL")?>&nbsp;<span class="starrequired">*</span>:</td>
							<td><input type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult["USER_EMAIL"]?>" onkeyup="writeLogin(this.value);" /></td>
						</tr>
						<tr>
							<td class="bx-auth-label"><?=GetMessage("AUTH_PASSWORD_REQ")?>&nbsp;<span class="starrequired">*</span>:</td>
							<td><input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" /></td>
						</tr>
						<tr>
							<td class="bx-auth-label"><?=GetMessage("AUTH_CONFIRM")?>&nbsp;<span class="starrequired">*</span>:</td>
							<td><input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" /></td>
						</tr>
				<?// ********************* User properties ***************************************************?>
				<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
					<tr><td colspan="2"><?=strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></td></tr>
					<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
					<tr><td><?if ($arUserField["MANDATORY"]=="Y"):?><span class="required">*</span><?endif;?>
						<?=$arUserField["EDIT_FORM_LABEL"]?>:</td><td>
							<?$APPLICATION->IncludeComponent(
								"bitrix:system.field.edit",
								$arUserField["USER_TYPE"]["USER_TYPE_ID"],
								array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "bform"), null, array("HIDE_ICONS"=>"Y"));?></td></tr>
					<?endforeach;?>
				<?endif;?>
				<?// ******************** /User properties ***************************************************
				
					/* CAPTCHA */
					if ($arResult["USE_CAPTCHA"] == "Y")
					{
						?>
						<tr>
							<td colspan="2"><b><?=GetMessage("CAPTCHA_REGF_TITLE")?></b></td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
								<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
							</td>
						</tr>
						<tr>
							<td class="bx-auth-label"><?=GetMessage("CAPTCHA_REGF_PROMT")?>&nbsp;<span class="starrequired">*</span>:</td>
							<td><input type="text" name="captcha_word" maxlength="50" value="" /></td>
						</tr>
						<?
					}
					/* CAPTCHA */
					?>
					</tbody>
					<tfoot>
						<tr>
							<td></td>
							<td><input type="submit" name="Register" class="btn" value="<?=GetMessage("AUTH_REGISTER")?>" /></td>
						</tr>
					</tfoot>
				</table>
				<?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?><br />
				<span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?><br />
				<br />
				<a href="<?=$arResult["AUTH_AUTH_URL"]?>" rel="nofollow"><b><?=GetMessage("AUTH_AUTH")?></b></a>
			</form>
		</noindex>
		<script type="text/javascript">
			document.bform.USER_NAME.focus();
			function writeLogin(val) {
				console.log(val);
				document.getElementById('USER_LOGIN').value = val;
			}
		</script>
	<? endif ?>
</div>