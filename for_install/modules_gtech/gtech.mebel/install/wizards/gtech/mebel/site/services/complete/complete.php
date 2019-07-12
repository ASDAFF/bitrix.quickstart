<?defined("B_PROLOG_INCLUDED")&&B_PROLOG_INCLUDED or die();

// copy tmp replaced files from tmp to site and delete tmp files
CopyDirFiles(
	WIZARD_SITE_PATH."tmp-".WIZARD_SITE_ID,
	WIZARD_SITE_PATH,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = true
);

//add discounts
CModule::IncludeModule("iblock");
$rsItems = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"catalog","ACTIVE"=>"Y"),false,array("nTopCount"=>"4"),array("ID"));
while($arItem = $rsItems->Fetch()){$arItems[]=$arItem[ID];}
CModule::IncludeModule("catalog");
$result = CCatalogDiscount::Add(array(
	"SITE_ID" => WIZARD_SITE_ID,
	"ACTIVE" => "Y",
	"NAME" => "Праздничная скидка",
	"VALUE_TYPE" => "P",
	"VALUE" => "10",
	"CURRENCY" => "RUB",
	"PRODUCT_IDS" => $arItems
));

//add sales
CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");
$PersonType = CSalePersonType::GetList(array(),array("LID"=>WIZARD_SITE_ID),false,array("nTopCount"=>"1"),array("ID"))->Fetch();
$PaySystem = CSalePaySystem::GetList(array(),array("PERSON_TYPE_ID"=>$PersonType["ID"]),false,array("nTopCount"=>"1"),array("ID"))->Fetch();
for($i=1; $i<=30; $i++){
$rsItems = CIBlockElement::GetList(Array("rand"=>"asc"),array("IBLOCK_CODE"=>"catalog","ACTIVE"=>"Y"),false,array("nTopCount"=>rand(3,5)),array("ID","NAME"));
while($arItem = $rsItems->Fetch()){
	$arBasketFields = array(
		"PRODUCT_ID" => $arItem[ID],
		"PRICE" => "1",
		"CURRENCY" => "RUB",
		"QUANTITY" => "1",
		"LID" => WIZARD_SITE_ID,
		"DELAY" => "N",
		"CAN_BUY" => "Y",
		"NAME" => $arItem[NAME],
	);
	$BasketItemID = CSaleBasket::Add($arBasketFields);
}
$arOrderFields = array(
	"LID" => WIZARD_SITE_ID,
	"PERSON_TYPE_ID" => $PersonType["ID"],
	"PAY_SYSTEM_ID" => $PaySystem["ID"],
	"PAYED" => "Y",
	"STATUS_ID" => "N",
	"PRICE" => 1,
	"CURRENCY" => "RUB",
	"USER_ID" => 1
);
$OrderID = CSaleOrder::Add($arOrderFields);
$SaleBasket = CSaleBasket::OrderBasket($OrderID, $_SESSION["SALE_USER_ID"], WIZARD_SITE_ID);
}
?>