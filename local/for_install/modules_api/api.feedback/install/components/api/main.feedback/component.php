<?
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

if(!$exp = CModule::IncludeModuleEx('api.feedback'))
{
	ShowError(GetMessage('API_MF_CP_MODULE_ERROR'));
	return;
}

if($USER->IsAdmin())
{
	if($exp == 2) ShowMessage(GetMessage('API_MF_CP_MODULE_DEMO'));
	if($exp == 3) ShowMessage(GetMessage('API_MF_CP_MODULE_DEMO_EXPIRED'));
}

//$arResult['MESSAGE']['DANGER'] = array();
//$arResult['MESSAGE']['SUCCESS'] = array();
//$arResult['MESSAGE']['WARNING'] = array();
$arResult['MESSAGE']        = array();
$arResult['DEFAULT_PARAMS'] = (array)$arParams;

$ini_get = array(
	'max_file_uploads' => ini_get('max_file_uploads'),
);

if($_REQUEST['API_MF_HIDDEN_PARAMS']){
	$arParams = unserialize(base64_decode($_REQUEST['API_MF_HIDDEN_PARAMS']));
}

$arParams['IBLOCK_ID']     = intval($arParams['IBLOCK_ID']);
$arParams['USE_CAPTCHA']   = ($arParams['USE_CAPTCHA'] == 'Y' && !$USER->IsAuthorized());
$arParams['HTTP_PROTOCOL'] = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
$arParams['EVENT_NAME']    = trim($arParams['EVENT_NAME']);
if(strlen($arParams['EVENT_NAME']) <= 0)
	$arParams['EVENT_NAME'] = 'API_FEEDBACK';

$arParams['REDIRECT_PAGE'] = trim($arParams['REDIRECT_PAGE']);
$arParams['USER_EMAIL']    = '';
$arParams['EMAIL_TO']      = trim(htmlspecialcharsback($arParams['EMAIL_TO']));
$arParams['BCC']           = trim(htmlspecialcharsback($arParams['BCC']));
$arParams['OK_TEXT']       = $arParams['OK_TEXT'] ? htmlspecialcharsback(trim($arParams['OK_TEXT'])) : GetMessage('MF_OK_MESSAGE');
$arParams['OK_TEXT_AFTER'] = htmlspecialcharsback(trim($arParams['OK_TEXT_AFTER']));

$arParams['FORM_TEXT_BEFORE']    = htmlspecialcharsback(trim($arParams['FORM_TEXT_BEFORE']));
$arParams['FORM_TEXT_AFTER']     = htmlspecialcharsback(trim($arParams['FORM_TEXT_AFTER']));
$arParams['BUTTON_TEXT_BEFORE']  = htmlspecialcharsback(trim($arParams['BUTTON_TEXT_BEFORE']));
$arParams['DEFAULT_OPTION_TEXT'] = $DEFAULT_OPTION_TEXT = trim($arParams['DEFAULT_OPTION_TEXT']);

if(!is_array($arParams['DISPLAY_FIELDS']))
	$arParams['DISPLAY_FIELDS'] = array();
foreach($arParams['DISPLAY_FIELDS'] as $k => $v)
{
	if($v === '')
		unset($arParams['DISPLAY_FIELDS'][ $k ]);
}

if(!is_array($arParams['REQUIRED_FIELDS']))
	$arParams['REQUIRED_FIELDS'] = array();
foreach($arParams['REQUIRED_FIELDS'] as $k => $v)
{
	if($v === '')
		unset($arParams['REQUIRED_FIELDS'][ $k ]);
}

if(!is_array($arParams['CUSTOM_FIELDS']))
	$arParams['CUSTOM_FIELDS'] = array();
foreach($arParams['CUSTOM_FIELDS'] as $k => $v)
{
	if($v === '')
		unset($arParams['CUSTOM_FIELDS'][ $k ]);
}

//Sort fields
CApiFeedback::sortFields($arParams['CUSTOM_FIELDS']);

//Group fields
$arParams['GROUP'] = array();
if($arParams['CUSTOM_FIELDS'])
{
	foreach($arParams['CUSTOM_FIELDS'] as $key => $val)
	{
		$arExplodeFields = explode('@', $val);
		foreach($arExplodeFields as $arField)
		{
			if(substr($arField, 0, 5) == "group")
			{
				$group = 0;
				$arData = explode('=', $arField);
				if($arData[1])
					$group = intval($arData[1]);

				$arParams['GROUP'][$group] += 1;
			}
		}
	}
}

//EVENT_MESSAGE_SETTINGS
$arParams['WRITE_ONLY_FILLED_VALUES']     = $arParams['WRITE_ONLY_FILLED_VALUES'] == 'Y';
$arParams['WRITE_MESS_FILDES_TABLE']      = $arParams['WRITE_MESS_FILDES_TABLE'] == 'Y';
$arParams['WRITE_MESS_TABLE_STYLE']       = strlen($arParams['WRITE_MESS_TABLE_STYLE']) ? trim($arParams['WRITE_MESS_TABLE_STYLE']) : 'border-collapse: collapse; border-spacing: 0;';
$arParams['WRITE_MESS_TABLE_STYLE_NAME']  = strlen($arParams['WRITE_MESS_TABLE_STYLE_NAME']) ? trim($arParams['WRITE_MESS_TABLE_STYLE_NAME']) : 'max-width: 200px; color: #848484; vertical-align: middle; padding: 5px 30px 5px 0; border-bottom: 1px solid #e0e0e0; border-top: 1px solid #e0e0e0;';
$arParams['WRITE_MESS_TABLE_STYLE_VALUE'] = strlen($arParams['WRITE_MESS_TABLE_STYLE_VALUE']) ? trim($arParams['WRITE_MESS_TABLE_STYLE_VALUE']) : 'vertical-align: middle; padding: 5px 30px 5px 0; border-bottom: 1px solid #e0e0e0; border-top: 1px solid #e0e0e0;';
$arParams['WRITE_MESS_DIV_STYLE']         = strlen($arParams['WRITE_MESS_DIV_STYLE']) ? trim($arParams['WRITE_MESS_DIV_STYLE']) : 'margin:0 0 20px 0';
$arParams['WRITE_MESS_DIV_STYLE_NAME']    = strlen($arParams['WRITE_MESS_DIV_STYLE_NAME']) ? trim($arParams['WRITE_MESS_DIV_STYLE_NAME']) : 'font-weight:bold;';
$arParams['WRITE_MESS_DIV_STYLE_VALUE']   = trim($arParams['WRITE_MESS_DIV_STYLE_VALUE']);

if(!is_array($arParams['ADMIN_EVENT_MESSAGE_ID']))
	$arParams['ADMIN_EVENT_MESSAGE_ID'] = array();

foreach($arParams['ADMIN_EVENT_MESSAGE_ID'] as $k => $v)
{
	if($v === '')
		unset($arParams['ADMIN_EVENT_MESSAGE_ID'][ $k ]);
}

if(!is_array($arParams['USER_EVENT_MESSAGE_ID']))
	$arParams['USER_EVENT_MESSAGE_ID'] = array();

foreach($arParams['USER_EVENT_MESSAGE_ID'] as $k => $v)
{
	if($v === '')
		unset($arParams['USER_EVENT_MESSAGE_ID'][ $k ]);
}


$arParams['MSG_PRIORITY']  = $arParams['MSG_PRIORITY'] === 'Y';
$arParams['BRANCH_ACTIVE'] = $arParams['BRANCH_ACTIVE'] === 'Y';

if(!is_array($arParams['BRANCH_FIELDS']))
	$arParams['BRANCH_FIELDS'] = array();
foreach($arParams['BRANCH_FIELDS'] as $k => $v)
{
	if($v === '')
		unset($arParams['BRANCH_FIELDS'][ $k ]);
}

if($arParams['BRANCH_ACTIVE'] && $arParams['BRANCH_FIELDS'])
	$arParams['REQUIRED_FIELDS'][] = 'BRANCH';



if(!is_array($arParams['FILE_DESCRIPTION']))
	$arParams['FILE_DESCRIPTION'] = array();
