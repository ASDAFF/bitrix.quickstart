<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CCacheManager $CACHE_MANAGER
 * @global CDatabase $DB
 * @param CBitrixComponent $this
 * @param array $this->arParams
 * @param array $this->arResult
 */

// localization messages
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $USER, $APPLICATION;
$userId = $USER->GetID();

if ($this->StartResultCache(false, $userId))
{
	$this->arResult = array();
	if ($USER->IsAuthorized())
	{
		$rsUser = CUser::GetByID($userId);
		$arUser = $rsUser->Fetch();

		$this->arResult['FIRST_LINK'] = str_replace('#ID#', $userId, $this->arParams['PATH_TO_PROFILE']);
		$this->arResult['FIRST_TITLE'] = CUser::FormatName($this->arParams['NAME_TEMPLATE'], $arUser, $this->arParams['NAME_TEMPLATE_LOGIN'], false);
		$paramsToRemove = array(
			'login',
			'logout',
			'register',
			'forgot_password',
			'change_password'
		);
		$this->arResult['SECOND_LINK'] = $APPLICATION->GetCurPageParam('logout=yes', $paramsToRemove);
		$this->arResult['SECOND_TITLE'] = Loc::getMessage('PRMEDIA_MM_PL_LOGOUT');
	}
	else
	{
		$this->arResult['FIRST_LINK'] = $this->arParams['PATH_TO_AUTH'];
		$this->arResult['FIRST_TITLE'] = Loc::getMessage('PRMEDIA_MM_PL_AUTH');
		$this->arResult['SECOND_LINK'] = $this->arParams['PATH_TO_REGISTER'];
		$this->arResult['SECOND_TITLE'] = Loc::getMessage('PRMEDIA_MM_PL_REGISTER');
	}

	$this->IncludeComponentTemplate();
}