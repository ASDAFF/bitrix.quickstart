<?
define('WDR2_INPUT_CONTINUE', 'wdr2_import_flag_continue');
define('WDR2_INPUT_DONE', 'wdr2_import_flag_done');
define('WDR2_IMPORTING', true);
$ModuleID = 'webdebug.reviews';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/install/demo.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
if (!CModule::IncludeModule($ModuleID)) {
	die('Module is not found!');
}
IncludeModuleLangFile(__FILE__);
$WD_Reviews2_InterfaceID = IntVal($_GET['interface']);
CWD_Reviews2::InitJQuery();
$Lang = LANGUAGE_ID;
$obJSPopup = new CJSPopup();
$obJSPopup->ShowTitlebar(GetMessage('WDR2_IMPORT_POPUP_TITLE'));

if ($_POST['wdr2_import']=='Y') {
	$StartTime = GetMicroTime();
	$MaxTime = 5;
	set_time_limit($MaxTime+1);
	$APPLICATION->RestartBuffer();
	if($_POST['start']=='Y') {
		$_SESSION['WDR2_IMPORT_START_DATA'] = $_POST['fields'];
		$_SESSION['WDR2_IMPORT_ALL_COUNT'] = CWebdebugReviewsImport::GetReviewsCount($_SESSION['WDR2_IMPORT_START_DATA']);
		$_SESSION['WDR2_IMPORT_IMPORTED'] = 0;
		$_SESSION['WDR2_IMPORT_SKIPPED'] = 0;
	}
	$WD_Reviews2_Reviews = new CWD_Reviews2_Reviews;
	while (true) {
		$arItem = CWebdebugReviewsImport::GetNext(IntVal($_SESSION['WDR2_IMPORT_LAST_ID']), $_SESSION['WDR2_IMPORT_START_DATA']);
		if (is_array($arItem) && !empty($arItem)) {
			$arFields = CWebdebugReviewsImport::BuildArray($arItem, $_SESSION['WDR2_IMPORT_START_DATA']);
			$arFields['SKIP_CHECK'] = 'Y';
			if (CWebdebugReviewsImport::ReviewAlreadyImported($arItem['ID'])) {
				$_SESSION['WDR2_IMPORT_LAST_ID'] = $arItem['ID'];
				$_SESSION['WDR2_IMPORT_SKIPPED']++;
			} elseif ($WD_Reviews2_Reviews->Add($arFields)) {
				$_SESSION['WDR2_IMPORT_LAST_ID'] = $arItem['ID'];
				$_SESSION['WDR2_IMPORT_IMPORTED']++;
			} else {
				print GetMessage('WDR2_IMPORT_STATUS_ERROR');
				print '<script>alert(\''.implode('\n',$WD_Reviews2_Reviews->arLastErrors).', '.print_r($arFields,1).'\');</script>';
				die();
			}
		} else {
			print sprintf(GetMessage('WDR2_IMPORT_STATUS_DONE'),$_SESSION['WDR2_IMPORT_IMPORTED'], $_SESSION['WDR2_IMPORT_SKIPPED']);
			print '<input type="hidden" name="'.WDR2_INPUT_DONE.'" value="Y" />';
			unset($_SESSION['WDR2_IMPORT_START_DATA'], $_SESSION['WDR2_IMPORT_ALL_COUNT'], $_SESSION['WDR2_IMPORT_LAST_ID']);
			break;
		}
		$TimeOver = GetMicroTime()>=($StartTime+$MaxTime);
		if ($TimeOver) {
			print sprintf(GetMessage('WDR2_IMPORT_STATUS_CONTINUE'),$_SESSION['WDR2_IMPORT_IMPORTED'],$_SESSION['WDR2_IMPORT_ALL_COUNT']);
			print '<input type="hidden" name="'.WDR2_INPUT_CONTINUE.'" value="Y" />';
			break;
		}
	}
	die();
}
?>

<?
function WDR2_GetIBlockList() {
	$arResult = array();
	if (CModule::IncludeModule('iblock')) {
			$resIBlockTypes = CIBlockType::GetList(array(),array());
			while ($arIBlockType = $resIBlockTypes->GetNext(false,false)) {
				$arIBlockTypeLang = CIBlockType::GetByIDLang($arIBlockType['ID'], LANGUAGE_ID, false);
				$arResult[$arIBlockType['ID']] = array(
					'NAME' => $arIBlockTypeLang['NAME'],
					'ITEMS' => array(),
				);
			}
		$arFilter = array();
		$resIBlock = CIBlock::GetList(array('SORT'=>'ASC'),$arFilter);
		while ($arIBlock = $resIBlock->GetNext(false,false)) {
			$arResult[$arIBlock['IBLOCK_TYPE_ID']]['ITEMS'][] = $arIBlock;
		}
	}
	return $arResult;
}
?>

