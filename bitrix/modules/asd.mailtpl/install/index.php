<?
global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
include($PathInstall.'/version.php');

if (class_exists('asd_mailtpl')) return;

class asd_mailtpl extends CModule {

	var $MODULE_ID = "asd.mailtpl";
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

		$this->PARTNER_NAME = GetMessage("ASD_MAILTPL_PARTNER_NAME");
		$this->PARTNER_URI = 'http://www.d-it.ru/solutions/modules/';

		$this->MODULE_NAME = GetMessage('ASD_MAILTPL_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ASD_MAILTPL_MODULE_DESCRIPTION');
	}

	public function InstallDB() {
		RegisterModuleDependences('main', 'OnBeforeEventSend', $this->MODULE_ID, 'CASDMailTpl', 'OnBeforeEventSend');
		RegisterModuleDependences('main', 'OnBeforeEventAdd', $this->MODULE_ID, 'CASDMailTpl', 'OnBeforeEventAdd');
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/', true, true);
		$GLOBALS['DB']->RunSQLBatch($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/db/'.$GLOBALS['DBType'].'/install.sql');
		RegisterModule($this->MODULE_ID);
	}

	public function UnInstallDB() {
		UnRegisterModuleDependences('main', 'OnBeforeEventSend', $this->MODULE_ID, 'CASDMailTpl', 'OnBeforeEventSend');
		UnRegisterModuleDependences('main', 'OnBeforeEventAdd', $this->MODULE_ID, 'CASDMailTpl', 'OnBeforeEventAdd');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/');
		if ($_REQUEST['savedata'] != 'Y') {
			$GLOBALS['DB']->RunSQLBatch($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/db/'.$GLOBALS['DBType'].'/uninstall.sql');
		}
		UnRegisterModule($this->MODULE_ID);
	}

	public function DoInstall() {
		global $APPLICATION, $adminPage, $USER, $adminMenu, $adminChain;

		if ($GLOBALS['APPLICATION']->GetGroupRight('main') < 'W') {
			return;
		}

		if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES)) {
			foreach ($this->NEED_MODULES as $module) {
				if (!IsModuleInstalled($module)) {
					$this->ShowForm('ERROR', GetMessage('ASD_MAILTPL_NEED_MODULES', array('#MODULE#' => $module)));
				}
			}
		}

		if (strlen($this->NEED_MAIN_VERSION)<=0 || version_compare(SM_VERSION, $this->NEED_MAIN_VERSION)>=0) {
			$this->InstallDB();
			$this->ShowForm('OK', GetMessage('MOD_INST_OK'));
		} else {
			$this->ShowForm('ERROR', GetMessage('ASD_MAILTPL_NEED_RIGHT_VER', array('#NEED#' => $this->NEED_MAIN_VERSION)));
		}
	}

	public function DoUninstall() {
		global $APPLICATION, $adminPage, $USER, $adminMenu, $adminChain;

		if ($GLOBALS['APPLICATION']->GetGroupRight('main') < 'W') {
			return;
		}

		if ($_REQUEST['step'] < 2) {
			$this->ShowDataSaveForm();
		} elseif ($_REQUEST['step'] == 2) {
			$this->UnInstallDB();
			$this->ShowForm('OK', GetMessage('MOD_UNINST_OK'));
		}
	}

	private function ShowForm($type, $message, $buttonName='') {
		global $APPLICATION, $adminPage, $USER, $adminMenu, $adminChain;

		$keys = array_keys($GLOBALS);
		for($i=0; $i<count($keys); $i++) {
			if($keys[$i]!='i' && $keys[$i]!='GLOBALS' && $keys[$i]!='strTitle' && $keys[$i]!='filepath') {
				global ${$keys[$i]};
			}
		}

		$PathInstall = str_replace('\\', '/', __FILE__);
		$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
		IncludeModuleLangFile($PathInstall.'/install.php');

		$GLOBALS['APPLICATION']->SetTitle(GetMessage('ASD_MAILTPL_MODULE_NAME'));
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
		echo CAdminMessage::ShowMessage(array('MESSAGE' => $message, 'TYPE' => $type));
		?>
		<form action="<?= $GLOBALS['APPLICATION']->GetCurPage()?>" method="get">
		<p>
			<input type="hidden" name="lang" value="<?= LANG?>" />
			<input type="submit" value="<?= strlen($buttonName) ? $buttonName : GetMessage('MOD_BACK')?>" />
		</p>
		</form>
		<?
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
		die();
	}

	private function ShowDataSaveForm() {
		global $APPLICATION, $adminPage, $USER, $adminMenu, $adminChain;

		$keys = array_keys($GLOBALS);
		for($i=0; $i<count($keys); $i++) {
			if($keys[$i]!='i' && $keys[$i]!='GLOBALS' && $keys[$i]!='strTitle' && $keys[$i]!='filepath') {
				global ${$keys[$i]};
			}
		}

		$PathInstall = str_replace('\\', '/', __FILE__);

		$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
		IncludeModuleLangFile($PathInstall.'/install.php');

		$GLOBALS['APPLICATION']->SetTitle(GetMessage('ASD_MAILTPL_MODULE_NAME'));
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
		?>
		<form action="<?= $GLOBALS['APPLICATION']->GetCurPage()?>" method="get">
			<?= bitrix_sessid_post()?>
			<input type="hidden" name="lang" value="<?= LANG?>" />
			<input type="hidden" name="id" value="<?= $this->MODULE_ID?>" />
			<input type="hidden" name="uninstall" value="Y" />
			<input type="hidden" name="step" value="2" />
			<?CAdminMessage::ShowMessage(GetMessage('MOD_UNINST_WARN'))?>
			<p><?echo GetMessage('MOD_UNINST_SAVE')?></p>
			<p>
				<input type="checkbox" name="savedata" id="savedata" value="Y" checked="checked" /><label for="savedata"><?echo GetMessage('MOD_UNINST_SAVE_TABLES')?></label><br />
			</p>
			<input type="submit" name="inst" value="<?echo GetMessage('MOD_UNINST_DEL')?>" />
		</form>
		<?
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
		die();
	}
}
?>