foreach($arParams['FILE_DESCRIPTION'] as $k => $v)
{
	if($v === '')
		unset($arParams['FILE_DESCRIPTION'][ $k ]);
}

if(!is_array($arParams['VALIDATION_MESSAGES']))
	$arParams['VALIDATION_MESSAGES'] = array();
foreach($arParams['VALIDATION_MESSAGES'] as $k => $v)
{
	if($v === '')
		unset($arParams['VALIDATION_MESSAGES'][ $k ]);
}


$arParams['UNIQUE_FORM_ID'] = !empty($arParams['UNIQUE_FORM_ID']) ? $arParams['UNIQUE_FORM_ID'] : 'FORM';
/*if(!isset($_REQUEST['UNIQUE_FORM_ID']))
	$_REQUEST['UNIQUE_FORM_ID'] = $arParams['UNIQUE_FORM_ID'];*/

//BASE SETTINGS
$arParams['IBLOCK_ELEMENT_ACTIVE'] = $arParams['IBLOCK_ELEMENT_ACTIVE'] == 'Y';
$arParams['DISABLE_SEND_MAIL']     = $arParams['DISABLE_SEND_MAIL'] === 'Y';

$arParams['SHOW_FILES']                 = $arParams['SHOW_FILES'] === 'Y';
$arParams['INCLUDE_VALIDATION']         = $arParams['INCLUDE_VALIDATION'] === 'Y';
$arParams['INCLUDE_INPUTMASK']          = $arParams['INCLUDE_INPUTMASK'] === 'Y';
$arParams['SEND_ATTACHMENT']            = $arParams['SEND_ATTACHMENT'] === 'Y';
$arParams['SET_ATTACHMENT_REQUIRED']    = $arParams['SET_ATTACHMENT_REQUIRED'] === 'Y';
$arParams['SHOW_ATTACHMENT_EXTENSIONS'] = $arParams['SHOW_ATTACHMENT_EXTENSIONS'] === 'Y';
$arParams['INCLUDE_AUTOSIZE']           = $arParams['INCLUDE_AUTOSIZE'] === 'Y';
$arParams['INCLUDE_FORM_STYLER']        = $arParams['INCLUDE_FORM_STYLER'] == 'Y';
$arParams['INCLUDE_ICHECK']             = $arParams['INCLUDE_ICHECK'] == 'Y';
$arParams['INCLUDE_CHOSEN']             = $arParams['INCLUDE_CHOSEN'] == 'Y';
$arParams['COUNT_INPUT_FILE']           = intval($arParams['COUNT_INPUT_FILE']);
$arParams['SCROLL_TO_FORM_IF_MESSAGES'] = $arParams['SCROLL_TO_FORM_IF_MESSAGES'] === 'Y';
$arParams['SCROLL_TO_FORM_SPEED']       = intval($arParams['SCROLL_TO_FORM_SPEED']) > 0 ? intval($arParams['SCROLL_TO_FORM_SPEED']) : 1000;
$arParams['REPLACE_FIELD_FROM']         = $arParams['REPLACE_FIELD_FROM'] === 'Y';
$arParams['HIDE_FORM_AFTER_SEND']       = $arParams['HIDE_FORM_AFTER_SEND'] === 'Y';
$arParams['SHOW_CSS_MODAL_AFTER_SEND']  = $arParams['SHOW_CSS_MODAL_AFTER_SEND'] === 'Y';
$arParams['INCLUDE_CSSMODAL']           = ($arParams['INCLUDE_CSSMODAL'] == 'Y') ? 'cssmodal' : ($arParams['INCLUDE_CSSMODAL'] == 'N') ? false : $arParams['INCLUDE_CSSMODAL'];
$arParams['MODAL_WIDTH']                = intval($arParams['MODAL_WIDTH']);
$arParams['MODAL_BUTTON_CLASS']         = (trim($arParams['MODAL_BUTTON_CLASS']) ? trim($arParams['MODAL_BUTTON_CLASS']) : 'uk-button');
$arParams['MODAL_BUTTON_HTML']          = htmlspecialcharsback(trim($arParams['MODAL_BUTTON_HTML']));
$arParams['MODAL_HEADER_HTML']          = htmlspecialcharsback(trim($arParams['MODAL_HEADER_HTML']));
$arParams['MODAL_FOOTER_HTML']          = htmlspecialcharsback(trim($arParams['MODAL_FOOTER_HTML']));

$arParams['INCLUDE_TOOLTIPSTER']        = $arParams['INCLUDE_TOOLTIPSTER'] == 'Y';
$arParams['TOOLTIPSTER_OPTIONS']        = (array)$arParams['TOOLTIPSTER_OPTIONS'];
foreach($arParams['TOOLTIPSTER_OPTIONS'] as $k => $v)
{
	if($v === '')
		unset($arParams['TOOLTIPSTER_OPTIONS'][ $k ]);
}


//VISUAL_SETTINGS
$arParams['FORM_CLASS']            = $arParams['FORM_CLASS'];
$arParams['HIDE_FIELD_NAME']       = $arParams['HIDE_FIELD_NAME'] === 'Y';
$arParams['HIDE_ASTERISK']         = $arParams['HIDE_ASTERISK'] === 'Y';
$arParams['FORM_AUTOCOMPLETE']     = $arParams['FORM_AUTOCOMPLETE'] === 'Y';
$arParams['FORM_SUBMIT_CLASS']     = trim($arParams['FORM_SUBMIT_CLASS']);
$arParams['FORM_SUBMIT_STYLE']     = trim($arParams['FORM_SUBMIT_STYLE']);
$arParams['UUID_LENGTH']           = $arParams['UUID_LENGTH'] ? intval($arParams['UUID_LENGTH']) : 10;
$arParams['UUID_PREFIX']           = $arParams['UUID_PREFIX'] ? trim($arParams['UUID_PREFIX']) : '';
$arParams['USE_HIDDEN_PROTECTION'] = $arParams['USE_HIDDEN_PROTECTION'] === 'Y';
$arParams['FIELD_SIZE']            = trim($arParams['FIELD_SIZE']);
$arParams['FIELD_NAME_POSITION']   = trim($arParams['FIELD_NAME_POSITION']);
$arParams['TITLE_DISPLAY']         = $arParams['TITLE_DISPLAY'] == 'Y';
$arParams['FORM_TITLE']            = trim($arParams['FORM_TITLE']);
$arParams['FIELD_ERROR_MESS']      = trim($arParams['FIELD_ERROR_MESS']) ? trim($arParams['FIELD_ERROR_MESS']) : GetMessage('MF_ERROR_REQUIRED');
$arParams['FILE_ERROR_MESS']       = trim($arParams['FILE_ERROR_MESS']) ? trim($arParams['FILE_ERROR_MESS']) : GetMessage('MF_ERROR_REQUIRED_FILE');
$arParams['EMAIL_ERROR_MESS']      = trim($arParams['EMAIL_ERROR_MESS']) ? trim($arParams['EMAIL_ERROR_MESS']) : GetMessage('MF_EMAIL_NOT_VALID');

//YM_GOALS_SETTINGS
$arParams['SEND_GOALS']     = false;
$arParams['USE_YM_GOALS']   = $arParams['USE_YM_GOALS'] == 'Y';
$arParams['YM_COUNTER_ID']  = trim($arParams['YM_COUNTER_ID']);
$arParams['YM_TARGET_ID']   = trim($arParams['YM_TARGET_ID']);
$arParams['YM_TARGET_NAME'] = trim($arParams['YM_TARGET_NAME']);

