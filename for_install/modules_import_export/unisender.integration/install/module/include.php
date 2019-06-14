<?
global $DB, $MESS, $APPLICATION;

define("ADMIN_MODULE_NAME", "unisender");
define("ADMIN_MODULE_ICON", "uni_menu_icon");

require_once (dirname(__FILE__)."/classes/general/unisender.php");

CModule::AddAutoloadClasses("triggmine", array(
		"UniAPI" => dirname(__FILE__)."/classes/general/unisender.php"
	)
);

$module_id = "unisender.integration";

IncludeModuleLangFile(__FILE__);
?>