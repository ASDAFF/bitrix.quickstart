<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

// bitrix:catalog.section неправильно выводит ссылки с SECTION_CODE_PATH (берет для ссылки текущий раздел вместо реального раздела элемента)
// закрываем косяк стандартного компонента — добавим <link rel="canonical"> с правильной ссылкой (нужного раздела)
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

// если контакт для предложения не указан или не найден, выберем первый контакт из списка, будем использовать его
if (!$arResult["CONTACT"])
	$arResult["CONTACT"] = \Citrus\Realty\Helper::getContactInfo();
