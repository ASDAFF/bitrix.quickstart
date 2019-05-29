<?php

global $MESS;

$sPath2Lang = dirname(dirname(__FILE__));
include(GetLangFileName($sPath2Lang."/lang/", "/install/index.php"));
include($sPath2Lang."/install/version.php");

Class lssoft_comingsoon extends CModule { 

	var $MODULE_ID = "lssoft.comingsoon";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	
	public function __construct(){
        $arModuleVersion=array();
		include(dirname(__FILE__)."/version.php");
		/**
		 * Определяем основные параметры модуля
		 */
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("LS_CS_MODULE_NAME"); 
		$this->MODULE_DESCRIPTION = GetMessage("LS_CS_MODULE_DESC"); 
        $this->PARTNER_NAME = GetMessage("LS_CS_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("LS_CS_PARTNER_URI");		
	}
	/**
	 * Формируем список прав и операций
	 *
	 * @return array
	 */
	public function GetModuleTasks() {
		return array(
			'ls_cs_skeep_denied' => array(
				"LETTER" => "D",
				"BINDING" => "module",
				"OPERATIONS" => array()
			),
			'ls_cs_skeep_allow' => array(
				"LETTER" => "R",
				"BINDING" => "module",
				"OPERATIONS" => array(
					'ls_cs_skeep_allow'
				)
			),
		);
	}
	/**
	 * Запускает установку модуля
	 */
	public function DoInstall(){
		RegisterModule($this->MODULE_ID);
		RegisterModuleDependences("main","OnBeforeProlog",$this->MODULE_ID,"CLsCsMain","OnBeforeProlog","100");
		
		$this->InstallFiles();
		$this->InstallTasks();
		$this->InstallIBlock();
		$this->InstallEvent();
	}
	/**
	 * Запускает удаление модуля
	 */
	public function DoUninstall(){
		UnRegisterModule($this->MODULE_ID);
		UnRegisterModuleDependences("main","OnBeforeProlog",$this->MODULE_ID,"CLsCsMain","OnBeforeProlog");
		
		$this->UnInstallFiles();
		$this->UnInstallTasks();
		$this->UnInstallIBlock();
		$this->UnInstallEvent();
	}

	public function InstallFiles() {
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".$this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
		return true;
	}

	public function UnInstallFiles() {
		DeleteDirFilesEx("/bitrix/components/lssoft/cs.show/");
		DeleteDirFilesEx("/bitrix/images/".$this->MODULE_ID."/");
		DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID."/");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		return true;
	}

	public function InstallIBlock() {
		global $DB;
		if (!CModule::IncludeModule("iblock")) {
			return false;
		}
		
		$aFields = Array(
			'ID'=>'ls_cs_user',
			'SECTIONS'=>'N',
			'IN_RSS'=>'N',
			'SORT'=>100,
			'LANG'=>Array(
				'en'=>Array(
					'NAME'=>'Invites',
					'SECTION_NAME'=>'',
					'ELEMENT_NAME'=>''
				),
				'ru'=>Array(
					'NAME'=>GetMessage("LS_CS_IBLOCK_TYPE_NAME"),
					'SECTION_NAME'=>'',
					'ELEMENT_NAME'=>''
				)
			)
		);
		
		$oBlocktype = new CIBlockType;
		$DB->StartTransaction();
		if(!($sIdType=$oBlocktype->Add($aFields))) {
   			$DB->Rollback();
   			echo 'Error: '.$oBlocktype->LAST_ERROR.'<br>';
		} else {
   			$DB->Commit();
   			$aSiteItems=array();
   			$res=CSite::GetDefList();
   			while($aSite=$res->Fetch()) {
   				$aSiteItems[]=$aSite['LID'];
   			}
   			
   			$oIBlock = new CIBlock;
			$arFields = Array(
  				"ACTIVE" => 'Y',
  				'VERSION' => 2,
  				"NAME" => GetMessage("LS_CS_IBLOCK_NAME"),
  				"CODE" => 'ls_cs_user',
  				"LIST_PAGE_URL" => '',
  				"DETAIL_PAGE_URL" => '',
  				"IBLOCK_TYPE_ID" => $sIdType,
  				"SITE_ID" => $aSiteItems,
  				"SORT" => 500,
  				"DESCRIPTION" => '',
  				"GROUP_ID" => Array(2=>'W')
  			);
			if ($ID=$oIBlock->Add($arFields)) {
				$arFields = Array(
      				"NAME" => GetMessage("LS_CS_IBLOCK_PROPERTY_LOGIN"),
      				"ACTIVE" => "Y",
      				"IS_REQUIRED" => "N",
      				"FILTRABLE" => "Y",
      				"SORT" => "1000",
      				"CODE" => "LOGIN",
      				"PROPERTY_TYPE" => "S",
      				"IBLOCK_ID" => $ID,
      			);
   				$oIBlockProperty=new CIBlockProperty;
				$sIdPropLogin=$oIBlockProperty->Add($arFields);
   				
   				$arFields = Array(
      				"NAME" => GetMessage("LS_CS_IBLOCK_PROPERTY_SITE"),
      				"ACTIVE" => "Y",
      				"IS_REQUIRED" => "Y",
      				"FILTRABLE" => "Y",
      				"SORT" => "1000",
      				"CODE" => "SITE",
      				"PROPERTY_TYPE" => "S",
      				"IBLOCK_ID" => $ID,
      			);
   				$oIBlockProperty=new CIBlockProperty;
				$sIdPropSite=$oIBlockProperty->Add($arFields);
   				
   				$arFields = Array(
      				"NAME" => GetMessage("LS_CS_IBLOCK_PROPERTY_KEY"),
      				"ACTIVE" => "Y",
      				"IS_REQUIRED" => "Y",
      				"SORT" => "1000",
      				"CODE" => "KEY",
      				"PROPERTY_TYPE" => "S",
      				"IBLOCK_ID" => $ID,
      			);
   				$oIBlockProperty=new CIBlockProperty;
				$sIdPropKey=$oIBlockProperty->Add($arFields);
   				
   				$arFields = Array(
      				"NAME" => GetMessage("LS_CS_IBLOCK_PROPERTY_CONFIRM_MAIL"),
      				"ACTIVE" => "Y",
      				"IS_REQUIRED" => "Y",
      				"FILTRABLE" => "Y",
      				"SORT" => "1000",
      				"CODE" => "CONFIRM",
      				"PROPERTY_TYPE" => "N",
      				"DEFAULT_VALUE" => '0',
      				"IBLOCK_ID" => $ID,
      			);
   				$oIBlockProperty=new CIBlockProperty;
   				$sIdPropConfirm=$oIBlockProperty->Add($arFields);


				/**
				 * Настраиваем отображение формы списка и формы добавления
				 */
				$sColumns='NAME,PROPERTY_'.$sIdPropLogin.',PROPERTY_'.$sIdPropSite.',PROPERTY_'.$sIdPropConfirm.',TIMESTAMP_X';
				$sOptionHash="tbl_iblock_list_".md5($sIdType.".".$ID);
				$arOptions = array(
					 array(
						  'c' => 'list',
						  'n' => $sOptionHash,
						  'd' => 'Y',
						  'v' => array(
							   'columns' => $sColumns,
							   'by' => 'timestamp_x',
							   'order' => 'desc',
							   'page_size' => '20',
						  ),
					 )
				);
				CUserOptions::SetOptionsFromArray($arOptions);
				/**
				 * Форма добавления
				 */
				$aTabs = array(
					array(
						'CODE' => 'edit1',
						'TITLE' => 'Основное',
						'FIELDS' => array(
							array(
								'NAME' => 'NAME',
								'TITLE' => '*'.GetMessage("LS_CS_IBLOCK_MAIL"),
							),
							array(
								'NAME' => 'PROPERTY_'.$sIdPropLogin,
								'TITLE' => GetMessage("LS_CS_IBLOCK_PROPERTY_LOGIN"),
							),
							array(
								'NAME' => 'PROPERTY_'.$sIdPropSite,
								'TITLE' => '*'.GetMessage("LS_CS_IBLOCK_PROPERTY_SITE"),
							),
							array(
								'NAME' => 'PROPERTY_'.$sIdPropConfirm,
								'TITLE' => '*'.GetMessage("LS_CS_IBLOCK_PROPERTY_CONFIRM_MAIL"),
							),
							array(
								'NAME' => 'PROPERTY_'.$sIdPropKey,
								'TITLE' => '*'.GetMessage("LS_CS_IBLOCK_PROPERTY_KEY"),
							),
						),
					),
				);

				$sTabsString='';
				foreach($aTabs AS $aTabItem) {
					$sTabsString .= $aTabItem['CODE'] . '--#--' . $aTabItem['TITLE'] . '--,--';
					foreach($aTabItem['FIELDS'] AS $aFieldItem) {
						$sTabsString .= $aFieldItem['NAME'] . '--#--' . $aFieldItem['TITLE'] . '--,--';
					}
				}
				$arOptions = array(
					array(
						'c' => 'form',
						'n' => 'form_element_'.$ID,
						'd' => 'Y',
						'v' => array(
							'tabs' => $sTabsString,
						),
					)
				);
				CUserOptions::SetOptionsFromArray($arOptions);


				return true;
			}
   		}
   		return false;
	}

	public function UnInstallIBlock() {
		global $DB;
		if (!CModule::IncludeModule("iblock")) {
			return false;
		}
		
		$DB->StartTransaction();
		if(!CIBlockType::Delete('ls_cs_user')) {
    		$DB->Rollback();
    		echo 'Delete error!';
		}
		$DB->Commit();
		return true;
	}

	public function InstallEvent() {
		$oEventType=new CEventType;
		$aData=array(
			"LID"           => 'ru',
			"EVENT_NAME"    => 'LS_CS_REGISTRATION_CONFIRM',
			"NAME"          => GetMessage("LS_CS_IBLOCK_EVENT_CONFIRM_REGISTRATION"),
			"DESCRIPTION"   => ''
		);
		if ($iIdType=$oEventType->Add($aData)) {
			$aSiteItems=array();
   			$res=CSite::GetDefList();
   			while($aSite=$res->Fetch()) {
   				$aSiteItems[]=$aSite['LID'];
   			}
   			
			$aData=array(
				'ACTIVE'=>'Y',
				'EVENT_NAME'=>'LS_CS_REGISTRATION_CONFIRM',
				'LID'=>$aSiteItems,
				'EMAIL_FROM'=>'#DEFAULT_EMAIL_FROM#',
				'EMAIL_TO'=>'#EMAIL_TO#',
				'SUBJECT'=>GetMessage("LS_CS_IBLOCK_EVENT_CONFIRM_REGISTRATION_MSG_TITLE"),
				'BODY_TYPE'=>'html',
				'MESSAGE'=>GetMessage("LS_CS_IBLOCK_EVENT_CONFIRM_REGISTRATION_MSG_TEXT"),
			);
			$oEventMsg = new CEventMessage;
			$oEventMsg->Add($aData);
		}
		
		// Уведомление о приглашении
		$oEventType=new CEventType;
		$aData=array(
			"LID"           => 'ru',
			"EVENT_NAME"    => 'LS_CS_INVITE_SEND',
			"NAME"          => GetMessage("LS_CS_IBLOCK_EVENT_INVITE_SEND"),
			"DESCRIPTION"   => ''
		);
		if ($iIdType=$oEventType->Add($aData)) {
			$aSiteItems=array();
   			$res=CSite::GetDefList();
   			while($aSite=$res->Fetch()) {
   				$aSiteItems[]=$aSite['LID'];
   			}
   			
			$aData=array(
				'ACTIVE'=>'Y',
				'EVENT_NAME'=>'LS_CS_INVITE_SEND',
				'LID'=>$aSiteItems,
				'EMAIL_FROM'=>'#DEFAULT_EMAIL_FROM#',
				'EMAIL_TO'=>'#EMAIL_TO#',
				'SUBJECT'=>GetMessage("LS_CS_IBLOCK_EVENT_INVITE_SEND_MSG_TITLE"),
				'BODY_TYPE'=>'html',
				'MESSAGE'=>GetMessage("LS_CS_IBLOCK_EVENT_INVITE_SEND_MSG_TEXT"),
			);
			$oEventMsg = new CEventMessage;
			$oEventMsg->Add($aData);
		}
		
		
		// Уведомление о приглашении с регистрацией
		$oEventType=new CEventType;
		$aData=array(
			"LID"           => 'ru',
			"EVENT_NAME"    => 'LS_CS_INVITE_SEND_REGISTRATION',
			"NAME"          => GetMessage("LS_CS_IBLOCK_EVENT_INVITE_SEND_REGISTRATION"),
			"DESCRIPTION"   => ''
		);
		if ($iIdType=$oEventType->Add($aData)) {
			$aSiteItems=array();
   			$res=CSite::GetDefList();
   			while($aSite=$res->Fetch()) {
   				$aSiteItems[]=$aSite['LID'];
   			}
   			
			$aData=array(
				'ACTIVE'=>'Y',
				'EVENT_NAME'=>'LS_CS_INVITE_SEND_REGISTRATION',
				'LID'=>$aSiteItems,
				'EMAIL_FROM'=>'#DEFAULT_EMAIL_FROM#',
				'EMAIL_TO'=>'#EMAIL_TO#',
				'SUBJECT'=>GetMessage("LS_CS_IBLOCK_EVENT_INVITE_SEND_MSG_TITLE"),
				'BODY_TYPE'=>'html',
				'MESSAGE'=>GetMessage("LS_CS_IBLOCK_EVENT_INVITE_SEND_REGISTRATION_MSG_TEXT"),
			);
			$oEventMsg = new CEventMessage;
			$oEventMsg->Add($aData);
		}
	}

	public function UnInstallEvent() {
		global $DB;
		$oEventType=new CEventType;
		$oEventType->Delete("LS_CS_REGISTRATION_CONFIRM");
		
		$oEventType=new CEventType;
		$oEventType->Delete("LS_CS_INVITE_SEND");
		
		$oEventType=new CEventType;
		$oEventType->Delete("LS_CS_INVITE_SEND_REGISTRATION");
		
		$aFilter = array(
    		"TYPE_ID" => "LS_CS_REGISTRATION_CONFIRM | LS_CS_INVITE_SEND | LS_CS_INVITE_SEND_REGISTRATION",
    	);
    	$res=CEventMessage::GetList($by="id", $order="desc",$aFilter);
		while ($aMsgItem = $res->Fetch()) {
			$oMsg = new CEventMessage;
			$DB->StartTransaction();
			if(!$oMsg->Delete(intval($aMsgItem['ID']))) {		
				$DB->Rollback();
			} else {
    			$DB->Commit();
    		}
		}
	}
}