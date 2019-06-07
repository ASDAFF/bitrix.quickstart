<?
use Bitrix\Main\Loader,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.reviews')) {
	ShowError(Loc::getMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

//Inc template lang
$templateFile = CApiReviews::getTemplateFile($this);
Loc::loadMessages($templateFile);

use Api\Reviews\Converter,
	 Api\Reviews\Tools,
	 Api\Reviews\ReviewsTable;


class ApiReviewsRecent extends \CBitrixComponent
{

	public function onPrepareComponentParams($arParams)
	{
		if(!isset($arParams['ITEMS_LIMIT'])) $arParams['ITEMS_LIMIT'] = 5;
		if(!isset($arParams['CACHE_TIME'])) $arParams['CACHE_TIME'] = 86400;
		if(!isset($arParams['ACTIVE_DATE_FORMAT'])) $arParams['ACTIVE_DATE_FORMAT'] = 'd-m-Y H:i:s';


		$arParams['DISPLAY_FIELDS'] = (array)$arParams['DISPLAY_FIELDS'];
		if($arParams['DISPLAY_FIELDS']) {
			foreach($arParams['DISPLAY_FIELDS'] as $k => $v) {
				if($v == '')
					unset($arParams['DISPLAY_FIELDS'][ $k ]);
			}
		}

		return $arParams;
	}

	public function executeComponent()
	{
		$this->initData();
	}

	protected function initData()
	{

		$obParser = new CTextParser();
		$arParams = $this->arParams;
		$arResult = &$this->arResult;

		//---------- $arSort ----------//
		$arSort = array();
		if($arParams['SORT_FIELD_1'] && $arParams['SORT_ORDER_1'])
			$arSort[ $arParams['SORT_FIELD_1'] ] = $arParams['SORT_ORDER_1'];

		if($arParams['SORT_FIELD_2'] && $arParams['SORT_ORDER_2'])
			$arSort[ $arParams['SORT_FIELD_2'] ] = $arParams['SORT_ORDER_2'];

		if(!$arSort)
			$arSort = array('ID' => 'DESC');


		//---------- $arSelect ----------//
		$arBaseSelect = array(
			 'ID',
			 //'ACTIVE_FROM',
			 //'DATE_CREATE',
			 'PAGE_URL',
		);
		$arDopSelect  = array_values($arParams['DISPLAY_FIELDS']);
		$arSelect     = array_merge($arBaseSelect, $arDopSelect);


		//---------- $arFilter ----------//
		$arFilter = array('=ACTIVE' => 'Y', '=SITE_ID' => SITE_ID);

		if($arParams['IBLOCK_ID'])
			$arFilter['=IBLOCK_ID'] = $arParams['IBLOCK_ID'];

		if($arParams['SECTION_ID'])
			$arFilter['=SECTION_ID'] = $arParams['SECTION_ID'];

		if($arParams['ELEMENT_ID'])
			$arFilter['=ELEMENT_ID'] = $arParams['ELEMENT_ID'];

		if($arParams['URL'])
			$arFilter['=URL'] = $arParams['URL'];


		if($this->startResultCache(false, array($arSort, $arFilter, $arParams['ITEMS_LIMIT']))) {

			$rsReviews = ReviewsTable::getList(array(
				 'order'  => $arSort,
				 'filter' => $arFilter,
				 'select' => $arSelect,
				 "limit"  => $arParams['ITEMS_LIMIT'],
			));

			while($arItem = $rsReviews->fetch(new Converter)) {

				if($arParams['TEXT_LIMIT'] > 0) {
					foreach($arParams['DISPLAY_FIELDS'] as $FIELD) {
						$arItem[ $FIELD ] = $obParser->html_cut($arItem[ $FIELD ], $arParams['TEXT_LIMIT']);
					}
				}

				if($arParams['USE_LINK'] == 'Y') {
					foreach($arParams['DISPLAY_FIELDS'] as $FIELD) {
						$arItem[ $FIELD ] = Converter::replace($arItem[ $FIELD ]);
					}
				}

				if(strlen($arItem['ACTIVE_FROM']) > 0)
					$arItem['DISPLAY_ACTIVE_FROM'] = Tools::formatDate($arParams['ACTIVE_DATE_FORMAT'], MakeTimeStamp($arItem['ACTIVE_FROM'], CSite::GetDateFormat()));
				else
					$arItem['DISPLAY_ACTIVE_FROM'] = '';


				$arResult['ITEMS'][] = $arItem;
			}

			$this->setResultCacheKeys(array());

			$this->includeComponentTemplate();
		}
	}
}