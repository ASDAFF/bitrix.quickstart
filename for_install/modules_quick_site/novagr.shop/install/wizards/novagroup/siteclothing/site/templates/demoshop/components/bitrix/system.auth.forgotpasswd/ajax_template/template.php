<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

ShowMessage($arParams["~AUTH_RESULT"]);

?>
<div aria-hidden="true" aria-labelledby="myModalLabel3" role="dialog" tabindex="-1" class="modal hide fade recovery-pass" id="forgotPass">
	<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h3 id="myModalLabel3"><?=GetMessage("AUTH_QUERY_LABEL")?></h3>
	</div>

	<div class="modal-body">
		<div id="error_forgot_container"></div>
		<form  id="forgotForm" name="bform" method="post" target="_top" action="<?=SITE_DIR?>auth/ajax/forms.php?forgot_password=yes">
	<?
/*if (strlen($arResult["BACKURL"]) > 0)
{
?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?
}*/
?>
	<input type="hidden" name="form_id" value="forgot" >
	<input type="hidden" name="AUTH_FORM" value="Y">
	<input type="hidden" name="TYPE" value="SEND_PWD_AJAX">
	<p>
	<?=GetMessage("AUTH_FORGOT_PASSWORD_1")?>
	</p>
	<table class="data-table bx-forgotpass-table">
		<thead>
		<tr> 
			<td colspan="2"><b><?=GetMessage("AUTH_GET_CHECK_STRING")?></b></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?=GetMessage("AUTH_LOGIN")?></td>
			<td><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" />
			</td>
		</tr>
		<tr>
	   <td>&nbsp;</td>
	   <td><?=GetMessage("AUTH_OR")?></td>
	  </tr>
		<tr> 
			<td><?=GetMessage("AUTH_EMAIL")?></td>
			<td>
				<input type="text" name="USER_EMAIL" maxlength="255" />
			</td>
		</tr>
	</tbody>
	</table>
		<a class="already-l" id="forgot_auth" href="#"><?=GetMessage("AUTH_AUTH")?></a>
		<input type="submit" class="btn btn-r" value="<?=GetMessage("AUTH_SEND")?>">
	</form>


	</div>
</div>
<script type="text/javascript">
//document.bform.USER_LOGIN.focus();
</script>
