<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
require($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/func.php');

// set default value for missing parameters, simple param check
$componentParams = CComponentUtil::GetComponentProps($this->getName());
if (is_array($componentParams))
{
	foreach ($componentParams["PARAMETERS"] as $paramName => $paramArray)
	{
		if (!is_set($arParams, $paramName) && is_set($paramArray, "DEFAULT"))
			$arParams[$paramName] = $paramArray["DEFAULT"];

		$paramArray["TYPE"] = ToUpper(is_set($paramArray, "TYPE") ? $paramArray["TYPE"] : "STRING");
		switch ($paramArray["TYPE"]) 
		{
			case "INT":
				$arParams[$paramName] = IntVal($arParams[$paramName]);
				break;

			case "LIST":
				if (!array_key_exists($arParams[$paramName], $paramArray["VALUES"]))
					$arParams[$paramName] = $paramArray["DEFAULT"];
				break;

			case "CHECKBOX":
				$arParams[$paramName] = ($arParams[$paramName] == (is_set($paramArray, "VALUE") ? $paramArray["VALUE"] : "Y"));
				break;

			default:
				// string etc.
				break;
		}
	}
}

if (isset($arParams['FIELDS']))
{
	// для тиражных решений, собранных в кодировке отличной от кодировки сайта, сериализованный массив
	// с демо-данными окажется не корректным — пересчитаем длину строк в сериализованной строке
	if (defined('BX_UTF') && BX_UTF === true && function_exists('mb_strlen'))
		$arParams['~FIELDS'] = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.mb_strlen('$2', 'latin1').':\"$2\";'", $arParams['~FIELDS'] );
	$arParams['FIELDS'] = unserialize($arParams['~FIELDS']);
}
else
{
	$arParams['FIELDS'] = CSE_GetFields($arParams["EVENT_TYPE"]);
}

if (!is_array($arParams["FIELDS"]))
{
	ShowError('Incorrect $arParams["FIELDS"]');
	return;
}

$arParams['SUCCESS_SEND_MESSAGE'] = trim($arParams['SUCCESS_SEND_MESSAGE']);
if (strlen($arParams['SUCCESS_SEND_MESSAGE']) <= 0)
{
	$arParams['SUCCESS_SEND_MESSAGE'] =  GetMessage('CSE_SUCCESS_SEND_MESSAGE');
}

$arErrors = Array();
$arResult = Array(
	"FORM_HASH" => substr(md5(serialize($arParams)), -5),
	"OLD_VALUE" => Array(),
);

// получим список доступных полей
$arResult["ITEMS"] = $arDefaultFields = CSE_GetFields($arParams["EVENT_TYPE"]);

if (is_array($arResult["ITEMS"]))
{
	// уберем все поля которые не нужно выводить в форме
	$arResult["ITEMS"] = array_intersect_key($arResult['ITEMS'], $arParams['FIELDS']);

	// добавим недостоющие поля, и заменим существующие если это необходимо
	foreach($arResult["ITEMS"] as $code => &$fieldsValue)
	{
		$fieldsValue = array_merge($fieldsValue, $arParams['FIELDS'][$code]);
		$fieldsValue['NAME'] = $arParams['FIELDS'][$code]['TITLE'];
	}

	$arResult["FORM_ID"] = 'cse_' . $arResult["FORM_HASH"];

	$bCurrentFormPost = $_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid() && isset($_REQUEST['send_submit']);
	// поддержка размещения нескольких форм на одной странице
	if (is_set($_REQUEST, 'cse_hash'))
		$bCurrentFormPost = $bCurrentFormPost && $_REQUEST['cse_hash'] === $arResult['FORM_HASH'];

	// обработаем данные полученные из формы
	if ($bCurrentFormPost)
	{
		// проверим правильно ли ввели слово с картинки (CAPTCHA)
		if (isset($arResult["ITEMS"]["__CAPTCHA__"]))
		{
			if (!$APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]))
				$arErrors[] = GetMessage("CSE_WRONG_CAPTCHA");
		}

		$arSendFields = $arFieldValues = array();

		foreach ($arResult["ITEMS"] as $code => $info)
		{
			if ($code == '__CAPTCHA__')
				continue;

			$fieldValue = $_REQUEST['FIELD'][$code];
			$arFieldValues[$code] = $fieldValue;

			if ($info['IS_REQUIRED'])
			{
				if (!array_key_exists($code, $_REQUEST['FIELD']) || strlen($fieldValue) <= 0)
				{
					$arErrors[$code] = GetMessage('NOT_DEFINED_REQUIRED_FIELD', array("#FIELD#" => $info['NAME']));
					continue;
				}
			}
			
			if ($info['IS_EMAIL'] && !check_email($fieldValue, true))
			{
				$arErrors[$code] = GetMessage('CSE_ERROR_INVALID_EMAIL', array("#FIELD#" => $info['NAME']));
				continue;
			}			

			$arSendFields[$code] = $fieldValue;
		}

		if (empty($arErrors))
		{
			$arMissingFields = array_diff_key($arDefaultFields, $arSendFields);
			foreach ($arMissingFields as $missingField => $arField)
			{
				switch ($missingField)
				{
					case "HEADER_SENDER":
						$arSendFields["HEADER_SENDER"] = getSenderHeader(SITE_ID, $arSendFields["EMAIL_FROM"]);
						break;
				}
			}

			if (CEvent::Send($arParams['EVENT_TYPE'], SITE_ID, $arSendFields))
			{
				$_SESSION[$this->__name] = htmlspecialchars_decode($arParams['SUCCESS_SEND_MESSAGE']);
				LocalRedirect($GLOBALS['APPLICATION']->GetCurUri());
			}
			else
			{
				$arResult['bVarsFromForm'] = true;
			}
		}

		// если массив ошибок не пуст то запишем текущие значение полей
		$arResult['OLD_VALUE'] = CSE_htmlspecialchars($arFieldValues);
	}
}
else
{
	$this->AbortResultCache();
	ShowError(GetMessage("CSE_EVENT_TYPE_NOT_FOUND"));
	@define("ERROR_404", "Y");
	if ($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
}

if (!empty($arErrors))
	$arResult["ERRORS"] = implode('<br />', $arErrors);

if (empty($arErrors) && is_set($_SESSION, $this->__name))
{
	$arResult["MESSAGE"] = $_SESSION[$this->__name];
	unset($_SESSION[$this->__name]);
}

// получить капчу
if (isset($arResult["ITEMS"]["__CAPTCHA__"]))
{
	$arResult["CAPTCHA_CODE"] = htmlspecialchars($APPLICATION->CaptchaGetCode());
}

$arResult["FORM_ACTION"] = $APPLICATION->GetCurPageParam();

$this->IncludeComponentTemplate();
