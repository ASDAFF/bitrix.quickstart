<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(CModule::IncludeModule("iblock")){
$PRODUCT_ID = $_POST["id"]; // изменяем элемент с кодом (ID)
$tag = $_POST["tag"];
$el = new CIBlockElement;


$PROP = array();
$db_props = CIBlockElement::GetProperty( 9, $PRODUCT_ID, array("sort" => "asc"), Array("CODE"=>"linked"));
	if($ar_props = $db_props->Fetch())
		$PROP[$ar_props["ID"]]=$ar_props["VALUE"];
$db_props = CIBlockElement::GetProperty( 9, $PRODUCT_ID, array("sort" => "asc"), Array("CODE"=>"value"));
	if($ar_props = $db_props->Fetch())
		$PROP[$ar_props["ID"]]= array("VALUE" => array("TYPE" =>$ar_props["VALUE"]["TYPE"],"TEXT" => $ar_props["VALUE"]["TEXT"]));
$db_props = CIBlockElement::GetProperty( 9, $PRODUCT_ID, array("sort" => "asc"), Array("CODE"=>"limitations"));
	if($ar_props = $db_props->Fetch())
		$PROP[$ar_props["ID"]]= array("VALUE" => array("TYPE" =>$ar_props["VALUE"]["TYPE"],"TEXT" => $ar_props["VALUE"]["TEXT"]));
$db_props = CIBlockElement::GetProperty( 9, $PRODUCT_ID, array("sort" => "asc"), Array("CODE"=>"rating"));
	if($ar_props = $db_props->Fetch())
		$PROP[$ar_props["ID"]]=$ar_props["VALUE"];
$db_props = CIBlockElement::GetProperty( 9, $PRODUCT_ID, array("sort" => "asc"), Array("CODE"=>"helpful"));
	if($ar_props = $db_props->Fetch())
		$PROP[$ar_props["ID"]]=$ar_props["VALUE"];
$db_props = CIBlockElement::GetProperty( 9, $PRODUCT_ID, array("sort" => "asc"), Array("CODE"=>"useless"));
	if($ar_props = $db_props->Fetch())
		$PROP[$ar_props["ID"]]=$ar_props["VALUE"];
	
if($tag=="plus"){
$PROP[36] = $PROP[36] + 1;  // свойству с кодом 12 присваиваем значение "Белый"
echo "span#yes";
}elseif($tag=="minus"){
$PROP[37] = $PROP[37] + 1;       // свойству с кодом 3 присваиваем значение 38
echo "span#no";
}

$arLoadProductArray = Array(
	"ACTIVE" => "Y",
	"PROPERTY_VALUES"=> $PROP,
  );

$res = $el->Update($PRODUCT_ID, $arLoadProductArray);
}
return 123;
?>