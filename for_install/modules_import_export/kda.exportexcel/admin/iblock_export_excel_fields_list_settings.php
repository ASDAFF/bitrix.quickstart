<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$moduleId = 'kda.exportexcel';
CModule::IncludeModule($moduleId);
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$listIndex = (int)$_GET['list_index'];

if($_POST['action']=='save' && $_POST['NEW_FIELDS_LIST'])
{
	$APPLICATION->RestartBuffer();
	ob_end_clean();
	$return = htmlspecialcharsex($_POST['NEW_FIELDS_LIST']);
	echo '<script>EList.SetFieldsListSettings("'.$listIndex.'", "'.$return.'")</script>';
	die();
}

$oProfile = new CKDAExportProfile();
$oProfile->Apply($SETTINGS_DEFAULT, $SETTINGS, $_GET['PROFILE_ID']);
$iblockId = $SETTINGS_DEFAULT['IBLOCK_ID'];
$listIndex = $_GET['list_index'];
$changeIblockId = (bool)($SETTINGS['CHANGE_IBLOCK_ID'][$listIndex]=='Y');
if($changeIblockId && $SETTINGS['LIST_IBLOCK_ID'][$listIndex])
{
	$iblockId = $SETTINGS['LIST_IBLOCK_ID'][$listIndex];
}
$fl = new CKDAEEFieldList($SETTINGS_DEFAULT);

$arFieldParams = array('MULTIPLE' => true);
if($_POST['onlysectionprops'])
{
	$arFieldParams['SHOW_ONLY_SECTION_PROPERTY'] = true; 
	$arFieldParams['SECTIONS'] = $_POST['sections'];
	$arFieldParams['ISSUBSECTIONS'] = (bool)($_POST['issubsections']);
}

require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="" method="post" enctype="multipart/form-data" name="field_settings">
	<input type="hidden" name="action" value="save">
	

	<table width="100%" class="kda-ee-fl-settings">		
		<tr>
			<td colspan="2">
				<table width="100%" class="kda-ee-fl-settings-select">
					<tr>
						<td>
							<b><?echo GetMessage("KDA_EE_AVAILABLE_FIELDS");?></b>
						</td>
						<td></td>
						<td>
							<b><?echo GetMessage("KDA_EE_SELECTED_FIELDS");?></b>
						</td>
						<td></td>
					</tr>
					<tr>
						<td>
							<?$fl->ShowSelectFields($iblockId, 'FIELDS_LIST[]', '', $arFieldParams);?>
						</td>
						<td>
							<input name="add" value="&nbsp; > &nbsp;" title="<?echo GetMessage("KDA_EE_SELECT_CHECKED_FIELDS");?>" type="button">
						</td>
						<td>
							<input type="hidden" name="OLD_FIELDS_LIST" value="<?echo htmlspecialcharsex(implode(';', $_POST['fields']));?>">
							<input type="hidden" name="NEW_FIELDS_LIST" value="<?echo htmlspecialcharsex(implode(';', $_POST['fields']));?>">
							<select name="SHOW_FIELDS_LIST[]" multiple></select>
						</td>
						<td>
							<input name="up" class="button" value="<?echo GetMessage("KDA_EE_ABOVE");?>" title="<?echo GetMessage("KDA_EE_FIELDS_ORDER_ABOVE");?>" type="button" disabled><br>
							<input name="down" class="button" value="<?echo GetMessage("KDA_EE_BELOW");?>" title="<?echo GetMessage("KDA_EE_FIELDS_ORDER_BELOW");?>" type="button" disabled><br>
							<input name="del" class="button" value="<?echo GetMessage("KDA_EE_REMOVE");?>" title="<?echo GetMessage("KDA_EE_REMOVE_FIELDS");?>" type="button" disabled><br>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
<?require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>