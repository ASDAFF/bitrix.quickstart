<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class CPrmediaMinimarketMapSalepoints extends CBitrixComponent
{
	public function onPrepareComponentParams($arParams)
	{
		if (CModule::IncludeModuleEx('prmedia.minimarket') == MODULE_DEMO_EXPIRED)
		{
			return false;
		}
		
		// include required modules
		if (!\Bitrix\Main\Loader::includeModule('catalog'))
		{
			ShowError(Loc::getMessage('PRMEDIA_MM_CSL_MODULE_CATALOG_ERROR'));
			return false;
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