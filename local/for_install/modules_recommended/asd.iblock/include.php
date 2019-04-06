<?php

if (!function_exists('htmlspecialcharsbx')) {
	function htmlspecialcharsbx($string, $flags=ENT_COMPAT) {
		return htmlspecialchars($string, $flags, (defined('BX_UTF')? 'UTF-8' : 'ISO-8859-1'));
	}
}


CModule::AddAutoloadClasses(
	'asd.iblock',
	array(
		'CASDiblock' => 'classes/general/iblock_interface.php',
		'CASDiblockInterface' => 'classes/general/iblock_interface.php',
		'CASDiblockAction' => 'classes/general/iblock_action.php',
		'CASDiblockTools' => 'classes/general/iblock_tools.php',
		'CASDIblockElementTools' => 'classes/general/iblock_tools.php',
		'CASDiblockPropCheckbox' => 'classes/general/iblock_prop_checkbox.php',
		'CASDiblockPropCheckboxNum' => 'classes/general/iblock_prop_checkbox_num.php',
		'CASDiblockPropPalette' => 'classes/general/iblock_prop_palette.php',
		'CASDiblockPropSection' => 'classes/general/iblock_prop_section.php',
		'CASDIblockRights' => 'classes/general/iblock_rights.php',
		'CASDiblockVersion' => 'classes/general/iblock_version.php'
	)
);

$arJSAsdIBlockConfig = array(
	'asd_iblock' => array(
		'js' => '/bitrix/js/asd.iblock/script.js',
		'css' => '/bitrix/panel/asd.iblock/interface.css',
		'rel' => array('jquery'),
	),
	'asd_palette' => array(
		'js' => '/bitrix/js/asd.iblock/jpicker/jpicker-1.1.6.min.js',
		'css' => '/bitrix/js/asd.iblock/jpicker/css/jPicker-1.1.6.min.css',
		'rel' => array('jquery'),
	),
);

foreach ($arJSAsdIBlockConfig as $ext => $arExt) {
	CJSCore::RegisterExt($ext, $arExt);
}