<?
if (array_key_exists('SET_ADDITIONAL_TITLE', $arResult) && !empty($arResult['SET_ADDITIONAL_TITLE']))
	$APPLICATION->SetPageProperty('title', $arResult['SET_ADDITIONAL_TITLE'] . ' &mdash; ' . $APPLICATION->GetTitle('title'));
