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
 */

if(!CModule::IncludeModule('api.feedback'))
	return;

$arParams['USE_CAPTCHA'] = ($arParams['USE_CAPTCHA'] == 'Y' && !$USER->IsAuthorized());
$arParams['HTTP_PROTOCOL']  = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';

$arParams['EVENT_NAME'] = trim($arParams['EVENT_NAME']);
if(strlen($arParams['EVENT_NAME']) <= 0)
	$arParams['EVENT_NAME'] = 'FEEDBACK_FORM';


$arParams['EMAIL_FROM'] = trim(COption::GetOptionString('main', 'email_from',"admin@".$GLOBALS["SERVER_NAME"]));
$arParams['EMAIL_TO'] = trim($arParams['EMAIL_TO']);
if(strlen($arParams['EMAIL_TO']) <= 0)
	$arParams['EMAIL_TO'] = $arParams['EMAIL_FROM'];

$arParams['OK_TEXT'] = htmlspecialcharsback(trim($arParams['OK_TEXT']));
if(empty($arParams['OK_TEXT']))
	$arParams['OK_TEXT'] = GetMessage('MF_OK_MESSAGE');

if(!is_array($arParams['CUSTOM_FIELDS']))
	$arParams['CUSTOM_FIELDS'] = array();
foreach($arParams['CUSTOM_FIELDS'] as $k => $v)
{
	if($v === '')
		unset($arParams['CUSTOM_FIELDS'][$k]);
}


if(!is_array($arParams['ADMIN_EVENT_MESSAGE_ID']))
	$arParams['ADMIN_EVENT_MESSAGE_ID'] = array();
foreach($arParams['ADMIN_EVENT_MESSAGE_ID'] as $k => $v)
{
	if($v === '')
		unset($arParams['ADMIN_EVENT_MESSAGE_ID'][$k]);
}

if(!is_array($arParams['USER_EVENT_MESSAGE_ID']))
	$arParams['USER_EVENT_MESSAGE_ID'] = array();
foreach($arParams['USER_EVENT_MESSAGE_ID'] as $k => $v)
{
	if($v === '')
		unset($arParams['USER_EVENT_MESSAGE_ID'][$k]);
}


if(!is_array($arParams['BRANCH_FIELDS']))
	$arParams['BRANCH_FIELDS'] = array();
foreach($arParams['BRANCH_FIELDS'] as $k => $v)
{
	if($v === '')
		unset($arParams['BRANCH_FIELDS'][$k]);
}


if(!is_array($arParams['FILE_DESCRIPTION']))
	$arParams['FILE_DESCRIPTION'] = array();
foreach($arParams['FILE_DESCRIPTION'] as $k => $v)
{
	if($v === '')
		unset($arParams['FILE_DESCRIPTION'][$k]);
}

$arParams['MSG_PRIORITY']   = $arParams['MSG_PRIORITY'] === 'Y';
$arParams['BRANCH_ACTIVE']  = $arParams['BRANCH_ACTIVE'] === 'Y';

$arParams['UNIQUE_FORM_ID'] = !empty($arParams['UNIQUE_FORM_ID']) ? $arParams['UNIQUE_FORM_ID'] : '123456789';
if(!isset($_REQUEST['UNIQUE_FORM_ID']))
	$_REQUEST['UNIQUE_FORM_ID'] = $arParams['UNIQUE_FORM_ID'];

$arParams['SHOW_FILES']                 = $arParams['SHOW_FILES'] === 'Y';
$arParams['VALIDTE_REQUIRED_FIELDS']    = $arParams['VALIDTE_REQUIRED_FIELDS'] === 'Y';
$arParams['SEND_ATTACHMENT']            = $arParams['SEND_ATTACHMENT'] === 'Y';
$arParams['SET_ATTACHMENT_REQUIRED']    = $arParams['SET_ATTACHMENT_REQUIRED'] === 'Y';
$arParams['SHOW_ATTACHMENT_EXTENSIONS'] = $arParams['SHOW_ATTACHMENT_EXTENSIONS'] === 'Y';
$arParams['INCLUDE_PRETTY_COMMENTS']    = $arParams['INCLUDE_PRETTY_COMMENTS'] === 'Y';
$arParams['INCLUDE_FORM_STYLER']        = $arParams['INCLUDE_FORM_STYLER'] == 'Y';
$arParams['COUNT_INPUT_FILE']           = intval($arParams['COUNT_INPUT_FILE']) > 0 ? $arParams['COUNT_INPUT_FILE'] : 3;
$arParams['SCROLL_TO_FORM_IF_MESSAGES'] = $arParams['SCROLL_TO_FORM_IF_MESSAGES'] === 'Y';
$arParams['SCROLL_TO_FORM_SPEED']       = intval($arParams['SCROLL_TO_FORM_SPEED']) > 0 ? intval($arParams['SCROLL_TO_FORM_SPEED']) : 1000;
$arParams['REPLACE_FIELD_FROM']         = $arParams['REPLACE_FIELD_FROM'] === 'Y';
$arParams['HIDE_FORM_AFTER_SEND']       = $arParams['HIDE_FORM_AFTER_SEND'] === 'Y';
$arParams['SHOW_CSS_MODAL_AFTER_SEND']  = $arParams['SHOW_CSS_MODAL_AFTER_SEND'] === 'Y';

