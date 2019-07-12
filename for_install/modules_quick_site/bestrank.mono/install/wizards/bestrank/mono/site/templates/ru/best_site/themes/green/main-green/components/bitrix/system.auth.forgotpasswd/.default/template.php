<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
ShowMessage($arParams["~AUTH_RESULT"]);
?>
<p><?=GetMessage("AUTH_FORGOT_PASSWORD_1")?></p>
<div class="workarea personal">
	<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
		<?
		if (strlen($arResult["BACKURL"]) > 0)
		{
		?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?
		}
		?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="SEND_PWD">


		<?=GetMessage("AUTH_LOGIN")?><br>
		<input type="text" name="USER_LOGIN" maxlength="50" class="input_text_style" value="<?=$arResult["LAST_LOGIN"]?>" /><br><br>

		E-Mail<br>
		<input type="text" name="USER_EMAIL" maxlength="255" class="input_text_style"/><br><br>

		<input type="submit" name="send_account_info" class="bt3" value="<?=GetMessage("AUTH_SEND")?>" /><br><br>
	</form>
	<script type="text/javascript">
	document.bform.USER_LOGIN.focus();
	</script>
</div>
<a href="<?=$arResult["AUTH_AUTH_URL"]?>" onclick='var modalH = $("#login").height(); $("#login").css({"display":"block","margin-top":"-"+(parseInt(modalH)/2)+"px" }); return false;'><?=GetMessage("AUTH_AUTH")?></a>
