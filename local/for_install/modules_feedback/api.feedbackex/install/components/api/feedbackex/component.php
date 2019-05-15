<?

use Bitrix\Main,
	 Bitrix\Main\Loader,
	 Bitrix\Main\Application,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

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
 *
 */

//ID компонента
//$cpId = $this->getEditAreaId($this->__currentCounter);

//Объект родительского компонента
//$parent = $this->getParent();
//$parentPath = $parent->getPath();

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.feedbackex')) {
	ShowError(Loc::getMessage('API_FEX_MODULE_ERROR'));
	return;
}

$bUseCore = Loader::includeModule('api.core');

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();


//Component mess from current template mess
$MESS = CApiFeedbackEx::incComponentLang($this);

/*if($this->initComponentTemplate()) {
	Loc::loadMessages($server->getDocumentRoot() . $this->getTemplate()->GetFile());
}*/


//==============================================================================
// $arParams
//==============================================================================

//BASE
$arParams['CONFIG_PATH']          = trim($arParams['CONFIG_PATH']);
$arParams['DISABLE_SEND_MAIL']    = $arParams['DISABLE_SEND_MAIL'] === 'Y';
$arParams['DISABLE_CHECK_SESSID'] = $arParams['DISABLE_CHECK_SESSID'] === 'Y';
$arParams['USE_SCROLL']           = $arParams['USE_SCROLL'] === 'Y';
$arParams['SCROLL_SPEED']         = $arParams['SCROLL_SPEED'] ? $arParams['SCROLL_SPEED'] : 1000;
$arParams['REPLACE_FIELD_FROM']   = $arParams['REPLACE_FIELD_FROM'] === 'Y';

$arParams['API_FEX_FORM_ID'] = $arParams['API_FEX_FORM_ID'] ? trim($arParams['API_FEX_FORM_ID']) : $this->GetEditAreaId($this->__currentCounter);

$arParams['HTTP_PROTOCOL'] = $request->isHttps() ? 'https://' : 'http://';
$arParams['HTTP_HOST']     = $arParams['HTTP_PROTOCOL'] . $server->getHttpHost();
$arParams['EVENT_NAME']    = 'API_FEEDBACKEX';


$arParams['FORM_FIELDS']     = CApiFeedbackEx::getFields(false, $arParams['CONFIG_PATH']);
$arParams['DISPLAY_FIELDS']  = array_diff((array)$arParams['DISPLAY_FIELDS'], array(''));
$arParams['REQUIRED_FIELDS'] = array_diff((array)$arParams['REQUIRED_FIELDS'], array(''));

if(!$arParams['DISPLAY_FIELDS']) //Выводим все поля, если не задано ни одно
	$arParams['DISPLAY_FIELDS'] = CApiFeedbackEx::getFieldsKeys($arParams['FORM_FIELDS']);


$arParams['USER_EMAIL'] = '';
$arParams['EMAIL_TO']   = trim($arParams['EMAIL_TO']);
$arParams['BCC']        = trim($arParams['BCC']);

$arParams['OK_TEXT']       = $arParams['OK_TEXT'] ? htmlspecialcharsback(trim($arParams['OK_TEXT'])) : $MESS['API_FEX_CP_MESSAGE_OK_TEXT'];
$arParams['OK_TEXT_AFTER'] = $arParams['OK_TEXT_AFTER'] ? htmlspecialcharsback(trim($arParams['OK_TEXT_AFTER'])) : $MESS['API_FEX_CP_MESSAGE_OK_TEXT_AFTER'];

$arParams['DEFAULT_OPTION_TEXT'] = $DEFAULT_OPTION_TEXT = trim($arParams['DEFAULT_OPTION_TEXT']);


//EVENT_MESSAGE_SETTINGS
$arParams['WRITE_MESS_FILDES_TABLE']      = $arParams['WRITE_MESS_FILDES_TABLE'] == 'Y';
$arParams['WRITE_MESS_TABLE_STYLE']       = trim($arParams['WRITE_MESS_TABLE_STYLE']);
$arParams['WRITE_MESS_TABLE_STYLE_NAME']  = trim($arParams['WRITE_MESS_TABLE_STYLE_NAME']);
$arParams['WRITE_MESS_TABLE_STYLE_VALUE'] = trim($arParams['WRITE_MESS_TABLE_STYLE_VALUE']);
$arParams['WRITE_MESS_DIV_STYLE']         = trim($arParams['WRITE_MESS_DIV_STYLE']);
$arParams['WRITE_MESS_DIV_STYLE_NAME']    = trim($arParams['WRITE_MESS_DIV_STYLE_NAME']);
$arParams['WRITE_MESS_DIV_STYLE_VALUE']   = trim($arParams['WRITE_MESS_DIV_STYLE_VALUE']);

