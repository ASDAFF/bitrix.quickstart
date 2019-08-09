<?
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$moduleId = 'kda.exportexcel';
$moduleFilePrefix = 'kda_export_excel';
$moduleJsId = str_replace('.', '_', $moduleId);
$moduleJsId2 = $moduleJsId;
$moduleDemoExpiredFunc = $moduleJsId2.'_demo_expired';
$moduleShowDemoFunc = $moduleJsId2.'_show_demo';
$moduleImagePath = '/bitrix/panel/'.$moduleId.'/images/';
$moduleRunnerClass = 'CKDAExportExcelRunner';
CModule::IncludeModule("iblock");
CModule::IncludeModule($moduleId);
$bCatalog = CModule::IncludeModule('catalog');
$bCurrency = CModule::IncludeModule("currency");
CJSCore::Init(array($moduleJsId));
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);

include_once(dirname(__FILE__).'/../install/demo.php');
if ($moduleDemoExpiredFunc()) {
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	$moduleShowDemoFunc();
	require ($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

/*Close session*/
$sess = $_SESSION;
session_write_close();
$_SESSION = $sess;
/*/Close session*/

$siteEncoding = CKDAExportUtils::getSiteEncoding();

if($_POST)
{
	$arFilterKeys = preg_grep('/^filter_\d+_/', array_keys($_POST));
	foreach($arFilterKeys as $key)
	{
		$arKey = explode('_', $key, 3);
		$SETTINGS['FILTER'][$arKey[1]][$arKey[2]] = $_POST[$key];
	}
	
	if(isset($SETTINGS) && !isset($SETTINGS['LIST_NAME']))
	{
		unset($SETTINGS);
	}
}

if(isset($_FILES) && is_array($_FILES))
{
	$arFileKeys = preg_grep('/^NEW_PICTURE_.+_\d+_\d+$/', array_keys($_FILES));
	foreach($arFileKeys as $fileKey)
	{
		if(!empty($_FILES[$fileKey]))
		{
			$fid = CFile::SaveFile($_FILES[$fileKey], $moduleId);
			$arSubKeys = explode('_', substr($fileKey, 12));
			$blockKey = array_pop($arSubKeys);
			$listKey = array_pop($arSubKeys);
			$blockTextKey = implode('_', $arSubKeys);
			$SETTINGS[$blockTextKey][$listKey][$blockKey] = '[['.$fid.']]';
		}
	}
}

if(($ACTION=='SHOW_PREVIEW' || $ACTION=='DO_EXPORT') && (!defined('BX_UTF') || !BX_UTF))
{
	$SETTINGS = $APPLICATION->ConvertCharsetArray($SETTINGS, 'UTF-8', 'CP1251');
	if($EXTRASETTINGS) $EXTRASETTINGS = $APPLICATION->ConvertCharsetArray($EXTRASETTINGS, 'UTF-8', 'CP1251');
}

$oProfile = new CKDAExportProfile();
if(strlen($PROFILE_ID) > 0 && $PROFILE_ID!=='new')
{
	$oProfile->Apply($SETTINGS_DEFAULT, $SETTINGS, $PROFILE_ID);
	if($EXTRASETTINGS)
	{
		foreach($EXTRASETTINGS as $k=>$v)
		{
			foreach($v as $k2=>$v2)
			{
				if($v2 && !is_array($v2))
				{
					$EXTRASETTINGS[$k][$k2] = CUtil::JsObjectToPhp($v2);
				}
			}
		}
	}
	$oProfile->ApplyExtra($EXTRASETTINGS, $PROFILE_ID);
}

$SETTINGS_DEFAULT['IBLOCK_ID'] = intval($SETTINGS_DEFAULT['IBLOCK_ID']);
$STEP = intval($STEP);
if ($STEP <= 0)
	$STEP = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if(isset($_POST["backButton"]) && strlen($_POST["backButton"]) > 0) $STEP = $STEP - 2;
	if(isset($_POST["saveConfigButton"]) && strlen($_POST["saveConfigButton"]) > 0) $STEP = $STEP - 1;
	if(isset($_POST["backButton2"]) && strlen($_POST["backButton2"]) > 0) $STEP = 1;
}

$strError = $oProfile->GetErrors();
$io = CBXVirtualIo::GetInstance();

/////////////////////////////////////////////////////////////////////
if ($REQUEST_METHOD == "POST" && $MODE=='AJAX')
{
	if($ACTION=='SHOW_MODULE_MESSAGE')
	{
		$APPLICATION->RestartBuffer();
		ob_end_clean();
		?><div><?
		$moduleShowDemoFunc(true);
		?></div><?
		die();
	}
	
	if($ACTION=='DELETE_TMP_DIRS')
	{
		CKDAExportUtils::RemoveTmpFiles();
		CKDAExportUtils::CheckZipArchive();
		die();
	}
	
	if($ACTION=='REMOVE_PROCESS_PROFILE')
	{
		$APPLICATION->RestartBuffer();
		ob_end_clean();
		$oProfile = new CKDAExportProfile();
		$oProfile->RemoveProcessedProfile($PROCCESS_PROFILE_ID);
		die();
	}
	
	if($ACTION=='GET_PROCESS_PARAMS')
	{
		$APPLICATION->RestartBuffer();
		ob_end_clean();
		$oProfile = new CKDAExportProfile();
		echo CUtil::PhpToJSObject($oProfile->GetProccessParams($PROCCESS_PROFILE_ID));
		die();
	}
	
	if($ACTION=='GET_SECTION_LIST')
	{
		$fl = new CKDAEEFieldList($SETTINGS_DEFAULT);
		$APPLICATION->RestartBuffer();
		ob_end_clean();
		?><div><?
		$fl->ShowSelectSections($IBLOCK_ID, 'sections');
		$fl->ShowSelectFields($IBLOCK_ID, 'fields');
		?></div><?
		die();
	}
	
	if($ACTION=='DELETE_PROFILE')
	{
		$fl = new CKDAExportProfile();
		$fl->Delete($_REQUEST['ID']);
		die();
	}
	
	if($ACTION=='COPY_PROFILE')
	{
		$fl = new CKDAExportProfile();
		$id = $fl->Copy($_REQUEST['ID']);
		echo CUtil::PhpToJSObject(array('id'=>$id));
		die();
	}
	
	if($ACTION=='RENAME_PROFILE')
	{
		$newName = $_REQUEST['NAME'];
		if((!defined('BX_UTF') || !BX_UTF)) $newName = $APPLICATION->ConvertCharset($newName, 'UTF-8', 'CP1251');
		$fl = new CKDAExportProfile();
		$fl->Rename($_REQUEST['ID'], $newName);
		die();
	}
	
	if($ACTION=='GET_UID')
	{
		$OFFERS_IBLOCK_ID = CKDAExportUtils::GetOfferIblock($IBLOCK_ID);
		ob_end_clean();
		echo CUtil::PhpToJSObject(array('isOffers'=>($OFFERS_IBLOCK_ID > 0 ? 1 : 0)));
		die();
	}
	
	if($ACTION=='APPLY_TO_LISTS')
	{
		$fl = new CKDAExportProfile();
		$fl->ApplyToLists($_REQUEST['PROFILE_ID'], $_REQUEST['LIST_FROM'], $_REQUEST['LIST_TO']);
		die();
	}
}

if ($REQUEST_METHOD == "POST" && $STEP > 1 && check_bitrix_sessid())
{
	//*****************************************************************//	
	if ($STEP > 1)
	{
		//*****************************************************************//		
		if(strlen($PROFILE_ID)==0)
		{
			$strError.= GetMessage("KDA_EE_PROFILE_NOT_CHOOSE")."<br>";
		}
		
		if (strlen($strError) <= 0)
		{
			if (!$SETTINGS_DEFAULT['IBLOCK_ID'])
				$strError.= GetMessage("KDA_EE_NO_IBLOCK")."<br>";
		}

		if (strlen($strError) <= 0)
		{
			if (!CIBlockRights::UserHasRightTo($SETTINGS_DEFAULT['IBLOCK_ID'], $SETTINGS_DEFAULT['IBLOCK_ID'], "element_read"))
				$strError.= GetMessage("KDA_EE_NO_IBLOCK")."<br>";
		}
		
		if (strlen($strError) <= 0)
		{
			$fpath = $SETTINGS_DEFAULT['FILE_PATH'];
			if(strpos($fpath, '/')!==0)
				$strError.= GetMessage("KDA_EE_FILE_PATH_INCORRECT")."<br>";
		}
		
		if (strlen($strError) <= 0)
		{
			/*Write profile*/
			$oProfile = new CKDAExportProfile();
			if($PROFILE_ID === 'new')
			{
				$PID = $oProfile->Add($NEW_PROFILE_NAME);
				if($PID===false)
				{
					if($ex = $APPLICATION->GetException())
					{
						$strError .= $ex->GetString().'<br>';
					}
				}
				else
				{
					$PROFILE_ID = $PID;
				}
			}
			/*/Write profile*/
		}

		if (strlen($strError) > 0)
			$STEP = 1;
		//*****************************************************************//

	}
	
	if($ACTION=='SHOW_PREVIEW')
	{
		if(!is_array($SETTINGS)) $SETTINGS = array();
		$iblockId = $SETTINGS_DEFAULT['IBLOCK_ID'];
		$list = $_POST['SHEET_INDEX'];
		$changeIblockId = (bool)($SETTINGS['CHANGE_IBLOCK_ID'][$list]=='Y');
		if($changeIblockId && $SETTINGS['LIST_IBLOCK_ID'][$list])
		{
			$iblockId = $SETTINGS['LIST_IBLOCK_ID'][$list];
		}
		$arFieldParams = array();
		if($SETTINGS['SHOW_ONLY_SECTION_PROPERTY'][$list]=='Y')
		{
			$arFieldParams = array(
				'SHOW_ONLY_SECTION_PROPERTY' => true,
				'SECTIONS' => $SETTINGS['FILTER'][$list]['find_section_section'],
				'ISSUBSECTIONS' => (bool)($SETTINGS['FILTER'][$list]['find_el_subsections']=='Y')
			);
		}

		$fl = new CKDAEEFieldList($SETTINGS_DEFAULT);
		$arIblocks = $fl->GetIblocks();
		$listName = ($SETTINGS['LIST_NAME'][$list] ? $SETTINGS['LIST_NAME'][$list] : sprintf(GetMessage("KDA_EE_SHEET_NAME"), $list+1));
		
		/*$filterId = 'kda_exportexcel_'.$PROFILE_ID.'_'.$list;
		CKDAExportUtils::ShowFilter($filterId, $list, $SETTINGS, $SETTINGS_DEFAULT);*/

		$cntLines = 15;
		if(intval($SETTINGS_DEFAULT['COUNT_SHOW_LINES']) > 0) $cntLines = $SETTINGS_DEFAULT['COUNT_SHOW_LINES'];		
		$params = array_merge($SETTINGS_DEFAULT, $SETTINGS);
		$params['MAX_PREVIEW_LINES'] = $cntLines;
		$ee = new CKDAExportExcel($params, $EXTRASETTINGS, false, $PROFILE_ID);

		$arFields = array();
		/*$arRes = $ee->GetExportData($list, $cntLines, 0);
		$arFields = $arRes['FIELDS'];
		$arData = $arRes['DATA'];*/
		$i = $si = 1;
		$arData = array();
		//while((count($arData) < $cntLines) && ($arRes = $ee->GetExportData($list, $cntLines, $i, $si)) && ($arRes['PAGE_COUNT'] >= $i || ($arRes['SECTION_COUNT'] > $si && ($si++) && ($i = 1)) || $i < 2))
		while((count($arData) < $cntLines) && ($arRes = $ee->GetExportData($list, $cntLines, $i, $si)) && (($arRes['SECTION_COUNT'] > 1 && $arRes['SECTION_COUNT'] >= $arRes['SECTION_KEY'] && ($si == $arRes['SECTION_KEY'] || (($i = 0) || 1)) && ($si = $arRes['SECTION_KEY'])) || /*$i < 2*/ $i <= max(1, $arRes['PAGE_COUNT'])))
		{
			if(!empty($arRes['FIELDS'])) $arFields = $arRes['FIELDS'];
			if(!empty($arRes['DATA'])) $arData = array_merge($arData, $arRes['DATA']);
			$i++;
		}
		$arData = array_slice($arData, 0, $cntLines);
		
		if(!isset($SETTINGS['DISPLAY_PARAMS'][$list])) $SETTINGS['DISPLAY_PARAMS'][$list] = array();
		if(!isset($SETTINGS['DISPLAY_PARAMS'][$list]['COLUMN_TITLES'])) $SETTINGS['DISPLAY_PARAMS'][$list]['COLUMN_TITLES'] = array('STYLE_BOLD' => 'Y');
		foreach($arData as $arElement)
		{
			if(isset($arElement['RTYPE']) && ($arElement['RTYPE']=='SECTION_PATH' || preg_match('/^SECTION_\d+$/', $arElement['RTYPE'])))
			{
				if(!isset($SETTINGS['DISPLAY_PARAMS'][$list][$arElement['RTYPE']])) $SETTINGS['DISPLAY_PARAMS'][$list][$arElement['RTYPE']] = array('STYLE_BOLD' => 'Y');
			}
		}
		$rowIndex = $rowTitleIndex = 1;
		
		/*Additionals rows*/
		$textKeys = array('TEXT_ROWS_TOP', 'TEXT_ROWS_TOP2');
		$additionalRows = array();
		foreach($textKeys as $textKey)
		{
			if($textKey=='TEXT_ROWS_TOP2')
			{
				$rowTitleIndex = $rowIndex++;
			}
			if(!empty($SETTINGS[$textKey][$list]))
			{
				foreach($SETTINGS[$textKey][$list] as $k=>$v)
				{
					$rowContent = '';
					$dataType = 'text';
					if(preg_match('/^\[\[(\d+)\]\]$/', $v, $m))
					{
						$dataType = 'image';
						$fileId = $m[1];
					}
					$v = trim($v);
					$dataKey = $textKey.'_'.$k;
					$dSettings = $SETTINGS['DISPLAY_PARAMS'][$list][$dataKey];
					$rowContent .= '<tr title="'.sprintf(GetMessage("KDA_EE_ROW_NUMBER"), $rowIndex++).'">';
					$rowContent .= '<td><span class="sandwich" data-key="'.$dataKey.'" data-type="'.$dataType.'" title="'.GetMessage("KDA_EE_ACTIONS_BTN").'"></span></td>';
					if($dataType == 'image')
					{
						$rowContent .= '<td colspan="'.count($arFields).'"><div class="cell cell_wide80"><div class="cell_inner" '.CKDAExportUtils::GetCellStyleFormatted($dSettings, $SETTINGS_DEFAULT).'>';
						$maxWidth = ((int)$dSettings['PICTURE_WIDTH'] > 0 ? (int)$dSettings['PICTURE_WIDTH'] : 0);
						$maxHeight = ((int)$dSettings['PICTURE_HEIGHT'] > 0 ? (int)$dSettings['PICTURE_HEIGHT'] : 0);
						$arFile = CFile::GetFileArray($fileId);
						$rowContent .= '<img src="'.htmlspecialcharsbx($arFile['SRC']).'" style="'.($maxWidth > 0 ? 'max-width: '.$maxWidth.'px;' : '').($maxHeight > 0 ? 'max-height: '.$maxHeight.'px;' : '').'">';
						$rowContent .= '<input type="hidden" name="SETTINGS['.$textKey.']['.$list.']['.$k.']" value="'.htmlspecialcharsbx($v).'">';
						$rowContent .= '</div></div></td>';
					}
					else
					{
						$rowContent .= '<td colspan="'.count($arFields).'"><div class="cell cell_wide80"><div class="cell_inner">';
						$rowContent .= '<textarea class="kda-ee-text-block" name="SETTINGS['.$textKey.']['.$list.']['.$k.']" '.CKDAExportUtils::GetCellStyleFormatted($dSettings, $SETTINGS_DEFAULT).'>'.$v.'</textarea>';
						//$rowContent .= '<input class="kda-ee-text-block-val" value="..." onclick="EList.ShowAddTextMenu(this);" type="button" title="'.GetMessage("KDA_EE_ADD_TEXT_VAL").'">';
						$rowContent .= '</div></div></td>';
					}
					$rowContent .= '</tr>';
					$additionalRows[$textKey][] = $rowContent;
				}
			}
		}
		/*/Additionals rows*/
		
		$sortVal = ($SETTINGS['SORT'][$list] ? $SETTINGS['SORT'][$list] : 'IE_NAME=>ASC');
		list($sortBy, $sortOrder) = explode('=>', $sortVal);
		$arSortableFields = $fl->GetSortableFields($iblockId);
		$level = 0;
		
		$APPLICATION->RestartBuffer();
		ob_end_clean();
		?>
		<div class="kda-ee-title">
			<input type="text" name="SETTINGS[LIST_NAME][<?echo $list?>]" value="<?echo htmlspecialcharsbx($listName)?>" maxlength="31">
			<?if($list > 0){?>
				<a href="javascript:void(0)" class="kda-ee-remove-list" onclick="EList.RemoveList(this);" title="<?echo GetMessage("KDA_EE_REMOVE_LIST"); ?>"></a>
			<?}?>
		</div>
		<div class="kda-ee-hidden-settings">
			<?echo $fl->ShowSelectFields($iblockId, 'FIELDS_LIST['.$list.']', '', $arFieldParams);?>
			<?if(isset($SETTINGS['DISPLAY_PARAMS'][$list]) && !empty($SETTINGS['DISPLAY_PARAMS'][$list])){?>
				<input type="hidden" name="SETTINGS[DISPLAY_PARAMS][<?echo $list;?>]" value="">
				<script>EList.SetDisplayParams("<?echo $list?>", <?echo CUtil::PhpToJSObject($SETTINGS['DISPLAY_PARAMS'][$list])?>)</script>
			<?}?>
			<input type="hidden" name="SETTINGS[SORT][<?echo $list;?>]" value="<?echo htmlspecialcharsbx($sortVal);?>">
		</div>
		<div class="kda-ee-additional-settings">
			<a href="javascript:void(0)" class="addsettings_link" onclick="EList.ToggleAddSettingsBlock(this)"><span><?echo GetMessage("KDA_EE_ADDITIONAL_SETTINGS"); ?></span></a>
			<div class="addsettings_inner">
				<table class="additional">
					<col><col width="400px">
					<tr>
						<td><?echo GetMessage("KDA_EE_LIST_LABEL_COLOR"); ?>:</td>
						<td>
							<input type="text" name="SETTINGS[LIST_LABEL_COLOR][<?echo $list;?>]" value="<?=htmlspecialcharsbx($SETTINGS['LIST_LABEL_COLOR'][$list])?>" placeholder="#ffffff" size="10">
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("KDA_EE_HIDE_COLUMN_TITLES"); ?>:</td>
						<td>
							<input type="hidden" name="SETTINGS[HIDE_COLUMN_TITLES][<?echo $list;?>]" value="N">
							<input type="checkbox" name="SETTINGS[HIDE_COLUMN_TITLES][<?echo $list;?>]" value="Y" <?if($SETTINGS['HIDE_COLUMN_TITLES'][$list]=='Y'){echo 'checked';}?>>
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("KDA_EE_ENABLE_AUTOFILTER"); ?>:</td>
						<td>
							<input type="hidden" name="SETTINGS[ENABLE_AUTOFILTER][<?echo $list;?>]" value="N">
							<input type="checkbox" name="SETTINGS[ENABLE_AUTOFILTER][<?echo $list;?>]" value="Y" <?if($SETTINGS['ENABLE_AUTOFILTER'][$list]=='Y'){echo 'checked';}?>>
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("KDA_EE_ENABLE_PROTECTION"); ?>:</td>
						<td>
							<input type="hidden" name="SETTINGS[ENABLE_PROTECTION][<?echo $list;?>]" value="N">
							<input type="checkbox" name="SETTINGS[ENABLE_PROTECTION][<?echo $list;?>]" value="Y" <?if($SETTINGS['ENABLE_PROTECTION'][$list]=='Y'){echo 'checked';}?>>
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("KDA_EE_SHOW_ONLY_SECTION_PROPERTY"); ?>:</td>
						<td>
							<input type="hidden" name="SETTINGS[SHOW_ONLY_SECTION_PROPERTY][<?echo $list;?>]" value="N">
							<input type="checkbox" name="SETTINGS[SHOW_ONLY_SECTION_PROPERTY][<?echo $list;?>]" value="Y" <?if($SETTINGS['SHOW_ONLY_SECTION_PROPERTY'][$list]=='Y'){echo 'checked';}?> onchange="EList.SetSectionProperties(this);">
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("KDA_EE_SHOW_ONLY_SECTION_FROM_FILTER"); ?>:</td>
						<td>
							<input type="hidden" name="SETTINGS[SHOW_ONLY_SECTION_FROM_FILTER][<?echo $list;?>]" value="N">
							<input type="checkbox" name="SETTINGS[SHOW_ONLY_SECTION_FROM_FILTER][<?echo $list;?>]" value="Y" <?if($SETTINGS['SHOW_ONLY_SECTION_FROM_FILTER'][$list]=='Y'){echo 'checked';}?> onchange="EList.SetSectionProperties(this);">
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("KDA_EE_CHANGE_IBLOCK_ID"); ?>:</td>
						<td>
							<input type="hidden" name="SETTINGS[CHANGE_IBLOCK_ID][<?echo $list;?>]" value="N">
							<input type="checkbox" name="SETTINGS[CHANGE_IBLOCK_ID][<?echo $list;?>]" value="Y" <?if($changeIblockId){echo 'checked';}?> onchange="EList.ToggleAddSettings(this); EList.ChooseChangeIblock(this);">
						</td>
					</tr>
					
					<tr class="subfield" <?if(!$changeIblockId){echo 'style="display: none;"';}?>>
						<td><?echo GetMessage("KDA_EE_INFOBLOCK"); ?>:</td>
						<td>
							<select name="SETTINGS[LIST_IBLOCK_ID][<?echo $list;?>]" onchange="EList.ChooseIblock(this);">
								<?
								foreach($arIblocks as $type)
								{
									?><optgroup label="<?echo $type['NAME']?>"><?
									foreach($type['IBLOCKS'] as $iblock)
									{
										?><option value="<?echo $iblock["ID"];?>" <?if($iblock["ID"]==$iblockId){echo 'selected';}?>><?echo htmlspecialcharsbx($iblock["NAME"]); ?></option><?
									}
									?></optgroup><?
								}
								?>
							</select>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?		
		echo '<div class="kda-ee-tbl-scroll"><div></div></div>';
		echo '<div class="kda-ee-tbl-wrap">';
		echo '<table class="kda-ee-tbl" data-iblock-id="'.$iblockId.'">';
		
		$textKey = 'TEXT_ROWS_TOP';
		if(isset($additionalRows[$textKey]) && is_array($additionalRows[$textKey]))
		{
			echo implode('', $additionalRows[$textKey]);
		}
		
		echo '<tr class="kda-ee-tbl-titles">';
		echo '<th title="'.sprintf(GetMessage("KDA_EE_ROW_NUMBER"), $rowTitleIndex).'"><span class="sandwich" data-key="COLUMN_TITLES" title="'.GetMessage("KDA_EE_ACTIONS_BTN").'"></span></th>';
		foreach($arFields as $k=>$field)
		{
			$fieldName = $SETTINGS['FIELDS_LIST_NAMES'][$list][$k];
			$isSortable = (bool)in_array($field, $arSortableFields);
			if($isSortable)
			{
				$sortClass = 'sort_up';
				$sortTitle = GetMessage("KDA_EE_SETTINGS_SORT_ASC");
				if($sortBy==$field)
				{
					if($sortOrder!='DESC')
					{
						$sortClass = 'sort_down';
						$sortTitle = GetMessage("KDA_EE_SETTINGS_SORTED_ASC")."\r\n".GetMessage("KDA_EE_SETTINGS_SORT_DESC");
					}
					else
					{
						$sortTitle = GetMessage("KDA_EE_SETTINGS_SORTED_DESC")."\r\n".$sortTitle;
					}
					$sortClass .= ' active';
				}
			}
			echo '<th>'.
					'<div>'.
						'<input type="hidden" name="SETTINGS[FIELDS_LIST]['.$list.']['.$k.']" value="'.htmlspecialcharsbx($field).'" >'.
						'<input type="text" name="FIELDS_LIST_SHOW['.$list.']['.$k.']" value="" class="fieldval">'.
						'<a href="javascript:void(0)" class="field_settings '.(empty($EXTRASETTINGS[$list][$k]) ? 'inactive' : '').'" id="field_settings_'.$list.'_'.$k.'" title="'.GetMessage("KDA_EE_SETTINGS_FIELD").'" onclick="EList.ShowFieldSettings(this);">'.
							'<input type="hidden" name="EXTRASETTINGS['.$list.']['.$k.']" value="">'.
							'<script>EList.SetExtraParams("field_settings_'.$list.'_'.$k.'", '.(empty($EXTRASETTINGS[$list][$k]) ? '""' : CUtil::PhpToJSObject($EXTRASETTINGS[$list][$k])).')</script>'.
						'</a>'.
						'<a href="javascript:void(0)" class="field_delete" title="'.GetMessage("KDA_EE_SETTINGS_DELETE_FIELD").'" onclick="EList.DeleteColumn(this);"></a>'.
						'<a href="javascript:void(0)" onclick="EList.AddColumn(this);" class="kda-ee-new-column" title="'.GetMessage("KDA_EE_SETTINGS_ADD_FIELD").'"></a>'.
					'</div>'.
					'<div>'.
						'<input type="text" name="SETTINGS[FIELDS_LIST_NAMES]['.$list.']['.$k.']" value="'.htmlspecialcharsbx($fieldName).'" class="fieldname" '.CKDAExportUtils::GetCellStyleFormatted($SETTINGS['DISPLAY_PARAMS'][$list]['COLUMN_TITLES'], $SETTINGS_DEFAULT).'>'.
						($isSortable ? '<a href="javascript:void(0);" class="'.$sortClass.'" onclick="EList.Sort(this);" title="'.htmlspecialcharsbx($sortTitle).'"></a>' : '').
					'</div>'.
				'</th>';
		}
		echo '</tr>';
		
		$textKey = 'TEXT_ROWS_TOP2';
		if(isset($additionalRows[$textKey]) && is_array($additionalRows[$textKey]))
		{
			echo implode('', $additionalRows[$textKey]);
		}
			
		foreach($arData as $arElement)
		{
			echo '<tr>';
			$m = false;
			if(isset($arElement['RTYPE']) && ($arElement['RTYPE']=='SECTION_PATH' || preg_match('/^SECTION_(\d+)$/', $arElement['RTYPE'], $m)))
			{
				if(!isset($SETTINGS['DISPLAY_PARAMS'][$list][$arElement['RTYPE']]))
				{
					$SETTINGS['DISPLAY_PARAMS'][$list][$arElement['RTYPE']] = array('STYLE_BOLD' => 'Y');
				}
				if(is_array($m) && $m[1] > 0)
				{
					$level = $m[1] - 1;
					if($SETTINGS_DEFAULT['EXPORT_GROUP_INDENT']=='Y')
					{
						$SETTINGS['DISPLAY_PARAMS'][$list][$arElement['RTYPE']]['INDENT'] = $level;
					}
					$level++;
				}
				echo '<td title="'.sprintf(GetMessage("KDA_EE_ROW_NUMBER"), $rowIndex++).'"><span class="sandwich" data-key="'.$arElement['RTYPE'].'" title="'.GetMessage("KDA_EE_ACTIONS_BTN").'"></span></td>';
				echo '<td colspan="'.count($arFields).'"><div class="cell cell_wide"><div class="cell_inner" '.CKDAExportUtils::GetCellStyleFormatted($SETTINGS['DISPLAY_PARAMS'][$list][$arElement['RTYPE']], $SETTINGS_DEFAULT).'>'.$arElement['NAME'].'</div></div></td>';
			}
			else
			{
				echo '<td title="'.sprintf(GetMessage("KDA_EE_ROW_NUMBER"), $rowIndex++).'"></td>';
				foreach($arFields as $key=>$field)
				{
					$val = (isset($arElement[$field.'_'.$key]) ? $arElement[$field.'_'.$key] : $arElement[$field]);
					$isHtml = (bool)(!is_array($val) && strpos($val, 'kda-ee-conversion-link')!==false);
					$fSettings = $EXTRASETTINGS[$list][$key];
					if(((isset($fSettings['INSERT_PICTURE']) && $fSettings['INSERT_PICTURE']=='Y') || $field=='IE_QR_CODE_IMAGE') && $ee->IsPictureField($field))
					{
						$arVals = $val;
						if(!is_array($arVals)) $arVals = array($arVals);
						$maxWidth = (isset($fSettings['PICTURE_WIDTH']) && (int)$fSettings['PICTURE_WIDTH'] > 0 ? (int)$fSettings['PICTURE_WIDTH'] : 100);
						$maxHeight = (isset($fSettings['PICTURE_HEIGHT']) && (int)$fSettings['PICTURE_HEIGHT'] > 0 ? (int)$fSettings['PICTURE_HEIGHT'] : 100);
						foreach($arVals as $mkey=>$val)
						{
							if($mkey==='TYPE') continue;
							if($ee->IsMultipleField($field))
							{
								if($fSettings['CHANGE_MULTIPLE_SEPARATOR']=='Y') $separator = $fSettings['MULTIPLE_SEPARATOR'];
								else $separator = $SETTINGS_DEFAULT['ELEMENT_MULTIPLE_SEPARATOR'];
								$arVals2 = explode($separator, $val);
								$val = '';
								foreach($arVals2 as $mval)
								{
									if(preg_match('/(<a[^>]+class="kda\-ee\-conversion\-link"[^>]*>)(.*)(<\/a>)/Uis', $mval, $m))
									{
										$val .= $m[1].'<img src="'.htmlspecialcharsbx($m[2]).'" style="max-width: '.$maxWidth.'px; max-height: '.$maxHeight.'px;">'.$m[3];
									}
									else
									{
										$val .= '<img src="'.htmlspecialcharsbx($mval).'" style="max-width: '.$maxWidth.'px; max-height: '.$maxHeight.'px;">';
									}
								}
							}
							else
							{
								if(preg_match('/(<a[^>]+class="kda\-ee\-conversion\-link"[^>]*>)(.*)(<\/a>)/Uis', $val, $m))
								{
									$val = $m[1].'<img src="'.htmlspecialcharsbx($m[2]).'" style="max-width: '.$maxWidth.'px; max-height: '.$maxHeight.'px;">'.$m[3];
								}
								else
								{
									$val = '<img src="'.htmlspecialcharsbx($val).'" style="max-width: '.$maxWidth.'px; max-height: '.$maxHeight.'px;">';
								}
							}
							$arVals[$mkey] = $val;
						}
						$isHtml = true;
						$val = $arVals;
						if(count($val)==1) $val = current($val);
					}
					if($SETTINGS_DEFAULT['EXPORT_GROUP_INDENT']=='Y' && $key==0)
					{
						$fSettings['INDENT'] = $level;
					}
					if(is_array($val) && isset($val['TYPE']) && $val['TYPE']=='MULTICELL')
					{
						$newVal = '';
						foreach($val as $kVal=>$vVal)
						{
							if(!is_numeric($kVal) && $kVal=='TYPE') continue;
							$style = (is_array($vVal) && isset($vVal['STYLE']) ? CKDAExportUtils::GetCellStyleFormatted(array_merge($fSettings, $vVal['STYLE']), $SETTINGS_DEFAULT) : '');
							if(is_array($vVal) && isset($vVal['VALUE'])) $newVal .= '<tr><td '.$style.'>'.(string)$vVal['VALUE'].'</td></tr>';
							elseif(!is_array($vVal)) $newVal .= '<tr><td '.$style.'>'.(string)$vVal.'</td></tr>';
							else $newVal .= '<tr><td '.$style.'></td></tr>';
						}
						if(strlen($newVal) > 0) $newVal = '<table class="kda-ee-multicell">'.$newVal.'</table>';
						$val = $newVal;
						$isHtml = true;
					}
					echo '<td><div class="cell"><div class="cell_inner"'.($SETTINGS_DEFAULT['NOT_SHOW_PREVIEW_STYLES']=='Y' ? '' : ' '.CKDAExportUtils::GetCellStyleFormatted($fSettings, $SETTINGS_DEFAULT)).'>'.($isHtml ? nl2br($val) : nl2br(htmlspecialcharsbx($val))).'</div></div></td>';
				}
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '</div>';
		die();
	}
	
	if($ACTION == 'DO_EXPORT')
	{
		unset($EXTRASETTINGS);
		$oProfile = new CKDAExportProfile();
		$oProfile->ApplyExtra($EXTRASETTINGS, $PROFILE_ID);
		$params = array_merge($SETTINGS_DEFAULT, $SETTINGS);
		//$params = $SETTINGS_DEFAULT + $SETTINGS;
		$stepparams = $_POST['stepparams'];
		if(!is_array($stepparams)) $stepparams = array();
		$sess = $_SESSION;
		session_write_close();
		$_SESSION = $sess;
		$arResult = $moduleRunnerClass::ExportIblock($params, $EXTRASETTINGS, $stepparams, $PROFILE_ID);
		$APPLICATION->RestartBuffer();
		ob_end_clean();
		echo CUtil::PhpToJSObject($arResult);
		die();
	}
	
	/*Update profile*/
	if(strlen($PROFILE_ID) > 0 && $PROFILE_ID!=='new')
	{
		$oProfile->Update($PROFILE_ID, $SETTINGS_DEFAULT, $SETTINGS);
		if(is_array($EXTRASETTINGS)) $oProfile->UpdateExtra($PROFILE_ID, $EXTRASETTINGS);
	}
	/*/Update profile*/
	
	if ($STEP > 2)
	{
		/*$params = array_merge($SETTINGS_DEFAULT, $SETTINGS);
		$ie = new CKDAExportExcel($DATA_FILE_NAME, $params);
		$ie->Import();
		die();*/
	}
	//*****************************************************************//

}

/////////////////////////////////////////////////////////////////////
$APPLICATION->SetTitle(GetMessage("KDA_EE_PAGE_TITLE").$STEP);
require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/

if (!$moduleDemoExpiredFunc()) {
	$moduleShowDemoFunc();
}

$arSubMenu = array();
if($oProfile instanceof CKDAExportProfileDB)
{
	$arSubMenu[] = array(
		"TEXT"=>GetMessage("KDA_EE_MENU_PROFILE_LIST"),
		"TITLE"=>GetMessage("KDA_EE_MENU_PROFILE_LIST"),
		"LINK" => "/bitrix/admin/".$moduleFilePrefix."_profile_list.php?lang=".LANG,
	);
}
$arSubMenu[] = array(
	"TEXT"=>GetMessage("KDA_EE_SHOW_CRONTAB"),
	"TITLE"=>GetMessage("KDA_EE_SHOW_CRONTAB"),
	"ONCLICK" => "EProfile.ShowCron();",
);
$aMenu = array(
	array(
		"TEXT"=>GetMessage("KDA_EE_MENU_HELP"),
		"TITLE"=>GetMessage("KDA_EE_MENU_HELP"),
		"ONCLICK" => "EHelper.ShowHelp();",
		"ICON" => "",
	),
	array(
		"TEXT"=>GetMessage("KDA_EE_TOOLS_LIST"),
		"TITLE"=>GetMessage("KDA_EE_TOOLS_LIST"),
		"MENU" => $arSubMenu,
		"ICON" => "btn_green",
	)
);
$context = new CAdminContextMenu($aMenu);
$context->Show();


if ($STEP < 2)
{
	$oProfile = new CKDAExportProfile();
	$arProfiles = $oProfile->GetProcessedProfiles();
	if(!empty($arProfiles))
	{
		$message = '';
		foreach($arProfiles as $k=>$v)
		{
			$message .= '<div class="kda-proccess-item">'.GetMessage("KDA_EE_PROCESSED_PROFILE").': '.$v['name'].' ('.GetMessage("KDA_EE_PROCESSED_PERCENT_LOADED").' '.$v['percent'].'%). &nbsp; &nbsp; &nbsp; &nbsp; <a href="javascript:void(0)" onclick="EProfile.ContinueProccess(this, '.$v['key'].')">'.GetMessage("KDA_EE_PROCESSED_CONTINUE").'</a> &nbsp; <a href="javascript:void(0)" onclick="EProfile.RemoveProccess(this, '.$v['key'].')">'.GetMessage("KDA_EE_PROCESSED_DELETE").'</a></div>';
		}
		echo '<div style="display: none;">';
		CAdminMessage::ShowMessage(array(
			'TYPE' => 'error',
			'MESSAGE' => GetMessage("KDA_EE_PROCESSED_TITLE"),
			'DETAILS' => $message,
			'HTML' => true
		));
		echo '</div>';
	}
}

CAdminMessage::ShowMessage($strError);
?>

<form method="POST" action="<?echo $sDocPath ?>?lang=<?echo LANG ?>" ENCTYPE="multipart/form-data" name="dataload" id="dataload" class="kda-ee-s1-form">

<?
$arProfile = (strlen($PROFILE_ID) > 0 ? $oProfile->GetFieldsByID($PROFILE_ID) : array());
$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => GetMessage("KDA_EE_TAB1") ,
		"ICON" => "iblock",
		"TITLE" => GetMessage("KDA_EE_TAB1_ALT"),
	) ,
	array(
		"DIV" => "edit2",
		"TAB" => GetMessage("KDA_EE_TAB2") ,
		"ICON" => "iblock",
		"TITLE" => sprintf(GetMessage("KDA_EE_TAB2_ALT"), (isset($arProfile['NAME']) ? $arProfile['NAME'] : '')),
	) ,
	array(
		"DIV" => "edit3",
		"TAB" => GetMessage("KDA_EE_TAB3") ,
		"ICON" => "iblock",
		"TITLE" => sprintf(GetMessage("KDA_EE_TAB3_ALT"), (isset($arProfile['NAME']) ? $arProfile['NAME'] : '')),
	) ,
);

$tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
$tabControl->Begin();
?>

<?$tabControl->BeginNextTab();
if ($STEP == 1)
{
	$oProfile = new CKDAExportProfile();
?>

	<tr class="heading">
		<td colspan="2" class="kda-ee-profile-header">
			<div>
				<?echo GetMessage("KDA_EE_PROFILE_HEADER"); ?>
				<a href="javascript:void(0)" onclick="EHelper.ShowHelp();" title="<?echo GetMessage("KDA_EE_MENU_HELP"); ?>" class="kda-ee-help-link"></a>
			</div>
		</td>
	</tr>

	<tr>
		<td><?echo GetMessage("KDA_EE_PROFILE"); ?>:</td>
		<td>
			<?$oProfile->ShowProfileList('PROFILE_ID');?>
			
			<?if(strlen($PROFILE_ID) > 0 && $PROFILE_ID!='new'){?>
				<span class="kda-ee-edit-btns">
					<a href="javascript:void(0)" class="adm-table-btn-edit" onclick="EProfile.ShowRename();" title="<?echo GetMessage("KDA_EE_RENAME_PROFILE");?>" id="action_edit_button"></a>
					<a href="javascript:void(0);" class="adm-table-btn-copy" onclick="EProfile.Copy();" title="<?echo GetMessage("KDA_EE_COPY_PROFILE");?>" id="action_copy_button"></a>
					<a href="javascript:void(0);" class="adm-table-btn-delete" onclick="if(confirm('<?echo GetMessage("KDA_EE_DELETE_PROFILE_CONFIRM");?>')){EProfile.Delete();}" title="<?echo GetMessage("KDA_EE_DELETE_PROFILE");?>" id="action_delete_button"></a>
				</span>
			<?}?>
		</td>
	</tr>
	
	<tr id="new_profile_name">
		<td><?echo GetMessage("KDA_EE_NEW_PROFILE_NAME"); ?>:</td>
		<td>
			<input type="text" name="NEW_PROFILE_NAME" value="<?echo htmlspecialcharsbx($NEW_PROFILE_NAME)?>">
		</td>
	</tr>

	<?
	if(strlen($PROFILE_ID) > 0)
	{
	?>
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("KDA_EE_DEFAULT_SETTINGS"); ?></td>
		</tr>
		
		<tr>
			<?
			//$xlsxShow = (bool)(class_exists('XMLWriter'));
			$xlsxShow = true;
			?>
			<td width="40%"><?echo GetMessage("KDA_EE_FILE_EXT"); ?></td>
			<td width="60%" class="kda-ie-file-choose">
				<select name="SETTINGS_DEFAULT[FILE_EXTENSION]" id="kda-ee-file-extension">
					<?if($xlsxShow){?>
						<option value="xlsx" <?if($SETTINGS_DEFAULT['FILE_EXTENSION']=='xlsx'){echo 'selected';}?>>.XLSX</option>
					<?}?>
					<option value="xls" <?if($SETTINGS_DEFAULT['FILE_EXTENSION']=='xls'){echo 'selected';}?>>.XLS</option>
					<option value="csv" <?if($SETTINGS_DEFAULT['FILE_EXTENSION']=='csv'){echo 'selected';}?>>.CSV</option>
					<option value="dbf" <?if($SETTINGS_DEFAULT['FILE_EXTENSION']=='dbf'){echo 'selected';}?>>.DBF</option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_FILE_PATH"); ?>:  </td>
			<td>
				<?
				$path = $SETTINGS_DEFAULT['FILE_PATH'];
				if(!$path)
				{
					$defaultExt = ($xlsxShow ? 'xlsx' : 'xls');
					$ext = ($SETTINGS_DEFAULT['FILE_EXTENSION'] ? $SETTINGS_DEFAULT['FILE_EXTENSION'] : $defaultExt);
					while(($path = '/upload/export_'.mt_rand().'.'.$ext) && file_exists($_SERVER['DOCUMENT_ROOT'].$path)){}
				}
				?>
				<input type="text" name="SETTINGS_DEFAULT[FILE_PATH]" id="kda-ee-file-path" value="<?echo htmlspecialcharsbx($path); ?>" size="55">
			</td>
		</tr>

		<tr>
			<td><?echo GetMessage("KDA_EE_INFOBLOCK"); ?></td>
			<td>
				<?echo GetIBlockDropDownListEx($SETTINGS_DEFAULT['IBLOCK_ID'], 'SETTINGS_DEFAULT[IBLOCK_TYPE_ID]', 'SETTINGS_DEFAULT[IBLOCK_ID]', array('MIN_PERMISSION'=>'R'), '', '', 'class="adm-detail-iblock-types"', 'class="adm-detail-iblock-list"'); ?>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_ELEMENT_MULTIPLE_SEPARATOR"); ?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[ELEMENT_MULTIPLE_SEPARATOR]" size="3" value="<?echo ($SETTINGS_DEFAULT['ELEMENT_MULTIPLE_SEPARATOR'] ? htmlspecialcharsbx($SETTINGS_DEFAULT['ELEMENT_MULTIPLE_SEPARATOR']) : ';'); ?>">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_MAX_SECTION_LEVEL"); ?>:  <span id="hint_MAX_SECTION_LEVEL"></span><script>BX.hint_replace(BX('hint_MAX_SECTION_LEVEL'), '<?echo GetMessage("KDA_EE_MAX_SECTION_LEVEL_HINT"); ?>');</script></td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[MAX_SECTION_LEVEL]" size="3" value="<?echo (strlen($SETTINGS_DEFAULT['MAX_SECTION_LEVEL']) > 0 ? htmlspecialcharsbx($SETTINGS_DEFAULT['MAX_SECTION_LEVEL']) : '5'); ?>" maxlength="3">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_EXPORT_FIELD_CODES"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_FIELD_CODES]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_FIELD_CODES']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr class="heading" id="csv_settings_block">
			<td colspan="2"><?echo GetMessage("KDA_EE_SETTINGS_CSV"); ?></td>
		</tr>
		
		<?
		$csvYandex = false;
		if($SETTINGS_DEFAULT['CSV_YANDEX']=='Y') $csvYandex = true;
		?>
		<tr>
			<td><?echo GetMessage("KDA_EE_CSV_SEPARATOR"); ?>:</td>
			<td>
				<select name="SETTINGS_DEFAULT[CSV_SEPARATOR]">
					<option value=";" <?if($SETTINGS_DEFAULT['CSV_SEPARATOR']==';'){echo 'selected';}?>><?echo GetMessage("KDA_EE_CSV_SEPARATOR_SEMICOLON"); ?></option>
					<option value="," <?if($SETTINGS_DEFAULT['CSV_SEPARATOR']==','){echo 'selected';}?>><?echo GetMessage("KDA_EE_CSV_SEPARATOR_COMMA"); ?></option>
					<option value="\t" <?if($SETTINGS_DEFAULT['CSV_SEPARATOR']=='\t'){echo 'selected';}?>><?echo GetMessage("KDA_EE_CSV_SEPARATOR_TAB"); ?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_CSV_ENCLOSURE"); ?>:</td>
			<td>
				<select name="SETTINGS_DEFAULT[CSV_ENCLOSURE]" <?if($csvYandex){echo 'disabled';}?>>
					<option value='"' <?if($SETTINGS_DEFAULT['CSV_ENCLOSURE']=='"'){echo 'selected';}?>><?echo GetMessage("KDA_EE_CSV_ENCLOSURE_DOUBLE_QUOTE"); ?></option>
					<option value="'" <?if($SETTINGS_DEFAULT['CSV_ENCLOSURE']=="'"){echo 'selected';}?>><?echo GetMessage("KDA_EE_CSV_ENCLOSURE_SINGLE_QUOTE"); ?></option>
					<option value="" <?if($SETTINGS_DEFAULT['CSV_ENCLOSURE']===""){echo 'selected';}?>><?echo GetMessage("KDA_EE_CSV_ENCLOSURE_EMPTY"); ?></option>
				</select>
				<?if($csvYandex){?><input type="hidden" name="SETTINGS_DEFAULT[CSV_ENCLOSURE]" value='"'><?}?>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_CSV_ENCODING"); ?>:</td>
			<td>
				<select name="SETTINGS_DEFAULT[CSV_ENCODING]" <?if($csvYandex){echo 'disabled';}?>>
					<option value="UTF-8" <?if($SETTINGS_DEFAULT['CSV_ENCODING']=='UTF-8'){echo 'selected';}?>>UTF-8</option>
					<option value="CP1251" <?if($SETTINGS_DEFAULT['CSV_ENCODING']=='CP1251'){echo 'selected';}?>>CP1251</option>
				</select>
				<?if($csvYandex){?><input type="hidden" name="SETTINGS_DEFAULT[CSV_ENCODING]" value="UTF-8"><?}?>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_CSV_YANDEX"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[CSV_YANDEX]" value="Y" <?if($SETTINGS_DEFAULT['CSV_YANDEX']=='Y'){echo 'checked';}?> onchange="EList.CsvAdaptYandex(this);">
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("KDA_EE_SETTINGS_SECTIONS"); ?></td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_EXPORT_ELEMENT_ONE_SECTION"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_ELEMENT_ONE_SECTION]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_ELEMENT_ONE_SECTION']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_EXPORT_SECTIONS_ONE_CELL"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_SECTIONS_ONE_CELL]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_SECTIONS_ONE_CELL']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_EXPORT_SEP_SECTIONS"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_SEP_SECTIONS]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_SEP_SECTIONS']=='Y'){echo 'checked';}?> onchange="EProfile.ToggleSectionsSettings(this);">
			</td>
		</tr>
		
		<tr <?if($SETTINGS_DEFAULT['EXPORT_SEP_SECTIONS']!='Y'){echo 'style="display: none;"';}?>>
			<td><?echo GetMessage("KDA_EE_EXPORT_SECTION_PATH"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_SECTION_PATH]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_SECTION_PATH']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr <?if($SETTINGS_DEFAULT['EXPORT_SEP_SECTIONS']!='Y'){echo 'style="display: none;"';}?>>
			<td><?echo GetMessage("KDA_EE_EXPORT_GROUP_PRODUCTS"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_GROUP_PRODUCTS]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_GROUP_PRODUCTS']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr <?if($SETTINGS_DEFAULT['EXPORT_SEP_SECTIONS']!='Y'){echo 'style="display: none;"';}?>>
			<td><?echo GetMessage("KDA_EE_EXPORT_GROUP_SUBSECTIONS"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_GROUP_SUBSECTIONS]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_GROUP_SUBSECTIONS']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr <?if($SETTINGS_DEFAULT['EXPORT_SEP_SECTIONS']!='Y'){echo 'style="display: none;"';}?>>
			<td><?echo GetMessage("KDA_EE_EXPORT_GROUP_OPEN"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_GROUP_OPEN]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_GROUP_OPEN']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr <?if($SETTINGS_DEFAULT['EXPORT_SEP_SECTIONS']!='Y'){echo 'style="display: none;"';}?>>
			<td><?echo GetMessage("KDA_EE_EXPORT_GROUP_INDENT"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_GROUP_INDENT]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_GROUP_INDENT']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr <?if($SETTINGS_DEFAULT['EXPORT_SEP_SECTIONS']!='Y'){echo 'style="display: none;"';}?>>
			<td><?echo GetMessage("KDA_EE_EXPORT_SEP_SECTIONS_SORT"); ?>:</td>
			<td>
				<select name="SETTINGS_DEFAULT[EXPORT_SEP_SECTIONS_SORT]">
					<option value=""><?echo GetMessage("KDA_EE_EXPORT_SEP_SECTIONS_SORT_DEFAULT");?></option>
					<option value="NAME"<?if($SETTINGS_DEFAULT['EXPORT_SEP_SECTIONS_SORT']=='NAME'){echo 'selected';}?>><?echo GetMessage("KDA_EE_EXPORT_SEP_SECTIONS_SORT_NAME");?></option>
				</select>
			</td>
		</tr>
		
		<?
		$OFFERS_IBLOCK_ID = CKDAExportUtils::GetOfferIblock($SETTINGS_DEFAULT['IBLOCK_ID']);
		?>
		<tr class="heading kda-sku-block" <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> >
			<td colspan="2"><?echo GetMessage("KDA_EE_SETTINGS_OFFERS"); ?> <a href="javascript:void(0)" onclick="EProfile.ToggleAdditionalSettings(this)" class="kda-head-more show"><?echo GetMessage("KDA_EE_SETTINGS_ADDITONAL_SHOW_HIDE"); ?></a></td>
		</tr>
		
		<tr class="kda-sku-block" <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> >
			<td><?echo GetMessage("KDA_EE_EXPORT_ONE_OFFER_MIN_PRICE"); ?>:</td>
			<td>
				<table cellspacing="0"><tr>
					<td style="padding-left: 0px;">
						<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_ONE_OFFER_MIN_PRICE]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_ONE_OFFER_MIN_PRICE']=='Y'){echo 'checked';}?> onclick="if(this.checked){$('#kda_ee_offerminpricetype').show();}else{$('#kda_ee_offerminpricetype').hide();}">
					</td>
					<td>&nbsp; &nbsp;</td>
					<td id="kda_ee_offerminpricetype" <?if($SETTINGS_DEFAULT['EXPORT_ONE_OFFER_MIN_PRICE']!='Y'){echo 'style="display: none;"';}?>>
					<?
					if($bCatalog)
					{
						$fl = new CKDAEEFieldList($SETTINGS_DEFAULT);
						$arCatalogFields = $fl->GetCatalogFields($OFFERS_IBLOCK_ID);
						?>
						<?echo GetMessage("KDA_EE_EXPORT_ONE_OFFER_MIN_PRICE_TYPE"); ?><br>
						<select name="SETTINGS_DEFAULT[EXPORT_ONE_OFFER_MIN_PRICE_TYPE]">
							<?
							foreach($arCatalogFields as $arCatalogField)
							{
								$priceCode = false;
								if($arCatalogField['value']=='ICAT_PURCHASING_PRICE') $priceCode = 'CATALOG_PURCHASING_PRICE';
								elseif(preg_match('/^ICAT_PRICE(\d+)_PRICE$/', $arCatalogField['value'], $m)) $priceCode = 'CATALOG_PRICE_'.$m[1];
								if($priceCode===false) continue;
								?><option value="<?echo $priceCode;?>"<?if($SETTINGS_DEFAULT['EXPORT_ONE_OFFER_MIN_PRICE_TYPE']==$priceCode){echo ' selected';}?>><?echo $arCatalogField['name'];?></option><?
							}
							?>
						</select>
						<?
					}
					?>
					</td>
				</tr></table>
			</td>
		</tr>
		
		<tr class="kda-sku-block" <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> >
			<td><?echo GetMessage("KDA_EE_EXPORT_OFFERS_JOIN"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_OFFERS_JOIN]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_OFFERS_JOIN']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr class="kda-sku-block" <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> >
			<td><?echo GetMessage("KDA_EE_EXPORT_PRODUCTS_JOIN"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_PROPUCTS_JOIN]" value="Y" <?if($SETTINGS_DEFAULT['EXPORT_PROPUCTS_JOIN']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("KDA_EE_SETTINGS_DISPLAY"); ?> <a href="javascript:void(0)" onclick="EProfile.ToggleAdditionalSettings(this)" class="kda-head-more show"><?echo GetMessage("KDA_EE_SETTINGS_ADDITONAL_SHOW_HIDE"); ?></a></td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_LOCK_HEADERS"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[DISPLAY_LOCK_HEADERS]" value="Y" <?if($SETTINGS_DEFAULT['DISPLAY_LOCK_HEADERS']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_EXPORT_ROW_AUTO_HEIGHT"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ROW_AUTO_HEIGHT]" value="Y" <?if($SETTINGS_DEFAULT['ROW_AUTO_HEIGHT']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_EXPORT_ROW_MIN_HEIGHT"); ?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[ROW_MIN_HEIGHT]" value="<?echo htmlspecialcharsbx($SETTINGS_DEFAULT['ROW_MIN_HEIGHT']);?>">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_TEXT_ALIGN"); ?>:</td>
			<td>
				<select name="SETTINGS_DEFAULT[DISPLAY_TEXT_ALIGN]">
					<option value="LEFT" <?if($SETTINGS_DEFAULT['DISPLAY_TEXT_ALIGN']=='LEFT'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_TEXT_ALIGN_LEFT"); ?></option>
					<option value="CENTER" <?if($SETTINGS_DEFAULT['DISPLAY_TEXT_ALIGN']=='CENTER'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_TEXT_ALIGN_CENTER"); ?></option>
					<option value="RIGHT" <?if($SETTINGS_DEFAULT['DISPLAY_TEXT_ALIGN']=='RIGHT'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_TEXT_ALIGN_RIGHT"); ?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN"); ?>:</td>
			<td>
				<select name="SETTINGS_DEFAULT[DISPLAY_VERTICAL_ALIGN]">
					<option value="TOP" <?if($SETTINGS_DEFAULT['DISPLAY_VERTICAL_ALIGN']=='TOP'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN_TOP"); ?></option>
					<option value="CENTER" <?if($SETTINGS_DEFAULT['DISPLAY_VERTICAL_ALIGN']=='CENTER'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN_CENTER"); ?></option>
					<option value="BOTTOM" <?if($SETTINGS_DEFAULT['DISPLAY_VERTICAL_ALIGN']=='BOTTOM'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN_BOTTOM"); ?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_SETTING_FONT_FAMILY");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[FONT_FAMILY]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['FONT_FAMILY'])?>" placeholder="Calibri">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_SETTING_FONT_SIZE");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[FONT_SIZE]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['FONT_SIZE'])?>" placeholder="11">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_SETTING_FONT_COLOR");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[FONT_COLOR]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['FONT_COLOR'])?>" placeholder="#000000">
			</td>
		</tr>
		
		<?/*?><tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_SETTING_BACKGROUND_COLOR");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[BACKGROUND_COLOR]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['BACKGROUND_COLOR'])?>" placeholder="#ffffff">
			</td>
		</tr><?*/?>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_SETTING_FONT_STYLE_BOLD");?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[STYLE_BOLD]" value="Y" <?=htmlspecialcharsbx($SETTINGS_DEFAULT['STYLE_BOLD']=='Y' ? 'checked' : '')?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_SETTING_FONT_STYLE_ITALIC");?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[STYLE_ITALIC]" value="Y" <?=htmlspecialcharsbx($SETTINGS_DEFAULT['STYLE_ITALIC']=='Y' ? 'checked' : '')?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_SETTING_BORDER_STYLE");?>:</td>
			<td>
				<select name="SETTINGS_DEFAULT[BORDER_STYLE]">
					<option value="NONE" <?if($SETTINGS_DEFAULT['BORDER_STYLE']=='NONE'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_SETTING_BORDER_STYLE_NONE"); ?></option>
					<option value="THIN" <?if($SETTINGS_DEFAULT['BORDER_STYLE']=='THIN'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_SETTING_BORDER_STYLE_THIN"); ?></option>
					<option value="MEDIUM" <?if($SETTINGS_DEFAULT['BORDER_STYLE']=='MEDIUM'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_SETTING_BORDER_STYLE_MEDIUM"); ?></option>
					<option value="THICK" <?if($SETTINGS_DEFAULT['BORDER_STYLE']=='THICK'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_SETTING_BORDER_STYLE_THICK"); ?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DISPLAY_SETTING_BORDER_COLOR");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[BORDER_COLOR]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['BORDER_COLOR'])?>" placeholder="#000000">
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("KDA_EE_DOCUMENT_PARAMS"); ?> <a href="javascript:void(0)" onclick="EProfile.ToggleAdditionalSettings(this)" class="kda-head-more show"><?echo GetMessage("KDA_EE_SETTINGS_ADDITONAL_SHOW_HIDE"); ?></a></td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DOCPARAM_TITLE");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[DOCPARAM_TITLE]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['DOCPARAM_TITLE'])?>">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DOCPARAM_SUBJECT");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[DOCPARAM_SUBJECT]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['DOCPARAM_SUBJECT'])?>">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DOCPARAM_AUTHOR");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[DOCPARAM_AUTHOR]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['DOCPARAM_AUTHOR'])?>">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DOCPARAM_ORG");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[DOCPARAM_ORG]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['DOCPARAM_ORG'])?>">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DOCPARAM_CATEGORY");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[DOCPARAM_CATEGORY]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['DOCPARAM_CATEGORY'])?>">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DOCPARAM_KEYWORDS");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[DOCPARAM_KEYWORDS]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['DOCPARAM_KEYWORDS'])?>">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_DOCPARAM_DESCRIPTION");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[DOCPARAM_DESCRIPTION]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['DOCPARAM_DESCRIPTION'])?>">
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("KDA_EE_EXT_SERVICES"); ?> <a href="javascript:void(0)" onclick="EProfile.ToggleAdditionalSettings(this)" class="kda-head-more show"><?echo GetMessage("KDA_EE_SETTINGS_ADDITONAL_SHOW_HIDE"); ?></a></td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_EXT_SERVICES_BX24");?>:</td>
			<td>
				<table cellspacing="0"><tr>
				<td style="padding-left: 0px;"><input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_TO_BX24]" value="Y" <?=htmlspecialcharsbx($SETTINGS_DEFAULT['EXPORT_TO_BX24']=='Y' ? 'checked' : '')?> onchange="if(this.checked){$('#bx24p').show();}else{$('#bx24p').hide();}"></td>
				<td>&nbsp; &nbsp;</td>
				<td id="bx24p"<?if($SETTINGS_DEFAULT['EXPORT_TO_BX24']!='Y'){echo ' style="display: none;"';}?>>
					<div id="bx24_help" style="display: none;">
						<?$imgStr = '<img src="'.$moduleImagePath.'%s">';?>
						<?echo sprintf(GetMessage("KDA_EE_EXT_SERVICES_BX24_REST_URL_HELP"), sprintf($imgStr, 'bx24_1.jpg'), sprintf($imgStr, 'bx24_2.jpg'), sprintf($imgStr, 'bx24_3.jpg'));?>
					</div>
					<div>
						<?echo GetMessage("KDA_EE_EXT_SERVICES_BX24_REST_URL");?>:
						<div><input type="text" name="SETTINGS_DEFAULT[BX24_REST_URL]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['BX24_REST_URL'])?>" id="bx24_rest_url" size="50"></div>
						<?echo sprintf(GetMessage("KDA_EE_EXT_SERVICES_BX24_REST_URL_EXAMPLE"), 'https://b24-b41cc2.bitrix24.ru/rest/1/e9pzrkwg0aijfh9m/');?> &nbsp; (<a href="javascript:void(0)" id="bx24_help_link"><?echo GetMessage("KDA_EE_EXT_SERVICES_BX24_REST_URL_HOW_GET");?></a>)
					</div>
					<br>
					<div>
						<?echo GetMessage("KDA_EE_EXT_SERVICES_BX24_FOLDER_ID");?>:
						<div>
							<input type="hidden" name="SETTINGS_DEFAULT[BX24_FOLDER_ID]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['BX24_FOLDER_ID'])?>" id="bx24_folder_id" size="50">
							<input type="hidden" name="SETTINGS_DEFAULT[BX24_FOLDER_PATH]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['BX24_FOLDER_PATH'])?>" id="bx24_folder_path">
						</div>
						<div id="bx24_folder_struct" class="kda-ee-bx24-struct"><select></select></div>
					</div>
					<br>
					<div>
						<?echo GetMessage("KDA_EE_EXT_SERVICES_BX24_MODE");?>:
						<div>
							<input type="radio" name="SETTINGS_DEFAULT[BX24_MODE]" value="UPDATE"<?if($SETTINGS_DEFAULT['BX24_MODE']!='REPLACE'){echo ' checked';}?> id="bx24_mode_update"> <label for="bx24_mode_update"><?echo GetMessage("KDA_EE_EXT_SERVICES_BX24_MODE_UPDATE");?></label>
							<br>
							<input type="radio" name="SETTINGS_DEFAULT[BX24_MODE]" value="REPLACE"<?if($SETTINGS_DEFAULT['BX24_MODE']=='REPLACE'){echo ' checked';}?> id="bx24_mode_replace"> <label for="bx24_mode_replace"><?echo GetMessage("KDA_EE_EXT_SERVICES_BX24_MODE_REPLACE");?></label>
						</div>
					</div>
				</td>
				</tr></table>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_EXT_SERVICES_YADISK");?>:</td>
			<td>
				<table cellspacing="0"><tr>
				<td style="padding-left: 0px;"><input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_TO_YADISK]" value="Y" <?=htmlspecialcharsbx($SETTINGS_DEFAULT['EXPORT_TO_YADISK']=='Y' ? 'checked' : '')?> onchange="if(this.checked){$('#yadiskp').show();}else{$('#yadiskp').hide();}"></td>
				<td>&nbsp; &nbsp;</td>
				<td id="yadiskp"<?if($SETTINGS_DEFAULT['EXPORT_TO_YADISK']!='Y'){echo ' style="display: none;"';}?>>
					<div>
						<?echo GetMessage("KDA_EE_EXT_SERVICES_YADISK_TOKEN");?>:
						<div><input type="text" name="SETTINGS_DEFAULT[YADISK_TOKEN]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['YADISK_TOKEN'])?>" size="50"> &nbsp; <a href="https://oauth.yandex.ru/authorize?response_type=token&client_id=9169c49a80754cf3a89e11c274870299" target="_blank"><?echo GetMessage("KDA_EE_EXT_SERVICES_YADISK_TOKEN_GET");?></a></div>
					</div>
					<br>
					<div>
						<?echo GetMessage("KDA_EE_EXT_SERVICES_YADISK_PATH");?>:
						<div><input type="text" name="SETTINGS_DEFAULT[YADISK_PATH]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['YADISK_PATH'])?>" size="50"></div>
						<?echo GetMessage("KDA_EE_EXT_SERVICES_YADISK_PATH_EXAMPLE");?>
					</div>
				</td>
				</tr></table>
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("KDA_EE_OTHER_SETTINGS"); ?> <a href="javascript:void(0)" onclick="EProfile.ToggleAdditionalSettings(this)" class="kda-head-more show"><?echo GetMessage("KDA_EE_SETTINGS_ADDITONAL_SHOW_HIDE"); ?></a></td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_OTHER_EXPORT_FILES_IN_ARCHIVE");?>:</td>
			<td>
				<table cellspacing="0"><tr>
				<td style="padding-left: 0px;">
					<input type="checkbox" name="SETTINGS_DEFAULT[EXPORT_FILES_IN_ARCHIVE]" value="Y" <?=htmlspecialcharsbx($SETTINGS_DEFAULT['EXPORT_FILES_IN_ARCHIVE']=='Y' ? 'checked' : '')?> onchange="if(this.checked){$('#EXPORT_FILES_IN_ARCHIVE_wrap').show();}else{$('#EXPORT_FILES_IN_ARCHIVE_wrap').hide();}">
				</td>
				<td>&nbsp; &nbsp;</td>
				<td>
					<div id="EXPORT_FILES_IN_ARCHIVE_wrap" <?if($SETTINGS_DEFAULT['EXPORT_FILES_IN_ARCHIVE']!='Y'){echo 'style="display: none;"';}?>>
						<?echo GetMessage("KDA_EE_OTHER_FILES_ARCHIVE_PATH");?><br>
						<?
						$archivePath = $SETTINGS_DEFAULT['FILES_ARCHIVE_PATH'];
						if(!$archivePath)
						{
							if($path)
							{
								$archivePath = '/upload/'.preg_replace('/\.[^\.]*$/', '', basename($path)).'.zip';
							}
							else
							{
								while(($archivePath = '/upload/export_'.mt_rand().'.zip') && file_exists($_SERVER['DOCUMENT_ROOT'].$archivePath)){}
							}
						}
						?>
						<input type="text" name="SETTINGS_DEFAULT[FILES_ARCHIVE_PATH]" id="kda-ee-file-path" value="<?echo htmlspecialcharsbx($archivePath); ?>" size="55">
					</div>
				</td>
				</tr></table>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_OTHER_SETTINGS_MERGE_SHEETS");?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[MERGE_SHEETS]" value="Y" <?=htmlspecialcharsbx($SETTINGS_DEFAULT['MERGE_SHEETS']=='Y' ? 'checked' : '')?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_OTHER_SETTINGS_NOT_SHOW_PREVIEW_STYLES");?>: <span id="hint_NOT_SHOW_PREVIEW_STYLES"></span><script>BX.hint_replace(BX('hint_NOT_SHOW_PREVIEW_STYLES'), '<?echo GetMessage("KDA_EE_OTHER_SETTINGS_NOT_SHOW_PREVIEW_STYLES_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[NOT_SHOW_PREVIEW_STYLES]" value="Y" <?=htmlspecialcharsbx($SETTINGS_DEFAULT['NOT_SHOW_PREVIEW_STYLES']=='Y' ? 'checked' : '')?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("KDA_EE_OTHER_SETTINGS_COUNT_SHOW_LINES");?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[COUNT_SHOW_LINES]" value="<?=htmlspecialcharsbx($SETTINGS_DEFAULT['COUNT_SHOW_LINES'])?>" placeholder="15">
			</td>
		</tr>
	<?
	}
}
$tabControl->EndTab();
?>

