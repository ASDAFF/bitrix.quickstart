<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class defa_tools extends CModule
{
	var $MODULE_ID = "defa.tools";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function defa_tools()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("DEFATOOLS_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("DEFATOOLS_INSTALL_DESCRIPTION");
		
		$this->PARTNER_NAME = "DEFA Interaktiv";
		$this->PARTNER_URI = "http://idefa.ru";
	}

	function InstallDB($install_wizard = true)
	{
		RegisterModule($this->MODULE_ID);
		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		UnRegisterModule($this->MODULE_ID);
		return true;
	}

	
	function InstallFiles()
	{
		// JS
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.tools/install/scripts/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/defa.tools", true, true);

		// IBlock Property MultipleFiles
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.tools/install/scripts/uploadify", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/defatools/uploadify", true, true);

		// Typograf
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.tools/install/scripts/typograf/defa_tools_js.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/defa_tools_js.php", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.tools/install/scripts/typograf/typograf.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/defatools/typograf/typograf.php", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.tools/install/scripts/typograf/typograf.js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/htmleditor2/typograf.js", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.tools/install/scripts/typograf/typograf.gif", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/fileman/htmledit2/typograf.gif", true);

		// IBlock Property ElemCompleter
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.tools/install/scripts/elemcompliter/ajax_iblock_items_search.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/defatools/elemcompliter/ajax_iblock_items_search.php", true);
		
		// DefaTools_IB_Demo
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.tools/install/scripts/menu/images/defatools_menu_icon.gif", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/defatools/menu/images/defatools_menu_icon.gif", true);

		return true;
	}
	
	function UnInstallFiles()
	{
		// JS
		DeleteDirFilesEx("/bitrix/js/defa.tools");

		// IBlock Property MultipleFiles
		DeleteDirFilesEx("/bitrix/tools/defatools/uplodify");

		// Typograf
		DeleteDirFilesEx("/bitrix/admin/defa_tools_js.php");
		DeleteDirFilesEx("/bitrix/tools/defatools/typograf");
		DeleteDirFilesEx("/bitrix/admin/htmleditor2/typograf.js");
		DeleteDirFilesEx("/bitrix/images/fileman/htmledit2/typograf.gif");
		
		// IBlock Property ElemCompleter
		DeleteDirFilesEx("/bitrix/tools/defatools/elemcompliter");
		
		// DefaTools_IB_Demo
		DeleteDirFilesEx("/bitrix/tools/defatools/menu");

		DeleteDirFilesEx("/bitrix/tools/defatools");

		return true;
	}


	function DoInstall()
	{
        // IBlock Property MultipleFiles
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "DefaTools_IBProp_MultipleFiles", "GetUserTypeDescription");
		RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyAdd", $this->MODULE_ID, "DefaTools_IBProp_MultipleFiles", "OnAfterIBlockPropertyHandler");
		RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyUpdate", $this->MODULE_ID, "DefaTools_IBProp_MultipleFiles", "OnAfterIBlockPropertyHandler");
		RegisterModuleDependences('main', 'main.file.input.upload', $this->MODULE_ID, 'DefaTools_IBProp_MultipleFiles', 'OnMainFileInputUploadHandler');
		
		// IBlock Property FileManEx
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "DefaTools_IBProp_FileManEx", "GetUserTypeDescription");
		
		// UserType Property Auth
		RegisterModuleDependences("main", "OnUserTypeBuildList", $this->MODULE_ID, "DefaTools_UserType_Auth", "GetUserTypeDescription");
		
		// Typograf
		RegisterModuleDependences("fileman", "OnBeforeHTMLEditorScriptsGet", $this->MODULE_ID, "DefaTools_Typograf", "addEditorScriptsHandler" );
		RegisterModuleDependences("fileman", "OnIncludeHTMLEditorScript", $this->MODULE_ID, "DefaTools_Typograf", "OnIncludeHTMLEditorHandler" );

				
		// IBlock Property ElemListDescr
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "DefaTools_IBProp_ElemListDescr", "GetUserTypeDescription");
		
		// IBlock Property OptionsGrid
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "GetUserTypeDescription");
		RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyAdd", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "CheckProperty");
		RegisterModuleDependences("iblock", "OnAfterIBlockPropertyAdd", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "OnAfterPropertyAdd");
		RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyUpdate", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "CheckProperty");
		RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyUpdate", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "SetEnums");
		RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyDelete", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "DeleteEnums");

		// IBlock Property ElemCompleter
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "DefaTools_IBProp_ElemCompleter", "GetUserTypeDescription");

		// AdminServicesManager
		RegisterModuleDependences('main', 'OnAdminContextMenuShow', 'defa.tools', 'DefaToolsGetMenu', 'GetTopMenu');
		RegisterModuleDependences('main', 'OnAdminListDisplay', 'defa.tools', 'DefaToolsGetMenu', 'GetActionsMenu');
		RegisterModuleDependences('main', 'OnAdminContextMenuShow', 'defa.tools', 'DefaToolsController', 'OnAdminContextMenuShowHandler');

        // IBlock Update Handlers TODO: to change
        RegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", $this->MODULE_ID, "DefaToolsDemo", "CheckElementModifyPermissions");
        RegisterModuleDependences("iblock", "OnBeforeIBlockSectionUpdate", $this->MODULE_ID, "DefaToolsDemo", "CheckSectionModifyPermissions");
		
		$this->InstallDB(false);
		$this->InstallFiles();
	}

	function DoUninstall()
	{
		// IBlock Property MultipleFiles
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "DefaTools_IBProp_MultipleFiles", "GetUserTypeDescription");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyAdd", $this->MODULE_ID, "DefaTools_IBProp_MultipleFiles", "OnAfterIBlockPropertyHandler");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyUpdate", $this->MODULE_ID, "DefaTools_IBProp_MultipleFiles", "OnAfterIBlockPropertyHandler");
		UnRegisterModuleDependences('main', 'main.file.input.upload', $this->MODULE_ID, 'DefaTools_IBProp_MultipleFiles', 'OnMainFileInputUploadHandler');
		
		// IBlock Property FileManEx
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "DefaTools_IBProp_FileManEx", "GetUserTypeDescription");
		
		// UserType Property Auth
		UnRegisterModuleDependences("main", "OnUserTypeBuildList", $this->MODULE_ID, "DefaTools_UserType_Auth", "GetUserTypeDescription");
		
		// Typograf
		UnRegisterModuleDependences("fileman", "OnBeforeHTMLEditorScriptsGet", $this->MODULE_ID, "DefaTools_Typograf", "addEditorScriptsHandler" );
		UnRegisterModuleDependences("fileman", "OnIncludeHTMLEditorScript", $this->MODULE_ID, "DefaTools_Typograf", "OnIncludeHTMLEditorHandler" );


		// IBlock Property ElemListDescr
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "DefaTools_IBProp_ElemListDescr", "GetUserTypeDescription");
		
		// IBlock Property OptionsGrid
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "GetUserTypeDescription");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyAdd", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "CheckProperty");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockPropertyAdd", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "OnAfterPropertyAdd");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyUpdate", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "CheckProperty");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyUpdate", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "SetEnums");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyDelete", $this->MODULE_ID, "DefaTools_IBProp_OptionsGrid", "DeleteEnums");
		
		// IBlock Property ElemCompleter
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "DefaTools_IBProp_ElemCompleter", "GetUserTypeDescription");
		
		// AdminServicesManager
		UnRegisterModuleDependences('main', 'OnAdminContextMenuShow', 'defa.tools', 'DefaToolsGetMenu', 'GetTopMenu');
		UnRegisterModuleDependences('main', 'OnAdminListDisplay', 'defa.tools', 'DefaToolsGetMenu', 'GetActionsMenu');
		UnRegisterModuleDependences('main', 'OnAdminContextMenuShow', 'defa.tools', 'DefaToolsController', 'OnAdminContextMenuShowHandler');

        // IBlock Update Handlers
        UnRegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", $this->MODULE_ID, "DefaToolsDemo", "CheckElementModifyPermissions");
        UnRegisterModuleDependences("iblock", "OnBeforeIBlockSectionUpdate", $this->MODULE_ID, "DefaToolsDemo", "CheckSectionModifyPermissions");

		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
