<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?=$arResult["NAVIGATION"];?>
	
<?$APPLICATION->IncludeComponent("bitrix:sale.personal.order.list", "", 
	Array(
		"PATH_TO_DETAIL" => $APPLICATION->GetCurPageParam("page=orders&ID=#ID#",array("page","ID","PID")),	// �������� c ��������� ����������� � ������
		"PATH_TO_COPY" => $arParams["PATH_TO_COPY"],	// �������� ���������� ������
		"PATH_TO_CANCEL" => $arParams["PATH_TO_CANCEL"],	// �������� ������ ������
		"PATH_TO_BASKET" => $arParams["PATH_TO_BASKET"],	// �������� �������
		"ORDERS_PER_PAGE" => $arParams["ORDERS_PER_PAGE"],	// ���������� �������, ��������� �� ��������
		"ID" => $ID,	// ������������� ������
		"SET_TITLE" => $arParams["SET_TITLE"],	// ������������� ��������� ��������
		"SAVE_IN_SESSION" => $arParams["SAVE_IN_SESSION"],	// ��������� ��������� ������� � ������ ������������
		"NAV_TEMPLATE" => $arParams["NAV_TEMPLATE"],	// ��� ������� ��� ������������ ���������
	),
	false
);?>