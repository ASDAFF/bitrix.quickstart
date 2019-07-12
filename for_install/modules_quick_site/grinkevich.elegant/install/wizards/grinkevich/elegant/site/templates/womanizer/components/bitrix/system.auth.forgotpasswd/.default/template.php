<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div>&nbsp;</div>
<?
ShowMessage($arParams["~AUTH_RESULT"]);
?>
<br />
<p><?=GetMessage("AUTH_FORGOT_PASSWORD_1")?></p>
<br />

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


		<b><?=GetMessage("AUTH_LOGIN")?></b><br>
		<input type="text" name="USER_LOGIN" maxlength="50" class="input_text_style" value="<?=$arResult["LAST_LOGIN"]?>" /><br><br>

		<b><?=GetMessage("AUTH_EMAIL")?></b><br>
		<input type="text" name="USER_EMAIL" maxlength="255" class="input_text_style"/><br><br>

		<input type="submit" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>"  class="orange-but" /><br><br>
	</form>
</div>
<a href="<?= $arResult['AUTH_AUTH_URL']; ?>"><?=GetMessage("AUTH_AUTH")?></a>
