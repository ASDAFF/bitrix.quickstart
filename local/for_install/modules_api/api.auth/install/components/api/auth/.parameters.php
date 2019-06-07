<?
use Bitrix\Main\Loader,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * @var array            $arCurrentValues
 * @var CUserTypeManager $USER_FIELD_MANAGER
 */

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.auth')) {
	ShowError(GetMessage('API_AUTH_MODULE_ERROR'));
	return;
}

//---------- Группы параметров стандартные ----------//
//BASE                  (сортировка 100). Основные параметры.
//DATA_SOURCE           (сортировка 200). Источник данных. Тип и ID инфоблока.
//VISUAL                (сортировка 300). Редко используемая группа. Сюда предполагается загонять параметры, отвечающие за внешний вид.
//URL_TEMPLATES         (сортировка 400). Шаблоны ссылок
//SEF_MODE              (сортировка 500). Группа для всех параметров, связанных с использованием ЧПУ.
//AJAX_SETTINGS         (сортировка 550). Все, что касается ajax.
//CACHE_SETTINGS        (сортировка 600). Появляется при указании параметра CACHE_TIME.
//ADDITIONAL_SETTINGS   (сортировка 700). Эта группа появляется, например, при указании параметра SET_TITLE.


$arComponentParameters = array(
	 'PARAMETERS' => array(
			'MESS_AUTHORIZED' => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('AAP_MESS_AUTHORIZED'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => Loc::getMessage('AAP_MESS_AUTHORIZED_DEF'),
			),
			'SET_TITLE' => array(
				 'PARENT'  => 'ADDITIONAL_SETTINGS',
				 'NAME'    => Loc::getMessage('AAP_SET_TITLE'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
	 ),
);