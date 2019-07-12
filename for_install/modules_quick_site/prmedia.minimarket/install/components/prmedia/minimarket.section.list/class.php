<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class CPrmediaMinimarketCatalogSectionList extends CBitrixComponent
{
	public function onPrepareComponentParams($arParams)
	{
		// check demo version
		if (CModule::IncludeModuleEx('prmedia.minimarket') == MODULE_DEMO_EXPIRED)
		{
			return false;
		}
		
		// include required modules
		if (!\Bitrix\Main\Loader::includeModule('iblock'))
		{
			ShowError(Loc::getMessage('PRMEDIA_MM_CSL_MODULE_IBLOCK_ERROR'));
			return false;
		}

		// prepare component params
		$arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
		if ($arParams['CACHE_TIME'] <= 0)
		{
			$arParams['CACHE_TIME'] = 36000000;
		}
		$arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);
		if ($arParams['IBLOCK_ID'] <= 0)
		{
			ShowError(Loc::getMessage('PRMEDIA_MM_CSL_PARAM_IBLOCK_ID_ERROR'));
			return false;
		}
		$arParams['SECTION_URL'] = trim($arParams['SECTION_URL']);
		if (empty($arParams['SECTION_URL']))
		{
			unset($arParams['SECTION_URL']);
		}
		$arParams['TOP_DEPTH'] = intval($arParams['TOP_DEPTH']);
		if (!in_array($arParams['TOP_DEPTH'], array(1, 2)))
		{
			$arParams['TOP_DEPTH'] = 2;
		}

		return $arParams;
	}

	public function executeComponent()
	{
		// return if there is some errors
		if ($this->arParams === false)
		{
			return;
		}

		// component logic
		parent::executeComponent();
	}
}