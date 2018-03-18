<?
global $DB, $MESS, $APPLICATION;

CModule::AddAutoloadClasses(
	"imyie.littleadmin",
	array(
		"CIMYIELittleAdmin" => "classes/general/main.php",
		"CIMYIELittleAdminStyle" => "classes/general/style.php",
		"CIMYIELittleAdminUtils" => "classes/general/utils.php",
	)
);
?>