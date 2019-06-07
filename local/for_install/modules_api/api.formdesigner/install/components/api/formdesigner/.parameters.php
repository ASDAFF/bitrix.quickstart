<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/** @var array $arCurrentValues */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


if(!Loader::includeModule('api.formdesigner')) {
	ShowError(Loc::getMessage('AFD_INC_MODULE_ERROR'));
	return;
}

if(!Loader::includeModule('iblock')) {
	ShowError(Loc::getMessage('AFD_INC_IBLOCK_MODULE_ERROR'));
	return;
}

use Api\FormDesigner\Crm;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array('sort' => 'asc'), Array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y'));
while($arr = $rsIBlock->Fetch()) {
	$arIBlock[ $arr['ID'] ] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}



$bShowUserGroups  = false;
$arProperty       = $arPropertyNS = $arPropertyS = array('' => Loc::getMessage('CHOOSE'));
$arFullPropertyNS = $arPropertyNSTID = array();

if($arCurrentValues['IBLOCK_ID']) {
	$rsProp = CIBlockProperty::GetList(array('sort' => 'asc', 'name' => 'asc'), array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y'));
	while($arr = $rsProp->Fetch()) {
		//It's must be first
		if(($arr['PROPERTY_TYPE'] == 'N' || $arr['PROPERTY_TYPE'] == 'S') && !$arr['USER_TYPE']) {
			//It's must be first
			$arPropertyNSTID[ $arr['CODE'] ] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];
			if($arr['CODE'] == 'TICKET_ID')
				continue;

			$arPropertyNS[ $arr['CODE'] ] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];

			if($arr['PROPERTY_TYPE'] == 'S')
				$arPropertyS[ $arr['CODE'] ] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];
		}

		if($arr['PROPERTY_TYPE'] != 'F')
			$arFullPropertyNS[ $arr['CODE'] ] = $arr;

		if($arr['USER_TYPE'] == 'UserID')
			$bShowUserGroups = true;

		//All enabled properties
		$arProperty[ $arr['CODE'] ] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];
	}
}


$site     = ($_REQUEST['site'] <> '' ? $_REQUEST['site'] : ($_REQUEST['src_site'] <> '' ? $_REQUEST['src_site'] : false));
$arFilter = Array(
	 'TYPE_ID' => 'API_FORMDESIGNER',
	 'ACTIVE'  => 'Y',
);
if($site !== false) {
	$arFilter['LID'] = $site;
}

$arEvent     = array();
$eventMess_1 = 0;

$rsMess = CEventMessage::GetList($by = 'id', $order = 'asc', $arFilter);
while($arMess = $rsMess->Fetch()) {
	if(!$eventMess_1)
		$eventMess_1 = $arMess['ID'];

	$arEvent[ $arMess['ID'] ] = '[' . $arMess['ID'] . '] ' . $arMess['SUBJECT'];
}

$rsSite = CSite::GetList($by = 'sort', $order = 'desc', array('ID' => $site));
$arSite = $rsSite->Fetch();



//---------- CRM list ----------//
$arCrmId = array();
$rsCrm   = Crm\CrmTable::getList();
while($row = $rsCrm->fetch()) {
	$arCrmId[ $row['ID'] ] = '[' . $row['ID'] . '] ' . $row['NAME'];
}

$arCrmFieldsId = array('' => Loc::getMessage('CHOOSE'));
if($arCurrentValues['CRM_ON'] == 'Y') {
	if($crmId = $arCurrentValues['CRM_ID']) {
		$link = new Crm\Lead($crmId);
		if($arCrmFields = $link->getFields()) {
			foreach($arCrmFields as $field) {
				if($field['TYPE'] != 'file')
					$arCrmFieldsId[ $field['ID'] ] = '[' . $field['ID'] . '] ' . $field['NAME'];
			}
		}
	}

	unset($arCrmFields, $field, $crmId, $link);
}


// Get user group list
$arGroups = array();
if($bShowUserGroups) {
	$rsGroups = CGroup::GetList($by = "c_sort", $order = "asc", Array("ACTIVE" => "Y"));
	while($arGroup = $rsGroups->Fetch()) {
		$arGroups[ $arGroup["ID"] ] = $arGroup["NAME"];
	}
}

$arServerVars = array();
if(is_array($_SERVER) && !empty($_SERVER)) {
	$arExcludeServerVars = array('PATH', 'SystemRoot', 'COMSPEC', 'PATHEXT', 'WINDIR', 'argv', 'argc');
	foreach($_SERVER as $key => $val) {
		if(!in_array($key, $arExcludeServerVars))
			$arServerVars[ $key ] = $key;
	}
}

//---------- Группы параметров стандартные ----------//
//BASE                  (сортировка 100). Основные параметры.
//DATA_SOURCE           (сортировка 200). Тип и ID инфоблока.
//VISUAL                (сортировка 300). Внешний вид.
//URL_TEMPLATES         (сортировка 400). Шаблоны ссылок
//SEF_MODE              (сортировка 500). ЧПУ.
//AJAX_SETTINGS         (сортировка 550). AJAX.
//CACHE_SETTINGS        (сортировка 600). Кэширование.
//ADDITIONAL_SETTINGS   (сортировка 700). Доп. настройки.
//COMPOSITE_SETTINGS    (сортировка 800). Композитный сайт

