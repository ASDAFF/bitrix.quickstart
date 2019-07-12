<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;?>

<?$APPLICATION->IncludeComponent(
	"v1rt.personal:comments",
	"comment",
	Array(
		"IBLOCK_TYPE" => "personal",
		"ID_IBLOCK" => "#COMMENTS_IBLOCK_ID#",
		"PROPERTY" => "ID_RECORD",
		"ID_RECORD" => $arResult["ID"]
	),
false
);?> 