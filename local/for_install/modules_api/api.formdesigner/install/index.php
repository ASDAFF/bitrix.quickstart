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

class api_formdesigner extends CModule
{
	var $MODULE_ID = 'api.formdesigner';
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
		$this->MODULE_NAME         = GetMessage("AFD_INSTALL_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("AFD_INSTALL_MODULE_DESC");
		$this->PARTNER_NAME        = GetMessage("AFD_INSTALL_PARTNER_NAME");
		$this->PARTNER_URI         = GetMessage("AFD_INSTALL_PARTNER_URI");
	}

	function checkDependency()
	{
		$bMainValid       = (defined('SM_VERSION') && version_compare(SM_VERSION, '15.00.00','>='));
		$bIblockInstalled = ModuleManager::isModuleInstalled('iblock');
		$bIblockActive    = Loader::includeModule('iblock');

		return (bool)($bMainValid && $bIblockInstalled && $bIblockActive);
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$eventManager = EventManager::getInstance();
		$eventManager->registerEventHandler('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Api\FormDesigner\Property\ESList', 'getUserTypeDescription');
		$eventManager->registerEventHandler('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Api\FormDesigner\Property\PSList', 'getUserTypeDescription');

		$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/install.sql');
		if(!empty($errors))
		{
			$APPLICATION->ThrowException(implode("", $errors));
			return false;
		}

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$eventManager = EventManager::getInstance();
		$eventManager->unRegisterEventHandler('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Api\FormDesigner\Property\ESList', 'getUserTypeDescription');
		$eventManager->unRegisterEventHandler('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Api\FormDesigner\Property\PSList', 'getUserTypeDescription');

		$DB->Query("DELETE FROM `b_option` WHERE `MODULE_ID` = '" . $this->MODULE_ID . "'", true);
		//$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/uninstall.sql');

		$errors = null;
		if(array_key_exists("savedata", $arParams) && $arParams["savedata"] != "Y")
		{
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/uninstall.sql');
			if(!empty($errors))
			{
				$APPLICATION->ThrowException(implode("", $errors));
				return false;
			}
		}


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
				'EVENT_NAME'  => Loc::getMessage('AFD_INSTALL_ET_EVENT_NAME'),
				'NAME'        => Loc::getMessage('AFD_INSTALL_RU_ET_NAME'),
				'DESCRIPTION' => Loc::getMessage('AFD_INSTALL_RU_ET_DESCRIPTION'),
			),
			array(
				'LID'         => 'en',
				'EVENT_NAME'  => Loc::getMessage('AFD_INSTALL_ET_EVENT_NAME'),
				'NAME'        => Loc::getMessage('AFD_INSTALL_RU_ET_NAME'),
				'DESCRIPTION' => Loc::getMessage('AFD_INSTALL_RU_ET_DESCRIPTION'),
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
			'admin' => array(
				'ACTIVE'     => 'Y',
				'EVENT_NAME' => Loc::getMessage('AFD_INSTALL_ET_EVENT_NAME'),
				'LID'        => $arSiteId,
				'EMAIL_FROM' => Loc::getMessage('AFD_INSTALL_EM_EMAIL_FROM'),
				'EMAIL_TO'   => Loc::getMessage('AFD_INSTALL_EM_EMAIL_TO'),
				'SUBJECT'    => Loc::getMessage('AFD_INSTALL_EM_SUBJECT'),
				'BODY_TYPE'  => 'html',
				'MESSAGE'    => Loc::getMessage('AFD_INSTALL_EM_MESSAGE'),
			),
			'user'  => array(
				'ACTIVE'     => 'Y',
				'EVENT_NAME' => Loc::getMessage('AFD_INSTALL_ET_EVENT_NAME'),
				'LID'        => $arSiteId,
				'EMAIL_FROM' => Loc::getMessage('AFD_INSTALL_EM_EMAIL_FROM'),
				'EMAIL_TO'   => Loc::getMessage('AFD_INSTALL_EM_EMAIL_TO'),
				'SUBJECT'    => Loc::getMessage('AFD_INSTALL_EM_SUBJECT'),
				'BODY_TYPE'  => 'html',
				'MESSAGE'    => Loc::getMessage('AFD_INSTALL_EM_MESSAGE'),
			),
		);

		$rsMess = $obEventMess->GetList($by = 'id', $order = 'asc', array(
			'TYPE_ID' => Loc::getMessage('AFD_INSTALL_ET_EVENT_NAME'),
		));

