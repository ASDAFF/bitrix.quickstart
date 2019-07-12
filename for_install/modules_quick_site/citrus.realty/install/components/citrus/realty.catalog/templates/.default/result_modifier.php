<?
if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
{
	ShowError(GetMessage("CITRUS_REALTY_MODULE_NOT_FOUND"));
	return;
}