$arParams['MAIL_SUBJECT_ADMIN'] = trim($arParams['MAIL_SUBJECT_ADMIN']);
$arParams['MAIL_SUBJECT_USER']  = trim($arParams['MAIL_SUBJECT_USER']);
$arParams['MAIL_SEND_USER']     = $arParams['MAIL_SEND_USER'] == 'Y';


$arParams['FILE_DESCRIPTION'] = array_diff((array)$arParams['FILE_DESCRIPTION'], array(''));


//JQUERY
$arParams['USE_VALIDATION']  = $arParams['USE_VALIDATION'] === 'Y';
$arParams['USE_AUTOSIZE']    = $arParams['USE_AUTOSIZE'] === 'Y';
$arParams['USE_JQUERY']      = $arParams['USE_JQUERY'] === 'Y';
$arParams['USE_PLACEHOLDER'] = $arParams['USE_PLACEHOLDER'] === 'Y';
$arParams['USE_FLATPICKR']   = $arParams['USE_FLATPICKR'] === 'Y';


//VISUAL
$arParams['FORM_WIDTH']            = trim($arParams['FORM_WIDTH']);
$arParams['FORM_CLASS']            = trim($arParams['FORM_CLASS']);
$arParams['HIDE_FIELD_NAME']       = $arParams['HIDE_FIELD_NAME'] === 'Y';
$arParams['HIDE_ASTERISK']         = $arParams['HIDE_ASTERISK'] === 'Y';
$arParams['FORM_AUTOCOMPLETE']     = $arParams['FORM_AUTOCOMPLETE'] === 'Y';
$arParams['FORM_SUBMIT_CLASS']     = trim($arParams['FORM_SUBMIT_CLASS']);
$arParams['FORM_SUBMIT_STYLE']     = trim($arParams['FORM_SUBMIT_STYLE']);
$arParams['FORM_SUBMIT_VALUE']     = htmlspecialcharsback($arParams['FORM_SUBMIT_VALUE']);
$arParams['FIELD_SIZE']            = trim($arParams['FIELD_SIZE']);
$arParams['FIELD_NAME_POSITION']   = trim($arParams['FIELD_NAME_POSITION']);
$arParams['TITLE_DISPLAY']         = $arParams['TITLE_DISPLAY'] == 'Y';
$arParams['FORM_TITLE']            = htmlspecialcharsback($arParams['FORM_TITLE']);
$arParams['FORM_TITLE_LEVEL']      = intval($arParams['FORM_TITLE_LEVEL']);
$arParams['FIELD_ERROR_MESS']      = trim($arParams['FIELD_ERROR_MESS']) ? trim($arParams['FIELD_ERROR_MESS']) : $MESS['API_FEX_CP_FIELD_ERROR_MESS'];
$arParams['EMAIL_ERROR_MESS']      = trim($arParams['EMAIL_ERROR_MESS']) ? trim($arParams['EMAIL_ERROR_MESS']) : $MESS['API_FEX_CP_EMAIL_ERROR_MESS'];
$arParams['FORM_LABEL_TEXT_ALIGN'] = ($arParams['FORM_LABEL_TEXT_ALIGN']) ? trim($arParams['FORM_LABEL_TEXT_ALIGN']) : 'left';
$arParams['FORM_TEXTAREA_ROWS']    = ($arParams['FORM_TEXTAREA_ROWS'] > 0) ? intval($arParams['FORM_TEXTAREA_ROWS']) : 4;

//YM_GOALS
$arParams['SEND_GOALS']     = true;
$arParams['USE_YM_GOALS']   = $arParams['USE_YM_GOALS'] == 'Y';
$arParams['YM_COUNTER_ID']  = trim($arParams['YM_COUNTER_ID']);
$arParams['YM_TARGET_NAME'] = trim($arParams['YM_TARGET_NAME']);


//SERVICE_MACROS_SETTINGS
$arParams['PAGE_URL']   = $arParams['PAGE_URL'] ? $arParams['PAGE_URL'] : $arParams['HTTP_HOST'] . $request->getRequestUri();
$arParams['DIR_URL']    = $arParams['DIR_URL'] ? $arParams['DIR_URL'] : $arParams['HTTP_HOST'] . $request->getRequestedPageDirectory();
$arParams['PAGE_TITLE'] = $arParams['PAGE_TITLE'] ? $arParams['PAGE_TITLE'] : $APPLICATION->GetTitle();
$arParams['DATETIME']   = $arParams['DATETIME'] ? $arParams['DATETIME'] : date('d-m-Y H:i:s');


