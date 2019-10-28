<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @var CBitrixComponent $this
 * @var array $arParams
 * @var array $arResult
 * @var string $componentPath
 * @var string $componentName
 * @var string $componentTemplate
 * @global CMain $APPLICATION
 * @global CDatabase $DB
 * @global CUser $USER
 */

$ALX = "FID".$arParams["FORM_ID"];

$ALREADY_SEND_MESSAGE = $_SESSION['alx_send_success'.$ALX] == 'Y';

if($_SERVER["REQUEST_METHOD"]=="POST")
{
        if($_POST["OPEN_POPUP"] == $ALX || $_POST["FEEDBACK_FORM_".$ALX])
        {
                CUtil::JSPostUnescape();
                $APPLICATION->RestartBuffer();
        }
}

if($arParams['ALX_LINK_POPUP']=='Y')
{
        if($_SERVER["REQUEST_METHOD"]=="POST")
        {
                if($_POST["OPEN_POPUP"] == $ALX || $_POST["FEEDBACK_FORM_".$ALX])
                {
                        $arParams['ALX_GET_POPUP'.$ALX] = 'N';

                        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
                }
                elseif(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
                {
                        return;
                }
        }
}
else
{
        $arParams['ALX_GET_POPUP'.$ALX] = 'Y';
}

if(is_array($arParams["PROPERTY_FIELDS_REQUIRED"]))
{
        foreach($arParams["PROPERTY_FIELDS_REQUIRED"] as $k => $v)
                $arParams["PROPERTY_FIELDS_REQUIRED"][$k] = $v."_".$ALX;
}

$arResult["FORM_ERRORS"] = Array();

if(!CModule::IncludeModule("iblock"))
{
        ShowError(GetMessage("IB_MODULE_NOT_INSTALLED"));
        return;
}

if(!CModule::IncludeModule("altasib.feedback"))
{
        ShowError(GetMessage("ALX_FEEDBACK_NOT_INSTALLED"));
        return;
}

$arFilter = array("PROPERTY_TYPE" => "F");

$cachePath = "/altasib/feedback";

$obCache = new CPHPCache();
if($obCache->InitCache(86400, serialize(array($arFilter, $arParams["IBLOCK_ID"], "iblock")), $cachePath))
{
        $arTypeFile = $obCache->GetVars();
}
elseif($obCache->StartDataCache())
{
        $arTypeFile = array();
        $rsIB = CIBlock::GetProperties($arParams["IBLOCK_ID"], array(), $arFilter);
        while($arIBp = $rsIB->Fetch())
        {
                $arTypeFile[$arIBp["CODE"]]["FILE_TYPE"] = $arIBp["FILE_TYPE"];
                $arTypeFile[$arIBp["CODE"]]["NAME"] = $arIBp["NAME"];
        }
        $obCache->EndDataCache($arTypeFile);
}
if(!isset($arTypeFile))
        $arTypeFile = array();

$codeFileFields = count($_POST["codeFileFields"]);

if(is_array($_FILES["myFile"]["name"]))
{
        foreach($_FILES["myFile"]["name"] as $k => $value)
        {
                $codeID = trim(htmlspecialcharsEx($_POST["codeFileFields"][$k]));
                $code = str_replace("_".$ALX, "", trim(htmlspecialcharsEx($_POST["codeFileFields"][$k])));

                $arParamTypeFile = array();
                $arParamTypeFileTrim = array();
                if(!empty($arTypeFile[$code]["FILE_TYPE"]))
                {
                        $arParamTypeFile = explode(",", $arTypeFile[$code]["FILE_TYPE"]);
                        foreach($arParamTypeFile as $v)
                                $arParamTypeFileTrim[] = trim($v);
                }

                $params = Array(
                        "max_len" => "200",
                        "change_case" => "L",
                        "replace_space" => "_",
                        "replace_other" => ".",
                        "delete_repeat_replace" => "true",
                );
                if(!is_array($_FILES["myFile"]["name"][$k]))
                {
                        $filename = $_FILES["myFile"]["name"][$k];
                        $arFileName = explode(".", $filename);

                        if((in_array($arFileName[count($arFileName)-1], $arParamTypeFileTrim) || empty($arTypeFile[$code]["FILE_TYPE"])) && !empty($_FILES["myFile"]["tmp_name"][$k]))
                        {
                                $arFile = Array();
                                $_FILES["myFile"]["name"][$k] = CUtil::translit($_FILES["myFile"]["name"][$k], "ru", $params);
                                $arFile["name"] = $_FILES["myFile"]["name"][$k];
                                $arFile["size"] = $_FILES["myFile"]["size"][$k];
                                $arFile["tmp_name"] = $_FILES["myFile"]["tmp_name"][$k];
                                $arFile["type"] = $_FILES["myFile"]["type"][$k];
                                $arFile["description"] = "";
                                $PROPS[$code] = $arFile;
                        }
                        elseif(!empty($_FILES["myFile"]["tmp_name"][$k]))
                        {
                                $errorFile[$codeID] .= GetMessage("ALX_FIELD1").$arTypeFile[$code]["NAME"].'". '.GetMessage("DISABLE_FILE");
                                $arErrFile[$codeID] .= GetMessage("ALX_FIELD1").$arTypeFile[$code]["NAME"].'". '.GetMessage("DISABLE_FILE");
                        }
                        elseif(in_array($k, $arParams["PROPERTY_FIELDS_REQUIRED"]) && $_FILES["myFile"]["error"][$k] == 4)
                                $errorFile[$codeID] .= GetMessage("ALX_FIELD1").$arTypeFile[$code]["NAME"].'". '.GetMessage("EMPTY_FILE");
                }
                else
                {
                        $n = 0;
                        foreach($_FILES["myFile"]["name"][$k] as $mk => $mval)
                        {
                                $filename = $_FILES["myFile"]["name"][$k][$mk];
                                $arFileName = explode(".", $filename);

                                if((in_array($arFileName[count($arFileName)-1], $arParamTypeFileTrim) || empty($arTypeFile[$code]["FILE_TYPE"])) && !empty($_FILES["myFile"]["tmp_name"][$k][$mk]))
                                {
                                        $arFile = Array();
                                        $_FILES["myFile"]["name"][$k][$mk] = CUtil::translit($_FILES["myFile"]["name"][$k][$mk], "ru", $params);
                                        $arFile["name"] = $_FILES["myFile"]["name"][$k][$mk];
                                        $arFile["size"] = $_FILES["myFile"]["size"][$k][$mk];
                                        $arFile["tmp_name"] = $_FILES["myFile"]["tmp_name"][$k][$mk];
                                        $arFile["type"] = $_FILES["myFile"]["type"][$k][$mk];
                                        $arFile["description"] = "";
                                        $PROPS[$code]["n".$n++] = $arFile;
                                }
                                elseif(!empty($_FILES["myFile"]["tmp_name"][$k][$mk]))
                                {
                                        $arErrFile[$codeID] .= GetMessage("ALX_FIELD1").$arTypeFile[$code]["NAME"].'". '.GetMessage("DISABLE_FILE");
                                        $errorFile[$codeID] .= GetMessage("ALX_FIELD1").$arTypeFile[$code]["NAME"].'". '.GetMessage("DISABLE_FILE");
                                }
                                elseif(in_array($k, $arParams["PROPERTY_FIELDS_REQUIRED"]) && $_FILES["myFile"]["error"][$k][$mk] == 4)
                                        $errorFile[$codeID] .= GetMessage("ALX_FIELD1").$arTypeFile[$code]["NAME"].'". '.GetMessage("EMPTY_FILE");
                        }
                }
        }
}

// checking for empty required files (ajax)
if(is_array($_POST["codeFileFields"]))
{
        $countFileFields = count($_FILES["myFile"]["name"]);
        if($codeFileFields > $countFileFields)
        {
                foreach($_POST["codeFileFields"] as $k => $v)
                {
                        if(in_array($v, $arParams["PROPERTY_FIELDS_REQUIRED"]))
                        {
                                if(empty($_FILES["myFile"]["name"][$v]) && empty($_FILES["myFile"]["name"][$k]))
                                {
                                        $code = str_replace("_".$ALX, "", trim(htmlspecialcharsEx($v)));
                                        $codeID = trim(htmlspecialcharsEx($v));
                                        $errorFile[$codeID] .= GetMessage("ALX_FIELD1").$arTypeFile[$code]["NAME"].'". '.GetMessage("EMPTY_FILE");
                                }
                        }
                }
        }
}

$arParams["IBLOCK_TYPE"] = trim(htmlspecialcharsEx($arParams["IBLOCK_TYPE"]));
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["EVENT_TYPE"] = trim(htmlspecialcharsEx($arParams["EVENT_TYPE"]));

$arParams["ACTIVE_ELEMENT"] = trim(htmlspecialcharsEx($arParams["ACTIVE_ELEMENT"]));
$arParams["USE_CAPTCHA"] = $arParams["USE_CAPTCHA"] == "Y" && !($arParams["NOT_CAPTCHA_AUTH"] != "N" && $USER->IsAuthorized());

if($arParams["SEND_MAIL"] == "Y")
{
        $arParams["USER_EVENT"] = trim(htmlspecialcharsEx($arParams["USER_EVENT"]));
        if(strlen($arParams["USER_EVENT"]) <= 0)
                $arParams["USER_EVENT"] = "ALX_FEEDBACK_FORM_SEND_MAIL";
}

if(strlen($arParams["EVENT_TYPE"]) <= 0)
        $arParams["EVENT_TYPE"] = "ALX_FEEDBACK_FORM";

// parameters name of property fields saving name, email and phone for auto-complete
$arAutompleteParams = array(
        "PROPS_AUTOCOMPLETE_NAME",
        "PROPS_AUTOCOMPLETE_EMAIL",
        "PROPS_AUTOCOMPLETE_PERSONAL_PHONE"
);

if($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["FEEDBACK_FORM_".$ALX]
        && $arParams["SECTION_FIELDS_ENABLE"] == "Y" && $_POST["REFRESH"] == "Y")
{
        $_POST["type_question_".$ALX] = trim(htmlspecialcharsEx($_POST["type_question_".$ALX]));

        if(!empty($arParams["SECTION_FIELDS".$_POST["type_question_".$ALX]]))
        {
                $arResult["CURSECT_FIELDS"] = $arCurFields = $arParams["SECTION_FIELDS".trim($_POST["type_question_".$ALX])];
        }
        $ALREADY_SEND_MESSAGE = false;
}
elseif($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["FEEDBACK_FORM_".$ALX] == 'Y' && check_bitrix_sessid())
{
        $arFields = $_POST["FIELDS"];

        if(!is_array($arFields))
                $arFields = Array();

        $arResult["POST"] = "Y";
        $arFieldsName = Array();

        $rsProp = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
        while($arrProp = $rsProp->Fetch())
                $arFieldsName[$arrProp["CODE"]."_".$ALX] = $arrProp;

        foreach($arParams["PROPERTY_FIELDS_REQUIRED"] as $k => $v)
        {
                if(is_array($_POST["codeFileFields"]))
                {
                        if(!array_key_exists($v, $arFields) && !in_array($v, $_POST["codeFileFields"]) && $v != "FEEDBACK_TEXT_".$ALX){
                                $arResult["FORM_ERRORS"]["EMPTY_FIELD"][$v] = GetMessage("ALX_FIELD1").$arFieldsName[$v]["NAME"].GetMessage("ALX_FIELD2");
                        }
                }
                else
                {
                        if(!array_key_exists($v, $arFields) && $v != "FEEDBACK_TEXT_".$ALX){
                                $arResult["FORM_ERRORS"]["EMPTY_FIELD"][$v] = GetMessage("ALX_FIELD1").$arFieldsName[$v]["NAME"].GetMessage("ALX_FIELD2");
                        }
                }
        }

        if($arParams['USER_CONSENT'] == 'Y')
        {
                if(!isset($_POST['alx_fb_agreement']))
                {
                        $arResult["FORM_ERRORS"]["EMPTY_FIELD"]["alx_fb_agreement"] = GetMessage('AFBF_ERROR_TEXT_AGREEMENT');
                }
        }
        foreach($arFields as $k => $v)
        {
                $k = htmlspecialcharsbx($k);
                $k = str_replace("_".$ALX, "", $k);
                if(!is_array($v))
                        $val = trim($v);

                if($k != "myFile")
                {
                        if(is_array($v))
                        {
                                $PROPS[$k] = $v;
                        }
                        elseif(strlen($val) <= 0)
                        {
                                if(in_array($k."_".$ALX, $arParams["PROPERTY_FIELDS_REQUIRED"]))
                                        $arResult["FORM_ERRORS"]["EMPTY_FIELD"][$k."_".$ALX] = GetMessage("ALX_FIELD1").$arFieldsName[$k."_".$ALX]["NAME"].GetMessage("ALX_FIELD2");
                        }
                        elseif($k == "EMAIL" && strlen($val) > 0)
                        {
                                if(check_email($val))
                                        $PROPS[$k] = $val;
                                else
                                        $arResult["FORM_ERRORS"]["EMPTY_FIELD"][$k."_".$ALX] = GetMessage("INCORRECT_MAIL");
                        }

                        else
                        {
                                if($k == "EMAIL")
                                {
                                        if(check_email($val))
                                                $PROPS[$k] = $val;
                                        else
                                                $arResult["FORM_ERRORS"]["EMPTY_FIELD"][$k."_".$ALX] = GetMessage("INCORRECT_MAIL");

                                        if($arParams["PROPS_AUTOCOMPLETE_VETO"]=="Y" && $USER->IsAuthorized())
                                        {
                                                if(is_array($arParams["PROPS_AUTOCOMPLETE_EMAIL"])
                                                        && !empty($arParams["PROPS_AUTOCOMPLETE_EMAIL"])
                                                )
                                                {
                                                        if(in_array($k, $arParams["PROPS_AUTOCOMPLETE_EMAIL"]))
                                                                $PROPS[$k] = $USER->GetEmail();
                                                }
                                                unset($arResult["FORM_ERRORS"]["EMPTY_FIELD"][$k."_".$ALX]);
                                        }
                                }
                                else
                                {
                                        if($arFieldsName[$k."_".$ALX]["USER_TYPE"] == "HTML")
                                        {
                                                $PROPS[$k] = array(
                                                        "VALUE" => array(
                                                                "TEXT" => $val,
                                                                "TYPE" => "TEXT"
                                                        )
                                                );
                                        }
                                        else
                                        {
                                                if($arParams["PROPS_AUTOCOMPLETE_VETO"]=="Y" && $USER->IsAuthorized())
                                                {
                                                        if(is_array($arParams["PROPS_AUTOCOMPLETE_NAME"])
                                                                && !empty($arParams["PROPS_AUTOCOMPLETE_NAME"])
                                                        )
                                                        {
                                                                if(in_array($k, $arParams["PROPS_AUTOCOMPLETE_NAME"]))
                                                                        $val = $USER->GetFormattedName(false);
                                                        }

                                                        if(is_array($arParams["PROPS_AUTOCOMPLETE_PERSONAL_PHONE"])
                                                                && !empty($arParams["PROPS_AUTOCOMPLETE_PERSONAL_PHONE"])
                                                        )
                                                        {
                                                                if(in_array($k, $arParams["PROPS_AUTOCOMPLETE_PERSONAL_PHONE"]))
                                                                {
                                                                        if($arUser = CUser::GetByID($USER->GetID())->Fetch())
                                                                                $val = $arUser["PERSONAL_PHONE"];
                                                                }
                                                        }
                                                }
                                                $PROPS[$k] = $val;
                                        }
                                }
                        }
                }
                else
                {
                        foreach($arFields["myFile"] as $kMyFile => $vMyFile)
                        {
                                if(is_array($errorFile))
                                        if(array_key_exists($kMyFile, $errorFile))
                                                $arResult["FORM_ERRORS"]["EMPTY_FIELD"][$kMyFile] = $errorFile["$kMyFile"];
                                if(is_array($arErrFile))
                                        if(array_key_exists($kMyFile, $arErrFile))
                                                $arResult["FORM_ERRORS"]["ERROR_FIELD"][$kMyFile] = $arErrFile["$kMyFile"];
                        }
                }
        }

        $arResult["FEEDBACK_TEXT"] = trim($_POST["FEEDBACK_TEXT_".$ALX]);
        if(strlen($arResult["FEEDBACK_TEXT"]) <= 0)
                if(in_array("FEEDBACK_TEXT_".$ALX, $arParams["PROPERTY_FIELDS_REQUIRED"]))
                {
                        $arResult["FORM_ERRORS"]["EMPTY_FIELD"]["FEEDBACK_TEXT_".$ALX] = GetMessage("ALX_FIELD1").(!empty($arParams["FB_TEXT_NAME"]) ? $arParams["FB_TEXT_NAME"] : GetMessage("ALX_CP_EVENT_TEXT_MESSAGE")).GetMessage("ALX_FIELD2");
                }

        if(count($PROPS) == 0 && empty($arResult["FEEDBACK_TEXT"]) && count($arResult["FORM_ERRORS"]["EMPTY_FIELD"]) == 0)
                $arResult["FORM_ERRORS"]["EMPTY_FIELD"]["ALL_EMPTY"] = GetMessage("ALX_ERROR_ALL_EMPTY");

        $PROPS["USERIP"] = $_SERVER["REMOTE_ADDR"];

        $PROPS["USER_ID"] = $USER->GetID();

        if($arParams["ADD_HREF_LINK"] != "N")
                $PROPS["HREF_LINK"] = htmlspecialcharsEx($_POST["HREF_LINK_".$ALX]);

        if($arParams["USE_CAPTCHA"])
        {
                if($arParams["CAPTCHA_TYPE"] == "recaptcha") // Google reCAPTCHA
                {
                        if(COption::GetOptionString('altasib.feedback', 'ALX_COMMON_CRM') == "Y")
                        {
                                $site_key = COption::GetOptionString('altasib.feedback', 'ALX_RECAPTCHA_SITE_KEY');
                                $server_key = COption::GetOptionString('altasib.feedback', 'ALX_RECAPTCHA_SECRET_KEY');
                        }
                        else
                        {
                                $site_key = COption::GetOptionString('altasib.feedback', 'ALX_RECAPTCHA_SITE_KEY_'.SITE_ID);
                                $server_key = COption::GetOptionString('altasib.feedback', 'ALX_RECAPTCHA_SECRET_KEY_'.SITE_ID);
                        }

                        $strResponse = $_POST["g-recaptcha-response"];
                        $user_ip = $_SERVER["REMOTE_ADDR"];
                        if (!empty($_SERVER["HTTP_X_REAL_IP"]))
                                $user_ip = $_SERVER["HTTP_X_REAL_IP"];

                        $strUrl = "https://www.google.com/recaptcha/api/siteverify?secret=".$server_key."&response=".$strResponse."&remoteip=".$user_ip;

                        if (!function_exists('curl_init'))
                        {
                                if(!$text = file_get_contents($strUrl))
                                        $arResult["FORM_ERRORS"]["CAPTCHA_WORD"]["ALX_CP_WRONG_CAPTCHA"] = GetMessage("ALX_CP_WRONG_RECAPTCHA_NOT");
                        }
                        else
                        {
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $strUrl);
                                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                                curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                                $text = curl_exec($ch);
                                $errno = curl_errno($ch);
                                $errstr = curl_error($ch);
                                curl_close($ch);

                                if ($errno)
                                        $arResult["FORM_ERRORS"]["CAPTCHA_WORD"]["ALX_CP_WRONG_CAPTCHA"] .= GetMessage("ALX_CP_WRONG_RECAPTCHA_NOT");
                        }
                        $answers = json_decode($text, true);
                        if(!$answers["success"])
                        {
                                $strCaptchaErr = '';
                                if(!empty($answers["error-codes"]))
                                {
                                        foreach($answers["error-codes"] as $err)
                                        {
                                                if($err == 'missing-input-response')
                                                        $strCaptchaErr .= GetMessage("ALX_CP_WRONG_RECAPTCHA_MIR");
                                                elseif($err == 'invalid-input-response')
                                                        $strCaptchaErr .= GetMessage("ALX_CP_WRONG_RECAPTCHA_IIR");
                                                else
                                                        $strCaptchaErr .= GetMessage("ALX_CP_WRONG_RECAPTCHA_ALL");
                                        }
                                }
                                $arResult["FORM_ERRORS"]["CAPTCHA_WORD"]["ALX_CP_WRONG_CAPTCHA"] .= $strCaptchaErr;
                        }
                }
                else // system CAPTCHA
                {
                        $captcha_sid = $_POST['captcha_sid'];
                        $captcha_word = $_POST['captcha_word'];
                        if(!$APPLICATION->CaptchaCheckCode($captcha_word, $captcha_sid))
                                $arResult["FORM_ERRORS"]["CAPTCHA_WORD"]["ALX_CP_WRONG_CAPTCHA"] = GetMessage("ALX_CP_WRONG_CAPTCHA");
                }
        }

        if(count($arResult["FORM_ERRORS"]) <= 0)
        {
                $arMessForm = array();
                $_POST["type_question_".$ALX] = trim(htmlspecialcharsEx($_POST["type_question_".$ALX]));

                // add element
                $arElementFields = Array(
                        "IBLOCK_ID"                                => $arParams["IBLOCK_ID"],
                        "IBLOCK_SECTION_ID"                => $_POST["type_question_".$ALX],
                        "ACTIVE"                                => $arParams["ACTIVE_ELEMENT"],
                        "PROPERTY_VALUES"                => $PROPS,
                );
                if($arParams["FB_TEXT_SOURCE"] == "DETAIL_TEXT")
                        $arElementFields["DETAIL_TEXT"] = $arResult["FEEDBACK_TEXT"];
                else
                        $arElementFields["PREVIEW_TEXT"] = $arResult["FEEDBACK_TEXT"];

                if(!empty($arParams["SECTION_MAIL".$_POST["type_question_".$ALX]]))
                {
                        $emailTo = trim($arParams["SECTION_MAIL".$_POST["type_question_".$ALX]]);
                        $emailTo .= ", ".trim($arParams["SECTION_MAIL_ALL"]);
                }
                else
                {
                        $emailTo = trim($arParams["SECTION_MAIL_ALL"]);
                }

                if ($arParams["ACTIVE_ELEMENT"] == "Y")
                        $arElementFields["ACTIVE"] == "Y";

                if ($arParams['NAME_ELEMENT'] == "ALX_DATE")
                {
                        $arElementFields["NAME"] = ConvertTimeStamp();
                }
                elseif ($arParams['NAME_ELEMENT'] == "ALX_TEXT")
                {
                        $arElementFields["NAME"] = $arResult["FEEDBACK_TEXT"];
                }
                elseif (!empty($PROPS[$arParams['NAME_ELEMENT']]))
                {
                        if(!is_array($PROPS[$arParams['NAME_ELEMENT']]))
                                $arElementFields["NAME"] = $PROPS[$arParams['NAME_ELEMENT']];
                        else
                                $arElementFields["NAME"] = $PROPS[$arParams['NAME_ELEMENT']]["VALUE"]["TEXT"];
                }
                else
                {
                        $arElementFields["NAME"] = ConvertTimeStamp();
                }

                $el = new CIBlockElement;

                if(!$ID = $el->Add($arElementFields))
                {
                        $arResult["FORM_ERRORS"]["ELEMENT_ADD"]["ELEMENT"] = $el->LAST_ERROR;
                }
                else
                {
                        $strMessageForm = "";
                        $strMessCategoty = "";
                        $_POST["type_question_name_" . $ALX] = trim(htmlspecialcharsEx($_POST["type_question_name_" . $ALX]));
                        if (!empty($_POST["type_question_name_" . $ALX]))
                                $strMessCategoty = GetMessage("ALX_TREATMENT_CATEGORY") . ": " . $_POST["type_question_name_" . $ALX] . "\n\n";

                        $dbProps = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $ID, array("sort" => "asc"));

                        /* ---- props ---- */

                        $arMessFormProps = Array();
                        $arFilesProps = Array();

                        while ($arProps = $dbProps->Fetch())
                        {
                                if (!empty($arProps["VALUE"]))
                                {
                                        $arMessFormProps[$arProps["ID"]]["SYSTEM"] = $arProps;
                                        $value = false;

                                        if ($arProps["PROPERTY_TYPE"] == "L")
                                                $value = $arProps["VALUE_ENUM"];
                                        elseif ($arProps["PROPERTY_TYPE"] == "F")
                                        {
                                                $filePath = CFile::GetPath($arProps["VALUE"]);
                                                $arFilesProps[] = $arProps["VALUE"];//$filePath;
                                                $value = "http://".$_SERVER["SERVER_NAME"].$filePath;
                                        }
                                        elseif ($arProps["USER_TYPE"] == "HTML")
                                                $value = $arProps["VALUE"]["TEXT"];
                                        else
                                                $value = $arProps["VALUE"];

                                        if ($value)
                                        {
                                                $arMessFormProps[$arProps["ID"]]["VALUE"][] = $value;
                                        }
                                }
                                $arPropsIds[$arProps["CODE"]] = array("ID" => $arProps["ID"], "PROPERTY_TYPE" => $arProps["PROPERTY_TYPE"]);

                                // save name, email and phone in session
                                foreach ($arAutompleteParams as $param)
                                {
                                        if (is_array($arParams[$param]) && !empty($arParams[$param]))
                                        {
                                                if (in_array($arProps["CODE"], $arParams[$param]))
                                                {
                                                        $_SESSION["ALTASIB_FB_" . $arProps["CODE"]] = htmlspecialcharsbx($arProps["VALUE"]);
                                                        break;
                                                }
                                        }
                                }
                        }

                        foreach ($arMessFormProps as $arMFProp)
                        {
                                $arMessForm[$arMFProp["SYSTEM"]["ID"]] = $arMFProp["SYSTEM"]["NAME"] . ": ";

                                if ($arMFProp["SYSTEM"]["PROPERTY_TYPE"] == "L")
                                {
                                        if (isset($arMFProp["VALUE"]) && is_array($arMFProp["VALUE"]))
                                                $arMessForm[$arMFProp["SYSTEM"]["ID"]] .= implode(", ", $arMFProp["VALUE"]);
                                        else
                                                $arMessForm[$arMFProp["SYSTEM"]["ID"]] .= ", " . $arMFProp["SYSTEM"]["VALUE_ENUM"];

                                } elseif ($arMFProp["SYSTEM"]["PROPERTY_TYPE"] == "E")
                                {
                                        foreach ($arMFProp["VALUE"] as $keyElem => $iElementId)
                                        {
                                                $resElement = CIBlockElement::GetByID($iElementId);
                                                if ($arElement = $resElement->GetNext())
                                                {
                                                        $arMFProp["VALUE"][$keyElem] = $arElement["NAME"] . ' (#' . $arElement["ID"] . ')';
                                                }
                                        }

                                        $arMessForm[$arMFProp["SYSTEM"]["ID"]] .= implode(", ", $arMFProp["VALUE"]);

                                } elseif ($arMFProp["SYSTEM"]["PROPERTY_TYPE"] == "G")
                                {
                                        foreach ($arMFProp["VALUE"] as $keySec => $iSectId)
                                        {
                                                $rsEl = CIBlockSection::GetByID($iSectId);
                                                if ($arSection = $rsEl->GetNext())
                                                {
                                                        $arMFProp["VALUE"][$keySec] = $arSection["NAME"] . ' (#' . $arSection["ID"] . ')';
                                                }
                                        }

                                        $arMessForm[$arMFProp["SYSTEM"]["ID"]] .= implode(", ", $arMFProp["VALUE"]);

                                } elseif ($arMFProp["SYSTEM"]["PROPERTY_TYPE"] == "F")
                                {
                                        if (is_array($arMFProp["VALUE"]) && !empty($arMFProp["VALUE"]))
                                        {
                                                foreach ($arMFProp["VALUE"] as $FlProp)
                                                        $arMessForm[$arMFProp["SYSTEM"]["ID"]] .= "\n" . $FlProp;
                                                $arMessForm[$arMFProp["SYSTEM"]["ID"]] .= "\n";
                                        } else
                                                $arMessForm[$arMFProp["SYSTEM"]["ID"]] .= "\n" . "http://" . $_SERVER["SERVER_NAME"] . CFile::GetPath($arMFProp["SYSTEM"]["VALUE"]) . "\n";
                                }
                                elseif ($arMFProp["SYSTEM"]["USER_TYPE"] == "HTML")
                                {
                                        $arMessForm[$arMFProp["SYSTEM"]["ID"]] .= "\n" . $arMFProp["SYSTEM"]["VALUE"]["TEXT"];
                                }
                                elseif($arMFProp["SYSTEM"]["USER_TYPE"] == "UserID")
                                {
                                        if($arMFProp["SYSTEM"]["VALUE"])
                                        {
                                                $rsUsers = CUser::GetList($by1, $order1, array("ID" => $arMFProp["SYSTEM"]["VALUE"]));
                                                $arUser = $rsUsers->Fetch();
                                                if($arUser)
                                                {
                                                        $arMessForm[$arMFProp["SYSTEM"]["ID"]] .= "[".$arUser["ID"]."] (".htmlspecialcharsbx($arUser["LOGIN"]).") ".htmlspecialcharsbx($arUser["NAME"])." ".htmlspecialcharsbx($arUser["LAST_NAME"]);
                                                }
                                        }
                                }
                                else
                                {
                                        $arMessForm[$arMFProp["SYSTEM"]["ID"]] .= implode(", ", $arMFProp["VALUE"]);
                                }
                        }
                        /* ---- end props ---- */

                        foreach ($arMessForm as $k => $v)
                                $strMessageForm .= $v . "\n";
                        // create EventType for admin
                        $rsET = CEventType::GetList(Array("TYPE_ID" => $arParams["EVENT_TYPE"]));
                        if (!$arET = $rsET->Fetch())
                        {
                                $et = new CEventType;
                                $eventID = $et->Add(array(
                                        "LID" => LANGUAGE_ID,
                                        "EVENT_NAME" => trim(htmlspecialcharsEx($arParams["EVENT_TYPE"])),
                                        "NAME" => GetMessage("ALX_CP_EVENT_NAME"),
                                        "DESCRIPTION" => GetMessage("ALX_CP_EVENT_DESCRIPTION")
                                ));
                                $emess = new CEventMessage;
                                $arMessage = Array(
                                        "ACTIVE" => "Y",
                                        "LID" => SITE_ID,
                                        "EVENT_NAME" => trim(htmlspecialcharsEx($arParams["EVENT_TYPE"])),
                                        "EMAIL_FROM" => "#EMAIL_FROM#",
                                        "EMAIL_TO" => "#DEFAULT_EMAIL_FROM#, #SECTION_EMAIL_TO#",
                                        "SUBJECT" => GetMessage("ALX_CP_EVENT_MESSAGE_SUBJECT"),
                                        "BODY_TYPE" => "text",
                                        "BCC" => "#BCC#",
                                        "MESSAGE" => GetMessage("ALX_CP_EVENT_MESSAGE"),
                                );

                                if (!$emess->Add($arMessage))
                                {
                                        $arResult["FORM_ERRORS"]["MESS_ADD"]["MESSAGE"] = $emess->LAST_ERROR;
                                }
                        }
                        $strMessage = $strMessCategoty;
                        if (!empty($arResult["FEEDBACK_TEXT"]))
                        {
                                $strMessage .= (!empty($arParams["FB_TEXT_NAME"]) ? $arParams["FB_TEXT_NAME"] : GetMessage("ALX_CP_EVENT_TEXT_MESSAGE")) . ":";
                                $strMessage .= "\n";
                                $strMessage .= htmlspecialcharsback($arResult["FEEDBACK_TEXT"]);
                                $strMessage .= "\n";
                                $strMessage .= "\n";
                        }

                        $strMessage .= htmlspecialcharsback($strMessageForm);
                        $strMessage .= "------------------------------------------";

                        if ($arParams["SHOW_MESSAGE_LINK"] == "Y")
                        {
                                $strEditUrl = GetMessage("ALX_CP_IBLOCK_ELEMENT_EDIT",
                                        Array("#ID#" => $ID, "#IBLOCK_TYPE#" => trim(htmlspecialcharsEx($arParams["IBLOCK_TYPE"])), "#LID#" => LANGUAGE_ID, "#IBLOCK_ID#" => intval($arParams["IBLOCK_ID"])));
                                $strMessLink = GetMessage("ALX_CP_EVENT_MESSAGE_LINK",
                                        Array("#SERVER_NAME#" => $_SERVER["SERVER_NAME"], "#EDIT_URL#" => $strEditUrl));
                                $strMessage .= "\n\n" . $strMessLink;
                        }

                        if (!empty($_POST["type_question_name_" . $ALX]))
                                $strMessCategoty = "(" . $_POST["type_question_name_" . $ALX] . ")";
                        $arEventSend = Array(
                                "SECTION_EMAIL_TO" => $emailTo,
                                "TEXT_MESSAGE" => $strMessage,
                                "BCC" => trim($arParams["BBC_MAIL"]),
                                "CATEGORY" => $strMessCategoty
                        );

                        if ($arParams["USERMAIL_FROM"] == "Y" && check_email($_POST["FIELDS"]["EMAIL_" . $ALX]))
                                $arEventSend["EMAIL_FROM"] = $_POST["FIELDS"]["EMAIL_" . $ALX];
                        else
                                $arEventSend["EMAIL_FROM"] = COption::GetOptionString("main", "email_from");

                        if (!$ALREADY_SEND_MESSAGE)
                        {
                                if($arParams["ADD_EVENT_FILES"] == "Y" && !empty($arFilesProps))
                                {

                                        if($arParams["SEND_IMMEDIATE"] == "N")
                                                                                        CEvent::Send($arParams["EVENT_TYPE"], SITE_ID, $arEventSend, "Y", "", $arFilesProps);
                                        else
                                                                            CEvent::SendImmediate($arParams["EVENT_TYPE"], SITE_ID, $arEventSend, "Y", "", $arFilesProps);
                                }
                                else
                                {
                                        if($arParams["SEND_IMMEDIATE"] == "N")
                                                CEvent::Send($arParams["EVENT_TYPE"], SITE_ID, $arEventSend);
                                        else
                                                CEvent::SendImmediate($arParams["EVENT_TYPE"], SITE_ID, $arEventSend);
                                }

                                // create EventType for user
                                $mail = trim(htmlspecialcharsEx($_POST["FIELDS"]["EMAIL_" . $ALX]));

                                if ($arParams["SEND_MAIL"] == "Y" && !empty($mail))
                                {
                                        $rsET = CEventType::GetList(Array("TYPE_ID" => $arParams["USER_EVENT"]));

                                        if (!$arET = $rsET->Fetch())
                                        {
                                                $et = new CEventType;
                                                $eventID = $et->Add(array(
                                                        "LID" => LANGUAGE_ID,
                                                        "EVENT_NAME" => $arParams["USER_EVENT"],
                                                        "NAME" => GetMessage("ALX_CP_SEND_MAIL"),
                                                        "DESCRIPTION" => ""
                                                ));
                                                $emess = new CEventMessage;
                                                $arMessage = Array(
                                                        "ACTIVE" => "Y",
                                                        "LID" => SITE_ID,
                                                        "EVENT_NAME" => $arParams["USER_EVENT"],
                                                        "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
                                                        "EMAIL_TO" => "#EMAIL#",
                                                        "SUBJECT" => GetMessage("ALX_CP_EVENT_MESSAGE_SUBJECT"),
                                                        "BODY_TYPE" => "text",
                                                        "BCC" => "#BCC#",
                                                        "MESSAGE" => GetMessage("ALX_SEND_USER_MESSAGE")
                                                );
                                                $flagErr = false;

                                                if (!$emess->Add($arMessage))
                                                {
                                                        $arResult["FORM_ERRORS"]["MESS_ADD"]["MESSAGE"] = $emess->LAST_ERROR;

                                                        $flagErr = true;
                                                }
                                        }
                                        if ($flagErr == false)
                                        {
                                                $arEventSend = Array(
                                                        "TEXT_MESSAGE" => GetMessage("ALX_SEND_USER_MESSAGE_TEXT"),
                                                        "CATEGORY" => $strMessCategoty,
                                                        "EMAIL" => $mail
                                                );

                                                if($arParams["SEND_IMMEDIATE"] == "N")
                                                        CEvent::Send($arParams["USER_EVENT"], SITE_ID, $arEventSend);
                                                else
                                                        CEvent::SendImmediate($arParams["USER_EVENT"], SITE_ID, $arEventSend);
                                        }
                                }
                                $_SESSION['alx_send_success'.$ALX] = 'Y';
                        }
                        $arResult["success_" . $ALX] = "yes";

                        // saving in cookies submit of form - if popup
                        if ($arParams['ALX_LOAD_PAGE'] == 'Y' && $arParams['ALX_LINK_POPUP'] == 'Y')
                        {
                                $APPLICATION->set_cookie("ALTASIB_FDB_SEND_" . $ALX, "Y", time() + 2592000); // 60*60*24*30
                        }
                }
        }
}
else
{
        unset($_SESSION['alx_send_success'.$ALX]);
}

