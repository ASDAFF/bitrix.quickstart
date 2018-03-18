<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 
IncludeModuleLangFile( __FILE__);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
//print "<pre>"; print_r($_SESSION['ARR_REAL_PROFILE']); print "</pre>";
?>

<?
$ARR_REAL_PROFILE = $_SESSION['ARR_REAL_PROFILE'];
$errMess = "";
$XLS_IBLOCK_ID=$_REQUEST["xls_iblock_id"];
$inputFileName =  $_REQUEST['xls_file_name'];
$SKU_IBLOCK_ID ="";
$titleRow = $_REQUEST['title_xls_row'];

$add_sku = $_REQUEST['add_sku'];
if ($add_sku=="Y")
	{$SKU_IBLOCK_ID = $_REQUEST['iblock_sku_id'];
	$CML2_LINK_CODE = $_REQUEST['cml2_link_code'];
	}


if ($XLS_IBLOCK_ID==0)
	$errMess = GetMessage("XLS_NULL_IBLOCK_SET");
	
if (empty($inputFileName))	
	$errMess =$errMess."</br>".GetMessage("XLS_NULL_FILE");

	
$highestColumn = strtoupper($_REQUEST["column_b"]);
$firstColumn = strtoupper($_REQUEST["column_a"]);
$firstRow = $_REQUEST['first_row'];
if (empty($firstRow))
	$firstRow = 1;

if (ord($highestColumn)<ord($firstColumn))
{
$tempColumn = $firstColumn;
$firstColumn = $highestColumn;
$highestColumn = $tempColumn;
}

if ((ord($highestColumn)>90)||(ord($firstColumn)<65))
	$errMess.=GetMessage("XLS_WRONG_DIAPAZONE");	

