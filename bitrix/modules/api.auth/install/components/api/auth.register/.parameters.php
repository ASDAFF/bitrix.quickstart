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
	ShowError(Loc::getMessage('API_AUTH_MODULE_ERROR'));
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
			'AUTH'               => array(
				 'NAME'    => Loc::getMessage('AARP_AUTOMATED_AUTH'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
				 'PARENT'  => 'ADDITIONAL_SETTINGS',
			),
			'USE_BACKURL'        => array(
				 'NAME'    => Loc::getMessage('AARP_USE_BACKURL'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
				 'PARENT'  => 'ADDITIONAL_SETTINGS',
			),
			'SUCCESS_PAGE'       => array(
				 'NAME'    => Loc::getMessage('AARP_SUCCESS_PAGE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'ADDITIONAL_SETTINGS',
			),
			'SET_TITLE'          => array(),
			'USER_PROPERTY_NAME' => array(
				 'NAME'    => Loc::getMessage('AARP_USER_PROPERTY_NAME'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),
	 ),
);
