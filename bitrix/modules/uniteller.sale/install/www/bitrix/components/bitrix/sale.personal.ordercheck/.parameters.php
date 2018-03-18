<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arYesNo = Array(
	"Y" => GetMessage("SPO_DESC_YES"),
	"N" => GetMessage("SPO_DESC_NO"),
);


$arComponentParameters = array(
	"PARAMETERS" => array(
		"SEF_MODE" => Array(
			"list" => Array(
				"NAME" => GetMessage("SPO_LIST_DESC"),
				"DEFAULT" => "index.php",
				"VARIABLES" => array()
			),
			"detail" => Array(
				"NAME" => GetMessage("SPO_DETAIL_DESC"),
				"DEFAULT" => "order_detail.php?ID=#ID#",
				"VARIABLES" => array("ID")
			),
			"cancel" => Array(
				"NAME" => GetMessage("SPO_CANCEL_DESC"),
				"DEFAULT" => "order_cancel.php?ID=#ID#",
				"VARIABLES" => array("ID")
			),
// UnitellerPlugin add
			'check' => Array(
				'NAME' => GetMessage('SPO_CHECK_DESC'),
				'DEFAULT' => 'order_check.php?ID=#ID#',
				'VARIABLES' => array('ID')
			),
// /UnitellerPlugin add

		),

		"ORDERS_PER_PAGE" => Array(
			"NAME" => GetMessage("SPO_ORDERS_PER_PAGE"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "20",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_PAYMENT" => Array(
			"NAME" => GetMessage("SPO_PATH_TO_PAYMENT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "payment.php",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_BASKET" => Array(
			"NAME" => GetMessage("SPO_PATH_TO_BASKET"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "basket.php",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),

		"SET_TITLE" => Array(),
		"SAVE_IN_SESSION" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SPO_SAVE_IN_SESSION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"NAV_TEMPLATE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SPOL_NAV_TEMPLATE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
	)
);

if(CModule::IncludeModule("sale"))
{
	$dbPerson = CSalePersonType::GetList(Array("SORT" => "ASC", "NAME" => "ASC"));
	while($arPerson = $dbPerson->GetNext())
	{
	
		$arPers2Prop = Array("" => GetMessage("SPO_SHOW_ALL"));
		$bProp = false;
		$dbProp = CSaleOrderProps::GetList(Array("SORT" => "ASC", "NAME" => "ASC"), Array("PERSON_TYPE_ID" => $arPerson["ID"]));
		while($arProp = $dbProp -> GetNext())
		{
			
			$arPers2Prop[$arProp["ID"]] = $arProp["NAME"];
			$bProp = true;
		}
		
		if($bProp)
		{
			$arComponentParameters["PARAMETERS"]["PROP_".$arPerson["ID"]] =  Array(
							"NAME" => GetMessage("SPO_PROPS_NOT_SHOW")." \"".$arPerson["NAME"]."\" (".$arPerson["LID"].")", 
							"TYPE"=>"LIST", "MULTIPLE"=>"Y", 
							"VALUES" => $arPers2Prop,
							"DEFAULT"=>"", 
							"COLS"=>25, 
							"ADDITIONAL_VALUES"=>"N",
							"PARENT" => "BASE",
				);
		}
	}
}
?>
