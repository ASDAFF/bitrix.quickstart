<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	
	
	/** @var CBitrixComponent $this */
	/** @var array $arParams */
	/** @var array $arResult */
	/** @var string $componentPath */
	/** @var string $componentName */
	/** @var string $componentTemplate */
	/** @global CDatabase $DB */
	/** @global CUser $USER */
	/** @global CMain $APPLICATION */

	if(!CModule::IncludeModule("iblock")) return;
	
	//Значения по умолчанию
	$arParams['WIDTH']=str_replace(' ', '', $arParams['WIDTH']);
	$arParams['WIDTH']=(stristr($arParams['WIDTH'], '%') === FALSE and !empty($arParams['WIDTH'])?$arParams['WIDTH'].'px':$arParams['WIDTH']);
	
	$arParams['HEIGHT']=str_replace(' ', '', $arParams['HEIGHT']);
	$arParams['HEIGHT']=(stristr($arParams['HEIGHT'], '%') === FALSE and !empty($arParams['HEIGHT'])?$arParams['HEIGHT'].'px':$arParams['HEIGHT']);
		
	$arParams['CENTER_MAP']=str_replace(' ', '', $arParams['CENTER_MAP']);
	$arParams['CENTER_MAP']=(empty($arParams['CENTER_MAP'])?array('37.556076', '55.754361'):explode(',',$arParams['CENTER_MAP']));
	
	$arParams['ZOOM_MAP']=str_replace(' ', '', $arParams['ZOOM_MAP']);
	$arParams['ZOOM_MAP']=(empty($arParams['ZOOM_MAP'])?'1':$arParams['ZOOM_MAP']);
	
	$arParams['COORDINATES_POINTS']=str_replace(' ', '', $arParams['COORDINATES_POINTS']);
	$arParams['COORDINATES_POINTS']=(empty($arParams['COORDINATES_POINTS'])?$arParams['CENTER_MAP']:explode(',',$arParams['COORDINATES_POINTS']));
	
	$arParams['ICON_POINTS']=trim($arParams['ICON_POINTS']);
	if ($arParams['ICON_POINTS'] and file_exists($_SERVER['DOCUMENT_ROOT'].$arParams['ICON_POINTS'])){
		$arICON_POINTS=getimagesize($_SERVER['DOCUMENT_ROOT'].$arParams['ICON_POINTS']);
		$arParams['ICON_POINTS']=array('FILE'=>$arParams['ICON_POINTS'],'WIDTH'=>$arICON_POINTS[0], 'HEIGHT'=>$arICON_POINTS[1]);
	} else {$arParams['ICON_POINTS']=array();}
	
	
	//Подклчаем CSS, JS файлы плагина arcticmodal
	$component_dir=substr(__DIR__, strpos(__DIR__, "/bitrix"), strlen(__DIR__));
	//$APPLICATION->SetAdditionalCSS($component_dir."/css/fotorama.css");
	$APPLICATION->AddHeadScript("http://maps.api.2gis.ru/2.0/loader.js?pkg=full");
	$APPLICATION->AddHeadScript($component_dir."/js/connect_map.js");

	$this->IncludeComponentTemplate();
?>