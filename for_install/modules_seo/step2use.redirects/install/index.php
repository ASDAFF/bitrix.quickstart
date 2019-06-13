<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));


Class step2use_redirects extends CModule
{
    const MODULE_ID = 'step2use.redirects';
	var $MODULE_ID = "step2use.redirects";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function step2use_redirects()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("S2U_REDIRECT_TITLE");
		$this->MODULE_DESCRIPTION = GetMessage("S2U_REDIRECT_DESC");
		$this->PARTNER_NAME = GetMessage("S2U_REDIRECT_ATLANT");
		$this->PARTNER_URI = 'https://atlant2010.ru/';
	}

	function DoInstall()
	{
		global $DB, $DBType, $APPLICATION;
        
        $this->InstallFiles();
        $this->installDB();
		
		// Это агент для фонового сжатия
		CAgent::AddAgent(
			"S2uRedirects::DeleteOldEntities();",     // имя функции
			"step2use.redirects",                         // идентификатор модуля
			"N",                                  // агент критичен к кол-ву запусков
			86400,                          // интервал запуска
			"",                             // когда проверить первый запуск? (сейчас)
			"Y"                          // агент активен
		);
		
		$APPLICATION->IncludeAdminFile(GetMessage("INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $this->MODULE_ID . "/install/install.php");
	}

	function DoUninstall()
	{
		global $DB, $DBType, $APPLICATION, $step;
		
		$step = IntVal($step);
		if($step<2)
			$APPLICATION->IncludeAdminFile(GetMessage("UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $this->MODULE_ID . "/install/uninstall1.php");
		elseif($step == 2) 
			$APPLICATION->IncludeAdminFile(GetMessage("UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $this->MODULE_ID . "/install/uninstall.php");

        CAgent::RemoveModuleAgents("step2use.redirects");
	}
	
	function GetModuleRightList()
	{
		$arr = array(
			"reference_id" => array("D", "F"), /*,"R","W","C",*/
			"reference" => array(
				"[D] ".GetMessage("S2U_REDIRECT_DENIED"),
				//"[R] ".GetMessage("S2U_REDIRECT_READ"),
				//"[W] ".GetMessage("S2U_REDIRECT_WRITE"),
				//"[C] ".GetMessage("S2U_REDIRECT_ALBUM_CREATING"),
				"[F] ".GetMessage("S2U_REDIRECT_FULL")
			)
		);
		return $arr;
	}
    
    function installDB() {
        global $DBType, $APPLICATION;
        
        $node_id = strlen($arParams["DATABASE"]) > 0? intval($arParams["DATABASE"]): false;

		if($node_id !== false)
			$DB = $GLOBALS["DB"]->GetDBNodeConnection($node_id);
		else
			$DB = $GLOBALS["DB"];

        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/step2use.redirects/install/db/".strtolower($DBType)."/install.sql");
        if($this->errors !== false) {
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
    }
    
    /**
	 * Копирует файлы модуля в нужные места.
	 * @return boolean
	 */
	public function InstallFiles() {
		$path_from = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/step2use.redirects/install';
		$path_to = $_SERVER['DOCUMENT_ROOT'];

        $result = CopyDirFiles($path_from.'/tools', $path_to.'/bitrix/tools', true, true, false);

		return $result;
	}
}
?>
