<?
IncludeModuleLangFile(__FILE__);

class CAllSaleOrderProps
{
	static function DoProcessOrder(&$arOrder, $arOrderPropsValues, &$arErrors, &$arWarnings)
	{
		if (!is_array($arOrderPropsValues))
			$arOrderPropsValues = array();

		$arUser = null;

		$dbOrderProps = CSaleOrderProps::GetList(
			array("SORT" => "ASC"),
			//array("PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"], "ACTIVE" => "Y", "UTIL" => "N"),
			array("PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"], "ACTIVE" => "Y"),
			false,
			false,
			array("ID", "NAME", "TYPE", "IS_LOCATION", "IS_LOCATION4TAX", "IS_PROFILE_NAME", "IS_PAYER", "IS_EMAIL",
				"REQUIED", "SORT", "IS_ZIP", "CODE", "DEFAULT_VALUE")
		);
		while ($arOrderProp = $dbOrderProps->Fetch())
		{
			if (!array_key_exists($arOrderProp["ID"], $arOrderPropsValues))
			{
				$curVal = $arOrderProp["DEFAULT_VALUE"];
				if (strlen($curVal) <= 0)
				{
					if ($arOrderProp["IS_EMAIL"] == "Y" || $arOrderProp["IS_PAYER"] == "Y")
					{
						if ($arUser == null)
						{
							$dbUser = CUser::GetList($by = "ID", $order = "desc", array("ID_EQUAL_EXACT" => $arOrder["USER_ID"]));
							$arUser = $dbUser->Fetch();
						}
						if ($arOrderProp["IS_EMAIL"] == "Y")
							$curVal = is_array($arUser) ? $arUser["EMAIL"] : "";
						elseif ($arOrderProp["IS_PAYER"] == "Y")
							$curVal = is_array($arUser) ? $arUser["NAME"].(strlen($arUser["NAME"]) <= 0 || strlen($arUser["LAST_NAME"]) <= 0 ? "" : " ").$arUser["LAST_NAME"] : "";
					}
				}
			}
			else
			{
				$curVal = $arOrderPropsValues[$arOrderProp["ID"]];
			}

			if ((!is_array($curVal) && strlen($curVal) > 0) || (is_array($curVal) && count($curVal) > 0))
			{
				//if ($arOrderProp["TYPE"] == "SELECT" || $arOrderProp["TYPE"] == "MULTISELECT" || $arOrderProp["TYPE"] == "RADIO")
				if ($arOrderProp["TYPE"] == "SELECT" || $arOrderProp["TYPE"] == "RADIO")
				{
					$arVariants = array();
					$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC", "NAME" => "ASC"),
						array("ORDER_PROPS_ID" => $arOrderProp["ID"]),
						false,
						false,
						array("*")
					);
					while ($arVariant = $dbVariants->Fetch())
						$arVariants[] = $arVariant["VALUE"];

					if (!is_array($curVal))
						$curVal = array($curVal);

					$arKeys = array_keys($curVal);
					foreach ($arKeys as $k)
					{
						if (!in_array($curVal[$k], $arVariants))
							unset($curVal[$k]);
					}

					if ($arOrderProp["TYPE"] == "SELECT" || $arOrderProp["TYPE"] == "RADIO")
						$curVal = array_shift($curVal);
				}
				elseif ($arOrderProp["TYPE"] == "LOCATION")
				{
					if (is_array($curVal))
						$curVal = array_shift($curVal);
					$curVal = intval($curVal);
					$dbVariants = CSaleLocation::GetList(
						array(),
						array("ID" => $curVal),
						false,
						false,
						array("ID")
					);
					if ($arVariant = $dbVariants->Fetch())
						$curVal = intval($arVariant["ID"]);
					else
						$curVal = null;
				}
			}

