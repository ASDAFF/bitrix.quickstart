<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arParams["TEXT_LINK"] = trim($arParams["TEXT_LINK"]);
$arParams["IMAGE_LINK"] = trim($arParams["IMAGE_LINK"]);
$arParams["REDIRECT_PAGE"] = trim($arParams["REDIRECT_PAGE"]); 

require_once 'include/LoginzaAPI.class.php';
require_once 'include/LoginzaUserProfile.class.php';

global $USER;
global $APPLICATION;
$LoginzaAPI = new LoginzaAPI();

if (!empty($_POST['token'])) {
	$current_id= COption::GetOptionString('infospice.loginzapro', 'id', '');
	$current_sid= COption::GetOptionString('infospice.loginzapro', 'sid', '');
	$plus_variables = $_POST['token'].$current_sid;
	$sig = md5($plus_variables);
	$UserProfile = $LoginzaAPI->getAuthInfo($_POST['token'],$current_id,$sig);
	if (!empty($UserProfile->error_type)) {
		$arResult['ERRORS'][] = $UserProfile->error_type.': '.$UserProfile->error_message;
	} 
	elseif (empty($UserProfile)) {
		$arResult['ERRORS'][] = GetMessage('TMP_ERROR');
	}
	else {
		$LoginzaProfile = new LoginzaUserProfile($UserProfile);
		
		$arResult['USER']['GENERATED_NAME'] = $LoginzaProfile->genDisplayName();
		$arResult['USER']['NICKNAME'] = $LoginzaProfile->genNickname();
		$arResult['USER']['FULL_NAME'] = $LoginzaProfile->genFullName();
		$arResult['USER']['URL'] = $LoginzaProfile->genUserSite();
		$arResult['USER']['EMAIL'] = $LoginzaProfile->genEmail();
		$arResult['USER']['PROVIDER'] = $LoginzaProfile->genProvider();
		$arResult['USER']['IDENTITY'] = $LoginzaProfile->genIdentity();
		$arResult['USER']['DOB'] = $LoginzaProfile->genDob();
		$arResult['USER']['UID'] = $LoginzaProfile->genUID();
		$arResult['USER']['GENDER'] = $LoginzaProfile->genGender();
	
		if($arResult['USER']['GENERATED_NAME']) $arResult['USER']['DISPLAY_NAME'] = $arResult['USER']['GENERATED_NAME'];
		else if($arResult['USER']['FULL_NAME']) $arResult['USER']['DISPLAY_NAME'] = $arResult['USER']['FULL_NAME'];
		else $arResult['USER']['DISPLAY_NAME'] = $arResult['USER']['NICKNAME'];
		
		// если на сайте кодировка win-1251, то преобразуем ответ в эту кодировку
		if(SITE_CHARSET == "windows-1251") {
			$arResult['USER']['GENERATED_NAME'] = utf8win1251($arResult['USER']['GENERATED_NAME']); 
			$arResult['USER']['NICKNAME'] 		= utf8win1251($arResult['USER']['NICKNAME']);
			$arResult['USER']['FULL_NAME'] 		= utf8win1251($arResult['USER']['FULL_NAME']);
		}
	
		// проверяем есть ли пользователь в БД.	Если есть - то авторизуем, нет  - регистрируем и авторизуем			
		$rsUsers = CUser::GetList(
			($by="email"), 
			($order="desc"), 
			array(
				"EXTERNAL_AUTH_ID" => $arResult['USER']["IDENTITY"],
				"ACTIVE" => "Y"
			)
		);
		$arUser = $rsUsers->GetNext();
		if($arUser["EXTERNAL_AUTH_ID"] == $arResult['USER']["IDENTITY"]) {
			// такой пользователь есть, авторизуем его 
			$USER->Authorize($arUser["ID"]); 
			
			if($arParams["REDIRECT_PAGE"] != "")
				LocalRedirect($arParams["REDIRECT_PAGE"]);
			else
				LocalRedirect($APPLICATION->GetCurPageParam("", array("logout")));
		}
		else {
			// регистрируем пользователя, и добавляем его в группы, указанные в параметрах
			$user = new CUser;
			$GroupID = "2";
			$passw = $LoginzaProfile->genRandomPassword(8);
			
			$stmp = MakeTimeStamp($arResult['USER']["DOB"], "YYYY-MM-DD");
			$birthday = ConvertTimeStamp($stmp);
			
			if(is_array($arParams["GROUP_ID"])) 
				$GroupID = $arParams["GROUP_ID"];
				
			if(!$arResult['USER']["EMAIL"])
				$arResult['USER']["EMAIL"] = "yourmail@domain.com";
				
			# надо проверить есть ли такой логин в БД
			$rsUsers = CUser::GetList(
				($by="email"), 
				($order="desc"), 
				array(
					/*"LOGIN_EQUAL" => $arResult['USER']["NICKNAME"],*/
					"LOGIN" => $arResult['USER']["NICKNAME"],
					"ACTIVE" => "Y"
				)
			);
			while ($arUser = $rsUsers->GetNext()){
				$count_user_id[] = $arUser["ID"];
			}
			
			if(count($count_user_id) > 0){
				$arResult['USER']["NICKNAME"] = $arResult['USER']["NICKNAME"]."_".count($count_user_id);
			}
							
			$arFields = Array(
				"NAME"              => $arResult['USER']['FULL_NAME'],
				"EMAIL"             => $arResult['USER']["EMAIL"],
				"LOGIN"             => $arResult['USER']["NICKNAME"],
				"PERSONAL_GENDER"	=> $arResult['USER']["GENDER"],
				"PERSONAL_WWW"		=> $arResult['USER']["URL"],
				"ADMIN_NOTES"		=> $arResult['USER']["PROVIDER"], // provider
				"PERSONAL_BIRTHDAY"	=> $birthday,
				"ACTIVE"            => "Y",
				"GROUP_ID"          => $GroupID,
				"EXTERNAL_AUTH_ID"	=> $arResult['USER']["IDENTITY"],
				"PASSWORD"          => $passw,
				"CONFIRM_PASSWORD"  => $passw,
			);		
			$UserID = $user->Add($arFields);
			if (intval($UserID)>0) {
				$USER->Authorize($UserID);
				
				if($arParams["REDIRECT_PAGE"] != "")
					LocalRedirect($arParams["REDIRECT_PAGE"]);
				else
					LocalRedirect($APPLICATION->GetCurPageParam("", array("logout")));
			}
			else
				$arResult["ERRORS"][] = $user->LAST_ERROR;
		}
	}
}
$arResult['WIDGET_URL'] = $LoginzaAPI->getWidgetUrl();
$this->IncludeComponentTemplate();

?>