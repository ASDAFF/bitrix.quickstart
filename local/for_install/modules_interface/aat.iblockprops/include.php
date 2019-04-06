<?

CModule::AddAutoloadClasses(
	'aat.iblockprops',
	array(
		'CAATIBlockPropSection' => 'classes/general/iblock_prop_section.php',
		'CAATIBlockPropElement' => 'classes/general/iblock_prop_element.php',
	)
);

$arJSConfig = array(
	'js' => '/bitrix/js/aat.iblockprops/interface.js',
	'css' => '/bitrix/css/aat.iblockprops/interface.css',
	'rel' => array('jquery'),
);

CJSCore::RegisterExt('aat_iblockprops', $arJSConfig);