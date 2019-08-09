<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$moduleId = 'kda.exportexcel';
CModule::IncludeModule('iblock');
CModule::IncludeModule($moduleId);
$bCurrency = CModule::IncludeModule("currency");
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$IBLOCK_ID = $_REQUEST['IBLOCK_ID'];

$oProfile = new CKDAExportProfile();
$arProfile = $oProfile->GetByID($_REQUEST['PROFILE_ID']);
$SETTINGS_DEFAULT = $arProfile['SETTINGS_DEFAULT'];

$fl = new CKDAEEFieldList($SETTINGS_DEFAULT);
$arFieldGroups = $fl->GetFields($IBLOCK_ID);
$arFields = array();
if(is_array($arFieldGroups))
{
	foreach($arFieldGroups as $arGroup)
	{
		if(is_array($arGroup['items']))
		{
			$arFields = array_merge($arFields, $arGroup['items']);
		}
	}
}

$isOffer = false;
$field = $_REQUEST['field'];
if(strpos($field, 'OFFER_')===0)
{
	$OFFER_IBLOCK_ID = CKDAExportUtils::GetOfferIblock($IBLOCK_ID);
	$field = substr($field, 6);
	$isOffer = true;
}

$addField = '';
if(strpos($field, '|') !== false)
{
	list($field, $addField) = explode('|', $field);
}

/*$obJSPopup = new CJSPopup();
$obJSPopup->ShowTitlebar(GetMessage("KDA_EE_SETTING_UPLOAD_FIELD").($arFields[$field] ? ' "'.$arFields[$field].'"' : ''));*/

$oProfile = new CKDAExportProfile();
$oProfile->ApplyExtra($PEXTRASETTINGS, $_REQUEST['PROFILE_ID']);
if(isset($_POST['POSTEXTRA']))
{
	$postExtra = $_POST['POSTEXTRA'];
	if(!defined('BX_UTF') || !BX_UTF)
	{
		$postExtra = $APPLICATION->ConvertCharset($postExtra, 'UTF-8', 'CP1251');
	}
	$arFieldParams = CUtil::JsObjectToPhp($postExtra);
	if(!$arFieldParams) $arFieldParams = array();
	$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name']));
	$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
	eval('$arFieldsParamsInArray = &$P'.$fNameEval.';');
	$arFieldsParamsInArray = $arFieldParams;
}

if($_POST['action']=='save' && is_array($_POST['EXTRASETTINGS']))
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

$ee = new CKDAExportExcel();
$bPicture = $ee->IsPictureField($field);
$bMultipleProp = $ee->IsMultipleField($field);

$bPrice = false;
if((strncmp($field, "ICAT_PRICE", 10) == 0 && (substr($field, -6)=='_PRICE') || substr($field, -15)=='_PRICE_DISCOUNT') || $field=="ICAT_PURCHASING_PRICE")
{
	$bPrice = true;
	if($bCurrency)
	{
		$arCurrency = array();
		$lcur = CCurrency::GetList(($by="sort"), ($order1="asc"), LANGUAGE_ID);
		while($arr = $lcur->Fetch())
		{
			$arCurrency[] = array(
				'CURRENCY' => $arr['CURRENCY'],
				'FULL_NAME' => $arr['FULL_NAME']
			);
		}
	}
}

$bIblockElement = false;
$bIblockSection = false;
if(strncmp($field, "IP_PROP", 7) == 0 && is_numeric(substr($field, 7)))
{
	$propId = intval(substr($field, 7));
	$dbRes = CIBlockProperty::GetList(array(), array('ID'=>$propId));
	if($arProp = $dbRes->Fetch())
	{
		if($arProp['PROPERTY_TYPE']=='E')
		{
			$bIblockElement = true;
			$iblockElementIblock = ($arProp['LINK_IBLOCK_ID'] ? $arProp['LINK_IBLOCK_ID'] : $IBLOCK_ID);
		}
		elseif($arProp['PROPERTY_TYPE']=='G')
		{
			$bIblockSection = true;
			$iblockSectionIblock = ($arProp['LINK_IBLOCK_ID'] ? $arProp['LINK_IBLOCK_ID'] : $IBLOCK_ID);
		}
	}
}

$bUser = (bool)($field=='IE_CREATED_BY' || $field=='IE_MODIFIED_BY');

