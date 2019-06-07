<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/** @var array $arCurrentValues */

$arTemplateParameters = array(
	'TEMPLATE_STYLE' => array(
		'NAME'              => GetMessage('TEMPLATE_STYLE'),
		'TYPE'              => 'LIST',
		'VALUES'            => Array(
			'uikit'        => 'uikit',
			'uikit-light'  => 'uikit-light',
			'uikit-chosen' => 'uikit-chosen',
			'bootstrap3'   => 'Bootstrap 3',
		),
		'DEFAULT'           => 'uikit',
		'ADDITIONAL_VALUES' => 'Y',
		'REFRESH'           => 'N',
		'PARENT'            => 'VISUAL',
	),

	'SHOW_CSS_MODAL_AFTER_SEND' => Array(
		'PARENT'  => 'CSS_MODAL_SETTINGS',
		'NAME'    => GetMessage('SHOW_CSS_MODAL_AFTER_SEND'),
		'TYPE'    => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	),
);

if($arCurrentValues['SHOW_CSS_MODAL_AFTER_SEND'] == 'Y')
{
	$arTemplateParameters['CSS_MODAL_HEADER']  = Array(
		'NAME'    => GetMessage('CSS_MODAL_HEADER'),
		'TYPE'    => 'STRING',
		'DEFAULT' => GetMessage('CSS_MODAL_HEADER_TXT'),
		'PARENT'  => 'CSS_MODAL_SETTINGS',
	);
	$arTemplateParameters['CSS_MODAL_FOOTER']  = Array(
		'NAME'    => GetMessage('CSS_MODAL_FOOTER'),
		'TYPE'    => 'STRING',
		'DEFAULT' => GetMessage('CSS_MODAL_FOOTER_TXT'),
		'PARENT'  => 'CSS_MODAL_SETTINGS',
	);
	$arTemplateParameters['CSS_MODAL_CONTENT'] = Array(
		'NAME'    => GetMessage('CSS_MODAL_CONTENT'),
		'TYPE'    => 'STRING',
		'DEFAULT' => GetMessage('CSS_MODAL_CONTENT_TXT'),
		'ROWS'    => 4,
		'COLS'    => 50,
		'PARENT'  => 'CSS_MODAL_SETTINGS',
	);
}