<?
$arOldFields = array(
	'NAME' => GetMessage('WDR2_IMPORT_FIELD_OLD_NAME'),
	'EMAIL' => GetMessage('WDR2_IMPORT_FIELD_OLD_EMAIL'),
	'WWW' => GetMessage('WDR2_IMPORT_FIELD_OLD_WWW'),
	'TEXT_PLUS' => GetMessage('WDR2_IMPORT_FIELD_OLD_TEXT_PLUS'),
	'TEXT_MINUS' => GetMessage('WDR2_IMPORT_FIELD_OLD_TEXT_MINUS'),
	'TEXT_COMMENTS' => GetMessage('WDR2_IMPORT_FIELD_OLD_TEXT_COMMENTS'),
);
$arVotes = array();
for ($i=0; $i<=9; $i++) {
	$arVotes[$i] = COption::GetOptionString($ModuleID, 'vote_name_'.$i);
	if ($arVotes[$i]=='-') {
		unset($arVotes[$i]);
	}
}
?>

<?
$arInterfaces = CWD_Reviews2_Interface::GetListArray();
$arReviewFields = CWD_Reviews2_Reviews::ReviewGetFields(false, $WD_Reviews2_InterfaceID);
$arReviewRatings = CWD_Reviews2_Reviews::ReviewGetRatings(false, $WD_Reviews2_InterfaceID);
?>

<?
$arTabs = array(
	array('DIV'=>'wdr2_import_tab_general', 'TAB'=>GetMessage('WDR2_IMPORT_TAB_GENERAL_NAME'), 'TITLE'=>GetMessage('WDR2_IMPORT_TAB_GENERAL_DESC')),
	array('DIV'=>'wdr2_import_tab_info', 'TAB'=>GetMessage('WDR2_IMPORT_TAB_INFO_NAME'), 'TITLE'=>GetMessage('WDR2_IMPORT_TAB_INFO_DESC')),
);
$tabControl = new CAdminTabControl("WD_Reviews2_Import", $arTabs);
?>

