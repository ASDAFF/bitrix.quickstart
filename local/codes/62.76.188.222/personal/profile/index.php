<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?> 






<?$APPLICATION->IncludeComponent(
	"bitrix:main.profile",
	".default",
	Array(
		"SET_TITLE" => "Y"
	)
);?>





<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>