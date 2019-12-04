<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?=$arResult["NAVIGATION"];?>
	
<?$APPLICATION->IncludeComponent("bitrix:sale.personal.order.list", "", 
	Array(
		"PATH_TO_DETAIL" => $APPLICATION->GetCurPageParam("page=orders&ID=#ID#",array("page","ID","PID")),	// Страница c подробной информацией о заказе
		"PATH_TO_COPY" => $arParams["PATH_TO_COPY"],	// Страница повторения заказа
		"PATH_TO_CANCEL" => $arParams["PATH_TO_CANCEL"],	// Страница отмены заказа
		"PATH_TO_BASKET" => $arParams["PATH_TO_BASKET"],	// Страница корзины
		"ORDERS_PER_PAGE" => $arParams["ORDERS_PER_PAGE"],	// Количество заказов, выводимых на страницу
		"ID" => $ID,	// Идентификатор заказа
		"SET_TITLE" => $arParams["SET_TITLE"],	// Устанавливать заголовок страницы
		"SAVE_IN_SESSION" => $arParams["SAVE_IN_SESSION"],	// Сохранять установки фильтра в сессии пользователя
		"NAV_TEMPLATE" => $arParams["NAV_TEMPLATE"],	// Имя шаблона для постраничной навигации
	),
	false
);?>