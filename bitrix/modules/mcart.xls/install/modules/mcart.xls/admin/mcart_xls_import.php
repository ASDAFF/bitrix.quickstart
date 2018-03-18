<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 
?>
<? $APPLICATION->AddHeadScript("http://code.jquery.com/jquery-2.1.0.min.js");?>
<?
IncludeModuleLangFile( __FILE__);
$APPLICATION->SetTitle(GetMessage("MCART_IMPORT_XLS_STEP_0"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>


<script type="text/javascript">

function ShowInput()
	{
		$('.last_row_num').css("display","block");
	}
function HideInput()
	{
		$('.last_row_num').css("display","none");
	}
function ShowSKU(chk)
{
if (chk.checked)
	$('.iblock_sku_id').css("display","block");
else
	$('.iblock_sku_id').css("display","none");

}
</script>
<?
$errMess = "";
$inputFileName =  $_FILES['xls_file_name']['tmp_name'];
if ($_REQUEST["del_prof"]):
?>	<form action="<?=$APPLICATION->GetCurPage()?>"  method="POST">
	<?echo GetMessage("MCART_DO_YOU_REALY_DEL_QUESTION")." id = ".$_REQUEST["xls_profile"]?>
	<?
		//LocalRedirect('mcart_xls_start.php');
			
	?>
	<input type="hidden" name="xls_profile" value="<?=$_REQUEST["xls_profile"]?>">
	<input type="submit" name="del_prof_real" value="Да">
	</form>
<? elseif ($_REQUEST["del_prof_real"]):
?>
	<?
	global $DB;
$prof_id = $_REQUEST["xls_profile"];
$strSql = "DELETE mcart_xls.* from mcart_xls WHERE (id=".$prof_id.")";
		$ret =  $DB->Query($strSql, false);
	?>
	<?=GetMessage("MCART_PROFLE_DELETED")?>&nbsp;
	<a href="mcart_xls_start.php">OK</a>
	<?
		//LocalRedirect('mcart_xls_start.php');
			
	?>
	
	</form>	
<?else:?>	
	<?
	if ($PROFILE_ID=$_REQUEST['xls_profile'])
	{
		if ($PROFILE_ID>0)
		{
			
			$strSql = "SELECT * from mcart_xls WHERE id=".(int)$PROFILE_ID;
			$ret =  $DB->Query($strSql, false);
			if ($ar = $ret->GetNext())
			foreach ($ar as $key=>$val)
				{$_SESSION['ARR_REAL_PROFILE'][$key] = $val;
				$ARR_REAL_PROFILE[$key] = $val;
				}
			
			$strSql = "SELECT * from mcart_xls_fields WHERE profile_id=".(int)$PROFILE_ID;
				$ret =  $DB->Query($strSql, false);
				while ($ar = $ret->GetNext())
					$_SESSION['ARR_REAL_PROFILE']["FIELDS"][$ar['col_id']][$ar['key2']] = $ar['field_code'];
		}
	}



	if (empty($inputFileName))	
		$errMess =$errMess."</br>".GetMessage("XLS_NULL_FILE");
	if (empty($errMess))	
	{
	$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/upload/';

	if (!is_dir($uploaddir))
		mkdir($uploaddir);
	$uploadfile = $uploaddir . basename($_FILES['xls_file_name']['name']);
	if (move_uploaded_file($_FILES['xls_file_name']['tmp_name'], $uploadfile))
		$inputFileName = $uploadfile;

	/*
	$res = CIBlock::GetProperties(2, Array("SORT"=>"ASC"), Array());
		while($res_arr = $res->GetNext())
			{$SrcPropID[$res_arr["CODE"]] =$res_arr["NAME"];
				print "<pre>"; print_r($res_arr); print "</pre>";
		*/	
			/*["PROPERTY_TYPE"] = 
			L - список 
			S - строка
			E - привязка к элементам
			F - файл
			*/
			//}

	include_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/mcart.xls/classes/general/PHPExcel/IOFactory.php';
	$arrWorkSheets = array();	 
		 try {
		 
		 CModule::IncludeModule('mcart.xls');
		 $langCls = new CMcartXlsStrRef();
		 
				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				
				if ($inputFileType=='CSV')
				{
					
					if (ini_get('mbstring.func_overload') & 2) 
					{
					
						die(GetMessage("MCART_WRONG_FILE_FORMAT")."</br><a href = '/bitrix/admin/mcart_xls_start.php'>".GetMessage("STEP_BACK")."</a>");
					
					}
				}
				
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				
				$worksheet_names = $objReader->listWorksheetNames($inputFileName);
				foreach ($worksheet_names as $ws_id=>$ws_name)
					$arrWorkSheets[$ws_id] =  $langCls->ConvertArrayCharset($ws_name, BP_EI_DIRECTION_IMPORT);
				
			} catch(Exception $e) {
				die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
			}
	?>
	<form action="/bitrix/admin/mcart_xls_import_step_1.php"  method="POST">
	<?
	$arIBlocks=Array();
	$arIBlocks["0"] = "";
	$db_iblock = CIBlock::GetList(Array("NAME"=>"ASC"), Array());
	while($arRes = $db_iblock->GetNext())
		$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
	?>
	<?
	$arIBlocksSKU=Array();
	$arIBlocks["0"] = "";
	$db_iblock = CIBlock::GetList(Array("NAME"=>"ASC"), Array("TYPE"=>"offers"));
	while($arRes = $db_iblock->GetNext())
		$arIBlocksSKU[$arRes["ID"]] = $arRes["NAME"];
	?>
	<h4><?=GetMessage("XLS_SELECT_CURRENT_SHEET")?></h4>
				<select name = "xls_shett_index" >
					<?foreach ($arrWorkSheets as $key=>$value):?>
					<option value="<?=$key?>" <?if (isset($ARR_REAL_PROFILE['sheet_id'])&&($ARR_REAL_PROFILE['sheet_id']==$key)) echo " selected"?>><?=$value?></option>
					<?endforeach?>
				</select>
	</br>
				<h4><?=GetMessage("XLS_SELECT_IBLOCK")?></h4>
				<select name = "xls_iblock_id" >
					<?foreach ($arIBlocks as $key=>$value):?>
					<option value="<?=$key?>" <?if (isset($ARR_REAL_PROFILE['iblock_id'])&&($ARR_REAL_PROFILE['iblock_id']==$key)) echo " selected"?>><?=$value?></option>
					<?endforeach?>
				</select>
	</br>
	<h4><?=GetMessage("MCART_TITLE_XLS_ROW")?></h4>
	<input type="text" name="title_xls_row" size="3" value="<?if (isset($ARR_REAL_PROFILE['title_row'])) echo $ARR_REAL_PROFILE['title_row']?>"/>
	</br>
	<h4><?=GetMessage("MCART_FIRST_ROW")?></h4>
	<input type="text" name="first_row" size="3" value="<?if (isset($ARR_REAL_PROFILE['data_row'])) echo $ARR_REAL_PROFILE['data_row']?>"/>
	</br>
	<h4><?=GetMessage("MCART_ROWS_END")?></h4>
		<input type="radio" name="rows_end_label" class="nonumber" value="auto" onclick="HideInput()" checked><?=GetMessage('XLS_MCART_AUTO')?></br>
		<input type="radio" name="rows_end_label" class="nonumber" value="keyword"  onclick="HideInput()" disabled><?=GetMessage('XLS_MCART_WORD1')?></br>
		<input type="radio" name="rows_end_label" class="lastrownumber" value="lastrownumber" onclick="ShowInput()" ><?=GetMessage('XLS_MCART_ROW_END_NUMBER')?><input type="text" class="last_row_num" name="last_row_num" style="display: none" size="2"/></br>
		<input type="radio" name="rows_end_label" class="nonumber" value="emptyinrow"  onclick="HideInput()" disabled><?=GetMessage('XLS_MCART_ROW_EEMPTY')?></br>
	</br>
	<input type="hidden" name = "xls_file_name" value='<?=$inputFileName?>'>
	<h4><?=GetMessage("MCART_COLUMN_DIAPAZONE")?></h4>
	<input type="text" name="column_a" size="3" value="<?if (isset($ARR_REAL_PROFILE['diapazone_a'])) echo $ARR_REAL_PROFILE['diapazone_a']?>"/>
	-
	<input type="text" name="column_b" size="3" value="<?if (isset($ARR_REAL_PROFILE['diapazone_z'])) echo $ARR_REAL_PROFILE['diapazone_z']?>"/>		
	</br>

	<?
	$MCART_IS_SKU = false;
		// определение базовой цены	
		if ((CModule::IncludeModule('catalog'))&&(CModule::IncludeModule('sale'))):
		$MCART_IS_SKU = true;
	?>	
		

	<h4><input type="checkbox" class="add_sku"  name="add_sku" value="Y" onclick="ShowSKU(this)">
	<?=GetMessage("XLS_SELECT_SKU_IBLOCK")?></h4>
	</br>
	<div class = "iblock_sku_id"  style="display:none" >
	<?=GetMessage("IBLOCK_SKU_TITLE")?>
	</br>
	<select name = "iblock_sku_id" class="sku_detail">
		<?foreach ($arIBlocks as $key=>$value):?>
		<option value="<?=$key?>"><?=$value?></option>
		<?endforeach?>
	</select>
	</br>
	<?=GetMessage("CML2_LINK_ASC")?><input type="text" name="cml2_link_code" >
	</br>
	</br>
	</div>
	</br>
	<?endif;?>


	<a href = "/bitrix/admin/mcart_xls_start.php"><?=GetMessage("STEP_BACK")?></a>
	<input type="submit" name="next_step" value="<?=GetMessage("NEXT_STEP")?>">
	</form>
	<?}
	else
	{
	echo $errMess;?>
	<br>
	<a href = "/bitrix/admin/mcart_xls_start.php"><?=GetMessage("STEP_BACK")?></a>
	<?
	}
	?>
<?endif;?>	
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>