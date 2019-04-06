<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$arInterfaces = array();
if (CModule::IncludeModule('webdebug.reviews')) {
	$resInterfaces = CWD_Reviews2_Interface::GetList(array('SORT'=>'ASC','ID'=>'ASC'));
	while ($arInterface = $resInterfaces->GetNext(false,false)) {
		$arInterfaces[$arInterface['ID']] = $arInterface['NAME'];
	}
}

$arComponentParameters = array(
	'PARAMETERS' => array(
		'CACHE_TIME' => array('DEFAULT'=>3600),
		'INTERFACE_ID' => array(
			'NAME' => GetMessage('WD_REVIEWS2_INTERFACE'),
			'TYPE' => 'LIST',
			'VALUES' => $arInterfaces,
			'PARENT' => 'BASE',
		),
		'TARGET_SUFFIX' => array(
			'NAME' => GetMessage('WD_REVIEWS2_TARGET_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'' => GetMessage('WD_REVIEWS2_TARGET_TYPE_DEFAULT'),
				'E_' => GetMessage('WD_REVIEWS2_TARGET_TYPE_ELEMENT'),
			),
			'PARENT' => 'BASE',
		),
		'TARGET' => array(
			'NAME' => GetMessage('WD_REVIEWS2_TARGET'),
			'TYPE' => 'TEXT',
			'PARENT' => 'BASE',
		),
		'MANUAL_CSS_INCLUDE' => array(
			'NAME' => GetMessage('WD_REVIEWS2_MANUAL_CSS_INCLUDE'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'BASE',
			'DEFAULT' => 'N',
			'PARENT' => 'BASE',
		),
		'MINIMIZE_FORM' => array(
			'NAME' => GetMessage('WD_REVIEWS2_MINIMIZE_FORM'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'DEFAULT' => 'Y',
		),
	),
);

?>