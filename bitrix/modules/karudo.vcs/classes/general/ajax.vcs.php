<?php

IncludeModuleLangFile(__FILE__);

class CVCSAjaxVcs extends CVCSAjaxService {

	public function cmdRestoreFromLastRevision($arParams = array()) {
		$arResult = array('status' => 'done');

		if (empty($arParams['step'])) {
			$this->cmdClearChangedItems();
			$arResult['status'] = 'next';
			$arResult['step'] = 'check';
		} elseif ($arParams['step'] === 'check') {
			$arCheckParams = empty($arParams['check_params']) ? array('status' => 'start') : (array) $arParams['check_params'];
			$arCheckResult = $this->cmdCheckForNew($arCheckParams);
			$arResult['status'] = 'next';
			if ($arCheckResult['status'] === 'next') {
				$arResult['step'] = 'check';
				$arResult['check_params'] = $arCheckResult;
			} else {
				$arResult['step'] = 'reset';
				$arResult['count'] = $arCheckResult['count'];
			}
		} elseif ($arParams['step'] === 'reset') {
			$arResult['count'] = $arParams['count'];
			while ($arChangedItems = CVCSChangedItemFactory::GetItemsArray(array('STATUS' => CVCSConfig::CIST_UPD))) {
				foreach ($arChangedItems as $arItem) {
					//if (empty($arItem['IS_NEW'])) {
					if ($arItem['STATUS'] === CVCSConfig::CIST_UPD) {
						$Item = CVCSItem::GetByDiverCodeOrigIdHash($arItem['DRIVER_CODE'], $arItem['ORIG_ID_HASH']);
						$DriverItem = CVCSDriverItemAbstract::GetItemObject($arItem['DRIVER_CODE'], $arItem['ORIG_ID']);
						$DriverItem->SetSource( $Item->GetSource() );
						CVCSChangedItemFactory::DeleteByDiverCodeOrigIdHash($arItem['DRIVER_CODE'], $arItem['ORIG_ID_HASH']);
					}
				}
			}
			$this->cmdClearChangedItems();
		}

		return $arResult;
	}

	public function cmdCheckAndCommit($arParams = array()) {
		$arParams['revision_desc'] = empty($arParams['revision_desc']) ? '' : $arParams['revision_desc'];
		if (strlen($arParams['revision_desc']) < 1) {
			throw new CVCSAjaxExceptionServiceError(GetMessage('VCS_EMPTY_REVISION_DESC'));
		}
		$arResult = array('status' => 'done', 'proc' => 100, 'revision_desc' => $arParams['revision_desc']);

		if (empty($arParams['step'])) {
			$this->cmdClearChangedItems();
			$arResult['status'] = 'next';
			$arResult['step'] = 'check';
		} elseif ($arParams['step'] === 'check') {
			$arCheckParams = empty($arParams['check_params']) ? array('status' => 'start') : (array) $arParams['check_params'];
			$arCheckResult = $this->cmdCheckForNew($arCheckParams);
			$arResult['status'] = 'next';
			if ($arCheckResult['status'] === 'next') {
				$arResult['step'] = 'check';
				$arResult['check_params'] = $arCheckResult;
			} else {
				$arResult['step'] = 'commit';
				$arResult['count'] = $arCheckResult['count'];
			}
		} elseif ($arParams['step'] === 'commit') {
			$arCommitParams = empty($arParams['commit_params']) ? array('status' => 'start', 'revision_desc' => $arParams['revision_desc']) : (array) $arParams['commit_params'];
			$arCommitResult = $this->cmdCommit($arCommitParams);
			if ($arCommitResult['status'] === 'next') {
				$arResult['step'] = 'commit';
				$arResult['commit_params'] = $arCommitResult;
				$arResult['status'] = 'next';
			} else {
				$arResult['status'] = 'done';
				//$arResult['proc'] = '100';
			}
			$arResult['count'] = $arParams['count'];
		}

		return $arResult;
	}

