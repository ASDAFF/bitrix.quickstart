<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "����� �����");
if(!isset($global_error_number)) {
$APPLICATION->SetTitle("����� �����");
} elseif($global_error_number == 404) {
$APPLICATION->SetPageProperty("title", "������ 404 - �������� �� ������");
$APPLICATION->SetTitle("������ 404 - �������� �� ������");
?><p>����������� �������� �� ����������. ���������� ����� ������ ��� �������� �� ����� ������ ����� ����.</p><?
}

?><?$APPLICATION->IncludeComponent("bitrix:main.map", "sitemap", array(
	"CACHE_TYPE" => "Y",
	"CACHE_TIME" => "3600",
	"SET_TITLE" => "N",
	"LEVEL" => "3",
	"COL_NUM" => "1",
	"SHOW_DESCRIPTION" => "N"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>