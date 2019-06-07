<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * @var array            $arCurrentValues
 * @var CUserTypeManager $USER_FIELD_MANAGER
 */

use Bitrix\Main\Loader,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


if(!Loader::includeModule('api.reviews')) {
	ShowError(GetMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

if(!Loader::includeModule('iblock')) {
	ShowError(GetMessage('IBLOCK_MODULE_ERROR'));
	return;
}

use Api\Reviews\Tools;

$arSort  = Loc::getMessage('SORT_FIELDS');
$arOrder = Loc::getMessage('ORDER_FIELDS');


//---------- Группы параметров стандартные ----------//
//BASE                  (сортировка 100). Основные параметры.
//DATA_SOURCE           (сортировка 200). Тип и ID инфоблока.
//VISUAL                (сортировка 300). Редко используемая группа. Сюда предполагается загонять параметры, отвечающие за внешний вид.
//URL_TEMPLATES         (сортировка 400). Шаблоны ссылок
//SEF_MODE              (сортировка 500). Группа для всех параметров, связанных с использованием ЧПУ.
//AJAX_SETTINGS         (сортировка 550). Все, что касается ajax.
//CACHE_SETTINGS        (сортировка 600). Появляется при указании параметра CACHE_TIME.
//ADDITIONAL_SETTINGS   (сортировка 700). Эта группа появляется, например, при указании параметра SET_TITLE.

$arComponentParameters = array(
	 'GROUPS'     => array(
			'REVIEWS_FILTER' => array(
				 'NAME' => Loc::getMessage('REVIEWS_FILTER'),
				 'SORT' => 295,
			),
	 ),
	 'PARAMETERS' => array(
			'INCLUDE_CSS'        => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('INCLUDE_CSS'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
			'USE_LINK'           => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('USE_LINK'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'TEXT_LIMIT'         => Array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('TEXT_LIMIT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 150,
			),
			'ITEMS_LIMIT'        => Array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('ITEMS_LIMIT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 5,
			),
			'DISPLAY_FIELDS'     => Array(
				 'PARENT'   => 'BASE',
				 'NAME'     => Loc::getMessage('DISPLAY_FIELDS'),
				 'TYPE'     => 'LIST',
				 'VALUES'   => (array)Loc::getMessage('DISPLAY_FIELDS_VALUES'),
				 'DEFAULT'  => array('RATING', 'TITLE'),
				 'MULTIPLE' => 'Y',
				 'SIZE'     => 5,
			),
			'ACTIVE_DATE_FORMAT' => Tools::addDateParameters(Loc::getMessage('ACTIVE_DATE_FORMAT'), 'REVIEWS_RECENT'),
			'SORT_FIELD_1'       => Array(
				 'PARENT'            => 'BASE',
				 'NAME'              => Loc::getMessage('SORT_FIELD_1'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'ACTIVE_FROM',
				 'VALUES'            => $arSort,
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'SORT_ORDER_1'       => Array(
				 'PARENT'            => 'BASE',
				 'NAME'              => Loc::getMessage('SORT_ORDER_1'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'DESC',
				 'VALUES'            => $arOrder,
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'SORT_FIELD_2'       => Array(
				 'PARENT'            => 'BASE',
				 'NAME'              => Loc::getMessage('SORT_FIELD_2'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'DATE_CREATE',
				 'VALUES'            => $arSort,
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'SORT_ORDER_2'       => Array(
				 'PARENT'            => 'BASE',
				 'NAME'              => Loc::getMessage('SORT_ORDER_2'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'DESC',
				 'VALUES'            => $arOrder,
				 'ADDITIONAL_VALUES' => 'Y',
			),

			'IBLOCK_ID'  => array(
				 'PARENT'  => 'REVIEWS_FILTER',
				 'NAME'    => Loc::getMessage('IBLOCK_ID'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),
			'SECTION_ID' => array(
				 'PARENT'  => 'REVIEWS_FILTER',
				 'NAME'    => Loc::getMessage('SECTION_ID'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),
			'ELEMENT_ID' => array(
				 'PARENT'  => 'REVIEWS_FILTER',
				 'NAME'    => Loc::getMessage('ELEMENT_ID'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),
			'URL'        => array(
				 'PARENT'  => 'REVIEWS_FILTER',
				 'NAME'    => Loc::getMessage('URL'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),

			'HEADER_TITLE' => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => Loc::getMessage('HEADER_TITLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => Loc::getMessage('HEADER_TITLE_DEFAULT'),
			),
			'FOOTER_TITLE' => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => Loc::getMessage('FOOTER_TITLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => Loc::getMessage('FOOTER_TITLE_DEFAULT'),
			),
			'FOOTER_URL'   => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => Loc::getMessage('FOOTER_URL'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => Loc::getMessage('FOOTER_URL_DEFAULT'),
			),

			'CACHE_TIME' => Array('DEFAULT' => 86400),
	 ),
);