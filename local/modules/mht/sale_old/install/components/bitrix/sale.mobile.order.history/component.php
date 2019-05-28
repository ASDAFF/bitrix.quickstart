<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('sale'))
{
	ShowError(GetMessage("SMOH_SALE_NOT_INSTALLED"));
	return;
}

if (isset($_REQUEST['id']))
	$orderId = $_REQUEST['id'];
else
	return;

$bUserCanViewOrder = CSaleOrder::CanUserViewOrder($orderId, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());

if(!$bUserCanViewOrder)
{
	echo ShowError(GetMessage("SMOH_NO_PERMS2VIEW"));
	return;
}

if (!CModule::IncludeModule('mobileapp'))
{
	ShowError("SMOH_MOBILEAPP_NOT_INSTALLED");
	return;
}

$arResult["ORDER"] = CSaleMobileOrderUtils::getOrderInfoDetail($orderId);

if (!function_exists("convertHistoryToNewFormat"))
{
	function convertHistoryToNewFormat($arFields)
	{
		foreach ($arFields as $fieldname => $fieldvalue)
		{
			if (strlen($fieldvalue) > 0)
			{
				foreach (CSaleOrderChangeFormat::$arOperationTypes as $code => $arInfo)
				{
					if (in_array($fieldname, $arInfo["TRIGGER_FIELDS"]))
					{
						$arData = array();
						foreach ($arInfo["DATA_FIELDS"] as $field)
							$arData[$field] = $arFields["$field"];

						return array(
							"ID" => $arFields["ID"],
							"ORDER_ID" => $arFields["H_ORDER_ID"],
							"TYPE" => $code,
							"DATA" => serialize($arData),
							"DATE_CREATE" => $arFields["H_DATE_INSERT"],
							"DATE_MODIFY" => $arFields["H_DATE_INSERT"],
							"USER_ID" => $arFields["H_USER_ID"]
						);
					}
				}
			}
		}

		return false;
	}
}

$arHistoryData = array();
$bUseOldHistory = false;

// collect records from old history to show in the new order changes list
$dbHistory = CSaleOrder::GetHistoryList(
	array("H_DATE_INSERT" => "DESC"),
	array("H_ORDER_ID" => $orderId),
	false,
	false,
	array("*")
);

while ($arHistory = $dbHistory->Fetch())
{
	$res = convertHistoryToNewFormat($arHistory);

	if ($res)
	{
		$arHistoryData[] = $res;
		$bUseOldHistory = true;
	}
}

// new order history data
$dbOrderChange = CSaleOrderChange::GetList(
	array("DATE_CREATE" => "DESC"),
	array("ORDER_ID" => $orderId),
	false,
	false,
	array("*")
);

while ($arChangeRecord = $dbOrderChange->Fetch())
	$arHistoryData[] = $arChangeRecord;

// advancing sorting is necessary if old history results are mixed with new order changes
if ($bUseOldHistory)
{
	$arData = array();
	foreach ($arHistoryData as $index => $arHistoryRecord)
		$arData[$index]  = $arHistoryRecord["DATE_CREATE"];

	$arIds = array();
	foreach ($arHistoryData as $index => $arHistoryRecord)
		$arIds[$index]  = $arHistoryRecord["ID"];

	array_multisort($arData, SORT_DESC, $arIds, SORT_DESC, $arHistoryData);
}

$dbRecords = new CDBResult;
$dbRecords->InitFromArray($arHistoryData);

$arResult["STATUSES"] = array();
$dbStatusList = CSaleStatus::GetList(
	array("SORT" => "ASC"),
	array("LID" => LANGUAGE_ID),
	false,
	false,
	array("ID", "NAME")
);

while ($arStatusList = $dbStatusList->Fetch())
	$arResult["STATUSES"][htmlspecialcharsbx($arStatusList["ID"])] = htmlspecialcharsbx($arStatusList["NAME"]);

$arResult["PAY_SYSTEMS"] = array();
$dbPaySystemList = CSalePaySystem::GetList(
		array("SORT"=>"ASC"),
		array()
		);
while ($arPaySystemList = $dbPaySystemList->Fetch())
	$arResult["PAY_SYSTEMS"][$arPaySystemList["ID"]] = htmlspecialcharsbx($arPaySystemList["NAME"]);

$userCache = array();
$deliveryCache = array();

while ($arHistory = $dbRecords->Fetch())
{
	if(isset($userCache[$arResult["ORDER"]["USER_ID"]]))
	{
		$arHistory["USER"] = $userCache[$arResult["ORDER"]["USER_ID"]];
	}
	else
	{
		$dbUser = CUser::GetByID($arResult["ORDER"]["USER_ID"]);

		if($arUser = $dbUser->Fetch())
		{

			$arHistory["USER"]["LOGIN"] = $arUser["LOGIN"];
			$arHistory["USER"]["NAME"] = htmlspecialcharsbx($arUser["NAME"]);
			$arHistory["USER"]["LAST_NAME"] = htmlspecialcharsbx($arUser["LAST_NAME"]);

			$userCache[$arResult["ORDER"]["USER_ID"]] = $arHistory["USER"];
		}
	}

	$arHistory = array_merge($arHistory, CSaleOrderChange::GetRecordDescription($arHistory["TYPE"], $arHistory["DATA"]));

	$arResult["HISTORY"][] = $arHistory;
}

$this->IncludeComponentTemplate();
?>
