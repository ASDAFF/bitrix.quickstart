<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"GROUPS" => array(
		"ALFA_GR_TEMPLATES" => array(
			"NAME" => GetMessage("ALFA_MSG_GROUP_TEMPLATES"),
		),
		"ALFA_GR_TEMPLATES_SOME" => array(
			"NAME" => GetMessage("ALFA_MSG_GROUP_TEMPLATES_SOME"),
		),
		"ALFA_GR_SORTINGS" => array(
			"NAME" => GetMessage("ALFA_MSG_GROUP_SORTINGS"),
		),
		"ALFA_GR_OUTPUT" => array(
			"NAME" => GetMessage("ALFA_MSG_GROUP_OUTPUT"),
		),
	),
	"PARAMETERS" => array(
		"ALFA_ACTION_PARAM_NAME" => array(
			"NAME" => GetMessage("ALFA_MSG_ACTION_PARAM_NAME"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "alfaction",
		),
		"ALFA_ACTION_PARAM_VALUE" => array(
			"NAME" => GetMessage("ALFA_MSG_ACTION_PARAM_VALUE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "alfavalue",
		),
		"ALFA_CHOSE_TEMPLATES_SHOW" => array(
			"NAME" => GetMessage("ALFA_MSG_CHOSE_TEMPLATES_SHOW"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"PARENT" => 'ALFA_GR_TEMPLATES',
			"REFRESH" => "Y",
		),
		"ALFA_SORT_BY_SHOW" => array(
			"NAME" => GetMessage("ALFA_MSG_SORT_BY_SHOW"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"PARENT" => 'ALFA_GR_SORTINGS',
			"REFRESH" => "Y",
		),
		"ALFA_SHORT_SORTER" => array(
			"NAME" => GetMessage("ALFA_MSG_SHORT_SORTER"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"PARENT" => 'ALFA_GR_SORTINGS',
			"REFRESH" => "N",
		),
		"ALFA_OUTPUT_OF_SHOW" => array(
			"NAME" => GetMessage("ALFA_MSG_OUTPUT_OF_SHOW"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"PARENT" => 'ALFA_GR_OUTPUT',
			"REFRESH" => "Y",
		),
	)
);

if($arCurrentValues["ALFA_CHOSE_TEMPLATES_SHOW"]=="Y")
{
	$arComponentParameters["PARAMETERS"]["ALFA_CNT_TEMPLATES"] = array(
		"PARENT" => "ALFA_GR_TEMPLATES",
		"NAME" => GetMessage("ALFA_MSG_CNT_TEMPLATES"),
		"TYPE" => "STRING",
		"REFRESH" => "Y",
	);
	for($i=0;$i<$arCurrentValues["ALFA_CNT_TEMPLATES"];$i++)
	{
		$arComponentParameters["PARAMETERS"]["ALFA_CNT_TEMPLATES_".$i] = array(
			"PARENT" => "ALFA_GR_TEMPLATES_SOME",
			"NAME" => GetMessage("ALFA_MSG_CNT_TEMPLATES_SOME_NAME_")." #".($i+1),
			"TYPE" => "STRING",
		);
		$arComponentParameters["PARAMETERS"]["ALFA_CNT_TEMPLATES_NAME_".$i] = array(
			"PARENT" => "ALFA_GR_TEMPLATES_SOME",
			"NAME" => GetMessage("ALFA_MSG_CNT_TEMPLATES_SOME_TMPL_NAME_")." #".($i+1),
			"TYPE" => "STRING",
		);
	}
	$arComponentParameters["PARAMETERS"]["ALFA_DEFAULT_TEMPLATE"] = array(
		"PARENT" => "ALFA_GR_TEMPLATES",
		"NAME" => GetMessage("ALFA_MSG_DEFAULT_TEMPLATE"),
		"TYPE" => "STRING",
		"REFRESH" => "N",
	);
}

if($arCurrentValues["ALFA_SORT_BY_SHOW"]=="Y")
{
	$arComponentParameters["PARAMETERS"]["ALFA_SORT_BY_NAME"] = array(
		"PARENT" => "ALFA_GR_SORTINGS",
		"NAME" => GetMessage("ALFA_MSG_SORT_BY"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"sort" => GetMessage("ALFA_MSG_SORT_BY_DATE"),
			"name" => GetMessage("ALFA_MSG_SORT_BY_NAME"),
			"catalog_price_1" => GetMessage("ALFA_MSG_SORT_BY_PRICE"),
		),
		"VALUE" => "Y",
		"MULTIPLE" => "Y",
		"ADDITIONAL_VALUES" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["ALFA_SORT_BY_DEFAULT"] = array(
		"PARENT" => "ALFA_GR_SORTINGS",
		"NAME" => GetMessage("ALFA_MSG_SORT_BY_DEFAULT"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"sort_asc" => GetMessage("ALFA_MSG_SORT_BY_DATE1"),
			"sort_desc" => GetMessage("ALFA_MSG_SORT_BY_DATE2"),
			"name_asc" => GetMessage("ALFA_MSG_SORT_BY_NAME1"),
			"name_desc" => GetMessage("ALFA_MSG_SORT_BY_NAME2"),
			"catalog_price_1_asc" => GetMessage("ALFA_MSG_SORT_BY_PRICE1"),
			"catalog_price_1_desc" => GetMessage("ALFA_MSG_SORT_BY_PRICE2"),
		),
		"VALUE" => "Y",
		"MULTIPLE" => "N",
	);
}

if($arCurrentValues["ALFA_OUTPUT_OF_SHOW"]=="Y")
{
	$arComponentParameters["PARAMETERS"]["ALFA_OUTPUT_OF"] = array(
		"PARENT" => "ALFA_GR_OUTPUT",
		"NAME" => GetMessage("ALFA_MSG_OUTPUT_OF"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => array(
			"5" => "5",
			"10" => "10",
			"15" => "15",
			"20" => "20",
			"25" => "25",
			"50" => "50",
			"75" => "75",
			"100" => "100",
		),
		"ADDITIONAL_VALUES" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["ALFA_OUTPUT_OF_DEFAULT"] = array(
		"PARENT" => "ALFA_GR_OUTPUT",
		"NAME" => GetMessage("ALFA_MSG_OUTPUT_OF_DEFAULT"),
		"TYPE" => "STRING",
	);
	$arComponentParameters["PARAMETERS"]["ALFA_OUTPUT_OF_SHOW_ALL"] = array(
		"PARENT" => "ALFA_GR_OUTPUT",
		"NAME" => GetMessage("ALFA_MSG_OUTPUT_OF_SHOW_ALL"),
		"TYPE" => "CHECKBOX",
		"VALUE" => "Y",
	);
}


?>