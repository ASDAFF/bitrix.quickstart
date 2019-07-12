<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="border_block">

<?//here you can place your own messages
	switch($arResult["MESSAGE_CODE"])
{
	case "E01":
		//When user not found
		$class = "alert-warning";
		break;
	case "E02":
		//User was successfully authorized after confirmation
		$class = "alert-success";
		break;
	case "E03":
		//User already confirm his registration
		$class = "alert-warning";
		break;
	case "E04":
		//Missed confirmation code
		$class = "alert-warning";
		break;
	case "E05":
		//Confirmation code provided does not match stored one
		$class = "alert-danger";
		break;
	case "E06":
		//Confirmation was successfull
		$class = "alert-success";
		break;
	case "E07":
		//Some error occured during confirmation
		$class = "alert-danger";
		break;
	default:
		$class = "alert-warning";
}
?>
<?
if($arResult["MESSAGE_TEXT"] <> ''):
	$text = str_replace(array("<br>", "<br />"), "\n", $arResult["MESSAGE_TEXT"]);
?>
<div class="block_wr <?=$class?>"><?echo nl2br(htmlspecialcharsbx($text))?></div>
<?endif?>
<?if($arResult["SHOW_FORM"]):?>
	<div class="module-form-block-wr">
		<div class="form-block">
			<form method="post" action="<?echo $arResult["FORM_ACTION"]?>">
				<div class="r form-control">
					<label><?echo GetMessage("CT_BSAC_LOGIN")?></label>
					<input type="text" name="<?echo $arParams["LOGIN"]?>" maxlength="50" value="<?echo (strlen($arResult["LOGIN"]) > 0? $arResult["LOGIN"]: $arResult["USER"]["LOGIN"])?>" size="17" />
				</div>
				<div class="r form-control">
					<label><?echo GetMessage("CT_BSAC_CONFIRM_CODE")?></label>
					<input type="text" name="<?echo $arParams["CONFIRM_CODE"]?>" maxlength="50" value="<?echo $arResult["CONFIRM_CODE"]?>" size="17" />
				</div>
				 <div class="but-r"><input type="submit" class="button vbig_btn wides" value="<?echo GetMessage("CT_BSAC_CONFIRM")?>" /></div>
				<input type="hidden" name="<?echo $arParams["USER_ID"]?>" value="<?echo $arResult["USER_ID"]?>" />
			</form>
		</div>
	</div>
<?elseif(!$USER->IsAuthorized()):?>
	<?
	$APPLICATION->IncludeComponent(
		"bitrix:system.auth.form",
		"main",
		Array(
			"REGISTER_URL" => SITE_DIR."auth/registration/",
			"PROFILE_URL" => SITE_DIR."auth/forgot-password/",
			"SHOW_ERRORS" => "Y"
		)
	);
	?>
<?endif?>
</div>