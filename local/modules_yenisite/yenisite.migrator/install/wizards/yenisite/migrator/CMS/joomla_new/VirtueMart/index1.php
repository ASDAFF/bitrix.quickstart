<?

/* справочник производителей */

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');


$this->content .= '<b style="color: green">'.GetMessage("VM_STEP5").'</b><br/>';

CModule::IncludeModule("iblock");

$sec = new CIBlockSection;
$el = new CIBlockElement;

$res = CIBlock::GetList(array(), array("CODE" => "Manufacturer"))->GetNext();
if($res)
	$id = $res["ID"];

$query = "SELECT mf_category_id as XML_ID, mf_category_name as NAME, mf_category_desc as DETAIL_TEXT FROM `".$arResult["prefix"]."vm_manufacturer_category`";
$result = mysql_query($query, $link);
while( $arItem = mysql_fetch_assoc($result) )
{
	$arItem["IBLOCK_ID"] = $id;
	$sec->Add($arItem);
}


$query = "SELECT manufacturer_id as XML_ID, mf_name as NAME, mf_desc as DETAIL_TEXT, mf_category_id as IBLOCK_SECTION_ID, mf_url as PROPERTY_URL, mf_email as PROPERTY_EMAIL FROM `".$arResult["prefix"]."vm_manufacturer`";
$result = mysql_query($query, $link);
while( $arItem = mysql_fetch_assoc($result) )
{
	$res = CIBlockSection::GetList(array(), array("XML_ID" => $arItem["IBLOCK_SECTION_ID"]))->GetNext();
	$arItem["IBLOCK_SECTION_ID"] = $res["ID"];
	$arItem["IBLOCK_ID"] = $id;
	$eid = $el->Add($arItem);
	CIBlockElement::SetPropertyValueCode($eid, "EMAIL", $arItem["PROPERTY_EMAIL"]);
	CIBlockElement::SetPropertyValueCode($eid, "URL", $arItem["PROPERTY_URL"]);
}


$step += 1;
$this->content .= $this->ShowHiddenField("step", $step);

?>


