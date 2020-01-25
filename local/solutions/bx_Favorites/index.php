<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Избранное");
    use Bitrix\Main\Application;
    use Bitrix\Main\Web\Cookie;
$application = Application::getInstance();
$context = $application->getContext(); 
?>

<div class="inner container favorites">
	<h1>Избранное</h1>
<?
if(!$USER->IsAuthorized()) // Для неавторизованного
{
    global $APPLICATION;
	$favorites = unserialize($APPLICATION->get_cookie('favorites'));
}
else {
     $idUser = $USER->GetID();
     $rsUser = CUser::GetByID($idUser);
     $arUser = $rsUser->Fetch();
     $favorites = $arUser['UF_FAVORITES'];
    
}
if(!is_array($favorites)) {
	unset($favorites);
}

$GLOBALS['arrFilter']=Array("ID" => $favorites);
if(count($favorites) > 0 && is_array($favorites)):
	$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	".default", 
	array(
		"ACTION_VARIABLE" => "action",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/personal/cart/",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COMPATIBLE_MODE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_COMPARE" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FILTER_NAME" => "arrFilter",
		"HIDE_NOT_AVAILABLE" => "N",
		"HIDE_NOT_AVAILABLE_OFFERS" => "Y",
		"IBLOCK_ID" => "2",
		"IBLOCK_TYPE" => "catalog",
		"INCLUDE_SUBSECTIONS" => "Y",
		"LAZY_LOAD" => "N",
		"LINE_ELEMENT_COUNT" => "3",
		"LOAD_ON_SCROLL" => "N",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"OFFERS_LIMIT" => "5",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "swiss",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "9",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => "",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
		"RCM_TYPE" => "personal",
		"SECTION_CODE" => "",
		"SECTION_ID" => "",
		"SECTION_ID_VARIABLE" => "SECTION_CODE",
		"SECTION_URL" => "/catalog/#SECTION_CODE#",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SEF_MODE" => "N",
		"SET_BROWSER_TITLE" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "Y",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "Y",
		"SHOW_404" => "N",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SHOW_CLOSE_POPUP" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_FROM_SECTION" => "N",
		"SHOW_MAX_QUANTITY" => "N",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"TEMPLATE_THEME" => "site",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"COMPONENT_TEMPLATE" => ".default",
		"CUSTOM_FILTER" => "",
		"PROPERTY_CODE_MOBILE" => array(
		),
		"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
		"ENLARGE_PRODUCT" => "STRICT",
		"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
		"SHOW_SLIDER" => "Y",
		"ADD_PICT_PROP" => "-",
		"LABEL_PROP" => array(
		),
		"SLIDER_INTERVAL" => "3000",
		"SLIDER_PROGRESS" => "N",
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "desc",
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"PRODUCT_DISPLAY_MODE" => "N"
	),
	false
);
else:
?>
<div class="inner">
	<div class="center">
		<div class="waitingfor">
			Вы не добавляли товары в избранное.			
		</div>
	</div>
</div>

<?endif;?>

</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
