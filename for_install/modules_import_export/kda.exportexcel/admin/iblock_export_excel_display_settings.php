<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$moduleId = 'kda.exportexcel';
CModule::IncludeModule($moduleId);
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$listIndex = htmlspecialcharsex($_REQUEST['list_index']);
$fieldKey = htmlspecialcharsex($_REQUEST['key']);
$fieldType = htmlspecialcharsex($_REQUEST['type']);
if($fieldType!='image') $fieldType = 'text';
$sharePrefix = 'SETTINGS[DISPLAY_PARAMS]['.$listIndex.']';
$fieldPrefix = $sharePrefix.'['.$fieldKey.']';

$oProfile = new CKDAExportProfile();
$arProfile = $oProfile->GetByID($_REQUEST['PROFILE_ID']);
$SETTINGS_DEFAULT = $arProfile['SETTINGS_DEFAULT'];

if($_POST['PARAMS'])
{
	$_POST['SETTINGS']['DISPLAY_PARAMS'][$listIndex] = $SETTINGS['DISPLAY_PARAMS'][$listIndex] = $_POST['PARAMS'];
}

if($_POST['action']=='save' && is_array($_POST['SETTINGS']))
{
	$APPLICATION->RestartBuffer();
	ob_end_clean();
	$returnJson = (empty($_POST['SETTINGS']['DISPLAY_PARAMS'][$listIndex]) ? '""' : CUtil::PhpToJSObject($_POST['SETTINGS']['DISPLAY_PARAMS'][$listIndex]));
	echo '<script>EList.SetDisplayParams("'.htmlspecialcharsex($listIndex).'", '.$returnJson.')</script>';
	die();
}
require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="" method="post" enctype="multipart/form-data" name="field_settings">
	<input type="hidden" name="action" value="save">
	<?
	if(is_array($SETTINGS['DISPLAY_PARAMS'][$listIndex]))
	{
		$arParams = $SETTINGS['DISPLAY_PARAMS'][$listIndex];
		foreach($arParams as $k=>$v)
		{
			if(is_array($v))
			{
				foreach($v as $k2=>$v2)
				{
					?><input type="hidden" name="<?echo htmlspecialcharsex($sharePrefix);?>[<?echo $k;?>][<?echo $k2;?>]" value="<?echo htmlspecialcharsex($v2);?>"><?
				}
			}
		}
	}
	?>
	<table width="100%">
	
		<?if($fieldKey=='SECTION_PATH'){?>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_SETTING_SECTION_PATH_SEPARATOR");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = $fieldPrefix.'[SECTION_PATH_SEPARATOR]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsex($val)?>" placeholder="/">
				</td>
			</tr>
		<?}?>
		
		<?if(strpos($fieldKey, 'TEXT_ROWS_TOP')===0){?>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_SETTING_HIDE_UNDER_GROUP");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = $fieldPrefix.'[HIDE_UNDER_GROUP]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $'.$fNameEval.';');
					?>
					<input type="hidden" name="<?=$fName?>" value="N">
					<input type="checkbox" name="<?=$fName?>" value="Y" <?if($val=='Y'){echo 'checked';}?>>
				</td>
			</tr>
		<?}?>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_TEXT_ALIGN"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = $fieldPrefix.'[TEXT_ALIGN]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $'.$fNameEval.';');
				if(!$val && $SETTINGS_DEFAULT['DISPLAY_TEXT_ALIGN']) $val = $SETTINGS_DEFAULT['DISPLAY_TEXT_ALIGN'];
				?>
				<select name="<?=$fName?>">
					<option value="LEFT" <?if($val=='LEFT'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_TEXT_ALIGN_LEFT"); ?></option>
					<option value="CENTER" <?if($val=='CENTER'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_TEXT_ALIGN_CENTER"); ?></option>
					<option value="RIGHT" <?if($val=='RIGHT'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_TEXT_ALIGN_RIGHT"); ?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = $fieldPrefix.'[VERTICAL_ALIGN]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $'.$fNameEval.';');
				if(!$val && $SETTINGS_DEFAULT['DISPLAY_VERTICAL_ALIGN']) $val = $SETTINGS_DEFAULT['DISPLAY_VERTICAL_ALIGN'];
				?>
				<select name="<?=$fName?>">
					<option value="TOP" <?if($val=='TOP'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN_TOP"); ?></option>
					<option value="CENTER" <?if($val=='CENTER'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN_CENTER"); ?></option>
					<option value="BOTTOM" <?if($val=='BOTTOM'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN_BOTTOM"); ?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_ROW_HEIGHT"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = $fieldPrefix.'[ROW_HEIGHT]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $'.$fNameEval.';');
				?>
				<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsex($val)?>">
			</td>
		</tr>
		
		<?if($fieldType!='image'){?>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_SETTING_FONT_FAMILY");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = $fieldPrefix.'[FONT_FAMILY]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsex($val)?>" placeholder="Calibri">
				</td>
			</tr>
			
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_SETTING_FONT_SIZE");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = $fieldPrefix.'[FONT_SIZE]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsex($val)?>" placeholder="11">
				</td>
			</tr>
			
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_SETTING_FONT_COLOR");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = $fieldPrefix.'[FONT_COLOR]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsex($val)?>" placeholder="#000000">
				</td>
			</tr>
			
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_SETTING_BACKGROUND_COLOR");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = $fieldPrefix.'[BACKGROUND_COLOR]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsex($val)?>" placeholder="#ffffff">
				</td>
			</tr>
			
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_SETTING_FONT_STYLE_BOLD");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = $fieldPrefix.'[STYLE_BOLD]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $'.$fNameEval.';');
					?>
					<input type="hidden" name="<?=$fName?>" value="N">
					<input type="checkbox" name="<?=$fName?>" value="Y" <?if($val=='Y'){echo 'checked';}?>>
				</td>
			</tr>
			
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_SETTING_FONT_STYLE_ITALIC");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = $fieldPrefix.'[STYLE_ITALIC]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $'.$fNameEval.';');
					?>
					<input type="hidden" name="<?=$fName?>" value="N">
					<input type="checkbox" name="<?=$fName?>" value="Y" <?if($val=='Y'){echo 'checked';}?>>
				</td>
			</tr>
		<?}?>
		
		<?if($fieldType=='image'){?>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_SETTING_PICTURE_WIDTH");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = $fieldPrefix.'[PICTURE_WIDTH]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsex($val)?>">
				</td>
			</tr>
			
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_SETTING_PICTURE_HEIGHT");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = $fieldPrefix.'[PICTURE_HEIGHT]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsex($val)?>">
				</td>
			</tr>
		<?}?>
		
	</table>
</form>
<?require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>