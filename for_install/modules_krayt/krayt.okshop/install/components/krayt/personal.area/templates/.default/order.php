<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?=$arResult["NAVIGATION"];?>
	
<?$APPLICATION->IncludeComponent("bitrix:sale.personal.order.detail", "", 
	Array(
		"PATH_TO_LIST" => $APPLICATION->GetCurPageParam("page=orders",array("page","ID","PID")),	// �������� �� ������� �������
		"PATH_TO_CANCEL" => $arParams["PATH_TO_CANCEL"],	// �������� ������ ������
		"PATH_TO_PAYMENT" => $arParams["PATH_TO_PAYMENT"],	// �������� ����������� ��������� �������
		"ID" => $_REQUEST["ID"],	// ������������� ������
		"SET_TITLE" => $arParams["SET_TITLE"],	// ������������� ��������� ��������
		"PROP_1" => $arParams["PROP_1"],	// �� ���������� �������� ��� ���� ����������� "���. ����" (s1)
		"PROP_2" => $arParams["PROP_2"]
	),
	false
);?>