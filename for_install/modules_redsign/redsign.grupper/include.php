<?
global $DBType, $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	"redsign.grupper",
	array(
		"CRSGrupper" => "classes/general/main.php",
		"CRSGGroups" => "classes/".$DBType."/groups.php",
		"CRSGBinds" => "classes/".$DBType."/binds.php",
	)
);
?>