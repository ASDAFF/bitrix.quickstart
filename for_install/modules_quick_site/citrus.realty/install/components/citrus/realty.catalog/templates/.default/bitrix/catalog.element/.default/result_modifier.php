<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

// bitrix:catalog.section ����������� ������� ������ � SECTION_CODE_PATH (����� ��� ������ ������� ������ ������ ��������� ������� ��������)
// ��������� ����� ������������ ���������� � ������� <link rel="canonical"> � ���������� ������� (������� �������)
// https://support.google.com/webmasters/answer/139066?hl=ru
$arResult["CANONICAL"] = false;
$this->__component->setResultCacheKeys(array("CANONICAL"));
if (intval($arResult["ID"]) > 0)
{
	$rsElement = CIBlockElement::GetList(
		Array("SORT" => "ASC"),
		Array("IBLOCK_ID" => $arResult["IBLOCK_ID"], "ID" => $arResult["ID"]),
		$arGroupBy = false,
		$arNavStartParams = false,
		$arSelectFields = Array("ID", "NAME", "DETAIL_PAGE_URL")
	);
	$rsElement->SetUrlTemplates();
	if ($arElement = $rsElement->GetNext())
	{
		$serverName = SITE_SERVER_NAME ? SITE_SERVER_NAME : (COption::GetOptionString('main', 'server_name', $_SERVER['HTTP_HOST']));
		$arResult["CANONICAL"] = $APPLICATION->IsHTTPS() ? 'https://' : 'http://' . $serverName . $arElement["DETAIL_PAGE_URL"];
	}
}

$arResult["CONTACT"] = false;
if ($contact = is_array($arResult["PROPERTIES"]["contact"]) ? $arResult["PROPERTIES"]["contact"]["VALUE"] : false)
	$arResult["CONTACT"] = \Citrus\Realty\Helper::getContactInfo($contact);

// ���� ������� ��� ����������� �� ������ ��� �� ������, ������� ������ ������� �� ������, ����� ������������ ���
if (!$arResult["CONTACT"])
	$arResult["CONTACT"] = \Citrus\Realty\Helper::getContactInfo();