$arFields = $fl->GetSettingsFields($IBLOCK_ID);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="" method="post" enctype="multipart/form-data" name="field_settings">
	<input type="hidden" name="action" value="save">
	<table width="100%">
		<col width="50%">
		<col width="50%">
		
		<?if($field=="IP_LIST_PROPS"){?>
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_PROPLIST_PROPS_LIST");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[PROPLIST_PROPS_LIST]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					if(!is_array($val)) $val = array();
					?>
					<select name="<?=$fName?>[]" multiple>
						<?
						$dbRes = CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => ($isOffer ? $OFFER_IBLOCK_ID : $IBLOCK_ID), "CHECK_PERMISSIONS" => "N"));
						while($arProp = $dbRes->Fetch())
						{
							?><option value="<?=$arProp['ID']?>"<?if(in_array($arProp['ID'], $val)){echo ' selected';}?>><?=$arProp['NAME']?></option><?
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_PROPLIST_PROPS_SEP_VALS");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[PROPLIST_PROPS_SEP_VALS]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsbx($val)?>" size="3">
				</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_PROPLIST_PROPS_SEP_NAMEVAL");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[PROPLIST_PROPS_SEP_NAMEVAL]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsbx($val)?>" size="3">
				</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_PROPLIST_PROPS_SHOW_EMPTY");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[PROPLIST_PROPS_SHOW_EMPTY]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					?>
					<input type="checkbox" name="<?=$fName?>" value="Y"<?if($val=='Y'){echo ' checked';}?>>
				</td>
			</tr>
		<?}?>
		
		<?if(preg_match('/^ICAT_PRICE\d+_PRICE_DISCOUNT$/', $field) || preg_match('/^ICAT_DISCOUNT_/', $field)){?>
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_DISCOUNT_USER_GROUP");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[USER_GROUP]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					
					$arGroups = array();
					$dbRes = \CGroup::GetList(($by="ID"), ($order="ASC"), array());
					while($arGroup = $dbRes->Fetch())
					{
						$arGroups[$arGroup['ID']] = $arGroup['NAME'];
					}
					?>
					<select name="<?echo $fName;?>[]" style="max-width: 450px;" multiple>
						<?foreach($arGroups as $groupId=>$groupName){?>
							<option value="<?echo $groupId;?>"<?if(is_array($val) && in_array($groupId, $val)){echo ' selected';}?>><?echo $groupName;?></option>
						<?}?>
					</select>
				</td>
			</tr>
			
			<?
			$arSites = array();
			if(class_exists('\Bitrix\Iblock\IblockSiteTable'))
			{
				$dbRes = \Bitrix\Iblock\IblockSiteTable::GetList(array('filter'=>array('IBLOCK_ID'=>$IBLOCK_ID), 'select'=>array('SITE_ID', 'SITE_NAME'=>'SITE.NAME')));
				while($arSite = $dbRes->Fetch())
				{
					$arSites[] = $arSite;
				}
			}
			if(count($arSites) > 1)
			{
			?>
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_DISCOUNT_SITE");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[SITE_ID]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					$dbRes = CIBlock::GetList(array(), array('ID'=>$IBLOCK_ID));
					$arIblock = $dbRes->Fetch();
					?>
					<select name="<?echo $fName;?>" style="max-width: 450px;">
						<?foreach($arSites as $arSite){?>
							<option value="<?echo $arSite['SITE_ID'];?>"<?if((!$val && $arSite['SITE_ID']==$arIblock['LID']) || $val==$arSite['SITE_ID']){echo ' selected';}?>>[<?echo $arSite['SITE_ID'];?>] <?echo $arSite['SITE_NAME'];?></option>
						<?}?>
					</select>
				</td>
			</tr>
			<?
			}
			?>
		<?}?>
		
		
		<?if($field=="IE_SECTION_PATH"){?>
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_SECTION_PATH_SEPARATOR");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[SECTION_PATH_SEPARATOR]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					?>
					<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsbx($val)?>" placeholder="<?echo GetMessage("KDA_EE_SETTINGS_SECTION_PATH_SEPARATOR_PLACEHOLDER");?>">
				</td>
			</tr>
		<?}?>
		
		<?if($bIblockElement){?>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_REL_ELEMENT_FIELD");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[REL_ELEMENT_FIELD]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					$val = '';
					if(is_array($PEXTRASETTINGS))
					{
						eval('$val = $P'.$fNameEval.';');
					}
					
					$strOptions = $fl->GetRelatedFields($iblockElementIblock, $val);
					?>
					<select name="<?echo $fName;?>" class="chosen" style="max-width: 450px;"><?echo $strOptions;?></select>
				</td>
			</tr>
		<?}?>
		
		<?if($bIblockSection){?>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_REL_SECTION_FIELD");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[REL_SECTION_FIELD]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					$val = '';
					if(is_array($PEXTRASETTINGS))
					{
						eval('$val = $P'.$fNameEval.';');
					}
					
					$strOptions = $fl->GetRelatedSectionFields($iblockSectionIblock, $val);
					?>
					<select name="<?echo $fName;?>" class="chosen" style="max-width: 450px;"><?echo $strOptions;?></select>
				</td>
			</tr>
		<?}?>
		
		<?if($bUser){?>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_REL_USER_FIELD");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[REL_USER_FIELD]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					$val = '';
					if(is_array($PEXTRASETTINGS))
					{
						eval('$val = $P'.$fNameEval.';');
					}
					?>
					<select name="<?echo $fName;?>" class="chosen" style="max-width: 450px;">
						<option value="ID"<?if($val=='ID'){echo ' selected';}?>><?echo GetMessage("KDA_EE_FIELD_USER_ID");?></option>
						<option value="XML_ID"<?if($val=='XML_ID'){echo ' selected';}?>><?echo GetMessage("KDA_EE_FIELD_XML_ID");?></option>
						<option value="LOGIN"<?if($val=='LOGIN'){echo ' selected';}?>><?echo GetMessage("KDA_EE_FIELD_LOGIN");?></option>
						<option value="EMAIL"<?if($val=='EMAIL'){echo ' selected';}?>><?echo GetMessage("KDA_EE_FIELD_EMAIL");?></option>
						<option value="LAST_NAME NAME"<?if($val=='LAST_NAME NAME'){echo ' selected';}?>><?echo GetMessage("KDA_EE_FIELD_LAST_NAME_NAME");?></option>
					</select>
				</td>
			</tr>
		<?}?>
		
		<?if($field=="IE_QR_CODE_IMAGE"){?>
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_QRCODE_CODE");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[QRCODE_SIZE]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					$arSizes = array();
					$sizeStep = 41;
					for($i=1; $i<25; $i++)
					{
						$arSizes[$i] = ($i*$sizeStep).'x'.($i*$sizeStep).'px';
					}
					?>
					<select name="<?=$fName?>">
						<?
						foreach($arSizes as $k=>$v)
						{
							?><option value="<?=$k?>"<?if(($val && $k==$val) || (!$val && $k==3)){echo ' selected';}?>><?=$v?></option><?
						}
						?>
					</select>
				</td>
			</tr>
		<?}elseif($bPicture){?>
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
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[CHANGE_MULTIPLE_SEPARATOR]';
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
			
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_MULTIPLE_SEPARATE_BY_ROWS");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[MULTIPLE_SEPARATE_BY_ROWS]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					?>
					<input type="checkbox" name="<?=$fName?>" value="Y" <?=($val=='Y' ? 'checked' : '')?>>
				</td>
			</tr>
			
			<tr>
				<td class="adm-detail-content-cell-l" valign="top"><?echo GetMessage("KDA_EE_SETTINGS_MULTIPLE_FROM_VALUE");?>:<br><small><?echo GetMessage("KDA_EE_SETTINGS_MULTIPLE_FROM_VALUE_COMMENT");?></small></td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName1 = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[MULTIPLE_FROM_VALUE]';
					$fNameEval1 = strtr($fName1, array("["=>"['", "]"=>"']"));
					eval('$val1 = $P'.$fNameEval1.';');
					
					$fName2 = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsex($_GET['field_name'])).'[MULTIPLE_TO_VALUE]';
					$fNameEval2 = strtr($fName2, array("["=>"['", "]"=>"']"));
					eval('$val2 = $P'.$fNameEval2.';');
					?>
					<input type="text" size="5" name="<?=$fName1?>" value="<?echo htmlspecialcharsbx($val1);?>" placeholder="1">
					<?echo GetMessage("KDA_EE_SETTINGS_MULTIPLE_TO_VALUE");?>
					<input type="text" size="5" name="<?=$fName2?>" value="<?echo htmlspecialcharsbx($val2);?>">
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
					$cellsOptions = '<option value="">'.sprintf(GetMessage("KDA_EE_SETTINGS_CONVERSION_CELL_CURRENT"), $i).'</option>';
					foreach($arFields as $k=>$arGroup)
					{
						if(is_array($arGroup['FIELDS']))
						{
							$cellsOptions .= '<optgroup label="'.$arGroup['TITLE'].'">';
							foreach($arGroup['FIELDS'] as $gkey=>$gfield)
							{
								$cellsOptions .= '<option value="'.$gkey.'"'.($v['CELL']==$gkey ? ' selected' : '').'>'.$gfield.'</option>';
							}
							$cellsOptions .= '</optgroup>';
						}
					}
					$cellsOptions .= '<optgroup label="'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CELL_GROUP_OTHER").'">';
					$cellsOptions .= '<option value="ELSE"'.($v['CELL']=='ELSE' ? ' selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CELL_ELSE").'</option>';
					$cellsOptions .= '</optgroup>';
					
					
					echo '<div class="kda-ee-settings-conversion" '.(!$showCondition ? 'style="display: none;"' : '').'>'.
							GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_TITLE").
							' <select name="'.$fName.'[CELL][]" class="field_cell">'.
								$cellsOptions.
							'</select> '.
							' <select name="'.$fName.'[WHEN][]" class="field_when">'.
								'<option value="EQ" '.($v['WHEN']=='EQ' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_EQ").'</option>'.
								'<option value="NEQ" '.($v['WHEN']=='NEQ' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_NEQ").'</option>'.
								'<option value="GT" '.($v['WHEN']=='GT' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_GT").'</option>'.
								'<option value="LT" '.($v['WHEN']=='LT' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_LT").'</option>'.
								'<option value="GEQ" '.($v['WHEN']=='GEQ' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_GEQ").'</option>'.
								'<option value="LEQ" '.($v['WHEN']=='LEQ' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_LEQ").'</option>'.
								'<option value="CONTAIN" '.($v['WHEN']=='CONTAIN' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_CONTAIN").'</option>'.
								'<option value="NOT_CONTAIN" '.($v['WHEN']=='NOT_CONTAIN' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_NOT_CONTAIN").'</option>'.
								'<option value="EMPTY" '.($v['WHEN']=='EMPTY' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_EMPTY").'</option>'.
								'<option value="NOT_EMPTY" '.($v['WHEN']=='NOT_EMPTY' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_NOT_EMPTY").'</option>'.
								'<option value="REGEXP" '.($v['WHEN']=='REGEXP' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_REGEXP").'</option>'.
								'<option value="NOT_REGEXP" '.($v['WHEN']=='NOT_REGEXP' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_NOT_REGEXP").'</option>'.
								'<option value="ANY" '.($v['WHEN']=='ANY' ? 'selected' : '').'>'.GetMessage("KDA_EE_SETTINGS_CONVERSION_CONDITION_ANY").'</option>'.
							'</select> '.
							'<input type="text" class="field_from" name="'.$fName.'[FROM][]" value="'.htmlspecialcharsbx($v['FROM']).'"> '.
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
		
		<?if($bPrice){?>
			<tr class="heading">
				<td colspan="2"><?echo GetMessage("KDA_EE_SETTINGS_PRICE_TITLE");?></td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_PRICE_USE_LANG_SETTINGS");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[PRICE_USE_LANG_SETTINGS]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					?>
					<input type="checkbox" name="<?=$fName?>" value="Y" <?=($val=='Y' ? 'checked' : '')?>>
				</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_PRICE_SHOW_CURRENCY");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[PRICE_SHOW_CURRENCY]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					?>
					<input type="checkbox" name="<?=$fName?>" value="Y" <?=($val=='Y' ? 'checked' : '')?>>
				</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_PRICE_CONVERT_CURRENCY");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[PRICE_CONVERT_CURRENCY]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					$convertCurrency = $val;
					?>
					<input type="checkbox" name="<?=$fName?>" value="Y" <?=($val=='Y' ? 'checked' : '')?> onchange="ESettings.ToggleSubfields(this)">
				</td>
			</tr>
			<tr class="subfield" <?if($convertCurrency!='Y'){echo 'style="display: none;"';}?>>
				<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_SETTINGS_PRICE_CONVERT_CURRENCY_TO");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[PRICE_CONVERT_CURRENCY_TO]';
					$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
					eval('$val = $P'.$fNameEval.';');
					?>
					<select name="<?=$fName?>">
					<?
					foreach($arCurrency as $item)
					{
						?><option value="<?echo $item['CURRENCY']?>"<?if($val==$item['CURRENCY']){echo 'selected';}?>>[<?echo $item['CURRENCY']?>] <?echo $item['FULL_NAME']?></option><?
					}
					?>
					</select>
				</td>
			</tr>
		<?}?>
		
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
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_TEXT_ALIGN"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[TEXT_ALIGN]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<select name="<?=$fName?>">
					<option value=""><?echo GetMessage("KDA_EE_NOT_CHANGE"); ?></option>
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
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[VERTICAL_ALIGN]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<select name="<?=$fName?>">
					<option value=""><?echo GetMessage("KDA_EE_NOT_CHANGE"); ?></option>
					<option value="TOP" <?if($val=='TOP'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN_TOP"); ?></option>
					<option value="CENTER" <?if($val=='CENTER'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN_CENTER"); ?></option>
					<option value="BOTTOM" <?if($val=='BOTTOM'){echo 'selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_VERTICAL_ALIGN_BOTTOM"); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_FONT_COLOR"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[FONT_COLOR]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsbx($val)?>" placeholder="#ffffff">
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_BACKGROUND_COLOR"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[BACKGROUND_COLOR]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsbx($val)?>" placeholder="#ffffff">
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_FONT_FAMILY"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[FONT_FAMILY]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsbx($val)?>" placeholder="Calibri">
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_FONT_SIZE"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[FONT_SIZE]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<input type="text" name="<?=$fName?>" value="<?=htmlspecialcharsbx($val)?>" placeholder="11">
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_FONT_STYLE_BOLD"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[STYLE_BOLD]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<input type="checkbox" name="<?=$fName?>" value="Y" <?=($val=='Y' ? 'checked' : '')?>>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_FONT_STYLE_ITALIC"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[STYLE_ITALIC]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<input type="checkbox" name="<?=$fName?>" value="Y" <?=($val=='Y' ? 'checked' : '')?>>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_NUMBER_FORMAT"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[NUMBER_FORMAT]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<select name="<?=$fName?>">
					<option value=""><?echo GetMessage("KDA_EE_DISPLAY_NUMBER_FORMAT_GENERAL"); ?></option>
					<option value="49"<?if($val=='49'){echo ' selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_NUMBER_FORMAT_TEXT"); ?></option>
					<option value="1"<?if($val=='1'){echo ' selected';}?>><?echo sprintf(GetMessage("KDA_EE_DISPLAY_NUMBER_FORMAT_NUMERIC"), '1234'); ?></option>
					<option value="3"<?if($val=='3'){echo ' selected';}?>><?echo sprintf(GetMessage("KDA_EE_DISPLAY_NUMBER_FORMAT_NUMERIC"), '1 234'); ?></option>
					<option value="2"<?if($val=='2'){echo ' selected';}?>><?echo sprintf(GetMessage("KDA_EE_DISPLAY_NUMBER_FORMAT_NUMERIC"), '1234,10'); ?></option>
					<option value="4"<?if($val=='4'){echo ' selected';}?>><?echo sprintf(GetMessage("KDA_EE_DISPLAY_NUMBER_FORMAT_NUMERIC"), '1 234,10'); ?></option>
					<option value="5"<?if($val=='5'){echo ' selected';}?>><?echo sprintf(GetMessage("KDA_EE_DISPLAY_NUMBER_FORMAT_FINANCIAL"), '1 234 P'); ?></option>
					<option value="7"<?if($val=='7'){echo ' selected';}?>><?echo sprintf(GetMessage("KDA_EE_DISPLAY_NUMBER_FORMAT_FINANCIAL"), '1 234,10 P'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_PROTECTION"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[PROTECTION]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<select name="<?=$fName?>">
					<option value=""><?echo GetMessage("KDA_EE_DISPLAY_PROTECTION_ENABLE"); ?></option>
					<option value="N"<?if($val=='N'){echo ' selected';}?>><?echo GetMessage("KDA_EE_DISPLAY_PROTECTION_DISABLE"); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("KDA_EE_DISPLAY_MAKE_DROPDOWN"); ?>:</td>
			<td class="adm-detail-content-cell-r">
				<?
				$fName = 'EXTRA'.str_replace('[FIELDS_LIST]', '', htmlspecialcharsbx($_GET['field_name'])).'[MAKE_DROPDOWN]';
				$fNameEval = strtr($fName, array("["=>"['", "]"=>"']"));
				eval('$val = $P'.$fNameEval.';');
				?>
				<input type="checkbox" name="<?=$fName?>" value="Y" <?=($val=='Y' ? 'checked' : '')?>>
			</td>
		</tr>
		
	</table>
</form>
<script>
var admKDASettingMessages = <?echo CUtil::PhpToJSObject($arFields)?>;
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>