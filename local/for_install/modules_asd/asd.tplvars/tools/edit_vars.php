<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/asd.tplvars/include.php');
IncludeModuleLangFile(__FILE__);

if (!function_exists('htmlspecialcharsbx')) {
	function htmlspecialcharsbx($string, $flags=ENT_COMPAT) {
		return htmlspecialchars($string, $flags, (defined('BX_UTF')? 'UTF-8' : 'ISO-8859-1'));
	}
}

function __ShowError($mess) {
	global $APPLICATION, $adminPage, $USER, $adminMenu, $adminChain;
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	ShowError($mess);
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	die();
}

function __Alert($mess) {
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_js.php');
	?><script type="text/javascript">
		alert('<?= CUtil::JSEscape($mess)?>');
	</script><?
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin_js.php');
	die();
}

if (isset($_REQUEST['save']) && !defined('BX_UTF')) {
	$_REQUEST = $APPLICATION->ConvertCharsetArray($_REQUEST, 'UTF-8', SITE_CHARSET);
}

//global $MAIN_OPTIONS;
$MAIN_OPTIONS = CASDOption::GetOptions();

$singleCode = trim($_REQUEST['code']);
$singleCodeReal = trim($_REQUEST['realcode']);
$site = trim($_REQUEST['site']);
$save = isset($_REQUEST['save']);
$bOptExist = isset($MAIN_OPTIONS[$site]) && isset($MAIN_OPTIONS[$site]['tpl_vars']);
$asd_new_var = trim($_REQUEST['asd_new_var']);
$asd_new_val = trim($_REQUEST['asd_new_val']);
$asd_new_desc = trim($_REQUEST['asd_new_desc']);

if (!CModule::IncludeModule('asd.tplvars')) {
	__ShowError(GetMessage('ASD_TPLVARS_NOT_INCL'));
} elseif (!$GLOBALS['USER']->CanDoOperation('lpa_template_edit')) {
	__ShowError(GetMessage('ASD_TPLVARS_DENIED'));
} elseif ($save) {
	if (!check_bitrix_sessid()) {
		__Alert(GetMessage('ASD_TPLVARS_SESSID'));
	} else {
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_js.php');
		if ($bOptExist) {
			foreach ($MAIN_OPTIONS[$site]['tpl_vars'] as $code => $val) {
				$codeMd5 = md5($code);
				if (strlen($singleCode) && $codeMd5!=$singleCode) {
					continue;
				}
				if (strlen(trim($_REQUEST[$codeMd5.'_val']))) {
					COption::SetOptionString('tpl_vars', $code, trim($_REQUEST[$codeMd5.'_val']), trim($_REQUEST[$codeMd5.'_desc']), $site);
				} else {
					COption::RemoveOption('tpl_vars', $code, $site);
				}
			}
		}
		if (strlen($asd_new_val)) {
			COption::SetOptionString('tpl_vars', $asd_new_var, $asd_new_val, $asd_new_desc, $site);
		}
		?><script type="text/javascript">
			top.BX.closeWait(); top.BX.WindowManager.Get().AllowClose();
			top.BX.WindowManager.Get().Close();
		</script><?
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin_js.php');
		die();
	}
}

$APPLICATION->SetTitle(GetMessage('ASD_TPLVARS_TITLE'));
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
?>
<script type="text/javascript">
	function editArea(area) {
		var obEditArea = BX('edit_area_' + area);
		var obViewArea = BX('view_area_' + area);
		obEditArea.style.display = 'block';
		obViewArea.style.display = 'none';
		return obEditArea;
	}
	function viewArea(area) {
		var obEditArea = BX('edit_area_' + area);
		var obViewArea = BX('view_area_' + area);
		obEditArea.firstChild.value = BX.util.trim(obEditArea.firstChild.value);
		obViewArea.innerHTML = '';
		BX.adjust(obViewArea, {text:obEditArea.firstChild.value.length > 0 ? obEditArea.firstChild.value : jsMenuMess.noname});
		obEditArea.style.display = 'none';
		obViewArea.style.display = 'block';
		return obViewArea;
	}
	function rowMouseOut(obArea) {
		obArea.className = 'edit-field view-area';
		obArea.style.backgroundColor = '';
	}
</script>
<form method="post" action="/bitrix/tools/asd.tplvars/edit_vars.php">
	<?= bitrix_sessid_post();?>
	<input type="hidden" name="save" value="Y" />
	<input type="hidden" name="site" value="<?= htmlspecialchars($site)?>" />
	<input type="hidden" name="code" value="<?= htmlspecialchars($singleCode)?>" />
	<table width="100%" class="asd_tplvars">
		<tr>
			<td><b><?= GetMessage('ASD_TPLVARS_DESC')?></b></td>
			<td><b><?= GetMessage('ASD_TPLVARS_VAR')?></b></td>
			<td><b><?= GetMessage('ASD_TPLVARS_VAL')?></b></td>
		</tr>
		<?if ($bOptExist):?>
		<?
		$needCreate = true;
		foreach ($MAIN_OPTIONS[$site]['tpl_vars'] as $code => $val):
			$codeMd5 = md5($code);
			if (strlen($singleCode) && $codeMd5!=$singleCode) {
				continue;
			}
			$needCreate = false;
			?>
		<tr>
			<td>
				<div class="edit-area" id="edit_area_<?= $codeMd5?>_desc" style="display: none;"><input type="text" style="width: 120px;" name="<?= $codeMd5?>_desc" value="<?= $desc=CASDTplVars::GetOptionsDesc($code, $site)?>" onblur="viewArea('<?= $codeMd5?>_desc')" /></div>
				<div onmouseout="rowMouseOut(this)" class="edit-field view-area" id="view_area_<?= $codeMd5?>_desc" onclick="editArea('<?= $codeMd5?>_desc')" style="width: 150px;"><?= $desc?></div>
			</td>
			<td>
				<div style="margin: 0 0 0 10px;"><?= $code?></div>
			</td>
			<td>
				<input type="text" name="<?= $codeMd5?>_val" value="<?= htmlspecialcharsbx($val)?>" />
			</td>
		</tr>
		<?endforeach;?>
		<?endif;?>
		<?if (!strlen($singleCode) || $needCreate || !$bOptExist):?>
		<tr>
			<td>
				<input type="text" name="asd_new_desc" value="" />
			</td>
			<td>
				<input type="text" name="asd_new_var" value="<?= htmlspecialcharsbx($singleCodeReal)?>" />
			</td>
			<td>
				<input type="text" name="asd_new_val" value="" />
			</td>
		</tr>
		<?endif;?>
	</table>
</form>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');