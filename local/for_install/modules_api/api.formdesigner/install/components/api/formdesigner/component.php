<?
/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 */

use Bitrix\Main\Loader,
	 Bitrix\Main\Application,
	 Bitrix\Main\SiteTable,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Text\Encoding,
	 Bitrix\Main\Config\Option,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.formdesigner')) {
	ShowError(Loc::getMessage('AFDC_MODULE_ERROR'));
	return;
}

if(!Loader::includeModule('iblock')) {
	ShowError(Loc::getMessage('AFDC_IBLOCK_ERROR'));
	return;
}

$bUseCore = Loader::includeModule('api.core');
$bUseHL   = Loader::includeModule('highloadblock');

use Bitrix\Highloadblock as HL;
use Api\FormDesigner\Crm;

$cache        = Application::getInstance()->getCache();
$taggetCache  = Application::getInstance()->getTaggedCache();
$managedCache = Application::getInstance()->getManagedCache();


$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();


//Inc template lang
if($this->initComponentTemplate()) {
	$templateFolder = &$this->getTemplate()->GetFolder();
	$templateFile   = $server->getDocumentRoot() . $templateFolder . '/template.php';
	Loc::loadMessages($templateFile);
}


$oFD          = new CApiFormDesigner();
$isUtfMode    = Application::isUtfMode();
$documentRoot = Application::getDocumentRoot();

//Идентификатор формы
$FORM_ID = $arParams['UNIQUE_FORM_ID'] = ($arParams['UNIQUE_FORM_ID'] ? trim($arParams['UNIQUE_FORM_ID']) : $this->GetEditAreaId($this->randString()));

$arParams['HTTP_PROTOCOL'] = $request->isHttps() ? 'https://' : 'http://';
$arParams['HTTP_HOST']     = $arParams['HTTP_PROTOCOL'] . $request->getHttpHost();


//PAGE_VARS
$oUri = new \Bitrix\Main\Web\Uri($request->getRequestUri());

$pageUrl = $request->getRequestUri();
if(method_exists($oUri, 'deleteParams')) {
	$oUri->deleteParams(\Bitrix\Main\HttpRequest::getSystemParameters());
	$pageUrl = $oUri->getUri();
}


/** Динамические параметры нужно удалять, если в компоненте используется кэширование */
$sysFormTitle = $arParams['FORM_TITLE'] ? $arParams['~FORM_TITLE'] : '';
$sysPageTitle = $arParams['PAGE_TITLE'] ? $arParams['PAGE_TITLE'] : $APPLICATION->GetTitle();
$sysPageUrl   = $arParams['PAGE_URL'] ? $arParams['PAGE_URL'] : $arParams['HTTP_HOST'] . $pageUrl;
$sysDirUrl    = $arParams['DIR_URL'] ? $arParams['DIR_URL'] : $arParams['HTTP_HOST'] . $request->getRequestedPageDirectory();
$sysDateTime  = $arParams['DATE_TIME'] ? $arParams['DATE_TIME'] : date('d-m-Y H:i:s');
$sysDate      = $arParams['DATE'] ? $arParams['DATE'] : date('d-m-Y');
$sysIp        = $arParams['IP'] ? $arParams['IP'] : $request->getRemoteAddress();
unset(
	 $arParams['FORM_TITLE'],
	 $arParams['~FORM_TITLE'],
	 $arParams['PAGE_TITLE'],
	 $arParams['~PAGE_TITLE'],
	 $arParams['PAGE_URL'],
	 $arParams['~PAGE_URL'],
	 $arParams['DIR_URL'],
	 $arParams['~DIR_URL'],
	 $arParams['DATE_TIME'],
	 $arParams['~DATE_TIME'],
	 $arParams['DATE'],
	 $arParams['~DATE'],
	 $arParams['IP'],
	 $arParams['~IP']
);


$arParams['PAGE_VARS']   = array_diff((array)$arParams['PAGE_VARS'], array(''));
$arParams['SERVER_VARS'] = array_diff((array)$arParams['SERVER_VARS'], array(''));
$arParams['UTM_VARS']    = array_diff((array)$arParams['UTM_VARS'], array(''));

//CACHE_SETTIGS
$arParams['CACHE_TIME'] = ($arParams['CACHE_TIME'] ? $arParams['CACHE_TIME'] : 86400 * 365);

//BASE_SETTIGS
$arParams['IBLOCK_TYPE']    = trim($arParams['IBLOCK_TYPE']);
$arParams['IBLOCK_ID']      = intval($arParams['IBLOCK_ID']);
$arParams['REDIRECT_URL']   = trim($arParams['REDIRECT_URL']);
$arParams['COMPATIBLE_ON']  = ($arParams['COMPATIBLE_ON'] == 'Y');
$arParams['ENABLED_FIELDS'] = array_diff((array)$arParams['ENABLED_FIELDS'], array(''));

//JQUERY_SETTINGS
$arParams['JQUERY_ON']      = ($arParams['JQUERY_ON'] == 'Y');
$arParams['JQUERY_VERSION'] = ($arParams['JQUERY_VERSION'] ? trim($arParams['JQUERY_VERSION']) : 'jquery');
if($arParams['JQUERY_ON'])
	CUtil::InitJSCore($arParams['JQUERY_VERSION']);

//CJSCore::Init(array('core','session','ls'));//array('ajax', 'json', 'ls', 'session', 'jquery', 'popup', 'pull')

//IBLOCK_SETTINGS
$arParams['IBLOCK_ON']             = $arParams['IBLOCK_ON'] == 'Y';
$arParams['IBLOCK_TICKET_CODE']    = trim($arParams['IBLOCK_TICKET_CODE']);
$arParams['IBLOCK_ELEMENT_ACTIVE'] = $arParams['IBLOCK_ELEMENT_ACTIVE'] == 'Y' ? 'Y' : 'N';
$arParams['IBLOCK_ELEMENT_NAME']   = trim($arParams['~IBLOCK_ELEMENT_NAME']);
$arParams['IBLOCK_ELEMENT_CODE']   = trim($arParams['~IBLOCK_ELEMENT_CODE']);
$arResult['TICKET_ID']             = trim($arParams['TICKET_ID']);
$arResult['ELEMENT_ID']            = trim($arParams['ELEMENT_ID']);

//VISUAL
$arParams['INCLUDE_STYLES'] = $arParams['INCLUDE_STYLES'] == 'Y';
//$arParams['FORM_AUTOCOMPLETE'] = $arParams['FORM_AUTOCOMPLETE'] == 'Y' ? 'on' : 'off';
$arParams['FORM_AUTOCOMPLETE'] = 'off';
$arParams['FORM_HORIZONTAL']   = $arParams['FORM_HORIZONTAL'] == 'Y';

if(is_array($arParams['SHOW_ERRORS']) && $arParams['SHOW_ERRORS']) {
	$arParams['SHOW_ERRORS'] = in_array('BOTTOM', $arParams['SHOW_ERRORS']) ? 'Y' : 'N';
}
$arParams['SHOW_ERRORS'] = $arParams['SHOW_ERRORS'] == 'Y';


//DEFAULT MESS
$arParams['MESS_SUCCESS']      = isset($arParams['MESS_SUCCESS']) ? $arParams['~MESS_SUCCESS'] : Loc::getMessage('AFDC_DEFAULT_MESS_SUCCESS');
$arParams['MESS_SUCCESS_DESC'] = isset($arParams['MESS_SUCCESS_DESC']) ? $arParams['~MESS_SUCCESS_DESC'] : Loc::getMessage('AFDC_DEFAULT_MESS_SUCCESS_DESC');
$arParams['MESS_SUCCESS_BTN']  = isset($arParams['MESS_SUCCESS_BTN']) ? $arParams['~MESS_SUCCESS_BTN'] : Loc::getMessage('AFDC_DEFAULT_MESS_SUCCESS_BTN');

$arParams['MESS_REQUIRED_FIELD'] = isset($arParams['MESS_REQUIRED_FIELD']) ? $arParams['~MESS_REQUIRED_FIELD'] : Loc::getMessage('AFDC_DEFAULT_MESS_REQUIRED_FIELD');
$arParams['MESS_CHECK_EMAIL']    = isset($arParams['MESS_CHECK_EMAIL']) ? $arParams['~MESS_CHECK_EMAIL'] : Loc::getMessage('AFDC_DEFAULT_MESS_CHECK_EMAIL');

$arParams['SUBMIT_BUTTON_TEXT']  = isset($arParams['SUBMIT_BUTTON_TEXT']) ? $arParams['~SUBMIT_BUTTON_TEXT'] : Loc::getMessage('AFDC_DEFAULT_SUBMIT_BUTTON_TEXT');
$arParams['SUBMIT_BUTTON_AJAX']  = isset($arParams['SUBMIT_BUTTON_AJAX']) ? $arParams['~SUBMIT_BUTTON_AJAX'] : Loc::getMessage('AFDC_DEFAULT_SUBMIT_BUTTON_AJAX');
$arParams['SUBMIT_BUTTON_CLASS'] = trim($arParams['SUBMIT_BUTTON_CLASS']);
$arParams['MESS_CHOOSE']         = isset($arParams['MESS_CHOOSE']) ? trim($arParams['~MESS_CHOOSE']) : Loc::getMessage('AFDC_DEFAULT_MESS_CHOOSE');

$arParams['HIDE_FIELDS'] = (array)$arParams['HIDE_FIELDS'];
foreach($arParams['HIDE_FIELDS'] as $k => $v)
	if($v == '')
		unset($arParams['HIDE_FIELDS'][ $k ]);

$arParams['DIVIDER_FIELDS'] = (array)$arParams['DIVIDER_FIELDS'];
foreach($arParams['DIVIDER_FIELDS'] as $k => $v)
	if($v == '')
		unset($arParams['DIVIDER_FIELDS'][ $k ]);