$arResult["FIELDS"] = Array();
$arPFiltr = Array(
        "ACTIVE" => "Y",
        "IBLOCK_ID" => $arParams["IBLOCK_ID"]
);

$obCache = new CPHPCache();
if($obCache->InitCache(86400, serialize(array($arPFiltr, $arParams["PROPERTY_FIELDS"], "property")), $cachePath))
{
        $arProp = $obCache->GetVars();
}
elseif($obCache->StartDataCache())
{
        $arProp = array();
        $rsProp = CIBlockProperty::GetList(Array("SORT" => "ASC"), $arPFiltr);

        while($arrProp = $rsProp->Fetch())
        {
                $arProp[] = $arrProp;
        }
        $obCache->EndDataCache($arProp);
}
if(!isset($arProp))
        $arProp = array();

foreach($arProp as $prop)
{
        if(!empty($arCurFields) && is_array($arCurFields))
        {
                if(!in_array($prop["CODE"], $arCurFields))
                        continue;
        }
        elseif(!in_array($prop["CODE"], $arParams["PROPERTY_FIELDS"]))
                continue;

        $arField = Array(
                "CODE"        =>        $prop["CODE"] ."_".$ALX,
                "NAME"        =>        $prop["NAME"],
                "SORT"        =>        $prop["SORT"],
                "TYPE"        =>        $prop["PROPERTY_TYPE"],
                "HINT"        =>        $prop["HINT"],
                "DEFAULT_VALUE"        =>        $prop["DEFAULT_VALUE"],
                "USER_TYPE"        =>        $prop["USER_TYPE"]
        );

        if(isset($prop["USER_TYPE_SETTINGS"]))
                $arField["USER_TYPE_SETTINGS"] = $prop["USER_TYPE_SETTINGS"];

        if($prop["PROPERTY_TYPE"] == "L")
        {
                $arField["LIST_TYPE"] = $prop["LIST_TYPE"];
                $arField["MULTIPLE"] = $prop["MULTIPLE"];

                if($obCache->InitCache(86400, serialize(array($prop["ID"], "PropertyEnum")), $cachePath))
                {
                        $arEnums = $obCache->GetVars();
                }
                elseif($obCache->StartDataCache())
                {
                        $arEnums = array();
                        $dbEnums = CIBlockProperty::GetPropertyEnum($prop["ID"]);
                        while($arEnum = $dbEnums->GetNext())
                        {
                                $arEnums[] = $arEnum;
                        }
                        $obCache->EndDataCache($arEnums);
                }
                if(!isset($arEnums))
                        $arEnums = array();

                $arField["ENUM"] = $arEnums;

        }

        if($prop["PROPERTY_TYPE"] == "F")
        {
                $arField["MULTIPLE"] = $prop["MULTIPLE"];
                $arField["MULTIPLE_CNT"] = $prop["MULTIPLE_CNT"];
        }

        if(in_array($arField["CODE"], $arParams["PROPERTY_FIELDS_REQUIRED"]))
                $arField["REQUIRED"] = "Y";

        if($prop["CODE"] == "CITY")
                if(CModule::IncludeModule("altasib.geoip"))
                {
                        $arGeoIP = ALX_GeoIP::GetAddr();
                        $arField["DEFAULT_VALUE"] = $arGeoIP["city"];
                }

        if($prop["PROPERTY_TYPE"] == "E")
        {
                $arField["PROPERTY"] = $prop;

                if(isset($prop["LINK_IBLOCK_ID"]))
                {
                        $arFilter = Array(
                                "IBLOCK_ID"        => $prop["LINK_IBLOCK_ID"],
                                "ACTIVE"        => "Y"
                        );

                        if($obCache->InitCache(3600, serialize(array($arFilter, "LinkElements")), $cachePath))
                        {
                                $arElement = $obCache->GetVars();
                        }
                        elseif($obCache->StartDataCache())
                        {
                                $arEnums = array();
                                $rsElement = CIBlockElement::GetList(
                                        Array("SORT" => "ASC"),
                                        $arFilter,
                                        false,
                                        array("nTopCount" => 200),
                                        Array("ID", "NAME")
                                );
                                while($arElem = $rsElement->Fetch())
                                {
                                        $arElement[] = array("ID"=>$arElem["ID"], "NAME"=>$arElem["NAME"]);
                                }
                                $obCache->EndDataCache($arElement);
                        }
                        if(!isset($arElement))
                                $arElement = array();

                        $arField["LINKED_ELEMENTS"] = $arElement;
                }
        }

        if($prop["PROPERTY_TYPE"] == "G")
        {
                $arField["PROPERTY"] = $prop;
                if(isset($prop["LINK_IBLOCK_ID"]))
                {
                        $arFilter = Array(
                                "IBLOCK_ID"        => $prop["LINK_IBLOCK_ID"],
                                "ACTIVE"        => "Y"
                        );

                        if($obCache->InitCache(3600, serialize(array($arFilter, "LinkSections")), $cachePath))
                        {
                                $arSection = $obCache->GetVars();
                        }
                        elseif($obCache->StartDataCache())
                        {
                                $arSection = array();
                                $resSect = CIBlockSection::GetList(
                                        Array("LEFT_MARGIN"=>"ASC", "SORT" => "ASC"),
                                        $arFilter,
                                        false,
                                        Array("ID", "NAME", "DEPTH_LEVEL")
                                );
                                while($arSect = $resSect->Fetch())
                                {
                                        $arSection[] = $arSect;
                                }
                                $obCache->EndDataCache($arSection);
                        }
                        if(!isset($arSection))
                                $arSection = array();

                        $arField["LINKED_SECTIONS"] = $arSection;
                }
        }

        // autocomplete the form fields from personal or session
        if($USER->IsAuthorized())
        {
                if(is_array($arParams["PROPS_AUTOCOMPLETE_NAME"])
                        && !empty($arParams["PROPS_AUTOCOMPLETE_NAME"]))
                {
                        if(in_array($prop["CODE"], $arParams["PROPS_AUTOCOMPLETE_NAME"]))
                                $arField["AUTOCOMPLETE_VALUE"] = $USER->GetFormattedName(false);
                }
                if(is_array($arParams["PROPS_AUTOCOMPLETE_EMAIL"])
                        && !empty($arParams["PROPS_AUTOCOMPLETE_EMAIL"]))
                {
                        if(in_array($prop["CODE"], $arParams["PROPS_AUTOCOMPLETE_EMAIL"]))
                                $arField["AUTOCOMPLETE_VALUE"] = htmlspecialcharsbx($USER->GetEmail());
                }
                if(is_array($arParams["PROPS_AUTOCOMPLETE_PERSONAL_PHONE"])
                        && !empty($arParams["PROPS_AUTOCOMPLETE_PERSONAL_PHONE"]))
                {
                        if(in_array($prop["CODE"], $arParams["PROPS_AUTOCOMPLETE_PERSONAL_PHONE"]))
                        {
                                if(isset($_SESSION["ALTASIB_FB_".$prop["CODE"]]))
                                {
                                        $arField["AUTOCOMPLETE_VALUE"] = htmlspecialcharsbx($_SESSION["ALTASIB_FB_".$prop["CODE"]]);
                                }
                                elseif($arUser = CUser::GetByID($USER->GetID())->Fetch())
                                {
                                        $arField["AUTOCOMPLETE_VALUE"] = $arUser["PERSONAL_PHONE"];
                                        $_SESSION["ALTASIB_FB_".$prop["CODE"]] = htmlspecialcharsbx($arUser["PERSONAL_PHONE"]);
                                }
                        }
                }
        }
        else
        {
                // save name, email and phone in session
                foreach($arAutompleteParams as $param)
                {
                        if(is_array($arParams[$param]) && !empty($arParams[$param]))
                        {
                                if(in_array($prop["CODE"], $arParams[$param]))
                                {
                                        if(strlen($_SESSION["ALTASIB_FB_".$prop["CODE"]]) > 0)
                                        {
                                                $arField["AUTOCOMPLETE_VALUE"] = htmlspecialcharsbx($_SESSION["ALTASIB_FB_".$prop["CODE"]]);
                                                break;
                                        }
                                }
                        }
                }
        }

        $arResult["FIELDS"][] = $arField;
}

