<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

$arAvailableExports = array(
//		"excel" => "excel.php",
		"csv" => "csv.php",
		"commerceml" => "commerceml.php",
		"commerceml2" => "commerceml2.php",
	);

$strPath2Export = BX_PERSONAL_ROOT."/php_interface/include/sale_export/";
$strPath2Export1 = "/bitrix/modules/sale/export/";

CheckDirPath($_SERVER["DOCUMENT_ROOT"].$strPath2Export);
if ($handle = opendir($_SERVER["DOCUMENT_ROOT"].$strPath2Export))
{
	while (($file = readdir($handle)) !== false)
	{
		if ($file == "." || $file == "..")
			continue;
		if (is_file($_SERVER["DOCUMENT_ROOT"].$strPath2Export.$file) && substr($file, strlen($file)-4)==".php")
		{
			$export_name = substr($file, 0, strlen($file) - 4);
			$arAvailableExports[$export_name] = $file;
		}
	}
}
closedir($handle);


$errorMessage = "";

if (CModule::IncludeModule("sale"))
{
	$EXPORT_FORMAT = Trim($EXPORT_FORMAT);
	if (strlen($EXPORT_FORMAT) > 0)
	{
		if (array_key_exists($EXPORT_FORMAT, $arAvailableExports))
		{
			$exportFilePath = "";
			if (file_exists($_SERVER["DOCUMENT_ROOT"].$strPath2Export.$arAvailableExports[$EXPORT_FORMAT])
				&& is_file($_SERVER["DOCUMENT_ROOT"].$strPath2Export.$arAvailableExports[$EXPORT_FORMAT]))
				$exportFilePath = $_SERVER["DOCUMENT_ROOT"].$strPath2Export.$arAvailableExports[$EXPORT_FORMAT];
			elseif (file_exists($_SERVER["DOCUMENT_ROOT"].$strPath2Export1.$arAvailableExports[$EXPORT_FORMAT])
				&& is_file($_SERVER["DOCUMENT_ROOT"].$strPath2Export1.$arAvailableExports[$EXPORT_FORMAT]))
				$exportFilePath = $_SERVER["DOCUMENT_ROOT"].$strPath2Export1.$arAvailableExports[$EXPORT_FORMAT];

			if (StrLen($exportFilePath) > 0)
			{
				@set_time_limit(50000);

				$arAccessibleSites = array();
				$dbAccessibleSites = CSaleGroupAccessToSite::GetList(
						array(),
						array("GROUP_ID" => $GLOBALS["USER"]->GetUserGroupArray()),
						false,
						false,
						array("SITE_ID")
					);
				while ($arAccessibleSite = $dbAccessibleSites->Fetch())
				{
					if (!in_array($arAccessibleSite["SITE_ID"], $arAccessibleSites))
						$arAccessibleSites[] = $arAccessibleSite["SITE_ID"];
				}

				$filter_lang = Trim($filter_lang);
				if (strlen($filter_lang) > 0)
				{
					if (!in_array($filter_lang, $arAccessibleSites) && $saleModulePermissions < "W")
						$filter_lang = "";
				}

				$arFilter = array();

				if (isset($OID) && is_array($OID) && count($OID) > 0)
					$arFilter["ID"] = $OID;
				elseif (isset($OID) && IntVal($OID) > 0)
					$arFilter["ID"] = IntVal($OID);

				if (IntVal($filter_id_from)>0) $arFilter[">=ID"] = IntVal($filter_id_from);
				if (IntVal($filter_id_to)>0) $arFilter["<=ID"] = IntVal($filter_id_to);
				if (strlen($filter_date_from)>0) $arFilter["DATE_FROM"] = Trim($filter_date_from);
				if (strlen($filter_date_to)>0) $arFilter["DATE_TO"] = Trim($filter_date_to);
				if (strlen($filter_lang)>0 && $filter_lang!="NOT_REF") $arFilter["LID"] = Trim($filter_lang);
				if (strlen($filter_currency)>0) $arFilter["CURRENCY"] = Trim($filter_currency);

				if (isset($filter_status) && !is_array($filter_status) && strlen($filter_status) > 0)
					$filter_status = array($filter_status);
				if (isset($filter_status) && is_array($filter_status) && count($filter_status) > 0)
				{
					for ($i = 0; $i < count($filter_status); $i++)
					{
						$filter_status[$i] = Trim($filter_status[$i]);
						if (strlen($filter_status[$i]) > 0)
							$arFilter["STATUS_ID"][] = $filter_status[$i];
					}
				}

				if (strlen($filter_date_status_from)>0) $arFilter["DATE_STATUS_FROM"] = Trim($filter_date_status_from);
				if (strlen($filter_date_status_to)>0) $arFilter["DATE_STATUS_TO"] = Trim($filter_date_status_to);
				if (strlen($filter_payed)>0) $arFilter["PAYED"] = Trim($filter_payed);
				if (strlen($filter_allow_delivery)>0) $arFilter["ALLOW_DELIVERY"] = Trim($filter_allow_delivery);
				if (strlen($filter_ps_status)>0) $arFilter["PS_STATUS"] = Trim($filter_ps_status);
				if (IntVal($filter_pay_system)>0) $arFilter["PAY_SYSTEM_ID"] = IntVal($filter_pay_system);
				if (strlen($filter_canceled)>0) $arFilter["CANCELED"] = Trim($filter_canceled);
				if (strlen($filter_buyer)>0) $arFilter["%BUYER"] = Trim($filter_buyer);

				if ($saleModulePermissions < "W")
				{
					if (strlen($filter_lang) <= 0)
						$arFilter["LID"] = $arAccessibleSites;
				}

				$shownFieldsList = COption::GetOptionString("sale", "order_list_fields", "ID,USER,PAY_SYSTEM,PRICE,STATUS,PAYED,PS_STATUS,CANCELED,BASKET");
				$arShownFieldsList = explode(",", $shownFieldsList);

				$arShownFieldsParams = array();
				$arSelectFields = array("PAYED");
				$ind = -1;
				foreach ($GLOBALS["AVAILABLE_ORDER_FIELDS"] as $key => $value)
				{
					if (in_array($key, $arShownFieldsList))
					{
						$ind++;
						$arShownFieldsParams[$ind] = $value;
						$arShownFieldsParams[$ind]["KEY"] = $key;

						$arFields_tmp = array();
						if (strlen($value["SELECT"]) > 0)
							$arFields_tmp = explode(",", $value["SELECT"]);

						$arShownFieldsParams[$ind]["SHOW"] = $arFields_tmp;

						for ($i = 0; $i < count($arFields_tmp); $i++)
						{
							if (!in_array($arFields_tmp[$i], $arSelectFields))
								$arSelectFields[] = $arFields_tmp[$i];
						}
					}
				}

				include($exportFilePath);
			}
			else
			{
				$errorMessage .= str_replace("#FILE#", $exportFilePath, GetMessage("SOE_NO_SCRIPT")).". ";
			}
		}
		else
		{
			$errorMessage .= str_replace("#EXPORT_FORMAT#", $EXPORT_FORMAT, GetMessage("SOE_WRONG_FORMAT")).". ";
		}
	}
	else
	{
		$errorMessage .= GetMessage("SOE_NO_FORMAT").". ";
	}
}
else
{
	$errorMessage .= GetMessage("SOE_NO_SALE").". ";
}

if (strlen($errorMessage) > 0)
{
	$APPLICATION->SetTitle(GetMessage("SOE_EXPORT_ERROR"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

	CAdminMessage::ShowMessage($errorMessage);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_before.php");
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
?>