<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$moduleId = 'kda.exportexcel';
CModule::IncludeModule('iblock');
CModule::IncludeModule('highloadblock');
CModule::IncludeModule($moduleId);
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$HLBL_ID = $_REQUEST['HLBL_ID'];

$oProfile = new CKDAExportProfile('highload');
$arProfile = $oProfile->GetByID($_REQUEST['PROFILE_ID']);
$SETTINGS_DEFAULT = $arProfile['SETTINGS_DEFAULT'];

$fl = new CKDAEEFieldList($SETTINGS_DEFAULT);

$isOffer = false;
$field = $_REQUEST['field'];

$oProfile = new CKDAExportProfile('highload');
$oProfile->ApplyExtra($PEXTRASETTINGS, $_REQUEST['PROFILE_ID']);
if(isset($_POST['POSTEXTRA']))
{
	$arFieldParams = CUtil::JsObjectToPhp($_POST['POSTEXTRA']);
	if(!$arFieldParams) $arFieldParams = array();
	if(!defined('BX_UTF') || !BX_UTF)
	{
		$arFieldParams = $APPLICATION->ConvertCharsetArray($arFieldParams, 'UTF-8', 'CP1251');
	}
	$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name']));
	$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
	eval('$arFieldsParamsInArray = &$P'.$fNameEval.';');
	$arFieldsParamsInArray = $arFieldParams;
}

if($_POST['action'] == 'save' && is_array($_POST['EXTRASETTINGS']))
{
	CKDAExportExtrasettings::HandleParams($PEXTRASETTINGS, $_POST['EXTRASETTINGS']);
	preg_match_all('/\[([_\d]+)\]/', $_GET['field_name'], $keys);
	$oid = 'field_settings_'.$keys[1][0].'_'.$keys[1][1];

	$APPLICATION->RestartBuffer();
	ob_end_clean();
	
	if($_GET['return_data'])
	{
		$returnJson = (empty($PEXTRASETTINGS[$keys[1][0]][$keys[1][1]]) ? '""' : CUtil::PhpToJSObject($PEXTRASETTINGS[$keys[1][0]][$keys[1][1]]));
		echo '<script>EList.SetExtraParams("'.$oid.'", '.$returnJson.')</script>';
	}
	else
	{
		$oProfile->UpdateExtra($_REQUEST['PROFILE_ID'], $PEXTRASETTINGS);
		$isEmpty = (empty($PEXTRASETTINGS[$keys[1][0]][$keys[1][1]]));
		echo '<script>ESettings.OnSettingsSave("'.$oid.'", '.($isEmpty ? 'false' : 'true').');</script>';
	}
	die();
}

