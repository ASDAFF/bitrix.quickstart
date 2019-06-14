<?php

IncludeModuleLangFile(__FILE__);

class CVCSAjaxDrivers extends CVCSAjaxService {

	public function cmdExport($arParams = array()) {
		$id = empty($arParams['id']) ? 0 : intval($arParams['id']);
		$arDriver = CVCSDriversFactory::GetList(array(), array('ID' => $id))->Fetch();
		if (!$arDriver) {
			throw new CVCSAjaxExceptionServiceError(GetMessage("VCS_ERROR_NO_DRIVER"));
		}

		if (empty($arParams['dir'])) {
			throw new CVCSAjaxExceptionServiceError(GetMessage("VCS_ERROR_EMPTY_DIR"));
		}

		$doc_root = $_SERVER['DOCUMENT_ROOT'] . $arParams['dir'];
		$Settings = new CVCSArrayObject(array(
			'doc_root' => $doc_root,
		));

		$arFilter = array(
			'LOGIC' => 'AND',
			'=DRIVER_CODE' => $arDriver['DRIVER_CODE'],
		);

		if (!empty($arParams['revision_from'])) {
			$arFilter['>=SOURCE_REVISION_ID'] = $arParams['revision_from'];
		}
		if (empty($arParams['revision_to'])) {
			$arFilter['DELETED'] = 0;
		} else {
			$arFilter['<=SOURCE_REVISION_ID'] = $arParams['revision_to'];
			$arFilter[] = array(
				'LOGIC' => 'OR',
				'DELETED' => 0,
				array(
					'DELETED' => 1,
					'>DELETED_IN_REVISION' => $arParams['revision_to'],
				)
			);
		}

		$arAllItems = array();
		$rs = CVCSItemFactory::GetList(array('ID' => 'ASC'), $arFilter);
		while ($arItem = $rs->Fetch()) {
			//$Item = CVCSItem::GetByID($arItem['ID']);
			$Item = CVCSItem::GetByItemArray($arItem);
			$DriverItem = new CVCSDriverItemFiles($arDriver['DRIVER_CODE'], $arItem['ORIG_ID'], $Settings);
			$DriverItem->SetSource($Item->GetSource(empty($arParams['revision_to']) ? false : $arParams['revision_to']), true);

			$arAllItems[] = $arItem['ORIG_ID'];
		}

		$arEvents = GetModuleEvents(CVCSConfig::MODULE_ID, 'OnDriverAfterExport', true);
		if (!empty($arEvents)) {
			foreach ($arEvents as $ev) {
				ExecuteModuleEventEx($ev, array($doc_root, $arDriver['DRIVER_CODE'], $arAllItems));
			}
		}

		return array('resilt' => 'ok', 'message' => GetMessage("VCS_FILES_EXPORTED") . $rs->SelectedRowsCount());
	}

	public function cmdGetList($arParams = array()) {
		$arResult = array();
		$rsSysDrivers = CVCSDriversFactory::GetList(false, array('ACTIVE' => 1));
		while ($arSysDriver = $rsSysDrivers->GetNext()) {
			$arResult[] = array(
				'code' => $arSysDriver['DRIVER_CODE'],
				'name' => $arSysDriver['NAME'],
				'last_check' => $arSysDriver['LAST_CHECK'],
				'fullname' => '[' . $arSysDriver['DRIVER_CODE'] . '] ' . $arSysDriver['NAME'],
			);
		}

		return $arResult;
	}

}