<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="bx_redetpassword_page bx-auth">
<?
ShowMessage($arParams["~AUTH_RESULT"]);
?>
	<form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform">
		<?if (strlen($arResult["BACKURL"]) > 0):?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?endif?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="CHANGE_PWD">

		<div class="form-group">
			<label><span class="starrequired">*</span><?=GetMessage("AUTH_LOGIN")?></label>
			<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" class="input_text_style form-control">
		</div>

		<div class="form-group">
			<label><span class="starrequired">*</span><?=GetMessage("AUTH_CHECKWORD")?></label>
			<input type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" class="input_text_style form-control">
		</div>
		
		<div class="form-group">
			<label><span class="starrequired">*</span><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?></label>
			<input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" class="input_text_style form-control">
		</div>
		
		<div class="form-group">
			<label><span class="starrequired">*</span><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?></label>
			<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" class="input_text_style form-control">
		</div>

		<hr>
		
		<div class="form-group">
			<input type="submit" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>" class="big bt_blue btn btn-default">
		</div>		

		<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?><br/>
		<span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p>

		<p><a href="<?=$arResult["AUTH_AUTH_URL"]?>"><b><?=GetMessage("AUTH_AUTH")?></b></a></p>
	</form>
</div>

<script type="text/javascript">
	document.bform.USER_LOGIN.focus();
</script>