$arParams['GROUPS_ID'] = is_array($arParams['GROUPS_ID']) ? $arParams['GROUPS_ID'] : array();
foreach($arParams['GROUPS_ID'] as $k => $v)
	if($v == '')
		unset($arParams['GROUPS_ID'][ $k ]);


//POST_SETTINGS
$arParams['POST_ON']           = $arParams['POST_ON'] == 'Y';
$arParams['USER_EMAIL']        = '';
$arParams['POST_REPLACE_FROM'] = $arParams['POST_REPLACE_FROM'] == 'Y';
$arParams['POST_EMAIL_CODE']   = trim($arParams['POST_EMAIL_CODE']);
$arParams['EMAIL_FROM']        = trim($arParams['POST_EMAIL_FROM']);
$arParams['EMAIL_TO']          = trim($arParams['POST_EMAIL_TO']);

$arParams['POST_ADMIN_SUBJECT'] = isset($arParams['POST_ADMIN_SUBJECT']) ? trim($arParams['POST_ADMIN_SUBJECT']) : Loc::getMessage('AFDC_DEFAULT_POST_ADMIN_SUBJECT');
$arParams['POST_USER_SUBJECT']  = isset($arParams['POST_USER_SUBJECT']) ? trim($arParams['POST_USER_SUBJECT']) : Loc::getMessage('AFDC_DEFAULT_POST_USER_SUBJECT');

$arParams['POST_ADMIN_MESSAGE_ID'] = (array)$arParams['POST_ADMIN_MESSAGE_ID'];
if($arParams['POST_ADMIN_MESSAGE_ID']) {
	foreach($arParams['POST_ADMIN_MESSAGE_ID'] as $k => $v)
		if(empty($v))
			unset($arParams['POST_ADMIN_MESSAGE_ID'][ $k ]);
}


$arParams['POST_USER_MESSAGE_ID'] = (array)$arParams['POST_USER_MESSAGE_ID'];
if($arParams['POST_USER_MESSAGE_ID']) {
	foreach($arParams['POST_USER_MESSAGE_ID'] as $k => $v)
		if(empty($v))
			unset($arParams['POST_USER_MESSAGE_ID'][ $k ]);
}


//FILES_SETTINGS
$arParams['UPLOAD_FILE_SIZE']    = trim($arParams['UPLOAD_FILE_SIZE']);
$arParams['UPLOAD_MAX_FILESIZE'] = $arParams['UPLOAD_FILE_SIZE'] ? CApiFormDesigner::getFileSizeInBytes($arParams['UPLOAD_FILE_SIZE']) : 10000 * 1024; //10M
$arParams['UPLOAD_FOLDER']       = $arParams['UPLOAD_FOLDER'] ? rtrim($arParams['UPLOAD_FOLDER'], '/') : '/upload/api_formdesigner';
$arParams['UPLOAD_TMP_DIR']      = $documentRoot . $arParams['UPLOAD_FOLDER'];
$arParams['UPLOAD_FILE_LIMIT']   = ($arParams['UPLOAD_FILE_LIMIT'] ? intval($arParams['UPLOAD_FILE_LIMIT']) : 5);


//MODAL_SETTINGS
$arParams['USE_MODAL']            = ($arParams['USE_MODAL'] == 'Y' && $bUseCore);
$arParams['MODAL_ID']             = $arParams['MODAL_ID'] ? $arParams['MODAL_ID'] : $this->GetEditAreaId($this->randString());
$arParams['MODAL_BTN_TEXT']       = trim($arParams['~MODAL_BTN_TEXT']);
$arParams['MODAL_BTN_CLASS']      = trim($arParams['~MODAL_BTN_CLASS']);
$arParams['MODAL_BTN_ID']         = trim($arParams['~MODAL_BTN_ID']);
$arParams['MODAL_BTN_SPAN_CLASS'] = trim($arParams['~MODAL_BTN_SPAN_CLASS']);
$arParams['MODAL_HEADER_TEXT']    = trim($arParams['~MODAL_HEADER_TEXT']);
$arParams['MODAL_FOOTER_TEXT']    = trim($arParams['~MODAL_FOOTER_TEXT']);



//CRM_LEAD_SETTINGS
$arParams['CRM_ON']          = $arParams['CRM_ON'] == 'Y';
$arParams['CRM_ID']          = intval($arParams['CRM_ID']);
$arParams['CRM_LEAD_TITLE']  = $arParams['~CRM_LEAD_TITLE'] ? trim($arParams['~CRM_LEAD_TITLE']) : $arParams['HTTP_HOST'];
$arParams['CRM_SHOW_ERRORS'] = $arParams['CRM_SHOW_ERRORS'] == 'Y';


//YAMETRIKA_SETTINGS
$arParams['SEND_GOALS']            = false;
$arParams['YAMETRIKA_ON']          = $arParams['YAMETRIKA_ON'] == 'Y';
$arParams['YAMETRIKA_COUNTER_ID']  = trim($arParams['YAMETRIKA_COUNTER_ID']);
$arParams['YAMETRIKA_TARGET_NAME'] = trim($arParams['YAMETRIKA_TARGET_NAME']);

$arParams['YM2_ON']                       = $arParams['YM2_ON'] == 'Y';
$arParams['YM2_COUNTER']                  = trim($arParams['YM2_COUNTER']);
$arParams['YM2_GOAL_SUBMIT_FORM_SUCCESS'] = trim($arParams['YM2_GOAL_SUBMIT_FORM_SUCCESS']);


//GA_SETTINGS
$arParams['GA_ON']   = $arParams['GA_ON'] == 'Y';
$arParams['GA_GTAG'] = trim($arParams['~GA_GTAG']);
/*$arParams['GA_CATEGORY'] = trim($arParams['GA_CATEGORY']);
$arParams['GA_ACTION']   = trim($arParams['GA_ACTION']);
$arParams['GA_LABEL']    = trim($arParams['GA_LABEL']);
$arParams['GA_VALUE']    = trim($arParams['GA_VALUE']);
*/

//EULA
$arParams['USE_EULA']          = $arParams['~USE_EULA'] == 'Y';
$arParams['MESS_EULA']         = trim($arParams['~MESS_EULA']);
$arParams['MESS_EULA_CONFIRM'] = trim($arParams['~MESS_EULA_CONFIRM']);

//PRIVACY
$arParams['USE_PRIVACY']          = $arParams['~USE_PRIVACY'] == 'Y';
$arParams['MESS_PRIVACY']         = trim($arParams['~MESS_PRIVACY']);
$arParams['MESS_PRIVACY_LINK']    = trim($arParams['~MESS_PRIVACY_LINK']);
$arParams['MESS_PRIVACY_CONFIRM'] = trim($arParams['~MESS_PRIVACY_CONFIRM']);

//USER_CONSENT
$arParams['USER_CONSENT_ID'] = (array)$arParams['USER_CONSENT_ID'];
if($arParams['USER_CONSENT_ID']) {
	foreach($arParams['USER_CONSENT_ID'] as $k => $v)
		if($v == '')
			unset($arParams['USER_CONSENT_ID'][ $k ]);
}
$arParams['USER_CONSENT'] = ($arParams['USER_CONSENT_ID'] && $arParams['USER_CONSENT'] == 'Y');


//POWERTIP_SETTINGS - $arResult['POWERTIP_SETTINGS']
$arParams['USE_POWERTIP']                = $arParams['USE_POWERTIP'] == 'Y';
$arParams['POWERTIP_PLACEMENT']          = trim($arParams['POWERTIP_PLACEMENT']);
$arParams['POWERTIP_COLOR']              = trim($arParams['POWERTIP_COLOR']) ? trim($arParams['POWERTIP_COLOR']) : 'black';
$arParams['POWERTIP_FOLLOWMOUSE']        = $arParams['POWERTIP_FOLLOWMOUSE'] == 'Y' ? 'true' : 'false';
$arParams['POWERTIP_POPUPID']            = $arParams['POWERTIP_POPUPID'] ? $arParams['POWERTIP_POPUPID'] : 'powerTip';
$arParams['POWERTIP_OFFSET']             = (int)$arParams['POWERTIP_OFFSET'] ? (int)$arParams['POWERTIP_OFFSET'] : 10;
$arParams['POWERTIP_FADEINTIME']         = (int)$arParams['POWERTIP_FADEINTIME'] ? (int)$arParams['POWERTIP_FADEINTIME'] : 200;
$arParams['POWERTIP_FADEOUTTIME']        = (int)$arParams['POWERTIP_FADEOUTTIME'] ? (int)$arParams['POWERTIP_FADEOUTTIME'] : 100;
$arParams['POWERTIP_CLOSEDELAY']         = (int)$arParams['POWERTIP_CLOSEDELAY'] ? (int)$arParams['POWERTIP_CLOSEDELAY'] : 100;
$arParams['POWERTIP_INTENTPOLLINTERVAL'] = (int)$arParams['POWERTIP_INTENTPOLLINTERVAL'] ? (int)$arParams['POWERTIP_INTENTPOLLINTERVAL'] : 100;
if($arParams['USE_POWERTIP']) {
	//	$APPLICATION->SetAdditionalCSS('/bitrix/js/api.formdesigner/powertip/css/jquery.powertip-' . $arParams['POWERTIP_COLOR'] . '.min.css');
	//	$APPLICATION->AddHeadScript('/bitrix/js/api.formdesigner/powertip/jquery.powertip.min.js');
}


