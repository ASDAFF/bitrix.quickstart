<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("��������");
?><?$APPLICATION->IncludeComponent("bitrix:catalog", "subjects", Array(
	"AJAX_MODE" => "N",	// �������� ����� AJAX
	"SEF_MODE" => "Y",	// �������� ��������� ���
	"IBLOCK_TYPE" => "school",	// ��� ����-�����
	"IBLOCK_ID" => "#SUBJECTS_IBLOCK_ID#",	// ����-����
	"USE_FILTER" => "N",	// ���������� ������
	"USE_REVIEW" => "N",	// ��������� ������
	"USE_COMPARE" => "N",	// ������������ ��������� ���������
	"SHOW_TOP_ELEMENTS" => "N",	// �������� ��� ���������
	"PAGE_ELEMENT_COUNT" => "20",	// ���������� ��������� �� ��������
	"LINE_ELEMENT_COUNT" => "1",	// ���������� ���������, ��������� � ����� ������ �������
	"ELEMENT_SORT_FIELD" => "sort",	// �� ������ ���� ��������� ������ � �������
	"ELEMENT_SORT_ORDER" => "asc",	// ������� ���������� ������� � �������
	"LIST_PROPERTY_CODE" => "",	// ��������
	"INCLUDE_SUBSECTIONS" => "Y",	// ���������� �������� ����������� �������
	"LIST_META_KEYWORDS" => "-",	// ���������� �������� ����� �������� �� �������� �������
	"LIST_META_DESCRIPTION" => "-",	// ���������� �������� �������� �� �������� �������
	"LIST_BROWSER_TITLE" => "-",	// ���������� ��������� ���� �������� �� �������� �������
	"DETAIL_PROPERTY_CODE" => array(
		0 => "TEACHERS",
		1 => "",
	),
	"DETAIL_META_KEYWORDS" => "-",	// ���������� �������� ����� �������� �� ��������
	"DETAIL_META_DESCRIPTION" => "-",	// ���������� �������� �������� �� ��������
	"DETAIL_BROWSER_TITLE" => "-",	// ���������� ��������� ���� �������� �� ��������
	"BASKET_URL" => "/personal/basket.php",	// URL, ������� �� �������� � �������� ����������
	"ACTION_VARIABLE" => "action",	// �������� ����������, � ������� ���������� ��������
	"PRODUCT_ID_VARIABLE" => "id",	// �������� ����������, � ������� ���������� ��� ������ ��� �������
	"SECTION_ID_VARIABLE" => "SECTION_ID",	// �������� ����������, � ������� ���������� ��� ������
	"CACHE_TYPE" => "A",	// ��� �����������
	"CACHE_TIME" => "36000000",	// ����� ����������� (���.)
	"CACHE_FILTER" => "N",	// ���������� ��� ������������� �������
	"CACHE_GROUPS" => "Y",	// ��������� ����� �������
	"SET_TITLE" => "Y",	// ������������� ��������� ��������
	"SET_STATUS_404" => "N",	// ������������� ������ 404, ���� �� ������� ������� ��� ������
	"PRICE_CODE" => "",	// ��� ����
	"USE_PRICE_COUNT" => "N",	// ������������ ����� ��� � �����������
	"SHOW_PRICE_COUNT" => "1",	// �������� ���� ��� ����������
	"PRICE_VAT_INCLUDE" => "Y",	// �������� ��� � ����
	"PRICE_VAT_SHOW_VALUE" => "N",	// ���������� �������� ���
	"LINK_IBLOCK_TYPE" => "",	// ��� ����-�����, �������� �������� ������� � ������� ���������
	"LINK_IBLOCK_ID" => "",	// ID ����-�����, �������� �������� ������� � ������� ���������
	"LINK_PROPERTY_SID" => "",	// ��������, � ������� �������� �����
	"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",	// URL �� �������� ��� ����� ������� ������ ��������� ���������
	"DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
	"DISPLAY_BOTTOM_PAGER" => "Y",	// �������� ��� �������
	"PAGER_TITLE" => "��������",	// �������� ���������
	"PAGER_SHOW_ALWAYS" => "N",	// �������� ������
	"PAGER_TEMPLATE" => "",	// �������� �������
	"PAGER_DESC_NUMBERING" => "N",	// ������������ �������� ���������
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// ����� ����������� ������� ��� �������� ���������
	"PAGER_SHOW_ALL" => "N",	// ���������� ������ "���"
	"AJAX_OPTION_SHADOW" => "Y",	// �������� ���������
	"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
	"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
	"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
	"SEF_FOLDER" => "#SITE_DIR#about/subjects/",	// ������� ��� (������������ ����� �����)
	"SEF_URL_TEMPLATES" => array(
		"section" => "#SECTION_ID#/",
		"element" => "#SECTION_ID#/#ELEMENT_ID#/",
		"compare" => "compare.php?action=#ACTION_CODE#",
	),
	"VARIABLE_ALIASES" => array(
		"section" => "",
		"element" => "",
		"compare" => array(
			"ACTION_CODE" => "action",
		),
	)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>