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
//COMPOSITE_SETTINGS    (сортировка 800). Композитный сайт

$arComponentParameters['GROUPS'] = array(
	 'LOGIN'    => array(
			'NAME' => Loc::getMessage('GROUP_LOGIN'),
			'SORT' => 10,
	 ),
	 'REGISTER' => array(
			'NAME' => Loc::getMessage('GROUP_REGISTER'),
			'SORT' => 20,
	 ),
	 'RESTORE'  => array(
			'NAME' => Loc::getMessage('GROUP_RESTORE'),
			'SORT' => 30,
	 ),
	 'PROFILE'  => array(
			'NAME' => Loc::getMessage('GROUP_PROFILE'),
			'SORT' => 40,
	 ),
);

$arComponentParameters['PARAMETERS'] = array(
	 'LOGIN_MESS_LINK'      => array(
			'PARENT'  => 'LOGIN',
			'TYPE'    => 'STRING',
			'NAME'    => Loc::getMessage('AAAP_LOGIN_MESS_LINK'),
			'DEFAULT' => Loc::getMessage('AAAP_LOGIN_MESS_LINK_DEF'),
	 ),
	 'LOGIN_MESS_HEADER'    => array(
		  'PARENT'  => 'LOGIN',
		  'TYPE'    => 'STRING',
		  'NAME'    => Loc::getMessage('AAAP_LOGIN_MESS_HEADER'),
		  'DEFAULT' => Loc::getMessage('AAAP_LOGIN_MESS_HEADER_DEF'),
	 ),
	 'LOGIN_BTN_CLASS'      => array(
		  'PARENT'  => 'LOGIN',
		  'TYPE'    => 'STRING',
		  'NAME'    => Loc::getMessage('AAAP_LOGIN_BTN_CLASS'),
		  'DEFAULT' => Loc::getMessage('AAAP_LOGIN_BTN_CLASS_DEF'),
	 ),

	 'REGISTER_MESS_LINK'   => array(
			'PARENT'  => 'REGISTER',
			'TYPE'    => 'STRING',
			'NAME'    => Loc::getMessage('AAAP_REGISTER_MESS_LINK'),
			'DEFAULT' => Loc::getMessage('AAAP_REGISTER_MESS_LINK_DEF'),
	 ),
	 'REGISTER_MESS_HEADER' => array(
			'PARENT'  => 'REGISTER',
			'TYPE'    => 'STRING',
			'NAME'    => Loc::getMessage('AAAP_REGISTER_MESS_HEADER'),
			'DEFAULT' => Loc::getMessage('AAAP_REGISTER_MESS_HEADER_DEF'),
	 ),
	 'REGISTER_BTN_CLASS'   => array(
		  'PARENT'  => 'REGISTER',
		  'TYPE'    => 'STRING',
		  'NAME'    => Loc::getMessage('AAAP_REGISTER_BTN_CLASS'),
		  'DEFAULT' => Loc::getMessage('AAAP_REGISTER_BTN_CLASS_DEF'),
	 ),

	 'RESTORE_MESS_HEADER'  => array(
			'PARENT'  => 'RESTORE',
			'TYPE'    => 'STRING',
			'NAME'    => Loc::getMessage('AAAP_RESTORE_MESS_HEADER'),
			'DEFAULT' => Loc::getMessage('AAAP_RESTORE_MESS_HEADER_DEF'),
	 ),

	 'PROFILE_URL'  => array(
			'PARENT'  => 'PROFILE',
			'TYPE'    => 'STRING',
			'NAME'    => Loc::getMessage('AAAP_PROFILE_URL'),
			'DEFAULT' => Loc::getMessage('AAAP_PROFILE_URL_DEF'),
	 ),
	 'LOGOUT_URL'  => array(
			'PARENT'  => 'PROFILE',
			'TYPE'    => 'STRING',
			'NAME'    => Loc::getMessage('AAAP_LOGOUT_URL'),
			'DEFAULT' => Loc::getMessage('AAAP_LOGOUT_URL_DEF'),
	 ),
);