$arParams['WYSIWYG_ON']   = ($arParams['WYSIWYG_ON'] == 'Y');
$arParams['INPUTMASK_ON'] = ($arParams['INPUTMASK_ON'] == 'Y');
$arParams['INPUTMASK_JS'] = ($arParams['INPUTMASK_ON'] && $arParams['INPUTMASK_JS'] == 'Y');
$arParams['VALIDATE_ON']  = $arParams['VALIDATE_ON'] == 'Y';



/** Идентификатор кэша здесь, чтобы динамические данные его не изменяли */
$sCacheId   = $this->getCacheID(array($FORM_ID));
$sCachePath = $managedCache->getCompCachePath($this->__relativePath);


/** Далее вся динамика, чтобы параметры не попадали в айди кэша */
$arParams['FORM_TITLE']     = $sysFormTitle;
$arParams['USE_BX_CAPTCHA'] = (($arParams['USE_BX_CAPTCHA'] == 'Y' && !$USER->IsAuthorized()));

$arResult['DISPLAY_USER_CONSENT'] = array();
if($arParams['USER_CONSENT']) {
	foreach($arParams['USER_CONSENT_ID'] as $agreementId) {

		$agreement = new \Bitrix\Main\UserConsent\Agreement($agreementId);

		if($agreement->isExist() && $agreement->isActive()) {

			$arReplace = array(
				 "button_caption" => $arParams['SUBMIT_BUTTON_TEXT'],
				 "fields"         => $arParams['USER_CONSENT_REPLACE'],
			);

			$agreement->setReplace($arReplace);

			$agreementData = $agreement->getData();

			$config = array(
				 'id'       => $agreementId,
				 'sec'      => $agreementData['SECURITY_CODE'],
				 'autoSave' => 'N',
				 'replace'  => $arReplace,
			);

			/*if($arParams['IS_LOADED']) {
				$config['text'] = $agreement->getText();
			}
			if($arParams['SUBMIT_EVENT_NAME']) {
				$config['submitEventName'] = $this->arParams['SUBMIT_EVENT_NAME'];
			}
			if($arParams['ORIGIN_ID']) {
				$config['originId'] = $this->arParams['ORIGIN_ID'];
			}
			if($arParams['ORIGINATOR_ID']) {
				$config['originatorId'] = $this->arParams['ORIGINATOR_ID'];
			}*/

			$arResult['DISPLAY_USER_CONSENT'][ $agreementId ] = array(
				 'ID'         => $agreementId,
				 'LABEL_TEXT' => $agreement->getLabelText(),
				 'CONFIG'     => $config,
				 'USER_VALUE' => '',
				 'ERROR'      => '',
			);
		}
	}

	unset($agreementId, $agreement, $arReplace, $config);
}


$arResult['POWERTIP_SETTINGS'] = $arResult['INPUTMASK_SETTINGS'] = '';
$arResult['VALIDATE_SETTINGS'] = $arResult['FILES_SETTINGS'] = '';


//$arResult['MESSAGE']['DANGER']  = array();
//$arResult['MESSAGE']['SUCCESS'] = array();
//$arResult['MESSAGE']['WARNING'] = array();
//$arResult['MESSAGE']['HIDDEN']  = array();
$arResult['MESSAGE'] = array();


if($arParams['POST_ON']) {
	//Через запятую несколько мыл не пропускает
	//	if($arParams['EMAIL_FROM'] && !check_email($arParams['EMAIL_FROM']))
	//		$arResult['MESSAGE']['WARNING'][] = Loc::getMessage('EMAIL_FROM_NOT_VALID');

	//	if($arParams['EMAIL_TO'] && !check_email($arParams['EMAIL_TO']))
	//		$arResult['MESSAGE']['WARNING'][] = Loc::getMessage('EMAIL_TO_NOT_VALID');

	if(!$arParams['POST_EMAIL_CODE'] && $arParams['POST_REPLACE_FROM'])
		$arResult['MESSAGE']['WARNING'][] = Loc::getMessage('AFDC_WARNING_POST_EMAIL_CODE');

	if(!$arParams['POST_ADMIN_MESSAGE_ID'])
		$arResult['MESSAGE']['WARNING'][] = Loc::getMessage('AFDC_WARNING_POST_ADMIN_MESSAGE_ID');
}


if(!$FORM_ID)
	$arResult['MESSAGE']['WARNING'][] = Loc::getMessage('AFDC_WARNING_UNIQUE_FORM_ID');


if($arParams['IBLOCK_ON']) {
	if(!$arParams['IBLOCK_TICKET_CODE'])
		$arResult['MESSAGE']['WARNING'][] = Loc::getMessage('AFDC_WARNING_IBLOCK_TICKET_CODE');
}


if($arParams['CRM_ON']) {
	if(!$arParams['CRM_ID']) {
		$arParams['CRM_ON']               = false;
		$arResult['MESSAGE']['WARNING'][] = Loc::getMessage('AFDC_WARNING_CRM_LEAD');
	}
}


//======================================================================================================================
//	PAGE_VARS & UTM_VARS
//======================================================================================================================

//PAGE_VARS
if($arParams['PAGE_VARS']) {
	$pageVars = array();
	foreach($arParams['PAGE_VARS'] as $key) {
		if($key && $arParams[ $key ]) {
			$pageVars[ $key ] = $arParams[ $key ];
		}
	}
	$arParams['PAGE_VARS'] = $pageVars;
	unset($pageVars, $key);
}

//UTM_VARS
if($arParams['UTM_VARS']) {
	$utmVars = array();
	foreach($arParams['UTM_VARS'] as $key) {
		$utmVars[ $key ] = $request->get($key);
	}
	$arParams['UTM_VARS'] = $utmVars;
}



//======================================================================================================================
//	IBLOCK PROPERTY
//======================================================================================================================
$arSite                          = array();
$arResult['DISPLAY_PROPERTIES']  = array();
$arResult['REQUIRED_PROPERTIES'] = array();

