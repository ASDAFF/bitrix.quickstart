<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("KHAYR_MAIN_COMMENT_IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}
if (!CModule::IncludeModule("parnas.khayrcomment"))
{
	ShowError(GetMessage("KHAYR_MAIN_COMMENT_MODULE_NOT_INSTALLED"));
	return;
}

$arParams["OBJECT_ID"] = intval($arParams["OBJECT_ID"]);
$arParams["COUNT"] = intval($arParams["COUNT"]);
if ($arParams["COUNT"] <= 0)
	$arParams["COUNT"] = 10;
$arParams["MAX_DEPTH"] = intval($arParams["MAX_DEPTH"]);
$arParams["JQUERY"] = ($arParams["JQUERY"] == "Y" ? "Y" : "N");
$arParams["MODERATE"] = (($arParams["MODERATE"] == "Y") && !$GLOBALS["USER"]->IsAdmin());
$arParams["LEGAL"] = (($arParams["LEGAL"] == "Y") && !$GLOBALS["USER"]->IsAdmin());
$arParams["LEGAL_TEXT"] = trim($arParams["LEGAL_TEXT"]);
if (!$arParams["LEGAL_TEXT"])
	$arParams["LEGAL_TEXT"] = GetMessage("KHAYR_MAIN_COMMENT_LEGAL_TEXT_DEFAULT");
$arParams["CAN_MODIFY"] = ($arParams["CAN_MODIFY"] == "N" ? "N" : "Y");
$arParams["NON_AUTHORIZED_USER_CAN_COMMENT"] = ($arParams["NON_AUTHORIZED_USER_CAN_COMMENT"] == "N" ? "N" : "Y");
$arParams["REQUIRE_EMAIL"] = ($arParams["REQUIRE_EMAIL"] == "N" ? "N" : "Y");
$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] == "Y") && !$GLOBALS["USER"]->IsAuthorized());
$arParams["AUTH_PATH"] = trim($arParams["AUTH_PATH"]);
if (!$arParams["AUTH_PATH"])
	$arParams["AUTH_PATH"] = "/auth/";
$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if (strlen($arParams["ACTIVE_DATE_FORMAT"]) <= 0)
	$arParams["ACTIVE_DATE_FORMAT"] = $GLOBALS["DB"]->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
$arParams["ASNAME"] = "FULL_NAME"; // will be deleted later
$arParams["LOAD_AVATAR"] = ($arParams["LOAD_AVATAR"] == "Y" ? "Y" : "N");
$arParams["LOAD_MARK"] = ($arParams["LOAD_MARK"] == "Y");
$arParams["LOAD_DIGNITY"] = ($arParams["LOAD_DIGNITY"] == "Y");
$arParams["LOAD_FAULT"] = ($arParams["LOAD_FAULT"] == "Y");
if (!is_array($arParams["ADDITIONAL"]) && !empty($arParams["ADDITIONAL"]))
	$arParams["ADDITIONAL"] = array($arParams["ADDITIONAL"]);
if (!is_array($arParams["ADDITIONAL"]) && empty($arParams["ADDITIONAL"]))
	$arParams["ADDITIONAL"] = array();
foreach ($arParams["ADDITIONAL"] as $i => $v)
{
	if (trim($v))
		$arParams["ADDITIONAL"][$i] = trim($v);
	else
		unset($arParams["ADDITIONAL"][$i]);
}
$arParams["ALLOW_RATING"] = ($arParams["ALLOW_RATING"] == "Y" ? "Y" : "N");
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"] == "Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"] == "Y";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"] == "Y";
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"] == "Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"] == "Y";

foreach ($_POST as $key => $val)
	$arResult["POST"][$key] = htmlspecialcharsbx(KhayRComment::CheckEncode(trim($val)));

if (!$arResult["POST"]["NONUSER"] && isset($_COOKIE["KHAYR_COMMENT_NONUSER"]) && $_COOKIE["KHAYR_COMMENT_NONUSER"])
	$arResult["POST"]["NONUSER"] = htmlspecialcharsbx(trim($_COOKIE["KHAYR_COMMENT_NONUSER"]));
