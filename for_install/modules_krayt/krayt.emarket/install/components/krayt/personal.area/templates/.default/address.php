<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?=$arResult["NAVIGATION"];?>
	
<?$APPLICATION->IncludeComponent("bitrix:sale.personal.profile.detail", "", 
	Array(
		"PATH_TO_LIST" => $APPLICATION->GetCurPageParam("page=addreses",array("page","ID","PID")),	// �������� �� ������� ��������
		"PATH_TO_DETAIL" => $APPLICATION->GetCurPageParam("page=addreses&PID=#PID#",array("page","ID","PID")),	// �������� �������������� �������
		"ID" => (int)$_REQUEST["PID"],	// ������������� �������
		"USE_AJAX_LOCATIONS" => $arParams["USE_AJAX_LOCATIONS"],	// ������������ ����������� ����� ��������������
		"SET_TITLE" => $arParams["SET_TITLE"],	// ������������� ��������� ��������
	),
	false
);?>