$arResult["TYPE_QUESTION"] = Array();
$arFilter = Array(
        "ACTIVE" => "Y",
        "IBLOCK_ID" => $arParams["IBLOCK_ID"]
);

$obCache = new CPHPCache();
if($obCache->InitCache(3600, serialize(array($arPFiltr, "section")), $cachePath))
{
        $arTSect = $obCache->GetVars();
}
elseif($obCache->StartDataCache())
{
        $arTSect = array();
        $rsSect = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, false, array("ID", "NAME", "CODE", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN"));
        while($arSect = $rsSect->GetNext())
        {
                $arTSect[] = $arSect;
        }
        $obCache->EndDataCache($arTSect);
}
if(!empty($arTSect))
        $arResult["TYPE_QUESTION"] = $arTSect;

if($arParams["USE_CAPTCHA"] == "Y" && $arParams["CAPTCHA_TYPE"] == "recaptcha")
{
        $common_crm = COption::GetOptionString('altasib.feedback', 'ALX_COMMON_CRM');
        if($common_crm == "Y")
                $arResult["SITE_KEY"] = COption::GetOptionString('altasib.feedback', 'ALX_RECAPTCHA_SITE_KEY');
        else
                $arResult["SITE_KEY"] = COption::GetOptionString('altasib.feedback', 'ALX_RECAPTCHA_SITE_KEY_'.SITE_ID);
}

$this->IncludeComponentTemplate();


if(count($arResult["TYPE_QUESTION"]) >= 1 || array_walk_recursive($arResult["FIELDS"], function($v,$k){if ($k === 'TYPE'&& ($v === 'L' || $v === 'G')) return true;}))
{
        $APPLICATION->AddHeadScript($this->__path.'/templates/'.$this->arParams['COMPONENT_TEMPLATE'].'/dropdown/jquery.dropdown.js');
}

if($arParams['ALX_LINK_POPUP']=='Y' || isset($arResult["POST"])
        || ($arParams["SECTION_FIELDS_ENABLE"] == "Y" && $_POST["REFRESH"] == "Y")
)
{
        if($_SERVER["REQUEST_METHOD"]=="POST" && ($_POST["OPEN_POPUP"] == $ALX || $_POST["FEEDBACK_FORM_".$ALX]))
                die();
}