<?$tabControl->BeginNextTab();
if ($STEP == 2)
{
?>
	
	<tr>
		<td colspan="2" id="kda-ee-sheet-list">
			<?
			$arKeys = array(0);
			if(is_array($SETTINGS['LIST_NAME']) && count($SETTINGS['LIST_NAME']) > 0)
			{
				$arKeys = array_keys($SETTINGS['LIST_NAME']);
			}
			
			$ind = 0;
			foreach($arKeys as $list)
			{
				?>
				<div class="kda-ee-sheet-wrap<?if($ind > 0){echo ' withmargin';}?>">
					<?
						/*echo '<div id="tree"></div>';
						$obCond = new CKDAExportCondTree();
						$boolCond = $obCond->Init( BT_COND_MODE_DEFAULT, BT_COND_BUILD_CATALOG, array(
								'FORM_NAME' => 'dataload',
								'CONT_ID' => 'tree',
								'JS_NAME' => 'JSCond'
						));
						if ($boolCond)
						{
							$obCond->Show(array(), 4);
						}*/

						$filterId = 'kda_exportexcel_'.$PROFILE_ID.'_'.$list;
						CKDAExportUtils::ShowFilter($filterId, $list, $SETTINGS, $SETTINGS_DEFAULT);
						$showCodes = ($SETTINGS_DEFAULT['EXPORT_FIELD_CODES']=='Y' ? 1 : 0);
					?>
					<div class="kda-ee-sheet" 
						id="kda-ee-sheet-<?echo $list;?>" 
						data-sheet-index="<?echo $list;?>" 
						data-show-field-codes="<?echo $showCodes;?>">
					</div>
					<div class="kda-ee-new-list-wrap" style="display: none;">
						<input type="button" value="<?echo GetMessage("KDA_EE_ADD_NEW_LIST"); ?>" title="<?echo GetMessage("KDA_EE_ADD_NEW_LIST_HINT");?>">
					</div>
				</div>
				<?
				$ind++;
			}
			?>
			<?/*?><div class="kda-ee-new-list-wrap">
				<input type="button" value="<?echo GetMessage("KDA_EE_ADD_NEW_LIST"); ?>" onclick="EList.AddNewList(this);">
			</div><?*/?>
		</td>
	</tr>
	
	<?
}
$tabControl->EndTab();
?>


