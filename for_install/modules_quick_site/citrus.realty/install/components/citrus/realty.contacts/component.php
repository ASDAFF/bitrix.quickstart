<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var $this CBitrixComponent */

if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
	return;

$arResult = \Citrus\Realty\Helper::getOfficeInfo($arParams["OFFICE"]);
if (!is_array($arResult))
{
	ShowError(GetMessage("CITRUS_REALTY_NO_OFFICE_INFO"));
	return;
}

if ($USER->IsAuthorized())
{
	if ($APPLICATION->GetShowIncludeAreas() && \Bitrix\Main\Loader::includeModule("iblock"))
	{
		$arButtons = CIBlock::GetPanelButtons(
			\Citrus\Realty\Helper::getIblock("offices"),
			$arResult["ID"],
			0,
			array("SECTION_BUTTONS"=>false, "SESSID"=>false)
		);
		$arResult["PANEL"]["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];

		if ($APPLICATION->GetShowIncludeAreas() && !$this->getParent())
			$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
	}
}

$this->IncludeComponentTemplate();