//FILE_SETTINGS
$arParams['UPLOAD_FOLDER']             = empty($arParams['UPLOAD_FOLDER']) ? '/upload/feedback' : rtrim($arParams['UPLOAD_FOLDER'], '/');
$arParams['UPLOAD_DIR']                = $arParams['UPLOAD_FOLDER'];
$arParams['DOWNLOAD_URL']              = $arParams['HTTP_PROTOCOL'] . $_SERVER['HTTP_HOST'] . $arParams['UPLOAD_FOLDER'];
$arParams['UPLOAD_FOLDER']             = $_SERVER['DOCUMENT_ROOT'] . $arParams['UPLOAD_FOLDER'];
$arParams['MAX_FILE_SIZE']             = empty($arParams['MAX_FILE_SIZE']) ? 10000000 : intval($arParams['MAX_FILE_SIZE']) * 1000;
$arParams['DELETE_FILES_AFTER_UPLOAD'] = $arParams['DELETE_FILES_AFTER_UPLOAD'] === 'Y';
$arParams['CHOOSE_FILE_TEXT']          = trim($arParams['CHOOSE_FILE_TEXT']) ? trim($arParams['CHOOSE_FILE_TEXT']) : GetMessage('CHOOSE_FILE_TEXT_VALUE');
if($arParams['DELETE_FILES_AFTER_UPLOAD'] && (trim($arParams['UPLOAD_DIR'], '/') == 'upload' || trim($arParams['UPLOAD_DIR'], '/') == 'bitrix'))
	$arResult['MESSAGE']['WARNING'][] = GetMessage('DELETE_FILES_AFTER_UPLOAD_ERROR', array('#UPLOAD_DIR#' => $arParams['UPLOAD_DIR']));

$arParams['MAX_FILE_UPLOADS'] = ($ini_get['max_file_uploads'] ? $ini_get['max_file_uploads'] : 20);


//SERVICE_MACROS_SETTINGS
$arParams['SUBJECT']    = trim($arParams['SUBJECT']);
$arParams['PAGE_URI']   = trim($arParams['PAGE_URI']) ? trim($arParams['PAGE_URI']) : $arParams['HTTP_PROTOCOL'] . $_SERVER['HTTP_HOST'] . $APPLICATION->GetCurUri();
$arParams['PAGE_URL']   = trim($arParams['PAGE_URL']) ? trim($arParams['PAGE_URL']) : $arParams['HTTP_PROTOCOL'] . $_SERVER['HTTP_HOST'] . $APPLICATION->GetCurPage();
$arParams['DIR_URL']    = trim($arParams['DIR_URL']) ? trim($arParams['DIR_URL']) : $arParams['HTTP_PROTOCOL'] . $_SERVER['HTTP_HOST'] . $APPLICATION->GetCurDir();
$arParams['PAGE_TITLE'] = trim($arParams['PAGE_TITLE']) ? trim($arParams['PAGE_TITLE']) : $APPLICATION->GetTitle();
$arParams['DATETIME']   = trim($arParams['DATETIME']) ? trim($arParams['DATETIME']) : date('d-m-Y H:i:s');


//AGREEMENT_SETTINGS
$arParams['USE_AGREEMENT']   = $arParams['USE_AGREEMENT'] == 'Y';
$arParams['AGREEMENT_TEXT']  = htmlspecialcharsback(trim($arParams['AGREEMENT_TEXT']));
$arParams['AGREEMENT_ERROR'] = htmlspecialcharsback(trim($arParams['AGREEMENT_ERROR']));
$arParams['AGREEMENT_LINK']  = htmlspecialcharsback(trim($arParams['AGREEMENT_LINK']));

$arParams['INCLUDE_JQUERY']      = trim($arParams['INCLUDE_JQUERY']);
$arParams['INCLUDE_PLACEHOLDER'] = $arParams['INCLUDE_PLACEHOLDER'] === 'Y';
$arParams['INCLUDE_STEPPER'] = false;

//SERVER_SETTINGS
$arParams['SERVER_VARS'] = (array)$arParams['SERVER_VARS'];
foreach($arParams['SERVER_VARS'] as $k => $v){
	if($v === '')
		unset($arParams['SERVER_VARS'][ $k ]);
}

$arParams['REQUEST_VARS'] = (array)$arParams['REQUEST_VARS'];
foreach($arParams['REQUEST_VARS'] as $k => $v){
	if($v === '')
		unset($arParams['REQUEST_VARS'][ $k ]);
}

