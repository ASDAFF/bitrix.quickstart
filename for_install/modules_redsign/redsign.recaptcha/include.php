<?
global $DBType, $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	"redsign.recaptcha",
	array(
		"redsign_recaptcha" => "install/index.php",
	)
);

?>