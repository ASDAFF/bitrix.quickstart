<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["INTERVAL"] = intval($arParams["INTERVAL"]);

$arParams["ELEMENTS_PER_STEP"] = intval($arParams["ELEMENTS_PER_STEP"]);
if($arParams["ELEMENTS_PER_STEP"] < 0)
	$arParams["ELEMENTS_PER_STEP"] = 0;

if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$arParams["USE_ZIP"] = $arParams["USE_ZIP"]!="N";

if($arParams["INTERVAL"] <= 0)
	@set_time_limit(0);

$start_time = time();

$bUSER_HAVE_ACCESS = false;
if(isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$bUSER_HAVE_ACCESS = $GLOBALS["USER"]->IsAdmin();
	if(!$bUSER_HAVE_ACCESS)
	{
		$arUserGroupArray = $GLOBALS["USER"]->GetUserGroupArray();
		foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
		{
			if(in_array($PERM, $arUserGroupArray))
			{
				$bUSER_HAVE_ACCESS = true;
				break;
			}
		}
	}
}

$bDesignMode = $GLOBALS["APPLICATION"]->GetShowIncludeAreas()
		&& !isset($_GET["mode"])
		&& is_object($GLOBALS["USER"])
		&& $GLOBALS["USER"]->IsAdmin()
;

if(!$bDesignMode)
{
	if(!isset($_GET["mode"]))
		return;
	if(isset($_SERVER["HTTP_REFERER"]))
		return;
	$APPLICATION->RestartBuffer();
	header("Pragma: no-cache");
}

$DIR_NAME = "";

ob_start();

