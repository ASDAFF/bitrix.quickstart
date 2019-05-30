<?
IncludeModuleLangFile(__FILE__);


Class webes_itemconsist extends CModule
{
	const MODULE_ID = 'webes.itemconsist';
	var $MODULE_ID = 'webes.itemconsist';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("webes.ic_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("webes.ic_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("webes.ic_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("webes.ic_PARTNER_URI");
	}

    function InstallDB($arParams = array()) {
        global $DBType, $APPLICATION;
        $node_id = strlen($arParams["DATABASE"]) > 0? intval($arParams["DATABASE"]): false;
        if($node_id !== false) {
            $DB = $GLOBALS["DB"]->GetDBNodeConnection($node_id);
        }
        else {
            $DB = $GLOBALS["DB"];
        }
        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".strtolower($DBType)."/install.sql");
        if($this->errors !== false) {
            $APPLICATION->ThrowException(implode("<br>", $this->errors));
            return false;
        }
        return true;
    }

    function UnInstallDB($arParams = array()) {
        $DB = CDatabase::GetModuleConnection(self::MODULE_ID);

        // Удаляем таблицу
        $DB->Query("DROP TABLE IF EXISTS webes_ic_groups");
        $DB->Query("DROP TABLE IF EXISTS webes_ic_ib_params");
        $DB->Query("DROP TABLE IF EXISTS webes_ic_items");
        $DB->Query("DROP TABLE IF EXISTS webes_ic_set");

        return true;
    }


	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{

        if(is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/webes.itemconsist/install/components/'))
            CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/webes.itemconsist/install/components/',$_SERVER['DOCUMENT_ROOT'].'/bitrix/components', true, true);

        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/admin')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || $item == 'menu.php')
                        continue;
                    file_put_contents($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/' . $this->MODULE_ID . '/admin/' . $item . '");?' . '>');
                }
                closedir($dir);
            }
        }

		return true;
	}



	function UnInstallFiles()
	{
        if(is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/webes/itemconsist'))
            DeleteDirFilesEx('/bitrix/components/webes/itemconsist');

        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/admin')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.'){
                        continue;
                    }
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item);
                }
                closedir($dir);
            }
        }
		return true;
	}


	function DoInstall()
	{
		global $APPLICATION, $USER, $step;

        RegisterModule(self::MODULE_ID);
        $this->InstallFiles();
        $this->InstallDB();
        CModule::IncludeModule(self::MODULE_ID);
    }

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
	

}
?>