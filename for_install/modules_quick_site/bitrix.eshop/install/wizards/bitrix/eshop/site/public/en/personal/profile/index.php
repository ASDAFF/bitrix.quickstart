<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Personal Profile");
?><?$APPLICATION->IncludeComponent("bitrix:main.profile", "eshop_adapt", Array(
	"SET_TITLE" => "Y",	
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>