<?php
/**
 *  скрипт запускается через ajax - в зависимости от переданных параметров происходит отображение
 *  формы или ее отправка
 * 
 *  form_id - идентификатор формы
 * 
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
/*ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);*/
//deb($_REQUEST);

$siteUTF8 = true;
$rsSites = CSite::GetByID(SITE_ID);
$arSite = $rsSites->Fetch();

if (strtolower($arSite["CHARSET"]) == "windows-1251") {
	$siteUTF8 = false;
	// конвертим реквест чтоб в письме не было кракозябр
	foreach ($_REQUEST as $key => $item) {
		
		if (is_array($item)) {
			foreach ($item as $k => $value) {
				if (!empty($_REQUEST[$key][$k])) $_REQUEST[$key][$k] = iconv('UTF-8', 'windows-1251', $_REQUEST[$key][$k]);
			}
		} else {
			if (!empty($_REQUEST[$key])) $_REQUEST[$key] = iconv('UTF-8', 'windows-1251', $_REQUEST[$key]);
		}
	}

}
//deb($_REQUEST);


if (isset($_REQUEST['form_id']) && $_REQUEST['form_id'] == 'forgot' && $_REQUEST["only_form"] == 1) {
	// форма забыли пароль
	$APPLICATION->IncludeComponent("bitrix:system.auth.forgotpasswd", "ajax_template", Array(), false );
	die('');
} elseif (isset($_REQUEST['form_id']) && $_REQUEST['form_id'] == 'forgot') {
	// форма забыли пароль
	// обрабатываем смену пароля
	if ($_REQUEST['TYPE'] == 'SEND_PWD_AJAX') {
			$result = array();
			global $USER;
			$arResult = $USER->SendPassword($_REQUEST["USER_LOGIN"], $_REQUEST["USER_EMAIL"]);
			if($arResult["TYPE"] == "OK") {
				$message =  "<span class='success' style='padding-bottom:10px;width:300px;'>Контрольная строка, а также ваши регистрационные данные были высланы по E-Mail. Пожалуйста, дождитесь прихода письма, так как контрольная строка изменяется при каждом запросе.</span>";
				$result['result'] = 'OK';
			}	
			else {
				$message = "<span class='error' style='padding-bottom:10px'>Логин или EMail не найдены.</span>";
				$result['result'] = 'ERROR';
			}		
			$result['message'] = $message;
			//корректируем результат в зависимости от кодир.
			if ($siteUTF8 == false) {
				foreach ($result as $key => $item) {
					$result[$key] = iconv('windows-1251', 'UTF-8', $result[$key]);
				}
			}
			
			$resultJson = json_encode($result);
			die($resultJson);

	}

} elseif (isset($_REQUEST['TYPE']) && $_REQUEST['TYPE'] == 'CHANGE_PWD_AJAX') {
	// смена пароля 
	$result = array();
	global $USER;
	//$USER->GetID()
	
	$arResult = $GLOBALS["USER"]->ChangePassword($_REQUEST["USER_LOGIN"], $_REQUEST["USER_CHECKWORD"], $_REQUEST["USER_PASSWORD"], $_REQUEST["USER_CONFIRM_PASSWORD"], false);
	//deb($arResult);
	
	if($arResult["TYPE"] == "OK") {
		$message =  "<span class='success'>".$arResult["MESSAGE"]."</span>";
		$result['result'] = 'OK';
	}
	else {
        if($arResult['FIELD'] == 'CHECKWORD') $arResult["MESSAGE"] = $arResult["MESSAGE"]." Попробуйте запросить новую контрольную строку для восстановления пароля.";
		$message = "<span class='error'>".$arResult["MESSAGE"]."</span>";
		$result['result'] = 'ERROR';
	}
	$result['message'] = $message;
	//корректируем результат в зависимости от кодир.
	if ($siteUTF8 == false) {
		foreach ($result as $key => $item) {
			$result[$key] = iconv('windows-1251', 'UTF-8', $result[$key]);
		}
	}
	
	$resultJson = json_encode($result);
	die($resultJson);
	
} elseif (isset($_REQUEST['form_id']) && $_REQUEST['form_id'] == 'reg') {
	// форма регистрации
	$emailAlreadyExists = false;
	
	// пробуем зарегистрировать пользователя
	if (is_array($_REQUEST['REGISTER'])) {

		$code_to_name_fields_array = array("NAME"=> "Ваше имя",
											"EMAIL"=> "Email",
											"PASSWORD"=> "Пароль",
											"CONFIRM_PASSWORD"=> "Подтверждение пароля" );
		$register_done = false;
		$arResult = array();
		// apply core fields to user defined
		$arResult["SHOW_FIELDS"] = array(
			"NAME",
			"EMAIL",
			"PASSWORD",
			"CONFIRM_PASSWORD",
		);
		$arParams["AUTH"] = "Y";
		$arResult["REQUIRED_FIELDS"] = $arResult["SHOW_FIELDS"];
		
		if(COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
			{
				//possible encrypted user password
				$sec = new CRsaSecurity();
				if(($arKeys = $sec->LoadKeys()))
				{
					$sec->SetKeys($arKeys);
					$errno = $sec->AcceptFromForm(array('REGISTER'));
					if($errno == CRsaSecurity::ERROR_SESS_CHECK)
						$arResult["ERRORS"][] = GetMessage("main_register_sess_expired");
					elseif($errno < 0)
						$arResult["ERRORS"][] = GetMessage("main_register_decode_err", array("#ERRCODE#"=>$errno));
				}
			}
		
			// check emptiness of required fields
			foreach ($arResult["SHOW_FIELDS"] as $key)
			{
				if ($key != "PERSONAL_PHOTO" && $key != "WORK_LOGO")
				{
					$arResult["VALUES"][$key] = $_REQUEST["REGISTER"][$key];
					if (in_array($key, $arResult["REQUIRED_FIELDS"]) && trim($arResult["VALUES"][$key]) == '')
						$arResult["ERRORS"][$key] = 'Поле '.$code_to_name_fields_array[$key].' обязательно для заполнения';
				}
				/*else
				{
					$_FILES["REGISTER_FILES_".$key]["MODULE_ID"] = "main";
					$arResult["VALUES"][$key] = $_FILES["REGISTER_FILES_".$key];
					if (in_array($key, $arResult["REQUIRED_FIELDS"]) && !is_uploaded_file($_FILES["REGISTER_FILES_".$key]["tmp_name"]))
						$arResult["ERRORS"][$key] = GetMessage("REGISTER_FIELD_REQUIRED");
				}*/
			}

			if(isset($_REQUEST["REGISTER"]["TIME_ZONE"]))
				$arResult["VALUES"]["TIME_ZONE"] = $_REQUEST["REGISTER"]["TIME_ZONE"];
			// check captcha
			// use captcha?
			$arResult["USE_CAPTCHA"] = COption::GetOptionString("main", "captcha_registration", "N") == "Y" ? "Y" : "N";
			if ($arResult["USE_CAPTCHA"] == "Y")
			{
				if (!$APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]))
				{
					$arResult["ERRORS"][] = "Неверный код защиты от автоматической регистрации";
				}
			}

			if(strlen($arResult["VALUES"]["EMAIL"]) > 0 && COption::GetOptionString("main", "new_user_email_uniq_check", "N") === "Y")
			{
				$res = CUser::GetList($b, $o, array("=EMAIL" => $arResult["VALUES"]["EMAIL"]));
				if($res->Fetch()) {
					$emailAlreadyExists = true;
					$emailExitstErrorText = "Пользователь с таким e-mail (" . $arResult["VALUES"]["EMAIL"] . ") уже существует.";
					$arResult["ERRORS"][] = $emailExitstErrorText;
				}	
			}
			
			if(count($arResult["ERRORS"]) > 0)
			{
				if(COption::GetOptionString("main", "event_log_register_fail", "N") === "Y")
					CEventLog::Log("SECURITY", "USER_REGISTER_FAIL", "main", false, implode("<br>", $arResult["ERRORS"]));
			}
			else // if there;s no any errors - create user
			{
				$bConfirmReq = COption::GetOptionString("main", "new_user_registration_email_confirmation", "N") == "Y";
				
				$arResult['VALUES']["CHECKWORD"] = randString(8);
				$arResult['VALUES']["~CHECKWORD_TIME"] = $DB->CurrentTimeFunction();
				$arResult['VALUES']["ACTIVE"] = $bConfirmReq? "N": "Y";
				$arResult['VALUES']["CONFIRM_CODE"] = $bConfirmReq? randString(8): "";
				$arResult['VALUES']["LID"] = SITE_ID;
		
				$arResult['VALUES']["USER_IP"] = $_SERVER["REMOTE_ADDR"];
				$arResult['VALUES']["USER_HOST"] = @gethostbyaddr($REMOTE_ADDR);
				
				if($arResult["VALUES"]["AUTO_TIME_ZONE"] <> "Y" && $arResult["VALUES"]["AUTO_TIME_ZONE"] <> "N")
					$arResult["VALUES"]["AUTO_TIME_ZONE"] = "";
		
				$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
				if($def_group != "")
					$arResult['VALUES']["GROUP_ID"] = explode(",", $def_group);
		
				$bOk = true;
		
				$GLOBALS["USER_FIELD_MANAGER"]->EditFormAddFields("USER", $arResult["VALUES"]);
		
				$events = GetModuleEvents("main", "OnBeforeUserRegister");
				while($arEvent = $events->Fetch())
				{
					if(ExecuteModuleEventEx($arEvent, array(&$arResult['VALUES'])) === false)
					{
						if($err = $APPLICATION->GetException())
							$arResult['ERRORS'][] = $err->GetString();
		
						$bOk = false;
						break;
					}
				}
				
				if ($bOk)
				{
					$user = new CUser();
					$ID = $user->Add($arResult["VALUES"]);
				}
		
				if (intval($ID) > 0)
				{
					$register_done = true;
		
					// authorize user
					if ($arParams["AUTH"] == "Y" && $arResult["VALUES"]["ACTIVE"] == "Y")
					{
						if (!$arAuthResult = $USER->Login($arResult["VALUES"]["LOGIN"], $arResult["VALUES"]["PASSWORD"]))
							$arResult["ERRORS"][] = $arAuthResult;
					}
		
					$arResult['VALUES']["USER_ID"] = $ID;
		
					$arEventFields = $arResult['VALUES'];
					unset($arEventFields["PASSWORD"]);
					unset($arEventFields["CONFIRM_PASSWORD"]);
		
					$event = new CEvent;
					$event->SendImmediate("NEW_USER", SITE_ID, $arEventFields);
					$event->SendImmediate("NEW_USER_NOTIFY", SITE_ID, $arEventFields);
					if($bConfirmReq)
						$event->SendImmediate("NEW_USER_CONFIRM", SITE_ID, $arEventFields);
				}
				else
				{
					$arResult["ERRORS"][] = $user->LAST_ERROR;
		
				}
		
				if(count($arResult["ERRORS"]) <= 0)
				{
					if(COption::GetOptionString("main", "event_log_register", "N") === "Y")
						CEventLog::Log("SECURITY", "USER_REGISTER", "main", $ID);
				}
				else
				{
					if(COption::GetOptionString("main", "event_log_register_fail", "N") === "Y")
						CEventLog::Log("SECURITY", "USER_REGISTER_FAIL", "main", $ID, implode("<br>", $arResult["ERRORS"]));
				}
		
				$events = GetModuleEvents("main", "OnAfterUserRegister");
				while ($arEvent = $events->Fetch())
					ExecuteModuleEventEx($arEvent, array(&$arResult['VALUES']));
			}

			$result = array();
			
			if ($register_done) {
				$result['result'] = "OK";
				$message =  '<h1 class="title">Вы зарегистрированы</h1>
							<div class="txt">Изменить пользовательские настройки можно в <a href="/personal/">личном кабинете</a></div>';				
			} else {
				$result['result'] = "ERROR";
				$message = "<span class='error'>".implode("<br />", $arResult["ERRORS"])."</span>";				
			}
			
			$result['message'] = $message;
				
			// если такой емэйл уже зарегистрован - и это единственная ошибка, то показываем форму восстановленя пароля
			if ($emailAlreadyExists == true && (count($arResult["ERRORS"]) == 1)) {
				
				$result['result'] = "EMAIL_EXISTS";
				
				// в том случае если регимся с отдельной страницы - возвращаем сообщение о том что имэйл уже
				// есть в бд
				// если из папап окна - возвращаем форму восстановления пароля
				if ($_REQUEST["from_auth_page"] == 1) {
					
				} else {
					ob_end_clean ();
					
					ob_start();
					$APPLICATION->IncludeComponent("bitrix:system.auth.forgotpasswd", "ajax_template", Array(), false );
					$forgotPasswdFormHtml = ob_get_clean();
					
					
					$forgotPasswdFormHtml = '<div id="autorize_inputs">'.
					$forgotPasswdFormHtml . $endPopupFormHtml;
					
					$result['form'] = $forgotPasswdFormHtml;
				}
				
				
				$result['message'] = '<span class="error">' . $emailExitstErrorText . '</span>';
			}
			
			//корректируем результат в зависимости от кодир.
			if ($siteUTF8 == false) {
				foreach ($result as $key => $item) {
					$result[$key] = iconv('windows-1251', 'UTF-8', $result[$key]);
				}
			}
			
			$resultJson = json_encode($result);
			die($resultJson);

	}

} elseif ($_REQUEST['AUTH_FORM'] == 'Y') { 
	// форма авторизации
	// если отправлены пароль и логин - пробуем авторизоваться
	
	if (isset($_REQUEST['USER_LOGIN']) && isset($_REQUEST['USER_PASSWORD'])) {
		
		$login = $_REQUEST['USER_LOGIN'];
		$pass = $_REQUEST['USER_PASSWORD'];
		$USER = new CUser;
		$arAuthResult = $USER->Login($login, $pass, "Y");
		//deb($_REQUEST);
		$result = array();
		if ($arAuthResult === true) {
			// пользователь найден - пароли совпали
			$result['result'] = 'AUTH_OK';
			$message = "";

			$_REQUEST['backurl'] = str_replace("wrong_pass=1", "", $_REQUEST['backurl']);
			
			if (!empty($_SESSION["REFFERER_FOR_AUTH"])) $redirectUrl = $_SESSION["REFFERER_FOR_AUTH"];
			else $redirectUrl = $_REQUEST['backurl'];
			
			$_SESSION["REFFERER_FOR_AUTH"] = '';
			
			header("Location: ".$redirectUrl);
			exit();
		}
		else {
			$result['result'] = 'AUTH_ERROR';
			$message =  "<span class='error'>Неверный логин или пароль.</span>";
			// возвращаем на страницу с ошибкой 

			header("Location: #SITE_DIR#auth/index.php?wrong_pass=1");
			exit();
		}
		$result['message'] = $message;
		$resultJson = json_encode($result);		
		die($resultJson);

	}
	
	
}

die();
?>