if (!$arResult["POST"]["EMAIL"] && isset($_COOKIE["KHAYR_COMMENT_EMAIL"]) && $_COOKIE["KHAYR_COMMENT_NONUSER"])
	$arResult["POST"]["EMAIL"] = htmlspecialcharsbx(trim($_COOKIE["KHAYR_COMMENT_EMAIL"]));

$arResult["USER"] = KhayRComment::GetAuthor($GLOBALS["USER"]->GetID());
if ($arResult["USER"]["ID"])
{
	$arResult["POST"]["NONUSER"] = $arResult["USER"]["FULL_NAME"];
	if ($arResult["USER"]["EMAIL"])
		$arResult["POST"]["EMAIL"] = $arResult["USER"]["EMAIL"];
}

$errors = array();
$success = false;

$arResult["CAN_COMMENT"] = (($arParams["NON_AUTHORIZED_USER_CAN_COMMENT"] == "Y") || $GLOBALS["USER"]->IsAuthorized());
$arResult["LOAD_EMAIL"] = ((!$arResult["USER"]["ID"] || ($arResult["USER"]["ID"] && !$arResult["USER"]["EMAIL"])) && $arParams["REQUIRE_EMAIL"] == "Y");
$arResult["LOAD_AVATAR"] = (!$arResult["USER"]["AVATAR"] && $arParams["LOAD_AVATAR"] == "Y");

