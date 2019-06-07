<?
/**
 * @var array            $arCurrentValues
 * @var CUserTypeManager $USER_FIELD_MANAGER
 */

use Bitrix\Main\Loader,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);


if(!Loader::includeModule('api.qa')) {
	ShowError(GetMessage('API_QA_MODULE_ERROR'));
	return;
}

if(!Loader::includeModule('iblock')) {
	ShowError(GetMessage('IBLOCK_MODULE_ERROR'));
	return;
}

use Api\QA\Tools;

$arIBlockType = CIBlockParameters::GetIBlockTypes(Array('-' => GetMessage('API_QA_RECENT_CHOOSE')));
$arIBlock     = array();
$rsIBlock     = CIBlock::GetList(Array("ID" => "ASC"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arIBlock[ $arr["ID"] ] = "[" . $arr["ID"] . "] " . $arr["NAME"];
}


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
	 'GROUPS'     => array(),
	 'PARAMETERS' => array(
			'IBLOCK_TYPE' => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('API_QA_RECENT_IBLOCK_TYPE'),
				 'TYPE'    => 'LIST',
				 'VALUES'  => $arIBlockType,
				 'REFRESH' => 'Y',
			),
			'IBLOCK_ID'   => array(
				 'PARENT'            => 'BASE',
				 'NAME'              => Loc::getMessage('API_QA_RECENT_IBLOCK_ID'),
				 'TYPE'              => 'LIST',
				 'ADDITIONAL_VALUES' => 'Y',
				 'VALUES'            => $arIBlock,
				 'REFRESH'           => 'N',
			),
			/*'TEXT_LIMIT'              => Array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('API_QA_RECENT_TEXT_LIMIT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 100,
			),*/
			'ITEMS_LIMIT' => Array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('API_QA_RECENT_ITEMS_LIMIT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 7,
			),
			'DATE_FORMAT' => Tools::addDateParameters(Loc::getMessage('API_QA_RECENT_DATE_FORMAT'), 'BASE'),

			'HEADER_ON'    => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => Loc::getMessage('API_QA_RECENT_HEADER_ON'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
			'HEADER_TITLE' => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => Loc::getMessage('API_QA_RECENT_HEADER_TITLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => Loc::getMessage('API_QA_RECENT_HEADER_TITLE_DEFAULT'),
			),
			'TEXT_ON'      => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => Loc::getMessage('API_QA_RECENT_TEXT_ON'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
			'TEXT_LIMIT'   => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => Loc::getMessage('API_QA_RECENT_TEXT_LIMIT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 100,
			),
			'CACHE_TIME'   => Array('DEFAULT' => 3600),
			'INCLUDE_CSS'  => array(
				 'PARENT'  => 'ADDITIONAL_SETTINGS',
				 'NAME'    => Loc::getMessage('API_QA_RECENT_INCLUDE_CSS'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
	 ),
);