$arComponentParameters['GROUPS'] = array(
	 'THEME_SETTINGS'     => array(
			'NAME' => Loc::getMessage('THEME_SETTINGS'),
			'SORT' => 301,
	 ),
	 'IBLOCK_SETTINGS'    => array(
			'NAME' => Loc::getMessage('IBLOCK_SETTINGS'),
			'SORT' => 620,
	 ),
	 'POST_SETTINGS'      => array(
			'NAME' => Loc::getMessage('POST_SETTINGS'),
			'SORT' => 630,
	 ),
	 'FILES_SETTINGS'     => array(
			'NAME' => Loc::getMessage('FILES_SETTINGS'),
			'SORT' => 640,
	 ),
	 'MODAL_SETTINGS'     => array(
			'NAME' => Loc::getMessage('MODAL_SETTINGS'),
			'SORT' => 700,
	 ),
	 'YAMETRIKA_SETTINGS' => array(
			'NAME' => Loc::getMessage('YAMETRIKA_SETTINGS'),
			'SORT' => 710,
	 ),
	 'YM2_SETTINGS' => array(
			'NAME' => Loc::getMessage('YM2_SETTINGS'),
			'SORT' => 711,
	 ),
	 'GA_SETTINGS'        => array(
			'NAME' => Loc::getMessage('GA_SETTINGS'),
			'SORT' => 720,
	 ),
	 'EULA'               => array(
			'NAME' => Loc::getMessage('GROUP_EULA'),
			'SORT' => 721,
	 ),
	 'PRIVACY'            => array(
			'NAME' => Loc::getMessage('GROUP_PRIVACY'),
			'SORT' => 722,
	 ),
	 'USER_CONSENT'       => array(
			"NAME" => Loc::getMessage("GROUP_USER_CONSENT"),
			"SORT" => 723,
	 ),
	 'COMP_VARS'          => array(
			'NAME' => Loc::getMessage('GROUP_COMP_VARS'),
			'SORT' => 724,
	 ),
	 'USER_VARS'          => array(
			'NAME' => Loc::getMessage('GROUP_USER_VARS'),
			'SORT' => 725,
	 ),
	 'WYSIWYG'            => array(
			'NAME' => Loc::getMessage('GROUP_WYSIWYG'),
			'SORT' => 726,
	 ),
	 'JQUERY_SETTINGS'    => array(
			'NAME' => Loc::getMessage('JQUERY_SETTINGS'),
			'SORT' => 730,
	 ),
	 'POWERTIP_SETTINGS'  => array(
			'NAME' => Loc::getMessage('POWERTIP_SETTINGS'),
			'SORT' => 810,
	 ),
	 'INPUTMASK_SETTINGS' => array(
			'NAME' => Loc::getMessage('INPUTMASK_SETTINGS'),
			'SORT' => 820,
	 ),
	 'VALIDATE_SETTINGS'  => array(
			'NAME' => Loc::getMessage('VALIDATE_SETTINGS'),
			'SORT' => 830,
	 ),
);