			if ($arOrderProp["TYPE"] == "LOCATION" && ($arOrderProp["IS_LOCATION"] == "Y" || $arOrderProp["IS_LOCATION4TAX"] == "Y"))
			{
				$curVal = intval($curVal);
				if ($arOrderProp["IS_LOCATION"] == "Y")
					$arOrder["DELIVERY_LOCATION"] = $curVal;
				if ($arOrderProp["IS_LOCATION4TAX"] == "Y")
					$arOrder["TAX_LOCATION"] = $curVal;

				if ($curVal <= 0)
					$bErrorField = true;
			}
			elseif ($arOrderProp["IS_PROFILE_NAME"] == "Y" || $arOrderProp["IS_PAYER"] == "Y" || $arOrderProp["IS_EMAIL"] == "Y" || $arOrderProp["IS_ZIP"] == "Y")
			{
				$curVal = trim($curVal);
				if ($arOrderProp["IS_PROFILE_NAME"] == "Y")
					$arOrder["PROFILE_NAME"] = $curVal;
				if ($arOrderProp["IS_PAYER"] == "Y")
					$arOrder["PAYER_NAME"] = $curVal;
				if ($arOrderProp["IS_ZIP"] == "Y")
					$arOrder["DELIVERY_LOCATION_ZIP"] = $curVal;
				if ($arOrderProp["IS_EMAIL"] == "Y")
				{
					$arOrder["USER_EMAIL"] = $curVal;
					if (!check_email($curVal))
						$arWarnings[] = array("CODE" => "PARAM", "TEXT" => str_replace(array("#EMAIL#", "#NAME#"), array(htmlspecialcharsbx($curVal), htmlspecialcharsbx($arOrderProp["NAME"])), GetMessage("SALE_GOPE_WRONG_EMAIL")));
				}

				if (strlen($curVal) <= 0)
					$bErrorField = true;
			}
			elseif ($arOrderProp["REQUIED"] == "Y")
			{
				if ($arOrderProp["TYPE"] == "TEXT" || $arOrderProp["TYPE"] == "TEXTAREA" || $arOrderProp["TYPE"] == "RADIO" || $arOrderProp["TYPE"] == "SELECT" || $arOrderProp["TYPE"] == "CHECKBOX")
				{
					if (strlen($curVal) <= 0)
						$bErrorField = true;
				}
				elseif ($arOrderProp["TYPE"] == "LOCATION")
				{
					if (intval($curVal) <= 0)
						$bErrorField = true;
				}
				elseif ($arOrderProp["TYPE"] == "MULTISELECT")
				{
					//if (!is_array($curVal) || count($curVal) <= 0)
					if (strlen($curVal) <= 0)
						$bErrorField = true;
				}
			}

			if ($bErrorField) 
			{
				$arWarnings[] = array("CODE" => "PARAM", "TEXT" => str_replace("#NAME#", htmlspecialcharsbx($arOrderProp["NAME"]), GetMessage("SALE_GOPE_FIELD_EMPTY")));
				$bErrorField = false;
			}

