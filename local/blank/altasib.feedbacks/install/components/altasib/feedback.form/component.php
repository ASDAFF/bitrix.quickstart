<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!function_exists("AlxFBConvertStr"))
{
        Function AlxFBConvertStr($str)
        {
                $arCyr = explode(",",GetMessage("ALX_RU_ABC"));
                $arLat = Array("e","i","ts","u","k","e","n","g","sh","sch","z","h","",
                "f","y","v","a","p","r","o","l","d","zh","e","ya","ch","s","m","i","t","","b",
                "yu","e","i","ts","u","k","e","n","g","sh","sch","z","h","","f","y","v","a","p",
                "r","o","l","d","zh","e","ya","ch","s","m","i","t","","b","yu");

                if(defined("BX_UTF") == false || (defined("BX_UTF") == true && BX_UTF !== true))
                       $str = preg_replace("/[^a-zA-Z".GetMessage("ALX_RU_ABC_REGULAR")."0-9. ]/i","",$str);
                else
                       $str = preg_replace("/[^a-zA-Z".GetMessage("ALX_RU_ABC_REGULAR")."0-9. ]/iu","",$str);
                $str = preg_replace("/ +/"," ",$str);
                $str = str_replace($arCyr,$arLat,$str);
                //$str = preg_replace("/\w+/ei", "ucfirst('\\0')",$str);
                $str = str_replace(" ","_",$str);
                return $str;
        }
}

$ALX = "FID".$arParams["FORM_ID"];

foreach($arParams["PROPERTY_FIELDS_REQUIRED"] as $k => $v)
        $arParams["PROPERTY_FIELDS_REQUIRED"][$k] = $v."_".$ALX;

$arResult["FORM_ERRORS"] = Array();

if(!CModule::IncludeModule("iblock"))
{
    ShowError(GetMessage("IB_MODULE_NOT_INSTALLED"));
    return;
}

$res = CIBlock::GetProperties($arParams["IBLOCK_ID"],
        array(),
        array("PROPERTY_TYPE" => "F")
);

while($res_arr = $res->Fetch())
{
        $arTypeFile[$res_arr["CODE"]]["FILE_TYPE"] = $res_arr["FILE_TYPE"];
        $arTypeFile[$res_arr["CODE"]]["NAME"] = $res_arr["NAME"];
}

$codeFileFields = count($_POST["codeFileFields"]);
if(is_array($_FILES["myFile"]["name"]))
{
        foreach($_FILES["myFile"]["name"] as $k => $value)
        {
                        $codeID = trim(htmlspecialcharsEx($_POST["codeFileFields"][$k]));
                        $code = trim(htmlspecialcharsEx($_POST["codeFileFields"][$k]));
                        $code = str_replace("_".$ALX, "", $code);

                        $filename = $_FILES["myFile"]["name"][$k];
                        $arFileName = explode(".", $filename);

                        $arParamTypeFile = array();
                        $arParamTypeFileTrim = array();
                        if(!empty($arTypeFile[$code]["FILE_TYPE"]))
                        {
                                $arParamTypeFile = explode(",", $arTypeFile[$code]["FILE_TYPE"]);
                                foreach($arParamTypeFile as $v)
                                        $arParamTypeFileTrim[] = trim($v);
                        }

                        if((in_array($arFileName[count($arFileName)-1], $arParamTypeFileTrim) || empty($arTypeFile[$code]["FILE_TYPE"])) && !empty($_FILES["myFile"]["tmp_name"][$k]))
                        {
                                $file_array = Array();
                                $_FILES["myFile"]["name"][$k] = AlxFBConvertStr($_FILES["myFile"]["name"][$k]);
                                $file_array["name"] = $_FILES["myFile"]["name"][$k];
                                $file_array["size"] = $_FILES["myFile"]["size"][$k];
                                $file_array["tmp_name"] = $_FILES["myFile"]["tmp_name"][$k];
                                $file_array["type"] = $_FILES["myFile"]["type"][$k];
                                $file_array["description"] = "";
                                $PROPS[$code] = $file_array;
                        }
                        elseif(!empty($_FILES["myFile"]["tmp_name"][$k]))
                                $errorFile[$codeID] = GetMessage("ALX_FIELD1") . $arTypeFile[$code]["NAME"] . '". ' . GetMessage("DISABLE_FILE");
                        elseif(in_array($k, $arParams["PROPERTY_FIELDS_REQUIRED"]) && $_FILES["myFile"]["error"][$k] == 4)
                                $errorFile[$codeID] = GetMessage("ALX_FIELD1") . $arTypeFile[$code]["NAME"] . '". ' . GetMessage("EMPTY_FILE");
        }
}

