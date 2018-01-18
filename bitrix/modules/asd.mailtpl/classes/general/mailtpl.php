<?php

class CASDMailTpl {

	public static function OnBeforeEventSend(&$arFields, &$arMailResult) {
		if ($arFields['ASD_TPL_EXIST'] == 'Y') {
			if ($arMailResult['BODY_TYPE']=='text' && $arFields['ASD_TPL_FORMAT']=='html') {
				$arMailResult['MESSAGE'] = str_replace("\n", '<br/>', $arMailResult['MESSAGE']);
			}
			$arMailResult['BODY_TYPE'] = $arFields['ASD_TPL_FORMAT'];
			if ($arMailResult['BODY_TYPE']=='html' && strlen($arFields['ASD_TPL_SETTINGS'])) {
				$arSettings = unserialize($arFields['ASD_TPL_SETTINGS']);
				if (strlen($arSettings['STYLE_P'])) {
					$arMailResult['MESSAGE'] = str_replace('<p>', '<p style="'.$arSettings['STYLE_P'].'">', $arMailResult['MESSAGE']);
				}
				if (strlen($arSettings['STYLE_SPAN'])) {
					$arMailResult['MESSAGE'] = str_replace('<span>', '<span style="'.$arSettings['STYLE_SPAN'].'">', $arMailResult['MESSAGE']);
				}
			}
			foreach ($arMailResult as $k => $v) {
				$arFields['ASD_TPL_HEADER'] = str_replace('#'.$k.'#', $v, $arFields['ASD_TPL_HEADER']);
				$arFields['ASD_TPL_FOOTER'] = str_replace('#'.$k.'#', $v, $arFields['ASD_TPL_FOOTER']);
			}
			$arMailResult['MESSAGE'] = $arFields['ASD_TPL_HEADER'].
											$arMailResult['MESSAGE'].
										$arFields['ASD_TPL_FOOTER'];
		}
	}

	public static function OnBeforeEventAdd(&$event, &$lid, &$arFields) {
		if ($arTpl = CASDMailTplDB::GetByEvent($event)) {
			$arFields['ASD_TPL_HEADER'] = $arTpl['HEADER'];
			$arFields['ASD_TPL_FOOTER'] = $arTpl['FOOTER'];
			$arFields['ASD_TPL_FORMAT'] = $arTpl['TYPE'];
			$arFields['ASD_TPL_SETTINGS'] = $arTpl['SETTINGS'];
			$arFields['ASD_TPL_EXIST'] = 'Y';
		}
	}

	public static function GetAllEventTypes() {
		$arTypes = array();
		$rsTypes = CEventType::GetList(array('LID' => LANG));
		while ($arType = $rsTypes->GetNext()) {
			$arTypes[$arType['EVENT_NAME']] = '['.$arType['EVENT_NAME'].'] '.$arType['NAME'];
		}
		ksort($arTypes);
		return $arTypes;
	}
}