//v1.3.6 - PHP antispam
$arParams['USE_HIDDEN_PROTECTION']      = $arParams['USE_HIDDEN_PROTECTION'] === 'Y';
$arParams['USE_PHP_ANTISPAM']           = $arParams['USE_PHP_ANTISPAM'] === 'Y';
$arParams['PHP_ANTISPAM_LEVEL']         = intval($arParams['PHP_ANTISPAM_LEVEL']) ? intval($arParams['PHP_ANTISPAM_LEVEL']) : 2;


//upload directory
if(!empty($arParams['UPLOAD_FOLDER']) && !preg_match('/^\/[\.\w\/-]+[^\/]$/', $arParams['UPLOAD_FOLDER']))
	$arResult['ERROR_MESSAGE'][] = GetMessage('UPLOAD_FOLDER_WRONG');

$arParams['UPLOAD_FOLDER'] = empty($arParams['UPLOAD_FOLDER']) ? '/upload/feedback' : $arParams['UPLOAD_FOLDER'];
$arParams['UPLOAD_DIR']    = $arParams['UPLOAD_FOLDER'];
$arParams['DOWNLOAD_URL']  = $arParams['HTTP_PROTOCOL'] . $_SERVER['HTTP_HOST'] . $arParams['UPLOAD_FOLDER'];
$arParams['UPLOAD_FOLDER'] = $_SERVER['DOCUMENT_ROOT'] . $arParams['UPLOAD_FOLDER'];
$arParams['MAX_FILE_SIZE'] = empty($arParams['MAX_FILE_SIZE']) ? 10000000 : intval($arParams['MAX_FILE_SIZE']) * 1000;
$arParams['DELETE_FILES_AFTER_UPLOAD'] = $arParams['DELETE_FILES_AFTER_UPLOAD'] === 'Y';

if($arParams['DELETE_FILES_AFTER_UPLOAD'] && ($arParams['UPLOAD_DIR'] =='/upload/' || $arParams['UPLOAD_DIR'] =='/upload'))
	$arResult['ERROR_MESSAGE'][] = GetMessage('DELETE_FILES_AFTER_UPLOAD_ERROR');


$arParams['INCLUDE_JQUERY'] = $arParams['INCLUDE_JQUERY'] === 'Y';
$arParams['INCLUDE_PLACEHOLDER'] = $arParams['INCLUDE_PLACEHOLDER'] === 'Y';

if($arParams['INCLUDE_JQUERY'])
	CUtil::InitJSCore('jquery');

if($arParams['VALIDTE_REQUIRED_FIELDS'])
	$APPLICATION->AddHeadScript($this->__path . '/js/_fn.js');

if($arParams['INCLUDE_PRETTY_COMMENTS'])
	$APPLICATION->AddHeadScript($this->__path . '/js/prettyComments.js');

if($arParams['SHOW_CSS_MODAL_AFTER_SEND'])
{
	$APPLICATION->SetAdditionalCSS($this->__path . '/css/jquery.modal.css');
	$APPLICATION->AddHeadScript($this->__path . '/js/jquery.modal.js');
}

//v1.3.0
if($arParams['INCLUDE_FORM_STYLER'])
{
	$APPLICATION->SetAdditionalCSS($this->__path . '/css/jquery.formstyler.css');
	$APPLICATION->AddHeadScript($this->__path . '/js/jquery.formstyler.min.js');
}

//v1.3.1
if($arParams['INCLUDE_PLACEHOLDER'])
	$APPLICATION->AddHeadScript($this->__path . '/js/jquery.placeholder.js');

