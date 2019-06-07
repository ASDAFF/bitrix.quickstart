<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$arComponentDescription = array(
	'NAME'        => GetMessage('API_SEARCH_CATALOG_CP_NAME'),
	'DESCRIPTION' => GetMessage('API_SEARCH_CATALOG_CP_DESCRIPTION'),
	'ICON'        => GetMessage('API_SEARCH_CATALOG_CP_ICON'),
	'SORT'        => GetMessage('API_SEARCH_CATALOG_CP_SORT'),
	'COMPLEX'     => GetMessage('API_SEARCH_CATALOG_CP_COMPLEX'),
	'CACHE_PATH'  => GetMessage('API_SEARCH_CATALOG_CP_CACHE_PATH'),
	'PATH'        => array(
		'ID'    => GetMessage('API_SEARCH_CATALOG_CP_PATH_ID'),
		'NAME'  => GetMessage('API_SEARCH_CATALOG_CP_PATH_NAME'),
		'CHILD' => array(
			'ID'   => GetMessage('API_SEARCH_CATALOG_CP_CHILD_ID'),
			'NAME' => GetMessage('API_SEARCH_CATALOG_CP_CHILD_NAME'),
		),
	),
);