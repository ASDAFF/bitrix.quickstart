<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

ShowMessage($arParams["~AUTH_RESULT"]);
?>
<div class="bx_forgotpassword_page">
	<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
		<?if (strlen($arResult["BACKURL"]) > 0):?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?endif;?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="SEND_PWD">

		<p><?=GetMessage("AUTH_FORGOT_PASSWORD_1")?></p>

		<h2><?=GetMessage("AUTH_GET_CHECK_STRING")?></h2>

		<strong><?=GetMessage("AUTH_LOGIN")?></strong><br />
		<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" class="input_text_style" /><br />

		<strong><?=GetMessage("AUTH_OR")?></strong><br /><br />

		<strong><?=GetMessage("AUTH_EMAIL")?></strong><br />
		<input type="text" name="USER_EMAIL" maxlength="255" class="input_text_style" /><br />

		<input type="submit" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>" class="big bt_blue"/>

		<p><a href="<?=$arResult["AUTH_AUTH_URL"]?>"><b><?=GetMessage("AUTH_AUTH")?></b></a></p>
	</form>
</div>
<script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>
