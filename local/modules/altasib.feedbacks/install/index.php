<?
#################################################
#   Company developer: ALTASIB                  #
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

Class altasib_feedback extends CModule
{
        var $MODULE_ID = "altasib.feedback";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $MODULE_CSS;
        var $MODULE_GROUP_RIGHTS = "Y";
        function altasib_feedback()
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
                        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
                        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
                }
                $this->MODULE_NAME = GetMessage("ALTASIB_FEEDBACK_REG_MODULE_NAME");
                $this->MODULE_DESCRIPTION = GetMessage("ALTASIB_FEEDBACK_REG_MODULE_DESCRIPTION");
                $this->PARTNER_NAME = "ALTASIB";
                $this->PARTNER_URI = "http://www.altasib.ru/";
        }
        function DoInstall()
        {
                global $APPLICATION, $step;
                $step = IntVal($step);
                $this->InstallFiles();
                $this->InstallIblock();
                $this->UnInstallEvents();
                RegisterModule("altasib.feedback");
                $GLOBALS["errors"] = $this->errors;
                $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_FEEDBACK_REG_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/step1.php");
        }
        function DoUninstall()
        {
                global $APPLICATION, $step;
                $step = IntVal($step);
                if($step<2)
                {
                        $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_FEEDBACK_REG_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/unstep1.php");
                }
                elseif($step==2)
                {
                        UnRegisterModule("altasib.feedback");
                        $this->UnInstallFiles();
                        $this->UnInstallEvents();
                        $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_FEEDBACK_REG_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/unstep2.php");
                }
        }
        function InstallFiles()
        {
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/upload", $_SERVER["DOCUMENT_ROOT"]."/upload", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/js",$_SERVER["DOCUMENT_ROOT"]."/bitrix/js",true,true);
                return true;
        }
        function UnInstallFiles()
        {
                DeleteDirFilesEx("/bitrix/components/altasib/feedback.form");
                DeleteDirFilesEx("/upload/altasib.feedback.gif");
                return true;
        }
        function InstallIblock()
        {
                CModule::IncludeModule("iblock");
                //add type iblock
                $res = CIBlockType::GetByID("alx_feedback");
                if(!$v = $res->GetNext())
                {
                        $arFields = Array(
                                'ID'=>'altasib_feedback',
                                'SECTIONS'=>'Y',
                                'IN_RSS'=>'N',
                                'SORT'=>100,
                                'LANG'=>Array(
                                        'ru'=>Array(
                                        'NAME'=>GetMessage("ALTASIB_IB_FEEDBACK")
                                        )
                                )
                        );
                        $obBlocktype = new CIBlockType;
                        $obBlocktype->Add($arFields);
                }
                //add iblock
                $rsSites = CSite::GetList();
                $i = 0;
                while ($arSite = $rsSites->Fetch())
                {
                        $arSiteID[$i] = $arSite["ID"];
                        $i++;
                }
                $res = CIBlock::GetList(
                        Array(),
                        Array(
                                'TYPE'=>'altasib_feedback',
                                'CODE'=>'altasib_feedback'
                        ),
                        true
                );
                $check_ib = false;
                while($ar_res = $res->Fetch())
                        if($ar_res) $check_ib = true;
                if(!$check_ib)
                        for($i = 0; $i < count($arSiteID); $i++)
                        {
                                $ib = new CIBlock;
                                $arFields = Array(
                                        "ACTIVE" => "Y",
                                        "NAME" => GetMessage("ALTASIB_IB_FEEDBACK"),
                                        "CODE" => "altasib_feedback",
                                        "IBLOCK_TYPE_ID" => "altasib_feedback",
                                        "INDEX_ELEMENT" => "N",
                                        "INDEX_SECTION" => "N",
                                        "WORKFLOW" => "N",
                                        "SITE_ID" => $arSiteID[$i]
                                );
                                $ib->Add($arFields);
                        }
                //add props
                $res = CIBlock::GetList(Array(),Array("CODE"=>'altasib_feedback'),true);
                $ar_res = $res->Fetch();
                $rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$ar_res["ID"]));
                while ($arr=$rsProp->Fetch())
                        $arPropsCode[] = $arr["CODE"];
                if(!in_array("FIO", $arPropsCode))
                {
                        $arFields = Array(
                                "NAME" => GetMessage("ALTASIB_IB_FIO"),
                                "ACTIVE" => "Y",
                                "SORT" => "100",
                                "CODE" => "FIO",
                                "PROPERTY_TYPE" => "S",
                                "IBLOCK_ID" => $ar_res['ID']
                        );
                        $ibp = new CIBlockProperty;
                        $PropID = $ibp->Add($arFields);
                }
                if(!in_array("EMAIL", $arPropsCode))
                {
                        $arFields = Array(
                                "NAME" => GetMessage("ALTASIB_IB_EMAIL"),
                                "ACTIVE" => "Y",
                                "SORT" => "110",
                                "CODE" => "EMAIL",
                                "PROPERTY_TYPE" => "S",
                                "IBLOCK_ID" => $ar_res['ID']
                        );
                        $ibp = new CIBlockProperty;
                        $PropID = $ibp->Add($arFields);
                }
                $arFields = Array(
                        "NAME" => GetMessage("ALTASIB_IB_FILE"),
                        "ACTIVE" => "Y",
                        "SORT" => "120",
                        "CODE" => "FILE",
                        "PROPERTY_TYPE" => "F",
                        "IBLOCK_ID" => $ar_res['ID'],
                        "FILE_TYPE" => "jpg, gif, bmp, png, jpeg, doc, txt, rtf, zip, rar, 7z"
                );
                if(!in_array("FILE", $arPropsCode))
                {
                        $ibp = new CIBlockProperty;
                        $PropID = $ibp->Add($arFields);
                }
                else
                {
                       $resPropFile = CIBlockProperty::GetByID("FILE", $ar_res['ID']);
                       if($ar_resPropFile = $resPropFile->GetNext())
                       {
                               $ibp = new CIBlockProperty;
                               $PropID = $ibp->Update($ar_resPropFile["ID"], $arFields);
                       }
                }
                if(!in_array("USERIP", $arPropsCode))
                {
                       $arFields = Array(
                               "NAME" => GetMessage("ALTASIB_IB_USERIP"),
                               "ACTIVE" => "Y",
                               "CODE" => "USERIP",
                               "PROPERTY_TYPE" => "S",
                               "IBLOCK_ID" => $ar_res['ID'],
                       );
                       $ibp = new CIBlockProperty;
                       $PropID = $ibp->Add($arFields);
                }
                CIBlock::SetPermission($ar_res['ID'], Array("1"=>"X", "2"=>"R"));
        }
        function UnInstallEvents()
        {
                global $DB;
                $DB->Query("DELETE FROM b_event_type WHERE EVENT_NAME in ('ALX_FEEDBACK_FORM')");
                $DB->Query("DELETE FROM b_event_message WHERE EVENT_NAME in ('ALX_FEEDBACK_FORM')");
                $DB->Query("DELETE FROM b_event_type WHERE EVENT_NAME in ('ALX_FEEDBACK_FORM_SEND_MAIL')");
                $DB->Query("DELETE FROM b_event_message WHERE EVENT_NAME in ('ALX_FEEDBACK_FORM_SEND_MAIL')");
        }
}
?>
