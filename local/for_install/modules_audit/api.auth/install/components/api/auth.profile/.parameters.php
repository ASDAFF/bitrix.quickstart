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