if($arParams['IBLOCK_ID']) {
	if($cache->initCache($arParams["CACHE_TIME"], $sCacheId, $sCachePath)) {
		$vars = $cache->getVars();

		$arSite                          = $vars['SITE'];
		$arResult["DISPLAY_PROPERTIES"]  = $vars['DISPLAY_PROPERTIES'];
		$arResult["REQUIRED_PROPERTIES"] = $vars['REQUIRED_PROPERTIES'];
	}
	else {
		$cache->startDataCache($arParams["CACHE_TIME"], $sCacheId, $sCachePath);

		/*
		PROPERTY_TYPE - Базовые типы
			S - Строка
			N - Число
			L - Список
			F - Файл
			E - Привязка к элементам
			G - Привязка к разделам

		USER_TYPE - Пользовательские типы
			S:HTML - HTML/текст
			S:video - Видео
			S:Date - Дата
			S:DateTime - Дата/Время
			S:SASDPalette - Палитра
			S:map_yandex - Привязка к Яндекс.Карте
			S:map2gis - Привязка к карте 2GIS
			S:map_google - Привязка к карте Google Maps
			S:APIFD_PSList - Привязка к платежным системам
			S:UserID - Привязка к пользователю
			G:SectionAuto - Привязка к разделам с автозаполнением
			N:SASDSection - Привязка к собственной секции
			S:TopicID - Привязка к теме форума
			E:SKU - Привязка к товарам (SKU)
			S:FileMan - Привязка к файлу (на сервере)
			E:EList - Привязка к элементам в виде списка
			S:ElementXmlID - Привязка к элементам по XML_ID
			E:EAutocomplete - Привязка к элементам с автозаполнением
			E:APIFD_ESList - Привязка к элементам с группировкой по разделам
			S:SASDCheckbox - Простой чекбокс (строка)
			N:SASDCheckboxNum - Простой чекбокс (число)
			S:directory - Справочник
			N:Sequence - Счетчик
		*/


		$dbProp = CIBlockProperty::GetList(
			 $arOrder = Array(
					'SORT' => 'ASC',
					'NAME' => 'ASC',
			 ),
			 $arFilter = Array(
					'IBLOCK_ID' => $arParams['IBLOCK_ID'],
					'ACTIVE'    => 'Y',
			 )
		);

		while($arProp = $dbProp->GetNext(true, false)) {

			$arProp['CODE'] = trim($arProp['CODE']);

			if($arParams['IBLOCK_TICKET_CODE'] == $arProp['CODE'])
				continue;

			if($arParams['ENABLED_FIELDS'] && !in_array($arProp['CODE'], $arParams['ENABLED_FIELDS']))
				continue;


			//Default property value structure
			$arProp['DATA']       = array(); //data- attributes for form inputs
			$arProp['USER_VALUE'] = $arProp['DEFAULT_VALUE'];


			// select + checkbox + radio
			if($arProp['PROPERTY_TYPE'] == 'L') {
				$arrEnum = array();
				$rsEnum  = CIBlockProperty::GetPropertyEnum($arProp['ID'], Array('SORT' => 'ASC', 'VALUE' => 'ASC'));
				while($ar_enum = $rsEnum->Fetch()) {
					$arrEnum[ $ar_enum['ID'] ] = $ar_enum;
				}

				$arProp['USER_VALUE']    = array();
				$arProp['DISPLAY_VALUE'] = $arrEnum;
			}
			// Link to section
			elseif($arProp['PROPERTY_TYPE'] == 'G') {
				$arrEnum    = array();
				$rsSections = CIBlockSection::GetList(
					 array('left_margin' => 'asc'),
					 array('ACTIVE' => 'Y', 'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'] ? $arProp['LINK_IBLOCK_ID'] : $arParams['IBLOCK_ID']),
					 false,
					 array('ID', 'NAME', 'DEPTH_LEVEL')
				);

				while($arSection = $rsSections->Fetch()) {
					/*if($arParams['REMOVE_POINTS'])
						$arSection["NAME"] = $arSection["NAME"];
					else*/
					$arSection["NAME"] = str_repeat(" . ", $arSection["DEPTH_LEVEL"]) . $arSection["NAME"];

					$arSection["VALUE"]          = $arSection["NAME"];
					$arrEnum[ $arSection['ID'] ] = $arSection;
				}
				unset($arSection);

				$arProp['USER_VALUE']    = array();
				$arProp['DISPLAY_VALUE'] = $arrEnum;
			}
			// Link to Elements
			elseif($arProp['PROPERTY_TYPE'] == 'E') {
				$arrEnum = array();
				if(!empty($arProp['LINK_IBLOCK_ID'])) {
					if($arProp['USER_TYPE'] == 'APIFD_ESList') {
						$arSettings = $arProp['USER_TYPE_SETTINGS'];
						$arrEnum    = \Api\FormDesigner\Property\ESList::getElements(
							 $arProp['LINK_IBLOCK_ID'],
							 $arSettings['SHOW_PICTURE'],
							 $arSettings
						);
					}
					else {
						$rsElement = CIBlockElement::GetList(
							 array('SORT' => 'ASC', 'NAME' => 'ASC'),
							 array('ACTIVE' => 'Y', 'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID']),
							 false,
							 false,
							 array('ID', 'NAME')
						);
						while($arElement = $rsElement->Fetch()) {
							$arElement["VALUE"]          = $arElement["NAME"];
							$arrEnum[ $arElement['ID'] ] = $arElement;
						}
						unset($arElement);
					}
				}

				$arProp['USER_VALUE']    = array();
				$arProp['DISPLAY_VALUE'] = $arrEnum;
			}
			// Sequence
			elseif($arProp['USER_TYPE'] == 'Sequence') {
				$arProp['USER_VALUE'] = 0;
			}
			elseif($arProp['USER_TYPE'] == 'HTML') {
				$arProp['USER_VALUE'] = $arProp['DEFAULT_VALUE']['TEXT'];
			}
			// UserID
			elseif($arProp['USER_TYPE'] == 'UserID') {
				$arrEnum      = array();
				$arUserFilter = array(
					 'ACTIVE' => 'Y',
				);

				$arUserSelect = array(
					 'SELECT' => array('UF_*'),
					 'FIELDS' => array('ID', 'TITLE', 'LAST_NAME', 'NAME', 'SECOND_NAME'),
				);

				if($arParams['GROUPS_ID'])
					$arUserFilter['GROUPS_ID'] = $arParams['GROUPS_ID'];

				$rsUsers = CUser::GetList($by = array('sort' => 'asc', 'name' => 'asc'), $order = 'sort', $arUserFilter, $arUserSelect);
				while($arUser = $rsUsers->Fetch()) {
					$arUser['VALUE'] = trim($arUser['TITLE']) ? trim($arUser['TITLE']) : trim($arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME']);

					if($arProp['DEFAULT_VALUE'] == $arUser['ID'])
						$arUser['DEF'] = 'Y';

					$arrEnum[ $arUser['ID'] ] = $arUser;
				}

				$arProp['USER_VALUE']    = array();
				$arProp['DISPLAY_VALUE'] = $arrEnum;
			}
			//APIFD_PSList
			elseif($arProp['USER_TYPE'] == 'APIFD_PSList') {
				$parameters = array();
				if($arPS = $arProp['USER_TYPE_SETTINGS']['PAYSYSTEM'])
					$parameters['filter'] = array('=ID' => $arPS);

				$arrEnum = \Api\FormDesigner\Property\PSList::getPaySystems($parameters);

				$arProp['USER_VALUE']    = array();
				$arProp['DISPLAY_VALUE'] = $arrEnum;
			}
			//S:directory - Справочник (кастомный)
			elseif($arProp["USER_TYPE"] == 'directory' && $bUseHL) {

				$arProp['DISPLAY_TYPE'] = 'H'; //TODO: Флажки с названиями и картинками
				//$arUserType = CIBlockProperty::GetUserType($arProp["USER_TYPE"]);

				$tableName = $arProp['USER_TYPE_SETTINGS']['TABLE_NAME'];

				$hlblock = HL\HighloadBlockTable::getList(array(
					 'select' => array('TABLE_NAME', 'NAME', 'ID'),
					 'filter' => array('=TABLE_NAME' => $tableName),
				))->fetch();

				$entity = HL\HighloadBlockTable::compileEntity($hlblock);

				//$directoryMap  = $entity->getFields();
				$directoryMap  = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('HLBLOCK_' . $hlblock['ID'], 0, LANGUAGE_ID);
				$directoryData = $entity->getDataClass();

				$listParameters = array(
					 'order'  => array(),
					 'select' => array('*'),
				);

				$sortExist = isset($directoryMap['UF_SORT']);
				if($sortExist) {
					$listParameters['order']['UF_SORT'] = 'ASC';
				}

				$nameExist = isset($directoryMap['UF_NAME']);
				if($nameExist) {
					$listParameters['order']['UF_NAME'] = 'ASC';
				}

				$fileKeys = array();
				if($directoryMap) {
					foreach($directoryMap as $key => $map) {
						if($map['USER_TYPE_ID'] == 'file')
							$fileKeys[] = $key;
					}
				}

				$arrEnum = array();
				$rsData  = $directoryData::getList($listParameters);
				while($arData = $rsData->fetch()) {

					if($fileKeys) {
						foreach($fileKeys as $k) {
							if($fileId = $arData[ $k ]) {
								$arFileTmp = CFile::ResizeImageGet($fileId, array("width" => 24, "height" => 24));

								if($arFileTmp['src'])
									$arFileTmp['src'] = CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

								$arData[ $k ] = array_change_key_case($arFileTmp, CASE_UPPER);
							}
						}

						unset($fileId, $arFileTmp, $k);
					}

					$arrEnum[ $arData['ID'] ] = $arData;
				}

				$arProp['USER_VALUE']     = array();
				$arProp['DISPLAY_FIELDS'] = $directoryMap;
				$arProp['DISPLAY_VALUE']  = $arrEnum;

				unset($tableName, $hlblock, $entity, $directoryMap, $directoryData, $listParameters, $sortExist, $nameExist, $rsData, $arData, $arrEnum, $fileKeys);
			}
			elseif($arProp["USER_TYPE"]) {
				$arUserType = CIBlockProperty::GetUserType($arProp["USER_TYPE"]);
				if(array_key_exists("GetPublicEditHTML", $arUserType))
					$arProp["GetPublicEditHTML"] = $arUserType["GetPublicEditHTML"];
				else
					$arProp["GetPublicEditHTML"] = false;
			}
			else {
				$arProp["GetPublicEditHTML"] = false;
			}


			//For <input type="hidden"> in template
			if(in_array($arProp['CODE'], $arParams['HIDE_FIELDS']))
				$arProp['PROPERTY_TYPE'] = 'HIDDEN';

			//For <div class="divider"> in template
			if(in_array($arProp['CODE'], $arParams['DIVIDER_FIELDS'])) {
				$arProp['USER_VALUE']    = trim($arProp['DEFAULT_VALUE']) ? trim($arProp['DEFAULT_VALUE']) : $arProp['NAME'];
				$arProp['PROPERTY_TYPE'] = 'DIVIDER';
			}


			//Add field mask
			if($arParams['INPUTMASK_ON']) {
				if($arParams[ 'INPUTMASK_FIELD_' . $arProp['CODE'] ])
					$arProp['DATA'][] = 'data-inputmask="' . ($arParams[ 'INPUTMASK_FIELD_' . $arProp['CODE'] ]) . '"';
			}


			//Add powertip
			if($arParams['USE_POWERTIP']) {
				if($arParams[ 'POWERTIP_FIELD_' . $arProp['CODE'] ])
					$arProp['DATA'][] = 'data-powertip="' . ($arParams[ 'POWERTIP_FIELD_' . $arProp['CODE'] ]) . '"';
				elseif($arProp['HINT'])
					$arProp['DATA'][] = 'data-powertip="' . ($arProp['HINT']) . '"';
			}


			//Add field validation
			if($arParams['VALIDATE_ON']) {
				$data_validation_message = Loc::getMessage('VALIDATION_MESSAGE_' . $arParams[ 'VALIDATE_RULE_' . $arProp['CODE'] ]);
				if(!$data_validation_message)
					$data_validation_message = str_replace('#PROPERTY_NAME#', $arProp['NAME'], Loc::getMessage('VALIDATION_MESSAGE_DEFAULT'));
				else
					$data_validation_message = str_replace('#PROPERTY_NAME#', $arProp['NAME'], $data_validation_message);

				if($arParams[ 'VALIDATE_RULE_' . $arProp['CODE'] ]) {
					$arProp['IS_REQUIRED'] = 'Y';
					$arProp['DATA'][]      = $arParams[ '~VALIDATE_RULE_' . $arProp['CODE'] ];
				}

				if($arParams[ 'VALIDATE_MESS_' . $arProp['CODE'] ])
					$arProp['DATA'][] = $arParams[ '~VALIDATE_MESS_' . $arProp['CODE'] ];
				else
					$arProp['DATA'][] = 'data-fv-message="' . htmlspecialcharsbx($data_validation_message) . '"';
			}


			if($arProp['IS_REQUIRED'] == 'Y' || $arParams['POST_EMAIL_CODE'] == $arProp['CODE']) {
				$arResult['REQUIRED_PROPERTIES'][ $arProp['ID'] ] = $arProp['CODE'];
				$arProp['IS_REQUIRED']                            = 'Y';
			}

			$arResult['DISPLAY_PROPERTIES'][ $arProp['ID'] ] = $arProp;
		}


		$arSite = SiteTable::getRow(array(
			 'select' => array('SITE_NAME', 'EMAIL'),
			 'filter' => array('=LID' => SITE_ID),
		));

		//Set cache vars
		$cache->endDataCache(
			 array(
				 //'FORM_ID'             => $FORM_ID,
				 'SITE'                => $arSite,
				 'DISPLAY_PROPERTIES'  => $arResult["DISPLAY_PROPERTIES"],
				 'REQUIRED_PROPERTIES' => $arResult['REQUIRED_PROPERTIES'],
			 )
		);

		unset($arProp);
		unset($arEnum);
		unset($rsEnum);
	}
}
else
	$arResult['MESSAGE']['WARNING'][] = Loc::getMessage('AFDC_WARNING_IBLOCK_ID');



