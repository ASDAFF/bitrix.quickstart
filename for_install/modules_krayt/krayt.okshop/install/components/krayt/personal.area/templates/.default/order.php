<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?=$arResult["NAVIGATION"];?>
	
<?$APPLICATION->IncludeComponent("bitrix:sale.personal.order.detail", "", 
	Array(
		"PATH_TO_LIST" => $APPLICATION->GetCurPageParam("page=orders",array("page","ID","PID")),	// Страница со списком заказов
		"PATH_TO_CANCEL" => $arParams["PATH_TO_CANCEL"],	// Страница отмены заказа
		"PATH_TO_PAYMENT" => $arParams["PATH_TO_PAYMENT"],	// Страница подключения платежной системы
		"ID" => $_REQUEST["ID"],	// Идентификатор заказа
		"SET_TITLE" => $arParams["SET_TITLE"],	// Устанавливать заголовок страницы
		"PROP_1" => $arParams["PROP_1"],	// Не показывать свойства для типа плательщика "Физ. лицо" (s1)
		"PROP_2" => $arParams["PROP_2"]
	),
	false
);?>