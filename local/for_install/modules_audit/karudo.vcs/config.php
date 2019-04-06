<?php


class CVCSConfig {
	const MODULE_ID = 'karudo.vcs';

	const CACHE_DIR = 'karudo.vcs';

	const TBL_ITEMS = 'b_karudo_vcs_items';
	const TBL_SOURCES = 'b_karudo_vcs_sources';
	const TBL_REVISIONS = 'b_karudo_vcs_revisions';
	const TBL_CHANGED_ITEMS = 'b_karudo_vcs_changed_items';
	const TBL_DRIVERS = 'b_karudo_vcs_drivers';

	//changed items statuses
	const CIST_NEW = 'NEW';
	const CIST_UPD = 'UPD';
	const CIST_DEL = 'DEL';

	public static function SetOptionArray($name, $value) {
		 return COption::SetOptionString(self::MODULE_ID, $name, serialize($value));
	}

	public static function GetOptionArray($name, $def = array()) {
		return unserialize(COption::GetOptionString(self::MODULE_ID, $name, serialize($def)));
	}

	public static function SetStepExecutionTime($time) {
		$time = intval($time);
		if ($time < 0) {
			$time = 0;
		}
		return COption::SetOptionInt(self::MODULE_ID, 'step_execution_time', $time);
	}

	public static function GetStepExecutionTime() {
		return (int) COption::GetOptionInt(self::MODULE_ID, 'step_execution_time', 30);
	}

	public static function SetDriversInMenu($m) {
		return (int) COption::SetOptionInt(self::MODULE_ID, 'show_drivers_in_menu', intval($m));
	}

	public static function GetDriversInMenu() {
		return (int) COption::GetOptionInt(self::MODULE_ID, 'show_drivers_in_menu', 0);
	}

	public static function SetShowPanelButtons($m) {
		return (int) COption::SetOptionInt(self::MODULE_ID, 'show_panel_buttons', intval($m));
	}

	public static function GetShowPanelButtons() {
		return (int) COption::GetOptionInt(self::MODULE_ID, 'show_panel_buttons', 0);
	}

	public static function GetPublicAjaxPath() {
		return '/bitrix/tools/' . self::MODULE_ID . '/ajax_responser.php';
	}

	public static function GetPublicJSPath($file) {
		return '/bitrix/js/' . self::MODULE_ID . '/' . $file;
	}

	public static function SetLastCheckTime($time = false) {
		if (!$time) {
			$time = time();
		}
		return COption::SetOptionInt(self::MODULE_ID, 'last_check_time', $time);
	}

	public static function GetLastCheckTime() {
		return COption::GetOptionInt(self::MODULE_ID, 'last_check_time', 0);
	}


	public static function KarudoDevelMode() {
		return defined('KARUDO_DEVEL_MODE');
	}

	public static function GetJavascripts() {
		return array(
			'karudo-object',
			'jquery.min',
			'bootstrap',
			'noconflict',
			'main',
			'ui',
			'vcs',
			'init',
		);
	}
}