$arParams["IBLOCK_TYPE"] = trim(htmlspecialcharsEx($arParams["IBLOCK_TYPE"]));
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["EVENT_TYPE"] = trim(htmlspecialcharsEx($arParams["EVENT_TYPE"]));
$arParams["ACTIVE_ELEMENT"] = trim(htmlspecialcharsEx($arParams["ACTIVE_ELEMENT"]));
$arParams["USE_CAPTCHA"] = $arParams["USE_CAPTCHA"] == "Y" && !$USER->IsAuthorized();

if(strlen($arParams["EVENT_TYPE"]) <= 0)
        $arParams["EVENT_TYPE"] = "ALX_FEEDBACK_FORM";

if($_POST["FEEDBACK_FORM_".$ALX] && check_bitrix_sessid())
{
        $arFields = $_POST["FIELDS"];

        if(!is_array($arFields))
                $arFields = Array();

        $arResult["POST"] = Array();
        $arFieldsName = Array();

        $rsProp = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
        while($arrProp = $rsProp->Fetch())
			$arFieldsName[$arrProp["CODE"]."_".$ALX] = $arrProp;

        foreach($arParams["PROPERTY_FIELDS_REQUIRED"] as $k => $v)
                if(!array_key_exists($v, $arFields) && !in_array($v, $_POST["codeFileFields"]) && $v != "FEEDBACK_TEXT_".$ALX)
                        $arResult["FORM_ERRORS"]["EMPTY_FIELD"][$v] = GetMessage("ALX_FIELD1") . $arFieldsName[$v]["NAME"] . GetMessage("ALX_FIELD2");

        foreach($arFields as $k => $v)
        {
                $k = htmlspecialcharsEx($k);
                $k = str_replace("_".$ALX, "", $k);
                if($k != "myFile")
                {
                        if(is_array($v))
                        {
                                $PROPS[$k] = $v;
                        }
                        elseif(strlen(trim(htmlspecialcharsEx($v))) <= 0)
                        {
                                if(in_array($k."_".$ALX, $arParams["PROPERTY_FIELDS_REQUIRED"]))
                                        $arResult["FORM_ERRORS"]["EMPTY_FIELD"][$k."_".$ALX] = GetMessage("ALX_FIELD1") . $arFieldsName[$k."_".$ALX]["NAME"] . GetMessage("ALX_FIELD2");
                        }
                        else
                        {
                                if($k == "EMAIL")
                                {
                                        $v = trim(htmlspecialcharsEx($v));
                                        if(check_email($v))
                                                $PROPS[$k] = $v;
                                        else
                                                $arResult["FORM_ERRORS"]["EMPTY_FIELD"][$k."_".$ALX] = GetMessage("INCORRECT_MAIL");
                                }
                                else
								{
									if($arFieldsName[$k."_".$ALX]["USER_TYPE"] == "HTML")
										$PROPS[$k] = array(
											"VALUE" => array(
												"TEXT" => trim(htmlspecialcharsEx($v)),
												"TYPE" => "TEXT"
											)
										);
									else
                                        $PROPS[$k] = trim(htmlspecialcharsEx($v));
								}
                        }
                }
                else
                {
                        foreach($arFields["myFile"] as $kMyFile => $vMyFile)
                                if(array_key_exists($kMyFile, $errorFile))
                                        $arResult["FORM_ERRORS"]["EMPTY_FIELD"][$kMyFile] = $errorFile["$kMyFile"];
                }
        }

        $arResult["FEEDBACK_TEXT"] = trim(htmlspecialcharsEx($_POST["FEEDBACK_TEXT_".$ALX]));
        if(strlen($arResult["FEEDBACK_TEXT"]) <= 0)
                if(in_array("FEEDBACK_TEXT_".$ALX, $arParams["PROPERTY_FIELDS_REQUIRED"]))
                                $arResult["FORM_ERRORS"]["EMPTY_FIELD"]["FEEDBACK_TEXT_".$ALX] = GetMessage("ALX_FIELD1") . GetMessage("ALX_CP_EVENT_TEXT_MESSAGE") . GetMessage("ALX_FIELD2");

        if(count($PROPS) == 0 && empty($arResult["FEEDBACK_TEXT"]) && count($arResult["FORM_ERRORS"]["EMPTY_FIELD"]) == 0)
                $arResult["FORM_ERRORS"]["EMPTY_FIELD"]["ALL_EMPTY"] = GetMessage("ALX_ERROR_ALL_EMPTY");

        $PROPS["USERIP"] = $_SERVER["REMOTE_ADDR"];

        if($arParams["USE_CAPTCHA"])
        {
                        $captcha_sid = $_POST['captcha_sid'];
                        $captcha_word = $_POST['captcha_word'];
                        if(!$APPLICATION->CaptchaCheckCode($captcha_word, $captcha_sid))
                                $arResult["FORM_ERRORS"]["CAPTCHA_WORD"]["ALX_CP_WRONG_CAPTCHA"] = GetMessage("ALX_CP_WRONG_CAPTCHA");
        }

        if(count($arResult["FORM_ERRORS"]) <= 0)
        {
                        $_POST["type_question_".$ALX] = trim(htmlspecialcharsEx($_POST["type_question_".$ALX]));

                        // add element
                        $arElementFields = Array(
                                "IBLOCK_ID"                => $arParams["IBLOCK_ID"],
                                "IBLOCK_SECTION_ID"        => $_POST["type_question_".$ALX],
                                "ACTIVE"                => $arParams["ACTIVE_ELEMENT"],
                                "PREVIEW_TEXT"                => $arResult["FEEDBACK_TEXT"],
                                "PROPERTY_VALUES"        => $PROPS,
                        );

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
							$arElementFields["NAME"] =  ConvertTimeStamp();
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
							$arElementFields["NAME"] =  ConvertTimeStamp();
						}


                        $el = new CIBlockElement;
                        if(!$ID = $el->Add($arElementFields));
                                        ShowError($el->LAST_ERROR);

                        $strMessageForm = "";
                        $strMessCategoty = "";
                        $_POST["type_question_name_".$ALX] = trim(htmlspecialcharsEx($_POST["type_question_name_".$ALX]));
                        if(!empty($_POST["type_question_name_".$ALX]))
                                $strMessCategoty = GetMessage("ALX_TREATMENT_CATEGORY") . ": " . $_POST["type_question_name_".$ALX] . "\n\n";

                        $db_props = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $ID, array("sort" => "asc"));

                        while($ar_props = $db_props->Fetch())
                        {
                                if(!empty($ar_props["VALUE"]))
                                {
                                        if(!array_key_exists($ar_props["ID"], $arMessForm))
                                        {
                                                if($ar_props["PROPERTY_TYPE"] == "L")
													$arMessForm[$ar_props["ID"]] = $ar_props["NAME"] . ": " . $ar_props["VALUE_ENUM"];
                                                elseif($ar_props["PROPERTY_TYPE"] == "F")
													$strMessageForm .= $ar_props["NAME"] . ":\n" . "http://".$_SERVER["SERVER_NAME"].CFile::GetPath($ar_props["VALUE"]) . "\n";
                                                elseif($ar_props["USER_TYPE"] == "HTML")
													$arMessForm[$ar_props["ID"]] = $ar_props["NAME"] . ": " . $ar_props["VALUE"]["TEXT"];
												else
													$arMessForm[$ar_props["ID"]] = $ar_props["NAME"] . ": " . $ar_props["VALUE"];
                                        }
                                        else
                                        {
                                                if($ar_props["PROPERTY_TYPE"] == "L")
													$arMessForm[$ar_props["ID"]] .= ", " . $ar_props["VALUE_ENUM"];
                                                elseif($ar_props["PROPERTY_TYPE"] == "F")
													$strMessageForm .= "\n" . "http://".$_SERVER["SERVER_NAME"].CFile::GetPath($ar_props["VALUE"]) . "\n";
                                                elseif($ar_props["USER_TYPE"] == "HTML")
													$arMessForm[$ar_props["ID"]] .= "\n" . $ar_props["VALUE"]["TEXT"];
                                                else
													$arMessForm[$ar_props["ID"]] .= ", " . $ar_props["VALUE"];
                                        }
                                }
                        }

                        foreach($arMessForm as $k => $v)
                                $strMessageForm .= $v . "\n";

                        // create EventType for admin
                        $rsET = CEventType::GetList(Array("TYPE_ID" => $arParams["EVENT_TYPE"]));
                        if(!$arET = $rsET->Fetch())
                        {
                                $et = new CEventType;
                                $eventID = $et->Add(array(
                                        "SITE_ID"       => LANGUAGE_ID,
                                        "EVENT_NAME"    => trim(htmlspecialcharsEx($arParams["EVENT_TYPE"])),
                                        "NAME"          => GetMessage("ALX_CP_EVENT_NAME"),
                                        "DESCRIPTION"   => GetMessage("ALX_CP_EVENT_DESCRIPTION")
                                ));
                                $emess = new CEventMessage;
                                $arMessage = Array(
                                        "ACTIVE"        =>        "Y",
                                        "LID"                =>        SITE_ID,
                                        "EVENT_NAME"        =>        trim(htmlspecialcharsEx($arParams["EVENT_TYPE"])),
                                        "EMAIL_FROM"        =>        "#EMAIL_FROM#",
                                        "EMAIL_TO"        =>        "#DEFAULT_EMAIL_FROM#, #SECTION_EMAIL_TO#",
                                        "SUBJECT"        =>        GetMessage("ALX_CP_EVENT_MESSAGE_SUBJECT"),
                                        "BODY_TYPE"        =>        "text",
                                        "BCC"                =>        "#BCC#",
                                        "MESSAGE"        =>        GetMessage("ALX_CP_EVENT_MESSAGE"),
                                );

                                if(!$emess->Add($arMessage))
                                                ShowError($emess->LAST_ERROR);
                        }
                        $strMessage = $strMessCategoty;
                        if(!empty($arResult["FEEDBACK_TEXT"]))
                        {
									$strMessage.= GetMessage("ALX_CP_EVENT_TEXT_MESSAGE");
									$strMessage.= "\n";
									$strMessage.= $arResult["FEEDBACK_TEXT"];
									$strMessage.= "\n";
									$strMessage.= "\n";
                        }
                        $strMessage.= $strMessageForm;
                        $strMessage.= "------------------------------------------";

						if($arParams["SHOW_MESSAGE_LINK"] == "Y")
						{
							$strEditUrl = GetMessage("ALX_CP_IBLOCK_ELEMENT_EDIT",
								Array("#ID#" => $ID, "#IBLOCK_TYPE#" => trim(htmlspecialcharsEx($arParams["IBLOCK_TYPE"])), "#LID#" => LANGUAGE_ID, "#IBLOCK_ID#" => intval($arParams["IBLOCK_ID"])));
							$strMessLink = GetMessage("ALX_CP_EVENT_MESSAGE_LINK",
								Array("#SERVER_NAME#" => $_SERVER["SERVER_NAME"], "#EDIT_URL#" => $strEditUrl));
							$strMessage .= "\n\n".$strMessLink;
						}

						if(!empty($_POST["type_question_name_".$ALX]))
                                $strMessCategoty = "(".$_POST["type_question_name_".$ALX].")";
                        $arEventSend = Array(
                                "SECTION_EMAIL_TO"        => $emailTo,
                                "TEXT_MESSAGE"                => $strMessage,
                                "BCC"                        => trim($arParams["BBC_MAIL"]),
                                "CATEGORY"                => $strMessCategoty
                        );

                        if($arParams["USERMAIL_FROM"] == "Y" && check_email($_POST["FIELDS"]["EMAIL_".$ALX]))
							$arEventSend["EMAIL_FROM"] = $_POST["FIELDS"]["EMAIL_".$ALX];
                       	else
                       		$arEventSend["EMAIL_FROM"] = COption::GetOptionString("main", "email_from");

                        CEvent::Send($arParams["EVENT_TYPE"], SITE_ID, $arEventSend);
                        // create EventType for user
                        $mail = trim(htmlspecialcharsEx($_POST["FIELDS"]["EMAIL_".$ALX]));
                        if ($arParams["SEND_MAIL"] == "Y" && !empty($mail))
                        {
                                        $rsET = CEventType::GetList(Array("TYPE_ID" => "ALX_FEEDBACK_FORM_SEND_MAIL"));
                                        if(!$arET = $rsET->Fetch())
                                        {
                                                $et = new CEventType;
                                                $eventID = $et->Add(array(
                                                        "SITE_ID"       => LANGUAGE_ID,
                                                        "EVENT_NAME"    => "ALX_FEEDBACK_FORM_SEND_MAIL",
                                                        "NAME"          => GetMessage("ALX_CP_SEND_MAIL"),
                                                        "DESCRIPTION"   => ""
                                                ));
                                                $emess = new CEventMessage;
                                                $arMessage = Array(
                                                        "ACTIVE"        =>        "Y",
                                                        "LID"                =>        SITE_ID,
                                                        "EVENT_NAME"        =>        "ALX_FEEDBACK_FORM_SEND_MAIL",
                                                        "EMAIL_FROM"        =>        "#DEFAULT_EMAIL_FROM#",
                                                        "EMAIL_TO"        =>        "#EMAIL#",
                                                        "SUBJECT"        =>        GetMessage("ALX_CP_EVENT_MESSAGE_SUBJECT"),
                                                        "BODY_TYPE"        =>        "text",
                                                        "BCC"                =>        "#BCC#",
                                                        "MESSAGE"        =>        GetMessage("ALX_SEND_USER_MESSAGE")
                                                );
                                                if(!$emess->Add($arMessage))
                                                        ShowError($emess->LAST_ERROR);
                                        }
                                        $arEventSend = Array(
                                                "TEXT_MESSAGE"        =>        GetMessage("ALX_SEND_USER_MESSAGE_TEXT"),
                                                "CATEGORY"        =>        $strMessCategoty,
                                                "EMAIL"                =>      $mail
                                        );
                                        CEvent::Send("ALX_FEEDBACK_FORM_SEND_MAIL", SITE_ID, $arEventSend);

                        }
                LocalRedirect($APPLICATION->GetCurPageParam("success_".$ALX."=yes", array("success")));
        }
}

