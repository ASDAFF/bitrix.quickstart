<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("���������");
$n = each($_SESSION['CATALOG_COMPARE_LIST']);
$filter = Array("LOGIC"=>"OR","ID"=>0);
foreach($_SESSION['CATALOG_COMPARE_LIST'][$n[0]]['ITEMS'] as $i=>$item){
	$item = CIBlockElement::GetById($item['ID'])->GetNext();
	if($item && $i==$item['ID']) $filter[] = Array("ID"=>$item['ID']);
	else unset($_SESSION['CATALOG_COMPARE_LIST'][$n[0]]['ITEMS'][$i]);
}
$arrFilter[] = $filter;
?><?$APPLICATION->IncludeComponent("bitrix:catalog.section", "favorite", Array(
	"IBLOCK_TYPE" => "iarga_shopplus100",	// ��� ���������
	"IBLOCK_ID" => $n[0],	// ��������
	"SECTION_ID" => "",	// ID �������
	"SECTION_CODE" => "",	// ��� �������
	"SECTION_USER_FIELDS" => array(	// �������� �������
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",	// �� ������ ���� ��������� ��������
	"ELEMENT_SORT_ORDER" => "asc",	// ������� ���������� ���������
	"FILTER_NAME" => "arrFilter",	// ��� ������� �� ���������� ������� ��� ���������� ���������
	"INCLUDE_SUBSECTIONS" => "Y",	// ���������� �������� ����������� �������
	"SHOW_ALL_WO_SECTION" => "Y",	// ���������� ��� ��������, ���� �� ������ ������
	"PAGE_ELEMENT_COUNT" => "60",	// ���������� ��������� �� ��������
	"LINE_ELEMENT_COUNT" => "3",	// ���������� ��������� ��������� � ����� ������ �������
	"PROPERTY_CODE" => array(	// ��������
		0 => "",
		1 => "vars",
		2 => "props",
		3 => "main",
		4 => "action",
		5 => "oldprice",
		6 => "",
	),
	"OFFERS_LIMIT" => "5",	// ������������ ���������� ����������� ��� ������ (0 - ���)
	"SECTION_URL" => "",	// URL, ������� �� �������� � ���������� �������
	"DETAIL_URL" => "",	// URL, ������� �� �������� � ���������� �������� �������
	"BASKET_URL" => "/personal/basket.php",	// URL, ������� �� �������� � �������� ����������
	"ACTION_VARIABLE" => "action",	// �������� ����������, � ������� ���������� ��������
	"PRODUCT_ID_VARIABLE" => "id",	// �������� ����������, � ������� ���������� ��� ������ ��� �������
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",	// �������� ����������, � ������� ���������� ���������� ������
	"PRODUCT_PROPS_VARIABLE" => "prop",	// �������� ����������, � ������� ���������� �������������� ������
	"SECTION_ID_VARIABLE" => "SECTION_ID",	// �������� ����������, � ������� ���������� ��� ������
	"AJAX_MODE" => "N",	// �������� ����� AJAX
	"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
	"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
	"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
	"CACHE_TYPE" => "A",	// ��� �����������
	"CACHE_TIME" => "1",	// ����� ����������� (���.)
	"CACHE_GROUPS" => "Y",	// ��������� ����� �������
	"META_KEYWORDS" => "-",	// ���������� �������� ����� �������� �� ��������
	"META_DESCRIPTION" => "-",	// ���������� �������� �������� �� ��������
	"BROWSER_TITLE" => "-",	// ���������� ��������� ���� �������� �� ��������
	"ADD_SECTIONS_CHAIN" => "N",	// �������� ������ � ������� ���������
	"DISPLAY_COMPARE" => "N",	// �������� ������ ���������
	"SET_TITLE" => "Y",	// ������������� ��������� ��������
	"SET_STATUS_404" => "N",	// ������������� ������ 404, ���� �� ������� ������� ��� ������
	"CACHE_FILTER" => "N",	// ���������� ��� ������������� �������
	"PRICE_CODE" => "",	// ��� ����
	"USE_PRICE_COUNT" => "N",	// ������������ ����� ��� � �����������
	"SHOW_PRICE_COUNT" => "1",	// �������� ���� ��� ����������
	"PRICE_VAT_INCLUDE" => "Y",	// �������� ��� � ����
	"PRODUCT_PROPERTIES" => "",	// �������������� ������
	"USE_PRODUCT_QUANTITY" => "N",	// ��������� �������� ���������� ������
	"CONVERT_CURRENCY" => "N",	// ���������� ���� � ����� ������
	"DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
	"DISPLAY_BOTTOM_PAGER" => "N",	// �������� ��� �������
	"PAGER_TITLE" => "������",	// �������� ���������
	"PAGER_SHOW_ALWAYS" => "Y",	// �������� ������
	"PAGER_TEMPLATE" => "",	// �������� �������
	"PAGER_DESC_NUMBERING" => "N",	// ������������ �������� ���������
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// ����� ����������� ������� ��� �������� ���������
	"PAGER_SHOW_ALL" => "Y",	// ���������� ������ "���"
	"AJAX_OPTION_ADDITIONAL" => "",	// �������������� �������������
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>