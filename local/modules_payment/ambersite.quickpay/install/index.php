<?
global $MESS;
$strPath2Lang = str_replace('\\', '/', __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class ambersite_quickpay extends CModule
{
var $MODULE_ID = "ambersite.quickpay";
var $MODULE_VERSION;
var $MODULE_VERSION_DATE;
var $MODULE_NAME;
var $MODULE_DESCRIPTION;
var $MODULE_CSS;
var $PARTNER_NAME;
var $PARTNER_URI;

	function ambersite_quickpay()
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
	
	$this->MODULE_NAME = GetMessage("MODULE_NAME");
	$this->MODULE_DESCRIPTION = GetMessage("MODULE_DESCRIPTION");
	$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
	$this->PARTNER_URI = GetMessage("PARTNER_URI");
	}
	
	function InstallEventMess() {
		function UET($EVENT_NAME, $NAME, $LID, $DESCRIPTION) 
		{ 
			$et = new CEventType; 
			$typeid = $et->Add(array( 
				"LID"           => $LID, 
				"EVENT_NAME"    => $EVENT_NAME, 
				"NAME"          => $NAME, 
				"DESCRIPTION"   => $DESCRIPTION 
				)); 
			return $typeid; 
		} 
		$rsSites = CSite::GetList($by="sort", $order="asc", array()); $arsites = array();
		while ($arSite = $rsSites->Fetch()) {$arsites[] = $arSite['LID'];}
		
		$typeidAdd = UET("AS_QUICKPAY_ADD", GetMessage("UVEDOMLENIE_O_NOVOM_ZAKAZE"), "ru", GetMessage("AS_QUICKPAY_ADD_DESC")); 		
		$arrAdd["ACTIVE"] = "Y"; 
		$arrAdd["EVENT_NAME"] = "AS_QUICKPAY_ADD"; 
		$arrAdd["LID"] = $arsites; 
		$arrAdd["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#"; 
		$arrAdd["EMAIL_TO"] = "mail@mail.com"; 
		$arrAdd["SUBJECT"] = GetMessage("DOBAVLEN_NOVUJ_ZAKAZ_NA_OPLATY"); 
		$arrAdd["BODY_TYPE"] = "text"; 
		$arrAdd["MESSAGE"] = GetMessage("AS_QUICKPAY_ADD_TEXT"); 		
		if(intval($typeidAdd)>0) {$emessAdd = new CEventMessage; $emessAdd->Add($arrAdd);} else return false;
		
		$typeidConfirm = UET("AS_QUICKPAY_CONFIRM", GetMessage("UVEDOMLENIE_OB_OPLATE_ZAKAZA"), "ru", GetMessage("AS_QUICKPAY_CONFIRM_DESC")); 		
		$arrConfirm["ACTIVE"] = "Y"; 
		$arrConfirm["EVENT_NAME"] = "AS_QUICKPAY_CONFIRM"; 
		$arrConfirm["LID"] = $arsites; 
		$arrConfirm["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#"; 
		$arrConfirm["EMAIL_TO"] = "mail@mail.com"; 
		$arrConfirm["SUBJECT"] = GetMessage("OPLACHEN_ZAKAZ"); 
		$arrConfirm["BODY_TYPE"] = "text"; 
		$arrConfirm["MESSAGE"] = GetMessage("AS_QUICKPAY_CONFIRM_TEXT"); 		
		if(intval($typeidConfirm)>0) {$emessConfirm = new CEventMessage; $emessConfirm->Add($arrConfirm);} else return false;
		
		return true;
	}
	
	function UninstallEventMess() {
		$rsMessAdd = CEventMessage::GetList($by="site_id", $order="desc", array('TYPE_ID' => 'AS_QUICKPAY_ADD'));
		$delMessAdd = false; while ($arMessAdd = $rsMessAdd->Fetch()) {CEventMessage::Delete($arMessAdd['ID']); $delMessAdd = true;}
		if($delMessAdd) {CEventType::Delete('AS_QUICKPAY_ADD');}
		$rsMessConfirm = CEventMessage::GetList($by="site_id", $order="desc", array('TYPE_ID' => 'AS_QUICKPAY_CONFIRM'));
		$delMessConfirm = false; while ($arMessConfirm = $rsMessConfirm->Fetch()) {CEventMessage::Delete($arMessConfirm['ID']); $delMessConfirm = true;}
		if($delMessConfirm) {CEventType::Delete('AS_QUICKPAY_CONFIRM');}
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION, $DB, $DBType, $CACHE_MANAGER; $this->errors = false;
		RegisterModule($this->MODULE_ID);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/asquickpay.php", $_SERVER["DOCUMENT_ROOT"], false, false);
		$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$this->MODULE_ID."/install/db/".$DBType."/install.sql");
		$this->InstallEventMess();
		$CACHE_MANAGER->CleanDir("fileman_component_tree");
		$this->InstallDB();
		if($this->errors !== false) {$APPLICATION->ThrowException(implode("", $this->errors)); return false;}
	}

	function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION, $DB, $DBType, $step, $CACHE_MANAGER; $this->errors = false; $step = IntVal($step);
		if(!$_REQUEST["step"]) $APPLICATION->IncludeAdminFile(GetMessage("IBLOCK_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep.php");
		if($_REQUEST["step"]=="2") {
			DeleteDirFilesEx("/bitrix/components/".$this->MODULE_ID);
			DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
			DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
			DeleteDirFilesEx("/bitrix/themes/.default/icons/".$this->MODULE_ID);
			if($_REQUEST["savedata"]<>"Y") $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$this->MODULE_ID."/install/db/".$DBType."/uninstall.sql");
			if($_REQUEST["savemess"]<>"Y") $this->UninstallEventMess();
			$this->UnIstallDB();
			COption::RemoveOption($this->MODULE_ID);
			UnRegisterModule($this->MODULE_ID);
			$CACHE_MANAGER->CleanDir("fileman_component_tree");
			if($this->errors !== false) {$APPLICATION->ThrowException(implode("", $this->errors)); return false;}
		}
	}
	
	function InstallDB() {}
	function UnIstallDB() {}
}
?>