///////////////////////////////////////////////////////////////////////////////
/// Загрузка файлов в сессию
///////////////////////////////////////////////////////////////////////////////


//DON'T CACHE PROPERTIES
$isAction = ($request->isPost() && check_bitrix_sessid() && $request->get('API_FD_ACTION') && $request->get('UNIQUE_FORM_ID') == $FORM_ID);
if($arResult["DISPLAY_PROPERTIES"]) {

	$action = $request->get('API_FD_ACTION');

	foreach($arResult["DISPLAY_PROPERTIES"] as &$arProp) {

		if($arProp['PROPERTY_TYPE'] == 'F') {

			//Готовим файлы для записи в сессию и временную папку
			if($isAction && $action == 'FILE_UPLOAD') {

				if($arFILE = $_FILES[ $arProp['CODE'] ]) {

					//Создаем папку для загрузки файлов, если не создана
					if(!is_dir($arParams['UPLOAD_TMP_DIR']))
						if(!mkdir($arParams['UPLOAD_TMP_DIR'], 0755, true))
							$arResult['MESSAGE']['WARNING'][] = Loc::getMessage('AFDC_WARNING_UPLOAD_TMP_DIR');

					$response = array(
						 'result'  => 'error',
						 'message' => null,
						 'file'    => null,
					);

					$filename = $oFD::translit($arFILE['name']);
					$filename = $oFD::getUniqueFileName($filename, $arProp['ID'], $USER->GetID());;
					$destination = $arParams['UPLOAD_TMP_DIR'] . '/' . $filename;

					$arFILE['size_round']  = \CFile::FormatSize($arFILE['size'], 0);
					$arFILE['code']        = $filename;
					$arFILE['del']         = '';
					$arFILE['description'] = '';

					/* Sample of file array
					$_FILES => Array(
					 [PROPERTY_CODE] => Array
					 (
						 [name] => 2015-01-30_14.33.02.png
						 [type] => image/png
						 [tmp_name] => D:/OpenServer/domains/tuning-soft.os/upload/formdesigner/56ad434f08e7f87a4e89cd710341d3b8.png
						 [error] => 0
						 [size] => 155756
						 [size_round] => 210.33 KB
						 [fake_name] => 56ad434f08e7f87a4e89cd710341d3b8.png
						 [del] =>
						 [description] =>
					 ),
					);
					*/

					if($arFILE['error'] == 0) {

						if(@is_uploaded_file($_FILES[ $arProp['CODE'] ]['tmp_name'])) {
							$res = CFile::CheckFile($arFILE, $arParams['UPLOAD_MAX_FILESIZE'], false, $arProp['FILE_TYPE']);
							if(strlen($res) > 0) {
								$response['message'] = $res;
							}
							else {
								@move_uploaded_file($_FILES[ $arProp['CODE'] ]['tmp_name'], $destination);
							}
						}

						if(is_file($destination) && file_exists($destination)) {
							$arFILE['tmp_name'] = $destination;

							//Main session file array
							$_SESSION['API_FORMDESIGNER'][ $FORM_ID ]['FILES'][ $arProp['CODE'] ][ $filename ] = $arFILE;

							$response = array(
								 'result'  => 'ok',
								 'message' => null,
								 'file'    => $arFILE,
							);
						}
					}
					else {
						$response['message'] = $arFILE['error'];
						//$APPLICATION->RestartBuffer();
						//header('HTTP/1.1 400 Bad Request');
						//die($arFILE['error']);
					}

					$APPLICATION->RestartBuffer();
					echo Json::encode($response);
					die();
				}
			}

			$arProp['DISPLAY_VALUE'] = $arProp['USER_VALUE'] = $_SESSION['API_FORMDESIGNER'][ $FORM_ID ]['FILES'][ $arProp['CODE'] ];

			$arResult['DISPLAY_PROPERTIES'][ $arProp['ID'] ] = $arProp;
		}
		else {

			//E-mail авторизованного пользователя по умолчанию
			if($arParams['POST_EMAIL_CODE'] && $arParams['POST_EMAIL_CODE'] == $arProp['CODE']) {
				if($USER->IsAuthorized())
					$arProp['USER_VALUE'] = $USER->GetEmail();
			}
		}
	}

	$response = array();
	if($isAction) {

		//Удаление файлов
		if($action == 'FILE_DELETE') {
			$fileName = $request->get('FILE_NAME');
			$fileCode = $request->get('FILE_CODE');
			if($fileName && $fileCode) {

				//Удалит файл с диска
				$filePath = $arParams['UPLOAD_TMP_DIR'] . '/' . $fileCode;
				if(is_file($filePath) && file_exists($filePath)) {
					@unlink($filePath);
				}

				//Удалит файл из сессии
				if($arSessFile = &$_SESSION['API_FORMDESIGNER'][ $FORM_ID ]['FILES'][ $fileName ]) {
					if(isset($arSessFile[ $fileCode ]))
						unset($arSessFile[ $fileCode ]);
				}
			}
			unset($fileName, $fileCode, $filePath, $arSessFile);
		}

		if($action == 'CAPTCHA_REFRESH') {
			$captchaCode = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
			$response    = array(
				 'captcha_sid' => $captchaCode,
				 'captcha_src' => '/bitrix/tools/captcha.php?captcha_sid=' . $captchaCode,
			);
		}

		$APPLICATION->RestartBuffer();
		if($response) {
			header('Content-Type: application/json');
			echo Json::encode($response);
		}
		die();
	}

	unset($arProp, $action, $isAction);
}

//unset($_SESSION['API_FORMDESIGNER'][$FORM_ID]['FILES']);


$arResult['ANTIBOT'] = $_REQUEST['ANTIBOT'];


///////////////////////////////////////////////////////////////////////////////
/// Отправка формы
///////////////////////////////////////////////////////////////////////////////

