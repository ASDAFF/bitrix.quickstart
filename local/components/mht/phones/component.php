<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult['PHONES'] = WP::cache('c_phones_'.WP::get('region')->cur()->prop('code'), null, function(){
        $region = WP::get('region')->cur();
		return array(
			'global' => WP::get('var')->phone('main-phone'),
			'local' => WP::parsePhone($region->prop('phones'))
		);	
	});

	$this->IncludeComponentTemplate();
?>