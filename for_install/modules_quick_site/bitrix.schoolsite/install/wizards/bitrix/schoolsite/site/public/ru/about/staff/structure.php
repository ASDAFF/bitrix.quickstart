<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Коллектив");
?><?$APPLICATION->IncludeComponent("bitrix:intranet.structure.list", "list", array(
	"FILTER_1C_USERS" => "N",
	"FILTER_SECTION_CURONLY" => "Y",
	"NAME_TEMPLATE" => "#NOBR##LAST_NAME# #NAME##/NOBR#",
	"SHOW_ERROR_ON_NULL" => "Y",
	"USERS_PER_PAGE" => "10",
	"NAV_TITLE" => "Сотрудники",
	"SHOW_NAV_TOP" => "Y",
	"SHOW_NAV_BOTTOM" => "Y",
	"SHOW_UNFILTERED_LIST" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"FILTER_NAME" => "structure",
	"PM_URL" => "/company/personal/messages/chat/#USER_ID#/",
	"USER_PROPERTY" => array(
		0 => "EMAIL",
		1 => "PERSONAL_PHONE",
		2 => "UF_DEPARTMENT",
		3 => "UF_SUBJECTS",
	)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>