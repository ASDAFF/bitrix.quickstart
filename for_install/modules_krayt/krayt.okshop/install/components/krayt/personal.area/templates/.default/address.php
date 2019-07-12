<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?=$arResult["NAVIGATION"];?>
	
<?$APPLICATION->IncludeComponent("bitrix:sale.personal.profile.detail", "", 
	Array(
		"PATH_TO_LIST" => $APPLICATION->GetCurPageParam("page=addreses",array("page","ID","PID")),	// Страница со списком профилей
		"PATH_TO_DETAIL" => $APPLICATION->GetCurPageParam("page=addreses&PID=#PID#",array("page","ID","PID")),	// Страница редактирования профиля
		"ID" => (int)$_REQUEST["PID"],	// Идентификатор профиля
		"USE_AJAX_LOCATIONS" => $arParams["USE_AJAX_LOCATIONS"],	// Использовать расширенный выбор местоположения
		"SET_TITLE" => $arParams["SET_TITLE"],	// Устанавливать заголовок страницы
	),
	false
);?>