<?$tabControl->BeginNextTab();
if ($STEP == 3)
{
?>
	<tr>
		<td id="resblock" class="kda-ee-result">
		 <table width="100%"><tr><td width="50%">
			<div id="progressbar"><span class="pline"></span><span class="presult load"><b>0%</b><span 
				data-prefix="<?echo GetMessage("KDA_EE_READ_LINES"); ?>" 
				data-export="<?echo GetMessage("KDA_EE_STATUS_EXPORT"); ?>" 
			><?echo GetMessage("KDA_EE_EXPORT_INIT"); ?></span></span></div>

			<div id="block_error_import" style="display: none;">
				<?echo CAdminMessage::ShowMessage(array(
					"TYPE" => "ERROR",
					"MESSAGE" => GetMessage("KDA_EE_IMPORT_ERROR_CONNECT"),
					/*"DETAILS" => '<div><a href="javascript:void(0)" onclick="EProfile.ContinueProccess(this, '.$PROFILE_ID.');">'.GetMessage("KDA_EE_PROCESSED_CONTINUE").'</a><br><br>'.sprintf(GetMessage("KDA_EE_IMPORT_ERROR_CONNECT_COMMENT"), '/bitrix/admin/settings.php?lang=ru&mid='.$moduleId.'&mid_menu=1').'</div>',*/
					"DETAILS" => '<div>'.(COption::GetOptionString($moduleId, 'AUTO_CONTINUE_EXPORT', 'N')=='Y' ? sprintf(GetMessage("KDA_EE_EXPORT_AUTO_CONTINUE"), '<span id="kda_ee_auto_continue_time"></span>').'<br>' : '').'<a href="javascript:void(0)" onclick="EProfile.ContinueProccess(this, '.$PROFILE_ID.');">'.GetMessage("KDA_EE_PROCESSED_CONTINUE").'</a></div>',
					"HTML" => true,
				))?>
			</div>
			
			<div id="block_error" style="display: none;">
				<?echo CAdminMessage::ShowMessage(array(
					"TYPE" => "ERROR",
					"MESSAGE" => GetMessage("KDA_EE_IMPORT_ERROR"),
					"DETAILS" => '<div id="res_error"></div>',
					"HTML" => true,
				))?>
			</div>
		 </td><td>
			<div class="detail_status">
				<?
				$outputFile = CKDAExportUtils::PrepareExportFileName($SETTINGS_DEFAULT['FILE_PATH']);
				if(strpos($outputFile, $_SERVER['DOCUMENT_ROOT'])===0)
				{
					$outputFile = substr($outputFile, strlen($_SERVER['DOCUMENT_ROOT']));
				}
				echo CAdminMessage::ShowMessage(array(
					"TYPE" => "PROGRESS",
					"MESSAGE" => '<!--<div id="res_continue">'.GetMessage("KDA_EE_AUTO_REFRESH_CONTINUE").'</div><div id="res_finish" style="display: none;">'.GetMessage("KDA_EE_SUCCESS").'</div>-->',
					"DETAILS" =>

					GetMessage("KDA_EE_SU_ALL").' <b id="total_read_line">0</b><br>'.
					/*.GetMessage("KDA_EE_SU_ELEMENT_ADDED").' <b id="element_added_line">0</b><br>'.
					(!empty($SETTINGS_DEFAULT['ELEMENT_UID_SKU']) ? (GetMessage("KDA_EE_SU_SKU_ADDED").' <b id="sku_added_line">0</b><br>') : '').
					GetMessage("KDA_EE_SU_SECTION_ADDED").' <b id="section_added_line">0</b><br>'.*/
					' <span id="kda_ee_ready_file" style="visibility: hidden;"><br>'.GetMessage("KDA_EE_READY_FILE_LINK").' <br><a href="'.htmlspecialcharsbx($outputFile).'?hash='.md5(mt_rand()).'" target="_blank">'.$outputFile.'</a> <br><br><a href="/bitrix/admin/fileman_file_download.php?path='.htmlspecialcharsbx($outputFile).'&lang='.LANGUAGE_ID.'&hash='.md5(mt_rand()).'">'.GetMessage("KDA_EE_DOWNLOAD_FILE").'</a></span><br>',
					"HTML" => true,
				));
				?>
			</div>
		 </td></tr></table>
		</td>
	</tr>
<?
}
$tabControl->EndTab();
?>

