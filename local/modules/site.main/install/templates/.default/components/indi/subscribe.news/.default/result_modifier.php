<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

foreach ($arResult['IBLOCKS'] as &$iblock) {
	foreach ($iblock['ITEMS'] as &$item) {
		if (is_array($item['PREVIEW_PICTURE'])) {
			
			$item['PREVIEW_PICTURE'] = CFile::ResizeImageGet(
				$item['PREVIEW_PICTURE'],
				array(
					'width' => 200,
					'height' => 200
				),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			
			if (COption::GetOptionString('subscribe', 'attach_images') != 'Y'
				&& strpos($item['PREVIEW_PICTURE']['src'], 'http') !== 0
			) {
				$item['PREVIEW_PICTURE']['src'] = 'http://' . $arResult['SERVER_NAME'] . $item['PREVIEW_PICTURE']['src'];
			}
		}
	}
	unset($item);
}
unset($iblock);