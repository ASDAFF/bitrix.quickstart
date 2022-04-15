<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); 

CModule::IncludeModule('sale');

if (!$USER->IsAuthorized()) { 
    $arResult["PERSON_TYPE_INFO"] = Array();
    $dbPersonType = CSalePersonType::GetList(
         array("SORT" => "ASC", "NAME" => "ASC"),
         array("LID" => SITE_ID, "ACTIVE" => "Y")
    );
    $bFirst = true;
    while ($arPersonType = $dbPersonType->GetNext()) {
        if (IntVal($arResult["POST"]["PERSON_TYPE"]) == IntVal($arPersonType["ID"]) || 
                IntVal($arResult["POST"]["PERSON_TYPE"]) <= 0 && $bFirst)
            $arPersonType["CHECKED"] = "Y";
        $arResult["PERSON_TYPE_INFO"][] = $arPersonType;
        $bFirst = false;
    }
} else { 
    $rsUser = CUser::GetByID($USER->GetID());
    $arResult['USER'] = $rsUser->Fetch(); 
    if($_REQUEST['PERSON_TYPE'])
        $arResult['USER']['PERSON_TYPE'] = CSalePersonType::GetByID($_REQUEST['PERSON_TYPE']);
}



if($arResult["CurrentStep"] == 4 && $arResult["PERSON_TYPE"] == 2){
     
                $arFilter = array("PROPS_GROUP_ID" => 3);
		$dbProperties = CSaleOrderProps::GetList(
				array(
						"GROUP_SORT" => "ASC",
						"PROPS_GROUP_ID" => "ASC",
					  	"SORT" => "ASC",
						"NAME" => "ASC"
					),
				$arFilter,
				false,  
				false,
				array("ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "GROUP_NAME", "GROUP_SORT", "SORT", "USER_PROPS")
			);
		while ($arProperties = $dbProperties->GetNext())
		{
			unset($curVal);
			if(strlen($arResult["POST"]["ORDER_PROP_".$arProperties["ID"]])>0)
				$curVal = $arResult["POST"]["ORDER_PROP_".$arProperties["ID"]];
				
			$arProperties["FIELD_NAME"] = "ORDER_PROP_".$arProperties["ID"];
			if (IntVal($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID || $propertyUSER_PROPS != $arProperties["USER_PROPS"])
				$arProperties["SHOW_GROUP_NAME"] = "Y";
			$propertyGroupID = $arProperties["PROPS_GROUP_ID"];
			$propertyUSER_PROPS = $arProperties["USER_PROPS"];
				
			if ($arProperties["REQUIED"]=="Y" || $arProperties["IS_EMAIL"]=="Y" || $arProperties["IS_PROFILE_NAME"]=="Y" || $arProperties["IS_LOCATION"]=="Y" || $arProperties["IS_LOCATION4TAX"]=="Y" || $arProperties["IS_PAYER"]=="Y")
				$arProperties["REQUIED_FORMATED"]="Y";
				

			if ($arProperties["TYPE"] == "CHECKBOX")
			{
				if ($curVal=="Y" || !isset($curVal) && $arProperties["DEFAULT_VALUE"]=="Y")
					$arProperties["CHECKED"] = "Y";
				$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 30);
			}
			elseif ($arProperties["TYPE"] == "TEXT")
			{
				if (strlen($curVal) <= 0)
				{
					if ($arProperties["IS_EMAIL"] == "Y")
						$arProperties["VALUE"] = $USER->GetEmail();
					elseif ($arProperties["IS_PAYER"] == "Y")
						$arProperties["VALUE"] = $USER->GetFullName();
					elseif(strlen($arProperties["DEFAULT_VALUE"])>0)
						$arProperties["VALUE"] = $arProperties["DEFAULT_VALUE"];
				}
				else
					$arProperties["VALUE"] = $curVal;

			}
			elseif ($arProperties["TYPE"] == "SELECT")
			{
				$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 1);
				$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC"),
						array("ORDER_PROPS_ID" => $arProperties["ID"]),
						false,
						false,
						array("*")
					);
				while ($arVariants = $dbVariants->GetNext())
				{
					
					if ($arVariants["VALUE"] == $curVal || !isset($curVal) && $arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"])
						$arVariants["SELECTED"] = "Y";
					$arProperties["VARIANTS"][] = $arVariants;
				}
			}
			elseif ($arProperties["TYPE"] == "MULTISELECT")
			{
				$arProperties["FIELD_NAME"] = "ORDER_PROP_".$arProperties["ID"].'[]';
				$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 5);
				$arDefVal = explode(",", $arProperties["DEFAULT_VALUE"]);
				for ($i = 0; $i < count($arDefVal); $i++)
					$arDefVal[$i] = Trim($arDefVal[$i]);
				
				$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC"),
						array("ORDER_PROPS_ID" => $arProperties["ID"]),
						false,
						false,
						array("*")
					);
				while ($arVariants = $dbVariants->GetNext())
				{
					if ((is_array($curVal) && in_array($arVariants["VALUE"], $curVal)) || (!isset($curVal) && in_array($arVariants["VALUE"], $arDefVal)))
						$arVariants["SELECTED"] = "Y";
					$arProperties["VARIANTS"][] = $arVariants;
				}
			}
			elseif ($arProperties["TYPE"] == "TEXTAREA")
			{
				$arProperties["SIZE2"] = ((IntVal($arProperties["SIZE2"]) > 0) ? $arProperties["SIZE2"] : 4);
				$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 40);
				$arProperties["VALUE"] = ((isset($curVal)) ? $curVal : $arProperties["DEFAULT_VALUE"]);
			}
			elseif ($arProperties["TYPE"] == "LOCATION")
			{
				$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 1);
				$dbVariants = CSaleLocation::GetList(
						array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"),
						array("LID" => LANGUAGE_ID),
						false,
						false,
						array("ID", "COUNTRY_NAME", "CITY_NAME", "SORT", "COUNTRY_NAME_LANG", "CITY_NAME_LANG")
					);
				while ($arVariants = $dbVariants->GetNext())
				{
					if (IntVal($arVariants["ID"]) == IntVal($curVal) || !isset($curVal) && IntVal($arVariants["ID"]) == IntVal($arProperties["DEFAULT_VALUE"]))
						$arVariants["SELECTED"] = "Y";
					$arVariants["NAME"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];
					$arProperties["VARIANTS"][] = $arVariants;
				}
			}
			elseif ($arProperties["TYPE"] == "RADIO")
			{
				$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC"),
						array("ORDER_PROPS_ID" => $arProperties["ID"]),
						false,
						false,
						array("*")
					);
				while ($arVariants = $dbVariants->GetNext())
				{
					if ($arVariants["VALUE"] == $curVal || (strlen($curVal)<=0 && $arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"]))
						$arVariants["CHECKED"]="Y";
					
					$arProperties["VARIANTS"][] = $arVariants;
				}
			}
			 
		    $arResult["PRINT_PROPS_FORM"][$arProperties["ID"]] = $arProperties;
			 
		}
 
}


if($arResult["ORDER"]['ID']){ 
    $res = CSaleOrderPropsValue::GetOrderProps($arResult["ORDER"]['ID']);
    while($prop = $res->Fetch()){ // prent($prop);
        if($prop['ORDER_PROPS_ID'] == 4 || $prop['ORDER_PROPS_ID'] == 5){
            $arResult['ADDR'] = $prop['VALUE'];
        }
        
        if($prop['ORDER_PROPS_ID'] == 12 || $prop['ORDER_PROPS_ID'] == 11){
            $arResult['PUNKT'] = $prop['VALUE'];
        }
    }
}