<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//******************************************
//subscription authorization form
//******************************************
?>
<table width="100%" border="0" cellpadding="0px" cellspacing="0" class="conf_table">
<tr><td>
	<h3><?=GetMessage('auth_form_for_edit')?></h3>
	<form action="<?=$arResult["FORM_ACTION"].($_SERVER["QUERY_STRING"]<>""? "?".htmlspecialchars($_SERVER["QUERY_STRING"]):"")?>" method="post">
	<table width="100%" border="0" cellpadding="5px" cellspacing="0">
	<thead><tr><td><?=GetMessage("adm_auth_note")?><?=!$arResult["SHOW_SMS_FORM"] ? "e-mail ":GetMessage('adm_auth_note_1')?><?=GetMessage('adm_auth_note_2')?><?=!$arResult["SHOW_SMS_FORM"] ? GetMessage('adm_auth_note_3') : GetMessage('adm_auth_note_3_SMS')?>&nbsp;<b><?=!$arResult["SHOW_SMS_FORM"] ? $arResult["REQUEST"]["sf_EMAIL"] : kill_post_fix($arResult["REQUEST"]["sf_EMAIL"])?></td></tr></thead>
	<tr valign="top">
		<td>
			<p><?=!$arResult["SHOW_SMS_FORM"] ? "e-mail":GetMessage('adm_auth_note_1')?><br /><input type="text" name="sf_EMAIL" size="20" value="<?=($arResult["SHOW_SMS_FORM"]) ? kill_post_fix($arResult["REQUEST"]["EMAIL"]) : $arResult["REQUEST"]["EMAIL"]?>" title="<?=GetMessage("subscr_auth_email")?>" /></p>
			<p><?=GetMessage("subscr_auth_pass")?><br /><input type="password" name="AUTH_PASS" size="20" value="" title="<?=GetMessage("subscr_auth_pass_title")?>" /></p>
		</td>
	</tr>
	<tfoot><tr><td><input type="submit" name="autorize" value="<?=GetMessage("adm_auth_butt")?>" /></td></tr></tfoot>
	</table>

	<input type="hidden" name="action" value="authorize" />
	<input type="hidden" name="auth_type" value="<?=!$arResult["SHOW_SMS_FORM"] ? "email" : "sms" ?>" />
	<?=bitrix_sessid_post();?>
	</form>
</td>
<td id="vline"></td>
<td>

	<h3><?=GetMessage('form_request_pass');?></h3>
	<form action="<?=$arResult["FORM_ACTION"]?>">
	<table width="100%" border="0" cellpadding="7px" cellspacing="0">
	<tr>
		<td>
			<?=GetMessage('YOURS')?>&nbsp;<?=!$arResult["SHOW_SMS_FORM"] ? "e-mail":GetMessage('adm_auth_note_1')?>&nbsp;<b><?=!$arResult["SHOW_SMS_FORM"] ? $arResult["REQUEST"]["EMAIL"]:kill_post_fix($arResult["REQUEST"]["EMAIL"])?></b>&nbsp;<?=GetMessage('YOURS_2')?>&nbsp;<?=$arResult["SUBSCRIPTION"]["DATE_INSERT"]?>.
		</td>
	</tr>
	<tr>
		<td>
			<?=GetMessage('auth_full_text')?><?=!$arResult["SHOW_SMS_FORM"] ? GetMessage('auth_full_text_post'):GetMessage('auth_full_text_sms')?>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="sendpassword" value="<?=GetMessage("subscr_pass_button")?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('remem_pass_1')?>&nbsp;<a href = "index.php"><?=GetMessage('back')?></a>&nbsp;<?=GetMessage('remem_pass_2')?>
		</td>
	</tr>
	<tr valign="top">
		<td width="40%">
			<input type="hidden" name="sf_EMAIL" size="20" value="<?=($arResult["SHOW_SMS_FORM"])?kill_post_fix($arResult["REQUEST"]["EMAIL"]):$arResult["REQUEST"]["EMAIL"]?>" title="<?=GetMessage("subscr_auth_email")?>" />
		</td>
	</tr>
	</table>
	<input type="hidden" name="ID" value="<?=$arResult["ID"]?>" />
	<input type="hidden" name="action" value="sendpassword" />
	<?=bitrix_sessid_post();?>
	</form>
</td>
</tr>
</table>