//MODAL_SETTINGS
$arParams['USE_MODAL']            = ($arParams['USE_MODAL'] == 'Y' && $bUseCore);
$arParams['MODAL_ID']             = $arParams['MODAL_ID'] ? $arParams['MODAL_ID'] : $this->GetEditAreaId($this->randString());
$arParams['MODAL_BTN_TEXT']       = trim($arParams['~MODAL_BTN_TEXT']);
$arParams['MODAL_BTN_CLASS']      = trim($arParams['~MODAL_BTN_CLASS']);
$arParams['MODAL_BTN_ID']         = trim($arParams['~MODAL_BTN_ID']);
$arParams['MODAL_BTN_SPAN_CLASS'] = trim($arParams['~MODAL_BTN_SPAN_CLASS']);
$arParams['MODAL_HEADER_TEXT']    = trim($arParams['~MODAL_HEADER_TEXT']);
$arParams['MODAL_FOOTER_TEXT']    = trim($arParams['~MODAL_FOOTER_TEXT']);

//EULA
$arParams['USE_EULA']          = $arParams['~USE_EULA'] == 'Y';
$arParams['MESS_EULA']         = trim($arParams['~MESS_EULA']);
$arParams['MESS_EULA_CONFIRM'] = trim($arParams['~MESS_EULA_CONFIRM']);

//PRIVACY
$arParams['USE_PRIVACY']          = $arParams['~USE_PRIVACY'] == 'Y';
$arParams['MESS_PRIVACY']         = trim($arParams['~MESS_PRIVACY']);
$arParams['MESS_PRIVACY_LINK']    = trim($arParams['~MESS_PRIVACY_LINK']);
$arParams['MESS_PRIVACY_CONFIRM'] = trim($arParams['~MESS_PRIVACY_CONFIRM']);



//==============================================================================
// Work with cache (On update module with new params this refresh Js & Css)
//==============================================================================
$obCache    = new CPHPCache();
$cacheTime  = 31536000;
$sCacheId   = md5(serialize(CApiFeedbackEx::excludeCacheParams($arParams)) . $this->GetTemplateName());
$sCachePath = $GLOBALS['CACHE_MANAGER']->GetCompCachePath($this->__relativePath);

$arParams['REFRESH_PARAMS'] = false;
if($obCache->InitCache($cacheTime, $sCacheId, $sCachePath)) {
	$arCacheVars = $obCache->GetVars();
}
elseif($obCache->StartDataCache()) {
	$arParams['REFRESH_PARAMS'] = true;

	$obCache->EndDataCache(array(
		 $arParams['API_FEX_FORM_ID'] => $sCacheId,
	));
}



//==============================================================================
// isPost()
//==============================================================================

/**
 * @var array  danger: Сообщения для полей<br>
 * @var array  warning: Служебные сообщения компонента
 */
$arMessage = array();
$arFields  = array();

