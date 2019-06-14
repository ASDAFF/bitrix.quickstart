<?
IncludeModuleLangFile(__FILE__);

global $DB;
$db_type = strtolower($DB->type);
CModule::AddAutoloadClasses(
	"d2mg.ufhtml",
	array(
		"CCustomTypeHtml" => "classes/general/customtypehtml.php",
	)
);

?>