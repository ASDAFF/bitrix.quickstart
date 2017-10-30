<?if(!Defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Sale\Location;

function getOrderPropFormated($arProperties, $arResult, &$arUserResult, &$arDeleteFieldLocation = array())
{
	global $USER;

	$isProfileChanged = ($arUserResult["PROFILE_CHANGE"] == "Y");

	$isEmptyUserResult = (empty($arUserResult["ORDER_PROP"]));

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

		if ($isProfileChanged || $isEmptyUserResult)
		{
			$arUserResult["ORDER_PROP"][$arProperties["ID"]] = (isset($arProperties["CHECKED"]) && $arProperties["CHECKED"] == "Y" ? 'Y' : "N");

		}

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

		if ($isProfileChanged || $isEmptyUserResult)
		{
			$arUserResult["ORDER_PROP"][$arProperties["ID"]] = $arProperties["VALUE"];
		}

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

				if ($isProfileChanged || $isEmptyUserResult)
				{
					$arUserResult["ORDER_PROP"][$arProperties["ID"]] = $arVariants["NAME"];
				}
			}
			$arProperties["VARIANTS"][] = $arVariants;
		}
		if ($flagDefault == "N")
		{
			$arProperties["VARIANTS"][0]["SELECTED"]= "Y";
			$arProperties["VARIANTS"][0]["VALUE_FORMATED"] = $nameProperty;
			if ($isProfileChanged || $isEmptyUserResult)
			{
				$arUserResult["ORDER_PROP"][$arProperties["ID"]] = $nameProperty;
			}
		}
	}
	elseif ($arProperties["TYPE"] == "MULTISELECT")
	{

		$setValue = array();
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
				$setValue[] = $arVariants["VALUE"];
				$i++;
			}
			$arProperties["VARIANTS"][] = $arVariants;
		}

		if ($isProfileChanged || $isEmptyUserResult)
		{
			$arUserResult["ORDER_PROP"][$arProperties["ID"]] = $setValue;
		}
	}
	elseif ($arProperties["TYPE"] == "TEXTAREA")
	{
		$arProperties["SIZE2"] = ((intval($arProperties["SIZE2"]) > 0) ? $arProperties["SIZE2"] : 4);
		$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 40);
		$arProperties["VALUE"] = htmlspecialcharsEx(isset($curVal) ? $curVal : $arProperties["DEFAULT_VALUE"]);
		$arProperties["VALUE_FORMATED"] = $arProperties["VALUE"];

		if ($isProfileChanged || $isEmptyUserResult)
		{
			$arUserResult["ORDER_PROP"][$arProperties["ID"]] = $arProperties["VALUE"];
		}
	}
	elseif ($arProperties["TYPE"] == "LOCATION")
	{
		if(CSaleLocation::isLocationProEnabled())
		{
			$arProperties["VALUE"] = $curVal;

			// variants
			$locationFound = false;
			$dbVariants = CSaleLocation::GetList(
					array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"),
					array("LID" => LANGUAGE_ID),
					false,
					false,
					array("ID", "COUNTRY_NAME", "CITY_NAME", "SORT", "COUNTRY_NAME_LANG", "CITY_NAME_LANG", "CITY_ID")
				);
			while ($arVariants = $dbVariants->GetNext())
			{
				if (intval($arVariants["ID"]) == intval($curVal) || (!isset($curVal) && intval($arVariants["ID"]) == intval($arProperties["DEFAULT_VALUE"])) || (strlen($curLocation) > 0 && ToUpper($curLocation) == ToUpper($arVariants["CITY_NAME"])))
				{
					// set formatted value
					$arProperties["VALUE_FORMATED"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];

					// location found, set it as DELIVERY_LOCATION and TAX_LOCATION

					$arUserResult["DELIVERY_LOCATION"] = $arVariants['ID'];
					if($arProperties["IS_LOCATION4TAX"]=="Y")
						$arUserResult["TAX_LOCATION"] = $arVariants['ID'];

					$locationFound = $arVariants;
					$arVariants["SELECTED"] = "Y";

					if ($isProfileChanged || $isEmptyUserResult)
					{
						$arUserResult["ORDER_PROP"][$arProperties["ID"]] = $arVariants['ID'];
					}
				}
				$arVariants["NAME"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];

				// save to variants
				$arProperties["VARIANTS"][] = $arVariants;
			}

			// this is not a COUNTRY, REGION or CITY, but must appear in $arProperties["VARIANTS"]
			if(!$locationFound && IntVal($curVal))
			{
				$item = CSaleLocation::GetById($curVal);
				if($item)
				{
					// set formatted value
					$arProperties["VALUE_FORMATED"] = $item["COUNTRY_NAME"].((strlen($item["CITY_NAME"]) > 0) ? " - " : "").$item["CITY_NAME"];

					// location found, set it as DELIVERY_LOCATION and TAX_LOCATION
					$arUserResult["DELIVERY_LOCATION"] = $arProperties["VALUE"];
					if($arProperties["IS_LOCATION4TAX"]=="Y")
						$arUserResult["TAX_LOCATION"] = $arProperties["VALUE"];

					if ($isProfileChanged || $isEmptyUserResult)
					{
						$arUserResult["ORDER_PROP"][$arProperties["ID"]] = $arProperties["VALUE"];
					}
					$locationFound = $item;
					$item['SELECTED'] = 'Y';
					$item['NAME'] = $item["COUNTRY_NAME"].((strlen($item["CITY_NAME"]) > 0) ? " - " : "").$item["CITY_NAME"];

					// save to variants
					$arProperties["VARIANTS"][] = $item;
				}
			}

			if($locationFound)
			{

				// enable location town text
				if(isset($arResult['LOCATION_ALT_PROP_DISPLAY_MANUAL'])) // its an ajax-hit and sale.location.selector.steps is used
				{
					if(intval($arResult['LOCATION_ALT_PROP_DISPLAY_MANUAL'][$arProperties["ID"]])) // user MANUALLY selected "Other location" in the selector
					{
						// Manually chosen, decide...

						//if(intval($locationFound['CITY_ID'])) // we are already selected CITY, no town property needed
						//	$arDeleteFieldLocation[$arProperties["ID"]] = $arProperties["INPUT_FIELD_LOCATION"];
						//else // somewhere above
							unset($arDeleteFieldLocation[$arProperties["ID"]]);
					}
					else
					{
						$arDeleteFieldLocation[$arProperties["ID"]] = $arProperties["INPUT_FIELD_LOCATION"];
					}
				}
				else
				{
					// first load, dont know what to do. default: hide
					$arDeleteFieldLocation[$arProperties["ID"]] = $arProperties["INPUT_FIELD_LOCATION"];
				}

			}
			else
			{
				$arDeleteFieldLocation[$arProperties["ID"]] = $arProperties["INPUT_FIELD_LOCATION"];
			}
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

					if ($isProfileChanged || $isEmptyUserResult)
					{
						$arUserResult["ORDER_PROP"][$arProperties["ID"]] = $arProperties["VALUE"];
					}

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

				if ($isProfileChanged || $isEmptyUserResult)
				{
					$arUserResult["ORDER_PROP"][$arProperties["ID"]] = $arVariants["VALUE"];
				}
			}

			$arProperties["VARIANTS"][] = $arVariants;
		}
	}
	elseif ($arProperties["TYPE"] == "FILE")
	{
		$arProperties["SIZE1"] = intval($arProperties["SIZE1"]);
		$arProperties["VALUE"] = isset($curVal) ? CSaleHelper::getFileInfo($curVal) : $arProperties["DEFAULT_VALUE"];

		if ($isProfileChanged || $isEmptyUserResult)
		{
			$arUserResult["ORDER_PROP"][$arProperties["ID"]] = $arProperties["VALUE"];
		}
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


function getFormatedProperties($personTypeId, &$arResult, &$arUserResult, &$params)
{
	$arDeleteFieldLocation = array();

	$arFilter = array("PERSON_TYPE_ID" => $personTypeId, "ACTIVE" => "Y", "UTIL" => "N", "RELATED" => false);
	if(!empty($params["PROP_".$personTypeId]))
		$arFilter["!ID"] = $params["PROP_".$personTypeId];

	$dbProperties = CSaleOrderProps::GetList(
		array(
			"GROUP_SORT" => "ASC",
			"PROPS_GROUP_ID" => "ASC",
			"USER_PROPS" => "ASC",
			"SORT" => "ASC",
			"NAME" => "ASC"
		),
		$arFilter,
		false,
		false,
		array(
			"ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION",
			"IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "DELIVERY_ID", "PAYSYSTEM_ID", "MULTIPLE",
			"CODE", "GROUP_NAME", "GROUP_SORT", "SORT", "USER_PROPS", "IS_ZIP", "INPUT_FIELD_LOCATION"
		)
	);

	$propIndex = array();

	if(is_array($_REQUEST['LOCATION_ALT_PROP_DISPLAY_MANUAL']))
	{
		foreach($_REQUEST['LOCATION_ALT_PROP_DISPLAY_MANUAL'] as $propId => $switch)
		{
			if(intval($propId))
				$arResult['LOCATION_ALT_PROP_DISPLAY_MANUAL'][intval($propId)] = !!$switch;
		}
	}

	while ($arProperties = $dbProperties->GetNext())
	{
		$arProperties = getOrderPropFormated($arProperties, $arResult, $arUserResult, $arDeleteFieldLocation);

		$flag = $arProperties["USER_PROPS"]=="Y" ? 'Y' : 'N';

		$arResult["ORDER_PROP"]["USER_PROPS_".$flag][$arProperties["ID"]] = $arProperties;
		$propIndex[$arProperties["ID"]] =& $arResult["ORDER_PROP"]["USER_PROPS_".$flag][$arProperties["ID"]];

		$arResult["ORDER_PROP"]["PRINT"][$arProperties["ID"]] = Array("ID" => $arProperties["ID"], "NAME" => $arProperties["NAME"], "VALUE" => $arProperties["VALUE_FORMATED"], "SHOW_GROUP_NAME" => $arProperties["SHOW_GROUP_NAME"]);
	}

	// additional city property process
	foreach($propIndex as $propId => $propDesc)
	{
		if(intval($propDesc['INPUT_FIELD_LOCATION']) && isset($propIndex[$propDesc['INPUT_FIELD_LOCATION']]))
		{
			$propIndex[$propDesc['INPUT_FIELD_LOCATION']]['IS_ALTERNATE_LOCATION_FOR'] = $propId;
			$propIndex[$propId]['CAN_HAVE_ALTERNATE_LOCATION'] = $propDesc['INPUT_FIELD_LOCATION']; // more strict condition rather INPUT_FIELD_LOCATION, check if the property really exists
		}
	}

	foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepOrderProps", true) as $arEvent)
		ExecuteModuleEventEx($arEvent, Array(&$arResult, &$arUserResult, &$params));
	/* Order Props End */

	//delete prop for text location (town)
	if (count($arDeleteFieldLocation) > 0)
	{
		foreach ($arDeleteFieldLocation as $fieldId)
			unset($arResult["ORDER_PROP"]["USER_PROPS_Y"][$fieldId]);
	}
}
?>