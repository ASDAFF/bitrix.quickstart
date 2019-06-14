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