$ee = new CKDAExportExcelHighload($SETTINGS_DEFAULT);
$bPicture = $ee->IsPictureField($field);
$bMultipleProp = $ee->IsMultipleField($field);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="" method="post" enctype="multipart/form-data" name="field_settings">
	<input type="hidden" name="action" value="save">
	<table width="100%">
		
		<?if($bPicture){?>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_INSERT_PICTURE");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[INSERT_PICTURE]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					$insertPic = $val;
					?>
					<input type="checkbox" name="<?=$fName?>" value="Y" <?=($val=='Y' ? 'checked' : '')?> onchange="ESettings.ToggleSubfields(this)">
					&nbsp; <?echo GetMessage("KDA_EE_SETTINGS_INSERT_PICTURE_NOTE");?>
				</td>
			</tr>
			<tr class="subfield" <?if($insertPic!='Y'){echo 'style="display: none;"';}?>>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_PICTURE_WIDTH");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[PICTURE_WIDTH]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>"  value="<?=htmlspecialcharsbx($val)?>" placeholder="100">
				</td>
			</tr>
			<tr class="subfield" <?if($insertPic!='Y'){echo 'style="display: none;"';}?>>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_PICTURE_HEIGHT");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[PICTURE_HEIGHT]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>"  value="<?=htmlspecialcharsbx($val)?>" placeholder="100">
				</td>
			</tr>
		<?}?>
		
		<?if($bMultipleProp){?>
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_CHANGE_MULTIPLE_SEPARATOR");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[CHANGE_MULTIPLE_SEPARATOR]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					$fName2 = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[MULTIPLE_SEPARATOR]';
					$fNameEval2 = strtr($fName2, array("["=>"['", "]"=>"']"));
					eval('$val2 = $P'.$fNameEval2.';');
					?>
					<input type="checkbox" name="<?=$fName?>" value="Y" <?=($val=='Y' ? 'checked' : '')?> onchange="$('#multiple_separator').css('display', (this.checked ? '' : 'none'));"><br>
					<input type="text" id="multiple_separator" name="<?=$fName2?>" value="<?=htmlspecialcharsbx($val2)?>" placeholder="<?echo GetMessage("KDA_EE_SETTINGS_MULTIPLE_SEPARATOR_PLACEHOLDER");?>" <?=($val!='Y' ? 'style="display: none"' : '')?>>
				</td>
			</tr>
		<?}?>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("KDA_EE_SETTINGS_CONVERSION_TITLE");?></td>
		</tr>
		<tr>
			<td class="kda-ee-settings-margin-container" colspan="2">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[CONVERSION]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				$arVals = array();
				if(is_array($PEXTRASETTINGS))
				{
					eval('$arVals = $P'.$fNameEval.';');
				}
				$showCondition = true;
				if(!is_array($arVals) || count($arVals)==0)
				{
					$showCondition = false;
					$arVals = array(
						array(
							'WHEN' => '',
							'FROM' => '',
							'THEN' => '',
							'TO' => ''
						)
					);
				}
				
				foreach($arVals as $k=>$v)
				{
					echo '<div class="kda-ee-settings-conversion" '.(!$showCondition ? 'style="display: none;"' : '').'>'.
							GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_TITLE").
							' <select name="'.$fName.'[WHEN][]">'.
								'<option value="EQ" '.($v['WHEN']=='EQ' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_EQ").'</option>'.
								'<option value="GT" '.($v['WHEN']=='GT' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_GT").'</option>'.
								'<option value="LT" '.($v['WHEN']=='LT' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_LT").'</option>'.
								'<option value="GEQ" '.($v['WHEN']=='GEQ' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_GEQ").'</option>'.
								'<option value="LEQ" '.($v['WHEN']=='LEQ' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_LEQ").'</option>'.
								'<option value="CONTAIN" '.($v['WHEN']=='CONTAIN' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_CONTAIN").'</option>'.
								'<option value="EMPTY" '.($v['WHEN']=='EMPTY' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_EMPTY").'</option>'.
								'<option value="NOT_EMPTY" '.($v['WHEN']=='NOT_EMPTY' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_NOT_EMPTY").'</option>'.
								'<option value="REGEXP" '.($v['WHEN']=='REGEXP' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_REGEXP").'</option>'.
								'<option value="ANY" '.($v['WHEN']=='ANY' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_ANY").'</option>'.
							'</select> '.
							'<input type="text" name="'.$fName.'[FROM][]" value="'.htmlspecialcharsbx($v['FROM']).'"> '.
							GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_THEN").
							' <select name="'.$fName.'[THEN][]">'.
								'<optgroup label="'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_GROUP_STRING").'">'.
									'<option value="REPLACE_TO" '.($v['THEN']=='REPLACE_TO' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_REPLACE_TO").'</option>'.
									'<option value="REMOVE_SUBSTRING" '.($v['THEN']=='REMOVE_SUBSTRING' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_REMOVE_SUBSTRING").'</option>'.
									'<option value="REPLACE_SUBSTRING_TO" '.($v['THEN']=='REPLACE_SUBSTRING_TO' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_REPLACE_SUBSTRING_TO").'</option>'.
									'<option value="ADD_TO_BEGIN" '.($v['THEN']=='ADD_TO_BEGIN' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_ADD_TO_BEGIN").'</option>'.
									'<option value="ADD_TO_END" '.($v['THEN']=='ADD_TO_END' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_ADD_TO_END").'</option>'.
									'<option value="TRANSLIT" '.($v['THEN']=='TRANSLIT' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_TRANSLIT").'</option>'.
									'<option value="STRIP_TAGS" '.($v['THEN']=='STRIP_TAGS' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_STRIP_TAGS").'</option>'.
									'<option value="CLEAR_TAGS" '.($v['THEN']=='CLEAR_TAGS' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_CLEAR_TAGS").'</option>'.
								'</optgroup>'.
								'<optgroup label="'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_GROUP_MATH").'">'.
									'<option value="MATH_ROUND" '.($v['THEN']=='MATH_ROUND' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_MATH_ROUND").'</option>'.
									'<option value="MATH_MULTIPLY" '.($v['THEN']=='MATH_MULTIPLY' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_MATH_MULTIPLY").'</option>'.
									'<option value="MATH_DIVIDE" '.($v['THEN']=='MATH_DIVIDE' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_MATH_DIVIDE").'</option>'.
									'<option value="MATH_ADD" '.($v['THEN']=='MATH_ADD' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_MATH_ADD").'</option>'.
									'<option value="MATH_SUBTRACT" '.($v['THEN']=='MATH_SUBTRACT' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_MATH_SUBTRACT").'</option>'.
								'</optgroup>'.
								'<optgroup label="'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_GROUP_OTHER").'">'.
									/*'<option value="NOT_LOAD" '.($v['THEN']=='NOT_LOAD' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_NOT_LOAD").'</option>'.*/
									'<option value="ADD_LINK" '.($v['THEN']=='ADD_LINK' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_ADD_LINK").'</option>'.
									'<option value="EXPRESSION" '.($v['THEN']=='EXPRESSION' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_THEN_EXPRESSION").'</option>'.
								'</optgroup>'.
							'</select> '.
							'<input type="text" name="'.$fName.'[TO][]" value="'.htmlspecialcharsbx($v['TO']).'">'.
							'<input class="choose_val" value="..." type="button" onclick="ESettings.ShowChooseVal(this)">'.
							'<a href="javascript:void(0)" onclick="ESettings.RemoveConversion(this)" title="'.GetMessage("KDA_EE_SETTINGS_DELETE").'" class="delete"></a>'.
						 '</div>';
				}
				?>
				<a href="javascript:void(0)" onclick="ESettings.AddConversion(this)"><?echo GetMessage("KDA_EE_SETTINGS_CONVERSION_ADD_VALUE");?></a>
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("KDA_EE_SETTINGS_DISPLAY_TITLE");?></td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_DISPLAY_WIDTH");?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[DISPLAY_WIDTH]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsbx($val)?>" placeholder="200">
			</td>
		</tr>
		
	</table>
</form>
<?
$arFields = $fl->GetSettingsFieldsHighload($HLBL_ID);
?>
<script>
var admKDASettingMessages = <?echo CUtil::PhpToJSObject($arFields)?>;
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>