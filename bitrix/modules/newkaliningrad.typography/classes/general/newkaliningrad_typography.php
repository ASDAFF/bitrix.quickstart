<?php

class newkaliningrad_typography
{
	function addEditorScriptsHandler($editorName, $arEditorParams) {
		return array( "JS" => array("newkaliningrad_typography.js"));
	}
	
	function OnIncludeHTMLEditorHandler() {
		$GLOBALS['APPLICATION']->AddHeadScript("/bitrix/js/main/ajax.js");
	}

	function OnBeforeIBlockElementAddOrUpdateHandler(&$arFields) {
		$nk_typography_autoiblocks = COption::GetOptionString('newkaliningrad.typography', 'nk_typography_autoiblocks');
		$nk_typography_autoiblocks = unserialize($nk_typography_autoiblocks);
		if (in_array($arFields['IBLOCK_ID'], $nk_typography_autoiblocks)) {

			if(!defined("BX_UTF")){
				$arFields['PREVIEW_TEXT'] = iconv('windows-1251','utf-8', $arFields['PREVIEW_TEXT']);
				$arFields['DETAIL_TEXT'] = iconv('windows-1251','utf-8', $arFields['DETAIL_TEXT']);
			}

			CModule::IncludeModule('newkaliningrad.typography');
			$typography = new newkaliningrad_EMTypograph(); 
			
			$typography->set_text($arFields['PREVIEW_TEXT']);
			$PREVIEW_TEXT = $typography->apply();
			if (strlen($PREVIEW_TEXT)>0) {
				$arFields['PREVIEW_TEXT'] = $PREVIEW_TEXT;
				$arFields['PREVIEW_TEXT_TYPE'] = 'html';
			}

			$typography->set_text($arFields['DETAIL_TEXT']);
			$DETAIL_TEXT = $typography->apply();
			if (strlen($DETAIL_TEXT)>0) {
				$arFields['DETAIL_TEXT'] = $DETAIL_TEXT;
				$arFields['DETAIL_TEXT_TYPE'] = 'html';
			}

			if(!defined("BX_UTF")){
				$arFields['PREVIEW_TEXT'] = iconv('utf-8','windows-1251', $arFields['PREVIEW_TEXT']);
				$arFields['DETAIL_TEXT'] = iconv('utf-8','windows-1251', $arFields['DETAIL_TEXT']);
			}

		}
	}
}

