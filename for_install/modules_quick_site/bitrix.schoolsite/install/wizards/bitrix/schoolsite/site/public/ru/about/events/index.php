<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("�����������");
?><?$APPLICATION->IncludeComponent("bitrix:news", "events", Array(
	"IBLOCK_TYPE" => "news",	// ��� ����-�����
	"IBLOCK_ID" => "#EVENTS_IBLOCK_ID#",	// ����-����
	"NEWS_COUNT" => "20",	// ���������� �������� �� ��������
	"USE_SEARCH" => "N",	// ��������� �����
	"USE_RSS" => "N",	// ��������� RSS
	"USE_RATING" => "N",	// ��������� �����������
	"USE_CATEGORIES" => "N",	// �������� ��������� �� ����
	"USE_REVIEW" => "N",	// ��������� ������
	"USE_FILTER" => "N",	// ���������� ������
	"SORT_BY1" => "ACTIVE_FROM",	// ���� ��� ������ ���������� ��������
	"SORT_ORDER1" => "DESC",	// ����������� ��� ������ ���������� ��������
	"SORT_BY2" => "SORT",	// ���� ��� ������ ���������� ��������
	"SORT_ORDER2" => "ASC",	// ����������� ��� ������ ���������� ��������
	"CHECK_DATES" => "N",	// ���������� ������ �������� �� ������ ������ ��������
	"SEF_MODE" => "Y",	// �������� ��������� ���
	"SEF_FOLDER" => "#SITE_DIR#about/events/",	// ������� ��� (������������ ����� �����)
	"AJAX_MODE" => "N",	// �������� ����� AJAX
	"AJAX_OPTION_SHADOW" => "Y",	// �������� ���������
	"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
	"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
	"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
	"CACHE_TYPE" => "A",	// ��� �����������
	"CACHE_TIME" => "36000000",	// ����� ����������� (���.)
	"CACHE_FILTER" => "N",	// ���������� ��� ������������� �������
	"CACHE_GROUPS" => "Y",	// ��������� ����� �������
	"SET_TITLE" => "Y",	// ������������� ��������� ��������
	"SET_STATUS_404" => "N",	// ������������� ������ 404, ���� �� ������� ������� ��� ������
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// �������� �������� � ������� ���������
	"ADD_SECTIONS_CHAIN" => "N",	// �������� ������ � ������� ���������
	"USE_PERMISSIONS" => "N",	// ������������ �������������� ����������� �������
	"PREVIEW_TRUNCATE_LEN" => "",	// ������������ ����� ������ ��� ������ (������ ��� ���� �����)
	"LIST_ACTIVE_DATE_FORMAT" => "j F Y",	// ������ ������ ����
	"LIST_FIELD_CODE" => array(	// ����
		0 => "",
		1 => "",
	),
	"LIST_PROPERTY_CODE" => array(	// ��������
		0 => "",
		1 => "",
	),
	"HIDE_LINK_WHEN_NO_DETAIL" => "Y",	// �������� ������, ���� ��� ���������� ��������
	"DISPLAY_NAME" => "N",	// �������� �������� ��������
	"META_KEYWORDS" => "-",	// ���������� �������� ����� �������� �� ��������
	"META_DESCRIPTION" => "-",	// ���������� �������� �������� �� ��������
	"BROWSER_TITLE" => "-",	// ���������� ��������� ���� �������� �� ��������
	"DETAIL_ACTIVE_DATE_FORMAT" => "j F Y",	// ������ ������ ����
	"DETAIL_FIELD_CODE" => array(	// ����
		0 => "",
		1 => "",
	),
	"DETAIL_PROPERTY_CODE" => array(	// ��������
		0 => "",
		1 => "",
	),
	"DETAIL_DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
	"DETAIL_DISPLAY_BOTTOM_PAGER" => "N",	// �������� ��� �������
	"DETAIL_PAGER_TITLE" => "��������",	// �������� ���������
	"DETAIL_PAGER_TEMPLATE" => "",	// �������� �������
	"DETAIL_PAGER_SHOW_ALL" => "N",	// ���������� ������ "���"
	"DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
	"DISPLAY_BOTTOM_PAGER" => "Y",	// �������� ��� �������
	"PAGER_TITLE" => "�����������",	// �������� ���������
	"PAGER_SHOW_ALWAYS" => "N",	// �������� ������
	"PAGER_TEMPLATE" => "",	// �������� �������
	"PAGER_DESC_NUMBERING" => "N",	// ������������ �������� ���������
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// ����� ����������� ������� ��� �������� ���������
	"PAGER_SHOW_ALL" => "N",	// ���������� ������ "���"
	"DISPLAY_DATE" => "Y",	// �������� ���� ��������
	"DISPLAY_PICTURE" => "Y",	// �������� ����������� ��� ������
	"DISPLAY_PREVIEW_TEXT" => "Y",	// �������� ����� ������
	"USE_SHARE" => "N",	// ���������� ������ ���. ��������
	"AJAX_OPTION_ADDITIONAL" => "",	// �������������� �������������
	"SEF_URL_TEMPLATES" => array(
		"news" => "",
		"section" => "",
		"detail" => "#ELEMENT_ID#/",
		"search" => "search/",
		"rss" => "rss/",
		"rss_section" => "#SECTION_ID#/rss/",
	)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>