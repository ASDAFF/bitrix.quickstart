<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$arTemplateParameters = array(
	'SHOW_SECTION_PICTURE' => array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => getMessage('RS_SLINE.BC_AL_P.SHOW_SECTION_PICTURE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	),
);

if('Y' == $arCurrentValues['SHOW_SECTION_PICTURE']){
	$arTemplateParameters['SECTION_PICTURE_WIDTH'] = array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => getMessage('RS_SLINE.BC_AL_P.SECTION_PICTURE_WIDTH'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);
	$arTemplateParameters['SECTION_PICTURE_HEIGHT'] = array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => getMessage('RS_SLINE.BC_AL_P.SECTION_PICTURE_HEIGHT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);
}