		if(!$arMess = $rsMess->Fetch())
		{
			foreach($arEventMessages as $key => $arEventMess)
			{
				if($messId = $obEventMess->Add($arEventMess))
					Option::set($this->MODULE_ID, 'post_' . $key . '_message_id', $messId);
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
			'TYPE_ID' => Loc::getMessage('AFD_INSTALL_ET_EVENT_NAME'),
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
		$et->Delete(Loc::getMessage('AFD_INSTALL_ET_EVENT_NAME'));

		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
		//CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/" . $this->MODULE_ID, true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');
		DeleteDirFilesEx('/bitrix/components/api/formdesigner/');
		//DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID . '/');

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $step;

		$step = intval($step);

		if(!$this->checkDependency())
		{
			CEventLog::Log("ERROR","AFD_INSTALL_CHECK_DEPENDENCY",$this->MODULE_ID,'DoInstall()',Loc::getMessage('AFD_LOG_CHECK_DEPENDENCY'));

			$APPLICATION->IncludeAdminFile('', $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/error_dependency.php");
			return false;
		}

		if($APPLICATION->GetGroupRight('main') < 'W')
		{
			CEventLog::Log("ERROR","AFD_INSTALL_RIGHTS",$this->MODULE_ID,'DoInstall()',Loc::getMessage('AFD_LOG_RIGHTS'));
			$APPLICATION->ThrowException($this->MODULE_ID . ' DoInstall() rights error');
			return false;
		}

		if($step < 2)
			$APPLICATION->IncludeAdminFile('', $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/step1.php");
		elseif($step == 2)
		{
			$this->InstallDB();
			$this->InstallFiles();
			$this->InstallEvents();
			$this->InstallCore();
			$this->InstallPublic();
			$this->InstallIblock();

			ModuleManager::registerModule($this->MODULE_ID);
			Loader::includeModule($this->MODULE_ID);

			$APPLICATION->IncludeAdminFile('', $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/step2.php");
		}

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		if($APPLICATION->GetGroupRight('main') < 'W')
		{
			CEventLog::Log("ERROR","AFD_INSTALL_RIGHTS",$this->MODULE_ID,'DoUninstall()',Loc::getMessage('AFD_LOG_RIGHTS'));
			$APPLICATION->ThrowException($this->MODULE_ID . ' DoUninstall() rights error');
			return false;
		}

		$step = intval($step);
		if($step < 2)
			$APPLICATION->IncludeAdminFile('', $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/unstep1.php");
		else
		{
			$arParams = array(
				 "savedata" => $_REQUEST["savedata"],
			);

			$this->UnInstallDB($arParams);
			$this->UnInstallFiles();
			$this->UnInstallEvents();
			$this->UnInstallPublic();
			//$this->UnInstallIblock();

			ModuleManager::unRegisterModule($this->MODULE_ID);
		}

		return true;
	}

	function InstallPublic()
	{
		$path_from = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/public';
		$path_to   = $_SERVER['DOCUMENT_ROOT'];

		CopyDirFiles($path_from, $path_to, true, true);
	}

	function UnInstallPublic()
	{
		DeleteDirFilesEx('/' . $this->MODULE_ID . '/');
	}

	/*function UnInstallIblock()
	{
		global $DB;

		if(Loader::includeModule('iblock'))
		{
			$iblockType = Loc::getMessage('AFD_INSTALL_IBLOCK_TYPE_ID');
			$rsType = CIBlockType::GetByID($iblockType);
			if($rsType->Fetch())
			{
				$DB->StartTransaction();
				if(!CIBlockType::Delete($iblockType)){
					$DB->Rollback();
				}
				$DB->Commit();
			}
		}
	}*/

	function InstallIblock()
	{
		global $DB;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		$arErrors    = array();
		$installDemo = $request->get('INSTALL_DEMO') == 'Y';
		$formType    = $request->get('FORM_TYPE');
		$iblockType  = $request->get('IBLOCK_TYPE');

		if($installDemo)
		{
			Loader::includeModule('iblock');

			if(!$iblockType)
				$iblockType = Loc::getMessage('AFD_INSTALL_IBLOCK_TYPE_ID');

			if(!$formType)
				$formType = 'simple';


			$iblockName = Loc::getMessage('AFD_INSTALL_IBLOCK_NAME_'. $formType);


			//---------- Get all sites ----------//
			$arSiteId = array();
			$rsSite   = SiteTable::getList(array(
				'select' => array('LID'),
				'filter' => array('ACTIVE' => 'Y'),
			));
			while($arSite = $rsSite->fetch())
				$arSiteId[] = $arSite['LID'];


			//---------- Install iblock type ----------//
			$rsType = TypeTable::getList(array(
				'select' => array('ID'),
				'filter' => array('=ID' => $iblockType),
			));

			//if($rsType->fetch())
			//TypeTable::delete($iblockType);

			if(!$rsType->fetch())
			{
				$arTypeFields = Array(
					'ID'       => $iblockType,
					'SECTIONS' => 'Y',
					'IN_RSS'   => 'N',
					'SORT'     => 500,
					'LANG'     => Loc::getMessage('AFD_INSTALL_IBLOCK_TYPE_LANG'),
				);

				$obType = new CIBlockType;
				$DB->StartTransaction();
				$res = $obType->Add($arTypeFields);
				if(!$res)
				{
					$DB->Rollback();
					$arErrors[] = $obType->LAST_ERROR;
				}
				else
					$DB->Commit();
			}


			//---------- Install iblock ----------//
			if(!$arErrors)
			{
				$obIblock       = new CIBlock;
				$arIblockFields = Array(
					'ACTIVE'           => 'Y',
					'NAME'             => $iblockName,
					'CODE'             => '',
					'IBLOCK_TYPE_ID'   => $iblockType,
					'SITE_ID'          => $arSiteId,
					'SORT'             => 500,
					'LIST_PAGE_URL'    => '',
					'SECTION_PAGE_URL' => '',
					'DETAIL_PAGE_URL'  => '',
					'DESCRIPTION_TYPE' => 'html',
					'RSS_ACTIVE'       => 'N',
					'INDEX_ELEMENT'    => 'N',
					'INDEX_SECTION'    => 'N',
					'WORKFLOW'         => 'N',
					'VERSION'          => 1,
					'GROUP_ID'         => Array(
						'1' => 'X',
						'2' => 'D',
						'3' => 'D',
						'4' => 'D',
						'5' => 'D',
					),
					'FIELDS'           => array(
						'ACTIVE_FROM'  => array('DEFAULT_VALUE' => '=now'),
						'CODE'         => array(
							'DEFAULT_VALUE' => array(
								'UNIQUE'          => 'N',
								'TRANSLITERATION' => 'N',
							),
						),
						'SECTION_CODE' => array(
							'DEFAULT_VALUE' => array(
								'UNIQUE'          => 'N',
								'TRANSLITERATION' => 'N',
							),
						),
					),
				);

				if($iblockId = $obIblock->Add($arIblockFields))
				{
					$obProp            = new CIBlockProperty;
					$arProps['simple'] = Loc::getMessage('AFD_INSTALL_IBLOCK_PROPS_'. $formType);


					$arPropCodes = array();
					foreach($arProps[ $formType ] as $arProp)
					{
						static $sort;
						$sort += 10;

						$arPropCodes[] = $arProp['CODE'];

						$arDopFields = array(
							'IBLOCK_ID'     => $iblockId,
							'SORT'          => $sort,
							'ACTIVE'        => 'Y',
							'IS_REQUIRED'   => 'Y',
							'PROPERTY_TYPE' => 'S',
						);

						if(!$obProp->Add(array_merge($arDopFields, $arProp)))
							$arErrors[] = $obProp->LAST_ERROR;
					}

					Option::set('api.formdesigner','iblock_type',$iblockType);
					Option::set('api.formdesigner','iblock_id',$iblockId);
					Option::set('api.formdesigner','form_title',$iblockName);
				}
				else
					$arErrors[] = $obIblock->LAST_ERROR;
			}
		}

		if($arErrors)
			CEventLog::Log("ERROR","AFD_INSTALL_IBLOCK",$this->MODULE_ID,'InstallIblock()',join("\n",$arErrors));
	}

	function InstallCore(){
		require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/update_client_partner.php");

		$coreModule = 'api.core';
		if(!IsModuleInstalled($coreModule)) {
			$strError = '';
			if(!CUpdateClientPartner::LoadModuleNoDemand($coreModule, $strError, 'N', LANGUAGE_ID)) {
				CUpdateClientPartner::AddMessage2Log("exec CUpdateClientPartner::LoadModuleNoDemand api.core error");
			}
			else{
				if($oModule = CModule::CreateModuleObject($coreModule)) {
					if(!$oModule->IsInstalled()) {
						$oModule->DoInstall();
					}
				}
			}
		}

		if(IsModuleInstalled($coreModule)) {
			do {
				$result = CUpdateClientPartner::loadModule4Wizard($coreModule);
			}
			while($result == 'STP');
		}
	}

}
?>