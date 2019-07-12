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
	"SHOW_INPUT" => array(
		"NAME" => GetMessage("TP_BST_SHOW_INPUT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	),
	"INPUT_ID" => array(
		"NAME" => GetMessage("TP_BST_INPUT_ID"),
		"TYPE" => "STRING",
		"DEFAULT" => "title-search-input",
	),
	"CONTAINER_ID" => array(
		"NAME" => GetMessage("TP_BST_CONTAINER_ID"),
		"TYPE" => "STRING",
		"DEFAULT" => "title-search",
	),
	"PRICE_CODE" => array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("TP_BST_PRICE_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arPrice,
	),
	"SHOW_PREVIEW" => array(
		"NAME" => GetMessage("TP_BST_SHOW_PREVIEW"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	),
);

if (isset($arCurrentValues['SHOW_PREVIEW']) && 'Y' == $arCurrentValues['SHOW_PREVIEW'])
{
	$arTemplateParameters["PREVIEW_WIDTH"] = array(
		"NAME" => GetMessage("TP_BST_PREVIEW_WIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => 75,
	);
	$arTemplateParameters["PREVIEW_HEIGHT"] = array(
		"NAME" => GetMessage("TP_BST_PREVIEW_HEIGHT"),
		"TYPE" => "STRING",
		"DEFAULT" => 75,
	);
}

?>
