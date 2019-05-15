<?php
define("ADMIN_MODULE_NAME", "karudo.vcs");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule('karudo.vcs');

$LIST_URL = '/bitrix/admin/karudo.drivers_list.php';

IncludeModuleLangFile(__FILE__);

if(!$USER->IsAdmin()) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$ID = isset($_REQUEST['ID']) ? intval($_REQUEST['ID']) : 0;

$message = false;

$save = empty($_REQUEST['save']) ? '' : $_REQUEST['save'];
$apply = empty($_REQUEST['apply']) ? '' : $_REQUEST['apply'];


if($_SERVER['REQUEST_METHOD'] == 'POST' && strlen($save.$apply)>0 && check_bitrix_sessid()) {
	$bOK = false;
	$new = false;
	$arDriver = $_REQUEST['DRV'];
	unset($arDriver['ID']);
	$doc_root = CVCSMain::_GetDriverDocRoot($arDriver);
	if (
		empty($arDriver['SETTINGS']['doc_root']) ||
		!file_exists($doc_root) ||
		!is_dir($doc_root)
	) {
		CVCSMain::GetAPPLICATION()->ThrowException(GetMessage("VCS_NO_DOC_ROOT"));
	} else {
		$arDriver['SETTINGS'] = array(
			'site' => strval($arDriver['SETTINGS']['site']),
			'doc_root' => strval($arDriver['SETTINGS']['doc_root']),
			'is_full_path' => intval($arDriver['SETTINGS']['is_full_path']),
			'extensions' => array_filter($arDriver['SETTINGS']['extensions']),
			'included_dirs' => array_filter($arDriver['SETTINGS']['included_dirs']),
			'excluded_dirs' => array_filter($arDriver['SETTINGS']['excluded_dirs']),
		);

		if ($ID > 0) {
			$bOK = CVCSDriversFactory::Update($ID, $arDriver);
		} else {
			$ID = CVCSDriversFactory::Add($arDriver);
			if ($ID) {
				$bOK = true;
			}

			$new = true;
		}
	}

	if ($bOK) {
		if (strlen($save)>0) LocalRedirect($LIST_URL . '?lang='.LANG);
		elseif ($new) LocalRedirect($APPLICATION->GetCurPage() . '?ID='.$ID. '&lang='.LANG.'&tabControl_active_tab='.urlencode($tabControl_active_tab));
	} else {
		if ($e = $APPLICATION->GetException())
			$message = new CAdminMessage(GetMessage("VCS_ERROR_SAVE"), $e);
	}
}

$arDriver = CVCSDriversFactory::GetList(array(), array('ID' => $ID))->Fetch();
if ($arDriver) {
	$arDriver['SETTINGS'] = unserialize($arDriver['SETTINGS']);
}

if (!empty($_REQUEST['DRV'])) {
	$arDriver = $_REQUEST['DRV'];
}

isset($arDriver['DRIVER_CODE']) or $arDriver['DRIVER_CODE'] = '';
isset($arDriver['NAME']) or $arDriver['NAME'] = '';
isset($arDriver['ACTIVE']) or $arDriver['ACTIVE'] = 1;

isset($arDriver['SETTINGS']['doc_root']) or $arDriver['SETTINGS']['doc_root'] = '/';
isset($arDriver['SETTINGS']['is_full_path']) or $arDriver['SETTINGS']['is_full_path'] = 0;
isset($arDriver['SETTINGS']['site']) or $arDriver['SETTINGS']['site'] = '';

if (empty($arDriver['SETTINGS']['extensions']) || !is_array($arDriver['SETTINGS']['extensions']))
	$arDriver['SETTINGS']['extensions'] = array('.php', '.js', '.css', '.htaccess', '.xml', '.html', '.htm');
if (empty($arDriver['SETTINGS']['included_dirs']) || !is_array($arDriver['SETTINGS']['included_dirs']))
	$arDriver['SETTINGS']['included_dirs'] = array('');
if (empty($arDriver['SETTINGS']['excluded_dirs']) || !is_array($arDriver['SETTINGS']['excluded_dirs']))
	$arDriver['SETTINGS']['excluded_dirs'] = array('');


CVCSMain::InitJS();