	public function cmdClearChangedItems($arParams = array()) {
		BXClearCache(true, CVCSConfig::CACHE_DIR);
		CVCSChangedItemFactory::Clear();
		return array('status' => 'ok');
	}

	public function cmdCheckForNew($arParams = array()) {
		$arResult = array('status' => 'done');
		$Timer = new CVCSTimer(CVCSConfig::GetStepExecutionTime() - .4);

		$drivers = empty($arParams['drivers']) ? array() : (array) $arParams['drivers'];

		$dcode = empty($arParams['dcode']) ? false : $arParams['dcode'];
		$last_item = empty($arParams['last_item']) ? false : $arParams['last_item'];
		$check_deleted = empty($arParams['check_deleted']) ? 0 : 1;

		if ($check_deleted) {
			while ($arItems = CVCSItemFactory::GetNotChangedItems($last_item, $drivers)) {
				foreach ($arItems as $arItem) {
					$DriverItem = CVCSDriverItemAbstract::GetItemObject($arItem['DRIVER_CODE'], $arItem['ORIG_ID']);
					CVCSChangedItemFactory::AddItem($DriverItem);
					$last_item = $arItem['ID'];

					if ($Timer->TimeExists()) {
						$arResult['check_deleted'] = 1;
						$arResult['last_item'] = $last_item;
						$arResult['status'] = 'next';
						break 2;
					}
				}
			}
		} else {
			$arDrivers = CVCSMain::GetDriversArray();
			foreach ($arDrivers as $drv) {
				if (!empty($drivers) && !in_array($drv['code'], $drivers)) {
					continue;
				}

				if (!empty($dcode)) {
					if ($dcode == $drv['code']) {
						$dcode = false;
					} else {
						continue;
					}
				}

				/* @var CVCSDriverIteratorAbstract $Iterator */
				$Iterator = new $drv['class_iterator']($drv);
				if (!empty($last_item)) {
					$Iterator->SetLastItemOrigID($last_item);
				}
				//print_r($Iterator);
				while ($DriverItem = $Iterator->GetNextItem()) {

					CVCSChangedItemFactory::AddItem($DriverItem);

					if ($Timer->TimeExists()) {
						$arResult['status'] = 'next';
						$arResult['dcode'] = $DriverItem->GetDriverCode();
						$arResult['last_item'] = $DriverItem->GetID();
						$arDriver = CVCSMain::GetDriverByCode($arResult['dcode']);
						$arResult['operation'] = GetMessage('VCS_CHECK_DRIVER', array('%NAME%' => $arDriver['name']));
						break 2;
					}
				}

				CVCSDriversFactory::UpdateLastCheck($drv['code']);
			}
			if ('done' === $arResult['status']) {
				$arResult['last_item'] = 0;
				$arResult['check_deleted'] = 1;
				$arResult['status'] = 'next';
			}
		}

		$arResult['time'] = $Timer->GetWorkTime();

		if ('next' === $arResult['status']) {
			if (!empty($Iterator)) {
				$arResult['count'] = (int) $Iterator->GetItemsCount();
				$arResult['cur_pos'] = (int) $Iterator->GetCurPosition();
				$arResult['percents'] = $arResult['count'] > 0 ? round($arResult['cur_pos'] * 100 / $arResult['count']) : 0;
			}
		} else {
			CVCSConfig::SetLastCheckTime();
			$arResult['count'] = CVCSChangedItemFactory::GetItemsCount();
			$arResult['last_check_text'] = CVCSMain::GetLastCheckTimeText();
		}

		if (!empty($arResult['check_deleted'])) {
			$arResult['operation'] = GetMessage('VCS_CHECK_DELETED');
			$arResult['percents'] = 100;
		}

		return $arResult;
	}

	public function cmdShowSource($arParams = array()) {
		$arChangedItem = CVCSChangedItemFactory::GetList(array(), array('=ID' => $arParams['id']), array('select_source' => true))->Fetch();
		$arResult = array('source' => array());
		$arSource = explode("\n", $arChangedItem['SOURCE']);
		foreach ($arSource as $s) {
			$arResult['source'][] = array('str' => CVCSMain::GetStrForJSShow($s), 'type' => 'norm');
		}

		return $arResult;
	}