			$arOrder["ORDER_PROP"][$arOrderProp["ID"]] = $curVal;
		}
	}

	static function DoSaveOrderProps($orderId, $personTypeId, $arOrderProps, &$arErrors)
	{
		$arIDs = array();
		$dbResult = CSaleOrderPropsValue::GetList(
			array(),
			//array("ORDER_ID" => $orderId, "PROP_UTIL" => "N"),
			array("ORDER_ID" => $orderId),
			false,
			false,
			array("ID", "ORDER_PROPS_ID")
		);
		while ($arResult = $dbResult->Fetch())
			$arIDs[$arResult["ORDER_PROPS_ID"]] = $arResult["ID"];

		$dbOrderProperties = CSaleOrderProps::GetList(
			array("SORT" => "ASC"),
			//array("PERSON_TYPE_ID" => $personTypeId, "ACTIVE" => "Y", "UTIL" => "N"),
			array("PERSON_TYPE_ID" => $personTypeId, "ACTIVE" => "Y"),
			false,
			false,
			array("ID", "TYPE", "NAME", "CODE", "USER_PROPS", "SORT")
		);
		while ($arOrderProperty = $dbOrderProperties->Fetch())
		{
			$curVal = $arOrderProps[$arOrderProperty["ID"]];
			if (($arOrderProperty["TYPE"] == "MULTISELECT") && is_array($curVal))
				$curVal = implode(",", $curVal);

			if (strlen($curVal) > 0)
			{
				$arFields = array(
					"ORDER_ID" => $orderId,
					"ORDER_PROPS_ID" => $arOrderProperty["ID"],
					"NAME" => $arOrderProperty["NAME"],
					"CODE" => $arOrderProperty["CODE"],
					"VALUE" => $curVal
				);
				
				if (array_key_exists($arOrderProperty["ID"], $arIDs))
				{
					CSaleOrderPropsValue::Update($arIDs[$arOrderProperty["ID"]], $arFields);
					unset($arIDs[$arOrderProperty["ID"]]);
				}
				else
				{
					CSaleOrderPropsValue::Add($arFields);
				}
			}
		}

		foreach ($arIDs as $id)
			CSaleOrderPropsValue::Delete($id);
	}

	function GetByID($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		$strSql =
			"SELECT * ".
			"FROM b_sale_order_props ".
			"WHERE ID = ".$ID."";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}

	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		global $DB, $USER;

		if (is_set($arFields, "PERSON_TYPE_ID") && $ACTION != "ADD")
			UnSet($arFields["PERSON_TYPE_ID"]);

		if ((is_set($arFields, "PERSON_TYPE_ID") || $ACTION=="ADD") && IntVal($arFields["PERSON_TYPE_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGOP_EMPTY_PERS_TYPE"), "ERROR_NO_PERSON_TYPE");
			return false;
		}
		if ((is_set($arFields, "NAME") || $ACTION=="ADD") && strlen($arFields["NAME"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGOP_EMPTY_PROP_NAME"), "ERROR_NO_NAME");
			return false;
		}
		if ((is_set($arFields, "TYPE") || $ACTION=="ADD") && strlen($arFields["TYPE"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGOP_EMPTY_PROP_TYPE"), "ERROR_NO_TYPE");
			return false;
		}

		if (is_set($arFields, "REQUIED") && $arFields["REQUIED"]!="Y")
			$arFields["REQUIED"]="N";
		if (is_set($arFields, "USER_PROPS") && $arFields["USER_PROPS"]!="Y")
			$arFields["USER_PROPS"]="N";
		if (is_set($arFields, "IS_LOCATION") && $arFields["IS_LOCATION"]!="Y")
			$arFields["IS_LOCATION"]="N";
		if (is_set($arFields, "IS_LOCATION4TAX") && $arFields["IS_LOCATION4TAX"]!="Y")
			$arFields["IS_LOCATION4TAX"]="N";
		if (is_set($arFields, "IS_EMAIL") && $arFields["IS_EMAIL"]!="Y")
			$arFields["IS_EMAIL"]="N";
		if (is_set($arFields, "IS_PROFILE_NAME") && $arFields["IS_PROFILE_NAME"]!="Y")
			$arFields["IS_PROFILE_NAME"]="N";
		if (is_set($arFields, "IS_PAYER") && $arFields["IS_PAYER"]!="Y")
			$arFields["IS_PAYER"]="N";
		if (is_set($arFields, "IS_FILTERED") && $arFields["IS_FILTERED"]!="Y")
			$arFields["IS_FILTERED"]="N";
		if (is_set($arFields, "IS_ZIP") && $arFields["IS_ZIP"]!="Y")
			$arFields["IS_ZIP"]="N";

		if (is_set($arFields, "IS_LOCATION") && is_set($arFields, "TYPE") && $arFields["IS_LOCATION"]=="Y" && $arFields["TYPE"]!="LOCATION")
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGOP_WRONG_PROP_TYPE"), "ERROR_WRONG_TYPE1");
			return false;
		}
		if (is_set($arFields, "IS_LOCATION4TAX") && is_set($arFields, "TYPE") && $arFields["IS_LOCATION4TAX"]=="Y" && $arFields["TYPE"]!="LOCATION")
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGOP_WRONG_PROP_TYPE"), "ERROR_WRONG_TYPE2");
			return false;
		}

		if ((is_set($arFields, "PROPS_GROUP_ID") || $ACTION=="ADD") && IntVal($arFields["PROPS_GROUP_ID"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGOP_EMPTY_PROP_GROUP"), "ERROR_NO_GROUP");
			return false;
		}

		if (is_set($arFields, "PERSON_TYPE_ID"))
		{
			if (!($arPersonType = CSalePersonType::GetByID($arFields["PERSON_TYPE_ID"])))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["PERSON_TYPE_ID"], GetMessage("SKGOP_NO_PERS_TYPE")), "ERROR_NO_PERSON_TYPE");
				return false;
			}
		}

		return True;
	}

	function Update($ID, $arFields)
	{
		global $DB;
		
		$ID = IntVal($ID);

		if (!CSaleOrderProps::CheckFields("UPDATE", $arFields, $ID)) return false;

		$strUpdate = $DB->PrepareUpdate("b_sale_order_props", $arFields);

		$strSql = "UPDATE b_sale_order_props SET ".$strUpdate." WHERE ID = ".$ID."";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;

		$ID = IntVal($ID);

		$DB->Query("DELETE FROM b_sale_order_props_variant WHERE ORDER_PROPS_ID = ".$ID."", true);
		$DB->Query("UPDATE b_sale_order_props_value SET ORDER_PROPS_ID = NULL WHERE ORDER_PROPS_ID = ".$ID."", true);
		$DB->Query("DELETE FROM b_sale_user_props_value WHERE ORDER_PROPS_ID = ".$ID."", true);
		CSaleOrderUserProps::ClearEmpty();

		return $DB->Query("DELETE FROM b_sale_order_props WHERE ID = ".$ID."", true);
	}

	function GetRealValue($propertyID, $propertyCode, $propertyType, $value, $lang = false)
	{
		$propertyID = IntVal($propertyID);
		$propertyCode = Trim($propertyCode);
		$propertyType = Trim($propertyType);

		if ($lang === false)
			$lang = LANGUAGE_ID;

		$arResult = array();

		$curKey = ((strlen($propertyCode) > 0) ? $propertyCode : $propertyID);

		if ($propertyType == "SELECT" || $propertyType == "RADIO")
		{
			$arValue = CSaleOrderPropsVariant::GetByValue($propertyID, $value);
			$arResult[$curKey] = $arValue["NAME"];
		}
		elseif ($propertyType == "MULTISELECT")
		{
			$curValue = "";

			if (!is_array($value))
				$value = explode(",", $value);

			for ($i = 0; $i < count($value); $i++)
			{
				if ($arValue1 = CSaleOrderPropsVariant::GetByValue($propertyID, $value[$i]))
				{
					if ($i > 0)
						$curValue .= ",";
					$curValue .= $arValue1["NAME"];
				}
			}

			$arResult[$curKey] = $curValue;
		}
		elseif ($propertyType == "LOCATION")
		{
			$arValue = CSaleLocation::GetByID($value, $lang);
			$curValue = $arValue["COUNTRY_NAME"].((strlen($arValue["COUNTRY_NAME"])<=0 || strlen($arValue["CITY_NAME"])<=0) ? "" : " - ").$arValue["CITY_NAME"];
			$arResult[$curKey] = $curValue;
			$arResult[$curKey."_COUNTRY"] = $arValue["COUNTRY_NAME"];
			$arResult[$curKey."_CITY"] = $arValue["CITY_NAME"];
		}
		else
		{
			$arResult[$curKey] = $value;
		}

		return $arResult;
	}
}
?>