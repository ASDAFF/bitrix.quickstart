<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install.php"));

Class edost_delivery extends CModule {

	var $MODULE_ID = 'edost.delivery';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';
	var $NEED_MAIN_VERSION = '11.0.0';
	var $NEED_MODULES = array('main', 'sale');
	//var $PARTNER_NAME;
	//var $PARTNER_URI;

	function edost_delivery() {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_URI  = "http://www.edost.ru";
		$this->PARTNER_NAME = GetMessage('EDOST_DELIVERY_PARTNER_NAME');
		$this->MODULE_NAME = GetMessage('EDOST_DELIVERY_INSTALL_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('EDOST_DELIVERY_INSTALL_DESCRIPTION');

	}

	function DoInstall() {
		global $DOCUMENT_ROOT, $APPLICATION;

		if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES))
			foreach ($this->NEED_MODULES as $module)
				if (!IsModuleInstalled($module))
					$this->ShowForm('ERROR', GetMessage('EDOST_DELIVERY_NEED_MODULES', array('#MODULE#' => $module)));

		if (strlen($this->NEED_MAIN_VERSION) <= 0 || version_compare(SM_VERSION, $this->NEED_MAIN_VERSION) >= 0) {
			$this->InstallFiles();

			RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepOrderProps', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepOrderPropsHandler');
			RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepDelivery', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepDeliveryHandler');
			RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepPaySystem', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepPaySystemHandler');
			RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepComplete', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepComplete');
			RegisterModuleDependences('sale', 'OnBeforeOrderAdd', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCBeforeOrderAdd');
//			RegisterModuleDependences('sale', 'OnOrderAdd', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderAdd');
			RegisterModuleDependences('sale', 'OnOrderRemindSendEmail', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderRemindSendEmail');
//			RegisterModuleDependences('sale', 'OnSaleCalculateOrderDelivery', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCCalculateOrderDelivery');
			RegisterModuleDependences('sale', 'OnSaleCalculateOrderPaySystem', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCCalculateOrderPaySystem');

			RegisterModule($this->MODULE_ID);

			$this->ShowForm('OK', GetMessage('EDOST_DELIVERY_INSTALL_OK'));
		}
		else
			$this->ShowForm('ERROR', GetMessage('EDOST_DELIVERY_NEED_RIGHT_VER', array('#NEED#' => $this->NEED_MAIN_VERSION)));

	}

	function DoUninstall() {
		global $DOCUMENT_ROOT, $APPLICATION;

		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepOrderProps', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepOrderPropsHandler');
		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepDelivery', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepDeliveryHandler');
		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepPaySystem', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepPaySystemHandler');
		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepComplete', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderOneStepComplete');
		UnRegisterModuleDependences('sale', 'OnBeforeOrderAdd', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCBeforeOrderAdd');
//		UnRegisterModuleDependences('sale', 'OnOrderAdd', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderAdd');
		UnRegisterModuleDependences('sale', 'OnOrderRemindSendEmail', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCOrderRemindSendEmail');
//		UnRegisterModuleDependences('sale', 'OnSaleCalculateOrderDelivery', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCCalculateOrderDelivery');
		UnRegisterModuleDependences('sale', 'OnSaleCalculateOrderPaySystem', $this->MODULE_ID, 'CEdostModifySaleOrderAjax', 'OnSCCalculateOrderPaySystem');

		UnRegisterModule($this->MODULE_ID);

		$this->UnInstallFiles();
		$this->ShowForm('OK', GetMessage('EDOST_DELIVERY_INSTALL_DEL'));
	}

	function InstallFiles()	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/delivery_edost/delivery_edost.php', $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_delivery/delivery_edost.php', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/edostpaycod', $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment/edostpaycod', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/delivery_edost_img', $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/delivery_edost_img', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes', true, true);
		return true;
	}

	function UnInstallFiles() {
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/delivery_edost/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_delivery/');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/edostpaycod', $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment/edostpaycod');
		DeleteDirFilesEx('/bitrix/images/delivery_edost_img/');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes/.default', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/.default');
		DeleteDirFilesEx('/bitrix/themes/.default/icons/edost.delivery/');
		return true;
	}

	private function ShowForm($type, $message, $buttonName = '')
	{
		$keys = array_keys($GLOBALS);

		for ($i = 0; $i < count($keys); $i++)
			if ($keys[$i] != 'i' && $keys[$i] != 'GLOBALS' && $keys[$i] != 'strTitle' && $keys[$i] != 'filepath')
				global ${$keys[$i]};

//		$PathInstall = str_replace('\\', '/', __FILE__);
//		$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
//		IncludeModuleLangFile($PathInstall.'/install.php');

		$APPLICATION->SetTitle(GetMessage('EDOST_DELIVERY_INSTALL_NAME'));

		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

		echo CAdminMessage::ShowMessage(array('MESSAGE' => $message, 'TYPE' => $type));
		?>
		<form action="<?= $APPLICATION->GetCurPage()?>" method="get">
		<p>
			<input type="hidden" name="lang" value="<?= LANG?>" />
			<input type="submit" value="<?= strlen($buttonName) ? $buttonName : GetMessage('MOD_BACK')?>" />
		</p>
		</form>
		<?

		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

		die();
	}

}
?>