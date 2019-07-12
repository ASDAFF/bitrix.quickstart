
</div>
<div id = "right">
<?$APPLICATION->IncludeComponent("bitrix:main.include", "connect", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "#SITE_DIR#include/connect.php",
	"EDIT_TEMPLATE" => "",
	"BLOCK_TITLE" => "#FEEDBACK_TITLE#",
	"BLOCK_TYPE" => "connect",
	"BLOCK_LINK" => "#SITE_DIR#contacts/"
	),
	false
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "connect", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "#SITE_DIR#include/online.php",
	"EDIT_TEMPLATE" => "",
	"BLOCK_TITLE" => "#ONLINEFORM_TITLE#",
	"BLOCK_TYPE" => "online",
	"BLOCK_LINK" => "#SITE_DIR#services/orderform/"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "#ORDERFORM_ACTIVE#"
	)
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "connect", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "#SITE_DIR#include/control.php",
	"EDIT_TEMPLATE" => "",
	"BLOCK_TITLE" => "#QUALITY_CONTROL_TITLE#",
	"BLOCK_TYPE" => "control",
	"BLOCK_LINK" => "#SITE_DIR#contacts/quality/"
	),
	false
);?>
</div>
</div>
</div>
<div id = "footer">
<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom_menu", Array(
	"ROOT_MENU_TYPE" => "top",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => "",
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N",
	),
	false
);?>
<div id = "footer_bot">
<div id = "f_buttons">
<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "#SITE_DIR#include/buttons.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
</div>
<div id = "f_contacts">
<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "#SITE_DIR#include/contacts.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
</div>
<div id = "f_copy">
<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "#SITE_DIR#include/copy.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
</div>
</div>
<div id = "itees_copy">
<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "#SITE_DIR#include/itees_copy.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
</div>
</div>
</div>
<?
global $USER;
if($USER->IsAdmin()){
	$GLOBALS["APPLICATION"]->AddPanelButton(array(
		"HREF" => "/bitrix/admin/wizard_install.php?lang=ru&wizardName=itees:ffc&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
		"ID" => "ffc",
		"ICON" => "icon-wizard",
		"ALT" => "#WIZARD_BUTTON_DESCRIPTION#",
		"TEXT" => "#WIZARD_BUTTON_NAME#",
		"MAIN_SORT" => 550,
		"SORT" => 30,
		"MENU" => array(),
	));
}
?>
</body>
</html>