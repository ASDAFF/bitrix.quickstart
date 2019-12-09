<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>
<?php
use \Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

$bIsAjax = (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    $request->get('rs_ajax') == 'Y' ||
    $request->get('rs_ajax__page') == 'Y'
);
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.register", 
	"al", 
	array(
		"COMPONENT_TEMPLATE" => "al",
		"SHOW_FIELDS" => array(
		),
		"REQUIRED_FIELDS" => array(
		),
		"AUTH" => "Y",
		"USE_BACKURL" => "N",
		"SUCCESS_PAGE" => $bIsAjax ? "":"#SITE_DIR#auth/?register=yes",
		"SET_TITLE" => "Y",
		"USER_PROPERTY" => array(
		),
		"USER_PROPERTY_NAME" => "",
		"AUTH_AUTH_URL" => "#SITE_DIR#auth/",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>