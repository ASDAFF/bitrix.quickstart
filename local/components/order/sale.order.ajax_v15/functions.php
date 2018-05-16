<?if(!Defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Sale\Location;

function getOrderPropFormated($arProperties, $arResult, &$arUserResult, &$arDeleteFieldLocation = array())
{
	global $USER;

	$curVal = $arUserResult["ORDER_PROP"][$arProperties["ID"]];
	$curLocation = false;
	static $propertyGroupID = 0;
	static $propertyUSER_PROPS = "";

	// take data from user profile
	if ($arUserResult["PROFILE_CHANGE"] == "Y"
		&& intval($arUserResult["PROFILE_ID"]) > 0
		&& !($arResult["HAVE_PREPAYMENT"]
		&& $arUserResult["PROFILE_DEFAULT"] == "Y"
		&& !empty($arResult["PREPAY_ORDER_PROPS"][$arProperties["CODE"]])))
	{
		$dbUserPropsValues = CSaleOrderUserPropsValue::GetList(
			array("SORT" => "ASC"),
			array(
				"USER_PROPS_ID" => $arUserResult["PROFILE_ID"],
				"ORDER_PROPS_ID" => $arProperties["ID"],
				"USER_ID" => intval($USER->GetID()),
			),
			false,
			false,
			array("VALUE", "PROP_TYPE", "VARIANT_NAME", "SORT", "ORDER_PROPS_ID")
		);
		if ($arUserPropsValues = $dbUserPropsValues->Fetch())
		{
			$valueTmp = "";
			if ($arUserPropsValues["PROP_TYPE"] == "MULTISELECT")
			{
				$arUserPropsValues["VALUE"] = explode(",", $arUserPropsValues["VALUE"]);
			}
			$curVal = $arUserPropsValues["VALUE"];

			if(CSaleLocation::isLocationProMigrated())
			{
				// SPIKE: map here LOCATION CODE to ID, kz now we keep CODE, not ID in the DB
				if($arProperties['TYPE'] == 'LOCATION')
				{
					$curVal = CSaleLocation::getLocationIDbyCODE($curVal);
				}
			}
		}
	}
	elseif($arUserResult["PROFILE_CHANGE"] == "Y" && intval($arUserResult["PROFILE_ID"]) <= 0)
	{
		if (isset($curVal))
			unset($curVal);
	}
	elseif(isset($arUserResult["ORDER_PROP"][$arProperties["ID"]]))
		$curVal = $arUserResult["ORDER_PROP"][$arProperties["ID"]];
	elseif($arResult["HAVE_PREPAYMENT"] && !empty($arResult["PREPAY_ORDER_PROPS"][$arProperties["CODE"]]))
	{
		$curVal = $arResult["PREPAY_ORDER_PROPS"][$arProperties["CODE"]];
		if($arProperties["TYPE"] == "LOCATION")
			$curLocation = $curVal;
	}

	if (intval($_REQUEST["NEW_LOCATION_".$arProperties["ID"]]) > 0)
		$curVal = intval($_REQUEST["NEW_LOCATION_".$arProperties["ID"]]);

	$arProperties["FIELD_NAME"] = "ORDER_PROP_".$arProperties["ID"];

	if(strlen($arProperties["CODE"]) > 0)
		$arProperties["FIELD_ID"] = "ORDER_PROP_".$arProperties["CODE"];
	else
		$arProperties["FIELD_ID"] = "ORDER_PROP_".$arProperties["ID"];

	if (intval($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID || $propertyUSER_PROPS != $arProperties["USER_PROPS"])
		$arProperties["SHOW_GROUP_NAME"] = "Y";

	$propertyGroupID = $arProperties["PROPS_GROUP_ID"];
	$propertyUSER_PROPS = $arProperties["USER_PROPS"];

	if ($arProperties["REQUIED"]=="Y" || $arProperties["IS_EMAIL"]=="Y" || $arProperties["IS_PROFILE_NAME"]=="Y" || $arProperties["IS_LOCATION"]=="Y" || $arProperties["IS_LOCATION4TAX"]=="Y" || $arProperties["IS_PAYER"]=="Y" || $arProperties["IS_ZIP"]=="Y")
		$arProperties["REQUIED_FORMATED"]="Y";

	if ($arProperties["TYPE"] == "CHECKBOX")
	{
		if ($curVal=="Y" || !isset($curVal) && $arProperties["DEFAULT_VALUE"]=="Y")
		{
			$arProperties["CHECKED"] = "Y";
			$arProperties["VALUE_FORMATED"] = GetMessage("SOA_Y");
		}
		else
			$arProperties["VALUE_FORMATED"] = GetMessage("SOA_N");

		$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 30);
	}
	elseif ($arProperties["TYPE"] == "TEXT")
	{
		if (strlen($curVal) <= 0)
		{
			if(strlen($arProperties["DEFAULT_VALUE"])>0 && !isset($curVal))
				$arProperties["VALUE"] = $arProperties["DEFAULT_VALUE"];
			elseif ($arProperties["IS_EMAIL"] == "Y")
				$arProperties["VALUE"] = $USER->GetEmail();
			elseif ($arProperties["IS_PAYER"] == "Y")
			{
				//$arProperties["VALUE"] = $USER->GetFullName();
				$rsUser = CUser::GetByID($USER->GetID());
				$fio = "";
				if ($arUser = $rsUser->Fetch())
				{
					$fio = CUser::FormatName(CSite::GetNameFormat(false), array("NAME" => $arUser["NAME"], "LAST_NAME" => $arUser["LAST_NAME"], "SECOND_NAME" => $arUser["SECOND_NAME"]), false, false);
				}
				$arProperties["VALUE"] = $fio;
			}

			$arProperties["SOURCE"] = 'DEFAULT';
		}
		else
		{
			$arProperties["VALUE"] = $curVal;
			$arProperties["SOURCE"] = 'FORM';
		}

		//select ZIP for LOCATION
		if ($arProperties["IS_ZIP"] == "Y" && $arUserResult["PROFILE_CHANGE"] == "N")
		{
			$dbPropertiesLoc = CSaleOrderProps::GetList(
					array("ID" => "DESC"),
					array(
						"PERSON_TYPE_ID" => $arUserResult["PERSON_TYPE_ID"],
						"ACTIVE" => "Y",
						"UTIL" => "N",
						"IS_LOCATION" => "Y"
						),
					false,
					false,
					array("ID")
				);
			$arPropertiesLoc = $dbPropertiesLoc->Fetch();

			if ($arPropertiesLoc["ID"] > 0)
			{
				// proxy location here?

				$arZipLocation = array();
				if(strlen($curVal) > 0)
					$arZipLocation = CSaleLocation::GetByZIP($curVal);

				$rsZipList = CSaleLocation::GetLocationZIP($arUserResult["ORDER_PROP"][$arPropertiesLoc["ID"]]);
				if($arZip = $rsZipList->Fetch())
				{
					if (strlen($arZip["ZIP"]) > 0 && (empty($arZipLocation) || $arZipLocation["ID"] != $arUserResult["ORDER_PROP"][$arPropertiesLoc["ID"]]))
						$arProperties["VALUE"] = $arZip["ZIP"];
				}
			}
		}

		if ($arProperties["IS_ZIP"]=="Y")
			$arUserResult["DELIVERY_LOCATION_ZIP"] = $arProperties["VALUE"];


		$arProperties["VALUE"] = htmlspecialcharsEx($arProperties["VALUE"]);
		$arProperties["VALUE_FORMATED"] = $arProperties["VALUE"];

	}
	elseif ($arProperties["TYPE"] == "SELECT")
	{
		$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 1);
		$dbVariants = CSaleOrderPropsVariant::GetList(
				array("SORT" => "ASC", "NAME" => "ASC"),
				array("ORDER_PROPS_ID" => $arProperties["ID"]),
				false,
				false,
				array("*")

		);
		$flagDefault = "N";
		$nameProperty = "";
		while ($arVariants = $dbVariants->GetNext())
		{
			if ($flagDefault == "N" && $nameProperty == "")
			{
				$nameProperty = $arVariants["NAME"];
			}
			if (($arVariants["VALUE"] == $curVal) || ((!isset($curVal) || $curVal == "") && ($arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"])))
			{
				$arVariants["SELECTED"] = "Y";
				$arProperties["VALUE_FORMATED"] = $arVariants["NAME"];
				$flagDefault = "Y";
			}
			$arProperties["VARIANTS"][] = $arVariants;
		}
		if ($flagDefault == "N")
		{
			$arProperties["VARIANTS"][0]["SELECTED"]= "Y";
			$arProperties["VARIANTS"][0]["VALUE_FORMATED"] = $nameProperty;
		}
	}
	elseif ($arProperties["TYPE"] == "MULTISELECT")
	{
		$arProperties["FIELD_NAME"] = "ORDER_PROP_".$arProperties["ID"].'[]';
		$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 5);
		$arDefVal = explode(",", $arProperties["DEFAULT_VALUE"]);
		$countDefVal = count($arDefVal);
		for ($i = 0; $i < $countDefVal; $i++)
			$arDefVal[$i] = Trim($arDefVal[$i]);

		$dbVariants = CSaleOrderPropsVariant::GetList(
				array("SORT" => "ASC"),
				array("ORDER_PROPS_ID" => $arProperties["ID"]),
				false,
				false,
				array("*")
			);
		$i = 0;
		while ($arVariants = $dbVariants->GetNext())
		{
			if ((is_array($curVal) && in_array($arVariants["VALUE"], $curVal)) || (!isset($curVal) && in_array($arVariants["VALUE"], $arDefVal)))
			{
				$arVariants["SELECTED"] = "Y";
				if ($i > 0)
					$arProperties["VALUE_FORMATED"] .= ", ";
				$arProperties["VALUE_FORMATED"] .= $arVariants["NAME"];
				$i++;
			}
			$arProperties["VARIANTS"][] = $arVariants;
		}
	}
	elseif ($arProperties["TYPE"] == "TEXTAREA")
	{
		$arProperties["SIZE2"] = ((intval($arProperties["SIZE2"]) > 0) ? $arProperties["SIZE2"] : 4);
		$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 40);
		$arProperties["VALUE"] = htmlspecialcharsEx(isset($curVal) ? $curVal : $arProperties["DEFAULT_VALUE"]);
		$arProperties["VALUE_FORMATED"] = $arProperties["VALUE"];
	}
	elseif ($arProperties["TYPE"] == "LOCATION")
	{
		if(CSaleLocation::isLocationProEnabled())
		{
			// default value for location is always kept in CODE
			if(!strlen($curVal) && strlen($arProperties["DEFAULT_VALUE"]))
				$curVal = CSaleLocation::getLocationIDbyCODE($arProperties["DEFAULT_VALUE"]);

			$arProperties["VALUE"] = $curVal;

			$arUserResult["DELIVERY_LOCATION"] = $arProperties["VALUE"];

			if($arProperties["IS_LOCATION4TAX"]=="Y")
				$arUserResult["TAX_LOCATION"] = $arProperties["VALUE"];

			$arDeleteFieldLocation[$arProperties["ID"]] = $arProperties["INPUT_FIELD_LOCATION"];

			$arProperties["VARIANTS"][] = array('ID' => $curVal, 'SELECTED' => 'Y'); // dumb
		}
		else
		{
			//enable location town text
			if ($_REQUEST["is_ajax_post"] == "Y" && $arProperties["IS_LOCATION"] == "Y" && intval($arProperties["INPUT_FIELD_LOCATION"]) > 0 && isset($_REQUEST["ORDER_PROP_".$arProperties["ID"]]))
			{
				$rsLocationsList = CSaleLocation::GetList(
					array(),
					array("ID" => $curVal),
					false,
					false,
					array("ID", "CITY_ID")
				);
				$arCity = $rsLocationsList->GetNext();

				if (intval($arCity["CITY_ID"]) <= 0)
					unset($arDeleteFieldLocation[$arProperties["ID"]]);
				else
					$arDeleteFieldLocation[$arProperties["ID"]] = $arProperties["INPUT_FIELD_LOCATION"];
			}
			elseif ($arProperties["IS_LOCATION"] == "Y" && intval($arProperties["INPUT_FIELD_LOCATION"]) > 0)
			{
				$arDeleteFieldLocation[$arProperties["ID"]] = $arProperties["INPUT_FIELD_LOCATION"];
			}

			$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 1);
			$dbVariants = CSaleLocation::GetList(
					array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"),
					array("LID" => LANGUAGE_ID),
					false,
					false,
					array("ID", "COUNTRY_NAME", "CITY_NAME", "SORT", "COUNTRY_NAME_LANG", "CITY_NAME_LANG")
				);
			while ($arVariants = $dbVariants->GetNext())
			{
				if (intval($arVariants["ID"]) == intval($curVal) || (!isset($curVal) && intval($arVariants["ID"]) == intval($arProperties["DEFAULT_VALUE"])) || (strlen($curLocation) > 0 && ToUpper($curLocation) == ToUpper($arVariants["CITY_NAME"])))
				{
					$arVariants["SELECTED"] = "Y";
					$arProperties["VALUE_FORMATED"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];
					$arProperties["VALUE"] = $arVariants["ID"];

					if ($arProperties["IS_LOCATION"]=="Y")
						$arUserResult["DELIVERY_LOCATION"] = $arProperties["VALUE"];
					if ($arProperties["IS_LOCATION4TAX"]=="Y")
						$arUserResult["TAX_LOCATION"] = $arProperties["VALUE"];

				}
				$arVariants["NAME"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];
				$arProperties["VARIANTS"][] = $arVariants;
			}
			if(count($arProperties["VARIANTS"]) == 1)
			{
				$arProperties["VALUE"] = $arProperties["VARIANTS"][0]["ID"];
				if($arProperties["IS_LOCATION"]=="Y")
					$arUserResult["DELIVERY_LOCATION"] = $arProperties["VALUE"];
				if($arProperties["IS_LOCATION4TAX"]=="Y")
					$arUserResult["TAX_LOCATION"] = $arProperties["VALUE"];
			}
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
			if ($arVariants["VALUE"] == $curVal || (!isset($curVal) && $arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"]))
			{
				$arVariants["CHECKED"]="Y";
				$arProperties["VALUE_FORMATED"] = $arVariants["NAME"];
			}

			$arProperties["VARIANTS"][] = $arVariants;
		}
	}
	elseif ($arProperties["TYPE"] == "FILE")
	{
		$arProperties["SIZE1"] = intval($arProperties["SIZE1"]);
		$arProperties["VALUE"] = isset($curVal) ? CSaleHelper::getFileInfo($curVal) : $arProperties["DEFAULT_VALUE"];
	}

	return $arProperties;
}

function getPropsInfo($source)
{
	$resultHTML = "";
	foreach ($source["PROPS"] as $val)
		$resultHTML .= str_replace(" ", "&nbsp;", $val["NAME"].": ".$val["VALUE"])."<br />";
	return $resultHTML;
}

function getIblockProps($value, $propData, $arSize = array("WIDTH" => 90, "HEIGHT" => 90), $orderId = 0)
{
	$res = array();

	if ($propData["MULTIPLE"] == "Y")
	{
		$arVal = array();
		if (!is_array($value))
		{
			if (strpos($value, ",") !== false)
				$arVal = explode(",", $value);
			else
				$arVal[] = $value;
		}
		else
			$arVal = $value;

		if (count($arVal) > 0)
		{
			foreach ($arVal as $key => $val)
			{
				if ($propData["PROPERTY_TYPE"] == "F")
					$res[] = getFileData(trim($val), $orderId, $arSize);
				else
					$res[] = array("type" => "value", "value" => $val);
			}
		}
	}
	else
	{
		if ($propData["PROPERTY_TYPE"] == "F")
			$res[] = getFileData($value, $orderId, $arSize);
		else
			$res[] = array("type" => "value", "value" => $value);
	}

	return $res;
}

function getFileData($fileId, $orderId = 0, $arSize = array("WIDTH" => 90, "HEIGHT" => 90))
{
	$res = "";
	$arFile = CFile::GetFileArray($fileId);

	if ($arFile)
	{
		$is_image = CFile::IsImage($arFile["FILE_NAME"], $arFile["CONTENT_TYPE"]);
		if ($is_image)
		{
			$arImgProduct = CFile::ResizeImageGet($arFile, array("width" => $arSize["WIDTH"], "height" => $arSize["HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);

			if (is_array($arImgProduct))
				$res = array("type" => "image", "value" => $arImgProduct["src"]);
		}
		else
			$res = array("type" => "file", "value" => "<a href=".$arFile["SRC"].">".$arFile["ORIGINAL_NAME"]."</a>");
	}

	return $res;
}

?>