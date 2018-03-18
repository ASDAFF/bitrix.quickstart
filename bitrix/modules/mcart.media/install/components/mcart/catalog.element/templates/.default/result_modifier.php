<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$cp = $this->__component; 
$PHOTO_IBLOCK_ID = 15;
$SORTAMENT_IBLOCK_ID = 7;
if (is_object($cp))
{
	$cp->arResult['PHOTOS_SECTION'] = array();
	$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), array("IBLOCK_ID"=>$PHOTO_IBLOCK_ID, "SECTION_ID"=>$arResult["PROPERTIES"]["PHOTOS"]["VALUE"]),
false, array('nTopCount' => 4));
	while ($ar_res = $res->GetNext())
	{
		$ob =$ar_res;

$DETAIL_HEIGHT_LIMIT = 200;
$DETAIL_WIDTH_LIMIT = 448;

$PREVIEW_HEIGHT_LIMIT = 50;
$PREVIEW_WIDTH_LIMIT = 110;

$arFile2 = CFile::GetFileArray($ob["PREVIEW_PICTURE"]);
$arFile1 = CFile::GetFileArray($ob["DETAIL_PICTURE"]);

if (($arFile1["HEIGHT"]/$arFile1["WIDTH"])>($DETAIL_HEIGHT_LIMIT/$DETAIL_WIDTH_LIMIT))
{$detail_height = $DETAIL_HEIGHT_LIMIT;
$detail_width = ($DETAIL_HEIGHT_LIMIT*$arFile1["WIDTH"])/$arFile1["HEIGHT"];
}
else
{$detail_width = $DETAIL_WIDTH_LIMIT;
$detail_height = ($DETAIL_WIDTH_LIMIT*$arFile1["HEIGHT"])/$arFile1["WIDTH"];
}


if (($arFile1["HEIGHT"]/$arFile1["WIDTH"])>($PREVIEW_HEIGHT_LIMIT/$PREVIEW_WIDTH_LIMIT))
{$preview_height = $PREVIEW_HEIGHT_LIMIT;
$preview_width = ($PREVIEW_HEIGHT_LIMIT*$arFile1["WIDTH"])/$arFile1["HEIGHT"];
}
else
{$preview_width = $PREVIEW_WIDTH_LIMIT;
$preview_height = ($PREVIEW_WIDTH_LIMIT*$arFile1["HEIGHT"])/$arFile1["WIDTH"];
}


		

		
		$cp->arResult['PHOTOS_SECTION'][] = ARRAY("small"=>array("SRC"=>$arFile2["SRC"],"HEIGHT"=>$preview_height, "WIDTH"=>$preview_width ),
													"big"=>array("SRC"=>$arFile1["SRC"],"HEIGHT"=>$detail_height, "WIDTH"=>$detail_width));
	}	
        $cp->SetResultCacheKeys(array('PHOTOS_SECTION'));
	
	
		$arResult['PHOTOS_SECTION'] = $cp->arResult['PHOTOS_SECTION'];
		
		

$cp->arResult['sortament_options'] = array();
	
	{$arr_price = array();
	$res = CIBlockElement::GetList(array('SORT'=>'ASC'),array("IBLOCK_ID"=>$SORTAMENT_IBLOCK_ID, "PROPERTY_PRODUCT_ID"=>$arResult["ID"]), false, false, array ("ID", "NAME", "IBLOCK_ID", 
							"PROPERTY_PRICE", "PROPERTY_THICKNESS", "PROPERTY_LENGTH", "PROPERTY_WIDTH"));
	while($ar_res = $res->GetNext())
	{	if (isset($ar_res["PROPERTY_PRICE_VALUE"]))
			$arr_price[] = $ar_res["PROPERTY_PRICE_VALUE"];
		if (isset($ar_res["PROPERTY_THICKNESS_VALUE"]))	
			$arr_thickness[] = $ar_res["PROPERTY_THICKNESS_VALUE"];
		if (isset($ar_res["PROPERTY_LENGTH_VALUE"]))	
			$arr_length[] = $ar_res["PROPERTY_LENGTH_VALUE"];
		if (isset($ar_res["PROPERTY_WIDTH_VALUE"]))	
			$arr_width[] = $ar_res["PROPERTY_WIDTH_VALUE"];
		
	}	
	$min_price = min($arr_price);
	$max_price = max($arr_price);
if ($min_price<$max_price)
$str_price=$min_price." - ".$max_price;
else
$str_price = $min_price;

$min_width = min($arr_width);
	$max_width = max($arr_width);
if ($min_width<$max_width)
$str_width=$min_width." - ".$max_width;
else
$str_width = $min_width;

$min_length = min($arr_length);
	$max_length = max($arr_length);
if ($min_length<$max_length)
$str_length=$min_length." - ".$max_length;
else
$str_length = $min_length;

$min_thickness = min($arr_thickness);
	$max_thickness = max($arr_thickness);
if ($min_thickness<$max_thickness)
$str_thickness=$min_thickness." - ".$max_thickness;
else
$str_thickness = $min_thickness;
		
		$cp->arResult['sortament_options']['price'] =$str_price;
		$cp->arResult['sortament_options']['thickness'] =$str_thickness." мм";
		$cp->arResult['sortament_options']['length'] =$str_length." мм";
		$cp->arResult['sortament_options']['width'] =$str_width." мм";
	}
	
	
	
        $cp->SetResultCacheKeys(array('sortament_options'));
	
		$arResult['sortament_options'] = $cp->arResult['sortament_options'];

}
		$arFile = CFile::GetFileArray($arResult["PROPERTIES"]["JPG_SCALE"]["VALUE"]);
		$arResult["DISPLAY_PROPERTIES"]["JPG_SCALE"]["VALUE"] = $arFile["SRC"];
		
		


?>