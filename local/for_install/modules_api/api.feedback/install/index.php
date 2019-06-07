<?
IncludeModuleLangFile(__FILE__);

Class api_feedback extends CModule
{
	var $MODULE_ID = 'api.feedback';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION      = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME         = GetMessage("api.feedback_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("api.feedback_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("api.feedback_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("api.feedback_PARTNER_URI");
	}

	function DoInstall()
	{
		$this->InstallFiles();
		//$this->InstallDB();
		$this->InstallEvents();
		RegisterModule($this->MODULE_ID);
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/" . $this->MODULE_ID, true, true);

		return true;
	}

	function InstallEvents()
	{
		global $DB;

		//RegisterModuleDependences($this->MODULE_ID, 'OnBeforeEmailSend', $this->MODULE_ID);
		//RegisterModuleDependences($this->MODULE_ID, 'OnAfterEmailSend', $this->MODULE_ID);

		//Сайты
		$arSiteId = array();
		$rsSite   = CSite::GetList($by = "sort", $order = "asc", Array("ACTIVE" => "Y"));
		while($arSite = $rsSite->fetch())
			$arSiteId[] = $arSite['LID'];

		$arEventTypeFields = array(
			 0 => array(
					'LID'         => 'ru',
					'EVENT_NAME'  => GetMessage('ET_EVENT_NAME'),
					'NAME'        => GetMessage('RU_ET_NAME'),
					'DESCRIPTION' => GetMessage('RU_ET_DESCRIPTION'),
			 ),
			 1 => array(
					'LID'         => 'en',
					'EVENT_NAME'  => GetMessage('ET_EVENT_NAME'),
					'NAME'        => GetMessage('RU_ET_NAME'),
					'DESCRIPTION' => GetMessage('RU_ET_DESCRIPTION'),
			 ),
		);

		$eventType = new CEventType;
		foreach($arEventTypeFields as $arField) {
			$rsET = $eventType->GetByID($arField['EVENT_NAME'], $arField['LID']);
			$arET = $rsET->Fetch();

			if(!$arET)
				$eventType->Add($arField);
			else
				$eventType->Update(array('ID' => $arET['ID']), $arField);
		}
		unset($rsET, $arET, $arField);

		$arEventMessFields = array(
			 0 => array(
					'ACTIVE'     => 'Y',
					'EVENT_NAME' => GetMessage('ET_EVENT_NAME'),
					'LID'        => $arSiteId,
					'EMAIL_FROM' => GetMessage('EM_EMAIL_FROM'),
					'EMAIL_TO'   => GetMessage('EM_EMAIL_TO'),
					'SUBJECT'    => GetMessage('EM_SUBJECT_ADMIN'),
					'BODY_TYPE'  => 'html',
					'MESSAGE'    => GetMessage('EM_MESSAGE'),
			 ),
			 1 => array(
					'ACTIVE'     => 'Y',
					'EVENT_NAME' => GetMessage('ET_EVENT_NAME'),
					'LID'        => $arSiteId,
					'EMAIL_FROM' => GetMessage('EM_EMAIL_FROM'),
					'EMAIL_TO'   => GetMessage('EM_EMAIL_TO'),
					'SUBJECT'    => GetMessage('EM_SUBJECT_USER'),
					'BODY_TYPE'  => 'html',
					'MESSAGE'    => GetMessage('EM_MESSAGE'),
			 ),
		);

		$eventM = new CEventMessage;
		foreach($arEventMessFields as $arField) {
			$eventM->Add($arField);
		}

		return true;
	}

	function DoUninstall()
	{
		UnRegisterModule($this->MODULE_ID);
		///$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/components/api/main.feedback/");
		DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID . '/');

		return true;
	}

	function UnInstallEvents()
	{
		global $DB;

		//UnRegisterModuleDependences($this->MODULE_ID, 'OnBeforeEmailSend', $this->MODULE_ID);
		//UnRegisterModuleDependences($this->MODULE_ID, 'OnAfterEmailSend', $this->MODULE_ID);

		$eventType = new CEventType;
		$eventType->Delete("API_FEEDBACK");

		$eventM = new CEventMessage;
		$rsMess = $eventM->GetList($by = 'id', $order = 'asc', array('TYPE_ID' => 'API_FEEDBACK'));
		while($arMess = $rsMess->Fetch()) {
			$DB->StartTransaction();
			if(!$eventM->Delete(intval($arMess['ID']))) {
				$DB->Rollback();
				//$strError.=GetMessage("DELETE_ERROR");
			}
			else
				$DB->Commit();
		}

		return true;
	}
}

?>