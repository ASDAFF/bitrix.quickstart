<?php

global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
include($PathInstall.'/version.php');

if (class_exists('asd_iblock')) return;

class asd_iblock extends CModule {

	var $MODULE_ID = 'asd.iblock';
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'N';
	public $NEED_MAIN_VERSION = '';
	public $NEED_MODULES = array();

	public function __construct() {

		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_NAME = GetMessage("ASD_PARTNER_NAME");
		$this->PARTNER_URI = 'http://www.d-it.ru/solutions/modules/';

		$this->MODULE_NAME = GetMessage('ASD_IBLOCK_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ASD_IBLOCK_MODULE_DESCRIPTION');
	}

	public function DoInstall() {
		if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES)) {
			foreach ($this->NEED_MODULES as $module) {
				if (!IsModuleInstalled($module)) {
					$this->ShowForm('ERROR', GetMessage('ASD_NEED_MODULES', array('#MODULE#' => $module)));
				}
			}
		}

		if (strlen($this->NEED_MAIN_VERSION)<=0 || version_compare(SM_VERSION, $this->NEED_MAIN_VERSION)>=0) {
			RegisterModuleDependences('main', 'OnAdminListDisplay', 'asd.iblock', 'CASDiblockInterface', 'OnAdminListDisplayHandler');
			RegisterModuleDependences('main', 'OnBeforeProlog', 'asd.iblock', 'CASDiblockAction', 'OnBeforePrologHandler');
			RegisterModuleDependences('main', 'OnAdminContextMenuShow', 'asd.iblock', 'CASDiblockInterface', 'OnAdminContextMenuShowHandler');
			RegisterModuleDependences('main', 'OnAdminTabControlBegin', 'asd.iblock', 'CASDiblockInterface', 'OnAdminTabControlBeginHandler');
			RegisterModuleDependences('iblock', 'OnAfterIBlockUpdate', 'asd.iblock', 'CASDiblockAction', 'OnAfterIBlockUpdateHandler');
			RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'asd.iblock', 'CASDiblockPropCheckbox', 'GetUserTypeDescription');
			RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'asd.iblock', 'CASDiblockPropCheckboxNum', 'GetUserTypeDescription');
			RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'asd.iblock', 'CASDiblockPropPalette', 'GetUserTypeDescription');
			RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'asd.iblock', 'CASDiblockPropSection', 'GetUserTypeDescription');
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/asd.iblock/install/js/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/', true, true);
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/asd.iblock/install/panel/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/panel/', true, true);
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/asd.iblock/install/tools/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/', true, true);
			RegisterModule('asd.iblock');
			$this->ShowForm('OK', GetMessage('MOD_INST_OK'));
		} else {
			$this->ShowForm('ERROR', GetMessage('ASD_NEED_RIGHT_VER', array('#NEED#' => $this->NEED_MAIN_VERSION)));
		}
	}

	public function DoUninstall() {
		UnRegisterModuleDependences('main', 'OnAdminListDisplay', 'asd.iblock', 'CASDiblockInterface', 'OnAdminListDisplayHandler');
		UnRegisterModuleDependences('main', 'OnBeforeProlog', 'asd.iblock', 'CASDiblockAction', 'OnBeforePrologHandler');
		UnRegisterModuleDependences('main', 'OnAdminContextMenuShow', 'asd.iblock', 'CASDiblockInterface', 'OnAdminContextMenuShowHandler');
		UnRegisterModuleDependences('main', 'OnAdminTabControlBegin', 'asd.iblock', 'CASDiblockInterface', 'OnAdminTabControlBeginHandler');
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockUpdate', 'asd.iblock', 'CASDiblockAction', 'OnAfterIBlockUpdateHandler');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'asd.iblock', 'CASDiblockPropCheckbox', 'GetUserTypeDescription');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'asd.iblock', 'CASDiblockPropCheckboxNum', 'GetUserTypeDescription');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'asd.iblock', 'CASDiblockPropPalette', 'GetUserTypeDescription');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'asd.iblock', 'CASDiblockPropSection', 'GetUserTypeDescription');
		DeleteDirFilesEx('/bitrix/js/asd.iblock/');
		DeleteDirFilesEx('/bitrix/panel/asd.iblock/');
		DeleteDirFilesEx('/bitrix/tools/asd.iblock/');
		UnRegisterModule('asd.iblock');
		$this->ShowForm('OK', GetMessage('MOD_UNINST_OK'));
	}

	private function ShowForm($type, $message, $buttonName='') {
		$keys = array_keys($GLOBALS);
		for($i=0, $intCount = count($keys); $i < $intCount; $i++) {
			if($keys[$i]!='i' && $keys[$i]!='GLOBALS' && $keys[$i]!='strTitle' && $keys[$i]!='filepath') {
				global ${$keys[$i]};
			}
		}

		$PathInstall = str_replace('\\', '/', __FILE__);
		$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
		IncludeModuleLangFile($PathInstall.'/install.php');

		$APPLICATION->SetTitle(GetMessage('ASD_IBLOCK_MODULE_NAME'));
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
		echo CAdminMessage::ShowMessage(array('MESSAGE' => $message, 'TYPE' => $type));
		?>
		<form action="<?= $APPLICATION->GetCurPage()?>" method="get">
		<p>
			<input type="hidden" name="lang" value="<? echo LANGUAGE_ID; ?>" />
			<input type="submit" value="<?= strlen($buttonName) ? $buttonName : GetMessage('MOD_BACK')?>" />
		</p>
		</form>
		<?
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
		die();
	}
}