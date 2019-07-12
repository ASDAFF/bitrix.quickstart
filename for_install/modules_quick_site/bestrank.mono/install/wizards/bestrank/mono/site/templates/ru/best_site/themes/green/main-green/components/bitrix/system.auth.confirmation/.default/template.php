<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="workarea personal">

<div class="field"><?echo $arResult["MESSAGE_TEXT"]?></div>
<?//here you can place your own messages
	switch($arResult["MESSAGE_CODE"])
	{
	case "E01":
		?><? //When user not found
		break;
	case "E02":
		?><? //User was successfully authorized after confirmation
		break;
	case "E03":
		?><? //User already confirm his registration
		break;
	case "E04":
		?><? //Missed confirmation code
		break;
	case "E05":
		?><? //Confirmation code provided does not match stored one
		break;
	case "E06":
		?><? //Confirmation was successfull
		break;
	case "E07":
		?><? //Some error occured during confirmation
		break;
	}
?>
<?if($arResult["SHOW_FORM"]):?>
	<form method="post" action="<?echo $arResult["FORM_ACTION"]?>">
		<?echo GetMessage("CT_BSAC_LOGIN")?><br>
		<input type="text" name="<?echo $arParams["LOGIN"]?>" maxlength="50" class="input_text_style" value="<?echo (strlen($arResult["LOGIN"]) > 0? $arResult["LOGIN"]: $arResult["USER"]["LOGIN"])?>" size="17" /><br><br>

		<?echo GetMessage("CT_BSAC_CONFIRM_CODE")?><br>
		<input type="text" name="<?echo $arParams["CONFIRM_CODE"]?>" maxlength="50" class="input_text_style" value="<?echo $arResult["CONFIRM_CODE"]?>" size="17" /><br><br>

		<input type="submit" class="bt3" value="<?echo GetMessage("CT_BSAC_CONFIRM")?>" /><br>
		<input type="hidden" name="<?echo $arParams["USER_ID"]?>" value="<?echo $arResult["USER_ID"]?>" />
	</form>
<?elseif(!$USER->IsAuthorized()):?>
	<?$APPLICATION->IncludeComponent("bitrix:system.auth.authorize", "", array());?>
<?endif?>
</div>