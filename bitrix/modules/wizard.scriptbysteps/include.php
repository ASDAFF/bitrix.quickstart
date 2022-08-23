<?
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

\Bitrix\Main\Loader::registerAutoLoadClasses(
	"wizard.scriptbysteps",
	array(
		"\\Wizard\\ScriptBySteps" => "lib/scriptBySteps.php",
	)
);