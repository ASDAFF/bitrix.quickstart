<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

function currVal()
{
	if(CModule::IncludeModule("currency"))
	{  
		$currency = CCurrency::GetList(($by = "name"));
		while($arCurrency = $currency->Fetch())
		{
			$res[$arCurrency["CURRENCY"]] = $arCurrency["FULL_NAME"];
		}
		return $res;
	} 
}

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
		
		"CURRENCY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CURRENCY_NAME"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => currVal()
		),
	
		"CACHE_TIME" => array(
			"DEFAULT" => "".(60*60*24),
		)
	)
);
?>