<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>



<?ShowError($arResult["strProfileError"]);?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
	ShowNote(GetMessage('PROFILE_DATA_SAVED'));
?>
<script type="text/javascript">
<!--
var opened_sections = [<?
$arResult["opened"] = $_COOKIE[$arResult["COOKIE_PREFIX"]."_user_profile_open"];
$arResult["opened"] = preg_replace("/[^a-z0-9_,]/i", "", $arResult["opened"]);
if (strlen($arResult["opened"]) > 0)
{
	echo "'".implode("', '", explode(",", $arResult["opened"]))."'";
}
else
{
	$arResult["opened"] = "reg";
	echo "'reg'";
}
?>];
//-->

var cookie_prefix = '<?=$arResult["COOKIE_PREFIX"]?>';
</script>
<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
<?=$arResult["BX_SESSION_CHECK"]?>
<div class="b-user-fields clearfix">
<input type="hidden" name="lang" value="<?=LANG?>" />
<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
<div class="b-user-field__item">
<label class="b-cart-field__label"><?=$arResult['USER_PROPERTIES']['DATA']['UF_TYPE']['EDIT_FORM_LABEL']?><span class="b-star">*</span></label>
<?
//pr($arResult);?>
<?$APPLICATION->IncludeComponent(
	"cm:system.field.edit",
	$arResult['USER_PROPERTIES']['DATA']['UF_TYPE']["USER_TYPE_ID"],
	array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arResult['USER_PROPERTIES']['DATA']['UF_TYPE']), null, array("HIDE_ICONS"=>"Y"));?>
	
		<label class="b-cart-field__label"><?=GetMessage('EMAIL')?> <span class="b-star">*</span></label>
		<input type="text" class="b-cart-field__input" name="EMAIL" value="<? echo $arResult["arUser"]["EMAIL"]?>"/>
		<input type="hidden" class="b-cart-field__input" name="LOGIN" value="<?=$arResult["arUser"]["EMAIL"]?>"/>
		<label class="b-cart-field__label"><?=GetMessage('LAST_NAME')?> <span class="b-star">*</span></label>
		<input type="text" class="b-cart-field__input" name="LAST_NAME" value="<?=$arResult["arUser"]["LAST_NAME"]?>"/>
		<label class="b-cart-field__label"><?=GetMessage('PERSONAL_PHONE')?> <span class="b-star">*</span></label>
		<input type="text" class="b-cart-field__input" name="PERSONAL_PHONE" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>"/>
	</div>
	<div class="b-user-field__item m-user-field__right">
		<label class="b-cart-field__label"><?=GetMessage('NEW_PASSWORD')?> <span class="b-star">*</span></label>
		<input type="password" class="b-cart-field__input" name="NEW_PASSWORD" autocomplete="off"/>
		<label class="b-cart-field__label"><?=GetMessage('NEW_PASSWORD_CONFIRM')?> <span class="b-star">*</span></label>
		<input type="password" class="b-cart-field__input" name="NEW_PASSWORD_CONFIRM" autocomplete="off"/>
	</div>
</div>
	<div class="b-user-submit"><input class="b-button" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>"></div>
</form>
