<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="box margin padding">
	<?=ShowError($arResult["strProfileError"]);?>
	<? if ($arResult['DATA_SAVED'] == 'Y')
		echo ShowNote(GetMessage('PROFILE_DATA_SAVED')); ?>
	<div class="bx_profile">
		<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
			<?=$arResult["BX_SESSION_CHECK"]?>
			<input type="hidden" name="lang" value="<?=LANG?>" />
			<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
			<input type="hidden" name="LOGIN" value=<?=$arResult["arUser"]["LOGIN"]?> />
			<input type="hidden" name="EMAIL" value=<?=$arResult["arUser"]["EMAIL"]?> />

			<h2><?=GetMessage("LEGEND_PROFILE")?></h2>
			<label for="NAME"><?=GetMessage('NAME')?></label>
			<input type="text" name="NAME" id="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"]?>" /><br>

			<label for="LAST_NAME"><?=GetMessage('LAST_NAME')?></label>
			<input type="text" name="LAST_NAME" id="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" /><br>

			<label for="SECOND_NAME"><?=GetMessage('SECOND_NAME')?></label>
			<input type="text" name="SECOND_NAME" id="SECOND_NAME" maxlength="50"  value="<?=$arResult["arUser"]["SECOND_NAME"]?>" /><br>

			<br><h2><?=GetMessage("MAIN_PSWD")?></h2>
			<label for="NEW_PASSWORD"><?=GetMessage('NEW_PASSWORD_REQ')?></label>
			<input type="password" name="NEW_PASSWORD" id="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" /> <br>

			<label for="NEW_PASSWORD_CONFIRM"><?=GetMessage('NEW_PASSWORD_CONFIRM')?></label>
			<input type="password" name="NEW_PASSWORD_CONFIRM" id="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" /> <br><br>

			<input name="save" value="<?=GetMessage("MAIN_SAVE")?>" class="bt_blue big shadow" type="submit">
		</form>
	</div>
	<br>
	<?
	if($arResult["SOCSERV_ENABLED"])
	{
		$APPLICATION->IncludeComponent("bitrix:socserv.auth.split", ".default", array(
				"SHOW_PROFILES" => "Y",
				"ALLOW_DELETE" => "Y"
			),
			false
		);
	}
	?>
</div>