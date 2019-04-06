<?
	IncludeModuleLangFile(__FILE__);
	if (!CModule::IncludeModule("sale"))
	{
	//	trigger_error("Currency is not installed");
		return false;
	}
	$arFieldsName = Array(
		"ANOTHER"=>GetMessage("TCS_ANOTHER"),
		"USER"=>GetMessage("TCS_USER"),
		"ORDER"=>GetMessage("TCS_ORDER"),
		"PROPERTY"=>GetMessage("TCS_PROPERTY"),
	);
	
	$arFields = Array(
		"USER"=> Array(
			"ID" => GetMessage("TCS_USER_ID"), 
			"USER_FIO" => GetMessage("TCS_FIO"), 
			"LOGIN" => GetMessage("TCS_LOGIN"), 
			"NAME" => GetMessage("TCS_NAME"), 
			"SECOND_NAME" => GetMessage("TCS_SECOND_NAME"), 
			"LAST_NAME" => GetMessage("TCS_LAST_NAME"), 
			"EMAIL" => GetMessage("TCS_EMAIL"), 
			"PERSONAL_MOBILE" => GetMessage("TCS_PERSONAL_MOBILE"), 
			"PERSONAL_PHONE" => GetMessage("TCS_PERSONAL_PHONE"), 
			"WORK_PHONE" => GetMessage("TCS_WORK_PHONE"), 
		),
		"ORDER" => Array(
			"ID" => GetMessage("TCS_ORDER_ID"), 
			"USER_ID" => GetMessage("TCS_ORDER_USER_ID")
		)
	);

	
	
	$arFields["PROPERTY"] = Array();
	$dbPersonType = CSalePersonType::GetList(Array("ID"=>"ASC"), Array("ACTIVE"=>"Y"));
	while($arPersonType = $dbPersonType -> GetNext())
	{
		$arFields["PROPERTY"][$arPersonType["ID"]] = Array();
		$dbOrderProps = CSaleOrderProps::GetList(
			array("SORT" => "ASC", "NAME" => "ASC"),
			array("PERSON_TYPE_ID" => $arPersonType["ID"]),
			false,
			false,
			array("ID", "CODE", "NAME", "TYPE", "SORT")
		);
		while ($arOrderProps = $dbOrderProps->Fetch())
		{
			$arFields["PROPERTY"][$arPersonType["ID"]][$arOrderProps["ID"]] =  $arOrderProps["NAME"];
		}
	}

?>