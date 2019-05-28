<?
	WP::loadScript('/js_/catalog.js');

	if($arParams['ADD_CHAIN'] == 'Y'){
		$APPLICATION->SetTitle("Продукты бренда ".$templateData['NAME']);
        $APPLICATION->AddChainItem($templateData['NAME'], '/brand/'.$templateData['CODE'].'/');
    }