<?
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/lookbook/([0-9a-zA-Z_-]+)/([0-9a-zA-Z_-]+)/.*#",
		"RULE" => "CODE=\$1&ELEMENT=\$2",
		"ID" => "",
		"PATH" => "/lookbook/detail.php",
	),
	array(
		"CONDITION" => "#^/campaign/([a-zA-Z1-9_\\-]+)/(.)*#",
		"RULE" => "ELEMENT_CODE=\$1",
		"ID" => "",
		"PATH" => "/campaign/detail.php",
	),
	array(
		"CONDITION" => "#^/lookbook/([0-9a-zA-Z_-]+)/.*#",
		"RULE" => "CODE=\$1",
		"ID" => "",
		"PATH" => "/lookbook/section.php",
	),
	array(
		"CONDITION" => "#^/brand/([a-zA-Z1-9_\\-]+)/(.)*#",
		"RULE" => "BRAND_CODE=\$1",
		"ID" => "",
		"PATH" => "/brand/detail.php",
	),
	array(
		"CONDITION" => "#^/bitrix/services/ymarket/#",
		"RULE" => "",
		"ID" => "",
		"PATH" => "/bitrix/services/ymarket/index.php",
	),
	array(
		"CONDITION" => "#^/about/vacancies/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/about/vacancies/index.php",
	),
	array(
		"CONDITION" => "#^/personal/order/#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.order",
		"PATH" => "/personal/order/index.php",
	),
	array(
		"CONDITION" => "#^/journal/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/journal/index.php",
	),
	array(
		"CONDITION" => "#^/catalog/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/catalog/index.php",
	),
	array(
		"CONDITION" => "#^/store/#",
		"RULE" => "",
		"ID" => "bitrix:catalog.store",
		"PATH" => "/store/index.php",
	),
	array(
		"CONDITION" => "#^/news/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/news/index.php",
	),
);

?>