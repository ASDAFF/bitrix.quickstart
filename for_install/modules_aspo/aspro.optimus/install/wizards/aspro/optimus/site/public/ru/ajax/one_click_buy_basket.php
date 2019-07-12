<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?$APPLICATION->IncludeComponent("aspro:oneclickbuy.optimus", "shop", array(
	"BUY_ALL_BASKET" => "Y",
	"CACHE_TYPE" => "Y",
	"CACHE_TIME" => "3600000",
	"CACHE_GROUPS" => "N",
	"PROPERTIES" => (strlen($tmp = COption::GetOptionString('aspro.optimus', 'ONECLICKBUY_PROPERTIES', 'FIO,PHONE,EMAIL,COMMENT', SITE_ID)) ? explode(',', $tmp) : array()),
	"REQUIRED" => (strlen($tmp = COption::GetOptionString('aspro.optimus', 'ONECLICKBUY_REQUIRED_PROPERTIES', 'FIO,PHONE', SITE_ID)) ? explode(',', $tmp) : array()),
	"DEFAULT_PERSON_TYPE" => COption::GetOptionString('aspro.optimus', 'ONECLICKBUY_PERSON_TYPE', '1', SITE_ID),
	"DEFAULT_DELIVERY" => COption::GetOptionString('aspro.optimus', 'ONECLICKBUY_DELIVERY', '2', SITE_ID),
	"DEFAULT_PAYMENT" => COption::GetOptionString('aspro.optimus', 'ONECLICKBUY_PAYMENT', '1', SITE_ID),
	"DEFAULT_CURRENCY" => COption::GetOptionString('aspro.optimus', 'ONECLICKBUY_CURRENCY', 'RUB', SITE_ID),
	),
	false
);?>