$isPost = ($request->isPost() && $request->get('API_FD_AJAX') && $FORM_ID == $request->get('UNIQUE_FORM_ID'));
if($isPost) {
	if(check_bitrix_sessid()) {

		//Validate ANTIBOT
		if(isset($arResult['ANTIBOT']) && is_array($arResult['ANTIBOT'])) {
			foreach($arResult['ANTIBOT'] as $k => $v)
				if(empty($v))
					unset($arResult['ANTIBOT'][ $k ]);
		}

		if($arResult['ANTIBOT'] || !isset($arResult['ANTIBOT']))
			return;


		//Validate captha
		if($arParams['USE_BX_CAPTCHA']) {
			$captcha      = (array)$_POST['CAPTCHA'];
			$captcha_word = $captcha['WORD'];
			$captcha_sid  = $captcha['SID'];

			if(!$APPLICATION->CaptchaCheckCode($captcha_word, $captcha_sid)) {
				$arResult['MESSAGE']['HIDDEN']['CAPTCHA'] = Loc::getMessage('AFDC_DANGER_CAPTCHA_WRONG');
			}

			unset($captcha, $captcha_word, $captcha_sid);
		}

		//Validate user consent
		if($arParams['USER_CONSENT']) {
			$reqConsent = (array)$request->get('USER_CONSENT');

			foreach($arResult['DISPLAY_USER_CONSENT'] as $agreementId => $arAgreement) {
				if(!in_array($agreementId, $reqConsent)) {
					$arResult['DISPLAY_USER_CONSENT'][ $agreementId ]['ERROR']       = Loc::getMessage('AFDC_USER_CONSENT_ERROR');
					$arResult['MESSAGE']['HIDDEN'][ 'USER_CONSENT_' . $agreementId ] = Loc::getMessage('AFDC_USER_CONSENT_ERROR');
				}
				else {
					$arResult['DISPLAY_USER_CONSENT'][ $agreementId ]['USER_VALUE'] = $agreementId;
				}
			}

			unset($agreementId, $arAgreement, $reqConsent);
		}

		//---------- Prepare $_POST ----------//
		$arPost       = $request->getPostList()->toArray();
		$arPostFields = $request->get('FIELDS');
		$mailFiles    = array();

		if($arPostFields) {
			foreach($arPostFields as $pKey => $pVal) {

				//Clear empty array
				if(is_array($pVal)) {
					if(!empty($pVal)) {
						foreach($pVal as $k => $v)
							if(empty($v))
								unset($pVal[ $k ]);
					}
					else {
						$pVal = array();
					}
				}
				else {
					$pVal = trim($pVal);
				}

				if(!$isUtfMode)
					$pVal = Encoding::convertEncoding($pVal, 'UTF-8', $context->getCulture()->getCharset());

				$arPostFields[ $pKey ] = $pVal;
			}
		}


		//---------- Validate user email ----------//
		if($mailPropCode = $arParams['POST_EMAIL_CODE']) {
			$arParams['USER_EMAIL'] = $arPostFields[ $mailPropCode ];

			if(!check_email($arParams['USER_EMAIL']))
				$arResult['MESSAGE']['HIDDEN'][ $mailPropCode ] = Loc::getMessage('AFDC_DEFAULT_MESS_CHECK_EMAIL');
		}


		//---------- Validate required fields ----------//
		if($arResult['REQUIRED_PROPERTIES']) {
			foreach($arResult['REQUIRED_PROPERTIES'] as $reqPropID => $reqPropCode) {

				//Подставим значения поля Файл в $arPostFields, т.к. они хранятся в сессии и USER_VALUE, в $_POST их не будет.
				if($prop = $arResult['DISPLAY_PROPERTIES'][ $reqPropID ]) {
					if($prop['PROPERTY_TYPE'] == 'F') {
						$arPostFields[ $reqPropCode ] = $prop['USER_VALUE'];
					}
					unset($prop);
				}

				//Далее стандартный перебор полей формы, с учетом файлов
				if(empty($arPostFields[ $reqPropCode ])) {
					$field = $arResult['DISPLAY_PROPERTIES'][ $reqPropID ]['NAME'];
					$error = str_replace('#FIELD#', $field, $arParams['MESS_REQUIRED_FIELD']);

					$arResult['MESSAGE']['HIDDEN'][ $reqPropCode ] = $error;

					$arResult['DISPLAY_PROPERTIES'][ $reqPropID ]['ERROR'] = $error;
				}
			}

			unset($field, $error);
		}


		//Variables for iblock Add
		$PROPERTY_VALUES = array();
		$FIELD_VALUES    = array();
		//\\Variables for iblock Add

		if($arResult['DISPLAY_PROPERTIES']) {
			$postValue = '';
			foreach($arResult['DISPLAY_PROPERTIES'] as $bPropID => $arProp) {
				$postValue = $arPostFields[ $arProp['CODE'] ];



				//--------------------------------//
				//---------- FOR IBLOCK ----------//
				//--------------------------------//
				if($arProp['USER_TYPE'] == 'HTML') {
					//$type = ($arProp['DEFAULT_VALUE']['TYPE'] == 'TEXT' ? 'TEXT' : 'HTML');

					if(is_array($postValue)) {
						foreach($postValue as $k => $v)
							$PROPERTY_VALUES[ $arProp['CODE'] ][] = array(
								 "VALUE" => array(
										"TEXT" => ($arParams['WYSIWYG_ON'] ? $postValue[ $k ] : TxtToHTML($postValue[ $k ])),
										"TYPE" => "HTML",
								 ),
							);
					}
					else
						$PROPERTY_VALUES[ $arProp['CODE'] ][] = array(
							 "VALUE" => array(
									"TEXT" => ($arParams['WYSIWYG_ON'] ? $postValue : TxtToHTML($postValue)),
									"TYPE" => "HTML",
							 ),
						);
				}
				elseif($arProp['USER_TYPE'] == 'TEXT') {
					if(is_array($postValue)) {
						foreach($postValue as $k => $v)
							$PROPERTY_VALUES[ $arProp['CODE'] ][] = array("VALUE" => array("TEXT" => TxtToHTML($postValue[ $k ]), "TYPE" => "TEXT"));
					}
					else
						$PROPERTY_VALUES[ $arProp['CODE'] ][] = array("VALUE" => array("TEXT" => TxtToHTML($postValue), "TYPE" => "TEXT"));
				}
				elseif($arProp['PROPERTY_TYPE'] == 'G') {
					if(!$arProp['LINK_IBLOCK_ID'])
						$FIELD_VALUES['IBLOCK_SECTION'] = $postValue;
					else
						$PROPERTY_VALUES[ $arProp['CODE'] ] = $postValue;
				}
				elseif($arProp['PROPERTY_TYPE'] == 'F') {
					$PROPERTY_VALUES[ $arProp['CODE'] ] = ($postValue ? $postValue : $_SESSION['API_FORMDESIGNER'][ $FORM_ID ]['FILES'][ $arProp['CODE'] ]);
					//$PROPERTY_VALUES[ $arProp['CODE'] ] = $_SESSION['API_FORMDESIGNER'][$FORM_ID]['FILES'][ $arProp['CODE'] ];
				}
				else
					$PROPERTY_VALUES[ $arProp['CODE'] ] = $postValue;
				//\\ END FOR IBLOCK


				//-------------------------------//
				//---------- FOR EMAIL ----------//
				//-------------------------------//
				if($arProp['PROPERTY_TYPE'] == 'L' ||
					 $arProp['PROPERTY_TYPE'] == 'G' ||
					 $arProp['PROPERTY_TYPE'] == 'E' ||
					 $arProp['USER_TYPE'] == 'UserID' ||
					 $arProp['USER_TYPE'] == 'APIFD_ESList' ||
					 $arProp['USER_TYPE'] == 'APIFD_PSList'
				) {
					if(empty($postValue) && $arProp['MULTIPLE'] == 'Y')
						$arResult['DISPLAY_PROPERTIES'][ $bPropID ]['PRINT_VALUE'] = array();


					if($arProp['USER_TYPE'] == 'APIFD_ESList') {
						$printValue = '';
						if($arDisplayValues = $arResult['DISPLAY_PROPERTIES'][ $bPropID ]['DISPLAY_VALUE']) {
							foreach($arDisplayValues as $section) {
								foreach($section['ITEMS'] as $element) {
									if(is_array($postValue)) {
										if(in_array($element['ID'], $postValue))
											$printValue = $element['NAME'];
									}
									else {
										if($element['ID'] == $postValue)
											$printValue = $element['NAME'];
									}
								}
							}
						}

						$arResult['DISPLAY_PROPERTIES'][ $bPropID ]['PRINT_VALUE'] = $printValue;
					}
					elseif($arProp['USER_TYPE'] == 'APIFD_PSList') {
						$printValue = '';
						if($arDisplayValues = $arResult['DISPLAY_PROPERTIES'][ $bPropID ]['DISPLAY_VALUE']) {
							foreach($arDisplayValues as $element) {
								if($element['ID'] == $postValue)
									$printValue = $element['NAME'];
							}
						}

						$arResult['DISPLAY_PROPERTIES'][ $bPropID ]['PRINT_VALUE'] = $printValue;
					}
					elseif(is_array($postValue)) {
						foreach($postValue as $k => $v) {
							$arResult['DISPLAY_PROPERTIES'][ $bPropID ]['PRINT_VALUE'][ $k ] = trim($arResult['DISPLAY_PROPERTIES'][ $bPropID ]['DISPLAY_VALUE'][ $v ]['VALUE']);
						}
					}
					else
						$arResult['DISPLAY_PROPERTIES'][ $bPropID ]['PRINT_VALUE'] = trim($arResult['DISPLAY_PROPERTIES'][ $bPropID ]['DISPLAY_VALUE'][ $postValue ]['VALUE']);
				}
				//S:directory - Справочник (кастомный)
				elseif($arProp["USER_TYPE"] == 'directory' && $bUseHL) {
					$printValue = '';

					if($arDisplayValues = $arResult['DISPLAY_PROPERTIES'][ $bPropID ]['DISPLAY_VALUE']) {
						foreach($arDisplayValues as $item) {
							if(is_array($postValue)) {
								if(in_array($item['UF_XML_ID'], $postValue)) {
									if($item['UF_FILE']) {
										$printValue .= '<img src="' . $arParams['HTTP_HOST'] . $item['UF_FILE']['SRC'] . '" alt="###" style="vertical-align:middle;"> ';
										$printValue .= '[' . $item['ID'] . '] ' . $item['UF_NAME'] . ' (' . $item['UF_XML_ID'] . ')<br>';
									}
									else
										$printValue .= '[' . $item['ID'] . '] ' . $item['UF_NAME'] . ' (' . $item['UF_XML_ID'] . ')<br>';
								}
							}
							elseif($item['UF_XML_ID'] == $postValue) {
								if($item['UF_FILE']) {
									$printValue .= '<img src="' . $arParams['HTTP_HOST'] . $item['UF_FILE']['SRC'] . '" alt="###"  style="vertical-align:middle;"> ';
									$printValue .= '[' . $item['ID'] . '] ' . $item['UF_NAME'] . ' (' . $item['UF_XML_ID'] . ')<br>';
								}
								else
									$printValue = '[' . $item['ID'] . '] ' . $item['UF_NAME'] . ' (' . $item['UF_XML_ID'] . ')<br>';
							}
						}
					}

					$arResult['DISPLAY_PROPERTIES'][ $bPropID ]['PRINT_VALUE'] = $printValue;
				}
				elseif($arProp['PROPERTY_TYPE'] == 'F') {

					$files = '';
					if($arProp['USER_VALUE']) {
						foreach($arProp['USER_VALUE'] as $name => $file) {
							//Массив абсолютных путей к файлам для отправки в письмах вложением
							$mailFiles[] = $file['tmp_name'];

							//Список прямых ссылок для файлов на сервере, т.r. после отправки письма они удаляются, смысла в них нет
							//$files       .= $arParams['HTTP_HOST'] . $arParams['UPLOAD_FOLDER'] . '/' . $name . '<br>';

							//Лучше отправим в письме текстом названия файлов соответствующие их md5-кодам
							$files .= $file['name'] . "\n";
						}
					}
					$arResult['DISPLAY_PROPERTIES'][ $bPropID ]['PRINT_VALUE'] = $files;
				}
				else
					$arResult['DISPLAY_PROPERTIES'][ $bPropID ]['PRINT_VALUE'] = $postValue;
				//\\ END FOR EMAIL


				if($arParams['POST_EMAIL_CODE'] == $arProp['CODE'] && $postValue) {
					if(!check_email($postValue))
						$arResult['DISPLAY_PROPERTIES'][ $bPropID ]['ERROR'] = $arParams['MESS_CHECK_EMAIL'];
				}

				//For user values show in form
				$arResult['DISPLAY_PROPERTIES'][ $bPropID ]['USER_VALUE'] = $postValue;
			}

			unset($arProp);
		}


		if(empty($arResult['MESSAGE'])) {

			//saveConsent
			if($arParams['USER_CONSENT']) {
				foreach($arResult['DISPLAY_USER_CONSENT'] as $consetId => $arConsent) {
					\Bitrix\Main\UserConsent\Consent::addByContext(
						 $consetId,
						 $request->getHttpHost(), //30 chars max
						 'api.formdesigner',      //30 chars max
						 array(
								'IP'  => $request->getRemoteAddress(),
								'URL' => $arParams['HTTP_HOST'] . $request->getRequestUri(),
						 )
					);
				}
			}



			//////////////////////////////////////////////////
			//          Prepare service fields
			/////////////////////////////////////////////////
			$arParams['SITE_NAME']          = ($arSite['SITE_NAME'] ? $arSite['SITE_NAME'] : Option::get('main', 'site_name', $request->getHttpHost()));
			$arParams['SERVER_NAME']        = $request->getHttpHost();
			$arParams['DEFAULT_EMAIL_FROM'] = ($arSite['EMAIL'] ? $arSite['EMAIL'] : Option::get('main', 'email_from', 'info@' . $request->getHttpHost()));
			$arParams['EMAIL_FROM']         = ($arParams['EMAIL_FROM'] ? $arParams['EMAIL_FROM'] : $arParams['DEFAULT_EMAIL_FROM']);
			$arParams['EMAIL_TO']           = ($arParams['EMAIL_TO'] ? $arParams['EMAIL_TO'] : $arParams['DEFAULT_EMAIL_FROM']);

			$arSystemFields = array(
				//SYSTEM_VARS
				'WORK_AREA'          => '',
				'SITE_NAME'          => ($arParams['SITE_NAME'] ? $arParams['SITE_NAME'] : $request->getHttpHost()),
				'SERVER_NAME'        => ($arParams['SERVER_NAME'] ? $arParams['SERVER_NAME'] : $request->getHttpHost()),
				'DEFAULT_EMAIL_FROM' => $arParams['DEFAULT_EMAIL_FROM'],
				'EMAIL_FROM'         => $arParams['EMAIL_FROM'],
				'EMAIL_TO'           => $arParams['EMAIL_TO'],
				'BCC'                => $arParams['BCC'],
				'TICKET_ID'          => $arResult['TICKET_ID'],
				'ELEMENT_ID'         => $arResult['ELEMENT_ID'],
				'HTTP_HOST'          => $arParams['HTTP_HOST'],
				'FORM_ID'            => $FORM_ID,

				//PAGE_VARS
				'FORM_TITLE'         => $sysFormTitle,
				'PAGE_TITLE'         => $sysPageTitle,
				'PAGE_URL'           => $sysPageUrl,
				'DIR_URL'            => $sysDirUrl,
				'DATE_TIME'          => $sysDateTime,
				'DATE'               => $sysDate,
				'IP'                 => $sysIp,
			);


			//////////////////////////////////////////////////
			//          Iblock element add
			/////////////////////////////////////////////////
			if($arParams['IBLOCK_ON'] && $arParams['IBLOCK_TICKET_CODE'] && $arParams['IBLOCK_ID']) {
				$el = new CIBlockElement();

				$TICKET_ID = CApiFormDesigner::getTicketID($arParams['IBLOCK_TICKET_CODE'], $arParams['IBLOCK_ID']);

				$arResult['TICKET_ID']       = $TICKET_ID;
				$arSystemFields['TICKET_ID'] = $TICKET_ID;

				$PROPERTY_VALUES[ $arParams['IBLOCK_TICKET_CODE'] ] = $TICKET_ID;

				$arParams['IBLOCK_ELEMENT_NAME'] = CApiFormDesigner::replaceMacros($arParams['IBLOCK_ELEMENT_NAME'], $PROPERTY_VALUES, $arSystemFields);
				$arParams['IBLOCK_ELEMENT_CODE'] = CApiFormDesigner::replaceMacros($arParams['IBLOCK_ELEMENT_CODE'], $PROPERTY_VALUES, $arSystemFields);

				if(!$arParams['IBLOCK_ELEMENT_NAME'])
					$arParams['IBLOCK_ELEMENT_NAME'] = 'Ticket#' . $TICKET_ID;

				if(!$arParams['IBLOCK_ELEMENT_CODE']) {
					if($arParams['IBLOCK_ELEMENT_NAME']) {
						$arParams['IBLOCK_ELEMENT_CODE'] = CUtil::translit(
							 $arParams['IBLOCK_ELEMENT_NAME'],
							 LANGUAGE_ID,
							 array("replace_space" => "-", "replace_other" => "-")
						);
					}
					else {
						$arParams['IBLOCK_ELEMENT_CODE'] = 'Ticket#' . $TICKET_ID;
					}
				}


				$arIblockFields = array(
					 'IBLOCK_ID'        => $arParams['IBLOCK_ID'],
					 //'DATE_ACTIVE_FROM' => date($DB->DateFormatToPHP(CSite::GetDateFormat())),
					 'DATE_ACTIVE_FROM' => ConvertTimeStamp(time() + CTimeZone::GetOffset(), 'FULL'),
					 'IBLOCK_SECTION'   => $FIELD_VALUES['IBLOCK_SECTION'],
					 'ACTIVE'           => $arParams['IBLOCK_ELEMENT_ACTIVE'],
					 'NAME'             => $arParams['IBLOCK_ELEMENT_NAME'],
					 'CODE'             => $arParams['IBLOCK_ELEMENT_CODE'],
					 'PROPERTY_VALUES'  => $PROPERTY_VALUES,
				);

				//Execute events before
				/*foreach(GetModuleEvents('api.formdesigner', "OnBeforeIblockElementAdd", true) as $arEvent) {
					ExecuteModuleEventEx($arEvent, array(&$arIblockFields, &$arParams));
				}*/

				if($elementId = $el->Add($arIblockFields, true, true, true)) {
					$arResult['ELEMENT_ID']       = $elementId;
					$arSystemFields['ELEMENT_ID'] = $elementId;
				}
				else {
					$arResult['MESSAGE']['DANGER'][] = $el->LAST_ERROR;
				}
			}



			//////////////////////////////////////////////////
			//          CRM lead add
			/////////////////////////////////////////////////
			if($arParams['CRM_ON']) {
				$link        = new Crm\Lead($arParams['CRM_ID']);
				$arCrmFields = $link->getFields();

				$arAddFields = array();
				foreach($arResult['DISPLAY_PROPERTIES'] as $arProp) {

					$crmFieldId = $arParams[ 'CRM_FIELD_' . $arProp['CODE'] ];
					$arCrmField = $arCrmFields[ $crmFieldId ];

					if(empty($arProp['USER_VALUE']) || empty($arCrmField)) {
						continue;
					}

					if($arCrmField['TYPE'] == 'enum') {
						//Ищем XML_ID значения списка для crm поля
						if(is_array($arProp['USER_VALUE'])) {
							foreach($arProp['USER_VALUE'] as $valueId) {
								if($arValue = $arProp['DISPLAY_VALUE'][ $valueId ]) {
									if($arValue['XML_ID'])
										$arAddFields[ $crmFieldId ][] = $arValue['XML_ID'];
									else
										$arAddFields[ $crmFieldId ][] = HTMLToTxt($arValue['VALUE']);
								}
							}
						}
						else {
							if($arValue = $arProp['DISPLAY_VALUE'][ $arProp['USER_VALUE'] ]) {
								if($arValue['XML_ID'])
									$arAddFields[ $crmFieldId ] = $arValue['XML_ID'];
								else
									$arAddFields[ $crmFieldId ] = HTMLToTxt($arValue['VALUE']);
							}
						}
					}
					else {

						if(is_array($arProp['USER_VALUE'])) {
							foreach($arProp['USER_VALUE'] as $valueText) {
								$arAddFields[ $crmFieldId ] = HTMLToTxt($arProp['USER_VALUE']);
							}
						}
						else {
							if($arCrmField['ID'] == 'COMMENTS') {
								$arAddFields[ $crmFieldId ] = TxtToHTML($arProp['USER_VALUE']);
							}
							else
								$arAddFields[ $crmFieldId ] = HTMLToTxt($arProp['USER_VALUE']);
						}
					}
				}

				//Required lead title
				if(!$arAddFields['TITLE']) {
					$title = $arParams['CRM_LEAD_TITLE'];
					foreach($arAddFields as $addKey => $addValue) {
						if(is_string($addValue)) {
							$title = str_replace('#' . $addKey . '#', $addValue, $title);
						}
					}
					$arAddFields['TITLE'] = trim(preg_replace('/(#.*?#)/im' . BX_UTF_PCRE_MODIFIER, '', $title));
				}

				//Create lead
				$resLead = $link->add($arAddFields);

				if($arParams['CRM_SHOW_ERRORS'] && $link->error)
					$arResult['MESSAGE']['DANGER'][] = $link->error;

				unset($val, $arProp, $crmFieldId, $arCrmFields, $arAddFields, $addKey, $addValue);
			}


			//////////////////////////////////////////////////
			//          Send e-mail
			/////////////////////////////////////////////////
			if($arParams['POST_ON'] && empty($arResult['MESSAGE'])) {

				$arUserFields = $arFieldsNames = array();
				foreach($arResult['DISPLAY_PROPERTIES'] as $k => $arProp) {
					//User $arPostFields values
					//$arUserFields[ ToUpper($arProp['CODE']) ] = (is_array($arProp['PRINT_VALUE']) ? $arProp['PRINT_VALUE'] : htmlspecialcharsbx($arProp['PRINT_VALUE']));

					$propCode = ToUpper($arProp['CODE']);

					if(is_array($arProp['PRINT_VALUE'])) {
						foreach($arProp['PRINT_VALUE'] as $key => $val) {
							$arUserFields[ $propCode ][ $key ] = $oFD::getTextForEmail($val);
						}
					}
					else {
						if(($arProp['USER_TYPE'] == 'HTML' && $arParams['WYSIWYG_ON']) || ($arProp["USER_TYPE"] == 'directory')) {
							$arUserFields[ $propCode ] = $oFD::getHtmlForEmail($arProp['PRINT_VALUE']);
						}
						else
							$arUserFields[ $propCode ] = $oFD::getTextForEmail($arProp['PRINT_VALUE']);
					}


					//Field names
					$arFieldsNames[ $propCode ] = $arProp['NAME'];
				}

				$arFields = array_merge($arUserFields, $arSystemFields);


				//SEND ADMIN TEMPLATE
				if($arParams['POST_ADMIN_MESSAGE_ID']) {
					$arFields['SUBJECT'] = $arParams['POST_ADMIN_SUBJECT'];
					foreach($arParams['POST_ADMIN_MESSAGE_ID'] as $emID) {
						if(!$oFD->sendMessage($emID, SITE_ID, $arFields, $arFieldsNames, $arParams, false, 'Y', $mailFiles)) {
							if($oFD->LAST_ERROR)
								$arResult['MESSAGE']['DANGER'][] = join('<br>', $oFD->LAST_ERROR);
							else
								$arResult['MESSAGE']['DANGER'][] = Loc::getMessage('AFDC_DANGER_SEND_ADMIN_MESSAGE');
						}
					}
				}


				//SEND USER TEMPLATE
				if($arParams['POST_USER_MESSAGE_ID'] && $arFields['EMAIL_TO']) {
					$arFields['SUBJECT'] = $arParams['POST_USER_SUBJECT'];
					foreach($arParams['POST_USER_MESSAGE_ID'] as $emID) {
						if(!$oFD->sendMessage($emID, SITE_ID, $arFields, $arFieldsNames, $arParams, true, 'Y', $mailFiles)) {
							if($oFD->LAST_ERROR)
								$arResult['MESSAGE']['DANGER'][] = join('<br>', $oFD->LAST_ERROR);
							else
								$arResult['MESSAGE']['DANGER'][] = Loc::getMessage('AFDC_DANGER_SEND_USER_MESSAGE');
						}
					}
				}
			}


			//CLEAR POST DATA IF NO ERRORS BEFORE
			if(empty($arResult['MESSAGE'])) {

				/*
				if(!$arParams['REDIRECT_URL'])
					$_SESSION['API_FORMDESIGNER'][ $FORM_ID ]['SUCCESS'] = true;
				else
					$arResult['REDIRECT_URL'] = $arParams['REDIRECT_URL'];
				*/

				$_SESSION['API_FORMDESIGNER'][ $FORM_ID ]['SUCCESS'] = true;
			}
		}
	}
	else {
		$arResult['MESSAGE']['DANGER'][] = Loc::getMessage('AFDC_DANGER_CHECK_BITRIX_SESSID');
	}



	if($_SESSION['API_FORMDESIGNER'][ $FORM_ID ]['SUCCESS']) {
		$arParams['SEND_GOALS']                              = true;
		$arResult['MESSAGE']['SUCCESS'][]                    = $arParams['MESS_SUCCESS'];
		$_SESSION['API_FORMDESIGNER'][ $FORM_ID ]['SUCCESS'] = false;


		//Удаляем файлы с диска и сессии после успешной отправки формы
		if($arFiles = $_SESSION['API_FORMDESIGNER'][ $FORM_ID ]['FILES']) {
			foreach($arFiles as $file) {
				if(is_array($file)) {
					foreach($file as $f) {
						if(file_exists($f['tmp_name'])) {
							@unlink($f['tmp_name']);
						}
					}
				}
				else {
					if(file_exists($file['tmp_name'])) {
						@unlink($file['tmp_name']);
					}
				}
			}
			unset($_SESSION['API_FORMDESIGNER'][ $FORM_ID ]['FILES'], $arFiles, $file, $f);
		}



		//Clear POST data
		if($arResult["DISPLAY_PROPERTIES"]) {
			foreach($arResult["DISPLAY_PROPERTIES"] as &$arProp) {
				if($arParams['HIDE_FIELDS'] && !in_array($arProp['CODE'], $arParams['HIDE_FIELDS']))
					$arProp['USER_VALUE'] = (is_array($arProp['USER_VALUE']) ? array() : '');
			}
		}
	}


	$arResult['EULA_ACCEPTED']    = ($arPost['EULA_ACCEPTED'] == 'Y' ? 'Y' : 'N');
	$arResult['PRIVACY_ACCEPTED'] = ($arPost['PRIVACY_ACCEPTED'] == 'Y' ? 'Y' : 'N');
}


