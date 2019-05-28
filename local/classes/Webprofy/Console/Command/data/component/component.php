<?
	// %RU_NAME% — %RU_DESC%
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult = WP::cache(
		array(
			'c_%UNDER%'%LAST_UPDATE%
		),
		WP::time(%CACHE_TIME%),
		function(){
			%EXAMPLE_COMPONENT%
		}
	);

	$this->IncludeComponentTemplate();

?>