<?$tabControl->Buttons();
?>


<?echo bitrix_sessid_post(); ?>
<?
if($STEP > 1)
{
	if(strlen($PROFILE_ID) > 0)
	{
		?><input type="hidden" name="PROFILE_ID" value="<?echo htmlspecialcharsbx($PROFILE_ID) ?>"><?
	}
	else
	{
		foreach($SETTINGS_DEFAULT as $k=>$v)
		{
			?><input type="hidden" name="SETTINGS_DEFAULT[<?echo $k?>]" value="<?echo htmlspecialcharsbx($v) ?>"><?
		}
	}
}
?>


<?
if($STEP == 2){ ?>
<input type="submit" name="backButton" value="&lt;&lt; <?echo GetMessage("KDA_EE_BACK"); ?>">
<input type="submit" name="saveConfigButton" value="<?echo GetMessage("KDA_EE_SAVE_CONFIGURATION"); ?>" style="float: right;">
<?
}

if($STEP < 3)
{
?>
	<input type="hidden" name="STEP" value="<?echo $STEP + 1; ?>">
	<input type="submit" value="<?echo ($STEP == 2) ? GetMessage("KDA_EE_NEXT_STEP_F") : GetMessage("KDA_EE_NEXT_STEP"); ?> &gt;&gt;" name="submit_btn" class="adm-btn-save">
<? 
}
else
{
?>
	<input type="hidden" name="STEP" value="1">
	<input type="submit" name="backButton2" value="&lt;&lt; <?echo GetMessage("KDA_EE_2_1_STEP"); ?>" class="adm-btn-save">
<?
}
?>

