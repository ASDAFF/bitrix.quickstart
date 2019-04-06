<?
	IncludeModuleLangFile(__FILE__);
	if (!CModule::IncludeModule("sale"))
	{
	//	trigger_error("Currency is not installed");
		return false;
	}
	$arFieldsName = Array(
		"ANOTHER"=>GetMessage("PP_ANOTHER"),
		"USER"=>GetMessage("PP_USER"),
		"ORDER"=>GetMessage("PP_ORDER"),
		"PROPERTY"=>GetMessage("PP_PROPERTY"),
	);
	
	$arFields = Array(
		"USER"=> Array(
			"ID" => GetMessage("PP_USER_ID"), 
			"USER_FIO" => GetMessage("PP_FIO"), 
			"LOGIN" => GetMessage("PP_LOGIN"), 
			"NAME" => GetMessage("PP_NAME"), 
			"SECOND_NAME" => GetMessage("PP_SECOND_NAME"), 
			"LAST_NAME" => GetMessage("PP_LAST_NAME"), 
			"EMAIL" => GetMessage("PP_EMAIL"), 
			"PERSONAL_MOBILE" => GetMessage("PP_PERSONAL_MOBILE"), 
			"PERSONAL_PHONE" => GetMessage("PP_PERSONAL_PHONE"), 
			"WORK_PHONE" => GetMessage("PP_WORK_PHONE"), 
		),
		"ORDER" => Array(
			"ID" => GetMessage("PP_ORDER_ID"), 
			"USER_ID" => GetMessage("PP_ORDER_USER_ID")
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