	public function cmdShowLastSource($arParams = array()) {
		$arChangedItem = CVCSChangedItemFactory::GetList(array(), array('=ID' => $arParams['id']))->Fetch();
		$Item = CVCSItem::GetByDiverCodeOrigIdHash($arChangedItem['DRIVER_CODE'], $arChangedItem['ORIG_ID_HASH']);
		$arResult = array('source' => array());
		$arSource = explode("\n", $Item->GetSource());
		foreach ($arSource as $s) {
			$arResult['source'][] = array('str' => CVCSMain::GetStrForJSShow($s), 'type' => 'norm');
		}

		return $arResult;
	}

	public function cmdShowDiff($arParams = array()) {
		$arChangedItem = CVCSChangedItemFactory::GetList(array(), array('=ID' => $arParams['id']), array('select_source' => true))->Fetch();

		$Item = CVCSItem::GetByDiverCodeOrigIdHash($arChangedItem['DRIVER_CODE'], $arChangedItem['ORIG_ID_HASH']);

		$arResult = array(
			'source' => CVCSMain::GetDiffArray($Item->GetSource(), $arChangedItem['SOURCE']),
		);

		return $arResult;
	}

	public function cmdRestoreItem($arParams = array()) {
		$arChangedItem = CVCSChangedItemFactory::GetList(array(), array('=ID' => $arParams['id']))->Fetch();
		if ($arChangedItem) {
			$Item = CVCSItem::GetByDiverCodeOrigIdHash($arChangedItem['DRIVER_CODE'], $arChangedItem['ORIG_ID_HASH']);
			if ($Item) {
				$DriverItem = CVCSDriverItemFiles::GetItemObject($arChangedItem['DRIVER_CODE'], $arChangedItem['ORIG_ID']);
				$DriverItem->SetSource($Item->GetSource(), true);
				CVCSChangedItemFactory::Delete($arParams['id']);
			}
		}

		return array('status' => 'ok');
	}

	public function cmdDeleteNewFile($arParams = array()) {
		$arChangedItem = CVCSChangedItemFactory::GetList(array(), array('=ID' => $arParams['id']))->Fetch();
		if ($arChangedItem) {
			$DriverItem = CVCSDriverItemFiles::GetItemObject($arChangedItem['DRIVER_CODE'], $arChangedItem['ORIG_ID']);
			if ($DriverItem->Delete()) {
				CVCSChangedItemFactory::Delete($arParams['id']);
			}
		}

		return array('status' => 'ok');
	}

	public function cmdCommit($arParams = array()) {
		$APPLICATION = CVCSMain::GetAPPLICATION();

		//$arResult['params'] = $arParams;
		$arResult['status'] = 'done';

		if ($arParams['status'] == 'start') {

			$c = CVCSChangedItemFactory::GetItemsCount();
			if (empty($c)) {
				throw new CVCSAjaxExceptionServiceError(GetMessage('VCS_NO_ITEMS_FOR_COMMIT'));
			}

			$arResult['revision_id'] = CVCSRevisionFactory::AddNewRevision($arParams['revision_desc']);
			if (empty($arResult['revision_id'])) {
				$e = $APPLICATION->GetException();
				throw new CVCSAjaxExceptionServiceError($e ? $e->GetString() : 'Error');
			}
		} else {
			$arResult['revision_id'] = $arParams['revision_id'];
		}

		if (empty($arResult['revision_id'])) {
			throw new CVCSAjaxExceptionServiceError(GetMessage('VCS_EMPTY_REVISION_ID'));
		}

		$Timer = new CVCSTimer(CVCSConfig::GetStepExecutionTime());

		while ($arChangedItems = CVCSChangedItemFactory::GetItemsArray()) {
			foreach ($arChangedItems as $arItem) {
				if ($arItem['STATUS'] === CVCSConfig::CIST_NEW) {
					$arItem['FIRST_REVISION_ID'] = $arResult['revision_id'];
					$Item = CVCSItem::GetByItemArray($arItem);
				} else {
					$Item = CVCSItem::GetByDiverCodeOrigIdHash($arItem['DRIVER_CODE'], $arItem['ORIG_ID_HASH']);
				}

				if ($arItem['STATUS'] === CVCSConfig::CIST_DEL) {
					$Item->DeleteByCI($arResult['revision_id']);
				} else {
					$Item->AddNewRevisionFromCI($arResult['revision_id']);
				}

				if ($Timer->TimeExists()) {
					$arResult['status'] = 'next';
					break 2;
				}
			}
		}
		$arResult['time'] = $Timer->GetWorkTime();

		return $arResult;
	}