<style>
#tab_cont_wdr2_import_tab_general:after, #tab_cont_wdr2_import_tab_info:after {content:none!important;}
</style>
<form method="post" action="<?=POST_FORM_ACTION_URI;?>" id="wd_reviews2_import_form">
	<?$tabControl->Begin();?>
	<?$tabControl->BeginNextTab();?>
		<tr id="tr_import_mode">
			<td class="field-name" width="30%"><?=GetMessage('WDR2_IMPORT_FIELD_INTERFACE');?>:</td>
			<td class="field-data">
				<select name="fields[INTERFACE_ID]" class="wdr2_import_control" id="wd_reviews2_import_interface">
					<option value=""><?=GetMessage('WDR2_IMPORT_FIELD_INTERFACE_EMPTY');?></option>
					<?foreach($arInterfaces as $arInterface):?>
						<option value="<?=$arInterface['ID'];?>"><?=$arInterface['NAME'];?></option>
					<?endforeach?>
				</select>
			</td>
		</tr>
		<tr id="tr_import_mode">
			<td class="field-name" width="30%"><?=GetMessage('WDR2_IMPORT_FIELD_MODE');?>:</td>
			<td class="field-data">
				<select name="fields[MODE]" class="wdr2_import_control" id="wd_reviews2_import_mode">
					<option value=""><?=GetMessage('WDR2_IMPORT_FIELD_MODE_EMPTY');?></option>
					<option value="ALL"><?=GetMessage('WDR2_IMPORT_FIELD_MODE_ALL');?></option>
					<option value="IBLOCK_ONLY"><?=GetMessage('WDR2_IMPORT_FIELD_MODE_IBLOCK_ONLY');?></option>
					<option value="IBLOCK_SKIP"><?=GetMessage('WDR2_IMPORT_FIELD_MODE_IBLOCK_SKIP');?></option>
				</select>
			</td>
		</tr>
		<tr id="tr_import_iblock" style="display:none">
			<td class="field-name" width="30%"><?=GetMessage('WDR2_IMPORT_FIELD_IBLOCK');?>:</td>
			<td class="field-data">
				<?$arIBlocks = WDR2_GetIBlockList();?>
				<select name="fields[IBLOCK_ID]" class="wdr2_import_control" id="wd_reviews2_import_iblock">
					<?foreach($arIBlocks as $IBlockTypeID => $arIBlockType):?>
						<optgroup label="[<?=$IBlockTypeID;?>] <?=$arIBlockType["NAME"]?>">
							<?foreach($arIBlockType["ITEMS"] as $arIBlock):?>
								<option value="<?=$arIBlock["ID"]?>"<?if($arIBlock["ID"]==$arSavedValues['iblock_id']):?> selected="selected"<?endif?>>[<?=$arIBlock["ID"]?>] <?=$arIBlock["NAME"]?></option>
							<?endforeach?>
						</optgroup>
					<?endforeach?>
				</select>
			</td>
		</tr>
		<tr id="tr_import_data">
			<td colspan="2">
				<div id="wdr2_import_ajax_wrapper">
					<?if($_GET['wdr2_import_reload']=='Y'){$APPLICATION->RestartBuffer();}?>
					<table class="adm-detail-content-table edit-table" id="wdr2_import_tab_general_edit_table_2">
						<tbody>
							<tr class="heading">
								<td colspan="2"><?=GetMessage('WDR2_IMPORT_HEADER_MATCHES');?></td>
							</tr>
							<?if(!empty($arReviewFields)):?>
								<?foreach($arReviewFields as $Key => $arField):?>
									<?if($arField['TYPE']=='FILE'){continue;}?>
									<tr id="tr_field_<?=$Key;?>">
										<td class="field-name" width="30%"><?=$arField['NAME'];?>:</td>
										<td class="field-data">
											<select class="wdr2_import_control" name="fields[FIELDS][<?=$arField['CODE'];?>]">
												<option value=""><?=GetMessage('WDR2_IMPORT_MATCH_NOT_LOAD');?></option>
												<?foreach($arOldFields as $Key => $strFieldName):?>
													<option value="<?=$Key;?>"><?=$strFieldName;?></option>
												<?endforeach?>
											</select>
										</td>
									</tr>
								<?endforeach?>
							<?else:?>
								<tr id="tr_nofields">
									<td class="field-name" width="30%"></td>
									<td class="field-data">
										<?if($WD_Reviews2_InterfaceID>0):?>
											<?=GetMessage('WDR2_IMPORT_ERROR_NO_FIELDS',array('#LINK#'=>"/bitrix/admin/wd_reviews2_interface.php?ID={$WD_Reviews2_InterfaceID}&lang={$Lang}&WD_Reviews2_Interface_active_tab=wd_reviews2_tab_fields"));?>
										<?else:?>
											<?=GetMessage('WDR2_IMPORT_ERROR_NO_INTERFACE');?>
										<?endif?>
									</td>
								</tr>
							<?endif?>
							<tr class="heading">
								<td colspan="2"><?=GetMessage('WDR2_IMPORT_HEADER_RATINGS');?></td>
							</tr>
							<?if(!empty($arReviewRatings)):?>
								<?foreach($arReviewRatings as $Key => $arRating):?>
									<tr id="tr_vote_<?=$Key;?>">
										<td class="field-name" width="30%"><?=$arRating['NAME'];?>:</td>
										<td class="field-data">
											<select class="wdr2_import_control" name="fields[RATINGS][<?=$arRating['ID'];?>]">
												<option value=""><?=GetMessage('WDR2_IMPORT_RATING_NOT_LOAD');?></option>
												<?foreach($arVotes as $Key => $strVoteName):?>
													<option value="<?=$Key;?>"><?=$strVoteName;?></option>
												<?endforeach?>
											</select>
										</td>
									</tr>
								<?endforeach?>
							<?else:?>
								<tr id="tr_noratings">
									<td class="field-name" width="30%"></td>
									<td class="field-data">
										<?if($WD_Reviews2_InterfaceID>0):?>
											<?=GetMessage('WDR2_IMPORT_ERROR_NO_RATINGS',array('#LINK#'=>"/bitrix/admin/wd_reviews2_interface.php?ID={$WD_Reviews2_InterfaceID}&lang={$Lang}&WD_Reviews2_Interface_active_tab=wd_reviews2_tab_ratings"));?>
										<?else:?>
											<?=GetMessage('WDR2_IMPORT_ERROR_NO_INTERFACE');?>
										<?endif?>
									</td>
								</tr>
							<?endif?>
						</tbody>
					</table>
					<script>
					$('#wdr2_import_tab_general_edit_table_2 tr').each(function(){
						var TD = $(this).find('td');
						if (TD.size()==2) {
							TD.eq(0).addClass('adm-detail-content-cell-l');
							TD.eq(1).addClass('adm-detail-content-cell-r');
						}
					});
					</script>
					<?if($_GET['wdr2_import_reload']=='Y'){die();}?>
				</div>
			</td>
		</tr>
		<?$tabControl->BeginNextTab();?>
		<tr>
			<td colspan="2">
				<?=GetMessage('WDR2_IMPORT_INFO_TEXT');?>
			</td>
		</tr>
	<?
	$strBtnStart = GetMessage('WDR2_IMPORT_BTN_START');
	$strBtnClose = GetMessage('WDR2_IMPORT_BTN_CLOSE');
	$btnSave = "{
		title: '{$strBtnStart}',
		name: 'start',
		id: 'wdr2_import_start',
		className: 'adm-btn-save',
		action: function(){WDR2_Import_Start(); return false;}
	}";
	$btnCancel = "{
		title: '{$strBtnClose}',
		name: 'cancel',
		id: 'wdr2_import_cancel',
		action: function(){BX.WindowManager.Get().Close();if(window.reloadAfterClose)top.BX.reload(true);}
	}";
	$tabControl->ButtonsPublic(array($btnSave,$btnCancel));
	?>
	<?$tabControl->End();?>