if($request->isPost() && strlen($request['API_FEX_SUBMIT_ID']) > 0 && $arParams['API_FEX_FORM_ID'] == $request['API_FEX_FORM_ID']) {

	if(!$arParams['DISABLE_CHECK_SESSID']) {
		if(!check_bitrix_sessid()) {
			$arMessage['warning'][] = $MESS['API_FEX_CP_SESSION_EXPIRED'];
		}
	}

	if(empty($arMessage)) {
		//$APPLICATION->ShowAjaxHead();

		$post = $request->getPostList()->toArray();

		//Скрытый антибот
		if(isset($_REQUEST['ANTIBOT']) && is_array($_REQUEST['ANTIBOT'])) {
			foreach($_REQUEST['ANTIBOT'] as $k => $v)
				if(empty($v))
					unset($_REQUEST['ANTIBOT'][ $k ]);
		}

		if($_REQUEST['ANTIBOT'] || !isset($_REQUEST['ANTIBOT'])) {
			$APPLICATION->RestartBuffer();
			die();
		}


		//Обработчик полей
		$arPostFields = $post['FIELDS'];
		foreach($arPostFields as $key => $value) {
			if(in_array($key, $arParams['DISPLAY_FIELDS'])) {
				$arPostFields[ $key ] = is_array($value) ? $value : TxtToHTML($value);
			}
			else {
				unset($arPostFields[ $key ]);
			}
		}


		$emailValue = '';
		$emailCode  = 'EMAIL';
		foreach($arParams['FORM_FIELDS'] as $key => $arField) {

			//Ищем код поля E-mail для валидации
			if($arField['TYPE'] == 'EMAIL') {
				$emailCode  = $key;
				$emailValue = trim($post['FIELDS'][ $key ]);
			}

			//Валидатор полей
			if(in_array($key, $arParams['REQUIRED_FIELDS'])) {
				$postFieldValue = $arPostFields[ $key ];
				if((is_string($postFieldValue) && strlen($postFieldValue) == 0) || (is_array($postFieldValue) && empty($postFieldValue)) || !isset($postFieldValue))
					$arMessage['danger'][ $key ] = str_replace('#FIELD_NAME#', $arField['NAME'], $arParams['FIELD_ERROR_MESS']);
			}
		}

		//Validate e-mail
		if($emailValue && !check_email($emailValue))
			$arMessage['danger'][ $emailCode ] = $arParams['EMAIL_ERROR_MESS'];


		//USER EMAIL ONLY HERE
		$arParams['USER_EMAIL'] = $emailValue;

		if(empty($arMessage)) {
			$arServiceFields = Array(
				 'WORK_AREA'   => '',
				 'FORM_ID'     => $arParams['API_FEX_FORM_ID'],
				 'EMAIL_TO'    => $arParams['EMAIL_TO'],
				 'BCC'         => $arParams['BCC'],
				 'PAGE_URL'    => $arParams['PAGE_URL'],
				 'DIR_URL'     => $arParams['DIR_URL'],
				 'PAGE_TITLE'  => $arParams['PAGE_TITLE'],
				 'DATETIME'    => $arParams['DATETIME'],
				 'DATE_TIME'   => $arParams['DATE_TIME'],
				 'FORM_TITLE'  => $arParams['FORM_TITLE'],
				 'SERVER_NAME' => $request->getHttpHost(),
				 'IP'          => $request->getRemoteAddress(),
			);

			$arFields = array_merge($arPostFields, $arServiceFields);
			if(!Main\Application::isUtfMode())
				$arFields = Main\Text\Encoding::convertEncoding($arFields, 'UTF-8', $context->getCulture()->getCharset());


			$arFieldsCodeName = array();
			$obApiFeedbackEx  = new CApiFeedbackEx();

			//Уберем все лишние поля, которые не участвуют в работе текущей формы
			$arDefaultFields = CApiFeedbackEx::getFields(true, $arParams['CONFIG_PATH']);
			if($arDefaultFields && $arParams['DISPLAY_FIELDS']) {
				foreach($arDefaultFields as $key => $val) {
					if(in_array($key, $arParams['DISPLAY_FIELDS']))
						$arFieldsCodeName[ $key ] = $val;
				}
			}
			else {
				//Иначе все поля на почту отправим
				$arFieldsCodeName = $arDefaultFields;
			}


			//Отправит сообщение администратору
			$arFields['SUBJECT'] = $arParams['MAIL_SUBJECT_ADMIN'];
			if(!$obApiFeedbackEx->Send($arParams['EVENT_NAME'], SITE_ID, $arFields, $arFieldsCodeName, $arParams)) {
				if($obApiFeedbackEx->isSuccess())
					$arMessage['warning'][] = implode('<br>', $obApiFeedbackEx->getErrors());
				else
					$arMessage['warning'][] = $MESS['SEND_MESSAGE_ERROR_ADMIN'];
			}

			//Отправит сообщение пользователю
			if($arParams['MAIL_SEND_USER']) {
				$arFields['SUBJECT'] = $arParams['MAIL_SUBJECT_USER'];
				if(!$obApiFeedbackEx->Send($arParams['EVENT_NAME'], SITE_ID, $arFields, $arFieldsCodeName, $arParams, true)) {
					if($obApiFeedbackEx->isSuccess())
						$arMessage['warning'][] = implode('<br>', $obApiFeedbackEx->getErrors());
					else
						$arMessage['warning'][] = $MESS['SEND_MESSAGE_ERROR_USER'];
				}
			}
		}

		$arResult['FIELDS'] = $arPostFields;
	}


	//==============================================================================
	// View result
	//==============================================================================
	if($arMessage) {
		foreach($arParams['DISPLAY_FIELDS'] as $key) {
			if(!$arMessage['danger'][ $key ])
				$arMessage['danger'][ $key ] = '';
		}


		$result = array(
			 'result'  => 'error',
			 'message' => $arMessage,
			 'html'    => '',
		);
	}
	else {
		$result = array(
			 'result'  => 'ok',
			 'message' => $arParams['OK_TEXT'],
			 'html'    => str_replace(
					array('#OK_TEXT#', '#OK_TEXT_AFTER#'),
					array($arParams['OK_TEXT'], $arParams['OK_TEXT_AFTER']),
					$MESS['API_FEX_CP_MESSAGE_OK_HTML']
			 ),
		);
	}


	$APPLICATION->RestartBuffer();
	echo Bitrix\Main\Web\Json::encode($result);
	die();
}


//CSS ANTIBOT
$arResult['ANTIBOT'] = $_REQUEST['ANTIBOT'];

$this->IncludeComponentTemplate();