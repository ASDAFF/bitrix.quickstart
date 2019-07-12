<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "�������� ������� ������� �������, ������� �������, �������� ������� �������, ������� �������, �������, ������� ������� �������");
$APPLICATION->SetPageProperty("description", "�������� ������� ������� �������: ������ ������� ������� � ������ � ����� �������� ��������!");
$APPLICATION->SetTitle("��������-������� ������� �������");
?>
<?$APPLICATION->IncludeComponent("bitrix:news.list", "main_banner", Array(
	"DISPLAY_DATE" => "N",	// �������� ���� ��������
	"DISPLAY_NAME" => "Y",	// �������� �������� ��������
	"DISPLAY_PICTURE" => "Y",	// �������� ����������� ��� ������
	"DISPLAY_PREVIEW_TEXT" => "N",	// �������� ����� ������
	"AJAX_MODE" => "N",	// �������� ����� AJAX
	"IBLOCK_TYPE" => "services",	// ��� ��������������� ����� (������������ ������ ��� ��������)
	"IBLOCK_ID" => "#main_banners_IBLOCK_ID#",	// ��� ��������������� �����
	"NEWS_COUNT" => "1",	// ���������� �������� �� ��������
	"SORT_BY1" => "RND",	// ���� ��� ������ ���������� ��������
	"SORT_ORDER1" => "DESC",	// ����������� ��� ������ ���������� ��������
	"SORT_BY2" => "SORT",	// ���� ��� ������ ���������� ��������
	"SORT_ORDER2" => "ASC",	// ����������� ��� ������ ���������� ��������
	"FILTER_NAME" => "",	// ������
	"FIELD_CODE" => "",	// ����
	"PROPERTY_CODE" => array(	// ��������
		0 => "LINK",
	),
	"CHECK_DATES" => "Y",	// ���������� ������ �������� �� ������ ������ ��������
	"DETAIL_URL" => "",	// URL �������� ���������� ��������� (�� ��������� - �� �������� ���������)
	"PREVIEW_TRUNCATE_LEN" => "",	// ������������ ����� ������ ��� ������ (������ ��� ���� �����)
	"ACTIVE_DATE_FORMAT" => "d.m.Y",	// ������ ������ ����
	"SET_TITLE" => "N",	// ������������� ��������� ��������
	"SET_STATUS_404" => "N",	// ������������� ������ 404, ���� �� ������� ������� ��� ������
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// �������� �������� � ������� ���������
	"ADD_SECTIONS_CHAIN" => "N",	// �������� ������ � ������� ���������
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// �������� ������, ���� ��� ���������� ��������
	"PARENT_SECTION" => "",	// ID �������
	"PARENT_SECTION_CODE" => "",	// ��� �������
	"CACHE_TYPE" => "A",	// ��� �����������
	"CACHE_TIME" => "36000000",	// ����� ����������� (���.)
	"CACHE_FILTER" => "N",	// ���������� ��� ������������� �������
	"CACHE_GROUPS" => "Y",	// ��������� ����� �������
	"DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
	"DISPLAY_BOTTOM_PAGER" => "N",	// �������� ��� �������
	"PAGER_TITLE" => "�������",	// �������� ���������
	"PAGER_SHOW_ALWAYS" => "N",	// �������� ������
	"PAGER_TEMPLATE" => "",	// �������� �������
	"PAGER_DESC_NUMBERING" => "N",	// ������������ �������� ���������
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// ����� ����������� ������� ��� �������� ���������
	"PAGER_SHOW_ALL" => "N",	// ���������� ������ "���"
	"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
	"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
	"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
	),
	false
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/main_info.php"
	)
);?>
<?
$APPLICATION->IncludeComponent("smedia:store.catalog.top", "new", array(
	"IBLOCK_TYPE_ID" => "#igrushka_IBLOCK_TYPE#",
	"IBLOCK_ID" => array(
		0 => "#igrushka_IBLOCK_ID#",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "4",
	"LINE_ELEMENT_COUNT" => "2",
	"PROPERTY_CODE" => array(
		0 => "SALELEADER",
		1 => "SIZE",
		2 => "HEIGHT",
		3 => "COLOR",
		4 => "",
	),
	"FLAG_PROPERTY_CODE" => "NEWPRODUCT",
	"SECTION_URL" => "/catalog/?arrFilter_pf%5BNEWPRODUCT%5D=#NEWPRODUCT_enum_Y#&set_filter=%CF%EE%E4%EE%E1%F0%E0%F2%FC&set_filter=Y",
	"DETAIL_URL" => "",
	"BASKET_URL" => "#SITE_DIR#personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
		0 => "SIZE",
		1 => "HEIGHT",
		2 => "COLOR",
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"DISPLAY_IMG_WIDTH" => "154",
	"DISPLAY_IMG_HEIGHT" => "110",
	"SHARPEN" => "30"
	),
	false
);
?>

               <div class="wrapper">
               	<div class="col-1">
                  	<div class="box3 p2">
					    <?
$APPLICATION->IncludeComponent("smedia:store.catalog.top", "specialoffer", array(
	"IBLOCK_TYPE_ID" => "#igrushka_IBLOCK_TYPE#",
	"IBLOCK_ID" => array(
		0 => "#igrushka_IBLOCK_ID#",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "1",
	"LINE_ELEMENT_COUNT" => "1",
	"PROPERTY_CODE" => array(
		0 => "SIZE",
		1 => "HEIGHT",
		2 => "COLOR",
		3 => "",
	),
	"FLAG_PROPERTY_CODE" => "SPECIALOFFER",
	"SECTION_URL" => "/catalog/?arrFilter_pf%5BSPECIALOFFER%5D=#SPECIALOFFER_enum_Y#&set_filter=%CF%EE%E4%EE%E1%F0%E0%F2%FC&set_filter=Y",
	"DETAIL_URL" => "",
	"BASKET_URL" => "#SITE_DIR#personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
		0 => "SIZE",
		1 => "HEIGHT",
		2 => "COLOR",
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"DISPLAY_IMG_WIDTH" => "327",
	"DISPLAY_IMG_HEIGHT" => "245",
	"SHARPEN" => "30"
	),
	false
);
?>
                     </div>
                     <div class="box4">
<?$APPLICATION->IncludeComponent("bitrix:news.list", "info", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => "#info_IBLOCK_ID#",
	"NEWS_COUNT" => "1",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"DISPLAY_DATE" => "N",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
                     </div>
                  </div>
                  <div class="col-2">
                  	<div class="box5">
					    <?
$APPLICATION->IncludeComponent("smedia:store.catalog.top", "saleleader", array(
	"IBLOCK_TYPE_ID" => "#igrushka_IBLOCK_TYPE#",
	"IBLOCK_ID" => array(
		0 => "#igrushka_IBLOCK_ID#",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "4",
	"LINE_ELEMENT_COUNT" => "",
	"PROPERTY_CODE" => array(
		0 => "SIZE",
		1 => "HEIGHT",
		2 => "COLOR",
		3 => "",
	),
	"FLAG_PROPERTY_CODE" => "SALELEADER",
	"SECTION_URL" => "/catalog/?arrFilter_pf%5BSALELEADER%5D=#SALELEADER_enum_Y#&set_filter=%CF%EE%E4%EE%E1%F0%E0%F2%FC&set_filter=Y",
	"DETAIL_URL" => "",
	"BASKET_URL" => "#SITE_DIR#personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
		0 => "SIZE",
		1 => "HEIGHT",
		2 => "COLOR",
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"DISPLAY_IMG_WIDTH" => "61",
	"DISPLAY_IMG_HEIGHT" => "100",
	"SHARPEN" => "30"
	),
	false
);
?>
                     </div>
                  </div>
               </div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>