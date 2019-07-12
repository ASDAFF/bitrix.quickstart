<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class CPrmediaMinimarketProfileLinks extends CBitrixComponent
{
	public function onPrepareComponentParams($arParams)
	{
		if (CModule::IncludeModuleEx('prmedia.minimarket') == MODULE_DEMO_EXPIRED)
		{
			return false;
		}
		
		// prepare component params
		$arParams['PATH_TO_PROFILE'] = trim($arParams['PATH_TO_PROFILE']);
		if (empty($arParams['PATH_TO_PROFILE']))
		{
			unset($arParams['PATH_TO_PROFILE']);
		}
		$arParams['PATH_TO_AUTH'] = trim($arParams['PATH_TO_AUTH']);
		if (empty($arParams['PATH_TO_AUTH']))
		{
			unset($arParams['PATH_TO_AUTH']);
		}
		$arParams['PATH_TO_REGISTER'] = trim($arParams['PATH_TO_REGISTER']);
		if (empty($arParams['PATH_TO_REGISTER']))
		{
			unset($arParams['PATH_TO_REGISTER']);
		}
		$arParams['NAME_TEMPLATE'] = trim($arParams['NAME_TEMPLATE']);
		if (empty($arParams['NAME_TEMPLATE']))
		{
			$arParams['NAME_TEMPLATE'] = CSite::GetNameFormat(false);
		}
		$arParams['NAME_TEMPLATE_LOGIN'] = trim($arParams['NAME_TEMPLATE_LOGIN']) === 'Y';

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