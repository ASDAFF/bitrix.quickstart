<?php

class CVCSAjaxItem extends CVCSAjaxService {

	public function cmdShowSource($arParams = array()) {
		$arResult = array('source' => array());

		!empty($arParams['revision_id']) or $arParams['revision_id'] = false;

		$Item = CVCSItem::GetByID($arParams['id']);
		$arSource = explode("\n", $Item->GetSource($arParams['revision_id']));
		foreach ($arSource as $s) {
			$arResult['source'][] = array('str' => CVCSMain::GetStrForJSShow($s), 'type' => 'norm');
		}

		return $arResult;
	}


	public function cmdShowChanges($arParams = array()) {
		$Item = CVCSItem::GetByID($arParams['id']);

		!empty($arParams['revision_id']) or $arParams['revision_id'] = $Item->GetLastRevisionID();

		$arRev = $Item->GetRevisionsList(false, array('<REVISION_ID' => $arParams['revision_id']), array('limit' => 1), array('REVISION_ID'))->Fetch();

		return array('source' => CVCSMain::GetDiffArray($Item->GetSource($arRev['REVISION_ID']), $Item->GetSource($arParams['revision_id'])));
	}

	public function cmdDelete($arParams = array()) {
		$Item = CVCSItem::GetByID($arParams['id']);
		if ($Item) {
			CVCSChangedItemFactory::AddForDelete($Item);
		}

		return array('result' => 'ok');
	}
}