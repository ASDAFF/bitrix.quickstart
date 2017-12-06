<?

class MillcomMenu {
	function sort($a, $b) {
		if ($a['SORT'] == $b['SORT']) {
			if ($a['ID'] == $b['ID']) return 0;
			return ($a['ID'] > $b['ID']) ? -1 : 1;
		}
		return ($a['SORT'] < $b['SORT']) ? -1 : 1;
	}
	
	function display($arMenu, $id, $level, &$aMenuLinks, $arParams) {
		if (!isset($arMenu[$id])) return false;
		//if ($arParams['DEPTH_LEVEL'] < $level) return false;
		foreach ($arMenu[$id] as $arItem) {
			if ($arParams['DEPTH_LEVEL'] <= $level) // Глубже не пойдём
				unset($arMenu[$arItem['ID']]);
			$aMenuLinks[] = array(
				htmlspecialcharsbx($arItem['NAME']),
				$arItem['DETAIL_PAGE_URL'] ? $arItem['DETAIL_PAGE_URL'] : $arItem['SECTION_PAGE_URL'],
				array($arItem['DETAIL_PAGE_URL'] ? $arItem['DETAIL_PAGE_URL'] : $arItem['SECTION_PAGE_URL']),
				array(
					'FROM_IBLOCK' => $arItem['IBLOCK_ID'],
					'IS_PARENT' => (isset($arItem['SECTION_PAGE_URL']) && isset($arMenu[$arItem['ID']])) ? 1 : '',
					'DEPTH_LEVEL' => $level
				)
			);
      if (isset($arItem['SECTION_PAGE_URL']))
        self::display($arMenu, $arItem['ID'], $level+1, $aMenuLinks, $arParams);
		}
	}
}
?>