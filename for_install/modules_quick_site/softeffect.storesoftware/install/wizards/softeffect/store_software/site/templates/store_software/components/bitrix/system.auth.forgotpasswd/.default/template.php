<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? ShowMessage($arParams["~AUTH_RESULT"]); ?>
<div class="content contenttext bx-auth" id="contactfaq">
<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
	<? if (strlen($arResult["BACKURL"]) > 0) { ?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<? } ?>
	<input type="hidden" name="AUTH_FORM" value="Y">
	<input type="hidden" name="TYPE" value="SEND_PWD">
	<?=GetMessage("AUTH_FORGOT_PASSWORD_1")?>
	<table class="data-table bx-auth-table">
		<thead>
			<tr> 
				<td colspan="2"><b><?=GetMessage("AUTH_GET_CHECK_STRING")?></b></td>
			</tr>
		</thead>
		<tbody>
			<?/*<tr>
				<td><?=GetMessage("AUTH_LOGIN")?></td>
				<td><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" />&nbsp;<?=GetMessage("AUTH_OR")?>
				</td>
			</tr>*/?>
			<tr> 
				<td class="bx-auth-label"><?=GetMessage("AUTH_EMAIL")?></td>
				<td>
					<input type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" />
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td></td>
				<td>
					<input type="submit" class="btn" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>" />
				</td>
			</tr>
		</tfoot>
	</table>
	<br />
	<a href="<?=$arResult["AUTH_AUTH_URL"]?>"><b><?=GetMessage("AUTH_AUTH")?></b></a>
</form>
<script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>
</div>