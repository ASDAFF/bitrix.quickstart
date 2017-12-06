<?

use \Bitrix\Main\Loader;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/** @var array $arCurrentValues */

Loc::loadMessages(__FILE__);

$userFields = (array)Loc::getMessage('API_MAIN_PROFILE_PM_FIELDS');

$customFields = array();
if($customFields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('USER', 0, LANGUAGE_ID)) {
	foreach($customFields as $key => $val) {
		$customFields[ $val['FIELD_NAME'] ] = (strLen($val['EDIT_FORM_LABEL']) > 0 ? $val['EDIT_FORM_LABEL'] : $val['FIELD_NAME']);
	}
	unset($key, $val);
}


$requiredFields = $userFields;
if($arCurrentValues['USER_FIELDS']) {
	foreach($requiredFields as $key => $val) {
		if(!in_array($key, $arCurrentValues['USER_FIELDS']))
			unset($requiredFields[ $key ]);
	}
	unset($requiredFields['PASSWORD'], $requiredFields['CONFIRM_PASSWORD']);
}

$readonlyFields = $requiredFields;

//Count
$userFieldsCount = count($userFields);
//$userFieldsCount = $userFieldsCount <= 10 ? $userFieldsCount : 10;

$customFieldsCount = count($customFields);
$customFieldsCount = $customFieldsCount <= 10 ? $customFieldsCount : 10;

$requiredFieldsCount = count($requiredFields);
$requiredFieldsCount = $requiredFieldsCount <= 10 ? $requiredFieldsCount : 10;

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


$arComponentParameters['GROUPS']     = array();
$arComponentParameters['PARAMETERS'] = array(

	//BASE
	/*'SEND_MAIL'     => array(
		 'PARENT'  => 'BASE',
		 'NAME'    => Loc::getMessage('API_MAIN_PROFILE_PM_SEND_MAIL'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
	),*/
	'CHECK_RIGHTS'    => array(
		 'PARENT'  => 'BASE',
		 'NAME'    => Loc::getMessage('API_MAIN_PROFILE_PM_CHECK_RIGHTS'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
	),
	'USER_FIELDS'     => array(
		 'PARENT'   => 'BASE',
		 'NAME'     => Loc::getMessage('API_MAIN_PROFILE_PM_USER_FIELDS'),
		 'TYPE'     => 'LIST',
		 'VALUES'   => $userFields,
		 'MULTIPLE' => 'Y',
		 'SIZE'     => $userFieldsCount,
		 'REFRESH'  => 'Y',
		 'DEFAULT'  => array(),
	),
	'CUSTOM_FIELDS'   => array(
		 'PARENT'   => 'BASE',
		 'NAME'     => Loc::getMessage('API_MAIN_PROFILE_PM_CUSTOM_FIELDS'),
		 'TYPE'     => 'LIST',
		 'VALUES'   => $customFields,
		 'MULTIPLE' => 'Y',
		 'SIZE'     => $customFieldsCount,
		 'DEFAULT'  => array(),
	),
	'REQUIRED_FIELDS' => array(
		 'PARENT'   => 'BASE',
		 'NAME'     => Loc::getMessage('API_MAIN_PROFILE_PM_REQUIRED_FIELDS'),
		 'TYPE'     => 'LIST',
		 'VALUES'   => $requiredFields,
		 'MULTIPLE' => 'Y',
		 'SIZE'     => $requiredFieldsCount,
		 'DEFAULT'  => array(),
	),
	'READONLY_FIELDS' => array(
		 'PARENT'   => 'BASE',
		 'NAME'     => Loc::getMessage('API_MAIN_PROFILE_PM_READONLY_FIELDS'),
		 'TYPE'     => 'LIST',
		 'VALUES'   => $readonlyFields,
		 'MULTIPLE' => 'Y',
		 'SIZE'     => $requiredFieldsCount,
		 'DEFAULT'  => array(),
	),

	//VISUAL
	'SHOW_LABEL'      => array(
		 'PARENT'  => 'VISUAL',
		 'NAME'    => Loc::getMessage('API_MAIN_PROFILE_PM_SHOW_LABEL'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'Y',
	),
);
