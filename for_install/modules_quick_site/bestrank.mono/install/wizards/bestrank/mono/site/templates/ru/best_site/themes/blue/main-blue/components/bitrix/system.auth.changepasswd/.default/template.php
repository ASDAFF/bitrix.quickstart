<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="workarea personal">
<?
ShowMessage($arParams["~AUTH_RESULT"]);
?>
	<form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform">
		<?if (strlen($arResult["BACKURL"]) > 0): ?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<? endif ?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="CHANGE_PWD">

		<?=GetMessage("AUTH_LOGIN")?><span class="star">*</span><br>
		<input type="text" name="USER_LOGIN" maxlength="50" class="input_text_style" value="<?=$arResult["LAST_LOGIN"]?>" /><br><br>

		<?=GetMessage("AUTH_CHECKWORD")?><span class="star">*</span><br>
		<input type="text" name="USER_CHECKWORD" maxlength="50" class="input_text_style" value="<?=$arResult["USER_CHECKWORD"]?>" /> <br><br>

		<?=GetMessage("AUTH_NEW_PASSWORD_REQ")?><span class="star">*</span><br>
		<input type="password" name="USER_PASSWORD" maxlength="50" class="input_text_style" value="<?=$arResult["USER_PASSWORD"]?>" /><br>
		<span class="description">&mdash; <?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></span><br/><br/>

		<?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?><span class="star">*</span><br>
		<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" class="input_text_style" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>"  /> <br><br>

		<input type="submit" class="bt3" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>" />
	</form>

	<script type="text/javascript">
	document.bform.USER_LOGIN.focus();
	</script>
</div>
<br><a href="<?=$arResult["AUTH_AUTH_URL"]?>" onclick='var modalH = $("#login").height(); $("#login").css({"display":"block","margin-top":"-"+(parseInt(modalH)/2)+"px" }); return false;'><?=GetMessage("AUTH_AUTH")?></a>