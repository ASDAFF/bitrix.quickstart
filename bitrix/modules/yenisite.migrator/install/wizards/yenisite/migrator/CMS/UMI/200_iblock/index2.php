<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

$iblock = new CIBlock;
$ibp = new CIBlockProperty;
$el = new CIBlockElement;

$query 	= "SELECT COUNT(*) AS CNT FROM `".$arResult["prefix"]."hierarchy` WHERE `type_id`=6";
$count 	= mysql_query($query, $link);
$count = mysql_fetch_assoc($count);

if($left > $count["CNT"])
{		
	$left = 0;
	$right = 10;

	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
}
else
{

	global $USER;
	
	$query 	= "SELECT obj_id, rel, is_active FROM `".$arResult["prefix"]."hierarchy` WHERE `type_id`=6 AND `is_deleted`=0 LIMIT ".$left.", 20";
	
	$resul = mysql_query($query, $link);
	while($arOb = mysql_fetch_assoc($resul))
	{
		$query 	= "SELECT name FROM `".$arResult["prefix"]."objects` WHERE `id`={$arOb['obj_id']}";
		$result = mysql_query($query, $link);
		$result = mysql_fetch_assoc($result);
		$productName = $result['name'];

		$query 	= "SELECT obj_id, rel, alt_name, is_active, is_visible FROM `".$arResult["prefix"]."hierarchy` WHERE `obj_id`={$arOb['obj_id']} ORDER BY rel ASC";
		$result = mysql_query($query, $link);
		$arItem = mysql_fetch_assoc($result);
		
		$elementCode = $arItem['alt_name'];
		$isActive = ($arItem['is_active'] == 1) ? 'Y' : 'N';
		
		$arItemSec = mysql_fetch_assoc($result);

		$sec = false;

		$query 	= "SELECT obj_id, rel, alt_name FROM `".$arResult["prefix"]."hierarchy` WHERE `id`={$arItem['rel']} AND `is_deleted`=0";
		$result = mysql_query($query, $link);
		$result = mysql_fetch_assoc($result);
		$codeIB = $result['alt_name'];
		
		$ib = CIBlock::GetList(array(), array('CODE'=>$codeIB))->Fetch();

		if (!empty($arItemSec))
		{
			$query 	= "SELECT obj_id, alt_name FROM `".$arResult["prefix"]."hierarchy` WHERE `id`={$arItemSec['rel']} AND `is_deleted`=0";
			$result = mysql_query($query, $link);
			$result = mysql_fetch_assoc($result);
			
			$query 	= "SELECT name FROM `".$arResult["prefix"]."objects` WHERE `id`={$result['obj_id']}";
			$result = mysql_query($query, $link);
			$result = mysql_fetch_assoc($result);
			$sec = CIBlockSection::GetList(Array(), Array('IBLOCK_ID' => $ib, 'NAME' => $result['name']))->Fetch();
			$sec = $sec['ID'];
			
			$elementCode = $arItemSec['alt_name'];
			$isActive = ($arItemSec['is_active'] == 1) ? 'Y' : 'N';
			
		}

		$query 	= "SELECT field_id, int_val, varchar_val, text_val, rel_val, tree_val, float_val FROM `".$arResult["prefix"]."object_content_6` WHERE `obj_id`={$arOb['obj_id']} AND ( `varchar_val` NOT LIKE 'NULL' OR `int_val` NOT LIKE 'NULL' OR `text_val` NOT LIKE 'NULL' OR `rel_val` NOT LIKE 'NULL' OR `tree_val` NOT LIKE 'NULL' OR `float_val` NOT LIKE 'NULL' )";
		$result = mysql_query($query, $link);

		$propsAdded = array();

		$propsNotAdded = array('h1', 'meta_keywords', 'title', 'meta_descriptions');

		// Photo data
		$arPhotos	= array();
		
		// For multiply properties
		$adProps 	 = array();
		
		$cur = 'RUB';
		
		$array = Array("IBLOCK_ID" => $ib['ID'], "CODE" => "MORE_PHOTO");
		$arRes = CIBlockProperty::GetList(Array(), $array)->Fetch();
		if (empty($arRes))
		{
			$arFields = array(
				"NAME" => "Фотографии",
				"ACTIVE" => "Y",
				"SORT" => "100",
				"CODE" => "MORE_PHOTO",
				"PROPERTY_TYPE" => "F",
				"MULTIPLE" => "Y",
				"IBLOCK_ID" => $ib['ID'],
			);
			$PropID = $ibp->Add($arFields);
		}

		while($arr = mysql_fetch_assoc($result))
		{
			$query 	= "SELECT name AS code, title, field_type_id FROM `".$arResult["prefix"]."object_fields` WHERE `id`={$arr['field_id']}";
			$res = mysql_query($query, $link);
			$res = mysql_fetch_assoc($res);
			
			if (!empty($arr['varchar_val']) && !empty($res['title']))
			{	
				$array = Array("IBLOCK_ID" => $ib['ID'], "CODE" => $res['code']);
				$arRes = CIBlockProperty::GetList(Array(), $array)->Fetch();
				
				if (!in_array($res['code'], $propsNotAdded) && empty($arRes))
				{
					$arFields = array(
						"NAME" 			=> $res['title'],
						"ACTIVE" 		=> "Y",
						"SORT" 			=> "100",
						"CODE" 			=> $res['code'],
						"PROPERTY_TYPE" => "S",
						// "MULTIPLE" => "Y",
						"IBLOCK_ID" 	=> $ib['ID'],
					);
					
					$PropID = $ibp->Add($arFields);
					if (!$PropID)
						echo $ibp->LAST_ERROR;
				}
				
				if (!in_array($res['code'], $propsNotAdded))
					$propsAdded[$res['code']] = $arr['varchar_val'];
				
				if ($res['code'] == 'price')
					$price = $arr['float_val'];
			}
			
			switch($res['field_type_id'])
			{
				// Photo
				case 9:
				
					$path = $arResult['site'].$arr['text_val'];
					$path = str_replace('www.','',$path);
					if(!substr_count($path, 'http://')) $path = 'http://'.$path;
					
					$arPh = CFile::MakeFileArray($path);
					
					$arPhotos = array('VALUE' => $arPh, "DESCRIPTION" => "");
					
					$adProps['MORE_PHOTO'] = $arPhotos;
				
				break;
			}
			
		} // end while
			
		$arLoadProductArray = Array(
			"IBLOCK_SECTION_ID" => $sec,
			"IBLOCK_ID"     => $ib['ID'],
			"NAME"          => $productName,
			"ACTIVE"        => $isActive,
			"XML_ID" 		=> $arItem['ID'],
			"CODE"			=> $elementCode,
		);

		if($PRODUCT_ID = $el->Add($arLoadProductArray))
		{
			echo "New ID: ".$PRODUCT_ID;
		}
		else
		{
			echo "Error: ".$el->LAST_ERROR.'<br />';
		}

		$el->SetPropertyValuesEx($PRODUCT_ID, $ib['ID'], $adProps);
		$el->SetPropertyValuesEx($PRODUCT_ID, $ib['ID'], $propsAdded);

		if ( !CPrice::SetBasePrice($PRODUCT_ID, $price, $cur) )
			echo 'Error SetBasePrice<br />';
			
		if( !CCatalogProduct::Add(array('ID' => $PRODUCT_ID, 'QUANTITY' => 0)) )
			echo 'Error Add<br />';
			
	} // end while
	
	$left += 20;
	$right += 20;
}

$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>