	public function cmdResetItem($arParams = array()) {
		$arResult['status'] = 'done';

		$arParams['item_id'] = empty($arParams['item_id']) ? 0 : intval($arParams['item_id']);
		$arParams['revision_id'] = empty($arParams['revision_id']) ? 0 : intval($arParams['revision_id']);

		$Item = CVCSItem::GetByID($arParams['item_id']);
		if (empty($Item)) {
			throw new CVCSAjaxExceptionServiceError(GetMessage("VCS_UNKNOWN_FILE"));
		}
		if (!CVCSRevisionFactory::GetList(array(), array('ID' => $arParams['revision_id']))->Fetch()) {
			throw new CVCSAjaxExceptionServiceError(GetMessage("VCS_UNKNOWN_REVISION"));
		}

		$DriverItem = CVCSDriverItemFiles::GetItemObject($Item->GetDriverCode(), $Item->GetOrigID());

		if ($Item->GetSourceHash($arParams['revision_id']) !== $DriverItem->GetSourceHash()) {
			$DriverItem->SetSource( $Item->GetSource($arParams['revision_id']) );
		}

		return $arResult;
	}

	public function cmdReset($arParams = array()) {
		$arResult['status'] = 'done';

		$last_id = empty($arParams['last_id']) ? 0 : intval($arParams['last_id']);
		$revision_id = empty($arParams['revision_id']) ? 0 : intval($arParams['revision_id']);

		if (!CVCSRevisionFactory::GetList(array(), array('ID' => $revision_id))->Fetch()) {
			throw new CVCSAjaxExceptionServiceError(GetMessage("VCS_UNKNOWN_REVISION"));
		}

		$Timer = new CVCSTimer(CVCSConfig::GetStepExecutionTime());

		$arFilter = array(
			'=DRIVER_CODE' => empty($arParams['driver']) ? '-' : $arParams['driver'],
			'<=FIRST_REVISION_ID' => $revision_id,
			'>REVISION_ID' => $revision_id,
			'>ID' => $last_id
		);

		while ($arItems = CVCSItemFactory::GetItemsArray(
			array('ID' => 'ASC'),
			$arFilter,
			array('limit' => 100)
		)) {
			foreach ($arItems as $arItem) {

				$Item = CVCSItem::GetByItemArray($arItem);
				$DriverItem = CVCSDriverItemAbstract::GetItemObject($arItem['DRIVER_CODE'], $arItem['ORIG_ID']);
				if ($Item->GetSourceHash($revision_id) !== $DriverItem->GetSourceHash()) {
					$DriverItem->SetSource( $Item->GetSource($revision_id) );
				}

				$last_id = $arItem['ID'];

				if ($Timer->TimeExists()) {
					$arResult['status'] = 'next';
					$arResult['last_id'] = $last_id;
					break 2;
				}
			}
		}

		return $arResult;
	}

	public function cmdSetDeleted($arParams = array()) {
		$id = empty($arParams['id']) ? 0 : intval($arParams['id']);
		CVCSChangedItemFactory::SetDeleted($id);
		return array('status' => 'ok');
	}

}