if($_GET["mode"] == "checkauth" && $USER->IsAuthorized())
{
	if(
		(COption::GetOptionString("main", "use_session_id_ttl", "N") == "Y")
		&& (COption::GetOptionInt("main", "session_id_ttl", 0) > 0)
		&& !defined("BX_SESSION_ID_CHANGE")
	)
	{
		echo "failure\n",GetMessage("CC_BCE1_ERROR_SESSION_ID_CHANGE");
	}
	else
	{
		echo "success\n";
		echo session_name()."\n";
		echo session_id() ."\n";
	}
}
elseif(!$USER->IsAuthorized())
{
	echo "failure\n",GetMessage("CC_BCE1_ERROR_AUTHORIZE");
}
elseif(!$bUSER_HAVE_ACCESS)
{
	echo "failure\n",GetMessage("CC_BCE1_PERMISSION_DENIED");
}
elseif(!CModule::IncludeModule('iblock'))
{
	echo "failure\n",GetMessage("CC_BCE1_ERROR_IBLOCK_MODULE");
}
elseif(!CModule::IncludeModule('catalog'))
{
	echo "failure\n",GetMessage("CC_BCE1_ERROR_CATALOG_MODULE");
}
else
{
	if($_GET["mode"]=="init")
	{
		$_SESSION["BX_CML2_EXPORT"] = array(
			"zip" => $arParams["USE_ZIP"] && function_exists("zip_open"),
			"step" => 1,
			"next_step" => array(),
			"SECTION_MAP" => array(),
			"PROPERTY_MAP" => false,
			"PRICES_MAP" => false,
		);
		echo "zip=".($_SESSION["BX_CML2_EXPORT"]["zip"]? "yes": "no")."\n";
	}
	elseif($_GET["mode"] == "query")
	{
		$start_time = time();
		if($fp = fopen("php://output", "ab"))
		{
			$obExport = new CIBlockCMLExport;
			if($_SESSION["BX_CML2_EXPORT"]["step"] === 1)
			{
				if($obExport->Init(
					$fp,
					$arParams["IBLOCK_ID"],
					$_SESSION["BX_CML2_EXPORT"]["next_step"],
					false,
					$work_dir = false,
					$file_dir = false,
					$bCheckPermissions = false
				))
				{
					$_SESSION["BX_CML2_EXPORT"]["total"] = CIBlockElement::GetList(array(), array("IBLOCK_ID"=> $arParams["IBLOCK_ID"], "ACTIVE" => "Y"), array());
					$_SESSION["BX_CML2_EXPORT"]["current"] = 0;
					echo GetMessage("CC_BCE1_PROGRESS_PRODUCT", array("#TOTAL#" => $_SESSION["BX_CML2_EXPORT"]["total"], "#COUNT#" => 0));

					$obExport->NotCatalog();
					$obExport->ExportFileAsURL();

					$obExport->StartExport();
					$obExport->StartExportMetadata();
					$obExport->ExportProperties($_SESSION["BX_CML2_EXPORT"]["PROPERTY_MAP"]);
					$obExport->ExportSections(
						$_SESSION["BX_CML2_EXPORT"]["SECTION_MAP"],
						0,
						0
					);
					$obExport->EndExportMetadata();
					$obExport->EndExport();

					$_SESSION["BX_CML2_EXPORT"]["next_step"] = $obExport->next_step;
					$_SESSION["BX_CML2_EXPORT"]["step"] = 2;
				}
				else
				{
					echo "failure\n",GetMessage("CC_BCE1_ERROR_INIT");
				}
			}
			elseif($_SESSION["BX_CML2_EXPORT"]["step"] === 2)
			{
				if($obExport->Init(
					$fp,
					$arParams["IBLOCK_ID"],
					$_SESSION["BX_CML2_EXPORT"]["next_step"],
					false,
					$work_dir = false,
					$file_dir = false,
					$bCheckPermissions = false
				))
				{
					$obExport->NotCatalog();
					$obExport->ExportFileAsURL();
					ob_start();
					$obExport->StartExport();
					$obExport->StartExportCatalog();
					$result = $obExport->ExportElements(
						$_SESSION["BX_CML2_EXPORT"]["PROPERTY_MAP"],
						$_SESSION["BX_CML2_EXPORT"]["SECTION_MAP"],
						$start_time,
						$arParams["INTERVAL"],
						$arParams["ELEMENTS_PER_STEP"]
					);

					if($result)
					{
						$_SESSION["BX_CML2_EXPORT"]["current"] += $result;
						$obExport->EndExportCatalog();
						$obExport->EndExport();
						$c = ob_get_contents();
						ob_end_clean();
						echo GetMessage("CC_BCE1_PROGRESS_PRODUCT", array(
							"#TOTAL#" => $_SESSION["BX_CML2_EXPORT"]["total"],
							"#COUNT#" => $_SESSION["BX_CML2_EXPORT"]["current"],
						));
						echo $c;
						$_SESSION["BX_CML2_EXPORT"]["next_step"] = $obExport->next_step;
					}
					else
					{
						ob_end_clean();
						$_SESSION["BX_CML2_EXPORT"] = array(
							"zip" => $arParams["USE_ZIP"] && function_exists("zip_open"),
							"step" => 3,
							"next_step" => array(),
							"SECTION_MAP" => array(),
							"PROPERTY_MAP" => false,
							"PRICES_MAP" => false,
						);
					}
				}
			}

			$arCatalog = false;
			if($_SESSION["BX_CML2_EXPORT"]["step"] === 3)
				$arCatalog = CCatalog::GetSkuInfoByProductID($arParams["IBLOCK_ID"]);

			$obExport = new CIBlockCMLExport;
			if(
				$_SESSION["BX_CML2_EXPORT"]["step"] === 3
				&& $obExport->Init(
					$fp,
					is_array($arCatalog)? $arCatalog["IBLOCK_ID"]: $arParams["IBLOCK_ID"],
					$_SESSION["BX_CML2_EXPORT"]["next_step"],
					false,
					$work_dir = false,
					$file_dir = false,
					$bCheckPermissions = false,
					is_array($arCatalog)? $arCatalog["PRODUCT_IBLOCK_ID"]: false
				)
			)
			{
				if(!array_key_exists("total", $_SESSION["BX_CML2_EXPORT"]))
				{
					$_SESSION["BX_CML2_EXPORT"]["total"] = CIBlockElement::GetList(array(), array("IBLOCK_ID"=> is_array($arCatalog)? $arCatalog["IBLOCK_ID"]: $arParams["IBLOCK_ID"], "ACTIVE" => "Y"), array());
					$_SESSION["BX_CML2_EXPORT"]["current"] = 0;
				}
				ob_start();
				$obExport->StartExport();

				ob_start();
				$obExport->StartExportMetadata();
				$obExport->ExportProperties($_SESSION["BX_CML2_EXPORT"]["PROPERTY_MAP"]);
				$obExport->ExportSections(
					$_SESSION["BX_CML2_EXPORT"]["SECTION_MAP"],
					0,
					0
				);
				$obExport->EndExportMetadata();
				ob_end_clean();

				$obExport->StartExportCatalog();
				$result = $obExport->ExportElements(
					$_SESSION["BX_CML2_EXPORT"]["PROPERTY_MAP"],
					$_SESSION["BX_CML2_EXPORT"]["SECTION_MAP"],
					$start_time,
					$arParams["INTERVAL"],
					$arParams["ELEMENTS_PER_STEP"]
				);

				if($result)
				{
					$_SESSION["BX_CML2_EXPORT"]["current"] += $result;
					$obExport->EndExportCatalog();
					$obExport->EndExport();
					$c = ob_get_contents();
					ob_end_clean();
					echo GetMessage("CC_BCE1_PROGRESS_OFFERS", array(
						"#TOTAL#" => $_SESSION["BX_CML2_EXPORT"]["total"],
						"#COUNT#" => $_SESSION["BX_CML2_EXPORT"]["current"],
					));
					echo $c;
					$_SESSION["BX_CML2_EXPORT"]["next_step"] = $obExport->next_step;
				}
				else
				{
					ob_end_clean();
					$_SESSION["BX_CML2_EXPORT"] = array(
						"zip" => $arParams["USE_ZIP"] && function_exists("zip_open"),
						"step" => 4,
						"next_step" => array(),
						"SECTION_MAP" => array(),
						"PROPERTY_MAP" => false,
						"PRICES_MAP" => false,
					);
				}
			}

			if(
				$_SESSION["BX_CML2_EXPORT"]["step"] === 4
			)
			{
				echo "finished=yes\n";
			}
		}
	}
	else
	{
		echo "failure\n",GetMessage("CC_BCE1_ERROR_UNKNOWN_COMMAND");
	}
}

