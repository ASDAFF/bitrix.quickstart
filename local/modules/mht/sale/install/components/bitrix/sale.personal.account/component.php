<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

if (!CBXFeatures::IsFeatureEnabled('SaleAccounts'))
	return;

if (!$USER->IsAuthorized())
{
	$APPLICATION->AuthForm(GetMessage("SALE_ACCESS_DENIED"));
}

$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y" );
if($arParams["SET_TITLE"] == 'Y')
	$APPLICATION->SetTitle(GetMessage("SPA_TITLE"));

	$dbAccountList = CSaleUserAccount::GetList(
		array("CURRENCY" => "ASC"),
		array("USER_ID" => IntVal($USER->GetID())),
		false,
		false,
		array("ID", "CURRENT_BUDGET", "CURRENCY", "TIMESTAMP_X")
	);

if($arAccountList = $dbAccountList->GetNext())
{
	$arResultTmp = Array();
	$arResult["DATE"] = str_replace("#DATE#", date(CDatabase::DateFormatToPHP(CSite::GetDateFormat("SHORT", SITE_ID))), GetMessage("SPA_MY_ACCOUNT"));
	do
	{
		$arResultTmp["CURRENCY"] = CCurrencyLang::GetByID($arAccountList["CURRENCY"], LANGUAGE_ID);
		$arResultTmp["ACCOUNT_LIST"] = $arAccountList;
		$arResultTmp["INFO"] = str_replace("#CURRENCY#", $arResultTmp["CURRENCY"]["CURRENCY"]." (".$arResultTmp["CURRENCY"]["FULL_NAME"].")", str_replace("#SUM#", SaleFormatCurrency($arAccountList["CURRENT_BUDGET"], $arAccountList["CURRENCY"]), GetMessage("SPA_IN_CUR")));
		$arResult["ACCOUNT_LIST"][] = $arResultTmp;
	}
	while($arAccountList = $dbAccountList->GetNext());
}
else
	$arResult["ERROR_MESSAGE"] = GetMessage("SPA_NO_ACCOUNT");
$this->IncludeComponentTemplate();
?>