$arResult["FIELDS"] = Array();
$rsProp = CIBlockProperty::GetList(Array("SORT" => "ASC"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
while($arrProp = $rsProp->Fetch())
{
        if(!in_array($arrProp["CODE"], $arParams["PROPERTY_FIELDS"]))
			continue;

        $arField = Array(
                "CODE"	=>	$arrProp["CODE"] ."_". $ALX,
                "NAME"	=>	$arrProp["NAME"],
                "TYPE"	=>	$arrProp["PROPERTY_TYPE"],
                "HINT"	=>	$arrProp["HINT"],
                "DEFAULT_VALUE"	=>	$arrProp["DEFAULT_VALUE"],
        		"USER_TYPE"	=>	$arrProp["USER_TYPE"]
        );

        if(isset($arrProp["USER_TYPE_SETTINGS"]))
        	$arField["USER_TYPE_SETTINGS"] = $arrProp["USER_TYPE_SETTINGS"];

		if($arrProp["PROPERTY_TYPE"] == "L")
        {
        	$arField["LIST_TYPE"] = $arrProp["LIST_TYPE"];
            $arField["MULTIPLE"] = $arrProp["MULTIPLE"];

            $db_enum_list = CIBlockProperty::GetPropertyEnum($arrProp["ID"]);
            while($ar_enum_list = $db_enum_list->GetNext())
            {
            	$arField["ENUM"][] = $ar_enum_list;
			}
		}

        if(in_array($arField["CODE"], $arParams["PROPERTY_FIELDS_REQUIRED"]))
                $arField["REQUIRED"] = "Y";

		if($arrProp["CODE"] == "CITY")
			if(CModule::IncludeModule("altasib.geoip"))
			{
				$arGeoIP = ALX_GeoIP::GetAddr();
				$arField["DEFAULT_VALUE"] = $arGeoIP["city"];
			}

        $arResult["FIELDS"][] = $arField;
}

$arResult["TYPE_QUESTION"] = Array();
$arFilter = Array(
        "ACTIVE"=>"Y",
        "IBLOCK_ID"=>$arParams["IBLOCK_ID"]
);
$arSection = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, true);
while($v = $arSection->GetNext())
        $arResult["TYPE_QUESTION"][] = $v;

$this->IncludeComponentTemplate();
?>
