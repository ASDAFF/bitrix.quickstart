<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

if(!CModule::IncludeModule('iblock'))
	return;

$MODULE_ID = 'api.print';
/*
$arTypesEx = CIBlockParameters::GetIBlockTypes(Array('-' => ' '));
$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array('SORT' => 'ASC'), Array(
                                                       'SITE_ID' => $_REQUEST['site'],
                                                       'TYPE'    => ($arCurrentValues['IBLOCK_TYPE'] != '-' ? $arCurrentValues['IBLOCK_TYPE'] : '')
                                                  ));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes['ID']] = $arRes['NAME'];
*/

$arCurrentValues['IBLOCK_ID'] = COption::GetOptionString($MODULE_ID, 'PRINT_IBLOCK_ID');
$arProperty = array();
if(0 < intval($arCurrentValues['IBLOCK_ID']))
{
	$rsProp = CIBlockProperty::GetList(Array(
	                                        'sort' => 'asc',
	                                        'name' => 'asc'
	                                   ), Array(
	                                           'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
	                                           'ACTIVE'    => 'Y'
	                                      ));
	while($arr = $rsProp->Fetch())
	{
		if($arr['PROPERTY_TYPE'] != 'F')
			$arProperty[$arr['CODE']] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];
	}
}

$arComponentParameters = array(
	'GROUPS'     => array(
		'IMAGE_SETTINGS'   => array(
			'NAME' => GetMessage('GROUP_IMAGE_SETTINGS'),
		),
	),
	'PARAMETERS' => array(
		/*'IBLOCK_TYPE'    => Array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('IBLOCK_TYPE'),
			'TYPE'    => 'LIST',
			'VALUES'  => $arTypesEx,
			'DEFAULT' => '',
			'REFRESH' => 'Y',
		),
		'IBLOCK_ID'      => Array(
			'PARENT'            => 'BASE',
			'NAME'              => GetMessage('IBLOCK_ID'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arIBlocks,
			'DEFAULT'           => '',
			'ADDITIONAL_VALUES' => 'Y',
			'REFRESH'           => 'Y',
		),*/
		'FIELD_CODE'     => CIBlockParameters::GetFieldCode(GetMessage('FIELD_CODE'), 'ADDITIONAL_SETTINGS'),
		'PROPERTY_CODE'  => array(
			'PARENT'            => 'ADDITIONAL_SETTINGS',
			'NAME'              => GetMessage('PROPERTY_CODE'),
			'TYPE'              => 'LIST',
			'MULTIPLE'          => 'Y',
			'VALUES'            => $arProperty,
			'ADDITIONAL_VALUES' => 'Y',
		),
		'TEXT_TEMPLATE'  => Array(
			'PARENT'  => 'ADDITIONAL_SETTINGS',
			'NAME'    => GetMessage('TEXT_TEMPLATE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('TEXT_TEMPLATE_HTML'),
			'ROWS'    => '2',
		),
		'PRINT_FILE_URL' => Array(
			'PARENT'  => 'ADDITIONAL_SETTINGS',
			'NAME'    => GetMessage('PRINT_FILE_URL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '/ts_print.php',
		),
		'CSS_FILE_URL'   => Array(
			'PARENT'  => 'ADDITIONAL_SETTINGS',
			'NAME'    => GetMessage('CSS_FILE_URL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '/ts_print.css',
		),
		'CHECK_ACTIVE_SECTION' => Array(
			'NAME'    => GetMessage('CHECK_ACTIVE_SECTION'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT'  => 'ADDITIONAL_SETTINGS',
		),
		'INCLUDE_JQUERY'           => array(
			'PARENT'  => 'ADDITIONAL_SETTINGS',
			'NAME'    => GetMessage('INCLUDE_JQUERY'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		/*'ENABLE_PDF' => Array(
			'NAME'    => GetMessage('ENABLE_PDF'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT'  => 'ADDITIONAL_SETTINGS',
		),*/
		'RESIZE_PREVIEW_PICTURE'   => Array(
			'PARENT'  => 'IMAGE_SETTINGS',
			'NAME'    => GetMessage('RESIZE_PREVIEW_PICTURE'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		'PREVIEW_PICTURE_WIDTH'   => Array(
			'PARENT'  => 'IMAGE_SETTINGS',
			'NAME'    => GetMessage('PREVIEW_PICTURE_WIDTH'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '250',
		),
		'PREVIEW_PICTURE_HEIGHT'   => Array(
			'PARENT'  => 'IMAGE_SETTINGS',
			'NAME'    => GetMessage('PREVIEW_PICTURE_HEIGHT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '500',
		),
		'RESIZE_DETAIL_PICTURE'   => Array(
			'PARENT'  => 'IMAGE_SETTINGS',
			'NAME'    => GetMessage('RESIZE_DETAIL_PICTURE'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		'DETAIL_PICTURE_WIDTH'   => Array(
			'PARENT'  => 'IMAGE_SETTINGS',
			'NAME'    => GetMessage('DETAIL_PICTURE_WIDTH'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '250',
		),
		'DETAIL_PICTURE_HEIGHT'   => Array(
			'PARENT'  => 'IMAGE_SETTINGS',
			'NAME'    => GetMessage('DETAIL_PICTURE_HEIGHT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '500',
		),
		'PICTURE_ALIGN'  => array(
			'PARENT'            => 'IMAGE_SETTINGS',
			'NAME'              => GetMessage('PICTURE_ALIGN'),
			'TYPE'              => 'LIST',
			'MULTIPLE'          => 'N',
			'VALUES'            => array(
				'none' => GetMessage('PICTURE_ALIGN_NONE'),
				'left' => GetMessage('PICTURE_ALIGN_LEFT'),
				'right' => GetMessage('PICTURE_ALIGN_RIGHT'),
				'middle' => GetMessage('PICTURE_ALIGN_CENTER'),
			),
			'DEFAULT' => 'none',
			'ADDITIONAL_VALUES' => 'N',
		),
		'SET_PICTURE_BORDER'   => Array(
			'PARENT'  => 'IMAGE_SETTINGS',
			'NAME'    => GetMessage('SET_PICTURE_BORDER'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
	),
);