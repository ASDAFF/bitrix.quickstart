<?
IncludeModuleLangFile(__FILE__);

class mlife_minilanding extends CModule
{
	var $MODULE_ID = "mlife.minilanding";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function mlife_minilanding()
	{
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = GetMessage("MLIFE_MINILANDING_COMPANY_NAME");
		$this->PARTNER_URI = "http://mlife-media.by/";
		$this->MODULE_NAME = GetMessage("MLIFE_MINILANDING_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MLIFE_MINILANDING_MODULE_DESCRIPTION");
		return true;
	}
	
	function DoInstall()
	{
		global $APPLICATION;
		
		if (!IsModuleInstalled("mlife.minilanding")) { 
			$this->InstallDB(false);
			$this->InstallFiles();
			$this->InstallEvents();
			$this->createAgents();
		}

	}

	function DoUninstall()
	{
		global $APPLICATION;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		$this->deleteAgents();
	}
	
	function InstallDB($install_wizard = true)
	{
		global $DB, $DBType, $APPLICATION;

		RegisterModule("mlife.minilanding");
		RegisterModuleDependences("main", "OnBeforeProlog", "mlife.minilanding", "СMlifeSiteMinilanding", "ShowPanel");

		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		global $DB, $DBType, $APPLICATION;

		UnRegisterModuleDependences("main", "OnBeforeProlog", "mlife.minilanding", "СMlifeSiteMinilanding", "ShowPanel"); 
		UnRegisterModule("mlife.minilanding");

		return true;
	}

	function InstallEvents()
	{
		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="desc", Array());
		while ($arSite = $rsSites->Fetch()) $siteList[] = $arSite["ID"];
		
		//добавляем тип
		$et = new CEventType;
		$evTypeId = $et->Add(array(
		"LID" => 'ru',
        "EVENT_NAME"    => 'MLIFE_MINILANDING',
        "NAME"          => GetMessage('MLIFE_MINILANDING_EVENT_TYPE_NAME'),
        "DESCRIPTION"   => GetMessage('MLIFE_MINILANDING_EVENT_TYPE_DESC'),
        ));
		
		//добавляем почтовые шаблоны
		if($evTypeId) {	
			$arr = array();
			$arr[] = array(
				"ACTIVE" => "Y", "EVENT_NAME" => "MLIFE_MINILANDING", "LID" => $siteList, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#SEND_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_MINILANDING_EVENT_MESS1_T'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_MINILANDING_EVENT_MESS1'),
			);
			$arr[] = array(
				"ACTIVE" => "Y", "EVENT_NAME" => "MLIFE_MINILANDING", "LID" => $siteList, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#SEND_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_MINILANDING_EVENT_MESS2_T'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_MINILANDING_EVENT_MESS2'),
			);
			$arr[] = array(
				"ACTIVE" => "Y", "EVENT_NAME" => "MLIFE_MINILANDING", "LID" => $siteList, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#SEND_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_MINILANDING_EVENT_MESS3_T'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_MINILANDING_EVENT_MESS3'),
			);
			
			$i=0;
			foreach($arr as $eventMess) {
				$i++;
				$emess = new CEventMessage;
				$id = $emess->Add($eventMess);
				COption::SetOptionString("mlife.minilanding","event".$i,$id);
			}

			unset($arr);
		}
		
		return true;
	}

	function UnInstallEvents()
	{
		
		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.minilanding/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.minilanding/install/wizards", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards", true, true);
		return true;
	}

	function InstallPublic()
	{
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/mlife/mlife.minilanding.form.ajax");
		//DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/mlife/minilanding"); - один фиг не фурычит
		return true;
	}
	
	function createAgents() {
		CAgent::AddAgent(
		"CMlifeMinilanding::SetDateShare(8);",
		"mlife.minilanding",
		"N",
		1200);
	}
	
	function deleteAgents() {
		CAgent::RemoveModuleAgents("mlife.minilanding");
	}

}
?>