$contents = ob_get_contents();
ob_end_clean();

if(!$bDesignMode)
{
	header("Content-Type: text/html; charset=LANG_CHARSET");
	echo $contents;
	die();
}
else
{
	$this->IncludeComponentLang(".parameters.php");
	if(
		(COption::GetOptionString("main", "use_session_id_ttl", "N") == "Y")
		&& (COption::GetOptionInt("main", "session_id_ttl", 0) > 0)
		&& !defined("BX_SESSION_ID_CHANGE")
	)
		ShowError(GetMessage("CC_BCE1_ERROR_SESSION_ID_CHANGE"));
	?><table class="data-table">
	<tr><td><?echo GetMessage("CC_BCE1_IBLOCK_ID")?></td><td><?echo $arParams["IBLOCK_ID"]?></td></tr>
	<tr><td><?echo GetMessage("CC_BCE1_INTERVAL")?></td><td><?echo $arParams["INTERVAL"]?></td></tr>
	<tr><td><?echo GetMessage("CC_BCE1_ELEMENTS_PER_STEP")?></td><td><?echo $arParams["ELEMENTS_PER_STEP"]?></td></tr>
	<tr><td><?echo GetMessage("CC_BCE1_USE_ZIP")?></td><td><?echo $arParams["USE_ZIP"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	</table>
	<?
}
?>