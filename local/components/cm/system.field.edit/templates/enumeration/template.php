<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$bWasSelect = false;

if($arParams["arUserField"]["SETTINGS"]["DISPLAY"]!="CHECKBOX"):
	?><select class="b-cart-field__select" name="<?=$arParams["arUserField"]["FIELD_NAME"]?>"<?if($arParams["arUserField"]["SETTINGS"]["LIST_HEIGHT"] > 1):?> size="<?=$arParams["arUserField"]["SETTINGS"]["LIST_HEIGHT"]
	?>"<?endif;?> <?
	if ($arParams["arUserField"]["MULTIPLE"]=="Y"):
	?> multiple="multiple"<?
	endif;
	?>><?
elseif($arParams["arUserField"]["MULTIPLE"]=="Y"):
	?><input type="hidden" name="<?=$arParams["arUserField"]["FIELD_NAME"]?>" value=""><?
endif;
foreach ($arParams["arUserField"]["USER_TYPE"]["FIELDS"] as $key => $val)
{

	$bSelected = in_array($key, $arResult["VALUE"]) && (
		(!$bWasSelect) ||
		($arParams["arUserField"]["MULTIPLE"] == "Y")
	);
	$bWasSelect = $bWasSelect || $bSelected;

	if($arParams["arUserField"]["SETTINGS"]["DISPLAY"]=="CHECKBOX")
	{
		?><?if($arParams["arUserField"]["MULTIPLE"]=="Y"):?>
			<label><input type="checkbox" value="<?echo $key?>" name="<?echo $arParams["arUserField"]["FIELD_NAME"]?>"<?echo ($bSelected? " checked" : "")?>><?=$val?></label><br />
		<?else:?>
			<label><input type="radio" value="<?echo $key?>" name="<?echo $arParams["arUserField"]["FIELD_NAME"]?>"<?echo ($bSelected? " checked" : "")?>><?=$val?></label><br />
		<?endif;?><?
	}
	else
	{
		?><option value="<?echo $key?>"<?echo ($bSelected? " selected" : "")?>><?echo $val?></option><?
	}
}
if($arParams["arUserField"]["SETTINGS"]["DISPLAY"]!="CHECKBOX"):
?></select><?
endif;?>