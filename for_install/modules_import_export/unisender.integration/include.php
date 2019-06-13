<?php
global $DB, $MESS, $APPLICATION;

define('ADMIN_MODULE_NAME', 'unisender');
define('ADMIN_MODULE_ICON', 'uni_menu_icon');

require_once(__DIR__ . '/classes/general/unisender.php');
require_once(__DIR__ . '/classes/general/unisenderAPI.php');

CModule::AddAutoloadClasses('unisender.integration', array(
		'Unisender' => __DIR__.'/classes/general/unisender.php',
		'UniAPI' => __DIR__.'/classes/general/unisenderAPI.php'
	)
);

IncludeModuleLangFile(__FILE__);
