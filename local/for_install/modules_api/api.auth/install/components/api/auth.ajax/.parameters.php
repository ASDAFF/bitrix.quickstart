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

//---------- ������ ���������� ����������� ----------//
//BASE                  (���������� 100). �������� ���������.
//DATA_SOURCE           (���������� 200). �������� ������. ��� � ID ���������.
//VISUAL                (���������� 300). ����� ������������ ������. ���� �������������� �������� ���������, ���������� �� ������� ���.
//URL_TEMPLATES         (���������� 400). ������� ������
//SEF_MODE              (���������� 500). ������ ��� ���� ����������, ��������� � �������������� ���.
//AJAX_SETTINGS         (���������� 550). ���, ��� �������� ajax.
//CACHE_SETTINGS        (���������� 600). ���������� ��� �������� ��������� CACHE_TIME.
//ADDITIONAL_SETTINGS   (���������� 700). ��� ������ ����������, ��������, ��� �������� ��������� SET_TITLE.
//COMPOSITE_SETTINGS    (���������� 800). ����������� ����

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