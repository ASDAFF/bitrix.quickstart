<?php
if (!function_exists('tplvar')) {

	function tplvar($code, $bShowIcon=false, $bNotVal=false) {
		static $MAIN_OPTIONS = array();
		$code = trim($code);
		if (!isset($MAIN_OPTIONS[SITE_ID]) || !isset($MAIN_OPTIONS[SITE_ID]['tpl_vars'])) {
			// get from DB to cache
			if (CModule::IncludeModule('asd.tplvars')) {
				$MAIN_OPTIONS = CASDOption::GetOptions();
			}
		}
		$val = $MAIN_OPTIONS[SITE_ID]['tpl_vars'][$code];
		if ($bNotVal) {
			$val = '';
		}
		if ($bShowIcon && $GLOBALS['APPLICATION']->GetPublicShowMode()!='view' && $GLOBALS['USER']->CanDoOperation('lpa_template_edit')) {
			$popupAction = "javascript:(new BX.CDialog({
							width: 550,
							height: 300,
							resizable: false,
							buttons: [BX.CDialog.btnSave, BX.CDialog.btnCancel],
							content_url: '/bitrix/tools/asd.tplvars/edit_vars.php?bxpublic=Y&site=".SITE_ID."&code=".md5($code)."&realcode=".urlencode($code)."'})).Show();";
			if (version_compare(SM_VERSION, '12.0.0')>=0) {
				$val .= ' <a href="javascript:void(0);" onclick="'.$popupAction.'"><img src="/bitrix/panel/main/images_old/panel/pencil.gif" alt="" /></a>';
			} else {
				$val .= ' <a href="javascript:void(0);" onclick="'.$popupAction.'"><img src="/bitrix/themes/.default/public/popup/pencil.gif" alt="" /></a>';
			}
		}
		return $val;
	}

	function tplinvis($code) {
		if ($GLOBALS['APPLICATION']->GetPublicShowMode()!='view') {
			return tplvar($code, true, true);
		}
	}

	function tplvar_set($code, $value, $site) {
		COption::SetOptionString('tpl_vars', $code, $value, false, $site);
	}
}