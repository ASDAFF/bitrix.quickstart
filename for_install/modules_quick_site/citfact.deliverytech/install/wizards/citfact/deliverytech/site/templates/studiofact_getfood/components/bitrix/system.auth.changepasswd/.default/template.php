<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="bx-auth box margin padding">
	<? ShowMessage($arParams["~AUTH_RESULT"]); ?>
	<form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform">
		<?if (strlen($arResult["BACKURL"]) > 0): ?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<? endif ?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="CHANGE_PWD">
		<table class="data-table bx-auth-table bx-changepass-table">
			<tbody>
				<tr>
					<td class="bx-auth-label"><?=GetMessage("AUTH_LOGIN")?><span class="starrequired">*</span></td>
					<td><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" class="bx-auth-input" /></td>
				</tr>
				<tr>
					<td class="bx-auth-label"><?=GetMessage("AUTH_CHECKWORD")?><span class="starrequired">*</span></td>
					<td><input type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" class="bx-auth-input" /></td>
				</tr>
				<tr>
					<td class="bx-auth-label"><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?><span class="starrequired">*</span></td>
					<td><input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" class="bx-auth-input" />
	<?if($arResult["SECURE_AUTH"]):?>
					<span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
						<div class="bx-auth-secure-icon"></div>
					</span>
					<noscript>
					<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
						<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
					</span>
					</noscript>
	<script type="text/javascript">
	document.getElementById('bx_auth_secure').style.display = 'inline-block';
	</script>
	<?endif?>
					</td>
				</tr>
				<tr>
					<td class="bx-auth-label"><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?><span class="starrequired">*</span></td>
					<td><input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" class="bx-auth-input"  /></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" align="center"><input type="submit" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>" /></td>
				</tr>
			</tfoot>
		</table>
		<br />
		<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
		<p><?=GetMessage("AUTH_REQ")?><span class="starrequired">*</span></p>
		<p><a href="<?=$arResult["AUTH_AUTH_URL"]?>"><b><?=GetMessage("AUTH_AUTH")?></b></a></p>
	</form>
	<script type="text/javascript">
	document.bform.USER_LOGIN.focus();
	</script>
</div>