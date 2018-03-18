<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"ALLOW_SEND_ANY_NUM" => Array(
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'N',
			'NAME' => GetMessage('ALLOW_SEND_ANY_NUM_PARAM'),
			'PARENT' => 'BASE'
		),
		"SET_TITLE" => array(),
	)
);
?>