<?
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	'%DOT%',
	array(
		'%CLASS%' => 'classes/general/%UNDER%.php',
	)
);

global $DBType;
?>