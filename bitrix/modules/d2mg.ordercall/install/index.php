<?

global $MESS;

$strPath2Lang = str_replace('\\', '/', __FILE__);

$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));

include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

include($strPath2Lang."/install/version.php");

require_once($strPath2Lang."/classes/general/d2mg_order_call.php");



Class d2mg_ordercall extends CModule
{
        var $MODULE_ID = "d2mg.ordercall";

        var $MODULE_VERSION;

        var $MODULE_VERSION_DATE;

        var $MODULE_NAME;

        var $MODULE_DESCRIPTION;


        function d2mg_ordercall()
        {        		

                $arModuleVersion = array();

				$path = str_replace("\\", "/", __FILE__);

				$path = substr($path, 0, strlen($path) - strlen("/index.php"));

				include($path."/version.php");

				$this->MODULE_VERSION = $arModuleVersion["VERSION"];

				$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

				$this->PARTNER_NAME = "Digital Dream Mediagroup";

				$this->PARTNER_URI = "http://d2mg.ru/";

				$this->MODULE_NAME = GetMessage("IX_GB_MODULE_NAME");

				$this->MODULE_DESCRIPTION = GetMessage("IX_GB_MODULE_DESC");

			return true;

        }		

        function DoInstall()
        {   
			// Создание типа почтового события
			COrderCall::EventTypeCreate();
			// Создание почтового шаблона
			COrderCall::EventMessageCreate();
			// Создание инфоблока
			COrderCall::IblockCreate();
            
			//Копирование файла, подключающего страницу административной части с настройками модуля
			$t = copy($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/d2mg.ordercall/admin/d2mg_ordercall_admin.php', $_SERVER["DOCUMENT_ROOT"].'/bitrix/admin/d2mg_ordercall_admin.php');			

			//Копирование компонентов
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/d2mg.ordercall/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/d2mg", true, true);			

            RegisterModule($this->MODULE_ID);
			
			LocalRedirect('/bitrix/admin/d2mg_ordercall_admin.php');
        }

        function DoUninstall()
        {          
			// Удаление файла, подключающего страницу административной части с настрйками модуля
			unlink($_SERVER["DOCUMENT_ROOT"].'/bitrix/admin/d2mg_ordercall_admin.php');
			
            UnRegisterModule($this->MODULE_ID);
        }
}

?>

