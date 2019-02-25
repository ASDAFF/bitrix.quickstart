<?
global $DBType, $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	'redsign.easycart',
	array(
		'CRSEasyCartMain' => "classes/general/main.php",
	)
);