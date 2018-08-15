<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

//удаляем параметры из сессии
$_SESSION["CMPT_PARAMS"] = null;

$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->GetTemplateName());

$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");

// ID почтового события
$arParams["EVENT_MESSAGE_ID"] = trim($arParams["EVENT_MESSAGE_ID"]);

$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if($arParams["EMAIL_TO"] == '')
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");

$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]);
if($arParams["OK_TEXT"] == '')
	$arParams["OK_TEXT"] = GetMessage("MF_OK_MESSAGE");

// мой код
	if(isset($arParams['_POST'])){
		//здесь производится отправка
		
		if(check_bitrix_sessid()){
			// собираем ошибки
			$arResult["ERROR_MESSAGE"] = array();
			
			if($arParams['_POST']['form_type'] == 'call_ord')
			{
				if(empty($arParams["REQUIRED_FIELDS"]) || !in_array("NONE", $arParams["REQUIRED_FIELDS"]))
				{
					if((empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])) && strlen($arParams['_POST']["v_name"]) <= 1)
						$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_NAME");		
					if((empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])) && strlen($arParams['_POST']["v_phone"]) <= 1)
						$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_PHONE");
					if((empty($arParams["REQUIRED_FIELDS"]) || in_array("TIMETOCALL", $arParams["REQUIRED_FIELDS"])) && strlen($arParams['_POST']["v_time"]) <= 1)
						$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_TIMETOCALL");	
					if($arParams["USE_MESSAGE_FIELD"] == "Y")
					{
						if((empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])) && strlen($arParams['_POST']["v_mess"]) <= 3)
							$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_MESSAGE");
					}						
				}		
			
			} 
			
			if($arParams["USE_CAPTCHA"] == "Y")
			{
				include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
				$captcha_code = $arParams['_POST']["captcha_sid"];
				$captcha_word = $arParams['_POST']["captcha_word"];
				$cpt = new CCaptcha();
				$captchaPass = COption::GetOptionString("main", "captcha_password", "");
				if (strlen($captcha_word) > 0 && strlen($captcha_code) > 0)
				{
					if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
						$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTCHA_WRONG");
				}
				else
					$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTHCA_EMPTY");

			}
			
			if(empty($arResult["ERROR_MESSAGE"]))
			{
				if($arParams["USE_MESSAGE_FIELD"] == "Y")
				{
					$arFields = Array(
						"AUTHOR" => $arParams["_POST"]["v_name"],
						"AUTHOR_PHONE" => $arParams["_POST"]["v_phone"],
						"TIME_TOCALL" => $arParams["_POST"]["v_time"],
						"EMAIL_TO" => $arParams["EMAIL_TO"],
						"MESSAGE" => $arParams["_POST"]["v_mess"],
					);
				}
				else
				{
					$arFields = Array(
							"AUTHOR" => $arParams["_POST"]["v_name"],
							"AUTHOR_PHONE" => $arParams["_POST"]["v_phone"],
							"TIME_TOCALL" => $arParams["_POST"]["v_time"],
							"EMAIL_TO" => $arParams["EMAIL_TO"],
						);
				}
			
				CEvent::Send($arParams["EVENT_MESSAGE_ID"], SITE_ID, $arFields);
				$_SESSION["MF_NAME"] = htmlspecialcharsbx($_POST["user_name"]);
				$_SESSION["MF_EMAIL"] = htmlspecialcharsbx($_POST["user_email"]);
				
				$arResult["OK_MESSAGE"] = "<p>".$arParams["OK_TEXT"]."</p>";
				
				echo $arResult["OK_MESSAGE"];
				
				if($arParams["SAVE_FORM_DATA"] == "Y" && !empty($arParams["IBLOCK_TYPE"]) && count($arParams["IBLOCKS"]) > 0){
					if(CModule::IncludeModule('iblock')){
						$el = new CIBlockElement;
						
						$elname = "Заказ от ".$arParams["_POST"]["v_name"];
						
						$props = Array();
						$props["VISITOR_FIO"] = $arParams["_POST"]["v_name"];
						$props["VISITOR_PHONE"] = $arParams["_POST"]["v_phone"];
						$props["TIME_TOCALL"] = $arParams["_POST"]["v_time"];
						if($arParams["USE_MESSAGE_FIELD"] == "Y"){
							$props["VISITOR_MESSAGE"] = Array("VALUE" => Array ("TEXT" => $arParams["_POST"]["v_mess"], "TYPE" => "text"));
						}
						
						$arLoadOrderArray = Array(
							"IBLOCK_ID" => $arParams["IBLOCKS"][0],
							"PROPERTY_VALUES" => $props,
							"NAME" => $elname,
							"ACTIVE" => "Y"
						);
												
						if(!$el->Add($arLoadOrderArray)){
							$error = GetMessage('MF_ERROR_MESSAGE').' '.$el->LAST_ERROR;
							ShowError($error);
						}				
					}			
				}
			}		
		}
		else
		{
			$arResult["ERROR_MESSAGE"][] = GetMessage("MF_SESS_EXP"); 
		
		}
		
		$errTXT = "";
		if(!empty($arResult["ERROR_MESSAGE"]) && count($arResult["ERROR_MESSAGE"]) > 1){
			foreach($arResult["ERROR_MESSAGE"] as $errtext)
			{
				$errTXT .= $errtext. '<br />';
			}
		}
		else{
			$errTXT .= $arResult["ERROR_MESSAGE"][0];
		}
		
		// выводим ошибки
		echo $errTXT;
	}
	else{
		// до отправки
		if(empty($arResult["ERROR_MESSAGE"]))
		{
			if($USER->IsAuthorized())
			{
				$arResult["AUTHOR_NAME"] = $USER->GetFormattedName(false);
				$arResult["AUTHOR_EMAIL"] = htmlspecialcharsbx($USER->GetEmail());
			}
			else
			{
				if(strlen($_SESSION["MF_NAME"]) > 0)
					$arResult["AUTHOR_NAME"] = htmlspecialcharsbx($_SESSION["MF_NAME"]);
				if(strlen($_SESSION["MF_EMAIL"]) > 0)
					$arResult["AUTHOR_EMAIL"] = htmlspecialcharsbx($_SESSION["MF_EMAIL"]);
			}
		}

		if($arParams["USE_CAPTCHA"] == "Y")
			$arResult["capCode"] =  htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
		
		// сохраняем параметры в сессии
		$_SESSION["CMPT_PARAMS"] = $arParams;
		
		$this->IncludeComponentTemplate();	
	}