//bitrix/modules/main/classes/general/component_util.php
$arComponentParameters['PARAMETERS'] = array(
	 'IBLOCK_TYPE'     => array(
			'PARENT'            => 'BASE',
			'NAME'              => Loc::getMessage('IBLOCK_TYPE'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arIBlockType,
			'REFRESH'           => 'Y',
			'ADDITIONAL_VALUES' => 'Y',
	 ),
	 'IBLOCK_ID'       => array(
			'PARENT'            => 'BASE',
			'NAME'              => Loc::getMessage('IBLOCK_ID'),
			'TYPE'              => 'LIST',
			'ADDITIONAL_VALUES' => 'Y',
			'VALUES'            => $arIBlock,
			'REFRESH'           => 'Y',
	 ),
	 'UNIQUE_FORM_ID'  => Array(
			'NAME'    => Loc::getMessage('UNIQUE_FORM_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '', //'form' . mt_rand(1, 10)
			'COLS'    => 45,
			'PARENT'  => 'BASE',
	 ),
	 'REDIRECT_URL'    => Array(
			'PARENT'  => 'BASE',
			'NAME'    => Loc::getMessage('REDIRECT_URL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
	 ),
	 'ENABLED_FIELDS'  => array(
			'PARENT'            => 'BASE',
			'NAME'              => Loc::getMessage('ENABLED_FIELDS'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arProperty,
			'ADDITIONAL_VALUES' => 'N',
			'DEFAULT'           => '',
			'MULTIPLE'          => 'Y',
			'SIZE'              => (count($arProperty) > 10 ? 10 : count($arProperty)),
	 ),
	 'POST_EMAIL_CODE' => array(
			'PARENT'            => 'BASE',
			'NAME'              => Loc::getMessage('POST_EMAIL_CODE'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arPropertyS,
			'DEFAULT'           => '',
			'MULTIPLE'          => 'N',
			'ADDITIONAL_VALUES' => 'Y',
	 ),

	 'JQUERY_ON' => array(
			'PARENT'  => 'JQUERY_SETTINGS',
			'TYPE'    => 'CHECKBOX',
			'NAME'    => Loc::getMessage('JQUERY_ON'),
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
	 ),


	 'IBLOCK_ON'           => Array(
			'NAME'    => Loc::getMessage('IBLOCK_ON'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
			'PARENT'  => 'IBLOCK_SETTINGS',
	 ),
	 'SHOW_ERRORS'         => array(
			'PARENT'  => 'VISUAL',
			'NAME'    => Loc::getMessage('SHOW_ERRORS'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
	 ),
	 'MESS_REQUIRED_FIELD' => Array(
			'PARENT'  => 'VISUAL',
			'TYPE'    => 'STRING',
			'COLS'    => 42,
			'NAME'    => Loc::getMessage('MESS_REQUIRED_FIELD'),
			'DEFAULT' => Loc::getMessage('MESS_REQUIRED_FIELD_DEFAULT'),
	 ),
	 'MESS_CHECK_EMAIL'    => Array(
			'PARENT'  => 'VISUAL',
			'TYPE'    => 'STRING',
			'COLS'    => 42,
			'NAME'    => Loc::getMessage('MESS_CHECK_EMAIL'),
			'DEFAULT' => Loc::getMessage('MESS_CHECK_EMAIL_DEFAULT'),
	 ),
	 'HIDE_FIELDS'         => array(
			'NAME'              => Loc::getMessage('HIDE_FIELDS'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arPropertyNS,
			'DEFAULT'           => '',
			'PARENT'            => 'VISUAL',
			'ADDITIONAL_VALUES' => 'N',
			'MULTIPLE'          => 'Y',
	 ),
	 'DIVIDER_FIELDS'      => array(
			'NAME'              => Loc::getMessage('DIVIDER_FIELDS'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arPropertyS,
			'DEFAULT'           => '',
			'PARENT'            => 'VISUAL',
			'ADDITIONAL_VALUES' => 'N',
			'MULTIPLE'          => 'Y',
	 ),
	 'FORM_WIDTH'          => array(
			'PARENT'  => 'VISUAL',
			'TYPE'    => 'STRING',
			'NAME'    => Loc::getMessage('FORM_WIDTH'),
			'DEFAULT' => Loc::getMessage('FORM_WIDTH_DEFAULT'),
	 ),
	 'FORM_TITLE'          => array(
			'NAME'    => Loc::getMessage('FORM_TITLE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_TITLE_TEXT'),
			'COLS'    => 45,
			'PARENT'  => 'VISUAL',
	 ),
	 'SHOW_TITLE'          => array(
			'NAME'    => Loc::getMessage('SHOW_TITLE'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT'  => 'VISUAL',
	 ),
	 /*'FORM_AUTOCOMPLETE'   => array(
			'NAME'    => Loc::getMessage('FORM_AUTOCOMPLETE'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'PARENT'  => 'VISUAL',
	 ),*/
	 'FORM_HORIZONTAL'         => array(
		  'PARENT'  => 'VISUAL',
		  'NAME'    => Loc::getMessage('FORM_HORIZONTAL'),
		  'TYPE'    => 'CHECKBOX',
		  'DEFAULT' => 'N',
	 ),

	 'MESS_SUCCESS'        => Array(
			'NAME'    => Loc::getMessage('MESS_SUCCESS'),
			'PARENT'  => 'VISUAL',
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
			'DEFAULT' => Loc::getMessage('MESS_SUCCESS_TEXT'),
	 ),
	 'MESS_SUCCESS_DESC'   => Array(
			'NAME'    => Loc::getMessage('MESS_SUCCESS_DESC'),
			'PARENT'  => 'VISUAL',
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
			'DEFAULT' => Loc::getMessage('MESS_SUCCESS_DESC_DEFAULT'),
	 ),
	 'SUBMIT_BUTTON_CLASS' => array(
			'NAME'    => Loc::getMessage('SUBMIT_BUTTON_CLASS'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('SUBMIT_BUTTON_CLASS_DEFAULT'),
			'COLS'    => 42,
			'PARENT'  => 'VISUAL',
	 ),
	 'SUBMIT_BUTTON_TEXT'  => array(
			'NAME'    => Loc::getMessage('SUBMIT_BUTTON_TEXT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('SUBMIT_BUTTON_TEXT_VALUE'),
			'COLS'    => 42,
			'PARENT'  => 'VISUAL',
	 ),
	 'SUBMIT_BUTTON_AJAX'  => array(
			'TYPE'    => 'STRING',
			'PARENT'  => 'VISUAL',
			'COLS'    => 42,
			'NAME'    => Loc::getMessage('SUBMIT_BUTTON_AJAX'),
			'DEFAULT' => Loc::getMessage('SUBMIT_BUTTON_AJAX_DEFAULT'),
	 ),
	 'MESS_CHOOSE'         => array(
			'NAME'    => Loc::getMessage('MESS_CHOOSE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('MESS_CHOOSE_DEFAULT'),
			'COLS'    => 42,
			'PARENT'  => 'VISUAL',
	 ),
	 'USE_BX_CAPTCHA'      => array(
			'NAME'    => Loc::getMessage('USE_BX_CAPTCHA'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT'  => 'VISUAL',
	 ),

	 //YAMETRIKA_ON
	 'POST_ON'             => Array(
			'NAME'    => Loc::getMessage('POST_ON'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
			'PARENT'  => 'POST_SETTINGS',
	 ),

	 'UPLOAD_FILE_SIZE'  => array(
			'NAME'    => Loc::getMessage('UPLOAD_FILE_SIZE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '10M',
			'COLS'    => 42,
			'PARENT'  => 'FILES_SETTINGS',
	 ),
	 'UPLOAD_FILE_LIMIT' => array(
			'NAME'    => Loc::getMessage('UPLOAD_FILE_LIMIT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 5,
			'COLS'    => 42,
			'PARENT'  => 'FILES_SETTINGS',
	 ),
	 'UPLOAD_FOLDER'     => array(
			'NAME'    => Loc::getMessage('UPLOAD_FOLDER'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '/upload/api_formdesigner',
			'COLS'    => 42,
			'PARENT'  => 'FILES_SETTINGS',
	 ),

	 'USE_MODAL' => Array(
			'NAME'    => Loc::getMessage('USE_MODAL'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
			'PARENT'  => 'MODAL_SETTINGS',
	 ),

	 'YAMETRIKA_ON' => Array(
			'NAME'    => Loc::getMessage('YAMETRIKA_ON'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
			'PARENT'  => 'YAMETRIKA_SETTINGS',
	 ),
	 'YM2_ON' => Array(
			'NAME'    => Loc::getMessage('YM2_ON'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
			'PARENT'  => 'YM2_SETTINGS',
	 ),
	 'GA_ON'        => Array(
			'NAME'    => Loc::getMessage('GA_ON'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
			'PARENT'  => 'GA_SETTINGS',
	 ),

	 'USE_EULA'     => Array(
			'PARENT'  => 'EULA',
			'NAME'    => Loc::getMessage('USE_EULA'),
			'TYPE'    => 'CHECKBOX',
			'REFRESH' => 'Y',
	 ),
	 'USE_PRIVACY'  => Array(
			'PARENT'  => 'PRIVACY',
			'NAME'    => Loc::getMessage('USE_PRIVACY'),
			'TYPE'    => 'CHECKBOX',
			'REFRESH' => 'Y',
	 ),
	 'USER_CONSENT' => Array(
			'PARENT'  => 'USER_CONSENT',
			'NAME'    => Loc::getMessage('USER_CONSENT_USE'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
	 ),

	 'INPUTMASK_ON' => Array(
			'NAME'    => Loc::getMessage('INPUTMASK_ON'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
			'PARENT'  => 'INPUTMASK_SETTINGS',
	 ),

	 //VARS
	 'PAGE_VARS'    => array(
			'PARENT'            => 'COMP_VARS',
			'NAME'              => Loc::getMessage('PAGE_VARS'),
			'TYPE'              => 'LIST',
			'VALUES'            => Loc::getMessage('PAGE_VARS_VALUES'),
			'MULTIPLE'          => 'Y',
			'SIZE'              => 8,
			'DEFAULT'           => '',
			'ADDITIONAL_VALUES' => 'Y',
	 ),
	 'SERVER_VARS'  => array(
			'PARENT'            => 'COMP_VARS',
			'NAME'              => Loc::getMessage('SERVER_VARS'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arServerVars,
			'MULTIPLE'          => 'Y',
			'SIZE'              => 10,
			'DEFAULT'           => '',
			'ADDITIONAL_VALUES' => 'Y',
	 ),
	 'UTM_VARS'     => array(
			'PARENT'            => 'COMP_VARS',
			'NAME'              => Loc::getMessage('UTM_VARS'),
			'TYPE'              => 'LIST',
			'VALUES'            => array(
				 'utm_source'   => 'utm_source',
				 'utm_medium'   => 'utm_medium',
				 'utm_campaign' => 'utm_campaign',
				 'utm_content'  => 'utm_content',
				 'utm_term'     => 'utm_term',
			),
			'MULTIPLE'          => 'Y',
			'SIZE'              => 6,
			'DEFAULT'           => '',
			'ADDITIONAL_VALUES' => 'Y',
	 ),

	 //USER_VARS
	 'PAGE_TITLE'   => array(
			'PARENT' => 'USER_VARS',
			'NAME'   => Loc::getMessage('API_FD_PARAMS_PAGE_TITLE'),
			'TYPE'   => 'STRING',
			'COLS'   => 42,
	 ),
	 'PAGE_URL'     => array(
			'PARENT' => 'USER_VARS',
			'NAME'   => Loc::getMessage('API_FD_PARAMS_PAGE_URL'),
			'TYPE'   => 'STRING',
			'COLS'   => 42,
	 ),
	 'DIR_URL'      => array(
			'PARENT' => 'USER_VARS',
			'NAME'   => Loc::getMessage('API_FD_PARAMS_DIR_URL'),
			'TYPE'   => 'STRING',
			'COLS'   => 42,
	 ),
	 'DATE_TIME'    => array(
			'PARENT' => 'USER_VARS',
			'NAME'   => Loc::getMessage('API_FD_PARAMS_DATE_TIME'),
			'TYPE'   => 'STRING',
			'COLS'   => 42,
	 ),
	 'DATE'         => array(
			'PARENT' => 'USER_VARS',
			'NAME'   => Loc::getMessage('API_FD_PARAMS_DATE'),
			'TYPE'   => 'STRING',
			'COLS'   => 42,
	 ),
	 'IP'           => array(
			'PARENT' => 'USER_VARS',
			'NAME'   => Loc::getMessage('API_FD_PARAMS_IP'),
			'TYPE'   => 'STRING',
			'COLS'   => 42,
	 ),


	 /*'USE_POWERTIP'  => Array(
		 'NAME'    => Loc::getMessage('USE_POWERTIP'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'REFRESH' => 'Y',
		 'PARENT'  => 'POWERTIP_SETTINGS',
	 ),*/
	 'VALIDATE_ON'  => Array(
			'NAME'    => Loc::getMessage('VALIDATE_ON'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
			'PARENT'  => 'VALIDATE_SETTINGS',
	 ),

	 'CACHE_TIME' => array('DEFAULT' => 86400 * 365),
);

if(Loader::includeModule('api.core')) {

	//WYSIWYG
	$arComponentParameters['PARAMETERS']['WYSIWYG_ON'] = Array(
		 'PARENT'  => 'WYSIWYG',
		 'NAME'    => Loc::getMessage('WYSIWYG_ON'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'REFRESH' => 'N',
	);
}

if($arCurrentValues['USE_MODAL'] == 'Y') {
	$arComponentParameters['PARAMETERS']['MODAL_ID']             = array(
		 'NAME'    => Loc::getMessage('MODAL_ID'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '#api_fd_modal_' . $arCurrentValues['UNIQUE_FORM_ID'],
		 'COLS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_BTN_TEXT']       = array(
		 'NAME'    => Loc::getMessage('MODAL_BTN_TEXT'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('MODAL_BTN_TEXT_DEFAULT'),
		 'COLS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_BTN_CLASS']      = array(
		 'NAME'    => Loc::getMessage('MODAL_BTN_CLASS'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('MODAL_BTN_CLASS_DEFAULT'),
		 'COLS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_BTN_ID']         = array(
		 'NAME'    => Loc::getMessage('MODAL_BTN_ID'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('MODAL_BTN_ID_DEFAULT'),
		 'COLS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_BTN_SPAN_CLASS'] = array(
		 'NAME'    => Loc::getMessage('MODAL_BTN_SPAN_CLASS'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('MODAL_BTN_SPAN_CLASS_DEFAULT'),
		 'COLS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_HEADER_TEXT']    = array(
		 'NAME'    => Loc::getMessage('MODAL_HEADER_TEXT'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('MODAL_HEADER_TEXT_DEFAULT'),
		 'ROWS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_FOOTER_TEXT']    = array(
		 'NAME'    => Loc::getMessage('MODAL_FOOTER_TEXT'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'ROWS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
}

if($arCurrentValues['USE_EULA'] == 'Y') {
	$arComponentParameters['PARAMETERS']['MESS_EULA']         = Array(
		 'PARENT'  => 'EULA',
		 'NAME'    => Loc::getMessage('MESS_EULA'),
		 'TYPE'    => 'STRING',
		 'ROWS'    => 4,
		 'DEFAULT' => Loc::getMessage('MESS_EULA_DEFAULT'),
	);
	$arComponentParameters['PARAMETERS']['MESS_EULA_CONFIRM'] = Array(
		 'PARENT'  => 'EULA',
		 'NAME'    => Loc::getMessage('MESS_EULA_CONFIRM'),
		 'TYPE'    => 'STRING',
		 'ROWS'    => 4,
		 'DEFAULT' => Loc::getMessage('MESS_EULA_CONFIRM_DEFAULT'),
	);
}

if($arCurrentValues['USE_PRIVACY'] == 'Y') {
	$arComponentParameters['PARAMETERS']['MESS_PRIVACY']         = Array(
		 'PARENT'  => 'PRIVACY',
		 'NAME'    => Loc::getMessage('MESS_PRIVACY'),
		 'TYPE'    => 'STRING',
		 'ROWS'    => 4,
		 'DEFAULT' => Loc::getMessage('MESS_PRIVACY_DEFAULT'),
	);
	$arComponentParameters['PARAMETERS']['MESS_PRIVACY_LINK']    = Array(
		 'PARENT'  => 'PRIVACY',
		 'NAME'    => Loc::getMessage('MESS_PRIVACY_LINK'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
	);
	$arComponentParameters['PARAMETERS']['MESS_PRIVACY_CONFIRM'] = Array(
		 'PARENT'  => 'PRIVACY',
		 'NAME'    => Loc::getMessage('MESS_PRIVACY_CONFIRM'),
		 'TYPE'    => 'STRING',
		 'ROWS'    => 4,
		 'DEFAULT' => Loc::getMessage('MESS_PRIVACY_CONFIRM_DEFAULT'),
	);
}

if($bShowUserGroups) {
	$arComponentParameters['PARAMETERS']['GROUPS_ID'] = array(
		 'PARENT'            => 'BASE',
		 'TYPE'              => 'LIST',
		 'NAME'              => Loc::getMessage('GROUPS_ID'),
		 'VALUES'            => $arGroups,
		 'DEFAULT'           => '',
		 'ADDITIONAL_VALUES' => 'N',
		 'MULTIPLE'          => 'Y',
	);
}

$arComponentParameters['PARAMETERS']['COMPATIBLE_ON'] = array(
	 'PARENT'  => 'BASE',
	 'TYPE'    => 'CHECKBOX',
	 'NAME'    => Loc::getMessage('COMPATIBLE_ON'),
	 'DEFAULT' => 'N',
);

if($arCurrentValues['IBLOCK_ON'] == 'Y' && $arCurrentValues['IBLOCK_ID']) {
	$arComponentParameters['PARAMETERS']['IBLOCK_TICKET_CODE']    = array(
		 'NAME'              => Loc::getMessage('IBLOCK_TICKET_CODE'),
		 'TYPE'              => 'LIST',
		 'MULTIPLE'          => 'N',
		 'VALUES'            => $arPropertyNSTID,
		 'DEFAULT'           => 'TICKET_ID',
		 'PARENT'            => 'IBLOCK_SETTINGS',
		 'ADDITIONAL_VALUES' => 'Y',
	);
	$arComponentParameters['PARAMETERS']['IBLOCK_ELEMENT_NAME']   = Array(
		 'NAME'    => Loc::getMessage('IBLOCK_ELEMENT_NAME'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('IBLOCK_ELEMENT_NAME_DEFAULT'),
		 'PARENT'  => 'IBLOCK_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['IBLOCK_ELEMENT_CODE']   = Array(
		 'NAME'    => Loc::getMessage('IBLOCK_ELEMENT_CODE'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('IBLOCK_ELEMENT_CODE_DEFAULT'),
		 'PARENT'  => 'IBLOCK_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['IBLOCK_ELEMENT_ACTIVE'] = array(
		 'NAME'    => Loc::getMessage('IBLOCK_ELEMENT_ACTIVE'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'PARENT'  => 'IBLOCK_SETTINGS',
	);
}

if($arCurrentValues['POST_ON'] == 'Y') {
	$arComponentParameters['PARAMETERS']['POST_REPLACE_FROM']     = array(
		 'PARENT'  => 'POST_SETTINGS',
		 'TYPE'    => 'CHECKBOX',
		 'NAME'    => Loc::getMessage('POST_REPLACE_FROM'),
		 'DEFAULT' => 'N',
	);
	$arComponentParameters['PARAMETERS']['POST_EMAIL_FROM']       = array(
		 'PARENT'  => 'POST_SETTINGS',
		 'TYPE'    => 'STRING',
		 'NAME'    => Loc::getMessage('POST_EMAIL_FROM'),
		 'DEFAULT' => '', //$arSite['EMAIL'] ? $arSite['EMAIL'] : COption::GetOptionString('main', 'email_from')
	);
	$arComponentParameters['PARAMETERS']['POST_EMAIL_TO']         = array(
		 'PARENT'  => 'POST_SETTINGS',
		 'TYPE'    => 'STRING',
		 'NAME'    => Loc::getMessage('POST_EMAIL_TO'),
		 'DEFAULT' => '', //$arSite['EMAIL'] ? $arSite['EMAIL'] : COption::GetOptionString('main', 'email_from')
	);
	$arComponentParameters['PARAMETERS']['POST_ADMIN_MESSAGE_ID'] = array(
		 'PARENT'            => 'POST_SETTINGS',
		 'TYPE'              => 'LIST',
		 'NAME'              => Loc::getMessage('POST_ADMIN_MESSAGE_ID'),
		 'VALUES'            => $arEvent,
		 'DEFAULT'           => $eventMess_1,
		 'MULTIPLE'          => 'Y',
		 'ADDITIONAL_VALUES' => 'Y',
	);
	$arComponentParameters['PARAMETERS']['POST_ADMIN_SUBJECT']    = array(
		 'PARENT'  => 'POST_SETTINGS',
		 'TYPE'    => 'STRING',
		 'NAME'    => Loc::getMessage('POST_ADMIN_SUBJECT'),
		 'DEFAULT' => Loc::getMessage('POST_ADMIN_SUBJECT_DEFAULT'),
	);
	$arComponentParameters['PARAMETERS']['POST_USER_MESSAGE_ID']  = array(
		 'PARENT'            => 'POST_SETTINGS',
		 'TYPE'              => 'LIST',
		 'NAME'              => Loc::getMessage('POST_USER_MESSAGE_ID'),
		 'VALUES'            => $arEvent,
		 'DEFAULT'           => '',
		 'MULTIPLE'          => 'Y',
		 'ADDITIONAL_VALUES' => 'Y',
	);
	$arComponentParameters['PARAMETERS']['POST_USER_SUBJECT']     = array(
		 'PARENT'  => 'POST_SETTINGS',
		 'TYPE'    => 'STRING',
		 'NAME'    => Loc::getMessage('POST_USER_SUBJECT'),
		 'DEFAULT' => Loc::getMessage('POST_USER_SUBJECT_DEFAULT'),
	);

	$arComponentParameters['PARAMETERS']['POST_MESS_STYLE_WRAP']  = array(
		 'PARENT'  => 'POST_SETTINGS',
		 'TYPE'    => 'STRING',
		 'NAME'    => Loc::getMessage('POST_MESS_STYLE_WRAP'),
		 'DEFAULT' => Loc::getMessage('POST_MESS_STYLE_WRAP_DEFAULT'),
	);
	$arComponentParameters['PARAMETERS']['POST_MESS_STYLE_NAME']  = array(
		 'PARENT'  => 'POST_SETTINGS',
		 'TYPE'    => 'STRING',
		 'NAME'    => Loc::getMessage('POST_MESS_STYLE_NAME'),
		 'DEFAULT' => Loc::getMessage('POST_MESS_STYLE_NAME_DEFAULT'),
	);
	$arComponentParameters['PARAMETERS']['POST_MESS_STYLE_VALUE'] = array(
		 'PARENT'  => 'POST_SETTINGS',
		 'TYPE'    => 'STRING',
		 'NAME'    => Loc::getMessage('POST_MESS_STYLE_VALUE'),
		 'DEFAULT' => Loc::getMessage('POST_MESS_STYLE_VALUE_DEFAULT'),
	);
}


if($arCrmId) {
	//CRM_SETTINGS
	$arComponentParameters['GROUPS']['GROUP_CRM'] = array(
		 'NAME' => Loc::getMessage('GROUP_CRM'),
		 'SORT' => 850,
	);

	$arComponentParameters['PARAMETERS']['CRM_ON'] = array(
		 'NAME'    => Loc::getMessage('CRM_ON'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'REFRESH' => 'Y',
		 'PARENT'  => 'GROUP_CRM',
	);


	if($arCurrentValues['CRM_ON'] == 'Y') {
		$arComponentParameters['PARAMETERS']['CRM_ID']          = array(
			 'NAME'              => Loc::getMessage('CRM_ID'),
			 'TYPE'              => 'LIST',
			 'VALUES'            => $arCrmId,
			 'ADDITIONAL_VALUES' => 'Y',
			 'COLS'              => 42,
			 'REFRESH'           => 'Y',
			 'PARENT'            => 'GROUP_CRM',
		);
		$arComponentParameters['PARAMETERS']['CRM_LEAD_TITLE']  = array(
			 'NAME'    => Loc::getMessage('CRM_LEAD_TITLE'),
			 'TYPE'    => 'STRING',
			 'DEFAULT' => '', //#LAST_NAME# #NAME# #SECOND_NAME#
			 'COLS'    => 42,
			 'PARENT'  => 'GROUP_CRM',
		);
		$arComponentParameters['PARAMETERS']['CRM_SHOW_ERRORS'] = array(
			 'NAME'    => Loc::getMessage('CRM_SHOW_ERRORS'),
			 'TYPE'    => 'CHECKBOX',
			 'DEFAULT' => 'N',
			 'PARENT'  => 'GROUP_CRM',
		);
	}
	//\\CRM_SETTINGS
}


if($arCurrentValues['YAMETRIKA_ON'] == 'Y') {
	$arComponentParameters['PARAMETERS']['YAMETRIKA_COUNTER_ID']  = array(
		 'NAME'    => Loc::getMessage('YAMETRIKA_COUNTER_ID'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'COLS'    => 8,
		 'PARENT'  => 'YAMETRIKA_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['YAMETRIKA_TARGET_NAME'] = array(
		 'NAME'    => Loc::getMessage('YAMETRIKA_TARGET_NAME'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'COLS'    => 42,
		 'PARENT'  => 'YAMETRIKA_SETTINGS',
	);
}

if($arCurrentValues['YM2_ON'] == 'Y') {
	$arComponentParameters['PARAMETERS']['YM2_COUNTER']  = array(
		 'NAME'    => Loc::getMessage('YM2_COUNTER'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'COLS'    => 8,
		 'PARENT'  => 'YM2_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['YM2_GOAL_SUBMIT_FORM_SUCCESS'] = array(
		 'NAME'    => Loc::getMessage('YM2_GOAL_SUBMIT_FORM_SUCCESS'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => 'FORM1_SUBMIT_SUCCESS',
		 'COLS'    => 42,
		 'PARENT'  => 'YM2_SETTINGS',
	);
}


if($arCurrentValues['GA_ON'] == 'Y') {
	$arComponentParameters['PARAMETERS']['GA_GTAG']    = array(
		 'PARENT'  => 'GA_SETTINGS',
		 'NAME'    => Loc::getMessage('GA_GTAG'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'COLS'    => 42,
		 'ROWS'    => 4,
	);
}

if($arCurrentValues['USE_POWERTIP'] == 'Y') {
	$arComponentParameters['PARAMETERS']['POWERTIP_COLOR']              = array(
		 'NAME'              => Loc::getMessage('POWERTIP_COLOR'),
		 'TYPE'              => 'LIST',
		 'VALUES'            => Loc::getMessage('POWERTIP_COLOR_VALUES'),
		 'ADDITIONAL_VALUES' => 'N',
		 'DEFAULT'           => 'black',
		 'MULTIPLE'          => 'N',
		 'COLS'              => 50,
		 'PARENT'            => 'POWERTIP_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['POWERTIP_PLACEMENT']          = array(
		 'NAME'              => Loc::getMessage('POWERTIP_PLACEMENT'),
		 'TYPE'              => 'LIST',
		 'VALUES'            => array(
				'n'      => 'n',
				'e'      => 'e',
				's'      => 's',
				'w'      => 'w',
				'nw'     => 'nw',
				'ne'     => 'ne',
				'sw'     => 'sw',
				'se'     => 'se',
				'nw-alt' => 'nw-alt',
				'ne-alt' => 'ne-alt',
				'sw-alt' => 'sw-alt',
				'se-alt' => 'se-alt',
		 ),
		 'ADDITIONAL_VALUES' => 'N',
		 'DEFAULT'           => 'e',
		 'MULTIPLE'          => 'N',
		 'COLS'              => 50,
		 'PARENT'            => 'POWERTIP_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['POWERTIP_FOLLOWMOUSE']        = array(
		 'NAME'    => Loc::getMessage('POWERTIP_FOLLOWMOUSE'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'PARENT'  => 'POWERTIP_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['POWERTIP_POPUPID']            = array(
		 'NAME'    => Loc::getMessage('POWERTIP_POPUPID'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => 'powerTip',
		 'COLS'    => 42,
		 'PARENT'  => 'POWERTIP_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['POWERTIP_OFFSET']             = array(
		 'NAME'    => Loc::getMessage('POWERTIP_OFFSET'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => 10,
		 'COLS'    => 4,
		 'PARENT'  => 'POWERTIP_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['POWERTIP_FADEINTIME']         = array(
		 'NAME'    => Loc::getMessage('POWERTIP_FADEINTIME'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => 200,
		 'COLS'    => 4,
		 'PARENT'  => 'POWERTIP_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['POWERTIP_FADEOUTTIME']        = array(
		 'NAME'    => Loc::getMessage('POWERTIP_FADEOUTTIME'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => 100,
		 'COLS'    => 4,
		 'PARENT'  => 'POWERTIP_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['POWERTIP_CLOSEDELAY']         = array(
		 'NAME'    => Loc::getMessage('POWERTIP_CLOSEDELAY'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => 100,
		 'COLS'    => 4,
		 'PARENT'  => 'POWERTIP_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['POWERTIP_INTENTPOLLINTERVAL'] = array(
		 'NAME'    => Loc::getMessage('POWERTIP_INTENTPOLLINTERVAL'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => 100,
		 'COLS'    => 4,
		 'PARENT'  => 'POWERTIP_SETTINGS',
	);
}


if($arFullPropertyNS) {
	$sDefaultMaskValue = '';
	$i                 = 0;
	foreach($arFullPropertyNS as $sPropCode => $arProp) {
		if(is_array($arCurrentValues['HIDE_FIELDS']) && $arCurrentValues['HIDE_FIELDS'])
			if(in_array($sPropCode, $arCurrentValues['HIDE_FIELDS']))
				continue;

		/*switch($sPropCode)
		{
			case 'EMAIL':
				$sDefaultMaskValue = "'alias': 'email'";
				break;
			case 'PHONE':
				$sDefaultMaskValue = "'alias': 'phone'";
				break;
		}*/

		$sCurFieldGroup = 'FIELD_' . $sPropCode . '_SETTINGS';

		$arComponentParameters['GROUPS'][ $sCurFieldGroup ] = array(
			 'NAME' => Loc::getMessage('FIELD_X_SETTINGS') . $arProp['NAME'],
			 'SORT' => 1000 + $i,
		);

		if($arCurrentValues['USE_POWERTIP'] == 'Y') {
			$arComponentParameters['PARAMETERS'][ 'POWERTIP_FIELD_' . $sPropCode ] = array(
				 'NAME'    => Loc::getMessage('POWERTIP_FIELD'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 50,
				 'PARENT'  => $sCurFieldGroup,
			);
		}

		if($arCurrentValues['VALIDATE_ON'] == 'Y') {
			$arComponentParameters['PARAMETERS'][ 'VALIDATE_RULE_' . $sPropCode ] = array(
				 'NAME'    => Loc::getMessage('VALIDATE_RULE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 50,
				 'PARENT'  => $sCurFieldGroup,
			);
			$arComponentParameters['PARAMETERS'][ 'VALIDATE_MESS_' . $sPropCode ] = array(
				 'NAME'    => Loc::getMessage('VALIDATE_MESS'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 50,
				 'PARENT'  => $sCurFieldGroup,
			);
		}

		if($arCurrentValues['INPUTMASK_ON'] == 'Y') {
			$arComponentParameters['PARAMETERS'][ 'INPUTMASK_FIELD_' . $sPropCode ] = array(
				 'NAME'    => Loc::getMessage('INPUTMASK_FIELD'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => $sDefaultMaskValue,
				 'COLS'    => 50,
				 'PARENT'  => $sCurFieldGroup,
			);
		}

		if($arCurrentValues['CRM_ID'] && $arCrmFieldsId) {
			$arComponentParameters['PARAMETERS'][ 'CRM_FIELD_' . $sPropCode ] = array(
				 'NAME'    => Loc::getMessage('CRM_FIELD'),
				 'TYPE'    => 'LIST',
				 'DEFAULT' => '',
				 'VALUES'  => $arCrmFieldsId,
				 'COLS'    => 50,
				 'PARENT'  => $sCurFieldGroup,
			);
		}

		$i++;
	}
}

if($arCurrentValues['INPUTMASK_ON'] == 'Y') {
	$arComponentParameters['PARAMETERS']['INPUTMASK_JS'] = array(
		 'PARENT'  => 'INPUTMASK_SETTINGS',
		 'TYPE'    => 'CHECKBOX',
		 'NAME'    => Loc::getMessage('INPUTMASK_JS'),
		 'DEFAULT' => 'Y',
	);
}

if($arCurrentValues['JQUERY_ON'] == 'Y') {
	$arComponentParameters['PARAMETERS']['JQUERY_VERSION'] = array(
		 'PARENT'  => 'JQUERY_SETTINGS',
		 'TYPE'    => 'LIST',
		 'NAME'    => Loc::getMessage('JQUERY_VERSION'),
		 'DEFAULT' => 'jquery',
		 'VALUES'  => GetMessage('JQUERY_VERSION_VALUES'),
	);
}


if($arCurrentValues['USER_CONSENT'] == 'Y') {
	$arComponentParameters['PARAMETERS']['USER_CONSENT_ID']         = array(
		 'PARENT'   => 'USER_CONSENT',
		 'NAME'     => Loc::getMessage('USER_CONSENT_ID'),
		 'TYPE'     => 'LIST',
		 'VALUES'   => array(Loc::getMessage('USER_CONSENT_ID_DEF')) + \Bitrix\Main\UserConsent\Agreement::getActiveList(),
		 'MULTIPLE' => 'Y',
	);
	$arComponentParameters['PARAMETERS']['USER_CONSENT_IS_CHECKED'] = array(
		 'PARENT'  => 'USER_CONSENT',
		 'NAME'    => Loc::getMessage('USER_CONSENT_IS_CHECKED'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'Y',
	);
	$arComponentParameters['PARAMETERS']['USER_CONSENT_REPLACE']    = array(
		 'PARENT'  => 'USER_CONSENT',
		 'NAME'    => Loc::getMessage('USER_CONSENT_REPLACE'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
	);
}

?>
<style type="text/css">
	.bxcompprop-content-table textarea{
		-webkit-box-sizing: border-box !important; -moz-box-sizing: border-box !important; box-sizing: border-box !important;
		width: 90% !important;
		min-height: 60px !important;
	}
</style>