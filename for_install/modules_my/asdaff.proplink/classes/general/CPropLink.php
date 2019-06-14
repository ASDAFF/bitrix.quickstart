<?

namespace ASDAFF;

class CPropLink {

	const MODULE_ID = 'asdaff.proplink';

	/**
	 * Function will link properties for iblock
	 * @param  string $msg
	 * @return array
	 */
	public function linkProperties($params, $exeptions = array()) {
		if (empty($params['iblock_id'])) {
			return self::returnError(GetMessage(self::MODULE_ID . '_NO_IBLOCK_ID'));
		}
		if (!self::setLinkingForIblock($params)) {
			return self::returnError(GetMessage(self::MODULE_ID . '_CANT_UPDATE_IBLOCK'));
		}

		\CModule::IncludeModule('iblock');

		$syncStatus   = array('status' => 'inProgress', 'count' => 0);
		$arProperties = array();
		$dbProperties = \CIBlock::GetProperties($params['iblock_id'], array(), array());

		if ($params['erase_links'] == 'Y') {
            $ibProps = \CIBlockSectionPropertyLink::GetArray($params['iblock_id'], 0);
		    \CIBlockSectionPropertyLink::DeleteBySection(0);
        }

		while ($arProperty = $dbProperties->Fetch()) {

			if (!in_array($arProperty['CODE'], $exeptions)) {

				$arProperties[$arProperty['ID']] = $arProperty;
			}
		}


		if (empty($arProperties)) {
			return self::returnError(GetMessage(self::MODULE_ID . '_NO_PROPS_TO_LINK'));
		}

		if (isset($params['pack']) && isset($params['size'])) {

			$offset = $params['pack'] * $params['size'];

			if (count($arProperties) <= $offset) {
				$syncStatus['status'] = 'finished';
				return self::returnSuccess($syncStatus);
			}
			$arProperties = array_slice($arProperties, $offset, $params['size']);
		}

		foreach ($arProperties as $arProperty) {
			$pid = $arProperty['ID'];

			$arSelect   = array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME');
			$arFilter   = array('IBLOCK_ID' => $params['iblock_id'], '!PROPERTY_' . $pid => FALSE);
			$arSections = array();
			$dbElements = \CIBlockElement::GetList(array(), $arFilter, $arSelect);

			while ($arElement = $dbElements->Fetch()) {
                $dbResult = \CIBlockElement::GetElementGroups($arElement['ID'], true);
				while ($arGroup = $dbResult->Fetch()){
                    $arSections[$arGroup['ID']] = 1;
                }
			}

			if (!empty($arSections)) {
				$syncStatus['count']++;

				if ($params['save_links'] == 'N') {
					\CIBlockSectionPropertyLink::DeleteByProperty($pid);

				}

				$arSmart = array('SMART_FILTER' => 'N', 'IBLOCK_ID' => $params['iblock_id']);
				if ($ibProps[$pid]) {
                    $arSmart['SMART_FILTER'] = $ibProps[$pid]['SMART_FILTER'];
                }

				foreach ($arSections as $sid => $value) {
					$props = \CIBlockSectionPropertyLink::GetArray($params['iblock_id'], $sid);

					if ($params['save_links'] == 'Y' && $props[$pid]) {
						continue;
					} else {
						\CIBlockSectionPropertyLink::Add($sid, $pid, $arSmart);
					}
				}
			}
		}

		if ($params['erase_links'] == 'Y') {
		    \CIBlockSectionPropertyLink::DeleteBySection(0);
        }

		return self::returnSuccess($syncStatus);
	}

	/**
	 * Function will clear properties for iblock
	 * @param  array $params
	 * @return bool
	 */
	public static function clearProperties($iblock) {
		\CIBlockSectionPropertyLink::DeleteByIBlock($iblock);
		$syncStatus['status'] = "removed";
		return self::returnSuccess($syncStatus);
	}

	/**
	 * Function will get statistic about linked properties for iblock
	 * @param  int $iblock
	 * @return array
	 */
	public static function getStat($iblock) {

		$iblockProps = \CIBlockSectionPropertyLink::GetArray($iblock);

		if (empty($iblockProps)) {

			$dbSections = \CIBlockSection::GetList(
						array('LEFT_MARGIN' => 'ASC'),
						array('IBLOCK_ID' => $iblock,
							'ELEMENT_SUBSECTIONS' => 'N'),
						true
					);

			while ($arSection = $dbSections->Fetch()) {

				if($arSection['ELEMENT_CNT'] > 0) {

					$arLinks = \CIBlockSectionPropertyLink::GetArray($iblock, $arSection['ID']);
					$syncStatus['stat'][ self::getValidEnc($arSection['NAME']) ] = count($arLinks);

				}

			}

		} else {

			$syncStatus['nostat'] = 'no props';
		}



		return self::returnSuccess($syncStatus);
	}

	/**
	 * Function will get valid current encoding
	 * @param  string $value
	 * @return string
	 */
	public static function getValidEnc($value) {
		if ((defined('BX_UTF') && BX_UTF) || empty($value)) {
			return $value;
		} else {
			return iconv('UTF-8', 'WINDOWS-1251', $value);
		}
	}

	/**
	 * Function will set magic field for iblock
	 * @param  array $params
	 * @return bool
	 */
	public static function setLinkingForIblock($params) {
		if ((int) $params['pack'] > 0) {
			return TRUE;
		}

		$iblock = new \CIBlock;
		return $iblock->Update((int) $params['iblock_id'],
			array('SECTION_PROPERTY' => 'Y'));
	}

	/**
	 * Function will return error for ajax
	 * @param  string $msg
	 * @return array
	 */
	public static function returnError($msg) {
		return array(
			'status' => 'ERROR',
			'data'   => NULL,
			'msg'    => $msg
		);
	}

	/**
	 * Function will return success for ajax
	 * @param  array $data
	 * @return array
	 */
	protected static function returnSuccess($data) {
		return array(
			'status' => 'OK',
			'data'   => $data,
			'msg'    => ''
		);
	}

}
?>
