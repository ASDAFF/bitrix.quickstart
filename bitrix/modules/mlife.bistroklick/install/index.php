<?
IncludeModuleLangFile(__FILE__);

class mlife_bistroklick extends CModule
{
	var $MODULE_ID = "mlife.bistroklick";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function mlife_bistroklick()
	{
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = GetMessage("MLIFE_COMPANY_NAME");
		$this->PARTNER_URI = "http://mlife-media.by/";
		$this->MODULE_NAME = GetMessage("MLIFE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MLIFE_MODULE_DESCRIPTION");
		return true;
	}

	function DoInstall()
	{
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.bistroklick/install/components",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/components/mlife", true, true);
		RegisterModule($this->MODULE_ID);
		$this->InstallDB();
		$this->addPostsEvent();
	}

	function DoUninstall()
	{
		UnRegisterModule($this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/components/mlife/mlife.bistroklick");
		$this->deletePostEvents();
	}
	
	function InstallDB() {
		return true;
	}
	
	function addPostsEvent() {
		
		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="desc", Array());
		while ($arSite = $rsSites->Fetch()) $siteList[] = $arSite["ID"];
		
		//добавляем тип
		$et = new CEventType;
		$evTypeId = $et->Add(array(
		"LID" => 'ru',
        "EVENT_NAME"    => 'MLIFE_BISTROKLICK',
        "NAME"          => GetMessage('MLIFE_BK_EVENT_TYPE_NAME'),
        "DESCRIPTION"   => GetMessage('MLIFE_BK_EVENT_TYPE_DESC'),
        ));
		
		//добавляем почтовые шаблоны
		if($evTypeId) {	
			$arr = array();
			$arr[] = array(
				"ACTIVE" => "Y", "EVENT_NAME" => "MLIFE_BISTROKLICK", "LID" => $siteList, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#USER_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_BK_EVENT_MESS1_T'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_BK_EVENT_MESS1'),
			);
			$arr[] = array(
				"ACTIVE" => "Y", "EVENT_NAME" => "MLIFE_BISTROKLICK", "LID" => $siteList, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#USER_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_BK_EVENT_MESS2_T'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_BK_EVENT_MESS2'),
			);
			$arr[] = array(
				"ACTIVE" => "Y", "EVENT_NAME" => "MLIFE_BISTROKLICK", "LID" => $siteList, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#USER_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_BK_EVENT_MESS3_T'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_BK_EVENT_MESS3'),
			);
			$arr[] = array(
				"ACTIVE" => "Y", "EVENT_NAME" => "MLIFE_BISTROKLICK", "LID" => $siteList, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#USER_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_BK_EVENT_MESS4_T'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_BK_EVENT_MESS4'),
			);
			$arr[] = array(
				"ACTIVE" => "Y", "EVENT_NAME" => "MLIFE_BISTROKLICK", "LID" => $siteList, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#SEND_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_BK_EVENT_MESS5_T'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_BK_EVENT_MESS5'),
			);
			$arr[] = array(
				"ACTIVE" => "Y", "EVENT_NAME" => "MLIFE_BISTROKLICK", "LID" => $siteList, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#SEND_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_BK_EVENT_MESS6_T'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_BK_EVENT_MESS6'),
			);

			
			foreach($arr as $eventMess) {
				$emess = new CEventMessage;
				$emess->Add($eventMess);
			}

			unset($arr);
		}
		return true;
	}
	
	function deletePostEvents() {
		global $DB;
		//удалить тип почтового события
		$et = new CEventType;
		$et->Delete("MLIFE_BISTROKLICK");
		//удалить почтовые шаблоны для данного события
		$arFilter = array('TYPE_ID'=>'MLIFE_BISTROKLICK');
		$rsMess = CEventMessage::GetList($by="site_id", $order="desc", $arFilter);
		while ($arMess = $rsMess->Fetch()) {
			$emessage = new CEventMessage;
			$DB->StartTransaction();
			if(!$emessage->Delete($arMess['ID']))
			{
				$DB->Rollback();
			}
			else $DB->Commit();
		}
		return;
	}
}
?>