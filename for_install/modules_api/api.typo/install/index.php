<?
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\SiteTable;
use \Bitrix\Iblock\TypeTable;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Class api_typo extends CModule
{
	var $MODULE_ID           = 'api.typo';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . '/version.php');
		$this->MODULE_VERSION      = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME         = Loc::getMessage('API_TYPO_MODULE_NAME');
		$this->MODULE_DESCRIPTION  = Loc::getMessage('API_TYPO_MODULE_DESC');

		$this->PARTNER_NAME = Loc::getMessage('API_TYPO_PARTNER_NAME');
		$this->PARTNER_URI  = Loc::getMessage('API_TYPO_PARTNER_URI');
	}

	function InstallDB($arParams = array())
	{
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		return true;
	}

	function InstallEvents()
	{
		//Сайты
		$arSiteId = array();
		$rsSite   = SiteTable::getList(array(
			 'select' => array('LID'),
			 'filter' => array('ACTIVE' => 'Y'),
		));
		while($arSite = $rsSite->fetch())
			$arSiteId[] = $arSite['LID'];


		//Тип почтового события
		$obType       = new CEventType;
		$arEventTypes = array(
			 array(
					'LID'         => 'ru',
					'EVENT_NAME'  => Loc::getMessage('API_TYPO_ET_EVENT_NAME'),
					'NAME'        => Loc::getMessage('API_TYPO_RU_ET_NAME'),
					'DESCRIPTION' => Loc::getMessage('API_TYPO_RU_ET_DESCRIPTION'),
			 ),
			 array(
					'LID'         => 'en',
					'EVENT_NAME'  => Loc::getMessage('API_TYPO_ET_EVENT_NAME'),
					'NAME'        => Loc::getMessage('API_TYPO_RU_ET_NAME'),
					'DESCRIPTION' => Loc::getMessage('API_TYPO_RU_ET_DESCRIPTION'),
			 ),
		);
		foreach($arEventTypes as $arEventType)
		{
			$rsET = $obType->GetByID($arEventType['EVENT_NAME'], $arEventType['LID']);
			if(!$arET = $rsET->Fetch())
				$obType->Add($arEventType);
		}


		//Почтовые шаблоны
		$obEventMess     = new CEventMessage;
		$arEventMessages = array(
			 array(
					'ACTIVE'     => 'Y',
					'EVENT_NAME' => Loc::getMessage('API_TYPO_ET_EVENT_NAME'),
					'LID'        => $arSiteId,
					'EMAIL_FROM' => Loc::getMessage('API_TYPO_EM_EMAIL_FROM'),
					'EMAIL_TO'   => Loc::getMessage('API_TYPO_EM_EMAIL_TO'),
					'SUBJECT'    => Loc::getMessage('API_TYPO_EM_SUBJECT'),
					'BODY_TYPE'  => 'html',
					'MESSAGE'    => Loc::getMessage('API_TYPO_EM_MESSAGE'),
			 ),
		);

		$rsMess = $obEventMess->GetList($by = 'id', $order = 'asc', array(
			 'TYPE_ID' => Loc::getMessage('API_TYPO_ET_EVENT_NAME'),
		));

		if(!$arMess = $rsMess->Fetch())
		{
			foreach($arEventMessages as $key => $arEventMess)
			{
				$obEventMess->Add($arEventMess);
				//if($messId = $obEventMess->Add($arEventMess))
					//Option::set($this->MODULE_ID, 'post_' . $key . '_message_id', $messId);
			}
		}

		return true;
	}

	function UnInstallEvents()
	{
		global $DB;

		//Удалит почтовый шаблон
		$obEventMess = new CEventMessage;
		$rsMess      = $obEventMess->GetList($by = 'id', $order = 'asc', array(
			 'TYPE_ID' => Loc::getMessage('API_TYPO_ET_EVENT_NAME'),
		));
		while($arMess = $rsMess->Fetch())
		{
			$DB->StartTransaction();
			if(!$obEventMess->Delete($arMess['ID']))
				$DB->Rollback();
			else
				$DB->Commit();
		}


		//Удалит почтовый тип
		$et = new CEventType;
		$et->Delete(Loc::getMessage('API_TYPO_ET_EVENT_NAME'));

		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/components', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx('/bitrix/components/api/typo/');

		return true;
	}

	function DoInstall()
	{
		$this->InstallFiles();
		$this->InstallEvents();
		$this->InstallDB();

		ModuleManager::registerModule($this->MODULE_ID);
	}

	function DoUninstall()
	{
		ModuleManager::unRegisterModule($this->MODULE_ID);

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
	}
}

?>