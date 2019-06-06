<?
global $MESS;
IncludeModuleLangFile(__DIR__ . "/../install.php");

if (class_exists("multiline_ml2webforms")) return;
Class multiline_ml2webforms extends CModule
{
	var $MODULE_ID = "multiline.ml2webforms";
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $errors = false;

	function multiline_ml2webforms()
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
			$this->MODULE_VERSION = ML2WEBFORMS_VERSION;
			$this->MODULE_VERSION_DATE = ML2WEBFORMS_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("ML2WEBFORMS_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ML2WEBFORMS_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("ML2WEBFORMS_INSTALL_PARTNER_NAME"); 
		$this->PARTNER_URI = GetMessage("ML2WEBFORMS_INSTALL_PARTNER_URI");

	}

	function DoInstall()
	{
		global $APPLICATION;
		$ver = explode(".", SM_VERSION);
		if ($ver[0] < 15) {
			$this->errors = array(GetMessage("ML2WEBFORMS_INSTALL_ERROR_BITRIX_VERSION"));
		} elseif (!defined("PHP_VERSION_ID") || PHP_VERSION_ID < 50300) {
			$this->errors = array(GetMessage("ML2WEBFORMS_INSTALL_ERROR_PHP_VERSION"));
		} else {
			$this->InstallFiles();
			$this->InstallDB();
		}
		$GLOBALS["errors"] = $this->errors;

		$APPLICATION->IncludeAdminFile(GetMessage("ML2WEBFORMS_INSTALL_TITLE"), __DIR__."/step1.php");
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;
		$step = IntVal($step);
		if($step<2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("ML2WEBFORMS_INSTALL_TITLE"), __DIR__."/unstep1.php");
		}
		elseif($step==2)
		{
			$this->UnInstallDB(array(
				"savedata" => $_REQUEST["savedata"],
			));
			$this->UnInstallFiles();

			$GLOBALS["errors"] = $this->errors;
			$APPLICATION->IncludeAdminFile(GetMessage("ML2WEBFORMS_INSTALL_TITLE"), __DIR__."/unstep2.php");
		}
	}
	
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;

		$this->errors = $DB->RunSQLBatch(__DIR__."/db/".strtolower($DB->type)."/install.sql");
		if (!empty($this->errors))
		{
			$APPLICATION->ThrowException(implode("", $this->errors));
			return false;
		} else {
			$this->errors = false;
		}
		$obEventType = new \CEventType();
		$obTemplate = new \CEventMessage();

		$rsSites = \CSite::GetList($sort = "sort", $by = "desc", $filter = array("ACTIVE" => "Y"));

		if ($arSite = $rsSites->Fetch()) {
			$lid = $arSite["LID"];

			$eventData = array(
				"ru" => array(
					"EVENT_NAME" => "ML2WEBFORMS_FEEDBACK_WEBFORM_FILL",
					"NAME" => "Multiline: Веб-формы. Заполнена форма \"Обратная связь\"",
					"LID" => "ru",
					"DESCRIPTION" => "#ID# - ID запроса\n#DATETIME# - время запроса\n#NAME# - Имя\n#EMAIL# - E-mail\n#PHONE# - Телефон\n#ATTACHMENT# - Вложение\n#STATUS# - Город\n#STATUS_EN# - Город [en]\n#COMMENT# - Комментарий\n#AGREE# - Я принимаю условия использования\n#HOBBY# - Хобби\n#HOBBY_EN# - Хобби [en]"
				),
				"en" => array(
					"EVENT_NAME" => "ML2WEBFORMS_FEEDBACK_WEBFORM_FILL",
					"NAME" => "Multiline: Web-forms. Form filled \"Feedback\"",
					"LID" => "en",
					"DESCRIPTION" => "#ID# - result ID\n#DATETIME# - result time\n#NAME# - Name\n#EMAIL# - E-mail\n#PHONE# - Phone\n#ATTACHMENT# - Attachment\n#STATUS# - City\n#STATUS_EN# - City [en]\n#COMMENT# - Comment\n#AGREE# - I agree terms of use\n#HOBBY# - Hobby\n#HOBBY_EN# - Hobby [en]"
				)
			);

			$obEventType->Add($eventData["ru"]);
			$obEventType->Add($eventData["en"]);
			$sth = $obTemplate->GetList($by = "ID", $order = "ASC", array("EVENT_NAME"=>"ML2WEBFORMS_FEEDBACK_WEBFORM_FILL"));
			$eventTemplateId = array();
			while ($row = $sth->Fetch()) {
				$eventTemplateId[] = $row["ID"];
			}
			if (!$eventTemplateId) {
				$eventTemplate = array(
					"ACTIVE" => "Y",
					"EVENT_NAME" => "ML2WEBFORMS_FEEDBACK_WEBFORM_FILL",
					"LID" => array($lid),
					"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
					"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
					"BCC" => "",
					"SUBJECT" => "#SITE_NAME#: Заполнена форма \"Обратная связь\"",
					"BODY_TYPE" => "text",
					"MESSAGE" => "ID запроса: #ID#\nВремя запроса: #DATETIME#\nИмя: #NAME#\nE-mail: #EMAIL#\nТелефон: #PHONE#\nВложение: #ATTACHMENT#\nГород: #STATUS#\nКомментарий: #COMMENT#\nЯ принимаю условия использования: #AGREE#\nХобби: #HOBBY#\n\n\nResult ID: #ID#\nResult time: #DATETIME#\nName: #NAME#\nE-mail: #EMAIL#\nPhone: #PHONE#\nAttachment: #ATTACHMENT#\nCity: #STATUS_EN#\nComment: #COMMENT#\nI agree terms of use: #AGREE#\nHobby: #HOBBY_EN#"
				);
				$eventTemplate["LID"] = $lid;
				$eventTemplateId = array($obTemplate->Add($eventTemplate));
			}
			$fname = __DIR__."/../lib/forms/feedback/class.php";
			if (is_array($eventTemplateId) && count($eventTemplateId) > 0 && $eventTemplateId[0] > 0 && file_exists($fname)) {
				$lines = explode("\n", file_get_contents($fname));
				if (strpos($lines[27], "return array") !== false) {
					$lines[27] = "        return array(".implode(",", $eventTemplateId).");";
				}
				file_put_contents($fname, implode("\n", $lines));
			}
		}

		RegisterModule($this->MODULE_ID);

		return true;
	}
	
	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$this->errors = false;

		if(!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y")
		{
			$errors = false;
			$folders = array(
				"/local/modules/multiline.ml2webforms",
				"/bitrix/modules/multiline.ml2webforms",
			);
			$moduleFolder = strpos(__DIR__, "local" . DIRECTORY_SEPARATOR . 'modules') !== false ? $folders[0] : $folders[1];
			$formsFolder = $moduleFolder."/lib/forms";
			if (file_exists($_SERVER["DOCUMENT_ROOT"].$formsFolder) && is_dir($_SERVER["DOCUMENT_ROOT"].$formsFolder)) {
				$obEventType = new \CEventType();
				$obTemplate = new \CEventMessage();
				if($handle = opendir($_SERVER["DOCUMENT_ROOT"].$formsFolder)) {
					while(($file = readdir($handle)) !== false) {
						if ($file == "." || $file == "..") {
							continue;
						}

						self::DeleteFormTables($file);
						DeleteDirFilesEx($formsFolder);
						$sth = $obTemplate->GetList($by = "ID", $order = "DESC", array("EVENT_NAME"=>"ML2WEBFORMS_".strtoupper($file)."_WEBFORM_FILL"));
						while ($row = $sth->Fetch()) {
							$obTemplate->Delete($row["ID"]);
						}
						$obEventType->Delete("ML2WEBFORMS_".strtoupper($file)."_WEBFORM_FILL");
					}
					closedir($handle);
				}
			}

			if (!empty($errors))
			{
				$APPLICATION->ThrowException(implode("", $errors));
				return false;
			}
		}

		UnRegisterModule($this->MODULE_ID);

		return true;
	}

	function DeleteFormTables($table) {
		global $DB;

		$table = "ml2webforms_$table";

		$sth = $DB->Query("show tables");
		while ($row = $sth->Fetch()) {
			list($base, $tableName) = each($row);
			if (substr($tableName, 0, strlen($table)) == $table) {
				$DB->Query("drop table ".$tableName);
			}
		}
	}


	function InstallFiles()
	{
		CopyDirFiles(__DIR__."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
		CopyDirFiles(__DIR__."/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles(__DIR__."/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);
		CopyDirFiles(__DIR__."/forms", __DIR__."/../lib/forms", false, true);
		mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/ml2webforms');
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/upload/ml2webforms/index.php', '');

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles(__DIR__."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFilesEx("/bitrix/components/multiline/ml2webforms.form.display/");
		DeleteDirFiles(__DIR__."/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools");

		return true;
	}
}
?>