//SEND_GOALS
$arResult['GOALS_SETTINGS'] = '';
if($arParams['SEND_GOALS']) {

	if($arParams['YAMETRIKA_ON'] && $arParams['YAMETRIKA_COUNTER_ID'] && $arParams['YAMETRIKA_TARGET_NAME']) {
		$arResult['GOALS_SETTINGS'] .= "
			(function (d, w) {
				try {
						w.yaCounter" . $arParams['YAMETRIKA_COUNTER_ID'] . ".reachGoal('" . $arParams['YAMETRIKA_TARGET_NAME'] . "');
				} catch(e) {
					console.error('YM_ERROR: ' + e.name + ' - ' + e.message + '\\n' + e.stack);
			  }
			})(document, window);
			";
	}

	if($arParams['YM2_ON'] && $arParams['YM2_COUNTER'] && $arParams['YM2_GOAL_SUBMIT_FORM_SUCCESS']) {
		$arResult['GOALS_SETTINGS'] .= "
			(function (d, w) {
				try {
						w.yaCounter" . $arParams['YM2_COUNTER'] . ".reachGoal('" . $arParams['YM2_GOAL_SUBMIT_FORM_SUCCESS'] . "');
				} catch(e) {
					console.error('YM2_ERROR: ' + e.name + ' - ' + e.message + '\\n' + e.stack);
			  }
			})(document, window);
			";
	}

	if($arParams['GA_ON'] && $arParams['GA_GTAG']) {
		$arResult['GOALS_SETTINGS'] .= "
			(function (d, w) {
				try {
					" . $arParams['GA_GTAG'] . "
				} catch(e) { 
					console.error('GA_ERROR: ' + e.name + ' - ' + e.message + '\\n' + e.stack);
				}
			})(document, window);
			";
		/*$arResult['GOALS_SETTINGS'] .= "
			(function (d, w) {
				try {
					ga('send', 'event', '" . $arParams['GA_CATEGORY'] . "', '" . $arParams['GA_ACTION'] . "', '" . $arParams['GA_LABEL'] . "', " . $arParams['GA_VALUE'] . ");
				} catch(e) { }
			})(document, window);
			";*/
	}
}


