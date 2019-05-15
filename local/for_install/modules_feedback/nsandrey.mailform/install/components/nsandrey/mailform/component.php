<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

global $UPLOADED_FILES;
$UPLOADED_FILES = array();

CModule::IncludeModule('nsandrey.mailform');

if ($arParams['JQUERY'] == 'Y')
{
	CJSCore::Init('jquery');
}


// Типы полей
$arTypes = array('HIDDEN', 'STRING', 'INT', 'CHECKBOX', 'DATE_TIME', 'DATE_TIME_INTERVAL', 'TEXTAREA', 'EMAIL', 'FILE', 'SELECT', 'MULTISELECT');
// Поля для антиспама
$arResult['ANTISPAM_FIELDS'] = $arAntiSpamFields = array('IMPORTANT_EMAIL_TO', 'IMPORTANT_EMAIL_FROM', 'IMPORTANT_PHONE', 'IMPORTANT_DATE', 'IMPORTANT_MESSAGE');

//Инициализация параметров
if ($arParams['EVENT_ID'] == '')
{
	ShowError(GetMessage('UNIF_NEED_SETTINGS'));
	return;
}

$formID = $arParams['FORM_ID'];

// save form parameters for ajax
$_SESSION['UNIF'][$formID] = $arParams;

$arParams['USE_CAPTCHA'] = ($arParams['USE_CAPTCHA'] != 'N' && !$USER->IsAuthorized()) ? true : false;

$arParams['EMAIL_TO'] = trim($arParams['EMAIL_TO']);
if (strlen($arParams['EMAIL_TO']) <= 0)
{
	$arParams['EMAIL_TO'] = COption::GetOptionString('main', 'email_from');
}

$arParams['OK_TEXT'] = trim($arParams['OK_TEXT']);
if (strlen($arParams['OK_TEXT']) <= 0)
{
	$arParams['OK_TEXT'] = GetMessage('UNIF_OK_MESSAGE');
}

//Выборка полей из почтового события - $arFields
$dbType = CEventType::GetList(array('TYPE_ID' => $arParams['EVENT_ID'], 'LID' => LANGUAGE_ID));
if ($arType = $dbType->GetNext())
{
	preg_match_all('|^#(.+)# - (.+)$|im', $arType['DESCRIPTION'], $matches);
	$a_size = sizeof($matches[1]);

	for ($i = 0; $i < $a_size; $i++)
	{
		$arFields[$matches[1][$i]] = trim($matches[2][$i]);
	}
}

//Подготовка полей для вывода
foreach ($arFields as $key => $value)
{
	if (SITE_CHARSET != 'UTF-8')
	{
		$_REQUEST['FIELDS'][$key] = iconv('UTF-8', 'windows-1251', $_REQUEST['FIELDS'][$key]);
	}

	$arReadyFields[$key] = unifGetField($APPLICATION, $key, $_REQUEST['FIELDS'][$key], $arParams);
	$arReadyFields[$key]['LABEL'] = $value;
}

// Проверка скрытых полей для борьбы со спамом
$antiSpamPassed = true;
if ($arParams['ENABLE_HIDDEN_ANTISPAM_FIELDS'] == 'Y')
{
	foreach ($arAntiSpamFields as $antiSpamFieldName)
	{
		if (!array_key_exists($antiSpamFieldName, $arFields) && !empty($_REQUEST['FIELDS'][$antiSpamFieldName]))
		{
			$antiSpamPassed = false;
		}
	}
}

//Проверка полей и генерация почтового события
$errors = array();

