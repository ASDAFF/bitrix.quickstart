<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрационные данные");
?><?$APPLICATION->IncludeComponent("sergeland:main.profile", ".default", Array(
	"SET_TITLE" => "Y",
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>