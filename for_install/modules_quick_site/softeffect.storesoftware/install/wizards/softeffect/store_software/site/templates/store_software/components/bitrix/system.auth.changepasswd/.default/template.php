<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="content contenttext bx-auth" id="contactfaq">
	<? ShowMessage($arParams["~AUTH_RESULT"]); ?>
	<form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform">
		<?if (strlen($arResult["BACKURL"]) > 0): ?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<? endif ?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="CHANGE_PWD">
		<table class="data-table bx-auth-table">
			<thead>
				<tr>
					<td colspan="2"><b><?=GetMessage("AUTH_CHANGE_PASSWORD")?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="bx-auth-label"><?=GetMessage("AUTH_LOGIN")?>&nbsp;<span class="starrequired">*</span>:</td>
					<td><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" /></td>
				</tr>
				<tr>
					<td class="bx-auth-label"><?=GetMessage("AUTH_CHECKWORD")?>&nbsp;<span class="starrequired">*</span>:</td>
					<td><input type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" /></td>
				</tr>
				<tr>
					<td class="bx-auth-label"><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?>&nbsp;<span class="starrequired">*</span>:</td>
					<td><input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" /></td>
				</tr>
				<tr>
					<td class="bx-auth-label"><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?>&nbsp;<span class="starrequired">*</span>:</td>
					<td><input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>"  /></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td><input type="submit" class="btn" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>" /></td>
				</tr>
			</tfoot>
		</table>
		<?=$arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?><br />
		<span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?><br />
		<br />
		<a href="<?=$arResult["AUTH_AUTH_URL"]?>"><b><?=GetMessage("AUTH_AUTH")?></b></a>
	</form>
	<script type="text/javascript">
		document.bform.USER_LOGIN.focus();
	</script>
</div>