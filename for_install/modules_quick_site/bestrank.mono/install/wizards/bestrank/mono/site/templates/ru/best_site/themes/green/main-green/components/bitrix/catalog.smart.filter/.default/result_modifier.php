<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//echo "<pre>";print_r($arResult); echo "</pre>";


$page = $APPLICATION->GetCurPage();
if (($page=="/index.php" || $page=="/") && (in_array("set_filter", array_keys($_GET)) || in_array("del_filter", array_keys($_GET))) ){
	//$arResult["FORM_ACTION"]='/catalog/';
	$url = $APPLICATION->GetCurPageParam("", array()); 
	LocalRedirect("/catalog".$url);
	
}

if(isset($_REQUEST["ajax"]) && $_REQUEST["ajax"] === "y"){
	$arFilter = $this->__component->makeFilter($arParams["FILTER_NAME"]);
	if($arFilter["SECTION_ID"]==0)
		unset($arFilter["SECTION_ID"]);
	//echo "<pre>";print_r($arFilter); echo "</pre>";
	$arResult["ELEMENT_COUNT"] = CIBlockElement::GetList(array(), $arFilter, array(), false);
}

$props_ids = array();
foreach($arResult["ITEMS"] as $k=>$arItem) {
	
}

CModule::IncludeModule("iblock");
$properties = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "USER_TYPE"=>"ElementXmlID"));
while ($prop_fields = $properties->GetNext())
{	
	$props_ids[] = $prop_fields["ID"];
}


$xml_ids = array();
foreach($arResult["ITEMS"] as $k=>$arItem) {
	if(!in_array($k, $props_ids)) continue;
	foreach($arItem["VALUES"] as $val => $ar){
		$xml_ids[$ar["VALUE"]]="";
	}
}

$res=CIBlockElement::GetList(
	array(), array("XML_ID"=>array_keys($xml_ids)), false, false, array("ID", "XML_ID", "NAME")
);
while($ar=$res->GetNext()){
	$xml_ids[$ar["XML_ID"]] = $ar["NAME"];
}

foreach($arResult["ITEMS"] as $k=>$arItem) {
	if(!in_array($k, $props_ids)) continue;
	foreach($arItem["VALUES"] as $val => $ar){
		if(in_array($ar["VALUE"], array_keys($xml_ids)))
			$arResult["ITEMS"][$k]["VALUES"][$val]["LIST_NAME"]=$xml_ids[$ar["VALUE"]];
	}
}

$arrSoloVar=array();

foreach($arResult["ITEMS"] as $k=>$arItem) {
	$arResult["ITEMS"][$k]["SELECTED"]=array();
	foreach($arItem["VALUES"] as $val => $ar){
		if($ar["CHECKED"])
			$arResult["ITEMS"][$k]["SELECTED"][]=$ar["LIST_NAME"] ? $ar["LIST_NAME"] : $ar["VALUE"];
	}

	$arResult["ITEMS"][$k]["JUST_CHECKBOX"]=false;
	if(count($arItem["VALUES"])==1){
		foreach($arItem["VALUES"] as $val => $ar){
			$arrSoloVar[$val]=$k ;
			break;
		}
	}
}

//print_r($arrSoloVar);
$property_enums = CIBlockPropertyEnum::GetList(
	Array(), Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "XML_ID"=>"Y", "ID"=>array_keys($arrSoloVar))
);
while($enum_fields = $property_enums->GetNext())
{
	$arResult["ITEMS"][$arrSoloVar[$enum_fields["ID"]]]["JUST_CHECKBOX"]=true;
}

//echo "<pre>";print_r($arResult); echo "</pre>";

?>

