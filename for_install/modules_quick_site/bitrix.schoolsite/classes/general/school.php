<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
IncludeModuleLangFile(__FILE__);

class CSchool { 
    function OnBeforeIBlockElementAddHandler(&$arFields) {
	if($arFields['WF_NEW'])return true; // from standalone elemet add form, elemnt add twice...
	CModule::IncludeModule("iblock");
	$rsET = CEventType::GetList(Array());
	while ($arET = $rsET->Fetch()){
		preg_match('#^SCHOOL_ELEMENT_ADD_IBLOCK([0-9]+)$#smi', $arET['EVENT_NAME'], $id);
		if($id[1] != $arFields['IBLOCK_ID'])continue;
		
		
		$fields = Array(
			'NAME' => $arFields['NAME'],
			'SECTIONS' => ''
		);
		
		// Sections
		$sec = CIBlockElement::GetElementGroups($arFields['ID'], true);
		$i = 0;
		while($ar_group = $sec->Fetch())$fields['SECTIONS'] .= (($i++)?'; ':''). $ar_group['NAME'];

		// Standalone fields
		$res = CIBlockElement::GetByID($arFields['ID']);
		if($ar_res = $res->GetNext()){
			$fields['PREVIEW_TEXT'] = $ar_res['PREVIEW_TEXT'];
			$fields['DETAIL_TEXT'] = $ar_res['DETAIL_TEXT'];
		}
		
		// Admin link
		$res = CIBlock::GetByID($arFields['IBLOCK_ID']);
		if($ar_res = $res->GetNext())$fields['DIRECT_LINK'] = 'http://'.$_SERVER['SERVER_NAME'].'/bitrix/admin/iblock_element_edit.php?ID='.$arFields['ID'].'&type='.$ar_res['IBLOCK_TYPE_ID'].'&IBLOCK_ID='.$arFields['IBLOCK_ID'];

		$ps = Array();
		$props = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID']);
		// $ar_props['MULTIPLE']
		while($ar_props = $props->Fetch()){
			if(empty($ps[$ar_props['CODE']]))$ps[$ar_props['CODE']] = Array();
			
			switch($ar_props['PROPERTY_TYPE']){
			case 'F':
                if($ar_props['VALUE'])
                {
				 $file = CFile::GetPath($ar_props['VALUE']);
				 $file = explode('/', $file);
				 $file[count($file) - 1] = rawurlencode($file[count($file) - 1]);
				 $file = implode('/', $file);
				 $ar_props['VALUE'] = 'http://'.$_SERVER['SERVER_NAME'].$file;
                }
				break;

			case 'L':
                if($ar_props["VALUE"])
                {
				$property_enums = CIBlockPropertyEnum::GetList(
					Array("SORT"=>"ASC", "VALUE"=>"ASC"),
					Array("ID"=>$ar_props['VALUE'])
				);
				$enum_fields = $property_enums->GetNext();
				$ar_props['VALUE'] = $enum_fields['VALUE'];
                }
				break;

			case 'S':
				if($ar_props['USER_TYPE'] == 'HTML')$ar_props['VALUE'] = $ar_props['VALUE']['TEXT'];	// text/HTML
				if($ar_props['USER_TYPE'] == 'UserID'){		// User
					$User = CUser::GetByID($ar_props['VALUE']);
					$User = $User->Fetch();
					$ar_props['VALUE'] = $User['LAST_NAME']. ' '. $User['NAME']. ' ('. $User['EMAIL']. ')';
				}
				break;

			case 'E':	// Element link
				$res = CIBlockElement::GetByID($ar_props['VALUE']);
				if($ar_res = $res->GetNext())$ar_props['VALUE'] = htmlspecialchars_decode($ar_res['NAME']);
				break;
			}
			$ps[$ar_props['CODE']][] = $ar_props['VALUE'];
		}

		foreach($ps as $code => $vals){
			$fields[$code] = '';
			foreach($vals as $i => $val){
				if($i)$fields[$code] .= '; ';
				$fields[$code] .= $val;
			}
		}

		require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
		CEvent::Send($arET['EVENT_NAME'], CMainPage::GetSiteByHost(), $fields); // SITE_ID - always say "ru" in administrtive panel.
		break;
	}
	return true; 
    } 
} 

?>
