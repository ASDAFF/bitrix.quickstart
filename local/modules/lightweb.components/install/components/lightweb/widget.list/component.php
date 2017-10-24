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

	$arParams["CACHE_TIME"] = 36000000;
	
	if (empty($arParams["FILTER"]) and !is_array($arParams["FILTER"])){
		$arParams["FILTER"]=array();
	}
	
	$IBLOCK_TYPE_ID = trim($arParams["IBLOCK_TYPE_ID"]);
	$IBLOCK_ID = trim($arParams["IBLOCK_ID"]);
	$FIELD_SORT = (in_array($arParams["FIELD_SORT"], array('id','name','sort','active_from','timestamp_x'))?$arParams["FIELD_SORT"]:'sort');
	$SORT_ORDER =(in_array($arParams["SORT_ORDER"], array('asc','desc'))?$arParams["SORT_ORDER"]:'asc');
	
	$NUM_PAGE = intval($arParams["NUM_PAGE"]);
	$NUM_PAGE = ($NUM_PAGE>0?$NUM_PAGE:0);
	
	$ELEMENT_COUNT = intval($arParams["ELEMENT_COUNT"]);
	$ELEMENT_COUNT = ($ELEMENT_COUNT>0?$ELEMENT_COUNT:6);
	
	if($this->StartResultCache(false)){
		//Получаем список разделов
		$obSection = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID), true);
		
		while($arSection = $obSection->GetNext()){
			$arSection['PICTURE']=CFile::GetFileArray($arSection["PICTURE"]);
			$arSection['DETAIL_PICTURE']=CFile::GetFileArray($arSection["DETAIL_PICTURE"]);
			$arSections[$arSection['ID']]=$arSection;
		}
		
		//Получаем список элементов
		$arOrder = array($FIELD_SORT=>$SORT_ORDER);
		$arFilter = array_merge(array("IBLOCK_TYPE"=>$IBLOCK_TYPE_ID, "IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"),$arParams["FILTER"]);
		$arNavStartParams=array("iNumPage"=>$NUM_PAGE, "nPageSize"=>$ELEMENT_COUNT);
		
		$dbElements = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParams, false);
		
		$RowsCount=$dbElements->SelectedRowsCount();
		
		while($obElements = $dbElements->GetNextElement()){
			$arElement = $obElements->GetFields();  
			$arElement['DETAIL_PICTURE']=CFile::GetFileArray($arElement["DETAIL_PICTURE"]);
			$arElement['PREVIEW_PICTURE']=CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
			
			$arElement['SECTION']['ID']=$arSections[$arElement['IBLOCK_SECTION_ID']]['ID'];
			$arElement['SECTION']['SECTION_CODE']=$arSections[$arElement['IBLOCK_SECTION_ID']]['CODE'];
			$arElement['SECTION']['SORT']=$arSections[$arElement['IBLOCK_SECTION_ID']]['SORT'];
			$arElement['SECTION']['PARENT_SECTION_ID']=$arSections[$arElement['IBLOCK_SECTION_ID']]['IBLOCK_SECTION_ID'];
			$arElement['SECTION']['DEPTH_LEVEL']=$arSections[$arElement['IBLOCK_SECTION_ID']]['DEPTH_LEVEL'];
			$arElement['SECTION']['ACTIVE']=$arSections[$arElement['IBLOCK_SECTION_ID']]['ACTIVE'];
			$arElement['SECTION']['NAME']=$arSections[$arElement['IBLOCK_SECTION_ID']]['NAME'];
			$arElement['SECTION']['SDESCRIPTION']=$arSections[$arElement['IBLOCK_SECTION_ID']]['DESCRIPTION'];
			$arElement['SECTION']['PICTURE']=$arSections[$arElement['IBLOCK_SECTION_ID']]['PICTURE'];
			$arElement['SECTION']['DETAIL_PICTURE']=$arSections[$arElement['IBLOCK_SECTION_ID']]['DETAIL_PICTURE'];
			$arElement['SECTION']['ELEMENT_CNT']=$arSections[$arElement['IBLOCK_SECTION_ID']]['ELEMENT_CNT'];
			
			$arButtons = CIBlock::GetPanelButtons(
				$arElement["IBLOCK_ID"],
				$arElement["ID"],
				0,
				array("SECTION_BUTTONS"=>false, "SESSID"=>false)
			);
			$arElement["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
			$arElement["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
			$arElement["ROW_COUNT"] = $RowsCount;
			$arElement['PROPERTY']=$obElements->GetProperties();
			
			$arResult[]=$arElement;
		}
		
		 $this->IncludeComponentTemplate();
	}
?>