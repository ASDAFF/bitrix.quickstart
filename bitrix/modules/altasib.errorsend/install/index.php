<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2010 ALTASIB             #
#################################################
?>
<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

Class altasib_errorsend extends CModule
{
        var $MODULE_ID = "altasib.errorsend";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $MODULE_CSS;
//        var $MODULE_GROUP_RIGHTS = "Y";

        function altasib_errorsend()
        {
                $arModuleVersion = array();

                $path = str_replace("\\", "/", __FILE__);
                $path = substr($path, 0, strlen($path) - strlen("/index.php"));
                include($path."/version.php");

                if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
                {
                        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
                }
                else
                {
                        $this->MODULE_VERSION = "1.1.0";
                        $this->MODULE_VERSION_DATE =  "2011-08-05 23:47:00";//"2010-11-17 15:47:00";
                }

                $this->MODULE_NAME = GetMessage("ALTASIB_ERROR_SEND_MODULE_NAME");
                $this->MODULE_DESCRIPTION = GetMessage("ALTASIB_ERROR_SEND_MODULE_DESCRIPTION");

                $this->PARTNER_NAME = "ALTASIB";
                $this->PARTNER_URI = "http://www.altasib.ru/";
        }
        function DoInstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->InstallFiles();
                $this->InstallDB();
                $this->InstallEvents();

                $GLOBALS["errors"] = $this->errors;
                $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_ERROR_SEND_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.errorsend/install/step1.php");
        }
        function DoUninstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->UnInstallDB();
                $this->UnInstallEvents();
                $this->UnInstallFiles();
                $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_ERROR_SEND_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.errorsend/install/unstep1.php");
        }
        function InstallDB()
        {
                global $DB, $DBType, $APPLICATION;
                $this->errors = false;

                CModule::IncludeModule("iblock");

                $arLang = array();
                $l = CLanguage::GetList($lby="sort", $lorder="asc");
                while($ar = $l->ExtractFields("l_"))
                        $arIBTLang[]=$ar;

                for($i=0; $i<count($arIBTLang); $i++)
                {

                        if($arIBTLang[$i]["LID"]=="ru")
                                $NAME = GetMessage("ALTASIB_ERROR_SEND_IBLOCK_TYPE_NAME");
                        else
                                $NAME = GetMessage("ALTASIB_ERROR_SEND_IBLOCK_TYPE_NAME_EN");

                        $arLang[$arIBTLang[$i]["LID"]] = array("NAME" => $NAME);
                }
                $arFields = array(
                        "ID" => GetMessage("ALTASIB_ERROR_SEND_IBLOCK_TYPE_NAME_EN"),
                        "LANG" => $arLang,
                        "SECTIONS" => "Y");

                $obBlocktype = new CIBlockType;
                if(!CIBlockType::GetByID(GetMessage("ALTASIB_ERROR_SEND_IBLOCK_TYPE_NAME_EN"))->Fetch())
                $IBLOCK_TYPE_ID = $obBlocktype->Add($arFields);

                if(!$IBLOCK_TYPE_ID)
                {
                   echo $obBlocktype->LAST_ERROR;
                }

                $arSites = Array();
                $obSites = CSite::GetList();
                while($arSite = $obSites->Fetch())
                {
                        $arSites[] = $arSite["ID"];
                }

                $arIB = CIBlock::GetList(false,Array("CODE"=>"spelling_errors_site"))->Fetch();
                if(!$arIB)
                {
                        $ib = new CIBlock;
                        $arFields = Array(
                                "NAME" => GetMessage("ALTASIB_ERROR_SEND_IBLOCK_TYPE_NAME"),
                                "CODE" => "spelling_errors_site",
                                "LIST_PAGE_URL" =>"",
                                "DETAIL_PAGE_URL" =>"",
                                "SITE_ID" => $arSites,
                                "IBLOCK_TYPE_ID" => GetMessage("ALTASIB_ERROR_SEND_IBLOCK_TYPE_NAME_EN"),
                                "INDEX_ELEMENT" => "N",
                                "INDEX_SECTION" => "N",
                        );
                        $IBLOCK_ID = $ib->Add($arFields);

                        if(!$IBLOCK_ID)
                        {
                           echo $ib->LAST_ERROR;
                           die();
                        }
                        else
                        {
                                CIBlock::SetPermission($IBLOCK_ID, Array("2"=>"R"));

                                COption::SetOptionInt("altasib_errorsend", "ERROR_SEND_IBLOCK_ID", $IBLOCK_ID);

                                $arFields = Array(
                                      "NAME" => GetMessage("ALTASIB_ERROR_SEND_IBLOCK_URL_PROPERTY_NAME"),
                                      "ACTIVE" => "Y",
                                      "SORT" => "100",
                                      "CODE" => "URL_ERROR",
                                      "PROPERTY_TYPE" => "S",
                                      "IBLOCK_ID" => $IBLOCK_ID,
                                );
                                $ibp = new CIBlockProperty;
                                $PropID = $ibp->Add($arFields);

                                if(!$PropID)
                                {
                                   echo $ibp->LAST_ERROR;
                                   die();
                                }

                                $arFields = Array(
                                      "NAME" => "IP",
                                      "ACTIVE" => "Y",
                                      "SORT" => "100",
                                      "CODE" => "IP_ADDRESS",
                                      "PROPERTY_TYPE" => "S",
                                      "IBLOCK_ID" => $IBLOCK_ID,
                                );
                                $ibp = new CIBlockProperty;
                                $PropID = $ibp->Add($arFields);
                                if(!$PropID)
                                {
                                   echo $ibp->LAST_ERROR;
                                   die();
                                }
                        }
                }
                else
                        COption::SetOptionInt("altasib_errorsend", "ERROR_SEND_IBLOCK_ID", $arIB["ID"]);

                RegisterModule("altasib.errorsend");
                RegisterModuleDependences("main","OnProlog","altasib.errorsend","ErrorSendMD","ErrorSendOnProlog", "100");
                RegisterModuleDependences("main","OnBeforeEndBufferContent","altasib.errorsend","ErrorSendMD","ErrorSendOnBeforeEndBufferContent", "100");
        }
        function UnInstallDB($arParams = array())
        {
                global $DB, $DBType, $APPLICATION;
                $this->errors = false;

                UnRegisterModuleDependences("main", "OnProlog", "altasib.errorsend", "ErrorSendMD", "ErrorSendOnProlog");
                UnRegisterModuleDependences("main", "OnBeforeEndBufferContent", "altasib.errorsend", "ErrorSendMD", "ErrorSendOnBeforeEndBufferContent");
                COption::RemoveOption("altasib_errorsend");
                UnRegisterModule("altasib.errorsend");

                return true;

        }
        Function InstallEvents()
        {
                $rsET = CEventType::GetList(Array("TYPE_ID" => "ALTASIB_ERROR_SEND_MAIL"));
                if(!$arET = $rsET->Fetch())
                {
                        $arSites = Array();
                        $obSites = CSite::GetList();
                        while($arSite = $obSites->Fetch())
                        {
                                $arSites[] = $arSite["ID"];
                        }

                        $et = new CEventType;
                        $ID = $et->Add(array(
                                "SITE_ID"       => "ru",
                                "EVENT_NAME"    => "ALTASIB_ERROR_SEND_MAIL",
                                "NAME"          => GetMessage("ALTASIB_ERROR_SEND_EVENT_NAME"),
                                "DESCRIPTION"   => GetMessage("ALTASIB_ERROR_SEND_EVENT_DESC")
                        ));
                        if(!$ID)
                                echo $et->LAST_ERROR;

                        $emess = new CEventMessage;
                        $arMessage = Array(
                                "ACTIVE"        =>        "Y",
                                "LID"           =>        $arSites,
                                "EVENT_NAME"    =>        "ALTASIB_ERROR_SEND_MAIL",
                                "EMAIL_FROM"    =>        "#DEFAULT_EMAIL_FROM#",
                                "EMAIL_TO"      =>        "#EMAIL_TO#",
                                "SUBJECT"       =>        GetMessage("ALTASIB_ERROR_SEND_EVENT_NAME"),
                                "BODY_TYPE"     =>        "html",
                                "MESSAGE"       =>        GetMessage("ALTASIB_ERROR_SEND_EVENT_MESSAGE")
                        );
                        if(!$emess->Add($arMessage))
                                echo $emess->LAST_ERROR;
                }
        }

        Function UnInstallEvents()
        {
                global $DB;
                $DB->Query("DELETE FROM b_event_type WHERE EVENT_NAME in ('ALTASIB_ERROR_SEND_MAIL')");
                $DB->Query("DELETE FROM b_event_message WHERE EVENT_NAME in ('ALTASIB_ERROR_SEND_MAIL')");
        }

        function InstallFiles()
        {
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.errorsend/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/altasib.errorsend", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.errorsend/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/altasib.errorsend", true, true);
                return true;
        }

        function UnInstallFiles()
        {
                DeleteDirFilesEx("/bitrix/js/altasib.errorsend");
                DeleteDirFilesEx("/bitrix/images/altasib.errorsend");

                return true;
        }
}
?>
