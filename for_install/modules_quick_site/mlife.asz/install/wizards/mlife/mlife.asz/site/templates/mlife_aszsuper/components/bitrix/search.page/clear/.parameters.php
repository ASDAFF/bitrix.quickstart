<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("mlife.asz");
//получаем типы цен для текущего сайта
$price = \Mlife\Asz\PricetipTable::getList(
	array(
		'select' => array('ID','NAME',"SITE_ID"),
		//'filter' => array("LOGIC"=>"OR",array("=SITE_ID"=>SITE_ID),array("=SITE_ID"=>false)),
	)
);
$arPrice = array();
while($arPricedb = $price->Fetch()){
	$arPrice[$arPricedb["ID"]] = "[".$arPricedb["SITE_ID"]."] - ".$arPricedb["NAME"];
}

$arTemplateParameters = array(
	"PRICE_CODE" => array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("TP_BST_PRICE_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arPrice,
	),
);
?>
