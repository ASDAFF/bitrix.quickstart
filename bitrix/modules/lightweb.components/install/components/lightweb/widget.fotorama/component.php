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
	if(!CModule::IncludeModule("lightweb.components")) return;
	
	$arParams["CACHE_TIME"] = 36000000;
	$IBLOCK_TYPE_ID = trim($arParams["IBLOCK_TYPE_ID"]);
	$IBLOCK_ID = trim($arParams["IBLOCK_ID"]);
	$ELEMENT_ID = trim($arParams["ELEMENT_ID"]);
	$PROP_ID = trim($arParams["PROP_ID"]);
	$ELEMENT_COUNT = intval($arParams["ELEMENT_COUNT"]);
	$ELEMENT_COUNT = ($ELEMENT_COUNT>0?$ELEMENT_COUNT:6);

	///Подклчаем CSS, JS файлы плагина arcticmodal
	CLWComponents::ConnectPlugin('fotorama');
	
	if($this->StartResultCache(false)){
		
		$obElement = CIBlockElement::GetByID($ELEMENT_ID);
		if($arElement = $obElement->GetNext()){
			$arButtons = CIBlock::GetPanelButtons(
				$arElement["IBLOCK_ID"],
				$arElement["ID"],
				0,
				array("SECTION_BUTTONS"=>false, "SESSID"=>false)
			);
			$arElement['EDIT_LINK']=$arButtons["edit"]["edit_element"]["ACTION_URL"];
			$arElement['DELETE_LINK']=$arButtons["edit"]["delete_element"]["ACTION_URL"];
			
			$arResult=$arElement;
		}
		
		$obProperty = CIBlockElement::GetProperty($IBLOCK_ID, $ELEMENT_ID, array(), array("ID"=>$PROP_ID));
		
		$i=0;
		while ($arProperty = $obProperty->GetNext()){
			$i++;
			$arPropertyValue=CFile::GetFileArray($arProperty['VALUE']);
			if (empty($arPropertyValue['DESCRIPTION'])){$arPropertyValue['DESCRIPTION']=$arElement['NAME'];}
			$arResult['PROPERTY'][]=$arPropertyValue;
			if($i>=$ELEMENT_COUNT){
				break;
			}
		}

		$this->IncludeComponentTemplate();
	}
?>