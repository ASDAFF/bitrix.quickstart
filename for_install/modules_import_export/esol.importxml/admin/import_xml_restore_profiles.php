<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$moduleId = 'esol.importxml';
CModule::IncludeModule($moduleId);
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if($_POST['action']=='save')
{
	$APPLICATION->RestartBuffer();
	if(ob_get_contents()) ob_end_clean();
	
	$oProfile = new \Bitrix\EsolImportxml\Profile();
	$arResult = $oProfile->RestoreBackup($_FILES['RESTORE_FILE'], $_POST['PARAMS']);
	echo \CUtil::PhpToJSObject($arResult);
	
	die();
	
	/*$APPLICATION->RestartBuffer();
	if(ob_get_contents()) ob_end_clean();

	CKDAImportExtrasettings::HandleParams($PEXTRASETTINGS, $_POST['EXTRASETTINGS']);
	preg_match_all('/\[([_\d]+)\]/', $fieldName, $keys);
	$oid = 'field_settings_'.$keys[1][0].'_'.$keys[1][1];
	
	if($_GET['return_data'])
	{
		$returnJson = (empty($PEXTRASETTINGS[$keys[1][0]][$keys[1][1]]) ? '""' : CUtil::PhpToJSObject($PEXTRASETTINGS[$keys[1][0]][$keys[1][1]]));
		echo '<script>EList.SetExtraParams("'.$oid.'", '.$returnJson.')</script>';
	}
	else
	{
		$oProfile->UpdateExtra($_REQUEST['PROFILE_ID'], $PEXTRASETTINGS);
		if(!empty($PEXTRASETTINGS[$keys[1][0]][$keys[1][1]])) echo '<script>$("#'.$oid.'").removeClass("inactive");</script>';
		else echo '<script>$("#'.$oid.'").addClass("inactive");</script>';
		echo '<script>BX.WindowManager.Get().Close();</script>';
	}
	die();*/
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="" method="post" enctype="multipart/form-data" name="restore_profiles" id="restore_profiles">
	<input type="hidden" name="action" value="save">
	<?/*if($error){
		ShowError($error);
		?><script>
			EProfileList.RestoreDialogButtonsSet(true);
		</script><?
	}*/?>
	
	<table width="100%">
		<col width="40%">
		<col width="60%">
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("EXOL_IX_RESTORE_FILE");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="file" name="RESTORE_FILE">
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("EXOL_IX_RESTORE_TYPE");?>:</td>
			<td class="adm-detail-content-cell-r">
				<label><input type="radio" name="PARAMS[RESTORE_TYPE]" value="ADD" checked> <?echo GetMessage("EXOL_IX_RESTORE_TYPE_ADD");?></label><br>
				<label><input type="radio" name="PARAMS[RESTORE_TYPE]" value="REPLACE"> <?echo GetMessage("EXOL_IX_RESTORE_TYPE_REPLACE");?></label>
			</td>
		</tr>
	</table>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>