if ($ID > 0) {
	$APPLICATION->SetTitle(GetMessage("VCS_TITLE_EDIT"));
} else {
	$APPLICATION->SetTitle(GetMessage("VCS_TITLE_ADD"));
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$aMenu = array(
	array(
		'ICON'	=> 'btn_list',
		'TEXT'	=> GetMessage("VCS_DRIVERS_LIST"),
		'LINK'	=> $LIST_URL . '?lang=' . LANG
	)
);


$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($message)
	echo $message->Show();

$aTabs = array();
$aTabs[] = array(
	'DIV' => 'edit1',
	'TAB' => GetMessage("VCS_SOURCE_DRIVER"),
	//'ICON'=>'ticket_dict_edit',
	'TITLE'=>GetMessage("VCS_SOURCE_DRIVER_TITLE")
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>
<form name="form1" method="POST" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>&ID=<?=$ID?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value="<?=$ID?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<?$tabControl->Begin();?>

	<?$tabControl->BeginNextTab();?>

	<tr valign="top">
		<td align="right" width="35%"><?=GetMessage("VCS_DRIVER_CODE")?></td>
		<td width="65%">
			<input type="text" name="DRV[DRIVER_CODE]" value="<?=htmlspecialchars($arDriver['DRIVER_CODE'])?>"<?if ($ID > 0){?> disabled="disabled"<?}?>>
			<?if ($ID > 0){?><input type="hidden" name="DRV[DRIVER_CODE]" value="<?=htmlspecialchars($arDriver['DRIVER_CODE'])?>"><?}?>
		</td>
	</tr>
	<tr valign="top">
		<td align="right" width="35%"><?=GetMessage("VCS_NAME")?></td>
		<td width="65%"><input type="text" name="DRV[NAME]" value="<?=htmlspecialchars($arDriver['NAME'])?>" size="70"></td>
	</tr>
	<tr valign="top">
		<td align="right" width="35%"><?=GetMessage("VCS_ACTIVE")?></td>
		<td width="65%">
			<input type="hidden" name="DRV[ACTIVE]" value="0">
			<input type="checkbox" name="DRV[ACTIVE]" value="1"<? if($arDriver['ACTIVE']){ ?> checked="checked"<? } ?>>
		</td>
	</tr>
	<tr valign="top">
		<td align="right" width="35%"><?=GetMessage("VCS_IS_ABS_PATH")?></td>
		<td width="65%">
			<input type="hidden" name="DRV[SETTINGS][is_full_path]" value="0">
			<input id="full_path_ch" type="checkbox" name="DRV[SETTINGS][is_full_path]" value="1"<? if($arDriver['SETTINGS']['is_full_path']){ ?> checked="checked"<? } ?>>
		</td>
	</tr>
	<tr valign="top">
		<td align="right" width="35%"><?=GetMessage("VCS_SITE")?></td>
		<td width="65%">
			<select id="sites_sel" name="DRV[SETTINGS][site]">
				<?
				$rsSites = CSite::GetList($_b, $_o);
				while ($arSite = $rsSites->GetNext()) {
					?><option value="<?=$arSite['LID']?>"<? if ($arSite['LID'] === $arDriver['SETTINGS']['site']) { ?> selected="selected"<?}?>><?=$arSite['NAME']?> (<?=$arSite['LID']?>)</option><?
				}
				?>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<td align="right" width="35%"><?=GetMessage("VCS_DOC_ROOT")?></td>
		<td width="65%"><input size="70" type="text" name="DRV[SETTINGS][doc_root]" value="<?=htmlspecialchars($arDriver['SETTINGS']['doc_root'])?>"></td>
	</tr>
	<tr valign="top">
		<td align="right" width="35%"><?=GetMessage("VCS_EXTENSIONS")?></td>
		<td width="65%">
			<div>
				<? foreach ($arDriver['SETTINGS']['extensions'] as $ext) { ?>
				<div>
					<input type="text" name="DRV[SETTINGS][extensions][]" value="<?=htmlspecialchars($ext)?>">
				</div>
				<? } ?>
				<button class="add-list-row"><?=GetMessage("VCS_ADD_NEW")?></button>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<td align="right" width="35%"><?=GetMessage("VCS_INC_DIRS")?></td>
		<td width="65%">
			<div>
				<? foreach ($arDriver['SETTINGS']['included_dirs'] as $ext) { ?>
				<div>
					<input size="50" type="text" name="DRV[SETTINGS][included_dirs][]" value="<?=htmlspecialchars($ext)?>">
				</div>
				<? } ?>
				<button class="add-list-row"><?=GetMessage("VCS_ADD_NEW")?></button>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<td align="right" width="35%"><?=GetMessage("VCS_EX_DIRS")?></td>
		<td width="65%">
			<div>
				<? foreach ($arDriver['SETTINGS']['excluded_dirs'] as $ext) { ?>
				<div>
					<input size="50" type="text" name="DRV[SETTINGS][excluded_dirs][]" value="<?=htmlspecialchars($ext)?>">
				</div>
				<? } ?>
				<button class="add-list-row"><?=GetMessage("VCS_ADD_NEW")?></button>
			</div>
		</td>
	</tr>
	<?
	$tabControl->Buttons(Array('back_url' => $LIST_URL . '?lang='.LANGUAGE_ID));
	$tabControl->End();
	?>
</form>
<script type="text/javascript">
(function($) {
	$(function() {
		var callbackEnDisSite = function() {
			if ($('#full_path_ch').is(':checked')) {
				$('#sites_sel').attr('disabled', 'disabled');
			} else {
				$('#sites_sel').removeAttr('disabled');
			}
		}

		$('#full_path_ch').click(callbackEnDisSite);

		callbackEnDisSite.call();

		$('.add-list-row').click(function() {
			$(this).parent().find('div').last().clone(true).insertBefore(this).find('input').val('');

			return false;
		});
	});
})(Karudo.$);
</script>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>