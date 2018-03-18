  <?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 
IncludeModuleLangFile( __FILE__);
$APPLICATION->SetTitle(GetMessage("MCART_IMPORT_XLS_STEP_2"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
global $MCART_IS_SKU;
$MCART_IS_SKU = false;
global $DB;
$db_type=strtolower($DB->type);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/classes/".$db_type."/profile.php"); 
if(
  (CModule::IncludeModule('catalog'))&&
  (CModule::IncludeModule('sale'))
  )
	$MCART_IS_SKU = true;
?>

<?

$arrRows = $_REQUEST["arr_rows"];
	//print "<pre>"; print_r($arrRows); print "</pre>";
	
	//print "<pre>"; print_r($_REQUEST); print "</pre>";
$NAME_ID = $_REQUEST['fld_name'];
$IDENTIFY = $_REQUEST['fld_identify'];
$XLS_IBLOCK_ID = $_REQUEST['xls_iblock_id'];
$PROP_COLUMNS = $_REQUEST['columns'];
$SECTION = $_REQUEST['xls_iblock_section_id'];
$CATALOG_PRICE_BASE_ID = $_REQUEST['catalog_base_price_id'];
$SKU_IBLOCK_ID = $_REQUEST['sku_iblock_id'];

$firstColumn = $_REQUEST['firstColumn'];
$firstRow = $_REQUEST['firstRow'];
$titleRow = $_REQUEST['titleRow'];
$highestColumn = $_REQUEST['highestColumn'];

if (!$CML2_LINK_CODE = $_REQUEST['cml2_link_code'])
	$CML2_LINK_CODE = "CML2_LINK";

if ($_REQUEST["make_translit_code"]=="Y")
	$MAKE_TRANSLIT_CODE = true;

	
if ($_REQUEST['save_profile']=="Y")	
	{
	$IS_SAVE_PROFILE = true;
	$PROFILE_NAME = mysql_real_escape_string($_REQUEST['profile_name']);
	}
else
	$IS_SAVE_PROFILE = false;
	

if (CModule::IncludeModule('iblock'))
{
	$ielcount = 0;
	$ierrcount = 0;
	$ielUpdcount = 0;
	foreach ($arrRows as $one_row)
	{
	if (empty($one_row[$IDENTIFY]))
		continue;
		
		$el = new CIBlockElement;

		$PROP = array();
		$detail_text = "";
		$preview_text = "";
		$base_price = 0;
		$purchasing_price = 0;
		$arFilter = array();
		
		foreach ($PROP_COLUMNS as $key_col=>$arr_col)
		{
		
		$val_text = $one_row[$key_col];
			if (!empty($val_text))
			{
			foreach ($arr_col as $one_prop)
				{
					switch ($one_prop) {
						case 'FLD_DETAIL_TEXT':
							$detail_text =$val_text;
							break;
						case 'FLD_PREVIEW_TEXT':
							$preview_text = $val_text;
							break;
						case 'FLD_DETAIL_PICTURE':
							$URL = $val_text;
							$detail_picture = CFile::MakeFileArray($URL);
					
						case 'FLD_PREVIEW_PICTURE':
							$preview_picture = CFile::MakeFileArray($val_text);
							break;	
						
						case "FLD_CATALOG_BASE_PRICE":
							$base_price = $val_text;
							break;
						case "FLD_PURCHASING_PRICE":
							$purchasing_price = $val_text;
							break;
						default:
							{$PROP[$one_prop] = $val_text;
							if ($IDENTIFY == $key_col)
								$arFilter["PROPERTY_".$one_prop] = $val_text;
							}	
					}
				
				}
			}
		}
		
		if ($NAME_ID==$IDENTIFY)
			$arFilter["NAME"] = $one_row[$NAME_ID];

			
		$SEARCH_EL_ID="";
		$PRODUCT_ID = "";
		if (!empty($arFilter))
		{	$arFilter["IBLOCK_ID"] = $XLS_IBLOCK_ID;
			$list = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter);
			if ($search_el = $list->GetNext())
				$SEARCH_EL_ID = $search_el["ID"];
		}
		$arLoadProductArray = Array(
		  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
		  "IBLOCK_SECTION_ID" => $SECTION,          // элемент лежит в корне раздела
		  "IBLOCK_ID"      => $XLS_IBLOCK_ID,
		  "PROPERTY_VALUES"=> $PROP,
		  "NAME"           => $one_row[$NAME_ID],
		  "ACTIVE"         => "Y",            // активен
		  "PREVIEW_TEXT"   => $preview_text,
		  "DETAIL_TEXT"    => $detail_text,
		 // "CATALOG_PRICE_".$CATALOG_PRICE_BASE_ID =>$base_price, 
		  "DETAIL_PICTURE" => $detail_picture,
		  "PREVIEW_PICTURE" => $preview_picture
		  
		  );
		
		if ($MAKE_TRANSLIT_CODE)
		{
			$params = Array(
							"max_len" => "75", // обрезает символьный код до 75 символов
							"change_case" => "L", // буквы преобразуютс€ к нижнему регистру
							"replace_space" => "-", // мен€ем пробелы на нижнее подчеркивание
							"replace_other" => "-", // мен€ем левые символы на нижнее подчеркивание
							"delete_repeat_replace" => "true", // удал€ем повтор€ющиес€ нижние подчеркивани€
							"use_google" => "false", // отключаем использование google
						 );
				
				$CODE = CUtil::Translit($one_row[$NAME_ID], "ru", $params);
			$arLoadProductArray["CODE"] = $CODE;
		}
		
		if (!empty($SEARCH_EL_ID))
			{
			
			unset($arLoadProductArray["IBLOCK_SECTION_ID"]);
			$res = $el->Update($SEARCH_EL_ID, $arLoadProductArray, false, false, true);
			$PRODUCT_ID = $SEARCH_EL_ID;
			echo GetMessage("XLS_ELEMENT_UPDATED").$PRODUCT_ID."</br>";
			  $ielUpdcount++;
			}
			
			
		elseif( $PRODUCT_ID = $el->Add($arLoadProductArray, false, false, true))
			{
			echo GetMessage("XLS_ELEMENT_ADDED").$PRODUCT_ID."</br>";
			  $ielcount++;
			}
			
		if (!empty($PRODUCT_ID))
		
		  {	$SKU_ID ="";
			 
			
			
		  
			if ((!empty($SKU_IBLOCK_ID))&&(empty($SEARCH_EL_ID)))
			{	
			
				$PROP[$CML2_LINK_CODE] = $PRODUCT_ID;
				$arLoadSKUArray = Array(
					  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
					  "IBLOCK_SECTION_ID" => $SECTION,          // элемент лежит в корне раздела
					  "IBLOCK_ID"      => $SKU_IBLOCK_ID,
					  "PROPERTY_VALUES"=> $PROP,
					  "NAME"           => $one_row[$NAME_ID],
					  "ACTIVE"         => "Y",            // активен
					  "PREVIEW_TEXT"   => $preview_text,
					  "DETAIL_TEXT"    => $detail_text,
					 // "CATALOG_PRICE_".$CATALOG_PRICE_BASE_ID =>$base_price, 
					  "DETAIL_PICTURE" => $detail_picture,
					  "PREVIEW_PICTURE" => $preview_picture
					  
					  );
				if($SKU_ID = $el->Add($arLoadSKUArray, false, false, true));

			}
			 
			if (!empty($SKU_ID))
				$PRICE_PROD_ID = $SKU_ID;
			else	
				$PRICE_PROD_ID = $PRODUCT_ID;
			

			if ($MCART_IS_SKU)
			{	
			 if ($base_price>0)// добавление базовой цены
			  {
			  CCatalogProduct::Add(array("ID"=>$PRICE_PROD_ID));
				
				$arCatalogFields = Array(
					"PRODUCT_ID" => $PRICE_PROD_ID,
					"CATALOG_GROUP_ID" => $CATALOG_PRICE_BASE_ID,
					"PRICE" => $base_price,
					"CURRENCY" => "RUB",
					"QUANTITY_FROM" => false,
					"QUANTITY_TO" => false
					
				);

					//$obPrice = new CPrice();
					
					$res = CPrice::GetList(
							array(),
							array(
									"PRODUCT_ID" => $PRICE_PROD_ID,
									"CATALOG_GROUP_ID" => $CATALOG_PRICE_BASE_ID
								)
						);

					if ($arr = $res->Fetch())
					{
						CPrice::Update($arr["ID"], $arCatalogFields);
					}
					else
					{
						CPrice::Add($arCatalogFields);
					}
					
					
					/*
					if (!$obPrice->Add($arCatalogFields,false))
					{
					 $e = $APPLICATION->GetException();
					 $str = $e->GetString();
					 echo $str;
					}
					*/
		  
			  }
			  
			  if($purchasing_price>0)// добавление закупочной цены
			  {
			  
				$arPurchFields = array("PURCHASING_PRICE" => $purchasing_price);// зарезервированное количество
				CCatalogProduct::Update($PRICE_PROD_ID, $arPurchFields);
			  }
			 
			}
			  
		  }
		else
		  {echo "Error: ".$el->LAST_ERROR."</br>";
		  $ierrcount++;
		  }
			
			
			
	}
	
echo '</br>'.GetMessage("XLS_MCART_READY")."</br>".
GetMessage("XLS_ADDED_COUNT").$ielcount."</br>".
GetMessage("XLS_UPDATED_COUNT").$ielUpdcount."</br>".
GetMessage("XLS_WITH_ERROR_COUNT").$ierrcount."</br>";
}

?>
<?

if ($IS_SAVE_PROFILE):
	$profile = new CMcartXlsProfile();
	
	$arrData = array(
	"NAME"=>$PROFILE_NAME,
	"IBLOCK_ID"=>$XLS_IBLOCK_ID,
	"SECTION_ID"=>$SECTION,
	"NAME_FIELD"=>$NAME_ID,
	"IDENTIFY" => $IDENTIFY,
	"DATA_ROW"=>$firstRow,
	"TITLE_ROW"=>$titleRow,
	"DIAPAZONE_A"=>$firstColumn,
	"DIAPAZONE_Z"=>$highestColumn,
	"FIELDS"=>$PROP_COLUMNS,
	"SHEET_ID" =>$_SESSION['ARR_XLS_DATA']["SHEET_ID"],
	"LAST_ROW_TYPE"=>$_SESSION['ARR_XLS_DATA']["LAST_ROW_TYPE"],
	"SKU_IBLOCK_ID" => 0,
	"CML2_LINK_CODE" =>0
	);
	
	$profile->Add($arrData);
	
endif;
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>