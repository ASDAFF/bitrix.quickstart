<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$moduleId = 'esol.importxml';
CModule::IncludeModule('iblock');
CModule::IncludeModule($moduleId);
$bCurrency = CModule::IncludeModule("currency");
IncludeModuleLangFile(__FILE__);

$IBLOCK_ID = (int)$_REQUEST['IBLOCK_ID'];

$error = '';
if($_POST['action']=='save' && $_POST['FIELD'])
{
	$arFields = $_POST['FIELD'];
	if(!defined('BX_UTF') || !BX_UTF)
	{
		$arFields = $APPLICATION->ConvertCharsetArray($arFields, 'UTF-8', 'CP1251');
	}
	$arFields['IBLOCK_ID'] = $IBLOCK_ID;
	if(strpos($arFields['PROPERTY_TYPE'], ':')!==false)
	{
		list($ptype, $utype) = explode(':', $arFields['PROPERTY_TYPE'], 2);
		$arFields['PROPERTY_TYPE'] = $ptype;
		$arFields['USER_TYPE'] = $utype;
	}
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
	
	if($PropID)
	{
		$APPLICATION->RestartBuffer();
		if(ob_get_contents()) ob_end_clean();
	
		echo '<script>EList.OnAfterAddNewProperty("'.htmlspecialcharsex($_REQUEST['FIELD_NAME']).'", "IP_PROP'.$PropID.'", "'.htmlspecialcharsex($arFields['NAME']).'", "'.$IBLOCK_ID.'");</script>';
		die();
	}
	else
	{
		$error = $ibp->LAST_ERROR;
	}
}

$arUserTypeList = CIBlockProperty::GetUserType();
\Bitrix\Main\Type\Collection::sortByColumn($arUserTypeList, array('DESCRIPTION' => SORT_STRING));
$boolUserPropExist = !empty($arUserTypeList);
$PROPERTY_TYPE = 'S';
if($_POST['FIELD']['PROPERTY_TYPE']) $PROPERTY_TYPE = $_POST['FIELD']['PROPERTY_TYPE'];
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="" method="post" enctype="multipart/form-data" name="new_property" id="newPropertyForm">
	<input type="hidden" name="action" value="save">
	<?if($error){
		ShowError($error);
		?><script>
			EList.NewPropDialogButtonsSet(true);
		</script><?
	}?>
	
	<table width="100%">
		<col width="50%">
		<col width="50%">
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_NP_TYPE");?>:</td>
			<td class="adm-detail-content-cell-r">
				<select name="FIELD[PROPERTY_TYPE]">
				<?
					if ($boolUserPropExist)
					{
						?><optgroup label="<? echo GetMessage('ESOL_IX_NP_PROPERTY_BASE_TYPE_GROUP'); ?>"><?
					}
					?>
					<option value="S" <?if($PROPERTY_TYPE=="S")echo " selected"?>><?echo GetMessage("ESOL_IX_NP_IBLOCK_PROP_S")?></option>
					<option value="N" <?if($PROPERTY_TYPE=="N")echo " selected"?>><?echo GetMessage("ESOL_IX_NP_IBLOCK_PROP_N")?></option>
					<option value="L" <?if($PROPERTY_TYPE=="L")echo " selected"?>><?echo GetMessage("ESOL_IX_NP_IBLOCK_PROP_L")?></option>
					<option value="F" <?if($PROPERTY_TYPE=="F")echo " selected"?>><?echo GetMessage("ESOL_IX_NP_IBLOCK_PROP_F")?></option>
					<option value="G" <?if($PROPERTY_TYPE=="G")echo " selected"?>><?echo GetMessage("ESOL_IX_NP_IBLOCK_PROP_G")?></option>
					<option value="E" <?if($PROPERTY_TYPE=="E")echo " selected"?>><?echo GetMessage("ESOL_IX_NP_IBLOCK_PROP_E")?></option>
					<?
					if ($boolUserPropExist)
					{
					?></optgroup><optgroup label="<? echo GetMessage('ESOL_IX_NP_PROPERTY_USER_TYPE_GROUP'); ?>"><?
					}
					foreach($arUserTypeList as  $ar)
					{
						?><option value="<?=htmlspecialcharsbx($ar["PROPERTY_TYPE"].":".$ar["USER_TYPE"])?>" <?if($PROPERTY_TYPE==$ar["PROPERTY_TYPE"].":".$ar["USER_TYPE"])echo " selected"?>><?=htmlspecialcharsbx($ar["DESCRIPTION"])?></option>
						<?
					}
					if ($boolUserPropExist)
					{
						?></optgroup><?
					}
					?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_NP_ACTIVE");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="checkbox" name="FIELD[ACTIVE]" value="Y" <?if(!isset($_POST['FIELD']['ACTIVE']) || $_POST['FIELD']['ACTIVE']=='Y'){?>checked<?}?>>
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_NP_SORT");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="text" name="FIELD[SORT]" value="<?echo ($_POST['FIELD']['SORT'] ? htmlspecialcharsex($_POST['FIELD']['SORT']) : '500')?>">
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><b><?echo GetMessage("ESOL_IX_NP_NAME");?></b>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="text" name="FIELD[NAME]" value="<?echo ($_POST['FIELD']['NAME'] ? htmlspecialcharsex($_POST['FIELD']['NAME']) : '')?>">
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_NP_CODE");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="text" name="FIELD[CODE]" value="<?echo ($_POST['FIELD']['CODE'] ? htmlspecialcharsex($_POST['FIELD']['CODE']) : '')?>">
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_NP_MULTIPLE");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="checkbox" name="FIELD[MULTIPLE]" value="Y" <?if(isset($_POST['FIELD']['MULTIPLE']) && $_POST['FIELD']['MULTIPLE']=='Y'){?>checked<?}?>>
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_NP_IS_REQUIRED");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="checkbox" name="FIELD[IS_REQUIRED]" value="Y" <?if(isset($_POST['FIELD']['IS_REQUIRED']) && $_POST['FIELD']['IS_REQUIRED']=='Y'){?>checked<?}?>>
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_NP_SEARCHABLE");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="checkbox" name="FIELD[SEARCHABLE]" value="Y" <?if(isset($_POST['FIELD']['SEARCHABLE']) && $_POST['FIELD']['SEARCHABLE']=='Y'){?>checked<?}?>>
			</td>
		</tr>
		
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_NP_FILTRABLE");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="checkbox" name="FIELD[FILTRABLE]" value="Y" <?if(isset($_POST['FIELD']['FILTRABLE']) && $_POST['FIELD']['FILTRABLE']=='Y'){?>checked<?}?>>
			</td>
		</tr>
	</table>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>