<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2013 ALTASIB             #
#################################################
?>
<?
global $DBType;
IncludeModuleLangFile(__FILE__);

$arClassesList = array(
);


if (method_exists(CModule, "AddAutoloadClasses"))
{

        CModule::AddAutoloadClasses(
                "altasib.errorsend",
                $arClassesList
        );
}
else
{
        foreach ($arClassesList as $sClassName => $sClassFile)
        {
                require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.errorsend/".$sClassFile);
        }
}

Class ErrorSendMD
{
        Function ErrorSendOnBeforeEndBufferContent()
        {
               global $APPLICATION;

               if (IsModuleInstalled("altasib.errorsend"))
               {
                        if(!defined(ADMIN_SECTION) && ADMIN_SECTION!==true)
                        {
                               $defLogoSrc = COption::GetOptionString("altasib_errorsend", "logo", "/bitrix/images/altasib.errorsend/altasib.errorsend.png");
                               if(!$defLogoSrc)
                                         $defLogoSrc = "/bitrix/images/altasib.errorsend/altasib.errorsend.png";

                               CUtil::InitJSCore(array('window', 'ajax', 'core'));
                               $APPLICATION->AddHeadString("<script type=\"text/javascript\">
                                         var ALXerrorLogoImgSrc = '".$defLogoSrc."';
					 var ALXerrorSendMessages = {
                                                'head':'".GetMessage("ALTASIB_ERROR_SEND_JS_HEAD")."',
                                                'footer':'".GetMessage("ALTASIB_ERROR_SEND_JS_FOOTER")."',
                                                'comment':'".GetMessage("ALTASIB_ERROR_SEND_JS_COMMENT")."',
                                                'TitleForm':'".GetMessage("ALTASIB_ERROR_SEND_JS_TITLEFORM")."',
                                                'ButtonSend':'".GetMessage("ALTASIB_ERROR_SEND_JS_BUTTONSEND")."',
                                                'LongText':'".GetMessage("ALTASIB_ERROR_SEND_JS_LONGTEXT")."',
                                                'LongText2':'".GetMessage("ALTASIB_ERROR_SEND_JS_LONGTEXT2")."',
                                                'text_ok':'".GetMessage("ALTASIB_ERROR_SEND_JS_TEXT_OK")."',
                                                'text_ok2':'".GetMessage("ALTASIB_ERROR_SEND_JS_TEXT_OK2")."'
                                        }</script>",false);
                               $APPLICATION->AddHeadScript("/bitrix/js/altasib.errorsend/error.js");
                               $APPLICATION->AddHeadString('<link href="/bitrix/js/altasib.errorsend/css/window.css" type="text/css" rel="stylesheet" />',true);
                               $APPLICATION->AddHeadString("<script type=\"text/javascript\">if(typeof ALXErrorSendClass == 'function')ALXErrorSend =  new ALXErrorSendClass();</script>",false);

                       }
               }
        }

        Function ErrorSendOnProlog()
        {
                global $APPLICATION;
                if($_SERVER["REQUEST_METHOD"]=="POST"
                && (isset($_REQUEST["AJAX_CALL"]) && $_REQUEST["AJAX_CALL"]=="Y")
                && (isset($_REQUEST["ERROR_SEND"]) && $_REQUEST["ERROR_SEND"]=="Y"))
                {
                        if(!CModule::IncludeModule("altasib.errorsend"))
                                return;

                        $APPLICATION->RestartBuffer();

                        $arFields = $_POST;

                        $BX_UTF = false;
                        if (defined('BX_UTF'))
                            if (is_bool(BX_UTF))
                                 if (BX_UTF)
                                    $BX_UTF = true;

                        foreach ($arFields as $F_NAME=>$F_VALUE)
                        {
                            if ($BX_UTF)
                              $arFields[$F_NAME] = $F_VALUE;
                            else
                              $arFields[$F_NAME] = mb_convert_encoding($F_VALUE, 'windows-1251', 'auto');
                        }
                        AddError($arFields);
                        die();
                }
        }
}
Function AddError($arFields)
{
        global $DB,$APPLICATION;

        if (!CModule::IncludeModule("iblock"))
                return "";

        $IBLOCK_ID = COption::GetOptionInt("altasib_errorsend", "ERROR_SEND_IBLOCK_ID");

        $LIMIT_IP = COption::GetOptionInt("altasib_errorsend", "limit_ip", 30);

        $IP_ADDRESS = $_SERVER["REMOTE_ADDR"];
        if(intval($LIMIT_IP) > 0 && $IBLOCK_ID>0 && $IP_ADDRESS)
        {

                $obElement = CIBlockElement::GetList(Array("id"=>"desc"), Array("IBLOCK_ID"=>$IBLOCK_ID, "PROPERTY_IP_ADDRESS" => $IP_ADDRESS), false, false, array("ID", "DATE_CREATE"));
                if($arElement = $obElement->Fetch())
                {
                        $site_format = CSite::GetDateFormat(); // DD.MM.YYYY HH:MI:SS
                        $stmp = MakeTimeStamp($arElement["DATE_CREATE"], $site_format);

                        if((time() - $stmp) < $LIMIT_IP)
                        {
                                echo GetMessage("ALTASIB_ERROR_SEND_ERROR_TEXT_LIMIT");
                                return;
                        }
                }
        }

        $SectionCode = date("m.Y", time());
        $obSection = CIBlockSection::GetList(Array($by=>$order), Array("CODE"=>$SectionCode), false);
        $arSection = $obSection->Fetch();
        if(!$arSection["ID"])
        {
                $bs = new CIBlockSection;
                $arSectionFields = Array(
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => $IBLOCK_ID,
                        "NAME" => $SectionCode,
                        "CODE" => $SectionCode,
                );
                $arSection["ID"]  = $bs->Add($arSectionFields);
                if(!$arSection["ID"])
                        echo "S:".$bs->LAST_ERROR;
        }

        $arFields["MESSAGE"] = $arFields["ERROR_TEXT_START"]."<font color='red'>".$arFields["ERROR_TEXT_BODY"]."</font>".$arFields["ERROR_TEXT_END"];
        $el = new CIBlockElement;
        $arAddFields = Array(
                "IBLOCK_ID"      =>$IBLOCK_ID,
                "IBLOCK_SECTION" => $arSection["ID"],
                "NAME"           => ConvertTimeStamp(time(), "FULL"),
                "ACTIVE"         => "Y",
                "PREVIEW_TEXT_TYPE" => "html",
                "PREVIEW_TEXT"   => $arFields["MESSAGE"],
                "DETAIL_TEXT_TYPE" => "html",
                "DETAIL_TEXT"    => $arFields["MESSAGE"]."<br /><br />\n\n".GetMessage("ALTASIB_ERROR_SEND_COMMENT").":<br />\n".$arFields["COMMENT"]."<br />",
        );
        $arAddFields["PROPERTY_VALUES"]["URL_ERROR"] = $arFields["ERROR_URL"];

        $arAddFields["PROPERTY_VALUES"]["IP_ADDRESS"] = $IP_ADDRESS;
        $ID = $el->Add($arAddFields);

        if(!$ID)
                echo "E: ".$el->LAST_ERROR;
        else
                echo "OK!";

        // to mail
        $defEmail = COption::GetOptionString("main", "email_from", "error@".str_replace("www.","",$_SERVER["SERVER_NAME"]));
        $arEventSend = Array(
                        "TEXT_MESSAGE"        => $arFields["MESSAGE"],
                        "COMMENT_MESSAGE"     => $arFields["COMMENT"],
                        "URL"                 => $arFields["ERROR_URL"],
                        "IP"                  => $IP_ADDRESS,
                        "EMAIL_TO"            => COption::GetOptionString("altasib_errorsend", "email_to", $defEmail),
        );

        CEvent::Send("ALTASIB_ERROR_SEND_MAIL", SITE_ID, $arEventSend);
}
?>
