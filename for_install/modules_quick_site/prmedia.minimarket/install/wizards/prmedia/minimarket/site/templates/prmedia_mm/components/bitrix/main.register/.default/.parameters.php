<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?

$arTemplateParameters = array(
	'PATH_TO_AUTH' => array(
		'NAME' => GetMessage('PATH_TO_AUTH'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'USE_CUSTOM_ORDER' => array(
		'NAME' => GetMessage('USE_CUSTOM_ORDER'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => '',
	),
);
?>