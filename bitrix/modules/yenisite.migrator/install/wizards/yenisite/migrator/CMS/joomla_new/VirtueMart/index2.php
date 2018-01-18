<?
/*
Выбираем категории
SELECT category_id AS XML_ID, category_name as NAME, category_publish as ACTIVE, category_description as DESCRIPTION, ( SELECT category_parent_id FROM jos_vm_category_xref WHERE category_child_id = category_id ) AS IBLOCK_SECTION_ID FROM `jos_vm_category` ORDER BY IBLOCK_SECTION_ID ASC
Справочник производителей:
Выбираем товары
SELECT product_id as ID, product_name as NAME, product_url as CODE, product_desc as PREVIEW_TEXT, product_full_image as DETAIL_PICTURE, product_publish as ACTIVE, product_weight as PROPERTY_WEIGHT, product_length as PROPERTY_LENGTH, product_height as PROPERTY_HEIGHT, product_width as PROPERTY_WIDTH, product_sku as PROPERY_ARTICUL, ( SELECT manufacturer_id as ID FROM `jos_vm_product_mf_xref` WHERE ID=ID ) as PROPERTY_MANUFACTURER, (SELECT mf_name FROM `jos_vm_manufacturer` WHERE manufacturer_id=PROPERTY_MANUFACTURER ) as PROPERTY_MANUFACTURER_VALUE, (SELECT category_id FROM `jos_vm_product_category_xref` WHERE product_id=ID ) as IBLOCK_SECTION_ID, (SELECT product_price FROM `jos_vm_product_price` WHERE product_id=ID ) as CATALOG_PRICE_1 FROM `jos_vm_product`2
*/

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

$this->content .= '<b style="color: green">'.GetMessage("VM_STEP2").'</b><br/>';

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

	$iblock = new CIBlock;
	$res = CIBlock::GetList(array(), array("CODE" => "catalog"))->GetNext();
	if($res)
		$id = $res["ID"];

	$iblock = new CIBlock;
	$res = CIBlock::GetList(array(), array("CODE" => "Manufacturer"))->GetNext();
	if($res)
		$mid = $res["ID"];

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');

/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."vm_product`";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);
$el = new CIBlockElement;


/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

if($left > $count["CNT"])
{	
	
	$left = 0;
	$right = 10;

	/* Две эти строчки непосредственно завершают шаг и скрипт переходит к следеющему файлу(если он существует) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
}
else
{
	/* Выбираем товары в XML_ID записываем старый идентификатор */
	$query = "SELECT product_id as XML_ID, product_name as NAME, product_url as CODE, product_desc as PREVIEW_TEXT, product_thumb_image as PREVIEW_PICTURE, product_full_image as DETAIL_PICTURE, product_publish as ACTIVE, product_weight as PROPERTY_WEIGHT,product_weight_uom as WEIGHT_UNITS, product_lwh_uom as PROPERTY_LENGTH_UNITS, product_length as PROPERTY_LENGTH, product_height as PROPERTY_HEIGHT, product_width as PROPERTY_WIDTH, product_sku as PROPERTY_ARTICUL, ( SELECT manufacturer_id FROM `".$arResult["prefix"]."vm_product_mf_xref` WHERE product_id=XML_ID LIMIT 1) as PROPERTY_MANUFACTURER, (SELECT mf_name FROM `".$arResult["prefix"]."vm_manufacturer` WHERE manufacturer_id=PROPERTY_MANUFACTURER LIMIT 1) as PROPERTY_MANUFACTURER_VALUE, (SELECT category_id FROM `".$arResult["prefix"]."vm_product_category_xref` WHERE product_id=XML_ID LIMIT 1) as IBLOCK_SECTION_ID, (SELECT product_price FROM `".$arResult["prefix"]."vm_product_price` WHERE product_id=XML_ID LIMIT 1) as CATALOG_PRICE FROM `".$arResult["prefix"]."vm_product` LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);
	while( $arItem = mysql_fetch_assoc($result) )
	{

		$res1 = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $id, "XML_ID" => $arItem["XML_ID"]))->GetNext();
		if($res1)
			continue;

		if($arItem["IBLOCK_SECTION_ID"] > 0)
		{
			$res = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $id, "XML_ID" => $arItem["IBLOCK_SECTION_ID"]))->GetNext();
			$arItem["IBLOCK_SECTION_ID"] = $res["ID"];
		}
		else
			unset($arItem["IBLOCK_SECTION_ID"]);
		
		$arItem["IBLOCK_ID"] = $id;
		$res = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $mid, "XML_ID" => $arItem["PROPERTY_MANUFACTURER"]))->GetNext();
		if($res){
			$arItem["PROPERTY_VALUES"]["MANUFACTURER"] = $res["ID"];

		}
		$arItem["PROPERTY_VALUES"]["LENGTH_UNITS"] = $arItem["PROPERTY_LENGTH_UNITS"]; unset($arItem["PROPERTY_LENGTH_UNITS"]);
		$arItem["PROPERTY_VALUES"]["WEIGHT_UNITS"] = $arItem["PROPERTY_WEIGHT_UNITS"]; unset($arItem["PROPERTY_WEIGHT_UNITS"]);
		$arItem["PROPERTY_VALUES"]["LENGTH"] = $arItem["PROPERTY_LENGTH"]; unset($arItem["PROPERTY_LENGTH"]);
		$arItem["PROPERTY_VALUES"]["HEIGHT"] = $arItem["PROPERTY_HEIGHT"]; unset($arItem["PROPERTY_HEIGHT"]);
		$arItem["PROPERTY_VALUES"]["WIDTH"] = $arItem["PROPERTY_WIDTH"]; unset($arItem["PROPERTY_WIDTH"]);
		$arItem["PROPERTY_VALUES"]["ARTICUL"] = $arItem["PROPERTY_ARTICUL"]; unset($arItem["PROPERTY_ARTICUL"]);
		$arItem["DETAIL_TEXT_TYPE"] = "html";
		$arItem["PREVIEW_TEXT_TYPE"] = "html";

		$image1 = $arResult["site"]."/components/com_virtuemart/shop_image/product/".$arItem["PREVIEW_PICTURE"];
		if(substr_count($image1,"http://")==0) $image1 = "http://".$image1;
		$image2 = $arResult["site"]."/components/com_virtuemart/shop_image/product/".$arItem["DETAIL_PICTURE"];
		if(substr_count($image2,"http://")==0) $image2 = "http://".$image2;

		if($arItem["PREVIEW_PICTURE"]) 
			$arFile = CFile::MakeFileArray($image1);		
		$arItem["PREVIEW_PICTURE"] = $arFile;
		if($arItem["DETAIL_PICTURE"])
			$arFile2 = CFile::MakeFileArray($image2);
		$arItem["DETAIL_PICTURE"] = $arFile2;

		$eid = $el->Add($arItem);

		CPrice::SetBasePrice($eid, $arItem["CATALOG_PRICE"], "RU");


	}

		//die();

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;
}
/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>