//Echo in template
//$arResult['JQUERY_SETTINGS'] = $arResult['INPUTMASK_SETTINGS'] . $arResult['POWERTIP_SETTINGS'] . $arResult['VALIDATE_SETTINGS'] . $arResult['FILES_SETTINGS'];

$arResult['JS_REDIRECT'] = '';
if($arParams['REDIRECT_URL']) {
	$arResult['JS_REDIRECT'] = "
		(function (d, w) {
			function fnReload() {
	      w.location.href = '" . CUtil::JSEscape($arParams['REDIRECT_URL']) . "';
			}
			w.setTimeout(fnReload, 2000);
		})(document, window);
	";
}


if($arResult['MESSAGE']['SUCCESS']) {
	foreach($arResult['MESSAGE']['SUCCESS'] as &$mess)
		$mess = str_replace('#TICKET_ID#', $arResult['TICKET_ID'], $mess);

	if($arParams['MESS_SUCCESS_DESC'])
		$arParams['MESS_SUCCESS_DESC'] = str_replace('#TICKET_ID#', $arResult['TICKET_ID'], $arParams['MESS_SUCCESS_DESC']);
}


if($bUseCore) {
	$extList = array(
		 'api_upload', 'api_button', 'api_modal', 'api_alert', 'api_flatpickr', 'api_icon', 'api_form',
	);

	if($arParams['INPUTMASK_JS'])
		$extList[] = 'api_inputmask';

	if($arParams['WYSIWYG_ON'])
		$extList[] = 'api_redactor2';

	if($arParams['VALIDATE_ON'])
		$extList[] = 'api_formvalidation';

	\CJSCore::Init($extList);
}

//Prepare captcha
if($arParams['USE_BX_CAPTCHA']) {
	$arResult['CAPTCHA_CODE'] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
}


if($isPost) {
	$arResult['POST'] = true;

	if($arParams['COMPATIBLE_ON']) {
		$arParams['SHOW_ERRORS_IN_FIELD'] = true;
		$arParams['SHOW_ERRORS_BOTTOM']   = $arParams['SHOW_ERRORS'];

		if($arResult['MESSAGE']['SUCCESS'])
			$arResult['JQUERY_AJAX'] = $arResult['JS_REDIRECT'];

		$APPLICATION->RestartBuffer();
		$this->includeComponentTemplate('ajax');
		die();
	}
	else {
		$jsParams = array(
			 'COMPATIBLE_ON',
			 'SHOW_ERRORS',
			 'SEND_GOALS',
			 'MESS_SUCCESS_DESC',
			 'MESS_SUCCESS_BTN',
		);
		$jsParams = array_flip($jsParams);

		foreach($jsParams as $pK => $pV) {
			$jsParams[ $pK ] = $arParams[ $pK ];
		}

		$result = array(
			 'params' => $jsParams,
			 'result' => $arResult,
		);

		$APPLICATION->RestartBuffer();
		header('Content-Type: application/json');
		echo Json::encode($result);
		die();
	}
}
else {
	$this->showComponentTemplate();
}