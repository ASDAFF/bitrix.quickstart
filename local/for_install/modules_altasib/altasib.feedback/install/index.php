<?
/**
 * Company developer: ALTASIB
 * Developer: adumnov
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * @copyright (c) 2006-2017 ALTASIB
 */

global $MESS;

$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
@include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));
IncludeModuleLangFile($strPath2Lang."/install/index.php");

Class altasib_feedback extends CModule
{
	var $MODULE_ID = "altasib.feedback";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function altasib_feedback()
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
		else
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}
		$this->MODULE_NAME = GetMessage("ALTASIB_FEEDBACK_REG_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ALTASIB_FEEDBACK_REG_MODULE_DESCRIPTION");
		$this->PARTNER_NAME = "ALTASIB";
		$this->PARTNER_URI = "http://www.altasib.ru/";
	}
	function DoInstall()
	{
		global $APPLICATION, $step;
		$step = IntVal($step);
		$this->InstallFiles();
		$this->InstallDB();
		$this->InstallIblock();
		$GLOBALS["errors"] = $this->errors;
		$APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_FEEDBACK_REG_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/step1.php");
	}
	function DoUninstall()
	{
		global $APPLICATION, $step;
		$step = IntVal($step);
		if($step<2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_FEEDBACK_REG_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/unstep1.php");
		}
		elseif($step==2)
		{
			$this->UnInstallDB();
			$this->UnInstallFiles();
			$this->UnInstallEvents();
			$APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_FEEDBACK_REG_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/unstep2.php");
		}
	}
	function InstallDB()
	{
		RegisterModule("altasib.feedback");
		RegisterModuleDependences("iblock", "OnAfterIBlockUpdate", "altasib.feedback", "AltasibFeedbackEvent", "OnAfterIBlockUpdateHandler", "100");
	}
	function UnInstallDB()
	{
		COption::RemoveOption("altasib.feedback");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockUpdate", "altasib.feedback", "AltasibFeedbackEvent", "OnAfterIBlockUpdateHandler");
		UnRegisterModule("altasib.feedback");
	}
	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/images",$_SERVER["DOCUMENT_ROOT"]."/bitrix/images",true,true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.feedback/install/jquery.maskedinput",$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/altasib.feedback/jquery.maskedinput",true,true);
		return true;
	}
	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/components/altasib/feedback.form");
		DeleteDirFilesEx("/bitrix/images/altasib.feedback");
		DeleteDirFilesEx("/bitrix/js/altasib.feedback");
		return true;
	}
	function InstallIblock()
	{
		if(!CModule::IncludeModule("iblock"))
			return;
		//add type iblock
		$res = CIBlockType::GetByID("alx_feedback");
		if(!$v = $res->GetNext())
		{
			$arFields = Array(
				'ID'=>'altasib_feedback',
				'SECTIONS'=>'Y',
				'IN_RSS'=>'N',
				'SORT'=>100,
				'LANG'=>Array(
					'ru'=>Array(
						'NAME'=>GetMessage("ALTASIB_IB_FEEDBACK")
					)
				)
			);
			$obBlocktype = new CIBlockType;
			$obBlocktype->Add($arFields);
		}
		//add iblock

		$rsSites = CSite::GetList($by="sort", $order="desc", Array());
		$i = 0;
		while ($arSite = $rsSites->Fetch())
		{
			$arSiteID[$i] = $arSite["ID"];
			$i++;
		}
		$res = CIBlock::GetList(
			Array(),
			Array(
				'TYPE'=>'altasib_feedback',
				'CODE'=>'altasib_feedback'
			),
			true
		);
		$check_ib = false;
		while($arRes = $res->Fetch())
			if($arRes) $check_ib = true;
		if(!$check_ib)
			for($i = 0; $i < count($arSiteID); $i++)
			{
				$ib = new CIBlock;
				$arFields = Array(
					"ACTIVE" => "Y",
					"NAME" => GetMessage("ALTASIB_IB_FEEDBACK"),
					"CODE" => "altasib_feedback",
					"IBLOCK_TYPE_ID" => "altasib_feedback",
					"INDEX_ELEMENT" => "N",
					"INDEX_SECTION" => "N",
					"WORKFLOW" => "N",
					"SITE_ID" => $arSiteID[$i]
				);
				$ib->Add($arFields);
			}
		//add props
		$res = CIBlock::GetList(Array(),Array("CODE"=>'altasib_feedback'),true);
		$arRes = $res->Fetch();
		$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arRes["ID"]));
		while ($arr=$rsProp->Fetch())
			$arPropsCode[] = $arr["CODE"];
		if(!is_array($arPropsCode)){
			$arPropsCode = array();
		}

		$ibp = new CIBlockProperty;

		if(!in_array("FIO", $arPropsCode))
		{
			$arFields = Array(
				"NAME" => GetMessage("ALTASIB_IB_FIO"),
				"ACTIVE" => "Y",
				"SORT" => "100",
				"CODE" => "FIO",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $arRes['ID']
			);
			$PropID = $ibp->Add($arFields);
		}
		if(!in_array("PHONE", $arPropsCode))
		{
			$arFields = Array(
				"NAME" => GetMessage("ALTASIB_IB_PHONE"),
				"ACTIVE" => "Y",
				"SORT" => "100",
				"CODE" => "PHONE",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $arRes['ID']
			);
			$PropID = $ibp->Add($arFields);
		}
		if(!in_array("EMAIL", $arPropsCode))
		{
			$arFields = Array(
				"NAME" => GetMessage("ALTASIB_IB_EMAIL"),
				"ACTIVE" => "Y",
				"SORT" => "110",
				"CODE" => "EMAIL",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $arRes['ID']
			);
			$PropID = $ibp->Add($arFields);
		}
		$arFields = Array(
			"NAME" => GetMessage("ALTASIB_IB_FILE"),
			"ACTIVE" => "Y",
			"SORT" => "120",
			"CODE" => "FILE",
			"PROPERTY_TYPE" => "F",
			"IBLOCK_ID" => $arRes['ID'],
			"FILE_TYPE" => "jpg, gif, bmp, png, jpeg, doc, docx, pdf, txt, rtf, zip, rar, 7z"
		);
		if(!in_array("FILE", $arPropsCode))
		{
			$PropID = $ibp->Add($arFields);
		}
		else
		{
			$resPropFile = CIBlockProperty::GetByID("FILE", $arRes['ID']);
			if($arPropFile = $resPropFile->GetNext())
			{
				$PropID = $ibp->Update($arPropFile["ID"], $arFields);
			}
		}
		if(!in_array("USERIP", $arPropsCode))
		{
			$arFields = Array(
				"NAME" => GetMessage("ALTASIB_IB_USERIP"),
				"ACTIVE" => "Y",
				"CODE" => "USERIP",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $arRes['ID'],
			);
			$PropID = $ibp->Add($arFields);
		}
		if(!in_array("USER_ID", $arPropsCode))
		{
			$arFields = Array(
				"NAME" => GetMessage("ALTASIB_IB_USR"),
				"ACTIVE" => "Y",
				"CODE" => "USER_ID",
				"PROPERTY_TYPE" => "S",
				"USER_TYPE" => "UserID",
				"IBLOCK_ID" => $arRes['ID'],
			);
			$PropID = $ibp->Add($arFields);
		}
		if(!in_array("HREF_LINK", $arPropsCode))
		{
			$arFields = Array(
				"NAME" => GetMessage("ALTASIB_IB_LINK"),
				"ACTIVE" => "Y",
				"CODE" => "HREF_LINK",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $arRes['ID'],
			);
			$PropID = $ibp->Add($arFields);
		}

		CIBlock::SetPermission($arRes['ID'], Array("1"=>"X", "2"=>"R"));
	}
	function UnInstallEvents()
	{
		global $DB;
		$DB->Query("DELETE FROM b_event_type WHERE EVENT_NAME in ('ALX_FEEDBACK_FORM')");
		$DB->Query("DELETE FROM b_event_message WHERE EVENT_NAME in ('ALX_FEEDBACK_FORM')");
		$DB->Query("DELETE FROM b_event_type WHERE EVENT_NAME in ('ALX_FEEDBACK_FORM_SEND_MAIL')");
		$DB->Query("DELETE FROM b_event_message WHERE EVENT_NAME in ('ALX_FEEDBACK_FORM_SEND_MAIL')");
	}
}
?>