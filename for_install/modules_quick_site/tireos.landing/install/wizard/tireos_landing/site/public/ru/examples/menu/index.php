<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Примеры меню");
?><?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"blue_tabs",
	Array(
		"ROOT_MENU_TYPE" => "top", 
		"MAX_LEVEL" => "1", 
		"CHILD_MENU_TYPE" => "left", 
		"USE_EXT" => "N" 
	)
);?>&nbsp; 
<br />
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"grey_tabs",
	Array(
		"ROOT_MENU_TYPE" => "top", 
		"MAX_LEVEL" => "1", 
		"CHILD_MENU_TYPE" => "left", 
		"USE_EXT" => "N" 
	)
);?>&nbsp; 
<br />
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"tree",
	Array(
		"ROOT_MENU_TYPE" => "top", 
		"MAX_LEVEL" => "3", 
		"CHILD_MENU_TYPE" => "left", 
		"USE_EXT" => "N" 
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>