if (strlen($arResult["POST"]["ACTION"]) > 0)
{
	if ($arParams["LEGAL"])
	{
		if ($arResult["POST"]["LEGAL"] != "Y")
			$errors[] = GetMessage("KHAYR_MAIN_COMMENT_LEGAL_WRONG");
	}
	
	if ($arParams["USE_CAPTCHA"])
	{
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
		$captcha_code = $arResult["POST"]["captcha_sid"];
		$captcha_word = $arResult["POST"]["captcha_word"];
		$cpt = new CCaptcha();
		$captchaPass = COption::GetOptionString("main", "captcha_password", "");
		if ((strlen($captcha_word) > 0) && (strlen($captcha_code) > 0))
		{
			if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
				$errors[] = GetMessage("KHAYR_MAIN_COMMENT_CAPTCHA_WRONG");
		}
		else
		{
			$errors[] = GetMessage("KHAYR_MAIN_COMMENT_CAPTHCA_EMPTY");
		}
	}
	
	if ($arResult["POST"]["ACTION"] == "add")
	{
		if (!$arResult["POST"]["NONUSER"])
			$errors[] = GetMessage("KHAYR_MAIN_COMMENT_NONUSER_EMPTY");
		
		if ($arResult["LOAD_EMAIL"])
		{
			if (!$arResult["POST"]["EMAIL"])
				$errors[] = GetMessage("KHAYR_MAIN_COMMENT_EMAIL_EMPTY");
			elseif (!check_email($arResult["POST"]["EMAIL"]))
				$errors[] = GetMessage("KHAYR_MAIN_COMMENT_EMAIL_WRONG");
		}
	}
	
	if (!$arResult["POST"]["MESSAGE"] && ($arResult["POST"]["ACTION"] != "delete"))
		$errors[] = GetMessage("KHAYR_MAIN_COMMENT_MESSAGE_EMPTY");
	
	// если у авторизованного юзера есть аватар, в форме не требуется загрузка нового аватара, и, соответственно, в инфоблок аватар не сохраняется
	// сейчас в инфоблок всегда сохраняются только аватары неавторизованных юзеров, при этом в каждом элементе он должен быть загружен заново
	// можно сделать сохранение аватара в куки, но все равно выводить форму для юзеров без аватара
	// можно и для авторизованных сохранять аватар в инфоблок и обновлять при обновлении у юзера
	$filetmp = false;
	if ($arResult["LOAD_AVATAR"])
	{
		$fileerror = false;
		if (!empty($_FILES) && !empty($_FILES['AVATAR']) && !empty($_FILES['AVATAR']['name']))
		{
			$fileMeta = $_FILES['AVATAR'];
			$allowedExts = array("png", "jpg", "jpeg");
			$extension = strtolower(end(explode(".", $fileMeta["name"])));
			if (
				(
					($fileMeta["type"] == "image/png")
					|| ($fileMeta["type"] == "image/jpeg")
					|| ($fileMeta["type"] == "image/pjpeg")
					|| ($fileMeta["type"] == "application/octet-stream")
				)
				&& in_array($extension, $allowedExts)
			)
			{
				if ($fileMeta["error"] > 0)
				{
					$fileerror = $fileMeta["error"];
				}
				else
				{
					if (!is_dir($_SERVER["DOCUMENT_ROOT"]."/upload/parnas.khayrcomment/tmp/"))
						mkdir($_SERVER["DOCUMENT_ROOT"]."/upload/parnas.khayrcomment/tmp/", 0777, true);
					$filetmp = $_SERVER["DOCUMENT_ROOT"]."/upload/parnas.khayrcomment/tmp/".md5($fileMeta["name"])."-".time().".".$extension;
					move_uploaded_file($fileMeta["tmp_name"], $filetmp);
				}
			}
			else
			{
				$fileerror = 'Wrong file';
			}
		}
		if ($fileerror)
		{
			$errors[] = GetMessage("KHAYR_MAIN_COMMENT_AVATAR_WRONG");//$fileerror;
			$filetmp = false;
		}
		if (!$fileerror && file_exists($filetmp) && $arResult["USER"]["ID"])
		{
			$avatar = CFile::MakeFileArray($filetmp);
			if ($arResult["USER"]["ALL"]["PERSONAL_PHOTO"])
			{
				$avatar['del'] = "Y";           
				$avatar['old_file'] = $arResult["USER"]["ALL"]["PERSONAL_PHOTO"];
			}
			$upduser = new CUser;
			$upduser->Update($arResult["USER"]["ID"], Array("PERSONAL_PHOTO" => $avatar));
		}
	}
	
	setcookie("KHAYR_COMMENT_NONUSER", $arResult["POST"]["NONUSER"], time()+3600*24, "/");
	setcookie("KHAYR_COMMENT_EMAIL", $arResult["POST"]["EMAIL"], time()+3600*24, "/");
	
	if (!$errors)
	{
		$additional = array();
		foreach ($arParams["ADDITIONAL"] as $addit)
		{
			if (isset($arResult["POST"][$addit]))
				$additional[$addit] = $arResult["POST"][$addit];
			elseif (isset($arResult["POST"][urldecode($addit)]))
				$additional[$addit] = $arResult["POST"][urldecode($addit)];
			elseif (isset($arResult["POST"][urlencode($addit)]))
				$additional[$addit] = $arResult["POST"][urlencode($addit)];
		}
		$additional = serialize($additional);
		switch ($arResult["POST"]["ACTION"])
		{
			case "add":
				if (KhayRComment::Add(array(
					"object" => $arParams["OBJECT_ID"],
					"parent" => $arResult["POST"]["PARENT"],
					"level" => $arResult["POST"]["DEPTH"],
					"text" => $arResult["POST"]["MESSAGE"],
					"mark" => $arResult["POST"]["MARK"],
					"dignity" => $arResult["POST"]["DIGNITY"],
					"fault" => $arResult["POST"]["FAULT"],
					"additional" => $additional,
					"author" => $arResult["USER"]["ID"], 
					"nonuser" => $arResult["POST"]["NONUSER"],
					"email" => $arResult["POST"]["EMAIL"],
					"avatar" => $filetmp,
					"active" => ($arParams["MODERATE"] ? "N" : "Y")
				)))
				{
					$on_add_email = "N";
					$use_site = COption::GetOptionString("parnas.khayrcomment", "use_on_sites_".SITE_ID, "");
					if ($use_site)
					{
						$on_add_email = COption::GetOptionString("parnas.khayrcomment", "ON_ADD_EMAIL_".SITE_ID, "N");
					}
					else
					{
						$on_add_email = COption::GetOptionString("parnas.khayrcomment", "ON_ADD_EMAIL", "N");
					}
					if ($on_add_email == "Y")
					{
						$email = "";
						if ($use_site)
						{
							$email = COption::GetOptionString("parnas.khayrcomment", "EMAIL_".SITE_ID, "");
						}
						else
						{
							$email = COption::GetOptionString("parnas.khayrcomment", "EMAIL", "");
						}
						if (!$email)
						{
							$email = COption::GetOptionString("main", "email_from", "");
						}
						if ($email)
						{
							$arEventFields = array(
								'OBJECT_ID' => $arParams["OBJECT_ID"],
								'NAME' => ($arResult["USER"]["ID"] ? "[".$arResult["USER"]["ID"]."] " : "").$arResult["POST"]["NONUSER"].($arResult["POST"]["EMAIL"] ? " (".$arResult["POST"]["EMAIL"].")" : ""),
								'NONUSER' => $arResult["POST"]["NONUSER"],
								'EMAIL' => $arResult["POST"]["EMAIL"],
								'MESSAGE' => $arResult["POST"]["MESSAGE"],
								'URL' => ($_SERVER["HTTPS"] ? "https://" : "http://").$_SERVER["HTTP_HOST"]."/bitrix/admin/parnas.khayrcomment_list.php?lang=".LANGUAGE_ID,
								'EMAIL_TO' => $email
							);
							CEvent::Send('KHAYR_COMMENT_ADD', SITE_ID, $arEventFields);
						}
					}
					
					$success = $arParams["MODERATE"] ? GetMessage("KHAYR_MAIN_COMMENT_SUC_MODER") : GetMessage("KHAYR_MAIN_COMMENT_SUC_ADD");
					
					$arResult["POST"]["MESSAGE"] = "";
					if (isset($arResult["POST"]["MARK"]))
						$arResult["POST"]["MARK"] = 0;
					if (isset($arResult["POST"]["DIGNITY"]))
						$arResult["POST"]["DIGNITY"] = "";
					if (isset($arResult["POST"]["FAULT"]))
						$arResult["POST"]["FAULT"] = "";
				}
				else
				{
					$errors[] = $GLOBALS["KHAYR_MAIN_COMMENT_COMMENT_ERROR"];
				}
				break;
			case "update":
				if (KhayRComment::Update(intval($arResult["POST"]["COM_ID"]), $arResult["POST"]["MESSAGE"]))
				{
					$success = GetMessage("KHAYR_MAIN_COMMENT_SUC_UPDATE");
					$arResult["POST"]["MESSAGE"] = "";
				}
				else
				{
					$errors[] = $GLOBALS["KHAYR_MAIN_COMMENT_COMMENT_ERROR"];
				}
				break;
			case "delete":
				if (KhayRComment::Delete(intval($arResult["POST"]["COM_ID"])))
				{
					$success = GetMessage("KHAYR_MAIN_COMMENT_SUC_DELETE");
				}
				break;
		}
	}
}

if ($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arNavParams = array(
		"nPageSize" => $arParams["COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
}
else
{
	$arNavParams = array(
		"nTopCount" => $arParams["COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	);
}

$arResult = array_merge($arResult, KhayRComment::Show($arParams, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "PROPERTY_*"), array("DATE_CREATE" => "DESC"), $arNavParams));

if ($arParams["USE_CAPTCHA"])
	$arResult["capCode"] = $APPLICATION->CaptchaGetCode();

$arResult["ERROR_MESSAGE"] = implode(" ", $errors);
$arResult["ERROR_MESSAGES"] = $errors;
$arResult["SUCCESS"] = $success;

if ($arParams["JQUERY"] == "Y")
{
	$APPLICATION->AddHeadScript("/bitrix/modules/parnas.khayrcomment/libs/jQuery/jquery-1.11.3.min.js");
}
$this->IncludeComponentTemplate();
?>