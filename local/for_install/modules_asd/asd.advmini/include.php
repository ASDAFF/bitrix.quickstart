<?
class CASDAdvMini {

	private static $cache;

	public static function ModuleIncluded() {
		static $bState = null;
		if ($bState === null) {
			$bState = !(CModule::IncludeModuleEx('asd.advmini')===MODULE_DEMO_EXPIRED);
		}
		return $bState;
	}

	private static function StartPHPCache(&$arData) {
		self::$cache = $cache = new CPHPCache;
		$cache_time = 3600;
		$cache_id = SITE_ID;
		$cache_path = '/'.SITE_ID.'/asd.advmini';
		if ($cache_time>0 && $cache->InitCache($cache_time, $cache_id, $cache_path, 'cache')) {
			$Vars = $cache->GetVars();
			if (is_array($arData)) {
				foreach ($Vars['arCache'] as $k => $v) {
					$arData[$k] = $v;
				}
			} else {
				$arData = $Vars['arCache'];
			}
			return false;
		} elseif ($cache_time > 0) {
			$cache->StartDataCache($cache_time, $cache_id);
			if (defined('BX_COMP_MANAGED_CACHE')) {
				$GLOBALS['CACHE_MANAGER']->StartTagCache($cache_path);
			}
			return true;
		} else {
			return true;
		}
	}

	private static function EndPHPCache($arData) {
		self::$cache->EndDataCache(array('arCache' => $arData));
		if (defined('BX_COMP_MANAGED_CACHE')) {
			$GLOBALS['CACHE_MANAGER']->EndTagCache();
		}
	}

	public static function GetBanners() {
		$arBanners = array();
		$IB = COption::GetOptionString('asd.advmini', 'iblock_id_'.SITE_ID);
		if ($IB>0 && self::StartPHPCache(&$arBanners)) {
			if (CModule::IncludeModule('iblock')) {
				$arBanners['NULL'] = array();
				$arEnums = array();
				$rsEnum = CIBlockPropertyEnum::GetList(array(), array('CODE' => 'TYPE', 'IBLOCK_ID' => $IB));
				while ($arEnum = $rsEnum->Fetch()) {
					$arEnums[$arEnum['ID']] = $arEnum['XML_ID'];
				}
				$rsBanners = CIBlockElement::GetList(
													array(),
													array('IBLOCK_ID' => $IB, 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y', '!PROPERTY_TYPE' => false),
													false, false, array('ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE',
													'PROPERTY_LINK', 'PROPERTY_TARGET', 'PROPERTY_TYPE'));
				while ($arBanner = $rsBanners->GetNext()) {
					if (!strlen(trim($arBanner['PREVIEW_TEXT'])) && !$arBanner['PREVIEW_PICTURE']) {
						continue;
					}
					if (!strlen(trim($arBanner['PREVIEW_TEXT']))) {
						$arPic = CFile::GetFileArray($arBanner['PREVIEW_PICTURE']);
						$arBanner['PREVIEW_TEXT'] = '<img src="'.$arPic['SRC'].'" width="'.$arPic['WIDTH'].
																				'" height="'.$arPic['HEIGHT'].
																				'" alt="'.$arBanner['NAME'].'" />';
					}
					if (!isset($arBanners[$arEnums[$arBanner['PROPERTY_TYPE_ENUM_ID']]])) {
						$arBanners[$arEnums[$arBanner['PROPERTY_TYPE_ENUM_ID']]] = array();
					}
					$arBanners[$arEnums[$arBanner['PROPERTY_TYPE_ENUM_ID']]][] = array(
						'TITLE' => $arBanner['NAME'],
						'CODE' => $arBanner['PREVIEW_TEXT'],
						'TARGET' => $arBanner['PROPERTY_TARGET_VALUE']=='Y' ? 'Y' : 'N',
						'LINK' => trim($arBanner['~PROPERTY_LINK_VALUE']),
					);
				}
			}
			self::EndPHPCache($arBanners);
		}
		return $arBanners;
	}

	public function ShowDelay($type, $arParams){

		static $arBanners = array();
		if (empty($arBanners)) {
			$arBanners = self::GetBanners();
		}

		if (isset($arBanners[$type])) {
			shuffle($arBanners[$type]);
			$arBanner = array_pop($arBanners[$type]);
		} else {
			return '';
		}

		$innerA = '';
		$innerA .= isset($arParams['class']) ? ' class="'.$arParams['class'].'"' : '';
		$innerA .= $arBanner['TARGET']=='Y' ? ' target="blank"' : '';
		$banner = '<a href="'.$arBanner['LINK'].'" title="'.$arBanner['TITLE'].'"'.$innerA.'>'.$arBanner['CODE'].'</a>';

		if (isset($arParams['before'])) {
			$banner = $arParams['before'].$banner;
		}
		if (isset($arParams['after'])) {
			$banner .= $arParams['after'];
		}

		return $banner;
	}

	public static function Show($type, $arParams) {
		if (!self::ModuleIncluded()) {
			return false;
		}
		$GLOBALS['APPLICATION']->AddBufferContent(array('CASDAdvMini', 'ShowDelay'), $type, $arParams);
	}
}
?>