$file_list = '';

//v1.3.2 - Fields Codes & Names for #WORK_AREA#
$arFieldsCodeName = array();

// boundary
$semi_rand     = md5(time());
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";


if($_SERVER['REQUEST_METHOD'] == 'POST' && strlen($_REQUEST['submit']) > 0 && empty($_REQUEST['hidden_protection']) && $arParams['UNIQUE_FORM_ID'] == $_REQUEST['UNIQUE_FORM_ID'])
{
	if(check_bitrix_sessid())
	{
		//file validation and replacement
		if(is_array($_FILES['UPLOAD_FILES']['name']) && $arParams['SHOW_FILES'])
		{
			$bFileNotIsset = false;

			foreach($_FILES['UPLOAD_FILES']['name'] as $k => $v)
			{
				if(!empty($v))
				{
					$arrFile = array(
						'name'     => CApiFeedback::FakeTranslit($_FILES['UPLOAD_FILES']['name'][$k]),
						'type'     => $_FILES['UPLOAD_FILES']['type'][$k],
						'tmp_name' => $_FILES['UPLOAD_FILES']['tmp_name'][$k],
						'error'    => $_FILES['UPLOAD_FILES']['error'][$k],
						'size'     => $_FILES['UPLOAD_FILES']['size'][$k],
						'desc'     => !empty($arParams['FILE_DESCRIPTION'][$k]) ? $arParams['FILE_DESCRIPTION'][$k] . ': ' : '',
					);

					$destination = $arParams['UPLOAD_FOLDER'] . '/' . $arrFile['name'];

					if(!is_dir($arParams['UPLOAD_FOLDER']))
						if(!mkdir($arParams['UPLOAD_FOLDER'], 0755, true))
							$arResult['ERROR_MESSAGE'][] = GetMessage('UPLOAD_FOLDER_MAKE_ERROR');

					if(is_dir($arParams['UPLOAD_FOLDER']))
					{
						if(@is_uploaded_file($arrFile['tmp_name']))
						{
							$res = CFile::CheckFile($arrFile, $arParams['MAX_FILE_SIZE'], false, $arParams['FILE_EXTENSIONS']);
							if(strlen($res) > 0)
								$arResult['ERROR_MESSAGE'][] = $res;
							else
							{
								if(@move_uploaded_file($arrFile['tmp_name'], $destination))
								{
									if(is_file($destination) && ($arParams['SEND_ATTACHMENT'] || $arParams['DELETE_FILES_AFTER_UPLOAD']))
									{
										$file_list .= "--{$mime_boundary}\n";
										$fp   = @fopen($destination, "rb");
										$data = @fread($fp, filesize($destination));
										@fclose($fp);
										$data = chunk_split(base64_encode($data));
										$file_list .= "Content-Type: application/octet-stream; name=\"" . $arrFile['name'] . "\"\n" . "Content-Description: " . $arrFile['name'] . "\n" . "Content-Disposition: attachment;\n" . " filename=\"" . $arrFile['desc'] . $arrFile['name'] . "\"; size=" . filesize($destination) . ";\n" . "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
									}
									elseif(!$arParams['DELETE_FILES_AFTER_UPLOAD'])
										$file_list .= $arrFile['desc'] . '<a href="' . $arParams['DOWNLOAD_URL'] . '/' . $arrFile['name'] . '">' . $arParams['DOWNLOAD_URL'] . '/' . $arrFile['name'] . '</a><br>';
									else
										$file_list = '';

									//if(!empty($arParams['USER_EVENT_MESSAGE_ID']))
										//$user_file_list .= $arrFile['desc'] . '<a href="' . $arParams['DOWNLOAD_URL'] . '/' . $arrFile['name'] . '">' . $arParams['DOWNLOAD_URL'] . '/' . $arrFile['name'] . '</a><br>';
								}
								else
									$arResult['ERROR_MESSAGE'][] = GetMessage('MOVE_UPLOADED_FILE_ERROR');
							}
						}
						else
							$arResult['ERROR_MESSAGE'][] = GetMessage('UPLOADED_FILE_ERROR');
					}
					else
						$arResult['ERROR_MESSAGE'][] = GetMessage('IS_DIR_ERROR');
				}
				else
					$bFileNotIsset = true;

			}
		}
		//\\file validation and replacement

		//Validate required fields from parameters
		if(empty($arParams['REQUIRED_FIELDS']) || !in_array('NONE', $arParams['REQUIRED_FIELDS']))
		{
			$arParams['REQUIRED_FIELDS'] = empty($arParams['REQUIRED_FIELDS']) ? $arParams['DISPLAY_FIELDS'] : $arParams['REQUIRED_FIELDS'];

			foreach($arParams['REQUIRED_FIELDS'] as $FIELD)
			{
				$message_field = !empty($arParams['USER_' . $FIELD]) ? $arParams['USER_' . $FIELD] : GetMessage('MF_' . $FIELD);

				if((empty($arParams['REQUIRED_FIELDS']) || in_array($FIELD, $arParams['REQUIRED_FIELDS'])) && strlen($_REQUEST[strtolower($FIELD)]) == 0)
					$arResult['ERROR_MESSAGE'][] = str_replace('#PROPERTY_NAME#', $message_field, GetMessage('MF_ERROR_REQUIRED'));
			}
		}

		//Validate custom fields
		$arCustomFields = array();
		if(!empty($arParams['CUSTOM_FIELDS']))
		{
			$field_val = '';
			$arInputTextVal = array();
			foreach($arParams['CUSTOM_FIELDS'] as $k => $fv)
			{
				if(!empty($_REQUEST['CUSTOM_FIELDS'][$k]))
				{
					$field_val[$k] = htmlspecialcharsEx(trim(implode(':~:', $_REQUEST['CUSTOM_FIELDS'][$k])));
				}

				$forArResult['CUSTOM_FIELD_' . $k]    = (strlen($field_val[$k]) > 0 && $field_val[$k] != ':~:') ? $field_val[$k] : '';
				$arCustomFields['CUSTOM_FIELD_' . $k] = str_replace(':~:', ', ', $forArResult['CUSTOM_FIELD_' . $k]);

				$arExplodeFields = explode('@', $fv);

				//v1.3.2
				$arFieldsCodeName['CUSTOM_FIELD_' . $k] = $arExplodeFields[0];

				if(in_array('required', $arExplodeFields) && (empty($field_val[$k]) || $field_val[$k] == ':~:'))
					$arResult['ERROR_MESSAGE'][] = str_replace('#PROPERTY_NAME#', $arExplodeFields[0], GetMessage('MF_ERROR_REQUIRED'));

				//v1.3.1 - Validate e-mail in CUSTOM_FIELDS
				if(in_array('email', $arExplodeFields))
				{
					if(!check_email($field_val[$k]) && strlen($field_val[$k]))
						$arResult['ERROR_MESSAGE'][] = str_replace('#PROPERTY_NAME#', $arExplodeFields[0],  GetMessage('MF_EMAIL_NOT_VALID'));
				}

				//v1.3.6 - PHP antispam
				if((in_array('input', $arExplodeFields) && in_array('text', $arExplodeFields) || in_array('textarea', $arExplodeFields))  && !empty($forArResult['CUSTOM_FIELD_' . $k]))
					$arInputTextVal[] = $forArResult['CUSTOM_FIELD_' . $k] .'';
			}

			//v1.3.6 - PHP antispam
			if($arParams['USE_PHP_ANTISPAM'])
			{
				$cntInputTextVal = count($arInputTextVal);
				$cntUniqueInputTextVal = count(array_unique($arInputTextVal));

				if(($cntInputTextVal != $cntUniqueInputTextVal) && ($cntInputTextVal - $cntUniqueInputTextVal) >= $arParams['PHP_ANTISPAM_LEVEL'] && !empty($arInputTextVal))
					$arResult['ERROR_MESSAGE'][] = GetMessage('YOU_BOT');

				unset($cntInputTextVal);
				unset($cntUniqueInputTextVal);
				unset($arInputTextVal);
			}

			unset($arExplodeFields);
		}

		//Validate attachment files and set error if empty
		if($bFileNotIsset && $arParams['SET_ATTACHMENT_REQUIRED'])
			$arResult['ERROR_MESSAGE'][] = str_replace('#PROPERTY_NAME#', GetMessage('MF_ERROR_REQUIRED_FILE') , GetMessage('MF_ERROR_REQUIRED'));

		//Validate e-mail
		if(!empty($_REQUEST['author_email']) && !check_email($_REQUEST['author_email']))
			$arResult['ERROR_MESSAGE'][] = GetMessage('MF_EMAIL_NOT_VALID');

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
					$arResult['ERROR_MESSAGE'][] = GetMessage('MF_CAPTCHA_WRONG');
			}
			else
				$arResult['ERROR_MESSAGE'][] = GetMessage('MF_CAPTHCA_EMPTY');
		}

		if(empty($arResult))
		{
			$arFields = Array(
                'EMAIL_FROM'             => $arParams['EMAIL_FROM'],
                'EMAIL_TO'               => $arParams['EMAIL_TO'],
				'AUTHOR_FIO'             => htmlspecialcharsEx(strip_tags($_REQUEST['author_fio'])),
				'AUTHOR_NAME'            => htmlspecialcharsEx(strip_tags($_REQUEST['author_name'])),
				'AUTHOR_LAST_NAME'       => htmlspecialcharsEx(strip_tags($_REQUEST['author_last_name'])),
				'AUTHOR_SECOND_NAME'     => htmlspecialcharsEx(strip_tags($_REQUEST['author_second_name'])),
				'AUTHOR_EMAIL'           => htmlspecialcharsEx(strip_tags($_REQUEST['author_email'])),
				'AUTHOR_PERSONAL_MOBILE' => htmlspecialcharsEx(strip_tags($_REQUEST['author_personal_mobile'])),
				'AUTHOR_WWW'             => htmlspecialcharsEx(strip_tags($_REQUEST['author_www'])),
				'AUTHOR_WORK_COMPANY'    => htmlspecialcharsEx(strip_tags($_REQUEST['author_work_company'])),
				'AUTHOR_POSITION'        => htmlspecialcharsEx(strip_tags($_REQUEST['author_position'])),
				'AUTHOR_PROFESSION'      => htmlspecialcharsEx(strip_tags($_REQUEST['author_profession'])),
				'AUTHOR_STATE'           => htmlspecialcharsEx(strip_tags($_REQUEST['author_state'])),
				'AUTHOR_CITY'            => htmlspecialcharsEx(strip_tags($_REQUEST['author_city'])),
				'AUTHOR_STREET'          => htmlspecialcharsEx(strip_tags($_REQUEST['author_street'])),
				'AUTHOR_ADRESS'          => htmlspecialcharsEx(strip_tags($_REQUEST['author_adress'])),
				'AUTHOR_PERSONAL_PHONE'  => htmlspecialcharsEx(strip_tags($_REQUEST['author_personal_phone'])),
				'AUTHOR_WORK_PHONE'      => htmlspecialcharsEx(strip_tags($_REQUEST['author_work_phone'])),
				'AUTHOR_FAX'             => htmlspecialcharsEx(strip_tags($_REQUEST['author_fax'])),
				'AUTHOR_MAILBOX'         => htmlspecialcharsEx(strip_tags($_REQUEST['author_mailbox'])),
				'AUTHOR_WORK_MAILBOX'    => htmlspecialcharsEx(strip_tags($_REQUEST['author_work_mailbox'])),
				'AUTHOR_SKYPE'           => htmlspecialcharsEx(strip_tags($_REQUEST['author_skype'])),
				'AUTHOR_ICQ'             => htmlspecialcharsEx(strip_tags($_REQUEST['author_icq'])),
				'AUTHOR_WORK_WWW'        => htmlspecialcharsEx(strip_tags($_REQUEST['author_work_www'])),
				'AUTHOR_MESSAGE_THEME'   => !empty($_REQUEST['author_message_theme']) ? htmlspecialcharsEx(strip_tags($_REQUEST['author_message_theme'])) : GetMessage('NO_MESSAGE_THEME'),
				'AUTHOR_MESSAGE'         => htmlspecialcharsEx(strip_tags($_REQUEST['author_message'])),
				'AUTHOR_NOTES'           => htmlspecialcharsEx(strip_tags($_REQUEST['author_notes'])),
				'PAGE_URL'               => $arParams['HTTP_PROTOCOL'] . $_SERVER['HTTP_HOST'] . $APPLICATION->GetCurDir(),
				'PAGE_TITLE'             => $APPLICATION->GetTitle(),
                'FORM_TITLE'             => !empty($arParams['FORM_TITLE']) ? trim($arParams['FORM_TITLE']) : '',
                'HTTP_HOST'              => $_SERVER['HTTP_HOST'],
                'DEFAULT_EMAIL_FROM'     => ($arParams['REPLACE_FIELD_FROM'] && strlen($_REQUEST['author_email'])) ? htmlspecialcharsEx(strip_tags(trim($_REQUEST['author_email']))) : $arParams['EMAIL_FROM'],
				'BRANCH_NAME'            => '',
				'MSG_PRIORITY'           => '',
				'IP'                     => $_SERVER['REMOTE_ADDR'], //v1.3.2
				'HTTP_USER_AGENT'        => $_SERVER['HTTP_USER_AGENT'],//v1.3.3
				'FILES'                  => $file_list,
			);

			//v1.3.2
			$arFieldsCodeNameTMP1 = $arFieldsCodeNameTMP2 = $arInputTextVal = array();
			foreach($arParams['DISPLAY_FIELDS'] as $FIELD_K)
			{

				//v1.3.6 - PHP antispam
				if(!empty($arFields[$FIELD_K])) $arInputTextVal[] = $arFields[$FIELD_K] .'';

				if($FIELD_K == 'AUTHOR_MESSAGE' || $FIELD_K == 'AUTHOR_NOTES')
				{
					$arFieldsCodeNameTMP2[$FIELD_K] = GetMessage('MF_' . $FIELD_K);
					continue;
				}
				$arFieldsCodeNameTMP1[$FIELD_K] = GetMessage('MF_' . $FIELD_K);
			}
			$arFieldsCodeName = array_merge($arFieldsCodeNameTMP1,$arFieldsCodeName,$arFieldsCodeNameTMP2);
			unset($arFieldsCodeNameTMP1);
			unset($arFieldsCodeNameTMP2);

			//v1.3.6 - PHP antispam
			if($arParams['USE_PHP_ANTISPAM'] &&  !empty($arInputTextVal))
			{
				$cntInputTextVal = count($arInputTextVal);
				$cntUniqueInputTextVal = count(array_unique($arInputTextVal));

				if(($cntInputTextVal != $cntUniqueInputTextVal) && ($cntInputTextVal - $cntUniqueInputTextVal) >= $arParams['PHP_ANTISPAM_LEVEL'])
					$arResult['ERROR_MESSAGE'][] = GetMessage('YOU_BOT');

				unset($cntInputTextVal);
				unset($cntUniqueInputTextVal);
				unset($arInputTextVal);
			}



			if($arParams['BRANCH_ACTIVE'] && intval($_REQUEST['BRANCH']) >= 0)
			{

				$arEmails = explode('###', $arParams['BRANCH_FIELDS'][intval($_REQUEST['BRANCH'])]);

				$arFields['BRANCH_NAME'] = trim($arEmails[0]);
				unset($arEmails[0]);

				if(!empty($arEmails) && is_array($arEmails))
				{
					$arFields['EMAIL_TO'] = implode(',', $arEmails);
				}

				if($arParams['MSG_PRIORITY'] && !empty($_REQUEST['MSG_PRIORITY']))
					$arFields['MSG_PRIORITY'] = htmlspecialcharsEx($_REQUEST['MSG_PRIORITY']);
			}

			//If no errors try send message
			if(empty($arResult['ERROR_MESSAGE']))
			{
				//For Admin
				if(!empty($arParams['ADMIN_EVENT_MESSAGE_ID']))
				{

					foreach($arParams['ADMIN_EVENT_MESSAGE_ID'] as $v)
					{
						if(IntVal($v) > 0)
							if(!CApiFeedback::Send($arParams['EVENT_NAME'], SITE_ID, array_merge($arCustomFields,$arFields), 'N', IntVal($v), false, $semi_rand, $arFieldsCodeName))
								$arResult['ERROR_MESSAGE'][] = GetMessage('SEND_MESSAGE_ERROR');
					}
				}
				else if(!CApiFeedback::Send($arParams['EVENT_NAME'], SITE_ID, array_merge($arCustomFields,$arFields), 'N', false, $semi_rand, $arFieldsCodeName))
					$arResult['ERROR_MESSAGE'][] = GetMessage('SEND_MESSAGE_ERROR');


				//For USER
				if(!empty($arParams['USER_EVENT_MESSAGE_ID']) && !empty($arFields['AUTHOR_EMAIL']))
				{
					$arFields['EMAIL_TO'] = $arFields['AUTHOR_EMAIL'];
					$arFields['DEFAULT_EMAIL_FROM'] = $arParams['EMAIL_FROM'];

					foreach($arParams['USER_EVENT_MESSAGE_ID'] as $v)
					{
						if(IntVal($v) > 0)
							if(!CApiFeedback::Send($arParams['EVENT_NAME'], SITE_ID, array_merge($arCustomFields,$arFields), 'N', IntVal($v), true, $semi_rand, $arFieldsCodeName))
								$arResult['ERROR_MESSAGE'][] = GetMessage('SEND_MESSAGE_ERROR');
					}
				}

				if(!empty($arResult['ERROR_MESSAGE']))
					$arResult['ERROR_MESSAGE'][] = GetMessage('FOUND_ERRORS_BEFORE_SEND');
				else
				{
					$_SESSION['API_MAIN_FEEDBACK']['SUCCESS'] = true;
					LocalRedirect($APPLICATION->GetCurPageParam('success=' . $arParams['UNIQUE_FORM_ID']));
				}
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

		if(!empty($forArResult))
			$arResult = array_merge($arResult, $forArResult);
	}
	else
		$arResult['ERROR_MESSAGE'][] = GetMessage('MF_SESS_EXP');
}
elseif($_REQUEST['success'] == $arParams['UNIQUE_FORM_ID'] && $_SESSION['API_MAIN_FEEDBACK']['SUCCESS'])
{
	$arResult['OK_MESSAGE']                   = $arParams['OK_TEXT'];
	$_SESSION['API_MAIN_FEEDBACK']['SUCCESS'] = false;

	if($arParams['DELETE_FILES_AFTER_UPLOAD'] && strlen($arParams['UPLOAD_DIR'])>10 && ($arParams['UPLOAD_DIR'] !='/upload/' || $arParams['UPLOAD_DIR'] !='/upload'))
	{
		$dir = $arParams['UPLOAD_FOLDER'];
		if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..")
				{
					if (filetype($dir."/".$object) != "dir")
						unlink($dir."/".$object);
				}
			}
			reset($objects);
			//rmdir($dir);
		}
	}
}
else
{
	//v1.3.3
	if($arParams['CUSTOM_FIELDS'])
	{
		foreach($arParams['CUSTOM_FIELDS'] as $k1 => $v1)
		{
			$arExplodeFields = explode('@', $v1);
			if(in_array('text',$arExplodeFields) || in_array('date',$arExplodeFields) || in_array('textarea',$arExplodeFields))
				if($arExplodeFields)
					foreach($arExplodeFields as $k2=>$v2)
						if(substr($v2,0,5)=="value")
							$arResult["CUSTOM_FIELD_".$k1] = str_replace('value=','',$v2);;
		}
	}

	//v1.3.4
	if($USER->IsAuthorized() && !empty($arParams['DISPLAY_FIELDS']) && !$USER->IsAdmin())
	{
		$rsUser = CUser::GetByID(intval($USER->GetID()));
		if($arUser = $rsUser->GetNext(true,false))
		{
			foreach($arParams['DISPLAY_FIELDS'] as $FIELD_CODE)
				$arResult[$FIELD_CODE] = $arUser[str_replace('AUTHOR_','PERSONAL_',$FIELD_CODE)];

			$arResult['AUTHOR_FIO']             = $arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'];
			$arResult['AUTHOR_NAME']            = $arUser['NAME'];
			$arResult['AUTHOR_LAST_NAME']       = $arUser['LAST_NAME'];
			$arResult['AUTHOR_SECOND_NAME']     = $arUser['SECOND_NAME'];
			$arResult['AUTHOR_EMAIL']           = $arUser['EMAIL'];
			$arResult['AUTHOR_PERSONAL_MOBILE'] = $arUser['PERSONAL_MOBILE'];
			$arResult['AUTHOR_PERSONAL_PHONE']  = $arUser['PERSONAL_PHONE'];
			$arResult['AUTHOR_WORK_COMPANY']    = $arUser['WORK_COMPANY'];
			$arResult['AUTHOR_POSITION']        = $arUser['WORK_POSITION'];
			$arResult['AUTHOR_WORK_PHONE']      = $arUser['WORK_PHONE'];
			$arResult['AUTHOR_WORK_MAILBOX']    = $arUser['WORK_MAILBOX'];
			$arResult['AUTHOR_WORK_WWW']        = $arUser['WORK_WWW'];
		}
	}
}

if($arParams['USE_CAPTCHA'])
	$arResult['capCode'] = htmlspecialchars($APPLICATION->CaptchaGetCode());

$this->IncludeComponentTemplate();