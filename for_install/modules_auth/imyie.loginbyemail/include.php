<?
global $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	"imyie.loginbyemail",
	array(
		"CIMYIELoginByEmail" => "classes/general/authbyemail.php",
		"CIMYIELoginByLink" => "classes/general/authbylink.php",
	)
);
?>