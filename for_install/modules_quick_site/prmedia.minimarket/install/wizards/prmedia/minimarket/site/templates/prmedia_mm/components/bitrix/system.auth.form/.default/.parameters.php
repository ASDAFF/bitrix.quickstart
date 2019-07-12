<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

$arTemplateParameters = array(
	'PRMEDIA_AUTH_FORM_SHOW_TITLE' => Array(
		'NAME' => GetMessage('PRMEDIA_AUTH_FORM_SHOW_TITLE_NAME'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y'
	),
	'PRMEDIA_AUTH_FORM_REDIRECT_URL' => Array(
		'NAME' => GetMessage('PRMEDIA_AUTH_FORM_REDIRECT_URL_NAME'),
		'TYPE' => 'STRING'
	),
);