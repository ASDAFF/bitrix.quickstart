<?

Class wl_form extends CModule {

    var $MODULE_ID = "wl.form";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function wl_form() {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        $path = substr($path, 0, strlen($path) - strlen("/install"));
        @include(GetLangFileName($path . "/lang/", "/install/index.php"));
        IncludeModuleLangFile($path . "/install/index.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = GetMessage("WL_FORM_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("WL_FORM_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = GetMessage('WL_FORM_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('WL_FORM_MODULE_PARTNER_URI');
    }

    function InstallFiles() {
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        CopyDirFiles($path . "/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
        return true;
    }

    function UnInstallFiles() {
        DeleteDirFilesEx("/bitrix/components/wlcomponents/form");
        return true;
    }

    function InstallLetters() {
        $rsType = CEventType::GetList(Array("TYPE_ID" => "WL_FORM_ADMIN_NOTIFICATION"));
        if (!$arType = $rsType->fetch()) {
            $et = new CEventType;
            $et->Add(array(
                "LID" => "ru",
                "EVENT_NAME" => "WL_FORM_ADMIN_NOTIFICATION",
                "NAME" => "WlAgency: " . GetMessage("WL_FORM_ADMIN_NOTIFICATION"),
                "DESCRIPTION" => "#TEXT# - " . GetMessage("WL_FORM_ADMIN_NOTIFICATION_TXT"),
            ));
        }

        $rsMessage = CEventMessage::GetList($by = 'id', $order = 'asc', Array("TYPE_ID" => "WL_FORM_ADMIN_NOTIFICATION"));
        if (!$arMessage = $rsMessage->fetch()) {
            $arSites = Array();
            $rsSites = CSite::GetList($by = 'sort', $order = 'asc');
            while ($arSite = $rsSites->fetch())
                $arSites[] = $arSite["ID"];
            $emess = new CEventMessage;
            $emess->Add(Array(
                "ACTIVE" => "Y",
                "EVENT_NAME" => "WL_FORM_ADMIN_NOTIFICATION",
                "LID" => $arSites,
                "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
                "EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
                "BCC" => "#BCC#",
                "SUBJECT" => "#SITE_NAME#: " . GetMessage("WL_FORM_ADMIN_NOTIFICATION_TXT"),
                "BODY_TYPE" => "text",
                "MESSAGE" => "#TEXT#",
            ));
        }
    }

    function UnIstallLetters() {
        $rsMessage = CEventMessage::GetList($by = 'id', $order = 'asc', Array("TYPE_ID" => "WL_FORM_ADMIN_NOTIFICATION"));
        if ($arMessage = $rsMessage->fetch()) {
            $emessage = new CEventMessage;
            $emessage->Delete($arMessage["ID"]);
        }

        $rsType = CEventType::GetList(Array("TYPE_ID" => "WL_FORM_ADMIN_NOTIFICATION"));
        if ($arType = $rsType->fetch()) {
            $et = new CEventType;
            $et->Delete("WL_FORM_ADMIN_NOTIFICATION");
        }
    }

    function DoInstall() {
        global $APPLICATION;
        $this->InstallFiles();
        $this->InstallLetters();
        RegisterModule($this->MODULE_ID);
    }

    function DoUninstall() {
        global $APPLICATION;
        $this->UnInstallFiles();
        $this->UnIstallLetters();
        UnRegisterModule($this->MODULE_ID);
    }

}

?>