<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?=$arResult["NAVIGATION"];?>

<?$APPLICATION->IncludeComponent("bitrix:sale.personal.profile.list", "", 
	Array(
		"PATH_TO_DETAIL" => $APPLICATION->GetCurPageParam("page=addreses&PID=#ID#",array("page","ID","PID")),	// Страница с подробной информацией о профиле
		"PER_PAGE" => $arParams["PER_PAGE_ADR"],	// Количество профилей, выводимых на странице
		"SET_TITLE" => $arParams["SET_TITLE"],	// Устанавливать заголовок страницы
	),
	false
);?>