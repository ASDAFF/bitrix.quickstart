<?php
IncludeModuleLangFile(__FILE__);

class CASDTplVars {

	public static function OnBeforeEndBufferContent() {
		if (isset($GLOBALS['APPLICATION']->arPanelButtons) &&
			is_array($GLOBALS['APPLICATION']->arPanelButtons) &&
			!empty($GLOBALS['APPLICATION']->arPanelButtons)
		) {
			if ($GLOBALS['USER']->CanDoOperation('lpa_template_edit')) {
				$arPanelButtons = &$GLOBALS['APPLICATION']->arPanelButtons;
				foreach ($arPanelButtons as &$arItemPanel) {
					if ($arItemPanel['ICON'] == 'bx-panel-site-template-icon') {
						if (isset($arItemPanel['MENU']) && is_array($arItemPanel['MENU'])) {
							$popupAction = "javascript:(new BX.CDialog({
											width: 550,
											height: 300,
											resizable: false,
											buttons: [BX.CDialog.btnSave, BX.CDialog.btnCancel],
											content_url: '/bitrix/tools/asd.tplvars/edit_vars.php?bxpublic=Y&site=".SITE_ID."'})).Show();";
							$arItemPanel['MENU'][] = array(
								'TEXT' => GetMessage('ASD_TPLVARS_PANEL_TITLE'),
								'ACTION' => $popupAction,
							);
						}
					}
				}
			}
		}
	}

	public static function GetOptionsDesc($code, $site=SITE_ID) {
		static $arOptions = array();
		if (empty($arOptions)) {
			$arOptions['asd_null'] = null;
			$site = $GLOBALS['DB']->ForSQL($site, 2);
			$rsOpt = $GLOBALS['DB']->Query("SELECT NAME, DESCRIPTION FROM b_option WHERE MODULE_ID='tpl_vars' AND SITE_ID='".$site."';");
			while ($arOpt = $rsOpt->GetNext()) {
				$arOptions[$arOpt['NAME']] = $arOpt['DESCRIPTION'];
			}
		}
		return $arOptions[$code];
	}
}

if (version_compare(SM_VERSION, '14.0.0')>=0) {
	class CASDOption extends \Bitrix\Main\Config\Option {
		public static function GetOptions() {
			return self::$options;
		}
	}
} else {
	class CASDOption {
		public static function GetOptions() {
			global $MAIN_OPTIONS;
			return $MAIN_OPTIONS;
		}
	}
}
