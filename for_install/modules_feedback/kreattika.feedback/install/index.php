<?
global $DOCUMENT_ROOT, $MESS;

IncludeModuleLangFile(__FILE__);

if (class_exists("kreattika_feedback")) return;

Class kreattika_feedback extends CModule
{
	var $MODULE_ID = "kreattika.feedback";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function kreattika_feedback()
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
		
		$this->PARTNER_NAME = "kreattika";
		$this->PARTNER_URI = "http://kreattika.ru";

		$this->MODULE_NAME = GetMessage("KREATTIKA_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("KREATTIKA_MODULE_DESCRIPTION");
	}

	function InstallEvents()
	{
	
		$arFilter = array(
			"TYPE_ID" => "KREATTIKA_FEEDBACK_FORM",
			);
		$rsET = CEventType::GetList($arFilter);
		if ($arET = $rsET->Fetch()):

		else:

			$DESCRIPTION = GetMessage("KREATTIKA_ETYPE_DESCRIPTION_TEXT");


			$et = new CEventType;
			$et->Add(array(
					"LID"           => "ru",
					"EVENT_NAME"    => "KREATTIKA_FEEDBACK_FORM",
					"NAME"          => GetMessage("KREATTIKA_ETYPE_NAME"),
					"DESCRIPTION"   => $DESCRIPTION
				));

		endif;

		$arFilter = Array(
			"TYPE_ID"       => "KREATTIKA_FEEDBACK_FORM",
			);
		$rsMess = CEventMessage::GetList($by="id", $order="desc", $arFilter);

		if ($arMess = $rsMess->Fetch()):
		else:

			$rsSites = CSite::GetList($by="def", $order="desc", Array());
			$arSite = $rsSites->Fetch();

			$arrMess["ACTIVE"] = "Y";
			$arrMess["EVENT_NAME"] = "KREATTIKA_FEEDBACK_FORM";
			$arrMess["LID"] = $arSite["ID"];
			$arrMess["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
			$arrMess["EMAIL_TO"] = "#EMAIL_TO#";
			$arrMess["SUBJECT"] = GetMessage("KREATTIKA_EMESS_SUBJECT");
			$arrMess["BODY_TYPE"] = "text";
			$arrMess["MESSAGE"] = GetMessage("KREATTIKA_EMESS_MESSAGE");

			$emess = new CEventMessage;
			$emess->Add($arrMess);

		endif;
	
	}
	
	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("kreattika.feedback"))
		{
			RegisterModule("kreattika.feedback");
			CopyDirFiles(
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.feedback/install/components/",
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/components",
				true, true
			);
			
			$this->InstallEvents();
			
		}
	}

	function DoUninstall()
	{
		UnRegisterModule("kreattika.feedback");
	}
}
?>