<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
require($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/func.php');

//CUtil::InitJSCore(array('window', 'ajax'));

if (!is_array($arParams["GROUPS"]))
	$arParams["GROUPS"] = Array();

$arParams['SUBMIT_TEXT'] = trim($arParams["SUBMIT_TEXT"]);
if (empty($arParams["SUBMIT_TEXT"]))
	$arParams['SUBMIT_TEXT'] = GetMessage("CIEE_SUBMIT_TEXT_DEFAULT");

global $USER;

if (!empty($arParams["GROUPS"]))
{
	$arGroups = array_intersect($arParams["GROUPS"], $USER->GetUserGroupArray());
	if (count($arGroups) <= 0)
	{
		$AccessDeniedmessage = strlen($arParams['ACCESS_DENIED_MESSAGE']) > 0 ? trim($arParams['ACCESS_DENIED_MESSAGE']) : GetMessage('CIEE_ACCESS_DENIED');
		$APPLICATION->AuthForm($AccessDeniedmessage);
	}
}

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("CIEE_IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = IntVal($arParams["IBLOCK_ID"]);
if($arParams["IBLOCK_ID"] <= 0)
{
	ShowError(GetMessage('IBLOCK_ID_NOT_FOUND'));
	return false;
}

$arParams['PARENT_SECTION'] = IntVal($arParams['PARENT_SECTION']);

$arParams["PARENT_SECTION_CODE"] = trim($arParams["PARENT_SECTION_CODE"]);
if(strlen($arParams["PARENT_SECTION_CODE"])<= 0)
{
	$arParams["PARENT_SECTION_CODE"] = false;
}

if(isset($arParams['FIELDS']))
{
	// для тиражных решений, собранных в кодировке отличной от кодировки сайта, сериализованный массив
	// с демо-данными окажется не корректным — пересчитаем длину строк в сериализованной строке
	if (defined('BX_UTF') && BX_UTF === true && function_exists('mb_strlen'))
		$arParams['~FIELDS'] = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.mb_strlen('$2', 'latin1').':\"$2\";'", $arParams['~FIELDS'] );
	$arParams['FIELDS'] = unserialize($arParams['~FIELDS']);
}
else
{
	$arParams['FIELDS'] = CIEE_GetDefaultFields($arParams["IBLOCK_ID"],true);
}

if (!is_array($arParams["FIELDS"]))
{
	ShowError('Incorrect $arParams["FIELDS"]');
	return;
}

$arParams["USE_CAPTCHA"] = array_key_exists('CAPTCHA', $arParams['FIELDS']);

$arParams['ERROR_LIST_MESSAGE'] = $arParams['ERROR_LIST_MESSAGE'];
if(strlen($arParams['ERROR_LIST_MESSAGE']) <= 0)
{
	$arParams['ERROR_LIST_MESSAGE'] = GetMessage('CIEE_ERROR_LIST_MESSAGE');
}

$arParams['SUCCESS_ADD_MESSAGE'] = $arParams['SUCCESS_ADD_MESSAGE'];
if(strlen($arParams['SUCCESS_ADD_MESSAGE']) <= 0)
{
	$arParams['SUCCESS_ADD_MESSAGE'] =  GetMessage('CIEE_SUCCESS_MESSAGE');
}

$COL_COUNT = intval($arParams["DEFAULT_INPUT_SIZE"]);
if($COL_COUNT < 1)
	$COL_COUNT = 30;


/*
 * добавим новую кнопку в настройки компанента,
 * она позволит нам добавить новый тип почтового шаблона
 * */
$curPagePath = $APPLICATION->GetCurPageParam('showDialog=1');
$this->AddIncludeAreaIcon(
	array(
		'URL' => 'javascript:' . $APPLICATION->GetPopupLink(Array(
			"URL" => $APPLICATION->GetCurPageParam('showDialog=1&' . bitrix_sessid_get(), Array('showDialog', 'sessid')),
			"PARAMS" => Array(
				"title" => GetMessage("CIEE_ADD_FORM_MAIL_EVENT"),
				'head' => '',
				'content' => '',
				'icon' => '',
				'resize_id' => '',
				'width' => '780',
				'height' => '370',
				'min_width' => '480',
				'min_height' => '270',
				'draggable' => true,
				'resizable' => true,
			),
		)),
		'TITLE' => GetMessage("CIEE_ADD_FORM_MAIL_EVENT"),
	)
);

require($_SERVER["DOCUMENT_ROOT"].$this->GetPath() . '/mailevent.php');

$arErrors = Array();
$arResult = Array(
	"FORM_HASH" => substr(md5(serialize($arParams)), -5),
	"OLD_VALUE" => Array(),
);

// получим список доступных полей инфоблока
$arResult["ITEMS"] = CIEE_GetDefaultFields($arParams["IBLOCK_ID"],true,true);

if(is_array($arResult["ITEMS"]))
{
	// уберем все поля которые не нужно выводить в форме
	$arResult["ITEMS"] = array_intersect_key($arResult['ITEMS'],$arParams['FIELDS']);

	// добавим недостоющие поля, и заменим существующие если это необходимо
	foreach($arResult["ITEMS"] as $code => &$fieldsValue)
	{
		$fieldsValue = array_merge($fieldsValue,$arParams['FIELDS'][$code]);
		$fieldsValue['NAME'] = $arParams['FIELDS'][$code]['TITLE'];
	}

	$arResult["FORM_ID"] = 'ciee_' . $arResult["FORM_HASH"];

	foreach($arResult["ITEMS"] as $code => $propInfo)
	{
		// установим значения по умолчанию (если они есть)
		if(isset($propInfo['DEFAULT_VALUE']))
		{
			$arResult['OLD_VALUE'][$code] = $propInfo['DEFAULT_VALUE'];
		}
	}

	// отсортируем в необходимом порядке
	$arSummFilds = $arResult['ITEMS'];
	$arResult['ITEMS'] = array();
	foreach($arParams['FIELDS'] as $fieldsCode => $value)
	{
		if(!isset($value["READ_ONLY"]) || !$value["READ_ONLY"])
			$arResult['ITEMS'][$fieldsCode] = $arSummFilds[$fieldsCode];
	}

	$bCurrentFormPost = $_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid() && isset($_REQUEST['iblock_submit']);
	// поддержка размещения нескольких форм на одной странице
	if (is_set($_REQUEST, 'ciee_hash'))
		$bCurrentFormPost = $bCurrentFormPost && $_REQUEST['ciee_hash'] === $arResult['FORM_HASH'];

	// обработаем данные полученные из формы
	if ($bCurrentFormPost)
	{
		// проверим правильно ли ввели слово с картинки (CAPTCHA)
		if ($arParams["USE_CAPTCHA"])
		{
			if (!$APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]))
				$arErrors[] = GetMessage("CIEE_IBLOCK_FORM_WRONG_CAPTCHA");
		}

		$typeList = array('N','L','E','G');
		$arUpdateFieldValues = $arUpdatePropertyValues = array();

		foreach($arResult["ITEMS"] as $code => $info)
		{
			if($code == 'CAPTCHA')
			{
				continue;
			}

			if(!array_key_exists($code, $_REQUEST['PROPERTY']) && $info['PROPERTY_TYPE'] != 'F')
			{
				if($info['IS_REQUIRED'])
					$arErrors[$code] = GetMessage('NOT_DEFINED_REQUIRED_FIELD',array("#FIELD#" => $info['NAME'])) ;
				continue;
			}

			$fieldsValue = $_REQUEST['PROPERTY'][$code];

			if($info['PROPERTY_TYPE'] == 'F')
			{
				$arFiles = $_FILES['PROPERTY']['tmp_name'][$code];
				$fieldsValue = array_diff($arFiles, array(''));

				if(empty($fieldsValue) && $info['IS_REQUIRED'])
				{
					$arErrors[$code] = GetMessage('NOT_DEFINED_REQUIRED_FIELD',array("#FIELD#" => $info['NAME']));
				}
			}
			elseif($info['MULTIPLE'] == "Y")
			{
				$fieldsValue = array_diff($fieldsValue, array(''));
				if(empty($fieldsValue) && $info['IS_REQUIRED'])
				{
					$arErrors[$code] = GetMessage('NOT_DEFINED_REQUIRED_FIELD',array("#FIELD#" => $info['NAME']));
				}
			}
			elseif($info['MULTIPLE'] == "N" && strlen($fieldsValue) <= 0 && $info['IS_REQUIRED'])
			{
				$arErrors[$code] = GetMessage('NOT_DEFINED_REQUIRED_FIELD',array("#FIELD#" => $info['NAME']));
			}
			elseif(in_array($info['PROPERTY_TYPE'],$typeList) && IntVal($fieldsValue) <= 0 && $info['IS_REQUIRED'])
			{
				$arErrors[$code] = GetMessage('NOT_COMPLETED_REQUIRED_FIELD',array("#FIELD#" => $info['NAME']));
			}

			if(isset($arErrors[$code]))
			{
				continue;
			}

			if(is_array($fieldsValue))
			{
				foreach($fieldsValue as $id => $value)
				{
					if ($info['PROPERTY_TYPE'] == 'F')
					{
						$filePath = $value;
						if (is_uploaded_file($filePath))
						{
							$arFile = CFile::MakeFileArray($filePath);
							$arFile["name"] = $_FILES['PROPERTY']['name'][$code][$id];
							$fieldsValue[$id] = $arFile;
						}
					}
					elseif(in_array($info['PROPERTY_TYPE'],$typeList) && IntVal($value) > 0)
					{
						$fieldsValue[$id] = IntVal($value);
					}
					elseif(!in_array($info['PROPERTY_TYPE'],$typeList) && strlen($value) > 0)
					{
						$fieldsValue[$id] = trim($value);
					}
				}
			}
			else
			{
				$fieldsValue = in_array($info['PROPERTY_TYPE'],$typeList) ? IntVal($fieldsValue) : trim($fieldsValue);
			}

			if($info['MULTIPLE'] == "Y" && !empty($fieldsValue))
			{
				$fieldsValue = array_values($fieldsValue);
				!isset($info['PROPERTY_ID']) ? $arUpdateFieldValues[$code] = $fieldsValue : $arUpdatePropertyValues[$code] = $fieldsValue;
			}
			elseif($info['PROPERTY_TYPE'] == 'F'  && !empty($fieldsValue))
			{
				$fieldsValue = array_shift($fieldsValue);
				!isset($info['PROPERTY_ID']) ? $arUpdateFieldValues[$code] = $fieldsValue : $arUpdatePropertyValues[$code] = $fieldsValue;
			}
			elseif(in_array($info['PROPERTY_TYPE'],$typeList) && IntVal($fieldsValue) > 0)
			{
				!isset($info['PROPERTY_ID']) ? $arUpdateFieldValues[$code] = $fieldsValue : $arUpdatePropertyValues[$code] = $fieldsValue;
			}
			elseif(!in_array($info['PROPERTY_TYPE'],$typeList) && strlen($fieldsValue) > 0)
			{
				!isset($info['PROPERTY_ID']) ? $arUpdateFieldValues[$code] = $fieldsValue : $arUpdatePropertyValues[$code] = $fieldsValue;
			}
		}

		// если в форме не указанно поле - привязка к разделу, то проверим настройки компанента
		if (!isset($arUpdateFieldValues['IBLOCK_SECTION'])
			|| IntVal($arUpdateFieldValues['IBLOCK_SECTION']) <= 0 && ($arParams["PARENT_SECTION"] > 0 || strlen($arParams["PARENT_SECTION_CODE"]) > 0))
		{
			$parentSection = CIBlockFindTools::GetSectionID(
				$arParams["PARENT_SECTION"],
				$arParams["PARENT_SECTION_CODE"],
				array(
					"IBLOCK_ID" => $arParams['IBLOCK_ID'],
				)
			);
			if ($parentSection > 0)
				$arUpdateFieldValues["IBLOCK_SECTION_ID"] = $parentSection;
		}

		if(empty($arErrors))
		{
			$arUpdate = array(
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			);

			// значения полей по умолчанию из настроек инфоблока
			$arIBlock = CIBlock::GetArrayByID($arUpdate['IBLOCK_ID']);
			if (is_array($arIBlock))
			{
				$arUpdate = array_merge($arUpdate, Array(
					"ACTIVE" => $arIBlock["FIELDS"]["ACTIVE"]["DEFAULT_VALUE"] === "N" ? "N": "Y",
					"NAME" => htmlspecialcharsbx($arIBlock["FIELDS"]["NAME"]["DEFAULT_VALUE"]),
					'PREVIEW_TEXT_TYPE' => $arIBlock["FIELDS"]["PREVIEW_TEXT_TYPE"]["DEFAULT_VALUE"] !== "html"? "text": "html",
					'PREVIEW_TEXT' => htmlspecialcharsbx($arIBlock["FIELDS"]["PREVIEW_TEXT"]["DEFAULT_VALUE"]),
					'DETAIL_TEXT_TYPE' => $arIBlock["FIELDS"]["DETAIL_TEXT_TYPE"]["DEFAULT_VALUE"] !== "html"? "text": "html",
					'DETAIL_TEXT' => htmlspecialcharsbx($arIBlock["FIELDS"]["DETAIL_TEXT"]["DEFAULT_VALUE"]),
				));
				if ($arIBlock["FIELDS"]["ACTIVE_FROM"]["DEFAULT_VALUE"] === "=now")
					$arUpdate['ACTIVE_FROM'] = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL");
				elseif ($arIBlock["FIELDS"]["ACTIVE_FROM"]["DEFAULT_VALUE"] === "=today")
					$arUpdate['ACTIVE_FROM'] = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "SHORT");

				if(intval($arIBlock["FIELDS"]["ACTIVE_TO"]["DEFAULT_VALUE"]) > 0)
					$arUpdate['ACTIVE_TO'] = ConvertTimeStamp(strtotime('+' . intval($arIBlock["FIELDS"]["ACTIVE_TO"]["DEFAULT_VALUE"]) . ' days') + CTimeZone::GetOffset(), "FULL");
			}

			$arUpdate = array_merge($arUpdate, $arUpdateFieldValues);

			if(!empty($arUpdatePropertyValues))
			{
				foreach($arUpdatePropertyValues as $propCode => $propValue)
				{
					$arUpdate['PROPERTY_VALUES'][$arResult["ITEMS"][$propCode]['PROPERTY_ID']] = $propValue;
				}
			}

			$obCIBlockElement = new CIBlockElement();
			if ($newID = $obCIBlockElement->Add($arUpdate,false,true,true))
			{
				// если установленно значение отправлять уведомление, то отправим письмо
				if($arParams['SEND_MESSAGE'] == "Y")
				{
					$arMailFields = array();
					$arEvent = CEventType::GetList(array("TYPE_ID" => $arParams['MAIL_EVENT']))->Fetch();
					if($arEvent)
					{
						$arFullFieldsValue = array_merge($arUpdateFieldValues,$arUpdatePropertyValues);
						foreach($arResult['ITEMS'] as $code => $info)
						{
							if (stripos($arEvent['DESCRIPTION'], $code) !== false)
							{
								if (in_array($info['PROPERTY_TYPE'], array('L','E','G')))
								{
									$arMailFields[$code] = $info['ENUM'][$arFullFieldsValue[$code]]['VALUE'];
								}
								elseif (in_array($info['PROPERTY_TYPE'], array('F')))
								{
									$arProperty = CIBlockElement::GetProperty($arUpdate['IBLOCK_ID'], $newID, array(), Array("ID" => $info['PROPERTY_ID']))->GetNext(false, false);
									if (is_array($arProperty) && !empty($arProperty['VALUE']))
									{
										$serverName = SITE_SERVER_NAME ? SITE_SERVER_NAME : (COption::GetOptionString('main', 'server_name', $_SERVER['HTTP_HOST']));
										$url = (CMain::IsHTTPS()) ? "https://" : "http://" . $serverName . CFile::GetPath($arProperty['VALUE']);
										$arMailFields[$code] = $url;
									}
									else
										$arMailFields[$code] = '';
								}
								else
								{
									$arMailFields[$code] = $arFullFieldsValue[$code];
								}
							}
						}
						// добавим возможность использования ID добавленного элемента в шаблоне почтового события
						$arMailFields['ID'] = $newID;
						CEvent::Send($arParams['MAIL_EVENT'],SITE_ID,$arMailFields);
					}
				}

				$_SESSION['iblock_message'] = str_replace(
					Array("#ID#", '#SITE_DIR#'),
					Array($newID, SITE_DIR),
					htmlspecialchars_decode($arParams['SUCCESS_ADD_MESSAGE'])
				);
				LocalRedirect($GLOBALS['APPLICATION']->GetCurPageParam("success=true", Array('success')));
			}
			else
			{
				$arErrors[] = $obCIBlockElement->LAST_ERROR;
				$arResult['bVarsFromForm'] = true;
			}
		}

		// если массив ошибок не пуст то запишем текущие значение полей
		$arResult['OLD_VALUE'] = CIEE_htmlspecialchars($arUpdatePropertyValues + $arUpdateFieldValues);
	}
}
else
{
	$this->AbortResultCache();
	ShowError(GetMessage("CIEE_IBLOCK_NOT_FOUND"));
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
}

if (!empty($arErrors))
	$arResult["ERRORS"] = implode('<br />', $arErrors);

if (empty($arErrors) && is_set($_GET, 'success') && !empty($_GET['success']))
	$arResult["MESSAGE"] = is_set($_SESSION, 'iblock_message') ? $_SESSION['iblock_message'] : htmlspecialchars_decode($arParams['SUCCESS_ADD_MESSAGE']);

// получить капчу
if ($arParams["USE_CAPTCHA"] && $arParams["ID"] <= 0)
{
	$arResult["CAPTCHA_CODE"] = htmlspecialchars($APPLICATION->CaptchaGetCode());
}

$arResult["FORM_ACTION"] = $APPLICATION->GetCurPageParam();
$this->IncludeComponentTemplate();

?>