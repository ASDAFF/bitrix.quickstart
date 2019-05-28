<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/order.php");

class CSaleOrder extends CAllSaleOrder
{
	function Add($arFields)
	{
		global $DB, $USER_FIELD_MANAGER, $CACHE_MANAGER;

		$arFields1 = array();
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1)=="=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}

		if (!CSaleOrder::CheckFields("ADD", $arFields))
			return false;

		foreach(GetModuleEvents("sale", "OnBeforeOrderAdd", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array(&$arFields))===false)
				return false;

		$arInsert = $DB->PrepareInsert("b_sale_order", $arFields);

		if (!array_key_exists("DATE_STATUS", $arFields))
		{
			$arInsert[0] .= ", DATE_STATUS";
			$arInsert[1] .= ", ".$DB->GetNowFunction();
		}
		if (!array_key_exists("DATE_INSERT", $arFields))
		{
			$arInsert[0] .= ", DATE_INSERT";
			$arInsert[1] .= ", ".$DB->GetNowFunction();
		}
		if (!array_key_exists("DATE_UPDATE", $arFields))
		{
			$arInsert[0] .= ", DATE_UPDATE";
			$arInsert[1] .= ", ".$DB->GetNowFunction();
		}

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($arInsert[0])>0)
			{
				$arInsert[0] .= ", ";
				$arInsert[1] .= ", ";
			}
			$arInsert[0] .= $key;
			$arInsert[1] .= $value;
		}

		$strSql =
			"INSERT INTO b_sale_order(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";

		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$ID = IntVal($DB->LastID());

		CSaleOrder::SetAccountNumber($ID);
		CSaleOrderChange::AddRecord($ID, "ORDER_ADDED");

		$USER_FIELD_MANAGER->Update("ORDER", $ID, $arFields);

		foreach(GetModuleEvents("sale", "OnOrderAdd", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID, $arFields));

		if(defined("CACHED_b_sale_order"))
		{
			$CACHE_MANAGER->Read(CACHED_b_sale_order, "sale_orders");
			$CACHE_MANAGER->SetImmediate("sale_orders", true);
		}

		return $ID;
	}

	function Update($ID, $arFields, $bDateUpdate = true)
	{
		global $DB, $USER_FIELD_MANAGER, $CACHE_MANAGER;

		$ID = IntVal($ID);

		$arFields1 = array();
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1)=="=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}

		if (!CSaleOrder::CheckFields("UPDATE", $arFields, $ID))
			return false;

		foreach(GetModuleEvents("sale", "OnBeforeOrderUpdate", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array($ID, &$arFields))===false)
				return false;

		$strUpdate = $DB->PrepareUpdate("b_sale_order", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($strUpdate)>0) $strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}

		//get old fields
		$arOrderOldFields = CSaleOrder::GetByID($ID);

		$strSql =
			"UPDATE b_sale_order SET ".
			"	".$strUpdate." ";
		if($bDateUpdate)
			$strSql .=	",	DATE_UPDATE = ".$DB->GetNowFunction()." ";
		$strSql .=	"WHERE ID = ".$ID." ";

		$res = $DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);

		if (!$res)
			return false;

		$USER_FIELD_MANAGER->Update("ORDER", $ID, $arFields);

		if ($res)
			CSaleOrderChange::AddRecordsByFields($ID, $arOrderOldFields, $arFields);

		unset($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID]);

		foreach(GetModuleEvents("sale", "OnOrderUpdate", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID, $arFields));

		if (isset($arFields["TRACKING_NUMBER"]))
		{
			foreach(GetModuleEvents("sale", "OnTrackingNumberChange", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array($ID, $arFields["TRACKING_NUMBER"]));

			if (strlen($arFields["TRACKING_NUMBER"]) > 0 && $arOrderOldFields["TRACKING_NUMBER"] != $arFields["TRACKING_NUMBER"])
			{
				$accountNumber = (isset($arFields["ACCOUNT_NUMBER"])) ? $arFields["ACCOUNT_NUMBER"] : $arOrderOldFields["ACCOUNT_NUMBER"];
				$userId =  (isset($arFields["USER_ID"])) ? $arFields["USER_ID"] : $arOrderOldFields["USER_ID"];

				$payerName = "";
				$payerEMail = '';
				$dbUser = CUser::GetByID($userId);
				if ($arUser = $dbUser->Fetch())
				{
					if (strlen($payerName) <= 0)
						$payerName = $arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"];
					if (strlen($payerEMail) <= 0)
						$payerEMail = $arUser["EMAIL"];
				}

				$arEmailFields = Array(
					"ORDER_ID" => $accountNumber,
					"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", $arOrderOldFields["LID"]))),
					"ORDER_USER" => $payerName,
					"ORDER_TRACKING_NUMBER" => $arFields["TRACKING_NUMBER"],
					"BCC" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
					"EMAIL" => $payerEMail,
					"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME)
				);

				$event = new CEvent;
				$event->Send("SALE_ORDER_TRACKING_NUMBER", $arOrderOldFields["LID"], $arEmailFields, "N");
			}
		}

		if(defined("CACHED_b_sale_order") && $bDateUpdate && $arFields["UPDATED_1C"] != "Y")
		{
			$CACHE_MANAGER->Read(CACHED_b_sale_order, "sale_orders");
			$CACHE_MANAGER->SetImmediate("sale_orders", true);
		}

		return $ID;
	}

	function PrepareGetListArray($key, &$arFields, &$arPropIDsTmp)
	{
		$propIDTmp = false;
		if (StrPos($key, "PROPERTY_ID_") === 0)
			$propIDTmp = IntVal(substr($key, StrLen("PROPERTY_ID_")));
		elseif (StrPos($key, "PROPERTY_NAME_") === 0)
			$propIDTmp = IntVal(substr($key, StrLen("PROPERTY_NAME_")));
		elseif (StrPos($key, "PROPERTY_VALUE_") === 0)
			$propIDTmp = IntVal(substr($key, StrLen("PROPERTY_VALUE_")));
		elseif (StrPos($key, "PROPERTY_CODE_") === 0)
			$propIDTmp = IntVal(substr($key, StrLen("PROPERTY_CODE_")));
		elseif (StrPos($key, "PROPERTY_VAL_BY_CODE_") === 0)
			$propIDTmp = preg_replace("/[^a-zA-Z0-9_-]/is", "", trim(substr($key, StrLen("PROPERTY_VAL_BY_CODE_"))));

		if (strlen($propIDTmp) > 0 || $propIDTmp > 0)
		{
			if (!in_array($propIDTmp, $arPropIDsTmp))
			{
				$arPropIDsTmp[] = $propIDTmp;

				$arFields["PROPERTY_ID_".$propIDTmp] = array("FIELD" => "SP_".$propIDTmp.".ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_order_props_value SP_".$propIDTmp." ON (SP_".$propIDTmp.".ORDER_PROPS_ID = ".$propIDTmp." AND O.ID = SP_".$propIDTmp.".ORDER_ID)");
				$arFields["PROPERTY_ORDER_PROPS_ID_".$propIDTmp] = array("FIELD" => "SP_".$propIDTmp.".ORDER_PROPS_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_order_props_value SP_".$propIDTmp." ON (SP_".$propIDTmp.".ORDER_PROPS_ID = ".$propIDTmp." AND O.ID = SP_".$propIDTmp.".ORDER_ID)");
				$arFields["PROPERTY_NAME_".$propIDTmp] = array("FIELD" => "SP_".$propIDTmp.".NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_order_props_value SP_".$propIDTmp." ON (SP_".$propIDTmp.".ORDER_PROPS_ID = ".$propIDTmp." AND O.ID = SP_".$propIDTmp.".ORDER_ID)");
				$arFields["PROPERTY_VALUE_".$propIDTmp] = array("FIELD" => "SP_".$propIDTmp.".VALUE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_order_props_value SP_".$propIDTmp." ON (SP_".$propIDTmp.".ORDER_PROPS_ID = ".$propIDTmp." AND O.ID = SP_".$propIDTmp.".ORDER_ID)");
				$arFields["PROPERTY_CODE_".$propIDTmp] = array("FIELD" => "SP_".$propIDTmp.".CODE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_order_props_value SP_".$propIDTmp." ON (SP_".$propIDTmp.".ORDER_PROPS_ID = ".$propIDTmp." AND O.ID = SP_".$propIDTmp.".ORDER_ID)");
				$arFields["PROPERTY_VAL_BY_CODE_".$propIDTmp] = array("FIELD" => "SP_".$propIDTmp.".VALUE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_order_props_value SP_".$propIDTmp." ON (SP_".$propIDTmp.".CODE = '".$propIDTmp."' AND O.ID = SP_".$propIDTmp.".ORDER_ID)");
			}
		}
	}

	function GetList($arOrder = array("ID"=>"DESC"), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
	{
		global $DB, $USER_FIELD_MANAGER;

		if (!is_array($arOrder))
			$arOrder = array('ID' => 'DESC');
		if (!is_array($arFilter))
			$arFilter = array();
		if (!is_array($arSelectFields))
			$arSelectFields = array();

		$obUserFieldsSql = new CUserTypeSQL;
		$obUserFieldsSql->SetEntity("ORDER", "O.ID");
		$obUserFieldsSql->SetSelect($arSelectFields);
		$obUserFieldsSql->SetFilter($arFilter);
		$obUserFieldsSql->SetOrder($arOrder);

		if (array_key_exists("DATE_FROM", $arFilter))
		{
			$val = $arFilter["DATE_FROM"];
			unset($arFilter["DATE_FROM"]);
			$arFilter[">=DATE_INSERT"] = $val;
		}
		if (array_key_exists("DATE_TO", $arFilter))
		{
			$val = $arFilter["DATE_TO"];
			unset($arFilter["DATE_TO"]);
			$arFilter["<=DATE_INSERT"] = $val;
		}
		if (array_key_exists("DATE_INSERT_FROM", $arFilter))
		{
			$val = $arFilter["DATE_INSERT_FROM"];
			unset($arFilter["DATE_INSERT_FROM"]);
			$arFilter[">=DATE_INSERT"] = $val;
		}
		if (array_key_exists("DATE_INSERT_TO", $arFilter))
		{
			$val = $arFilter["DATE_INSERT_TO"];
			unset($arFilter["DATE_INSERT_TO"]);
			$arFilter["<=DATE_INSERT"] = $val;
		}
		if (array_key_exists("DATE_UPDATE_FROM", $arFilter))
		{
			$val = $arFilter["DATE_UPDATE_FROM"];
			unset($arFilter["DATE_UPDATE_FROM"]);
			$arFilter[">=DATE_UPDATE"] = $val;
		}
		if (array_key_exists("DATE_UPDATE_TO", $arFilter))
		{
			$val = $arFilter["DATE_UPDATE_TO"];
			unset($arFilter["DATE_UPDATE_TO"]);
			$arFilter["<=DATE_UPDATE"] = $val;
		}

		if (array_key_exists("DATE_STATUS_FROM", $arFilter))
		{
			$val = $arFilter["DATE_STATUS_FROM"];
			unset($arFilter["DATE_STATUS_FROM"]);
			$arFilter[">=DATE_STATUS"] = $val;
		}
		if (array_key_exists("DATE_STATUS_TO", $arFilter))
		{
			$val = $arFilter["DATE_STATUS_TO"];
			unset($arFilter["DATE_STATUS_TO"]);
			$arFilter["<=DATE_STATUS"] = $val;
		}
		if (array_key_exists("DATE_PAYED_FROM", $arFilter))
		{
			$val = $arFilter["DATE_PAYED_FROM"];
			unset($arFilter["DATE_PAYED_FROM"]);
			$arFilter[">=DATE_PAYED"] = $val;
		}
		if (array_key_exists("DATE_PAYED_TO", $arFilter))
		{
			$val = $arFilter["DATE_PAYED_TO"];
			unset($arFilter["DATE_PAYED_TO"]);
			$arFilter["<=DATE_PAYED"] = $val;
		}
		if (array_key_exists("DATE_ALLOW_DELIVERY_FROM", $arFilter))
		{
			$val = $arFilter["DATE_ALLOW_DELIVERY_FROM"];
			unset($arFilter["DATE_ALLOW_DELIVERY_FROM"]);
			$arFilter[">=DATE_ALLOW_DELIVERY"] = $val;
		}
		if (array_key_exists("DATE_ALLOW_DELIVERY_TO", $arFilter))
		{
			$val = $arFilter["DATE_ALLOW_DELIVERY_TO"];
			unset($arFilter["DATE_ALLOW_DELIVERY_TO"]);
			$arFilter["<=DATE_ALLOW_DELIVERY"] = $val;
		}
		if (array_key_exists("DATE_CANCELED_FROM", $arFilter))
		{
			$val = $arFilter["DATE_CANCELED_FROM"];
			unset($arFilter["DATE_CANCELED_FROM"]);
			$arFilter[">=DATE_CANCELED"] = $val;
		}
		if (array_key_exists("DATE_CANCELED_TO", $arFilter))
		{
			$val = $arFilter["DATE_CANCELED_TO"];
			unset($arFilter["DATE_CANCELED_TO"]);
			$arFilter["<=DATE_CANCELED"] = $val;
		}
		if (array_key_exists("DATE_DEDUCTED_FROM", $arFilter))
		{
			$val = $arFilter["DATE_DEDUCTED_FROM"];
			unset($arFilter["DATE_DEDUCTED_FROM"]);
			$arFilter[">=DATE_DEDUCTED"] = $val;
		}
		if (array_key_exists("DATE_DEDUCTED_TO", $arFilter))
		{
			$val = $arFilter["DATE_DEDUCTED_TO"];
			unset($arFilter["DATE_DEDUCTED_TO"]);
			$arFilter["<=DATE_DEDUCTED"] = $val;
		}
		if (array_key_exists("DATE_MARKED_FROM", $arFilter))
		{
			$val = $arFilter["DATE_MARKED_FROM"];
			unset($arFilter["DATE_MARKED_FROM"]);
			$arFilter[">=DATE_MARKED"] = $val;
		}
		if (array_key_exists("DATE_MARKED_TO", $arFilter))
		{
			$val = $arFilter["DATE_MARKED_TO"];
			unset($arFilter["DATE_MARKED_TO"]);
			$arFilter["<=DATE_MARKED"] = $val;
		}
		if (array_key_exists("DATE_PAY_BEFORE_FROM", $arFilter))
		{
			$val = $arFilter["DATE_PAY_BEFORE_FROM"];
			unset($arFilter["DATE_PAY_BEFORE_FROM"]);
			$arFilter[">=DATE_PAY_BEFORE"] = $val;
		}
		if (array_key_exists("DATE_PAY_BEFORE_TO", $arFilter))
		{
			$val = $arFilter["DATE_PAY_BEFORE_TO"];
			unset($arFilter["DATE_PAY_BEFORE_TO"]);
			$arFilter["<=DATE_PAY_BEFORE"] = $val;
		}
		if (array_key_exists("DELIVERY_REQUEST_SENT", $arFilter))
		{
			if($arFilter["DELIVERY_REQUEST_SENT"] == "Y")
				$arFilter["!DELIVERY_DATE_REQUEST"] = "";
			else
				$arFilter["+DELIVERY_DATE_REQUEST"] = "";

			unset($arFilter["DELIVERY_REQUEST_SENT"]);
		}

		$callback = false;
		if (array_key_exists("CUSTOM_SUBQUERY", $arFilter))
		{
			$callback = $arFilter["CUSTOM_SUBQUERY"];
			unset($arFilter["CUSTOM_SUBQUERY"]);
		}

		if (empty($arSelectFields))
		{
			$arSelectFields = array(
				"ID",
				"LID",
				"PERSON_TYPE_ID",
				"PAYED",
				"DATE_PAYED",
				"EMP_PAYED_ID",
				"CANCELED",
				"DATE_CANCELED",
				"EMP_CANCELED_ID",
				"REASON_CANCELED",
				"MARKED",
				"DATE_MARKED",
				"EMP_MARKED_ID",
				"REASON_MARKED",
				"STATUS_ID",
				"DATE_STATUS",
				"PAY_VOUCHER_NUM",
				"PAY_VOUCHER_DATE",
				"EMP_STATUS_ID",
				"PRICE_DELIVERY",
				"ALLOW_DELIVERY",
				"DATE_ALLOW_DELIVERY",
				"EMP_ALLOW_DELIVERY_ID",
				"DEDUCTED",
				"DATE_DEDUCTED",
				"EMP_DEDUCTED_ID",
				"REASON_UNDO_DEDUCTED",
				"RESERVED",
				"PRICE",
				"CURRENCY",
				"DISCOUNT_VALUE",
				"SUM_PAID",
				"USER_ID",
				"PAY_SYSTEM_ID",
				"DELIVERY_ID",
				"DATE_INSERT",
				"DATE_INSERT_FORMAT",
				"DATE_UPDATE",
				"USER_DESCRIPTION",
				"ADDITIONAL_INFO",
				"PS_STATUS",
				"PS_STATUS_CODE",
				"PS_STATUS_DESCRIPTION",
				"PS_STATUS_MESSAGE",
				"PS_SUM",
				"PS_CURRENCY",
				"PS_RESPONSE_DATE",
				"COMMENTS",
				"TAX_VALUE",
				"STAT_GID",
				"RECURRING_ID",
				"RECOUNT_FLAG",
				"USER_LOGIN",
				"USER_NAME",
				"USER_LAST_NAME",
				"USER_EMAIL",
				"DELIVERY_DOC_NUM",
				"DELIVERY_DOC_DATE",
				"DELIVERY_DATE_REQUEST",
				"STORE_ID",
				"ORDER_TOPIC",
				"RESPONSIBLE_ID",
				"RESPONSIBLE_LOGIN",
				"RESPONSIBLE_NAME",
				"RESPONSIBLE_LAST_NAME",
				"RESPONSIBLE_SECOND_NAME",
				"RESPONSIBLE_EMAIL",
				"RESPONSIBLE_WORK_POSITION",
				"RESPONSIBLE_PERSONAL_PHOTO",
				"RESPONSIBLE_GROUP_ID",
				"DATE_PAY_BEFORE",
				"DATE_BILL",
				"ACCOUNT_NUMBER",
				"TRACKING_NUMBER",
				"XML_ID"
			);
		}
		elseif (in_array("*", $arSelectFields))
		{
			$arSelectFields = array(
				"ID",
				"LID",
				"PERSON_TYPE_ID",
				"PAYED",
				"DATE_PAYED",
				"EMP_PAYED_ID",
				"CANCELED",
				"DATE_CANCELED",
				"EMP_CANCELED_ID",
				"REASON_CANCELED",
				"MARKED",
				"DATE_MARKED",
				"EMP_MARKED_ID",
				"REASON_MARKED",
				"STATUS_ID",
				"DATE_STATUS",
				"PAY_VOUCHER_NUM",
				"PAY_VOUCHER_DATE",
				"EMP_STATUS_ID",
				"PRICE_DELIVERY",
				"ALLOW_DELIVERY",
				"DATE_ALLOW_DELIVERY",
				"EMP_ALLOW_DELIVERY_ID",
				"DEDUCTED",
				"DATE_DEDUCTED",
				"EMP_DEDUCTED_ID",
				"REASON_UNDO_DEDUCTED",
				"RESERVED",
				"PRICE",
				"CURRENCY",
				"DISCOUNT_VALUE",
				"SUM_PAID",
				"USER_ID",
				"PAY_SYSTEM_ID",
				"DELIVERY_ID",
				"DATE_INSERT",
				"DATE_INSERT_FORMAT",
				"DATE_UPDATE",
				"USER_DESCRIPTION",
				"ADDITIONAL_INFO",
				"PS_STATUS",
				"PS_STATUS_CODE",
				"PS_STATUS_DESCRIPTION",
				"PS_STATUS_MESSAGE",
				"PS_SUM",
				"PS_CURRENCY",
				"PS_RESPONSE_DATE",
				"COMMENTS",
				"TAX_VALUE",
				"STAT_GID",
				"RECURRING_ID",
				"RECOUNT_FLAG",
				"USER_LOGIN",
				"USER_NAME",
				"USER_LAST_NAME",
				"USER_EMAIL",
				"DELIVERY_DOC_NUM",
				"DELIVERY_DOC_DATE",
				"DELIVERY_DATE_REQUEST",
				"STORE_ID",
				"ORDER_TOPIC",
				"RESPONSIBLE_ID",
				"RESPONSIBLE_LOGIN",
				"RESPONSIBLE_NAME",
				"RESPONSIBLE_LAST_NAME",
				"RESPONSIBLE_SECOND_NAME",
				"RESPONSIBLE_EMAIL",
				"RESPONSIBLE_WORK_POSITION",
				"RESPONSIBLE_PERSONAL_PHOTO",
				"RESPONSIBLE_GROUP_ID",
				"DATE_PAY_BEFORE",
				"DATE_BILL",
				"ACCOUNT_NUMBER",
				"TRACKING_NUMBER",
				"XML_ID"
			);
		}

		$maxLock = IntVal(COption::GetOptionString("sale", "MAX_LOCK_TIME", "60"));
		if(is_object($GLOBALS["USER"]))
			$userID = IntVal($GLOBALS["USER"]->GetID());
		else
			$userID = 0;

		// FIELDS -->
		$arFields = array(
			"ID" => array("FIELD" => "O.ID", "TYPE" => "int"),
			"LID" => array("FIELD" => "O.LID", "TYPE" => "string"),
			"PERSON_TYPE_ID" => array("FIELD" => "O.PERSON_TYPE_ID", "TYPE" => "int"),
			"PAYED" => array("FIELD" => "O.PAYED", "TYPE" => "char"),
			"DATE_PAYED" => array("FIELD" => "O.DATE_PAYED", "TYPE" => "datetime"),
			"EMP_PAYED_ID" => array("FIELD" => "O.EMP_PAYED_ID", "TYPE" => "int"),
			"CANCELED" => array("FIELD" => "O.CANCELED", "TYPE" => "char"),
			"DATE_CANCELED" => array("FIELD" => "O.DATE_CANCELED", "TYPE" => "datetime"),
			"EMP_CANCELED_ID" => array("FIELD" => "O.EMP_CANCELED_ID", "TYPE" => "int"),
			"REASON_CANCELED" => array("FIELD" => "O.REASON_CANCELED", "TYPE" => "string"),
			"STATUS_ID" => array("FIELD" => "O.STATUS_ID", "TYPE" => "char"),
			"DATE_STATUS" => array("FIELD" => "O.DATE_STATUS", "TYPE" => "datetime"),
			"PAY_VOUCHER_NUM" => array("FIELD" => "O.PAY_VOUCHER_NUM", "TYPE" => "string"),
			"PAY_VOUCHER_DATE" => array("FIELD" => "O.PAY_VOUCHER_DATE", "TYPE" => "date"),
			"EMP_STATUS_ID" => array("FIELD" => "O.EMP_STATUS_ID", "TYPE" => "int"),
			"PRICE_DELIVERY" => array("FIELD" => "O.PRICE_DELIVERY", "TYPE" => "double"),
			"ALLOW_DELIVERY" => array("FIELD" => "O.ALLOW_DELIVERY", "TYPE" => "char"),
			"DATE_ALLOW_DELIVERY" => array("FIELD" => "O.DATE_ALLOW_DELIVERY", "TYPE" => "datetime"),
			"EMP_ALLOW_DELIVERY_ID" => array("FIELD" => "O.EMP_ALLOW_DELIVERY_ID", "TYPE" => "int"),
			"DEDUCTED" => array("FIELD" => "O.DEDUCTED", "TYPE" => "char"),
			"DATE_DEDUCTED" => array("FIELD" => "O.DATE_DEDUCTED", "TYPE" => "datetime"),
			"EMP_DEDUCTED_ID" => array("FIELD" => "O.EMP_DEDUCTED_ID", "TYPE" => "int"),
			"REASON_UNDO_DEDUCTED" => array("FIELD" => "O.REASON_UNDO_DEDUCTED", "TYPE" => "string"),
			"RESERVED" => array("FIELD" => "O.RESERVED", "TYPE" => "char"),
			"MARKED" => array("FIELD" => "O.MARKED", "TYPE" => "char"),
			"DATE_MARKED" => array("FIELD" => "O.DATE_MARKED", "TYPE" => "datetime"),
			"EMP_MARKED_ID" => array("FIELD" => "O.EMP_MARKED_ID", "TYPE" => "int"),
			"REASON_MARKED" => array("FIELD" => "O.REASON_MARKED", "TYPE" => "string"),
			"PRICE" => array("FIELD" => "O.PRICE", "TYPE" => "double"),
			"CURRENCY" => array("FIELD" => "O.CURRENCY", "TYPE" => "string"),
			"DISCOUNT_VALUE" => array("FIELD" => "O.DISCOUNT_VALUE", "TYPE" => "double"),
			"SUM_PAID" => array("FIELD" => "O.SUM_PAID", "TYPE" => "double"),
			"USER_ID" => array("FIELD" => "O.USER_ID", "TYPE" => "int"),
			"PAY_SYSTEM_ID" => array("FIELD" => "O.PAY_SYSTEM_ID", "TYPE" => "int"),
			"DELIVERY_ID" => array("FIELD" => "O.DELIVERY_ID", "TYPE" => "string"),
			"DATE_INSERT" => array("FIELD" => "O.DATE_INSERT", "TYPE" => "datetime"),
			"DATE_INSERT_FORMAT" => array("FIELD" => "O.DATE_INSERT", "TYPE" => "datetime"),
			"DATE_UPDATE" => array("FIELD" => "O.DATE_UPDATE", "TYPE" => "datetime"),
			"USER_DESCRIPTION" => array("FIELD" => "O.USER_DESCRIPTION", "TYPE" => "string"),
			"ADDITIONAL_INFO" => array("FIELD" => "O.ADDITIONAL_INFO", "TYPE" => "string"),
			"PS_STATUS" => array("FIELD" => "O.PS_STATUS", "TYPE" => "char"),
			"PS_STATUS_CODE" => array("FIELD" => "O.PS_STATUS_CODE", "TYPE" => "string"),
			"PS_STATUS_DESCRIPTION" => array("FIELD" => "O.PS_STATUS_DESCRIPTION", "TYPE" => "string"),
			"PS_STATUS_MESSAGE" => array("FIELD" => "O.PS_STATUS_MESSAGE", "TYPE" => "string"),
			"PS_SUM" => array("FIELD" => "O.PS_SUM", "TYPE" => "double"),
			"PS_CURRENCY" => array("FIELD" => "O.PS_CURRENCY", "TYPE" => "string"),
			"PS_RESPONSE_DATE" => array("FIELD" => "O.PS_RESPONSE_DATE", "TYPE" => "datetime"),
			"COMMENTS" => array("FIELD" => "O.COMMENTS", "TYPE" => "string"),
			"TAX_VALUE" => array("FIELD" => "O.TAX_VALUE", "TYPE" => "double"),
			"STAT_GID" => array("FIELD" => "O.STAT_GID", "TYPE" => "string"),
			"RECURRING_ID" => array("FIELD" => "O.RECURRING_ID", "TYPE" => "int"),
			"RECOUNT_FLAG" => array("FIELD" => "O.RECOUNT_FLAG", "TYPE" => "char"),
			"AFFILIATE_ID" => array("FIELD" => "O.AFFILIATE_ID", "TYPE" => "int"),
			"LOCKED_BY" => array("FIELD" => "O.LOCKED_BY", "TYPE" => "int"),
			"LOCK_STATUS" => array("FIELD" => "if(DATE_LOCK is null, 'green', if(DATE_ADD(DATE_LOCK, interval ".$maxLock." MINUTE)<now(), 'green', if(LOCKED_BY=".$userID.", 'yellow', 'red')))", "TYPE" => "string"),
			"LOCK_USER_NAME" => array("FIELD" => "concat('(',UL.LOGIN,') ',UL.NAME,' ',UL.LAST_NAME)", "FROM" => "LEFT JOIN b_user UL ON (O.LOCKED_BY = UL.ID)", "TYPE" => "string"),
			"DELIVERY_DOC_NUM" => array("FIELD" => "O.DELIVERY_DOC_NUM", "TYPE" => "string"),
			"DELIVERY_DOC_DATE" => array("FIELD" => "O.DELIVERY_DOC_DATE", "TYPE" => "date"),
			"UPDATED_1C" => array("FIELD" => "O.UPDATED_1C", "TYPE" => "string"),
			"STORE_ID" => array("FIELD" => "O.STORE_ID", "TYPE" => "int"),

			"ORDER_TOPIC" => array("FIELD" => "O.ORDER_TOPIC", "TYPE" => "string"),
			"RESPONSIBLE_ID" => array("FIELD" => "O.RESPONSIBLE_ID", "TYPE" => "int"),
			"DATE_PAY_BEFORE" => array("FIELD" => "O.DATE_PAY_BEFORE", "TYPE" => "date"),
			"DATE_BILL" => array("FIELD" => "O.DATE_BILL", "TYPE" => "date"),
			"ACCOUNT_NUMBER" => array("FIELD" => "O.ACCOUNT_NUMBER", "TYPE" => "string"),
			"TRACKING_NUMBER" => array("FIELD" => "O.TRACKING_NUMBER", "TYPE" => "string"),
			"XML_ID" => array("FIELD" => "O.XML_ID", "TYPE" => "string"),
			"ID_1C" => array("FIELD" => "O.ID_1C", "TYPE" => "string"),
			"VERSION_1C" => array("FIELD" => "O.VERSION_1C", "TYPE" => "string"),
			"VERSION" => array("FIELD" => "O.VERSION", "TYPE" => "int"),
			"EXTERNAL_ORDER" => array("FIELD" => "O.EXTERNAL_ORDER", "TYPE" => "string"),

			"NAME_SEARCH" => array("FIELD" => "U.NAME, U.LAST_NAME, U.SECOND_NAME, U.EMAIL, U.LOGIN, U.ID", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (O.USER_ID = U.ID)"),
			"USER_LOGIN" => array("FIELD" => "U.LOGIN", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (O.USER_ID = U.ID)"),
			"USER_NAME" => array("FIELD" => "U.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (O.USER_ID = U.ID)"),
			"USER_LAST_NAME" => array("FIELD" => "U.LAST_NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (O.USER_ID = U.ID)"),
			"USER_EMAIL" => array("FIELD" => "U.EMAIL", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (O.USER_ID = U.ID)"),
			"USER_GROUP_ID" => array("FIELD" => "UG.GROUP_ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_user_group UG ON (UG.USER_ID = O.USER_ID)"),

			"RESPONSIBLE_LOGIN" => array("FIELD" => "UR.LOGIN", "TYPE" => "string", "FROM" => "LEFT JOIN b_user UR ON (O.RESPONSIBLE_ID = UR.ID)"),
			"RESPONSIBLE_NAME" => array("FIELD" => "UR.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user UR ON (O.RESPONSIBLE_ID = UR.ID)"),
			"RESPONSIBLE_LAST_NAME" => array("FIELD" => "UR.LAST_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user UR ON (O.RESPONSIBLE_ID = UR.ID)"),
			"RESPONSIBLE_SECOND_NAME" => array("FIELD" => "UR.SECOND_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user UR ON (O.RESPONSIBLE_ID = UR.ID)"),
			"RESPONSIBLE_EMAIL" => array("FIELD" => "UR.EMAIL", "TYPE" => "string", "FROM" => "LEFT JOIN b_user UR ON (O.RESPONSIBLE_ID = UR.ID)"),
			"RESPONSIBLE_WORK_POSITION" => array("FIELD" => "UR.WORK_POSITION", "TYPE" => "string", "FROM" => "LEFT JOIN b_user UR ON (O.RESPONSIBLE_ID = UR.ID)"),
			"RESPONSIBLE_PERSONAL_PHOTO" => array("FIELD" => "UR.PERSONAL_PHOTO", "TYPE" => "string", "FROM" => "LEFT JOIN b_user UR ON (O.RESPONSIBLE_ID = UR.ID)"),

			"BUYER" => array("FIELD" => "U.LOGIN,U.NAME,U.LAST_NAME,U.EMAIL,U.ID", "WHERE_ONLY" => "Y", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (O.USER_ID = U.ID)"),
			"BASKET_ID" => array("FIELD" => "B.ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_PRODUCT_ID" => array("FIELD" => "B.PRODUCT_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_PRODUCT_XML_ID" => array("FIELD" => "B.PRODUCT_XML_ID", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_MODULE" => array("FIELD" => "B.MODULE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_NAME" => array("FIELD" => "B.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_QUANTITY" => array("FIELD" => "B.QUANTITY", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_PRICE" => array("FIELD" => "B.PRICE", "TYPE" => "double", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_CURRENCY" => array("FIELD" => "B.CURRENCY", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_DISCOUNT_PRICE" => array("FIELD" => "B.DISCOUNT_PRICE", "TYPE" => "double", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_DISCOUNT_NAME" => array("FIELD" => "B.DISCOUNT_NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_DISCOUNT_VALUE" => array("FIELD" => "B.DISCOUNT_VALUE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_DISCOUNT_COUPON" => array("FIELD" => "B.DISCOUNT_COUPON", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_VAT_RATE" => array("FIELD" => "B.VAT_RATE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_RECOMMENDATION" => array("FIELD" => "B.RECOMMENDATION", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),
			"BASKET_PRICE_TOTAL" => array("FIELD" => "(B.PRICE * B.QUANTITY)", "TYPE" => "double", "FROM" => "INNER JOIN b_sale_basket B ON (O.ID = B.ORDER_ID)"),

			"STATUS_PERMS_GROUP_ID" => array("FIELD" => "SS2G.GROUP_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),
			"STATUS_PERMS_PERM_VIEW" => array("FIELD" => "SS2G.PERM_VIEW", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),
			"STATUS_PERMS_PERM_CANCEL" => array("FIELD" => "SS2G.PERM_CANCEL", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),
			"STATUS_PERMS_PERM_MARK" => array("FIELD" => "SS2G.PERM_MARK", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),
			"STATUS_PERMS_PERM_DELIVERY" => array("FIELD" => "SS2G.PERM_DELIVERY", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),
			"STATUS_PERMS_PERM_DEDUCTION" => array("FIELD" => "SS2G.PERM_DEDUCTION", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),
			"STATUS_PERMS_PERM_PAYMENT" => array("FIELD" => "SS2G.PERM_PAYMENT", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),
			"STATUS_PERMS_PERM_STATUS" => array("FIELD" => "SS2G.PERM_STATUS", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),
			"STATUS_PERMS_PERM_STATUS_FROM" => array("FIELD" => "SS2G.PERM_STATUS_FROM", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),
			"STATUS_PERMS_PERM_UPDATE" => array("FIELD" => "SS2G.PERM_UPDATE", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),
			"STATUS_PERMS_PERM_DELETE" => array("FIELD" => "SS2G.PERM_DELETE", "TYPE" => "char", "FROM" => "INNER JOIN b_sale_status2group SS2G ON (O.STATUS_ID = SS2G.STATUS_ID)"),

			"PROPERTY_ID" => array("FIELD" => "SP.ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_order_props_value SP ON (O.ID = SP.ORDER_ID)"),
			"PROPERTY_ORDER_PROPS_ID" => array("FIELD" => "SP.ORDER_PROPS_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_order_props_value SP ON (O.ID = SP.ORDER_ID)"),
			"PROPERTY_NAME" => array("FIELD" => "SP.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_order_props_value SP ON (O.ID = SP.ORDER_ID)"),
			"PROPERTY_VALUE" => array("FIELD" => "SP.VALUE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_order_props_value SP ON (O.ID = SP.ORDER_ID)"),
			"PROPERTY_CODE" => array("FIELD" => "SP.CODE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_order_props_value SP ON (O.ID = SP.ORDER_ID)"),
			"PROPERTY_VAL_BY_CODE" => array("FIELD" => "SP.VALUE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_order_props_value SP ON (O.ID = SP.ORDER_ID)"),

			"COMPLETE_ORDERS" => array("WHERE" => array(self, "ProcessCompleteOrdersParam")),
			"DELIVERY_DATE_REQUEST" => array("FIELD" => "OD.DATE_REQUEST", "TYPE" => "datetime", "FROM" => "LEFT JOIN b_sale_order_delivery OD ON (O.ID = OD.ORDER_ID)")
		);
		// <-- FIELDS

		$arPropIDsTmp = array();
		foreach ($arOrder as $key => $value)
			CSaleOrder::PrepareGetListArray($key, $arFields, $arPropIDsTmp);

		foreach ($arFilter as $key => $value)
		{
			$arKeyTmp = CSaleOrder::GetFilterOperation($key);
			$key = $arKeyTmp["FIELD"];

			CSaleOrder::PrepareGetListArray($key, $arFields, $arPropIDsTmp);
		}

		if (is_array($arGroupBy))
			foreach ($arGroupBy as $key => $value)
				CSaleOrder::PrepareGetListArray($key, $arFields, $arPropIDsTmp);

		foreach ($arSelectFields as $key => $value)
			CSaleOrder::PrepareGetListArray($key, $arFields, $arPropIDsTmp);

		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields, $obUserFieldsSql, $callback, $arOptions);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		$r = $obUserFieldsSql->GetFilter();
		$strSqlUFFilter = '';
		if(strlen($r)>0)
			$strSqlUFFilter = " (".$r.") ";

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
					$obUserFieldsSql->GetSelect()." ".
				"FROM b_sale_order O ".
				"	".$arSqls["FROM"]." ".
					$obUserFieldsSql->GetJoin("O.ID")." ";

			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["WHERE"]) > 0 && strlen($strSqlUFFilter) > 0)
				$strSql .= " AND ".$strSqlUFFilter." ";
			elseif (strlen($arSqls["WHERE"]) <= 0 && strlen($strSqlUFFilter) > 0)
				$strSql .= " WHERE ".$strSqlUFFilter." ";

			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!1!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$dbRes->SetUserFields($USER_FIELD_MANAGER->GetUserFields("ORDER"));

			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
				$obUserFieldsSql->GetSelect()." ".
			"FROM b_sale_order O ".
			"	".$arSqls["FROM"]." ".
				$obUserFieldsSql->GetJoin("O.ID")." ";

		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["WHERE"]) > 0 && strlen($strSqlUFFilter) > 0)
			$strSql .= " AND ".$strSqlUFFilter." ";
		elseif (strlen($arSqls["WHERE"]) <= 0 && strlen($strSqlUFFilter) > 0)
			$strSql .= " WHERE ".$strSqlUFFilter." ";

		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_sale_order O ".
				"	".$arSqls["FROM"]." ".
					$obUserFieldsSql->GetJoin("O.ID")." ";

			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["WHERE"]) > 0 && strlen($strSqlUFFilter) > 0)
				$strSql_tmp .= " AND ".$strSqlUFFilter." ";
			elseif (strlen($arSqls["WHERE"]) <= 0 && strlen($strSqlUFFilter) > 0)
				$strSql_tmp .= " WHERE ".$strSqlUFFilter." ";

			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!2.1!=".htmlspecialcharsbx($strSql_tmp)."<br>";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (strlen($arSqls["GROUPBY"]) <= 0)
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			//echo "!2.2!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes->SetUserFields($USER_FIELD_MANAGER->GetUserFields("ORDER"));
			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".IntVal($arNavStartParams["nTopCount"]);

			//echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$dbRes->SetUserFields($USER_FIELD_MANAGER->GetUserFields("ORDER"));
		}

		return $dbRes;
	}

	function GetLockStatus($ID, &$lockedBY, &$dateLock)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
			return False;

		$maxLock = IntVal(COption::GetOptionString("sale", "MAX_LOCK_TIME", "60"));
		$userID = IntVal($GLOBALS["USER"]->GetID());

		$strSql =
			"SELECT LOCKED_BY, ".
			"	".$DB->DateToCharFunction("DATE_LOCK")." as DATE_LOCK, ".
			"	if(DATE_LOCK is null, 'green',  ".
			"		if(DATE_ADD(DATE_LOCK, interval ".$maxLock." MINUTE)<now(), 'green', ".
			"			if(LOCKED_BY=".$userID.", 'yellow', 'red'))) as LOCK_STATUS ".
			"FROM b_sale_order ".
			"WHERE ID = ".$ID." ";
		$dbRes = $DB->Query($strSql);
		$arRes = $dbRes->Fetch();

		$lockedBY = $arRes["LOCKED_BY"];
		$dateLock = $arRes["DATE_LOCK"];

		return $arRes["LOCK_STATUS"];
	}

	/*
	 * Change order to add stories
	 *
	 * @param array $arOrderOld old order fields
	 * @return bool true
	 */
	public function AddOrderHistory($OldFields, $NewFields)
	{
		global $DB, $USER;

		foreach(GetModuleEvents("sale", "OnBeforeOrderAddHistory", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array(&$NewFields))===false)
				return false;

		if ($OldFields["ID"] <= 0)
			return false;

		if (isset($NewFields["ID"]))
			unset($NewFields["ID"]);

		$bChange = false;
		$strSql = '';
		$arInsert = array("H_USER_ID" => $USER->GetID(), "H_ORDER_ID" => $OldFields["ID"], "H_CURRENCY" => $OldFields["CURRENCY"]);

		$arDeleteFields = array(
			"ID",
			"EMP_CANCELED_ID",
			"EMP_MARKED_ID",
			"EMP_DEDUCTED_ID",
			"EMP_STATUS_ID",
			"EMP_ALLOW_DELIVERY_ID",
			"LOCKED_BY",
			"DATE_LOCK",
			"UPDATED_1C",
			"DATE_INSERT",
			"DATE_UPDATE",
			"USER_DESCRIPTION",
			"ADDITIONAL_INFO",
			"COMMENTS",
			"RECOUNT_FLAG",
			"RECURRING_ID"
		);

		foreach ($NewFields as $key => $val)
		{
			if ($key == "PAY_VOUCHER_DATE" || $key == "DELIVERY_DOC_DATE")
			{
				$valOld = $val;
				$val =  CDatabase::FormatDate(trim($val), false, "Y-M-D");
			}

			if (array_key_exists($key, $OldFields) && strlen($val) > 0 && $val != $OldFields[$key] && !in_array($key, $arDeleteFields))
			{
				if ($key == "PAY_VOUCHER_DATE" || $key == "DELIVERY_DOC_DATE")
					$val = $valOld;

				$bChange = true;
				$arInsert[$key] = $val;
			}
		}

		if ($bChange)
		{
			$arPrepare = $DB->PrepareInsert("b_sale_order_history", $arInsert);
			$arPrepare[0] .= ", H_DATE_INSERT";
			$arPrepare[1] .= ", ".$DB->GetNowFunction();

			$strSql = "INSERT INTO b_sale_order_history (".$arPrepare[0].") "."VALUES (".$arPrepare[1].");";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		foreach(GetModuleEvents("sale", "OnAfterOrderAddHistory", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($NewFields));

		return true;
	}
}
?>