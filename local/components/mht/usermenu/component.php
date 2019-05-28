<?
	// Пользовательское меню — Пользовательско?? меню в заголов??е
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult = WP::cache(
		array(
			'c_mht_usermenu',
			$USER->GetID()
		),
		WP::time(10, 'm'),
		function() use ($USER){
			if(!$USER->GetID()){
				return array(
					'logged' => false
				);
			}

			return array(
				'logged' => true,
				'name' => $USER->GetFirstName()
			);
		}
	);

	$this->IncludeComponentTemplate();

?>