$arResult['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET';
$file_list                  = '';
$arFieldsCodeName           = $arFiles = $arFields = array();
$mime_boundary              = "------------{" . md5(time()) . "}";
$obApiFeedback              = new CApiFeedback();



if($arResult['REQUEST_METHOD'] && (strlen($_REQUEST['API_MF_SUBMIT_BUTTON']) || strlen($_REQUEST['API_MF_HIDDEN_SUBMIT']))
	 && $arResult['DEFAULT_PARAMS']['UNIQUE_FORM_ID'] == $_REQUEST['UNIQUE_FORM_ID'])
{
	if(check_bitrix_sessid())
	{

		//CSS ANTIBOT
		if($arParams['USE_HIDDEN_PROTECTION'])
		{
			if(isset($_REQUEST['ANTIBOT']) && is_array($_REQUEST['ANTIBOT']))
			{
				foreach($_REQUEST['ANTIBOT'] as $k => $v)
					if(empty($v))
						unset($_REQUEST['ANTIBOT'][ $k ]);
			}

			if($_REQUEST['ANTIBOT'] || !isset($_REQUEST['ANTIBOT']))
				return;
		}
		//\\CSS ANTIBOT

		//file validation and replacement
		if(is_array($_FILES['UPLOAD_FILES']['name']))
		{
			$bFileNotIsset = false;

			foreach($_FILES['UPLOAD_FILES']['name'] as $k => $v)
			{
				if(!empty($v))
				{
					$arrFile = array(
						'name'     => $obApiFeedback->FakeTranslit($_FILES['UPLOAD_FILES']['name'][ $k ]),
						'type'     => $_FILES['UPLOAD_FILES']['type'][ $k ],
						'tmp_name' => $_FILES['UPLOAD_FILES']['tmp_name'][ $k ],
						'error'    => $_FILES['UPLOAD_FILES']['error'][ $k ],
						'size'     => $_FILES['UPLOAD_FILES']['size'][ $k ],
						'desc'     => !empty($arParams['FILE_DESCRIPTION'][ $k ]) ? $arParams['FILE_DESCRIPTION'][ $k ] . ': ' : '',
						'del'      => '',
					);

					$destination = $arParams['UPLOAD_FOLDER'] . '/' . $arrFile['name'];

					if(!is_dir($arParams['UPLOAD_FOLDER']))
						if(!mkdir($arParams['UPLOAD_FOLDER'], 0755, true))
							$arResult['MESSAGE']['DANGER']['UPLOAD']['MESS'][] = GetMessage('UPLOAD_FOLDER_MAKE_ERROR');

					if(is_dir($arParams['UPLOAD_FOLDER']))
					{
						if(@is_uploaded_file($arrFile['tmp_name']))
						{
							$res = CFile::CheckFile($arrFile, $arParams['MAX_FILE_SIZE'], false, $arParams['FILE_EXTENSIONS']);
							if(strlen($res) > 0)
								$arResult['MESSAGE']['DANGER']['UPLOAD']['FILES'][ $k ] = $res;
							else
							{
								if(@move_uploaded_file($arrFile['tmp_name'], $destination))
								{
									$arFiles[ $k ]                = $arrFile;
									$arFiles[ $k ]['tmp_name']    = $destination;
									$arFiles[ $k ]['description'] = trim($arParams['FILE_DESCRIPTION'][ $k ]);

									if(is_file($destination) && ($arParams['SEND_ATTACHMENT'] || $arParams['DELETE_FILES_AFTER_UPLOAD']))
									{
										$file_list .= "--{$mime_boundary}\n";
										$fp   = @fopen($destination, "rb");
										$data = @fread($fp, filesize($destination));
										@fclose($fp);
										$data = chunk_split(base64_encode($data));
										$file_list .= "Content-Type: application/octet-stream;\n name=\"" . $arrFile['name'] . "\"\n";
										$file_list .= "Content-Transfer-Encoding: base64\n";
										$file_list .= "Content-Description: " . $arrFile['name'] . "\n";
										$file_list .= "Content-Disposition: attachment;\n filename=\"" . $arrFile['desc'] . $arrFile['name'] . "\"; size=" . filesize($destination) . ";\n\n";
										$file_list .= $data;
									}
									elseif(!$arParams['DELETE_FILES_AFTER_UPLOAD'])
										$file_list .= $arrFile['desc'] . '<a href="' . $arParams['DOWNLOAD_URL'] . '/' . $arrFile['name'] . '">' . $arParams['DOWNLOAD_URL'] . '/' . $arrFile['name'] . '</a><br>';
									else
										$file_list = '';
								}
								else
									$arResult['MESSAGE']['DANGER']['UPLOAD']['FILES'][ $k ] = GetMessage('MOVE_UPLOADED_FILE_ERROR');
							}
						}
						else
							$arResult['MESSAGE']['DANGER']['UPLOAD']['FILES'][ $k ] = GetMessage('UPLOADED_FILE_ERROR');
					}
					else
						$arResult['MESSAGE']['DANGER']['UPLOAD']['MESS'][] = GetMessage('IS_DIR_ERROR');
				}
				else
					$bFileNotIsset = true;
			}
		}
		//\\file validation and replacement


		//Validate required fields
		if(empty($arParams['REQUIRED_FIELDS']) || !in_array('NONE', $arParams['REQUIRED_FIELDS']))
		{
			$arParams['REQUIRED_FIELDS'] = empty($arParams['REQUIRED_FIELDS']) ? $arParams['DISPLAY_FIELDS'] : $arParams['REQUIRED_FIELDS'];

			foreach($arParams['REQUIRED_FIELDS'] as $FIELD)
			{
				$message_field = !empty($arParams[ 'USER_' . $FIELD ]) ? $arParams[ 'USER_' . $FIELD ] : GetMessage('MFP_' . $FIELD);

				if($arParams['BRANCH_ACTIVE'] && $FIELD == 'BRANCH' && $arParams['BRANCH_BLOCK_NAME'])
					$message_field = str_replace(':','',$arParams['BRANCH_BLOCK_NAME']);


				if((empty($arParams['REQUIRED_FIELDS']) || in_array($FIELD, $arParams['REQUIRED_FIELDS'])) && strlen($_REQUEST[ strtolower($FIELD) ]) == 0)
					$arResult['MESSAGE']['DANGER'][ $FIELD ] = str_replace('#FIELD_NAME#', $message_field, $arParams['FIELD_ERROR_MESS']);
			}
			unset($FIELD);
		}

		//Form constructor fileds
		$arCustomFields = array();
		if(!empty($arParams['CUSTOM_FIELDS']))
		{
			foreach($arParams['CUSTOM_FIELDS'] as $k => $fv)
			{
				$arExplodeFields = explode('@', $fv);

				$inputName = $arExplodeFields[0];
				$inputType = $arExplodeFields[1]; //textarea
				unset($arExplodeFields[0]);

				$attrName  = '';
				$attrValue = '';
				$attrType  = '';
				foreach($arExplodeFields as $attr)
				{
					if(substr($attr, 0, 5) == "name=")
						$attrName = str_replace('name=', '', $attr);

					if(substr($attr, 0, 5) == "type=")
						$attrType = str_replace('type=', '', $attr);
				}

				//+ if field is delimiter
				if(!$attrName)
					continue;

				$arFieldsCodeName[ $attrName ] = $inputName;

				//START Validate $_REQUEST values
				if(isset($_REQUEST[ $attrName ]))
					$attrValue = $_REQUEST[ $attrName ];

				if(is_array($attrValue))
				{
					foreach($attrValue as $attrKey => $attrVal)
					{
						if($attrVal == '')
							unset($attrValue[ $attrKey ]);
						else
							$attrValue[ $attrKey ] = htmlspecialcharsEx(trim($attrVal));
					}
					unset($attrVal);
				}
				else
					$attrValue = htmlspecialcharsEx(trim($attrValue));
				//END Validate $_REQUEST values


				//For active values in template elements
				$arResultCustom[ $attrName ] = $attrValue;

				//For mail send in include.php
				$arCustomFields[ $attrName ] = ($inputType == 'textarea') ? nl2br($attrValue) : $attrValue;

				//Check required
				if(in_array('required', $arExplodeFields) && $attrType != 'file' && empty($attrValue))
					$arResult['MESSAGE']['DANGER'][ $k ] = str_replace('#FIELD_NAME#', $inputName, $arParams['FIELD_ERROR_MESS']);

				//Check e-mail
				if(ToLower($attrName) == 'email')
				{
					$arParams['USER_EMAIL'] = trim($attrValue);

					if(in_array('required', $arExplodeFields))
					{
						if(empty($attrValue))
							$arResult['MESSAGE']['DANGER'][ $k ] = str_replace('#FIELD_NAME#', $inputName, $arParams['FIELD_ERROR_MESS']);
						elseif(!check_email($arParams['USER_EMAIL']))
							$arResult['MESSAGE']['DANGER'][ $k ] = $arParams['EMAIL_ERROR_MESS'];
					}
				}
			}

			unset($fv);
			unset($arExplodeFields);
		}

		//Validate attachment files and set error if empty
		if($bFileNotIsset && $arParams['SET_ATTACHMENT_REQUIRED'])
			$arResult['MESSAGE']['DANGER']['UPLOAD']['MESS'][] = $arParams['FILE_ERROR_MESS'];

		//Validate e-mail
		if(!empty($_REQUEST['author_email']) && !check_email($_REQUEST['author_email']))
			$arResult['MESSAGE']['DANGER']['AUTHOR_EMAIL'] = $arParams['EMAIL_ERROR_MESS'];

		//Validate captha
		if($arParams['USE_CAPTCHA'])
		{
			include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/captcha.php');
			$captcha_code = $_REQUEST['captcha_sid'];
			$captcha_word = $_REQUEST['captcha_word'];
			$cpt          = new CCaptcha();
			$captchaPass  = COption::GetOptionString('main', 'captcha_password', '');
			if(strlen($captcha_word) > 0 && strlen($captcha_code) > 0)
			{
				if(!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
					$arResult['MESSAGE']['DANGER']['BITRIX_CAPTCHA'] = GetMessage('MF_CAPTCHA_WRONG');
			}
			else
				$arResult['MESSAGE']['DANGER']['BITRIX_CAPTCHA'] = GetMessage('MF_CAPTHCA_EMPTY');
		}


		//USER EMAIL ONLY HERE
		$arParams['USER_EMAIL'] = $arParams['USER_EMAIL'] ? $arParams['USER_EMAIL'] : htmlspecialcharsEx(trim($_REQUEST['author_email']));

		if(empty($arResult['MESSAGE']))
		{
			$arFields = Array(
				'AUTHOR_FIO'             => htmlspecialcharsEx(trim($_REQUEST['author_fio'])),
				'AUTHOR_NAME'            => htmlspecialcharsEx(trim($_REQUEST['author_name'])),
				'AUTHOR_LAST_NAME'       => htmlspecialcharsEx(trim($_REQUEST['author_last_name'])),
				'AUTHOR_SECOND_NAME'     => htmlspecialcharsEx(trim($_REQUEST['author_second_name'])),
				'AUTHOR_EMAIL'           => htmlspecialcharsEx(trim($_REQUEST['author_email'])),
				'AUTHOR_PERSONAL_MOBILE' => htmlspecialcharsEx(trim($_REQUEST['author_personal_mobile'])),
				'AUTHOR_WWW'             => htmlspecialcharsEx(trim($_REQUEST['author_www'])),
				'AUTHOR_WORK_COMPANY'    => htmlspecialcharsEx(trim($_REQUEST['author_work_company'])),
				'AUTHOR_POSITION'        => htmlspecialcharsEx(trim($_REQUEST['author_position'])),
				'AUTHOR_PROFESSION'      => htmlspecialcharsEx(trim($_REQUEST['author_profession'])),
				'AUTHOR_STATE'           => htmlspecialcharsEx(trim($_REQUEST['author_state'])),
				'AUTHOR_CITY'            => htmlspecialcharsEx(trim($_REQUEST['author_city'])),
				'AUTHOR_WORK_CITY'       => htmlspecialcharsEx(trim($_REQUEST['author_work_city'])),
				'AUTHOR_STREET'          => htmlspecialcharsEx(trim($_REQUEST['author_street'])),
				'AUTHOR_ADRESS'          => htmlspecialcharsEx(trim($_REQUEST['author_adress'])),
				'AUTHOR_PERSONAL_PHONE'  => htmlspecialcharsEx(trim($_REQUEST['author_personal_phone'])),
				'AUTHOR_WORK_PHONE'      => htmlspecialcharsEx(trim($_REQUEST['author_work_phone'])),
				'AUTHOR_FAX'             => htmlspecialcharsEx(trim($_REQUEST['author_fax'])),
				'AUTHOR_MAILBOX'         => htmlspecialcharsEx(trim($_REQUEST['author_mailbox'])),
				'AUTHOR_WORK_MAILBOX'    => htmlspecialcharsEx(trim($_REQUEST['author_work_mailbox'])),
				'AUTHOR_SKYPE'           => htmlspecialcharsEx(trim($_REQUEST['author_skype'])),
				'AUTHOR_ICQ'             => htmlspecialcharsEx(trim($_REQUEST['author_icq'])),
				'AUTHOR_WORK_WWW'        => htmlspecialcharsEx(trim($_REQUEST['author_work_www'])),
				'AUTHOR_MESSAGE_THEME'   => !empty($_REQUEST['author_message_theme']) ? htmlspecialcharsEx(trim($_REQUEST['author_message_theme'])) : GetMessage('NO_MESSAGE_THEME'),
				'AUTHOR_MESSAGE'         => nl2br(htmlspecialcharsEx(trim($_REQUEST['author_message']))),
				'AUTHOR_NOTES'           => nl2br(htmlspecialcharsEx(trim($_REQUEST['author_notes']))),
				'FORM_ID'                => trim($arParams['UNIQUE_FORM_ID']),
				'DEFAULT_EMAIL_FROM'     => trim($arParams['USER_EMAIL']),
				'EMAIL_TO'               => trim($arParams['EMAIL_TO']),
				'BCC'                    => trim($arParams['BCC']),
				'SUBJECT'                => trim($arParams['SUBJECT']),
				'PAGE_URI'               => trim($arParams['PAGE_URI']),
				'PAGE_URL'               => trim($arParams['PAGE_URL']),
				'DIR_URL'                => trim($arParams['DIR_URL']),
				'PAGE_TITLE'             => trim($arParams['PAGE_TITLE']),
				'DATETIME'               => trim($arParams['DATETIME']),
				'FORM_TITLE'             => trim($arParams['FORM_TITLE']),
				'HTTP_HOST'              => trim($_SERVER['HTTP_HOST']),
				'BRANCH_NAME'            => '',
				'MSG_PRIORITY'           => '',
				'IP'                     => trim($_SERVER['REMOTE_ADDR']),
				'HTTP_USER_AGENT'        => trim($_SERVER['HTTP_USER_AGENT']),
				'AR_FILES'               => $arFiles,
				'FILES'                  => $file_list,
			);

			$arFieldsCodeNameTMP1 = $arFieldsCodeNameTMP2 = array();
			foreach($arParams['DISPLAY_FIELDS'] as $FIELD_K)
			{
				if($FIELD_K == 'AUTHOR_MESSAGE' || $FIELD_K == 'AUTHOR_NOTES')
				{
					$arFieldsCodeNameTMP2[ $FIELD_K ] = GetMessage('MFP_' . $FIELD_K);
					continue;
				}
				$arFieldsCodeNameTMP1[ $FIELD_K ] = GetMessage('MFP_' . $FIELD_K);
			}
			$arFieldsCodeName = array_merge($arFieldsCodeNameTMP1, $arFieldsCodeName, $arFieldsCodeNameTMP2);
			unset($arFieldsCodeNameTMP1);
			unset($arFieldsCodeNameTMP2);

			if($arParams['BRANCH_ACTIVE'] && intval($_REQUEST['branch']) >= 0)
			{
				$arEmails = explode('###', $arParams['BRANCH_FIELDS'][ intval($_REQUEST['branch']) ]);

				$arFields['BRANCH_NAME'] = trim($arEmails[0]);
				unset($arEmails[0]);

				if(!empty($arEmails) && is_array($arEmails))
				{
					$arFields['EMAIL_TO'] = implode(',', $arEmails);
				}

				if($arParams['MSG_PRIORITY'] && !empty($_REQUEST['msg_priority']))
					$arFields['MSG_PRIORITY'] = htmlspecialcharsEx($_REQUEST['msg_priority']);
			}

			//If no errors try send message
			//For Admin
			if(!empty($arParams['ADMIN_EVENT_MESSAGE_ID']))
			{
				foreach($arParams['ADMIN_EVENT_MESSAGE_ID'] as $v)
				{
					if(IntVal($v) > 0)
					{
						if(!$obApiFeedback->Send(
							$arParams['EVENT_NAME'],
							SITE_ID,
							array_merge($arCustomFields, $arFields),
							'N',
							IntVal($v),
							false,
							$mime_boundary,
							$arFieldsCodeName,
							$arParams)
						)
						{
							if($obApiFeedback->LAST_ERROR)
								$arResult['MESSAGE']['WARNING'][] = implode('<br>', $obApiFeedback->LAST_ERROR);
							else
								$arResult['MESSAGE']['WARNING'][] = GetMessage('SEND_MESSAGE_ERROR_ADMIN');
						}
					}
					else
						$arResult['MESSAGE']['WARNING'][] = GetMessage('SEND_MESSAGE_ERROR_ADMIN');
				}
			}
			else
				$arResult['MESSAGE']['WARNING'][] = GetMessage('NO_SET_ADMIN_MESSAGE_TEMPLATE');

			//For USER
			if(!empty($arParams['USER_EVENT_MESSAGE_ID']) && !empty($arParams['USER_EMAIL']))
			{
				foreach($arParams['USER_EVENT_MESSAGE_ID'] as $v)
				{
					if(IntVal($v) > 0)
					{
						if(!$obApiFeedback->Send(
							$arParams['EVENT_NAME'],
							SITE_ID,
							array_merge($arCustomFields, $arFields),
							'N',
							IntVal($v),
							true,
							$mime_boundary,
							$arFieldsCodeName,
							$arParams)
						)
						{
							if($obApiFeedback->LAST_ERROR)
								$arResult['MESSAGE']['WARNING'][] = implode('<br>', $obApiFeedback->LAST_ERROR);
							else
								$arResult['MESSAGE']['WARNING'][] = GetMessage('SEND_MESSAGE_ERROR_USER');
						}
					}
					else
						$arResult['MESSAGE']['WARNING'][] = GetMessage('SEND_MESSAGE_ERROR_USER');
				}
			}

			if(empty($arResult['MESSAGE']))
			{
				if(!$arParams['REDIRECT_PAGE'])
					$_SESSION['API_FEEDBACK']['SUCCESS'] = $arParams['UNIQUE_FORM_ID'];
				else
					$arResult["REDIRECT_URL"] = $arParams['REDIRECT_PAGE'];
				/*
				if($arParams['REDIRECT_PAGE'])
					LocalRedirect($arParams['REDIRECT_PAGE']);
				else
					LocalRedirect($APPLICATION->GetCurPageParam('success=' . $arParams['UNIQUE_FORM_ID'],array('success')));
				die();
				*/
			}
		}

		$arResult['AUTHOR_FIO']             = htmlspecialcharsEx($_REQUEST['author_fio']);
		$arResult['AUTHOR_NAME']            = htmlspecialcharsEx($_REQUEST['author_name']);
		$arResult['AUTHOR_LAST_NAME']       = htmlspecialcharsEx($_REQUEST['author_last_name']);
		$arResult['AUTHOR_SECOND_NAME']     = htmlspecialcharsEx($_REQUEST['author_second_name']);
		$arResult['AUTHOR_EMAIL']           = htmlspecialcharsEx($_REQUEST['author_email']);
		$arResult['AUTHOR_PERSONAL_MOBILE'] = htmlspecialcharsEx($_REQUEST['author_personal_mobile']);
		$arResult['AUTHOR_WWW']             = htmlspecialcharsEx($_REQUEST['author_www']);
		$arResult['AUTHOR_WORK_COMPANY']    = htmlspecialcharsEx($_REQUEST['author_work_company']);
		$arResult['AUTHOR_POSITION']        = htmlspecialcharsEx($_REQUEST['author_position']);
		$arResult['AUTHOR_PROFESSION']      = htmlspecialcharsEx($_REQUEST['author_profession']);
		$arResult['AUTHOR_STATE']           = htmlspecialcharsEx($_REQUEST['author_state']);
		$arResult['AUTHOR_CITY']            = htmlspecialcharsEx($_REQUEST['author_city']);
		$arResult['AUTHOR_WORK_CITY']       = htmlspecialcharsEx($_REQUEST['author_work_city']);
		$arResult['AUTHOR_STREET']          = htmlspecialcharsEx($_REQUEST['author_street']);
		$arResult['AUTHOR_ADRESS']          = htmlspecialcharsEx($_REQUEST['author_adress']);
		$arResult['AUTHOR_PERSONAL_PHONE']  = htmlspecialcharsEx($_REQUEST['author_personal_phone']);
		$arResult['AUTHOR_WORK_PHONE']      = htmlspecialcharsEx($_REQUEST['author_work_phone']);
		$arResult['AUTHOR_FAX']             = htmlspecialcharsEx($_REQUEST['author_fax']);
		$arResult['AUTHOR_MAILBOX']         = htmlspecialcharsEx($_REQUEST['author_mailbox']);
		$arResult['AUTHOR_WORK_MAILBOX']    = htmlspecialcharsEx($_REQUEST['author_work_mailbox']);
		$arResult['AUTHOR_SKYPE']           = htmlspecialcharsEx($_REQUEST['author_skype']);
		$arResult['AUTHOR_ICQ']             = htmlspecialcharsEx($_REQUEST['author_icq']);
		$arResult['AUTHOR_WORK_WWW']        = htmlspecialcharsEx($_REQUEST['author_work_www']);
		$arResult['AUTHOR_MESSAGE_THEME']   = htmlspecialcharsEx($_REQUEST['author_message_theme']);
		$arResult['AUTHOR_MESSAGE']         = htmlspecialcharsEx($_REQUEST['author_message']);
		$arResult['AUTHOR_NOTES']           = htmlspecialcharsEx($_REQUEST['author_notes']);
		$arResult['API_MF_AGREEMENT']       = htmlspecialcharsEx($_REQUEST['API_MF_AGREEMENT']);
		$arResult['BRANCH_NAME']            = htmlspecialcharsEx($_REQUEST['branch']);
		$arResult['MSG_PRIORITY']           = htmlspecialcharsEx($_REQUEST['msg_priority']);

		if(!empty($arResultCustom))
			$arResult = array_merge($arResult, $arResultCustom);
	}
	else
		$arResult['MESSAGE']['WARNING'][] = GetMessage('MF_SESS_EXP');



	//v.2.8.0
	if($_SESSION['API_FEEDBACK']['SUCCESS'] && $_SESSION['API_FEEDBACK']['SUCCESS'] == $arParams['UNIQUE_FORM_ID'])
	{
		$arParams['SEND_GOALS'] = true;

		if($arAllFormFields = array_merge($arCustomFields, $arFields))
		{
			foreach($arAllFormFields as $field => $val)
			{

				$arParams['OK_TEXT_AFTER'] = str_replace('#'.$field.'#', $val, $arParams['OK_TEXT_AFTER']);
				$arParams['OK_TEXT']       = str_replace('#'.$field.'#', $val, $arParams['OK_TEXT']);

				//Clear POST data
				$arResult[ $field ] = '';
			}
		}


		$TICKET_ID                 = intval($_SESSION['API_FEEDBACK']['TICKET_ID']) ? $_SESSION['API_FEEDBACK']['TICKET_ID'] : '';
		$arParams['OK_TEXT_AFTER'] = str_replace('#TICKET_ID#', $TICKET_ID, $arParams['OK_TEXT_AFTER']);
		$arParams['OK_TEXT']       = str_replace('#TICKET_ID#', $TICKET_ID, $arParams['OK_TEXT']);


		$arResult['MESSAGE']['SUCCESS'][]      = $arParams['OK_TEXT'];
		$_SESSION['API_FEEDBACK']['SUCCESS']   = false;
		$_SESSION['API_FEEDBACK']['TICKET_ID'] = false;


		if($arParams['DELETE_FILES_AFTER_UPLOAD'] && (trim($arParams['UPLOAD_DIR'], '/') != 'upload' && trim($arParams['UPLOAD_DIR'], '/') != 'bitrix'))
		{
			$dir = $arParams['UPLOAD_FOLDER'];
			if(is_dir($dir))
			{
				$objects = scandir($dir);
				foreach($objects as $object)
				{
					if($object != "." && $object != "..")
					{
						if(filetype($dir . "/" . $object) != "dir")
							unlink($dir . "/" . $object);
					}
				}
				reset($objects);
				//rmdir($dir);
			}
		}
	}
}
else
{
	//Default values for input, select
	if($arParams['CUSTOM_FIELDS'])
	{

		$arUser = array();
		if($USER->IsAuthorized() && !$USER->IsAdmin())
			$arUser = CUser::GetByID(intval($USER->GetID()))->Fetch();

		foreach($arParams['CUSTOM_FIELDS'] as $key => $val)
		{
			$arExplodeFields = explode('@', $val);

			if(!empty($arExplodeFields) && is_array($arExplodeFields))
			{
				if($arExplodeFields[1] == 'input')
				{
					$type = trim(end(explode('=', $arExplodeFields[2])));
					$name = trim(end(explode('=', $arExplodeFields[3])));

					if($type == 'stepper')
						$arParams['INCLUDE_STEPPER'] = true;

					//For text, hidden
					if($type == 'text' || $type == 'hidden' || $type == 'radio' || $type == 'checkbox' || $type == 'stepper' || $type == 'password')
					{
						foreach($arExplodeFields as $key2 => $val2)
						{
							if(substr($val2, 0, 6) == "value=" && $type != 'checkbox')
								$arResult[ $name ] = substr($val2, 6);


							if(substr($val2, 0, 8) == "checked=")
							{
								$values            = ($type == 'checkbox' || $type == 'select' ? explode("#", substr($val2, 8)) : substr($val2, 8));
								$arResult[ $name ] = $values;

								//For one checkbox
								if($type == 'checkbox' && strpos($val, 'value=') !== false)
									$arResult[ $name ] = substr($val2, 8);
							}
						}
					}

					if($type == 'text' && !$arResult[ $name ] && $arUser[ $name ])
					{
						$arResult[ $name ] = $arUser[ $name ];
					}

				}
				elseif($arExplodeFields[1] == 'select')
				{
					$name = trim(end(explode('=', $arExplodeFields[2])));
					foreach($arExplodeFields as $key2 => $val2)
					{
						if(substr($val2, 0, 8) == "checked=")
						{
							$values            = explode("#", substr($val2, 8));
							$arResult[ $name ] = $values;
						}
					}
				}
			}
		}
	}

}


if($arParams['USE_CAPTCHA'])
	$arResult['capCode'] = htmlspecialchars($APPLICATION->CaptchaGetCode());

$arResult['UUID'] = CApiFeedback::GetUUID($arParams['UUID_LENGTH'], $arParams['UUID_PREFIX']);

//CSS ANTIBOT
$arResult['ANTIBOT'] = $_REQUEST['ANTIBOT'];


//YM_GOALS
$arResult['GOALS_SETTINGS'] = '';
if($arParams['SEND_GOALS'])
{
	$YM_COUNTER_ID  = $arParams['YM_COUNTER_ID'];
	$YM_TARGET_ID   = $arParams['YM_TARGET_ID'];
	$YM_TARGET_NAME = $arParams['YM_TARGET_NAME'];

	if($arParams['USE_YM_GOALS'] && $YM_COUNTER_ID && $YM_TARGET_NAME)
	{
		$arResult['GOALS_SETTINGS'] = <<<EOT
	(function (d, w, c) {
		(w[c] = w[c] || []).push(function() {
			try {
				var yaCounter{$YM_TARGET_ID} = new Ya.Metrika({
					id:{$YM_COUNTER_ID},
				});
				
				yaCounter{$YM_TARGET_ID}.reachGoal('{$YM_TARGET_NAME}');
			} catch(e) {
			 console.log(e,'yaCounter{$YM_TARGET_ID}');
			}
		});

		var n = d.getElementsByTagName("script")[0],
			 s = d.createElement("script"),
			 f = function () { n.parentNode.insertBefore(s, n); };
		s.type = "text/javascript";
		s.async = true;
		s.src = "https://mc.yandex.ru/metrika/watch.js";

		if (w.opera == "[object Opera]") {
			d.addEventListener("DOMContentLoaded", f, false);
		} else { f(); }
	})(document, window, "yandex_metrika_callbacks");
EOT;
	}
}


//CSS SETTINGS
$CSS_FORM_ID              = '#API-MF-' . trim($arParams['UNIQUE_FORM_ID']) . ' ';
$CSS_MODAL_ID             = '#API-MF-MODAL-' . ToUpper($arParams['UNIQUE_FORM_ID']);

$arResult['CSS_SETTINGS'] = '<style type="text/css">' . "\n";

if($arParams['FORM_LABEL_TEXT_ALIGN'])
{
	$arResult['CSS_SETTINGS'] .= $CSS_FORM_ID . '.uk-form-label{text-align:' . (trim($arParams['FORM_LABEL_TEXT_ALIGN']) ? trim($arParams['FORM_LABEL_TEXT_ALIGN']) : 'left') . '}' . "\n";
}

if($arParams['FORM_LABEL_WIDTH'])
{
	$FORM_LABEL_WIDTH = ((int)$arParams['FORM_LABEL_WIDTH'] > 0) ? (int)$arParams['FORM_LABEL_WIDTH'] : 200;
	$arResult['CSS_SETTINGS'] .= '@media (min-width:960px){' . "\n";
	$arResult['CSS_SETTINGS'] .= "\t" . $CSS_FORM_ID . '.uk-form-horizontal .uk-form-label{width:' . $FORM_LABEL_WIDTH . 'px}' . "\n";
	$arResult['CSS_SETTINGS'] .= "\t" . $CSS_FORM_ID . '.uk-form-horizontal .uk-form-controls{margin-left:' . ($FORM_LABEL_WIDTH + 15) . 'px}' . "\n";
	$arResult['CSS_SETTINGS'] .= '}' . "\n";
}

$FORM_FIELD_WIDTH = trim($arParams['FORM_FIELD_WIDTH']);
if($FORM_FIELD_WIDTH)
{
	$arResult['CSS_SETTINGS'] .= <<<EOT
		$CSS_FORM_ID {width:$FORM_FIELD_WIDTH;}
		$CSS_FORM_ID .uk-form-controls{width:100%;}
		$CSS_FORM_ID input[type=text],
		$CSS_FORM_ID select,
		$CSS_FORM_ID textarea{width:100%;}
EOT;
}

if($FIELD_BOX_SHADOW_ACTIVE = $arParams['FIELD_BOX_SHADOW_ACTIVE'])
{
	$arResult['CSS_SETTINGS'] .= <<<EOT
		$CSS_FORM_ID input[type=text]:focus,
		$CSS_FORM_ID select:focus,
		$CSS_FORM_ID textarea:focus{
			$FIELD_BOX_SHADOW_ACTIVE
		}
EOT;
}

if($FIELD_BORDER_ACTIVE = $arParams['FIELD_BORDER_ACTIVE'])
{
	$arResult['CSS_SETTINGS'] .= <<<EOT
		$CSS_FORM_ID .uk-form input:not([type]):focus,
		$CSS_FORM_ID .uk-form input[type=text]:focus,
		$CSS_FORM_ID .uk-form select:focus,
		$CSS_FORM_ID .uk-form textarea:focus{border:$FIELD_BORDER_ACTIVE;}
EOT;
}

if($arParams['MODAL_WIDTH'])
{
	if($arParams['INCLUDE_CSSMODAL'] == 'uikit2')
		$arResult['CSS_SETTINGS'] .= "\n" . $CSS_MODAL_ID . " .uk-modal-dialog{width:". $arParams['MODAL_WIDTH'] ."px}";
	elseif($arParams['INCLUDE_CSSMODAL'] == 'bootstrap3')
		$arResult['CSS_SETTINGS'] .= "\n" . $CSS_MODAL_ID . ".bootstrap3-modal .modal-dialog{width:". $arParams['MODAL_WIDTH'] ."px}";
	else
	{
		$arResult['CSS_SETTINGS'] .= "\n" . $CSS_MODAL_ID . " .modal--fade .modal-inner, .modal--show .modal-inner{width:". $arParams['MODAL_WIDTH'] ."px; margin-left:-".($arParams['MODAL_WIDTH']/2)."px;}";
		$arResult['CSS_SETTINGS'] .= "\n" . $CSS_MODAL_ID . " .modal--fade .modal-close::after, .modal--show .modal-close::after{margin-right:-".($arParams['MODAL_WIDTH']/2)."px;}";
	}
}

if($arParams['INCLUDE_TOOLTIPSTER'])
{
	$arResult['CSS_SETTINGS'] .= "\n" . $CSS_FORM_ID . " .uk-form-controls{padding-right:25px}";
}


$arResult['CSS_SETTINGS'] .= "\n".'</style>';

if($arResult['CSS_SETTINGS'])
	$APPLICATION->AddHeadString($arResult['CSS_SETTINGS']);
//\\CSS SETTINGS


//////////////////////////////////////////////////////////////////////////////
//                            JS SETTINGS
//////////////////////////////////////////////////////////////////////////////

//bitrix/modules/main/jscore.php
CJSCore::Init();

//JQUERY PLUGINS
if($arParams['INCLUDE_JQUERY'] && $arParams['INCLUDE_JQUERY'] != 'N')
{
	if($arParams['INCLUDE_JQUERY'] == 'Y')
		$arParams['INCLUDE_JQUERY'] = 'jquery';

		CUtil::InitJSCore($arParams['INCLUDE_JQUERY']);
}

if($arParams['INCLUDE_VALIDATION'])
{
	CJSCore::Init(array('api_feedback_validation'));
	//$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/validation/jquery.validation.min.js');
}

if($arParams['INCLUDE_INPUTMASK'])
	$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/inputmask/jquery.inputmask.bundle.min.js');

if($arParams['INCLUDE_AUTOSIZE'])
	$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/autosize/jquery.autosize.min.js');


if($arParams['SHOW_CSS_MODAL_AFTER_SEND'] && !empty($arResult['MESSAGE']['SUCCESS']))
	$arParams['INCLUDE_CSSMODAL'] = 'cssmodal';


if($arParams['INCLUDE_STEPPER'])
{
	$APPLICATION->SetAdditionalCSS('/bitrix/js/api.feedback/stepper/jquery.stepper.min.css');
	$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/stepper/jquery.stepper.min.js');
}

$MODAL_ID   = 'API-MF-MODAL-' . ToUpper($arParams['UNIQUE_FORM_ID']);
if($arParams['INCLUDE_CSSMODAL'])
{
	switch($arParams['INCLUDE_CSSMODAL'])
	{
		case 'uikit2':
		{
			$APPLICATION->SetAdditionalCSS('/bitrix/js/api.feedback/uikit2/uikit.gradient.min.css');
			$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/uikit2/uikit.core.min.js');
			$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/uikit2/uikit.modal.min.js');

			if($arParams['MODAL_BUTTON_HTML'])
				$arParams['MODAL_BUTTON_HTML'] = '<div><a href="#' . $MODAL_ID . '" class="'. $arParams['MODAL_BUTTON_CLASS'] .'" data-uk-modal>' . $arParams['MODAL_BUTTON_HTML'] . '</a></div>';

			$modal_title                   = $arParams['MODAL_HEADER_HTML'] ? '<div class="modal-title">' . $arParams['MODAL_HEADER_HTML'] . '</div>' : '';
			$arParams['MODAL_HEADER_HTML'] = '<div id="' . $MODAL_ID . '" class="uk-modal">
				<div class="uk-modal-dialog">
					<a class="uk-modal-close uk-close" href="">&times;</a>
					<div class="uk-modal-header">' . $modal_title . '</div>
					<div class="modal-content">';


			$modal_footer                  = $arParams['MODAL_FOOTER_HTML'] ? '<div class="uk-modal-footer">' . $arParams['MODAL_FOOTER_HTML'] . '</div>' : '';
			$arParams['MODAL_FOOTER_HTML'] = '</div>' . $modal_footer . '</div></div>';
		}
			break;

		case 'bootstrap3':
		{
			$APPLICATION->SetAdditionalCSS('/bitrix/js/api.feedback/bootstrap3/bootstrap.min.css');
			$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/bootstrap3/bootstrap.transition.js');
			$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/bootstrap3/bootstrap.modal.js');


			if($arParams['MODAL_BUTTON_HTML'])
				$arParams['MODAL_BUTTON_HTML'] = '<div><button type="button" data-toggle="modal" data-target="#' . $MODAL_ID . '" class="'. $arParams['MODAL_BUTTON_CLASS'] .'">' . $arParams['MODAL_BUTTON_HTML'] . '</button></div>';

			$modal_title                   = $arParams['MODAL_HEADER_HTML'] ? '<div class="modal-title">' . $arParams['MODAL_HEADER_HTML'] . '</div>' : '';
			$arParams['MODAL_HEADER_HTML'] = '<div id="' . $MODAL_ID . '" class="modal fade bootstrap3-modal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<a href="" class="close" data-dismiss="modal" aria-label="' . GetMessage('MODAL_CLOSE') . '">&times;</a>
							' . $modal_title . '
						</div>
						<div class="modal-body">';


			$modal_footer                  = $arParams['MODAL_FOOTER_HTML'] ? '<div class="modal-footer">' . $arParams['MODAL_FOOTER_HTML'] . '</div>' : '';
			$arParams['MODAL_FOOTER_HTML'] = '</div>' . $modal_footer . '</div></div></div>';
		}
			break;

		default:
		{
			$APPLICATION->SetAdditionalCSS('/bitrix/js/api.feedback/cssmodal/jquery.cssmodal.css');
			$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/cssmodal/jquery.cssmodal.min.js');

			if($arParams['MODAL_BUTTON_HTML'])
				$arParams['MODAL_BUTTON_HTML'] = '<div><a href="#' . $MODAL_ID . '" class="'. $arParams['MODAL_BUTTON_CLASS'] .'">' . $arParams['MODAL_BUTTON_HTML'] . '</a></div>';

			$modal_title                   = $arParams['MODAL_HEADER_HTML'] ? '<div class="modal-title">' . $arParams['MODAL_HEADER_HTML'] . '</div>' : '';
			$arParams['MODAL_HEADER_HTML'] = '<div class="modal--show" id="' . $MODAL_ID . '" tabindex="-1" role="dialog" aria-labelledby="stackable-label" aria-hidden="true">
				<div class="modal-inner">
					<div class="modal-header">' . $modal_title . '</div>
				<div class="modal-content">';


			$modal_footer                  = $arParams['MODAL_FOOTER_HTML'] ? '<div class="modal-footer">' . $arParams['MODAL_FOOTER_HTML'] . '</div>' : '';
			$arParams['MODAL_FOOTER_HTML'] = '</div>' . $modal_footer . '</div>
				<a href="#!" class="modal-close" title="' . GetMessage('MODAL_CLOSE') . '" data-close="' . GetMessage('MODAL_CLOSE') . '" data-dismiss="modal">&times;</a></div>';
		}
	}
}

/** @deprecated in v2.4.0 use INCLUDE_ICHECK */
if($arParams['INCLUDE_FORM_STYLER'] && !$arParams['INCLUDE_ICHECK'])
{
	$APPLICATION->SetAdditionalCSS('/bitrix/js/api.feedback/formstyler/jquery.formstyler.css');
	$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/formstyler/jquery.formstyler.min.js');
}

if($arParams['INCLUDE_PLACEHOLDER'])
	$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/placeholder/jquery.placeholder.min.js');



$JS_FORM_ID              = '#' . $arParams['UNIQUE_FORM_ID'] . ' ';
$arResult['JS_SETTINGS'] = '';

//iCheck
$arResult['ICHECK_SETTINGS'] = '';
if($arParams['INCLUDE_ICHECK'])
{
	/** @deprecated in v2.4.0 use INCLUDE_ICHECK */
	$arParams['INCLUDE_FORM_STYLER'] = false;
	$ICHECK_THEME                    = $arParams['ICHECK_THEME'] ? trim($arParams['ICHECK_THEME']) : 'flat';
	$ICHECK_THEME_COLOR              = trim($arParams['ICHECK_THEME_COLOR']);

	$ICHECK_CLASS = ($ICHECK_THEME_COLOR ? $ICHECK_THEME . '-' . $ICHECK_THEME_COLOR : $ICHECK_THEME);
	$ICHECK_STYLE = ($ICHECK_THEME_COLOR ? $ICHECK_THEME_COLOR : $ICHECK_THEME);

	$APPLICATION->SetAdditionalCSS('/bitrix/js/api.feedback/icheck/skins/' . $ICHECK_THEME . '/' . $ICHECK_STYLE . '.css');
	$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/icheck/icheck.min.js');

$arResult['ICHECK_SETTINGS'] = <<<EOT

$('$JS_FORM_ID input[type="checkbox"], $JS_FORM_ID input[type="radio"]').iCheck({
 checkboxClass: 'icheckbox_$ICHECK_CLASS',
 radioClass: 'iradio_$ICHECK_CLASS',
 increaseArea: '20%' // optional
});

EOT;

	$arResult['JS_SETTINGS'] .= $arResult['ICHECK_SETTINGS'];
}

$arResult['CHOSEN_SETTINGS'] = '';
if($arParams['INCLUDE_CHOSEN'])
{
	$APPLICATION->SetAdditionalCSS('/bitrix/js/api.feedback/chosen/chosen.min.css');
	$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/chosen/jquery.chosen.min.js');
$arResult['CHOSEN_SETTINGS'] = <<<EOT

var chosenConfig = {
      disable_search_threshold:15,
      allow_single_deselect: true, 
      no_results_text:'Oops, nothing found!', 
      width:"100%", 
      placeholder_text_multiple :'$DEFAULT_OPTION_TEXT',
      placeholder_text_single   :'$DEFAULT_OPTION_TEXT'
 };
 $('$CSS_FORM_ID select').chosen(chosenConfig);

EOT;

	$arResult['JS_SETTINGS'] .= $arResult['CHOSEN_SETTINGS'];
}


//Tooltipster
if($arParams['INCLUDE_TOOLTIPSTER'])
{
	$APPLICATION->SetAdditionalCSS('/bitrix/js/api.feedback/tooltipster/tooltipster.bundle.min.css');
	$APPLICATION->SetAdditionalCSS('/bitrix/js/api.feedback/tooltipster/plugins/sideTip/tooltipster-sideTip.min.css');
	$APPLICATION->SetAdditionalCSS('/bitrix/js/api.feedback/tooltipster/plugins/sideTip/themes/tooltipster-sideTip-punk.min.css');
	$APPLICATION->AddHeadScript('/bitrix/js/api.feedback/tooltipster/tooltipster.bundle.min.js');
}

//\\JS SETTINGS

if($arResult["REDIRECT_URL"])
{
	$arResult['JS_REDIRECT'] = "window.top.location.href = '" . CUtil::JSEscape($arResult["REDIRECT_URL"]) . "';";
	$arResult['JS_SETTINGS'] .= "\n" . $arResult['JS_REDIRECT'];
}


if(($arResult['MESSAGE']['SUCCESS']))
	$arResult['API_MF_AGREEMENT'] = false;


if($_REQUEST['is_api_mf_ajax']=='Y')
{
	if($arResult['DEFAULT_PARAMS']['UNIQUE_FORM_ID'] == $_REQUEST['UNIQUE_FORM_ID'])
		$this->IncludeComponentTemplate();
}
else{
	$this->IncludeComponentTemplate();
}