</form>
<script>
function WDR2_Wait(Flag) {
	if (Flag) {
		$('#wd_reviews2_import_form .wdr2_import_control').attr('disabled','disabled');
		$('#wdr2_import_start').attr('disabled','disabled');
	} else {
		$('#wd_reviews2_import_form .wdr2_import_control').removeAttr('disabled');
		$('#wdr2_import_start').removeAttr('disabled');
	}
}
function WDR2_Import_Start() {
	var bCanImport = true;
	var Mode = $('#wd_reviews2_import_mode').val();
	if (Mode.length<=0) {
		alert('<?=GetMessage('WDR2_IMPORT_ALERT_NOMODE');?>');
		bCanImport = false;
	}
	if (bCanImport) {
		var InterfaceID = parseInt($('#wd_reviews2_import_interface').val());
		if (isNaN(InterfaceID) || InterfaceID<=0) {
			alert('<?=GetMessage('WDR2_IMPORT_ALERT_NOINTERFACE');?>');
			bCanImport = false;
		}
	}
	if (bCanImport) {
		WDR2_Import_Do(true);
	}
}
function WDR2_Import_Do(Start) {
	WDR2_Wait(true);
	var InterfaceID = parseInt($('#wd_reviews2_import_interface').val());
	var FormData = 'wdr2_import=Y';
	if (Start) {
		FormData += '&start=Y'
	} else {
		FormData += '&continue=Y'
	}
	FormData += '&interface='+InterfaceID+'&lang=<?=$Lang;?>';
	FormData += '&'+$('#wd_reviews2_import_form').serialize();
	$('#wd_reviews2_import_form [name]').each(function(){
		FormData += '&'+encodeURIComponent($(this).attr('name')) + '=' + $(this).val();
	});
	$.ajax({
		url: '<?=$APPLICATION->GetCurPage();?>',
		type: 'POST',
		data: FormData,
		success: function(HTML) {
			$('#wdr2_import_status').html(HTML);
			WDR2_Wait(false);
			if ($('#wdr2_import_status input[type=hidden][name=<?=WDR2_INPUT_CONTINUE;?>]').val()=='Y') {
				WDR2_Import_Do(false);
			}
		}
	});
}
$('#wd_reviews2_import_interface').change(function(){
	var InterfaceID = $(this).val();
	WDR2_Wait(true);
	$.ajax({
		url: '<?=$APPLICATION->GetCurPage();?>',
		type: 'GET',
		data: 'wdr2_import_reload=Y&interface='+InterfaceID+'&lang=<?=$Lang;?>',
		success: function(HTML) {
			$('#wdr2_import_ajax_wrapper').html(HTML);
			WDR2_Wait(false);
		}
	});
});
$('#wdr2_import_start').parent().append('<span id="wdr2_import_status"></span>');

$('#wd_reviews2_import_mode').change(function(){
	if ($(this).val().indexOf('IBLOCK_')=='0') {
		$('#tr_import_iblock').show();
	} else {
		$('#tr_import_iblock').hide();
	}
});

</script>

<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>