<?$tabControl->End();
?>

</form>

<script language="JavaScript">
<?if ($STEP < 2): 
?>
tabControl.SelectTab("edit1");
tabControl.DisableTab("edit2");
tabControl.DisableTab("edit3");
<?elseif ($STEP == 2): 

?>
tabControl.SelectTab("edit2");
tabControl.DisableTab("edit1");
tabControl.DisableTab("edit3");

<?elseif ($STEP > 2): ?>
tabControl.SelectTab("edit3");
tabControl.DisableTab("edit1");
tabControl.DisableTab("edit2");

<?
$arPost = $_POST;
unset($arPost['SETTINGS']);
if(COption::GetOptionString($moduleId, 'SET_MAX_EXECUTION_TIME')=='Y')
{
	$delay = (int)COption::GetOptionString($moduleId, 'EXECUTION_DELAY');
	$stepsTime = (int)COption::GetOptionString($moduleId, 'MAX_EXECUTION_TIME');
	if($delay > 0) $arPost['STEPS_DELAY'] = $delay;
	if($stepsTime > 0) $arPost['STEPS_TIME'] = $stepsTime;
}
else
{
	$stepsTime = intval(ini_get('max_execution_time'));
	if($stepsTime > 0) $arPost['STEPS_TIME'] = $stepsTime;
}

if($_POST['PROCESS_CONTINUE']=='Y'){
	$oProfile = new CKDAExportProfile();
?>
	EImport.Init(<?=CUtil::PhpToJSObject($arPost);?>, <?=CUtil::PhpToJSObject($oProfile->GetProccessParams($_POST['PROFILE_ID']));?>);
<?}else{?>
	EImport.Init(<?=CUtil::PhpToJSObject($arPost);?>);
<?}?>
<?endif; ?>
//-->
</script>

<?
require ($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");
?>
