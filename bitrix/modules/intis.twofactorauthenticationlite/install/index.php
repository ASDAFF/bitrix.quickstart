<?
/////////////////////////////
//INTIS LLC. 2013          //
//Tel.: 8 800-333-12-02    //
//www.sms16.ru             //
//Ruslan Semagin           //
//Skype: pixel365          //
/////////////////////////////

global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

Class intis_twofactorauthenticationlite extends CModule
{
    var $MODULE_ID = "intis.twofactorauthenticationlite";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function intis_twofactorauthenticationlite()
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

        $this->MODULE_NAME = GetMessage("TWOFACTORAUTHENTIFICATIONLITE_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("TWOFACTORAUTHENTIFICATIONLITE_MODULE_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("TWOFACTORAUTHENTIFICATIONLITE_PARTNER_NAME");
        $this->PARTNER_URI = "http://www.sms16.ru";
    }

    function InstallDB()
    {
        RegisterModule("intis.twofactorauthenticationlite");

        CModule::IncludeModule("iblock");

        $prefix = date("dmY").date("his");

        $createIBlockType =  new CIBlockType;
        $createIBlock = new CIBlock;

        $sID= array();
        $rsSites = CSite::GetList($by="sort", $order="desc", Array("NAME" => ""));
        while ($arSite = $rsSites->Fetch())
        {
            $sID[] = $arSite['LID'];
        }

        //Iblock type
        $newIblockType = "intis_ip_blocked_".$prefix;
        $arIblockTypeFields = Array(
            "ID"=>$newIblockType,
            "SECTIONS"=>"Y",
            "LANG"=>Array(
                "ru"=>Array(
                    "NAME"=> GetMessage('TWOFACTORAUTHENTIFICATIONLITE_CREATE_IBLOCK_TYPE_NAME')." ".$prefix,
                )
            )
        );
        //Add iblock type
        $res = $createIBlockType->Add($arIblockTypeFields);

        //Iblock
        $arIblockFields = Array(
            "ACTIVE" => "Y",
            "NAME" => GetMessage('TWOFACTORAUTHENTIFICATIONLITE_CREATE_IBLOCK_TYPE_NAME')." ".$prefix,
            "CODE" => "intis_ip_blocked",
            "LIST_PAGE_URL" => "",
            "DETAIL_PAGE_URL" => "",
            "IBLOCK_TYPE_ID" => $newIblockType,
            "SITE_ID" => $sID,
            "SORT" => "100",
            "PICTURE" => "",
            "DESCRIPTION" => GetMessage('TWOFACTORAUTHENTIFICATIONLITE_CREATE_IBLOCK_DESCRIPTION'),
            "DESCRIPTION_TYPE" => "text",
            "GROUP_ID" => Array("1"=>"R")
        );
        $customIblockId = $createIBlock->Add($arIblockFields);

        COption::SetOptionString("intis.twofactorauthenticationlite", "ONE_TIME_PASSWORD_TEMPLATE_FIELD", "123456789");
        COption::SetOptionString("intis.twofactorauthenticationlite", "ONE_TIME_PASSWORD_TEMPLATE_SYMBOL_FIELD", "10");
        COption::SetOptionString("intis.twofactorauthenticationlite", "SELECT_USER_PHONE_IN_FIELDS_FIELD", "PERSONAL_PHONE");
        COption::SetOptionString("intis.twofactorauthenticationlite", "IBLOCK_WITH_DATA", $customIblockId);

        CAgent::AddAgent("CIntisTwoFactorAuthentificationLite::DelayedLocking();","intis.twofactorauthenticationlite", "Y", 60);

        return true;
    }

    function UnInstallDB()
    {
        COption::RemoveOption("intis.twofactorauthenticationlite");
        CAgent::RemoveAgent("CIntisTwoFactorAuthentificationLite::DelayedLocking();", "intis.twofactorauthenticationlite");
        UnRegisterModule("intis.twofactorauthenticationlite");
        return true;
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intis.twofactorauthenticationlite/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }

    function CreateUserField()
    {
        $dbUserField = CUserTypeEntity::GetList(
            array(
                "SORT" => "ASC"
            ),
            array(
                "FIELD_NAME" => "UF_INTIS_ONETIMEPASS"
            )
        );
        if(!$dbUserField->Fetch()){
            $ob = new CUserTypeEntity();
            $arFields = array(
                "ENTITY_ID" => "USER",
                "FIELD_NAME" => "UF_INTIS_ONETIMEPASS",
                "USER_TYPE_ID" => "string",
                "XML_ID" => "",
                "SORT" => 100,
                "MULTIPLE" => "N",
                "MANDATORY" => "N",
                "SHOW_FILTER" => "N",
                "SHOW_IN_LIST" => "Y",
                "EDIT_IN_LIST" => "Y",
                "IS_SEARCHABLE" => "N"
            );

            $FIELD_ID = $ob->Add($arFields);
        }
    }
	
	function CreateUserFieldConfirm()
    {
        $dbUserField = CUserTypeEntity::GetList(
            array(
                "SORT" => "ASC"
            ),
            array(
                "FIELD_NAME" => "UF_INTIS_CONFIRM"
            )
        );
        if(!$dbUserField->Fetch()){
            $ob = new CUserTypeEntity();
            $arFields = array(
                "ENTITY_ID" => "USER",
                "FIELD_NAME" => "UF_INTIS_CONFIRM",
                "USER_TYPE_ID" => "string",
                "XML_ID" => "",
                "SORT" => 100,
                "MULTIPLE" => "N",
                "MANDATORY" => "N",
                "SHOW_FILTER" => "N",
                "SHOW_IN_LIST" => "Y",
                "EDIT_IN_LIST" => "Y",
                "IS_SEARCHABLE" => "N"
            );

            $FIELD_ID = $ob->Add($arFields);
        }
    }

    function __AdminIsCurlInstalled()
    {
        if  (in_array  ('curl', get_loaded_extensions()))
        {
            return true;
        }
        else{
            return false;
        }
    }

    function DoInstall()
    {
        global $DB, $APPLICATION;
        if ($this->__AdminIsCurlInstalled())
        {
            $this->InstallDB();
            $this->InstallFiles();
            $this->CreateUserField();
            $this->CreateUserFieldConfirm();
            $APPLICATION->IncludeAdminFile(GetMessage("TWOFACTORAUTHENTIFICATIONLITE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intis.twofactorauthenticationlite/install/step.php");
        }else{
            $APPLICATION->IncludeAdminFile(GetMessage("TWOFACTORAUTHENTIFICATIONLITE_NOINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intis.twofactorauthenticationlite/install/error.php");
        }
    }

    function DoUninstall()
    {
        global $APPLICATION, $DB;
        $this->UnInstallFiles();
        $this->UnInstallDB();
        $APPLICATION->IncludeAdminFile(GetMessage("TWOFACTORAUTHENTIFICATIONLITE_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intis.twofactorauthenticationlite/install/unstep.php");
    }
}
?>