if ($antiSpamPassed && $_REQUEST['REQUEST_TYPE'] == 'SEND' && $_REQUEST['FORM_ID'] == $formID)
{
	$arSend = array();

	// проверка капчи
	if ($arParams['USE_CAPTCHA'])
	{
		include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/captcha.php');

		$cpt = new CCaptcha();

		if (strlen($_REQUEST['CAPTCHA_WORD']) > 0 && strlen($_REQUEST['CAPTCHA_SID']) > 0)
		{
			if (!$cpt->CheckCodeCrypt($_REQUEST['CAPTCHA_WORD'], $_REQUEST['CAPTCHA_SID'], COption::GetOptionString('main', 'captcha_password', '')))
			{
				$errors['CAPTCHA'] = array(
					'FIELD_TYPE' => 'CAPTCHA',
					'ERROR_TYPE' => 'WRONG'
				);
			}
		}
		else
		{
			$errors['CAPTCHA'] = array(
				'FIELD_TYPE' => 'CAPTCHA',
				'ERROR_TYPE' => 'EMPTY'
			);
		}
	}

	if (sizeof($errors) < 1)
	{
		foreach ($_FILES['FIELDS'] as $ff_key => $ff)
		{
			foreach ($ff as $f_key => $f_value)
			{
				$arReadyFields[$f_key]['VALUE'][$ff_key] = $f_value;
				$arReadyFields[$f_key]['VALUE']['del'] = 'N';
				$arReadyFields[$f_key]['VALUE']['MODULE_ID'] = 'nsandrey.mailform';
			}
		}

		//Проверка полей
		foreach ($arReadyFields as $fieldName => $fieldData)
		{
			$additional = $fieldData['TYPE'] == 'FILE' ? $arParams['FILE_EXT'] : '';

			$fieldCheck = unifCheckField($fieldData['TYPE'], $fieldData['VALUE'], $additional);

			if ($fieldCheck == 'EMPTY' && $fieldData['REQUIRED'] || $fieldCheck == 'WRONG')
			{
				$errors[$fieldName] = array(
					'FIELD_TYPE' => $fieldCheck == 'EMPTY' ? 'FIELD' : $fieldData['TYPE'],
					'ERROR_TYPE' => $fieldCheck
				);
			}
		}

		if (sizeof($errors) < 1)
		{
			foreach ($arFields as $key => $value)
			{
				if (!in_array($arParams[$key], $arTypes))
				{
					$arSend[$key] = $arParams[$key];
					continue;
				}

				//Сохраняем поле для отправки
				$sValue = '';
				$arFiles = array();

				switch ($arParams[$key])
				{
					case 'FILE':
						if ($arReadyFields[$key]['VALUE']['error'] == 0)
						{
							$arFiles[] = $key;
						}
						break;

					case 'CHECKBOX':
						$sValue = $arReadyFields[$key]['VALUE'] == 'Y' ? GetMessage('UNIF_CHECKBOX_TRUE') : GetMessage('UNIF_CHECKBOX_FALSE');
						break;

					case 'SELECT':
						$sValue = isset($arParams[$key.'_SELECT_VALUE'][$arReadyFields[$key]['VALUE']]) ? $arParams[$key.'_SELECT_VALUE'][$arReadyFields[$key]['VALUE']] : '';
						break;

					case 'MULTISELECT':
						$arMS = array();

						foreach ($arReadyFields[$key]['VALUE'] as $m_value)
						{
							if ($arParams[$key . '_SELECT_VALUE'][$m_value] != '')
							{
								$arMS[] = $arParams[$key . '_SELECT_VALUE'][$m_value];
							}
						}

						$sValue = sizeof($arMS) > 0 ? implode('; ', $arMS) : '';
						break;

					case 'DATE_TIME':
						$sValue = str_replace(' 00:00:00', '', $arReadyFields[$key]['VALUE']);
						break;

					case 'DATE_TIME_INTERVAL':
						$sValue = str_replace(' 00:00:00', '', $arReadyFields[$key]['VALUE'][0]).' - '.str_replace(' 00:00:00', '', $arReadyFields[$key]['VALUE'][1]);
						break;

					default:
						$sValue = $arReadyFields[$key]['VALUE'];
						break;
				}

				$arSend[$key] = $sValue != '' ? $sValue : GetMessage('UNIF_NOT_SET');
			}

			//Поля файлов
			foreach ($arFiles as $f)
			{
				$temp_f = $arReadyFields[$f]['VALUE'];

				if ($arParams['FILE_SAVE'] == 'Y' && ($f_id = CFile::SaveFile($temp_f, 'nsandrey.mailform')))
    			{
    				$file_path = CFile::GetPath($f_id);
					$arSend[$f] = '<a href="http://'.$_SERVER['SERVER_NAME'].$file_path.'">'.GetMessage('UNIF_FILE_LINK').' ( '.$temp_f['name'].' )</a>';
					$UPLOADED_FILES[] = array('PATH' => $file_path, 'NAME' => $temp_f['name']);
				}
				else
				{
					$UPLOADED_FILES[] = array('PATH' => $temp_f['tmp_name'], 'NAME' => $temp_f['name']);
				}
			}
			
			//Подписка на рассылки
			if ($_REQUEST['SIGN'] == 'Y' && $arParams['SIGN'] == 'Y' && $arParams[$arParams['SIGN_EMAIL']] == 'EMAIL' && CModule::IncludeModule('subscribe'))
			{
				$subscr = new CSubscription;
				$subscr->Add(array('EMAIL' => $arReadyFields[$arParams['SIGN_EMAIL']]['VALUE'], 'ACTIVE' => 'Y', 'RUB_ID' => $arParams['SIGN_ON'], 'SEND_CONFIRM' => ($arParams['SIGN_CONFIRM'] == 'Y' ? 'Y' : 'N'), 'CONFIRMED' => ($arParams['SIGN_CONFIRM'] == 'Y' ? 'N' : 'Y')));
			}

			//Сохранение в инфоблок
			if ($arParams['SAVE_TO_IBLOCK'] == 'Y' && $arParams['IBLOCK_ID'] > 0 && CModule::IncludeModule('iblock'))
			{
				$to_ibl = new CIBlockElement;

				$arFieldsS = array(
					'IBLOCK_ID' => $arParams['IBLOCK_ID'],
					'ACTIVE' => 'N',
					'NAME' => $arReadyFields[$arParams['FIELD_FOR_NAME']]['VALUE'],
					'IBLOCK_SECTION_ID' => $arParams['FIELD_FOR_SECTION'] == '0' ? 0 : $arReadyFields[$arParams['FIELD_FOR_SECTION']]['VALUE'],
					'PROPERTY_VALUES' => array()
				);

				foreach ($arSend as $s_key => $s_value)
				{
					if (!empty($arParams[$s_key . '_TO_IBLOCK']))
					{
						if (in_array($arParams[$s_key . '_TO_IBLOCK'], array('PREVIEW_TEXT', 'DETAIL_TEXT', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'SORT', 'DATE_ACTIVE_FROM', 'DATE_ACTIVE_TO')))
						{
							$arFieldsS[$arParams[$s_key . '_TO_IBLOCK']] = $arParams[$s_key] != 'FILE' ? $s_value : $arReadyFields[$s_key]['VALUE'];
						}
						else
						{
							$arFieldsS['PROPERTY_VALUES'][$arParams[$s_key . '_TO_IBLOCK']] = $arParams[$s_key] != 'FILE' ? $s_value : $arReadyFields[$s_key]['VALUE'];
						}
					}
				}

				$to_ibl->Add($arFieldsS);
			}

			//Поле с e-mail'ом на который отправлять все
			$arSend['EMAIL_TO'] = $arParams['EMAIL_TO'];

			//Посылаем сообщения
			foreach ($arParams['EVENT_MESSAGE_ID'] as $v)
			{
				if (IntVal($v) > 0)
				{
					$rsEvents = GetModuleEvents("nsandrey.mailform", "OnBeforeEmailSend");

					while ($arEvent = $rsEvents->Fetch())
					{
						if (ExecuteModuleEventEx($arEvent, array(&$arSend)) === false)
						{
							return false;
						}
					}

					CEvent::SendImmediate($arParams['EVENT_ID'], SITE_ID, $arSend, 'N', $v);
				}
			}

			$arResult['MESSAGE_SENDED'] = 'Y';
			$_REQUEST['FIELDS'] = array();
		}
	}
}

if (sizeof($errors) > 0)
{
	$errors['NEW_CAPTCHA'] = htmlspecialchars($APPLICATION->CaptchaGetCode());
}

$arResult['ERRORS'] = $errors;

if ($arParams['SIGN'] == 'Y')
{
	$arResult['SIGN'] = array(
		'LABEL' => $arParams['SIGN_NAME'],
		'HTML' => '<input type="checkbox" name="SIGN" value="Y" checked>'
	);
}

if ($arParams['USE_CAPTCHA'])
{
	$arResult['CAPTCHA_CODE'] = htmlspecialchars($APPLICATION->CaptchaGetCode());
}

$arResult['FIELDS'] = $arReadyFields;
$arResult['COMPONENT_PATH'] = $this->GetPath();
$arResult['FORM_ID'] = $formID;

$this->IncludeComponentTemplate();
?>