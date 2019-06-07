<?
/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var string           $parentComponentPath
 * @var string           $parentComponentName
 * @var string           $parentComponentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 */

use Bitrix\Main\Loader,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Type\DateTime;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

if(!Loader::includeModule('api.reviews')) {
	ShowError(Loc::getMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

class ApiReviewsFilterComponent extends \CBitrixComponent
{
	public function onPrepareComponentParams($params)
	{
		$params['FORM_ID'] = $this->getEditAreaId($this->randString());

		return $params;
	}

	public function executeComponent()
	{
		//$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		$arResult['SESSION_RATING'] = (array)$_SESSION['API_REVIEWS_RATING'];

		$this->IncludeComponentTemplate();
	}
}

