<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use \Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

class pixelb_backcall extends CModule
{
	var $MODULE_ID = "pixelb.backcall";
    var $MODULE_NAME;

    public function __construct()
    {
        $arModuleVersion = array();

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'pixelb.backcall';
        $this->MODULE_NAME = Loc::getMessage('PIXELB_BACKCALL_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('PIXELB_BACKCALL_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('PIXELB_BACKCALL_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'http://pixelb.ru';
    }

    public function DoInstall()
    {
		global $DOCUMENT_ROOT, $APPLICATION;
        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallDB();
        $this->installFiles();
        $this->InstallMsg();
		$APPLICATION->IncludeAdminFile(Loc::getMessage('PIXELB_BACKCALL_MODULE_INSTALL') . $this->MODULE_ID, $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/step.php");
    }

    public function DoUninstall()
    {
		global $DOCUMENT_ROOT, $APPLICATION;
        $this->uninstallFiles();
        $this->UnInstallDB();
        $this->UnInstallMsg();
        ModuleManager::unRegisterModule($this->MODULE_ID);
		$APPLICATION->IncludeAdminFile(Loc::getMessage('PIXELB_BACKCALL_MODULE_UNINSTALL') . $this->MODULE_ID, $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/unstep.php");
    }

    public function installFiles ()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pixelb.backcall/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);

		return true;
	}

    public function uninstallFiles ()
	{
		DeleteDirFilesEx("/bitrix/components/pixelb/backcall");

		if(count(glob('/bitrix/components/pixelb/*', GLOB_ONLYDIR)) === 0) {
			DeleteDirFilesEx("/bitrix/components/pixelb");
		}

		return true;
	}

	public function InstallDB()
	{
		return true;
	}

	public function UnInstallDB()
	{
		return true;
	}

	public function InstallMsg()
	{

		$arFields = array(
			"LID"           => 'ru',
			"EVENT_NAME"    => "PB_BACKCALL_FORM_EVENT",
			"NAME"          => Loc::getMessage('PIXELB_BACKCALL_MODULE_INSTALL_EVENT_NAME'),
			"DESCRIPTION"   => Loc::getMessage('PIXELB_BACKCALL_MODULE_INSTALL_EVENT_DESCR')
		);

		$nEventId = CEventType::Add($arFields);

		if($nEventId){

			$rsSites = CSite::GetList($by = 'sort',$sort = 'ASC',$filter = array('ACTIVE' => 'Y'));

			$arSites = array();
			if($arSite = $rsSites->Fetch()){
				$arSites[] = $arSite['ID'];
			}

			if(count($arSites)){
				$arFields = array(
					'ACTIVE' => 'Y',
					'EVENT_NAME' => 'PB_BACKCALL_FORM_EVENT',
					'LID' => $arSites,
					'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
					'EMAIL_TO' => '#EMAIL_TO#',
					'SUBJECT' => Loc::getMessage('PIXELB_BACKCALL_MODULE_INSTALL_EVENT_SUBJECT'),
					'BODY_TYPE' => 'text',
					'MESSAGE' => '#TEXT#',
				);


				$oMess = new CEventMessage;
				$nMsgId = $oMess->Add($arFields);

				COption::SetOptionInt($this->MODULE_ID,'PIXELB_BACKCALL_MODULE_INSTALL_MSG_ID',$nMsgId,false,false);

			}
		}

		return true;
	}

	public function UnInstallMsg()
	{
		$oEvent = new CEventType;
		$oEvent->Delete("PB_BACKCALL_FORM_EVENT");

		$arFilter = Array(
			"TYPE_ID"       => "PB_BACKCALL_FORM_EVENT",
		);

		$rsMess = CEventMessage::GetList($by="id", $order="desc", $arFilter);

		while($arMess = $rsMess->GetNext()){
			$oMess = new CEventMessage;
			$oMess->Delete($arMess['ID']);
		}

		return true;
	}
}
