<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

// accessories
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/omponents/bitrix/catalog.section/light/style.css',true);
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/omponents/bitrix/catalog.section/light/script.js',true);

// grouped props
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/omponents/redsign/grupper.list/gopro/style.css',true);

// set
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/omponents/bitrix/catalog.set.constructor/gopro/style.css',true);
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/omponents/bitrix/catalog.set.constructor/gopro/script.js',true);