if (empty($errMess))
{

CModule::IncludeModule('mcart.xls');
	 $langCls = new CMcartXlsStrRef();

?>
<form action="mcart_xls_import_step_2.php"  method="POST">
<input type=hidden name="xls_iblock_id" value="<?=intval($XLS_IBLOCK_ID)?>">
<?

	 include $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/mcart.xls/classes/general/PHPExcel/IOFactory.php';
	 
	 try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			
			if ($inputFileType=='CSV')
			{
				
				if (ini_get('mbstring.func_overload') & 2) 
				{
				
					die(GetMessage("MCART_WRONG_FILE_FORMAT")."</br><a href = '/bitrix/admin/mcart_xls_import.php'>".GetMessage("STEP_BACK")."</a>");
				
				}
			}
			
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
			$worksheet_names = $objReader->listWorksheetNames($inputFileName);
//print_r($worksheet_names);
			
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
	
	$XLS_SHEET_INDEX = $_REQUEST['xls_shett_index'];
	$_SESSION['ARR_XLS_DATA']["SHEET_ID"] = $XLS_SHEET_INDEX;
	$arRows = array();
	$sheet = $objPHPExcel->getSheet($XLS_SHEET_INDEX); 
	
	
	$LAST_ROW_TYPE = $_REQUEST['rows_end_label'];
	$_SESSION['ARR_XLS_DATA']["LAST_ROW_TYPE"] = $LAST_ROW_TYPE;
	
	switch ($LAST_ROW_TYPE) {
						case 'auto':
							$highestRow = $sheet->getHighestRow(); 
							break;
						case 'lastrownumber':
							$num = $_REQUEST['last_row_num'];
							$highestRow = intval($num); 
							break;	
						default:
							$highestRow = $sheet->getHighestRow(); 
						
						}	

	foreach(range(ord($firstColumn), ord($highestColumn)) as $v)
	{
	$alfavit[] = chr($v);
	}

	$arIBlocksSection=Array();
	$arIBlocksSection["0"] = "";
	$db_list = CIBlockSection::GetList(Array("NAME"=>"ASC"), array("IBLOCK_ID"=>$XLS_IBLOCK_ID));
	while($arRes = $db_list->GetNext())
		$arIBlocksSection[$arRes["ID"]] = $arRes["NAME"];
	
?>

<h4><?=GetMessage("XLS_SELECT_IBLOCK_SECTION")?></h4>
			<select name = "xls_iblock_section_id" >
				<?foreach ($arIBlocksSection as $key=>$value):?>
				<option value="<?=$key?>"   <?if (isset($ARR_REAL_PROFILE['section_id'])&&($ARR_REAL_PROFILE['section_id']==$key)) echo " selected"?>><?=$value?></option>
				<?endforeach?>
			</select>
				
</br>

<h3><?=GetMessage("XLS_DATA_EXAMPLE")?></h3>
</br>
<table border="1">

<tr>
	<? foreach ($alfavit as $bukva):?>
	<td><h3><?=$bukva?></h3></td>
	<?endforeach;?>
</tr>
<?



?>
<?$count_visible_row = 0;?>
<?$count_simply_row = 0;?>
<?
$ARR_TITLE_ROW = array();

$rowTitleData = $sheet->rangeToArray($firstColumn . $titleRow . ':' . $highestColumn . $titleRow,
											NULL,
											TRUE,
											FALSE);
foreach ($rowTitleData as $row_str)											
	foreach ($row_str as $row_cell)	
			$ARR_TITLE_ROW[] = $row_cell;
											
	for ($row = $firstRow; $row <= $highestRow; $row++)
		{ 
			$rowData = $sheet->rangeToArray($firstColumn . $row . ':' . $highestColumn . $row,
											NULL,
											TRUE,
											FALSE);
											
											?>
											
											
											<?foreach ($rowData as $row_id=>$row_val):?>
											<?if ($count_visible_row<5):?>
											<tr>
												<?foreach($row_val as $col_id=>$txt):?>
												<td><?= $langCls->ConvertArrayCharset($txt, BP_EI_DIRECTION_IMPORT)?></td>
												<?endforeach?>
											</tr>
											<?endif;?>
												
												<?foreach($row_val as $col_id=>$txt):?>
												<input type=hidden name="arr_rows[<?=$count_simply_row?>][<?=$col_id?>]" value="<?=htmlspecialchars($langCls->ConvertArrayCharset($txt, BP_EI_DIRECTION_IMPORT))?>">
												<?endforeach?>
												
											<?$count_visible_row ++;?>
											<?$count_simply_row ++;?>
											<?endforeach;?>
											
											<?
											
											
		}
?>
</table >
<?
global $MCART_IS_SKU;

	// определение базовой цены	
	if ((CModule::IncludeModule('catalog'))&&(CModule::IncludeModule('sale')))
	{
	$MCART_IS_SKU = true;
	$db_res = GetCatalogGroups(($b="SORT"), ($o="ASC"));
	while ($res = $db_res->Fetch())
	{
		if ($res["BASE"]=="Y")
		{
		$BASE_PRICE_ID = $res["ID"];
		$BASE_PRICE_NAME = $res["NAME_LANG"];
		//print "<pre>"; print_r($res); print "</pre>";
		break;
		}
	  
	}	
	}	
		
	//print "<pre>"; print_r($arRows); print "</pre>";
	$SrcPropID["FLD_DETAIL_TEXT"] = GetMessage("XLS_FLD_DETAIL_TEXT");
	$SrcPropID["FLD_PREVIEW_TEXT"] = GetMessage("XLS_FLD_PREVIEW_TEXT");
	
	$SrcPropID["FLD_DETAIL_PICTURE"] = GetMessage("XLS_FLD_DETAIL_PICTURE");
	$SrcPropID["FLD_PREVIEW_PICTURE"] = GetMessage("XLS_FLD_PREVIEW_PICTURE");
	
	if ($MCART_IS_SKU)
	{
		$SrcPropID["FLD_CATALOG_BASE_PRICE"] = $BASE_PRICE_NAME;
		$SrcPropID["FLD_PURCHASING_PRICE"] = GetMessage("XLS_FLD_PURCHASING_PRICE");
	}
	
	$res = CIBlock::GetProperties($XLS_IBLOCK_ID, Array("SORT"=>"ASC"), Array());
	while($res_arr = $res->GetNext())
		$SrcPropID[$res_arr["CODE"]] =$res_arr["NAME"];
		
	//$SrcPropID["0"] =GetMessage("XLS_NOT_RECORD");
	//$SrcPropID["FLD_NAME"] = GetMessage("XLS_FLD_NAME");

?>
</br>
</br>
<h1><?=GetMessage("XLS_SET_COLUMN_FILED");?></h1>
</br>
<table border="1px">
<thead>
<td>
<?=GetMessage("XLS_TITLE_1")?>
</td>
<td>
<?=GetMessage("XLS_TITLE_2")?>
</td>
<td>
<?=GetMessage("XLS_TITLE_3")?>
</td>
<td>
<?=GetMessage("XLS_TITLE_4")?>
</td>
<td>
<?=GetMessage("XLS_MODIFY_TYPE")?>
</td>

</thead>
<?$bcheck = false;?>
	<?foreach($alfavit as $key=>$val):?>
	<tr>
		<td><?=$val?></br><?=$ARR_TITLE_ROW[$key]?></td>
		<td>
			<select name = "columns[<?=$key?>][]" multiple size="5">
			<?foreach ($SrcPropID as $kode=>$name):?>
			<option value="<?=$kode?>" <? if (in_array($kode, $ARR_REAL_PROFILE["FIELDS"][$key])) echo " selected"?>><?=$name?></option>
			<?endforeach?>
			</select>
		</td>
		<td>
			<input type="radio" name="fld_name" value="<?=$key?>" <?if (!$bcheck): echo 'checked'; $bcheck=true; endif; if ($ARR_REAL_PROFILE['name_field']==$key) echo " checked"?>>
		</td>
		<td>
			<input type="radio" name="fld_identify" value="<?=$key?>" <?if (!$bcheck2): echo 'checked'; $bcheck2=true; endif; if ($ARR_REAL_PROFILE['identify']==$key) echo " checked"?>>
		</td>
		<td>
		
			<select name = "modify_type[<?=$key?>]" >
			<option value="XLS_MODIFY_TYPE_NONE"><?=GetMessage("XLS_MODIFY_TYPE_NONE")?></option>
			<option value="XLS_MODIFY_TYPE_TO_INT"><?=GetMessage("XLS_MODIFY_TYPE_TO_INT")?></option>
			<option value="XLS_MODIFY_TYPE_EXP_2"><?=GetMessage("XLS_MODIFY_TYPE_EXP_2")?></option>
			<option value="XLS_MODIFY_TYPE_TRIM_20"><?=GetMessage("XLS_MODIFY_TYPE_TRIM_20")?></option>
			</select>
		</td>
	</tr>
	<?endforeach;?>	
</table>
</br>
</br>
<h4><input type="checkbox" class="make_translit_code"  name="make_translit_code" value="Y">
<?=GetMessage("MAKE_TRANSLIT_CODE")?></h4>
</br>
</br>
<h4><input type="checkbox" class="save_profile"  name="save_profile" value="Y">
<?=GetMessage("XLS_SAVE_PROFILE")?></h4>
</br>
<input type="text" name="profile_name" >
</br>
</br>
</br>
<a href = "/bitrix/admin/mcart_xls_import.php"><?=GetMessage("STEP_BACK")?></a>
<input type="submit" name="next_step" value="<?=GetMessage("BEGIN_IMPORT")?>">
<input type='hidden' name='catalog_base_price_id' value=<?=$BASE_PRICE_ID?>>
<input type='hidden' name='sku_iblock_id' value=<?=$SKU_IBLOCK_ID?>>
<input type='hidden' name='cml2_link_code' value=<?=$CML2_LINK_CODE?>>
<input type='hidden' name='firstColumn' value=<?=$firstColumn?>>
<input type='hidden' name='firstRow' value=<?=$firstRow?>>
<input type='hidden' name='titleRow' value=<?=$titleRow?>>
<input type='hidden' name='highestColumn' value=<?=$highestColumn?>>

</form>

<?}
else
{
?>
<?=$errMess?>

<a href = "/bitrix/admin/mcart_xls_import.php"><?=GetMessage("STEP_BACK")?></a>
<?
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>