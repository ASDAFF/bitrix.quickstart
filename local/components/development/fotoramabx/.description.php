<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('COMPONENT_NAME'),
	'DESCRIPTION' => GetMessage('COMPONENT_DESCRIPTION'),
	'ICON' => '/images/component.gif',
	'PATH' => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
		'NAME' => GetMessage('CATEGORY_NAME'),
        "CHILD" => array(
            "ID" => "media",
            "NAME" => "Мультимедия",
            "SORT" => 30
        ),
	),
	'CACHE_PATH' => 'Y',
);