<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");

global $USER;

$bBizproc = CModule::IncludeModule("bizproc");
$bWorkflow = CModule::IncludeModule("workflow");
$bFileman = CModule::IncludeModule("fileman");

$bSearch = false;
$bCurrency = false;
$arCurrencyList = array();

if(isset($_REQUEST['mode']))
{
	if($_REQUEST['mode']=='list' || $_REQUEST['mode']=='frame')
		CFile::DisableJSFunction(true);
}

$arIBTYPE = CIBlockType::GetByIDLang($type, LANGUAGE_ID);
if($arIBTYPE===false)
	$APPLICATION->AuthForm(GetMessage("IBLOCK_BAD_BLOCK_TYPE_ID"));

$IBLOCK_ID = IntVal($IBLOCK_ID);

$arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);
if($arIBlock)
	$bBadBlock = !CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_admin_display");
else
	$bBadBlock = true;

if($bBadBlock)
{
	$APPLICATION->SetTitle($arIBTYPE["NAME"]);
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	?>
	<?echo ShowError(GetMessage("IBLOCK_BAD_IBLOCK"));?>
	<a href="<?echo htmlspecialcharsbx("iblock_admin.php?lang=".urlencode(LANGUAGE_ID)."&type=".urlencode($type))?>"><?echo GetMessage("IBLOCK_BACK_TO_ADMIN")?></a>
	<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$arIBlock["SITE_ID"] = array();
$rsSites = CIBlock::GetSite($IBLOCK_ID);
while($arSite = $rsSites->Fetch())
	$arIBlock["SITE_ID"][] = $arSite["LID"];

$bWorkFlow = $bWorkflow && (CIBlock::GetArrayByID($IBLOCK_ID, "WORKFLOW") != "N");
$bBizproc = $bBizproc && (CIBlock::GetArrayByID($IBLOCK_ID, "BIZPROC") != "N");
$minImageSize = array("W" => 1, "H"=>1);
$maxImageSize = array("W" => 50, "H"=>50);

define("MODULE_ID", "iblock");
define("ENTITY", "CIBlockDocument");
define("DOCUMENT_TYPE", "iblock_".$IBLOCK_ID);

$bCatalog = CModule::IncludeModule("catalog");
$boolSKU = false;
$boolSKUFiltrable = false;
$strSKUName = '';
$uniq_id = 0;

if ($bCatalog)
{
	$arCatalog = CCatalog::GetByIDExt($arIBlock["ID"]);
	if (false == is_array($arCatalog))
	{
		$bCatalog = false;
	}
	else
	{
		if (in_array($arCatalog['CATALOG_TYPE'],array('P','X')))
		{
			if (CIBlockRights::UserHasRightTo($arCatalog['OFFERS_IBLOCK_ID'], $arCatalog['OFFERS_IBLOCK_ID'], "iblock_admin_display"))
			{
				$boolSKU = true;
				$strSKUName = GetMessage('IBEL_A_OFFERS');
			}
		}
		if ('P' == $arCatalog['CATALOG_TYPE'])
			$bCatalog = false;
		if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_price')))
			$bCatalog = false;
	}
}

$dbrFProps = CIBlockProperty::GetList(
		Array(
			"SORT"=>"ASC",
			"NAME"=>"ASC"
		),
		Array(
			"IBLOCK_ID"=>$IBLOCK_ID,
			"ACTIVE"=>"Y",
			"CHECK_PERMISSIONS"=>"N",
		)
	);

$arProps = Array();
while($arProp = $dbrFProps->GetNext())
{
	if(strlen($arProp["USER_TYPE"])>0)
		$arUserType = CIBlockProperty::GetUserType($arProp["USER_TYPE"]);
	else
		$arUserType = array();

	$arProp["PROPERTY_USER_TYPE"] = $arUserType;

	$arProps[] = $arProp;
}

if ($boolSKU)
{
	$dbrFProps = CIBlockProperty::GetList(
		Array(
			"SORT"=>"ASC",
			"NAME"=>"ASC"
		),
		Array(
			"IBLOCK_ID"=>$arCatalog['OFFERS_IBLOCK_ID'],
			"ACTIVE"=>"Y",
			"CHECK_PERMISSIONS"=>"N",
		)
	);

	$arSKUProps = Array();
	while($arProp = $dbrFProps->GetNext())
	{
		if(strlen($arProp["USER_TYPE"])>0)
			$arUserType = CIBlockProperty::GetUserType($arProp["USER_TYPE"]);
		else
			$arUserType = array();

		$arProp["PROPERTY_USER_TYPE"] = $arUserType;

		if (('Y' == $arProp['FILTRABLE']) && ('F' != $arProp['PROPERTY_TYPE']) && ($arCatalog['OFFERS_PROPERTY_ID'] != $arProp['ID']))
			$boolSKUFiltrable = true;
		$arSKUProps[] = $arProp;
	}
}

$sTableID = (defined("CATALOG_PRODUCT")? "tbl_product_admin_": "tbl_iblock_element_").md5($type.".".$IBLOCK_ID);
$oSort = new CAdminSorting($sTableID, "timestamp_x", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);
$lAdmin->bMultipart = true;

$arFilterFields = Array(
	"find_el",
	"find_el_type",
	"find_section_section",
	"find_el_subsections",
	"find_el_id_start",
	"find_el_id_end",
	"find_el_timestamp_from",
	"find_el_timestamp_to",
	"find_el_modified_user_id",
	"find_el_modified_by",
	"find_el_created_from",
	"find_el_created_to",
	"find_el_created_user_id",
	"find_el_created_by",
	"find_el_status_id",
	"find_el_status",
	"find_el_date_active_from_from",
	"find_el_date_active_from_to",
	"find_el_date_active_to_from",
	"find_el_date_active_to_to",
	"find_el_active",
	"find_el_name",
	"find_el_intext",
	"find_el_code",
	"find_el_external_id",
	"find_el_tags",
);

foreach ($arProps as $arProp)
{
	if($arProp["FILTRABLE"]!="Y" || $arProp["PROPERTY_TYPE"]=="F")
		continue;
	$arFilterFields[] = "find_el_property_".$arProp["ID"];
}

if ($boolSKU && $boolSKUFiltrable)
{
	for($i = 0, $intPropCount = count($arSKUProps); $i < $intPropCount; $i++)
	{
		if($arSKUProps[$i]["FILTRABLE"]!="Y" || $arSKUProps[$i]["PROPERTY_TYPE"]=="F" || $arCatalog['OFFERS_PROPERTY_ID'] == $arSKUProps[$i]['ID'])
			continue;
		$arFilterFields[] = "find_sub_el_property_".$arSKUProps[$i]["ID"];
	}
}

if(isset($_REQUEST["del_filter"]) && $_REQUEST["del_filter"] != "")
	$find_section_section = -1;
elseif(isset($_REQUEST["find_section_section"]))
	$find_section_section = $_REQUEST["find_section_section"];
else
	$find_section_section = -1;

//We have to handle current section in a special way
$section_id = intval($find_section_section);
$lAdmin->InitFilter($arFilterFields);
if(!defined("CATALOG_PRODUCT"))
	$find_section_section = $section_id;
//This is all parameters needed for proper navigation
$sThisSectionUrl = '&type='.urlencode($type).'&lang='.urlencode(LANGUAGE_ID).'&IBLOCK_ID='.$IBLOCK_ID.'&find_section_section='.intval($find_section_section);

$arFilter = array(
	"IBLOCK_ID" => $IBLOCK_ID,
	"SECTION_ID" => $find_section_section,
	"MODIFIED_USER_ID" => $find_el_modified_user_id,
	"MODIFIED_BY" => $find_el_modified_by,
	"CREATED_USER_ID" => $find_el_created_user_id,
	"ACTIVE" => $find_el_active,
	"CODE" => $find_el_code,
	"EXTERNAL_ID" => $find_el_external_id,
	"?TAGS" => $find_el_tags,
	"?NAME" => ($find_el!='' && $find_el_type == "name"? $find_el: $find_el_name),
	"?SEARCHABLE_CONTENT" => ($find_el!='' && $find_el_type == "desc"? $find_el: $find_el_intext),
	"SHOW_NEW" => "Y",
	"CHECK_PERMISSIONS" => "Y",
	"MIN_PERMISSION" => "R",
);

if ($bBizproc && 'E' != $arIBlock['RIGHTS_MODE'])
{
	$strPerm = CIBlock::GetPermission($IBLOCK_ID);
	if ('W' > $strPerm)
	{
		unset($arFilter['CHECK_PERMISSIONS']);
		unset($arFilter['MIN_PERMISSION']);
		$arFilter['CHECK_BP_PERMISSIONS'] = 'read';
	}
}

for($i=0, $intPropCount = count($arProps); $i < $intPropCount; $i++)
{
	if (('Y' == $arProps[$i]["FILTRABLE"]) && ('F' != $arProps[$i]["PROPERTY_TYPE"]))
	{
		if (array_key_exists("AddFilterFields", $arProps[$i]["PROPERTY_USER_TYPE"]))
		{
			call_user_func_array($arProps[$i]["PROPERTY_USER_TYPE"]["AddFilterFields"], array(
				$arProps[$i],
				array("VALUE" => "find_el_property_".$arProps[$i]["ID"]),
				&$arFilter,
				&$filtered,
			));
		}
		else
		{
			$value = ${"find_el_property_".$arProps[$i]["ID"]};
			if(is_array($value) || strlen($value))
			{
				if($value === "NOT_REF")
					$value = false;
				$arFilter["?PROPERTY_".$arProps[$i]["ID"]] = $value;
			}
		}
	}
}

$arSubQuery = array("IBLOCK_ID" => $arCatalog['OFFERS_IBLOCK_ID']);
if ($boolSKU && $boolSKUFiltrable)
{
	for($i = 0, $intPropCount = count($arSKUProps); $i < $intPropCount; $i++)
	{
		if (('Y' == $arSKUProps[$i]["FILTRABLE"]) && ('F' != $arSKUProps[$i]["PROPERTY_TYPE"]) && ($arCatalog['OFFERS_PROPERTY_ID'] != $arSKUProps[$i]["ID"]))
		{
			if (array_key_exists("AddFilterFields", $arSKUProps[$i]["PROPERTY_USER_TYPE"]))
			{
				call_user_func_array($arSKUProps[$i]["PROPERTY_USER_TYPE"]["AddFilterFields"], array(
					$arSKUProps[$i],
					array("VALUE" => "find_sub_el_property_".$arSKUProps[$i]["ID"]),
					&$arSubQuery,
					&$filtered,
				));
			}
			else
			{
				$value = ${"find_sub_el_property_".$arSKUProps[$i]["ID"]};
				if(is_array($value) || strlen($value))
				{
					if($value === "NOT_REF")
						$value = false;
					$arSubQuery["?PROPERTY_".$arSKUProps[$i]["ID"]] = $value;
				}
			}
		}
	}
}

if (($boolSKU) && (1 < sizeof($arSubQuery)))
{
	$arFilter['ID'] = CIBlockElement::SubQuery('PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID'], $arSubQuery);
}

if(IntVal($find_section_section)<0 || strlen($find_section_section)<=0)
	unset($arFilter["SECTION_ID"]);

elseif($find_el_subsections=="Y")
{
	if($arFilter["SECTION_ID"]==0)
		unset($arFilter["SECTION_ID"]);
	else
		$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
}

if(!empty($find_el_id_start))
	$arFilter[">=ID"] = $find_el_id_start;
if(!empty($find_el_id_end))
	$arFilter["<=ID"] = $find_el_id_end;
if(!empty($find_el_timestamp_from))
	$arFilter["DATE_MODIFY_FROM"] = $find_el_timestamp_from;
if(!empty($find_el_timestamp_to))
	$arFilter["DATE_MODIFY_TO"] = CIBlock::isShortDate($find_el_timestamp_to)? ConvertTimeStamp(AddTime(MakeTimeStamp($find_el_timestamp_to), 1, "D"), "FULL"): $find_el_timestamp_to;
if(!empty($find_el_created_from))
	$arFilter[">=DATE_CREATE"] = $find_el_created_from;
if(!empty($find_el_created_to))
	$arFilter["<=DATE_CREATE"] = CIBlock::isShortDate($find_el_created_to)? ConvertTimeStamp(AddTime(MakeTimeStamp($find_el_created_to), 1, "D"), "FULL"): $find_el_created_to;
if(!empty($find_el_created_by) && strlen($find_el_created_by)>0)
	$arFilter["CREATED_BY"] = $find_el_created_by;
if(!empty($find_el_status_id))
{
	$intPos = strpos($find_el_status_id,'-');
	$arFilter["WF_STATUS"] = (false !== $intPos ? substr($find_el_status_id,0,$intPos) : $find_el_status_id);
}
if(!empty($find_el_status) && strlen($find_el_status)>0)
	$arFilter["WF_STATUS"] = $find_el_status;
if(!empty($find_el_date_active_from_from))
	$arFilter[">=DATE_ACTIVE_FROM"] = $find_el_date_active_from_from;
if(!empty($find_el_date_active_from_to))
	$arFilter["<=DATE_ACTIVE_FROM"] = $find_el_date_active_from_to;
if(!empty($find_el_date_active_to_from))
	$arFilter[">=DATE_ACTIVE_TO"] = $find_el_date_active_to_from;
if(!empty($find_el_date_active_to_to))
	$arFilter["<=DATE_ACTIVE_TO"] = $find_el_date_active_to_to;

if($lAdmin->EditAction())
{
	if(is_array($_FILES['FIELDS']))
		CAllFile::ConvertFilesToPost($_FILES['FIELDS'], $_POST['FIELDS']);
	if(is_array($FIELDS_del))
		CAllFile::ConvertFilesToPost($FIELDS_del, $_POST['FIELDS'], "del");

	foreach($_POST['FIELDS'] as $ID=>$arFields)
	{
		if(!$lAdmin->IsUpdated($ID))
			continue;
		$ID = IntVal($ID);

		$arRes = CIBlockElement::GetByID($ID);
		$arRes = $arRes->Fetch();
		if(!$arRes)
			continue;

		$WF_ID = $ID;
		if($bWorkFlow)
		{
			$WF_ID = CIBlockElement::WF_GetLast($ID);
			if($WF_ID!=$ID)
			{
				$rsData2 = CIBlockElement::GetByID($WF_ID);
				if($arRes = $rsData2->Fetch())
					$WF_ID = $arRes["ID"];
				else
					$WF_ID = $ID;
			}

			if($arRes["LOCK_STATUS"]=='red' && !($_REQUEST['action']=='unlock' && CWorkflow::IsAdmin()))
			{
				$lAdmin->AddUpdateError(GetMessage("IBEL_A_UPDERR1")." (ID:".$ID.")", $ID);
				continue;
			}
		}
		elseif ($bBizproc)
		{
			if (CIBlockDocument::IsDocumentLocked($ID, ""))
			{
				$lAdmin->AddUpdateError(GetMessage("IBEL_A_UPDERR_LOCKED", array("#ID#" => $ID)), $ID);
				continue;
			}
		}

		if($bWorkFlow)
		{
			if (!CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ID, "element_edit"))
			{
				$lAdmin->AddUpdateError(GetMessage("IBEL_A_UPDERR3")." (ID:".$ID.")", $ID);
				continue;
			}
			$STATUS_PERMISSION = 2;
			// change is under workflow find status and its permissions
			if (!CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ID, "element_edit_any_wf_status"))
				$STATUS_PERMISSION = CIBlockElement::WF_GetStatusPermission($arRes["WF_STATUS_ID"]);
			if($STATUS_PERMISSION < 2)
			{
				$lAdmin->AddUpdateError(GetMessage("IBEL_A_UPDERR3")." (ID:".$ID.")", $ID);
				continue;
			}

			// status change  - check permissions
			if(isset($arFields["WF_STATUS_ID"]))
			{
				if(CIBlockElement::WF_GetStatusPermission($arFields["WF_STATUS_ID"])<1)
				{
					$lAdmin->AddUpdateError(GetMessage("IBEL_A_UPDERR2")." (ID:".$ID.")", $ID);
					continue;
				}
			}
		}
		elseif($bBizproc)
		{
			$bCanWrite = CIBlockDocument::CanUserOperateDocument(
				CBPCanUserOperateOperation::WriteDocument,
				$USER->GetID(),
				$ID,
				array(
					"IBlockId" => $IBLOCK_ID,
					'IBlockRightsMode' => $arIBlock['RIGHTS_MODE'],
					'UserGroups' => $USER->GetUserGroupArray()
				)
			);
			if(!$bCanWrite)
			{
				$lAdmin->AddUpdateError(GetMessage("IBEL_A_UPDERR3")." (ID:".$ID.")", $ID);
				continue;
			}
		}
		elseif(!CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ID, "element_edit"))
		{
			$lAdmin->AddUpdateError(GetMessage("IBEL_A_UPDERR3")." (ID:".$ID.")", $ID);
			continue;
		}

		if(isset($arFields["PREVIEW_PICTURE"]) && !is_array($arFields["PREVIEW_PICTURE"]))
			$arFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($arFields["PREVIEW_PICTURE"]);

		if(isset($arFields["PREVIEW_PICTURE"]) && is_array($arFields["PREVIEW_PICTURE"]))
		{
			if(isset($_REQUEST["FIELDS_descr"][$ID]["PREVIEW_PICTURE"]))
				$arFields["PREVIEW_PICTURE"]["description"] = $_REQUEST["FIELDS_descr"][$ID]["PREVIEW_PICTURE"];
		}

		if(isset($arFields["DETAIL_PICTURE"]) && !is_array($arFields["DETAIL_PICTURE"]))
			$arFields["DETAIL_PICTURE"] = CFile::MakeFileArray($arFields["DETAIL_PICTURE"]);

		if(isset($arFields["DETAIL_PICTURE"]) && is_array($arFields["DETAIL_PICTURE"]))
		{
			if(isset($_REQUEST["FIELDS_descr"][$ID]["DETAIL_PICTURE"]))
				$arFields["DETAIL_PICTURE"]["description"] = $_REQUEST["FIELDS_descr"][$ID]["DETAIL_PICTURE"];
		}

		if(!is_array($arFields["PROPERTY_VALUES"]))
			$arFields["PROPERTY_VALUES"] = Array();
		$bFieldProps = array();
		foreach($arFields as $k=>$v)
		{
			if(
				substr($k, 0, strlen("PROPERTY_")) == "PROPERTY_"
				&& $k != "PROPERTY_VALUES"
			)
			{
				$prop_id = substr($k, strlen("PROPERTY_"));

				if(isset($_REQUEST["FIELDS_descr"][$ID][$k]) && is_array($_REQUEST["FIELDS_descr"][$ID][$k]))
				{
					foreach($_REQUEST["FIELDS_descr"][$ID][$k] as $PROPERTY_VALUE_ID => $ar)
					{
						if(
							is_array($ar)
							&& isset($ar["VALUE"])
							&& isset($v[$PROPERTY_VALUE_ID]["VALUE"])
							&& is_array($v[$PROPERTY_VALUE_ID]["VALUE"])
						)
							$v[$PROPERTY_VALUE_ID]["DESCRIPTION"] = $ar["VALUE"];
					}
				}

				$arFields["PROPERTY_VALUES"][$prop_id] = $v;
				unset($arFields[$k]);
				$bFieldProps[$prop_id]=true;
			}
		}

		if(count($bFieldProps) > 0)
		{
			//We have to read properties from database in order not to delete its values
			if(!$bWorkFlow)
			{
				$dbPropV = CIBlockElement::GetProperty($IBLOCK_ID, $ID, "sort", "asc", Array("ACTIVE"=>"Y"));
				while($arPropV = $dbPropV->Fetch())
				{
					if(!array_key_exists($arPropV["ID"], $bFieldProps) && $arPropV["PROPERTY_TYPE"] != "F")
					{
						if(!array_key_exists($arPropV["ID"], $arFields["PROPERTY_VALUES"]))
							$arFields["PROPERTY_VALUES"][$arPropV["ID"]] = array();

						$arFields["PROPERTY_VALUES"][$arPropV["ID"]][$arPropV["PROPERTY_VALUE_ID"]] = array(
							"VALUE" => $arPropV["VALUE"],
							"DESCRIPTION" => $arPropV["DESCRIPTION"],
						);
					}
				}
			}
		}
		else
		{
			//We will not update property values
			unset($arFields["PROPERTY_VALUES"]);
		}

		//All not displayed required fields from DB
		foreach($arIBlock["FIELDS"] as $FIELD_ID => $field)
		{
			if(
				$field["IS_REQUIRED"] === "Y"
				&& !array_key_exists($FIELD_ID, $arFields)
				&& $FIELD_ID !== "DETAIL_PICTURE"
				&& $FIELD_ID !== "PREVIEW_PICTURE"
			)
				$arFields[$FIELD_ID] = $arRes[$FIELD_ID];
		}
		if($arRes["IN_SECTIONS"] == "Y")
		{
			$arFields["IBLOCK_SECTION"] = array();
			$rsSections = CIBlockElement::GetElementGroups($arRes["ID"], true);
			while($arSection = $rsSections->Fetch())
				$arFields["IBLOCK_SECTION"][] = $arSection["ID"];
		}

		$arFields["MODIFIED_BY"] = $USER->GetID();
		$ib = new CIBlockElement();
		$DB->StartTransaction();

		if(!$ib->Update($ID, $arFields, true, true, true))
		{
			$lAdmin->AddUpdateError(GetMessage("IBEL_A_SAVE_ERROR", array("#ID#"=>$ID, "#ERROR_TEXT#"=>$ib->LAST_ERROR)), $ID);
			$DB->Rollback();
		}
		else
		{
			$DB->Commit();
		}

		if ($bCatalog)
		{
			if(
				$USER->CanDoOperation('catalog_price')
				&& CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ID, "element_edit_price")
			)
			{
				$CATALOG_QUANTITY = $arFields["CATALOG_QUANTITY"];
				$CATALOG_QUANTITY_TRACE = $arFields["CATALOG_QUANTITY_TRACE"];
				$CATALOG_WEIGHT = $arFields["CATALOG_WEIGHT"];

				if(!CCatalogProduct::GetByID($ID))
				{
					$arCatalogQuantity = Array("ID" => $ID);
					if(strlen($CATALOG_QUANTITY) > 0)
						$arCatalogQuantity["QUANTITY"] = $CATALOG_QUANTITY;
					if(strlen($CATALOG_QUANTITY_TRACE) > 0)
						$arCatalogQuantity["QUANTITY_TRACE"] = (($CATALOG_QUANTITY_TRACE == "Y") ? "Y" : (($CATALOG_QUANTITY_TRACE == "D") ? "D" : "N"));
					if (strlen($CATALOG_WEIGHT) > 0)
						$arCatalogQuantity['WEIGHT'] = $CATALOG_WEIGHT;
					CCatalogProduct::Add($arCatalogQuantity);
				}
				else
				{
					$arCatalogQuantity = Array();
					if(strlen($CATALOG_QUANTITY) > 0)
						$arCatalogQuantity["QUANTITY"] = $CATALOG_QUANTITY;
					if(strlen($CATALOG_QUANTITY_TRACE) > 0)
						$arCatalogQuantity["QUANTITY_TRACE"] = (($CATALOG_QUANTITY_TRACE == "Y") ? "Y" : (($CATALOG_QUANTITY_TRACE == "D") ? "D" : "N"));
					if (strlen($CATALOG_WEIGHT) > 0)
						$arCatalogQuantity['WEIGHT'] = $CATALOG_WEIGHT;
					if(!empty($arCatalogQuantity))
						CCatalogProduct::Update($ID, $arCatalogQuantity);
				}
			}
		}
	}

	if($bCatalog)
	{
		if($USER->CanDoOperation('catalog_price') && (isset($_POST["CATALOG_PRICE"]) || isset($_POST["CATALOG_CURRENCY"])))
		{
			$CATALOG_PRICE = $_POST["CATALOG_PRICE"];
			$CATALOG_CURRENCY = $_POST["CATALOG_CURRENCY"];
			$CATALOG_EXTRA = $_POST["CATALOG_EXTRA"];
			$CATALOG_PRICE_ID = $_POST["CATALOG_PRICE_ID"];
			$CATALOG_QUANTITY_FROM = $_POST["CATALOG_QUANTITY_FROM"];
			$CATALOG_QUANTITY_TO = $_POST["CATALOG_QUANTITY_TO"];
			$CATALOG_PRICE_old = $_POST["CATALOG_old_PRICE"];
			$CATALOG_CURRENCY_old = $_POST["CATALOG_old_CURRENCY"];

			$db_extras = CExtra::GetList(($by3="NAME"), ($order3="ASC"));
			while ($extras = $db_extras->Fetch())
				$arCatExtraUp[$extras["ID"]] = $extras["PERCENTAGE"];

			foreach($CATALOG_PRICE as $elID => $arPrice)
			{
				if (
					!(CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $elID, "element_edit")
					&& CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $elID, "element_edit_price"))
				)
					continue;
				//1 Find base price ID
				//2 If such a column is displayed then
				//	check if it is greater than 0
				//3 otherwise
				//	look up it's value in database and
				//	output an error if not found or found less or equal then zero
				$bError = false;
				$arBaseGroup = CCatalogGroup::GetBaseGroup();

				if (COption::GetOptionString('catalog','save_product_without_price','N') != 'Y')
				{
					if (isset($arPrice[$arBaseGroup['ID']]))
					{
						if ($arPrice[$arBaseGroup['ID']] <= 0)
						{
							$bError = true;
							$lAdmin->AddUpdateError($elID.': '.GetMessage('IB_CAT_NO_BASE_PRICE'), $elID);
						}
					}
					else
					{
						$arBasePrice = CPrice::GetBasePrice(
							$elID,
							$CATALOG_QUANTITY_FROM[$elID][$arBaseGroup['ID']],
							$CATALOG_QUANTITY_FROM[$elID][$arBaseGroup['ID']]
						);

						if (!is_array($arBasePrice) || $arBasePrice['PRICE'] <= 0)
						{
							$bError = true;
							$lAdmin->AddGroupError($elID.': '.GetMessage('IB_CAT_NO_BASE_PRICE'), $elID);
						}
					}
				}

				if($bError)
					continue;

				$arCurrency = $CATALOG_CURRENCY[$elID];

				$dbCatalogGroups = CCatalogGroup::GetList(
						array("SORT" => "ASC"),
						array("LID"=>LANGUAGE_ID)
					);
				while ($arCatalogGroup = $dbCatalogGroups->Fetch())
				{
					if(doubleval($arPrice[$arCatalogGroup["ID"]]) != doubleval($CATALOG_PRICE_old[$elID][$arCatalogGroup["ID"]])
						|| $arCurrency[$arCatalogGroup["ID"]] != $CATALOG_CURRENCY_old[$elID][$arCatalogGroup["ID"]])
					{
						if($arCatalogGroup["BASE"]=="Y") // if base price check extra for other prices
						{
							$arFields = Array(
								"PRODUCT_ID" => $elID,
								"CATALOG_GROUP_ID" => $arCatalogGroup["ID"],
								"PRICE" => DoubleVal($arPrice[$arCatalogGroup["ID"]]),
								"CURRENCY" => $arCurrency[$arCatalogGroup["ID"]],
								"QUANTITY_FROM" => $CATALOG_QUANTITY_FROM[$elID][$arCatalogGroup["ID"]],
								"QUANTITY_TO" => $CATALOG_QUANTITY_TO[$elID][$arCatalogGroup["ID"]],
							);
							if($arFields["PRICE"] <=0 )
							{
								CPrice::Delete($CATALOG_PRICE_ID[$elID][$arCatalogGroup["ID"]]);
							}
							elseif(IntVal($CATALOG_PRICE_ID[$elID][$arCatalogGroup["ID"]])>0)
							{
								CPrice::Update(IntVal($CATALOG_PRICE_ID[$elID][$arCatalogGroup["ID"]]), $arFields);
							}
							elseif($arFields["PRICE"] > 0)
							{
								CPrice::Add($arFields);
							}

							$arPrFilter = array(
								"PRODUCT_ID" => $elID,
							);
							if(DoubleVal($arPrice[$arCatalogGroup["ID"]])>0)
							{
								$arPrFilter["!CATALOG_GROUP_ID"] = $arCatalogGroup["ID"];
								$arPrFilter["+QUANTITY_FROM"] = "1";
								$arPrFilter["!EXTRA_ID"] = false;
							}
							$db_res = CPrice::GetList(
								array(),
								$arPrFilter,
								false,
								false,
								Array("ID", "PRODUCT_ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO", "EXTRA_ID")
							);
							while($ar_res = $db_res->Fetch())
							{
								$arFields = Array(
									"PRICE" => DoubleVal($arPrice[$arCatalogGroup["ID"]])*(1+$arCatExtraUp[$ar_res["EXTRA_ID"]]/100) ,
									"EXTRA_ID" => $ar_res["EXTRA_ID"],
									"CURRENCY" => $arCurrency[$arCatalogGroup["ID"]],
									"QUANTITY_FROM" => $ar_res["QUANTITY_FROM"],
									"QUANTITY_TO" => $ar_res["QUANTITY_TO"]
								);
								if($arFields["PRICE"] <= 0)
									CPrice::Delete($ar_res["ID"]);
								else
									CPrice::Update($ar_res["ID"], $arFields);
							}
						}
						elseif(!isset($CATALOG_EXTRA[$elID][$arCatalogGroup["ID"]]))
						{
							$arFields = Array(
								"PRODUCT_ID" => $elID,
								"CATALOG_GROUP_ID" => $arCatalogGroup["ID"],
								"PRICE" => DoubleVal($arPrice[$arCatalogGroup["ID"]]),
								"CURRENCY" => $arCurrency[$arCatalogGroup["ID"]],
								"QUANTITY_FROM" => $CATALOG_QUANTITY_FROM[$elID][$arCatalogGroup["ID"]],
								"QUANTITY_TO" => $CATALOG_QUANTITY_TO[$elID][$arCatalogGroup["ID"]]
							);
							if($arFields["PRICE"] <= 0)
								CPrice::Delete($CATALOG_PRICE_ID[$elID][$arCatalogGroup["ID"]]);
							elseif(IntVal($CATALOG_PRICE_ID[$elID][$arCatalogGroup["ID"]])>0)
								CPrice::Update(IntVal($CATALOG_PRICE_ID[$elID][$arCatalogGroup["ID"]]), $arFields);
							elseif($arFields["PRICE"] > 0)
								CPrice::Add($arFields);
						}
					}
				}
			}
		}
	}
}

if(($arID = $lAdmin->GroupAction()))
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = CIBlockElement::GetList(Array($by=>$order), $arFilter);
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	foreach($arID as $ID)
	{
		if(strlen($ID)<=0)
			continue;

		$ID = IntVal($ID);
		$arRes = CIBlockElement::GetByID($ID);
		$arRes = $arRes->Fetch();
		if(!$arRes)
			continue;

		$WF_ID = $ID;
		if($bWorkFlow)
		{
			$WF_ID = CIBlockElement::WF_GetLast($ID);
			if($WF_ID != $ID)
			{
				$rsData2 = CIBlockElement::GetByID($WF_ID);
				if($arRes = $rsData2->Fetch())
					$WF_ID = $arRes["ID"];
				else
					$WF_ID = $ID;
			}

			if($arRes["LOCK_STATUS"]=='red' && !($_REQUEST['action']=='unlock' && CWorkflow::IsAdmin()))
			{
				$lAdmin->AddGroupError(GetMessage("IBEL_A_UPDERR1")." (ID:".$ID.")", $ID);
				continue;
			}
		}
		elseif ($bBizproc)
		{
			if (CIBlockDocument::IsDocumentLocked($ID, "") && !($_REQUEST['action']=='unlock' && CBPDocument::IsAdmin()))
			{
				$lAdmin->AddUpdateError(GetMessage("IBEL_A_UPDERR_LOCKED", array("#ID#" => $ID)), $ID);
				continue;
			}
		}

		$bPermissions = false;
		//delete and modify can:
		if ($bWorkFlow)
		{
			//For delete action we have to check all statuses in element history
			$STATUS_PERMISSION = CIBlockElement::WF_GetStatusPermission($arRes["WF_STATUS_ID"], $_REQUEST['action']=="delete"? $ID: false);
			if($STATUS_PERMISSION >= 2)
				$bPermissions = true;
		}
		elseif ($bBizproc)
		{
			$bCanWrite = CIBlockDocument::CanUserOperateDocument(
				CBPCanUserOperateOperation::WriteDocument,
				$USER->GetID(),
				$ID,
				array(
					"IBlockId" => $IBLOCK_ID,
					'IBlockRightsMode' => $arIBlock['RIGHTS_MODE'],
					'UserGroups' => $USER->GetUserGroupArray(),
				)
			);
			if ($bCanWrite)
				$bPermissions = true;
		}
		else
		{
			$bPermissions = true;
		}

		if(!$bPermissions)
		{
			$lAdmin->AddGroupError(GetMessage("IBEL_A_UPDERR3")." (ID:".$ID.")", $ID);
			continue;
		}

		switch($_REQUEST['action'])
		{
		case "delete":
			if(CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ID, "element_delete"))
			{
				@set_time_limit(0);
				$DB->StartTransaction();
				$APPLICATION->ResetException();
				if(!CIBlockElement::Delete($ID))
				{
					$DB->Rollback();
					if($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError(GetMessage("IBLOCK_DELETE_ERROR")." [".$ex->GetString()."]", $ID);
					else
						$lAdmin->AddGroupError(GetMessage("IBLOCK_DELETE_ERROR"), $ID);
				}
				else
				{
					$DB->Commit();
				}
			}
			else
			{
				$lAdmin->AddGroupError(GetMessage("IBLOCK_DELETE_ERROR")." [".$ID."]", $ID);
			}
			break;
		case "activate":
		case "deactivate":
			if(CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ID, "element_edit"))
			{
				$ob = new CIBlockElement();
				$arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"Y":"N"));
				if(!$ob->Update($ID, $arFields, true))
					$lAdmin->AddGroupError(GetMessage("IBEL_A_UPDERR").$ob->LAST_ERROR, $ID);
			}
			else
			{
				$lAdmin->AddGroupError(GetMessage("IBEL_A_UPDERR3")." (ID:".$ID.")", $ID);
			}
			break;
		case "section":
		case "add_section":
			$new_section = intval($_REQUEST["section_to_move"]);
			if(
				($new_section >= 0)
				&& ($new_section != $section_id)
			)
			{
				if (CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $new_section, "section_element_bind"))
				{
					$obE = new CIBlockElement();

					$arSections = array($new_section);
					if($_REQUEST['action'] == "add_section")
					{
						$rsSections = $obE->GetElementGroups($ID, true);
						while($ar = $rsSections->Fetch())
							$arSections[] = $ar["ID"];
					}

					if(!$obE->Update($ID, array("IBLOCK_SECTION" => $arSections)))
						$lAdmin->AddGroupError(GetMessage("IBEL_A_SAVE_ERROR", array("#ID#" => $ID, "#ERROR_TEXT#" => $obE->LAST_ERROR)), $ID);
				}
				else
				{
					$lAdmin->AddGroupError(GetMessage("IBEL_A_UPDERR3")." (ID:".$ID.")", $ID);
				}
			}
			break;
		case "wf_status":
			if($bWorkFlow)
			{
				$new_status = intval($_REQUEST["wf_status_id"]);
				if(
					$new_status > 0
				)
				{
					if (CIBlockElement::WF_GetStatusPermission($new_status) > 0
						|| CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ID, "element_edit_any_wf_status"))
					{
						if($arRes["WF_STATUS_ID"] != $new_status)
						{
							$obE = new CIBlockElement();
							$res = $obE->Update($ID, array(
								"WF_STATUS_ID" => $new_status,
								"MODIFIED_BY" => $USER->GetID(),
							), true);
							if(!$res)
								$lAdmin->AddGroupError(GetMessage("IBEL_A_SAVE_ERROR", array("#ID#" => $ID, "#ERROR_TEXT#" => $obE->LAST_ERROR)), $ID);
						}
					}
					else
					{
						$lAdmin->AddGroupError(GetMessage("IBEL_A_UPDERR3")." (ID:".$ID.")", $ID);
					}
				}
			}
			break;
		case "lock":
			if($bWorkFlow && !CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ID, "element_edit"))
			{
				$lAdmin->AddGroupError(GetMessage("IBEL_A_UPDERR3")." (ID:".$ID.")", $ID);
				continue;
			}
			CIBlockElement::WF_Lock($ID);
			break;
		case "unlock":
			if ($bWorkFlow && !CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ID, "element_edit"))
			{
				$lAdmin->AddGroupError(GetMessage("IBEL_A_UPDERR3")." (ID:".$ID.")", $ID);
				continue;
			}
			if ($bBizproc)
				call_user_func(array(ENTITY, "UnlockDocument"), $ID, "");
			else
				CIBlockElement::WF_UnLock($ID);
			break;
		}
	}

	if(isset($return_url) && strlen($return_url)>0)
		LocalRedirect($return_url);
}

$CAdminCalendar_ShowScript = CAdminCalendar::ShowScript();

$arHeader = array();
$arHeader[] = array(
	"id" => "NAME",
	"content" => GetMessage("IBLOCK_FIELD_NAME"),
	"title" => "",
	"sort" => "name",
	"default" => true,
);
$arHeader[] = array(
	"id" => "ACTIVE",
	"content" => GetMessage("IBLOCK_FIELD_ACTIVE"),
	"title" => "",
	"sort" => "active",
	"default" => true,
	"align" => "center",
);
$arHeader[] = array(
	"id" => "DATE_ACTIVE_FROM",
	"content" => GetMessage("IBEL_A_ACTFROM"),
	"title" => "",
	"sort" => "date_active_from",
	"default" => false,
);
$arHeader[] = array(
	"id" => "DATE_ACTIVE_TO",
	"content" => GetMessage("IBEL_A_ACTTO"),
	"title" => "",
	"sort" => "date_active_to",
	"default" => false,
);
$arHeader[] = array(
	"id" => "SORT",
	"content" => GetMessage("IBLOCK_FIELD_SORT"),
	"title" => "",
	"sort" => "sort",
	"default" => true,
	"align" => "right",
);
$arHeader[] = array(
	"id" => "TIMESTAMP_X",
	"content" => GetMessage("IBLOCK_FIELD_TIMESTAMP_X"),
	"title" => "",
	"sort" => "timestamp_x",
	"default" => true,
);
$arHeader[] = array(
	"id" => "USER_NAME",
	"content" => GetMessage("IBLOCK_FIELD_USER_NAME"),
	"title" => "",
	"sort" => "modified_by",
	"default" => false,
);
$arHeader[] = array(
	"id" => "DATE_CREATE",
	"content" => GetMessage("IBLOCK_EL_ADMIN_DCREATE"),
	"title" => "",
	"sort" => "created",
	"default" => false,
);
$arHeader[] = array(
	"id" => "CREATED_USER_NAME",
	"content" => GetMessage("IBLOCK_EL_ADMIN_WCREATE2"),
	"title" => "",
	"sort" => "created_by",
	"default" => false,
);
$arHeader[] = array(
	"id" => "CODE",
	"content" => GetMessage("IBEL_A_CODE"),
	"title" => "",
	"sort" => "code",
	"default" => false,
);
$arHeader[] = array(
	"id" => "EXTERNAL_ID",
	"content" => GetMessage("IBEL_A_EXTERNAL_ID"),
	"title" => "",
	"sort" => "external_id",
	"default" => false,
);
$arHeader[] = array(
	"id" => "TAGS",
	"content" => GetMessage("IBEL_A_TAGS"),
	"title" => "",
	"sort" => "tags",
	"default" => false,
);

if($bWorkFlow)
{
	$arHeader[] = array(
		"id" => "WF_STATUS_ID",
		"content" => GetMessage("IBLOCK_FIELD_STATUS"),
		"title" => "",
		"sort" => "status",
		"default" => true,
	);
	$arHeader[] = array(
		"id" => "WF_NEW",
		"content" => GetMessage("IBEL_A_EXTERNAL_WFNEW"),
		"title" => "",
		"sort" => "",
		"default" => false,
	);
	$arHeader[] = array(
		"id" => "LOCK_STATUS",
		"content" => GetMessage("IBEL_A_EXTERNAL_LOCK"),
		"title" => "",
		"default" => true,
	);
	$arHeader[] = array(
		"id" => "LOCKED_USER_NAME",
		"content" => GetMessage("IBEL_A_EXTERNAL_LOCK_BY"),
		"title" => "",
		"default" => false,
	);
	$arHeader[] = array(
		"id" => "WF_DATE_LOCK",
		"content" => GetMessage("IBEL_A_EXTERNAL_LOCK_WHEN"),
		"title" => "",
		"default" => false,
	);
	$arHeader[] = array(
		"id" => "WF_COMMENTS",
		"content" => GetMessage("IBEL_A_EXTERNAL_COM"),
		"title" => "",
		"default" => false,
	);
}

$arHeader[] = array(
	"id" => "SHOW_COUNTER",
	"content" => GetMessage("IBEL_A_EXTERNAL_SHOWS"),
	"title" => "",
	"sort" => "show_counter",
	"align" => "right",
	"default" => false,
);
$arHeader[] = array(
	"id" => "SHOW_COUNTER_START",
	"content" => GetMessage("IBEL_A_EXTERNAL_SHOW_F"),
	"title" => "",
	"sort" => "show_counter_start",
	"align" => "right",
	"default" => false,
);
$arHeader[] = array(
	"id" => "PREVIEW_PICTURE",
	"content" => GetMessage("IBEL_A_EXTERNAL_PREV_PIC"),
	"title" => "",
	"align" => "right",
	"default" => false,
);
$arHeader[] = array(
	"id" => "PREVIEW_TEXT",
	"content" => GetMessage("IBEL_A_EXTERNAL_PREV_TEXT"),
	"title" => "",
	"default" => false,
);
$arHeader[] = array(
	"id" => "DETAIL_PICTURE",
	"content" => GetMessage("IBEL_A_EXTERNAL_DET_PIC"),
	"title" => "",
	"align" => "center",
	"default" => false,
);
$arHeader[] = array(
	"id" => "DETAIL_TEXT",
	"content" => GetMessage("IBEL_A_EXTERNAL_DET_TEXT"),
	"title" => "",
	"default" => false,
);
$arHeader[] = array(
	"id" => "ID",
	"content" => "ID",
	"title" => "",
	"sort" => "id",
	"default" => true,
	"align" => "right",
);

foreach($arProps as $arFProps)
{
	$arHeader[] = array(
		"id" => "PROPERTY_".$arFProps['ID'],
		"content" => $arFProps['NAME'],
		"title" => "",
		"align" => ($arFProps["PROPERTY_TYPE"]=='N'? "right": "left"),
		"sort" => ($arFProps["MULTIPLE"]!='Y'? "PROPERTY_".$arFProps['ID']: ""),
		"default" => false,
	);
}
unset($arFProps);

$arWFStatus = Array();
if($bWorkFlow)
{
	$rsWF = CWorkflowStatus::GetDropDownList("Y");
	while($arWF = $rsWF->GetNext())
		$arWFStatus[$arWF["~REFERENCE_ID"]] = $arWF["~REFERENCE"];
}

if($bCatalog)
{
	$arHeader[] = array(
		"id" => "CATALOG_QUANTITY",
		"content" => GetMessage("IBEL_CATALOG_QUANTITY"),
		"title" => "",
		"align" => "right",
		"sort" => "CATALOG_QUANTITY",
		"default" => false,
	);
	$arHeader[] = array(
		"id" => "CATALOG_QUANTITY_TRACE",
		"content" => GetMessage("IBEL_CATALOG_QUANTITY_TRACE"),
		"title" => "",
		"align" => "right",
		"default" => false,
	);
	$arHeader[] = array(
		"id" => "CATALOG_WEIGHT",
		"content" => GetMessage("IBEL_CATALOG_WEIGHT"),
		"title" => "",
		"align" => "right",
		"sort" => "CATALOG_WEIGHT",
		"default" => false,
	);

	$arCatGroup = array();
	$dbCatalogGroups = CCatalogGroup::GetList(
		array("SORT" => "ASC"),
		array("LID"=>LANGUAGE_ID)
	);
	while ($arCatalogGroup = $dbCatalogGroups->Fetch())
	{
		$arHeader[] = array(
			"id" => "CATALOG_GROUP_".$arCatalogGroup["ID"],
			"content" => htmlspecialcharsex(!empty($arCatalogGroup["NAME_LANG"]) ? $arCatalogGroup["NAME_LANG"] : $arCatalogGroup["NAME"]),
			"title" => "",
			"align" => "right",
			"sort" => "CATALOG_PRICE_".$arCatalogGroup["ID"],
			"default" => false,
		);
		$arCatGroup[$arCatalogGroup["ID"]] = $arCatalogGroup;
	}

	$arCatExtra = array();
	$db_extras = CExtra::GetList(($by3="NAME"), ($order3="ASC"));
	while ($extras = $db_extras->Fetch())
		$arCatExtra[] = $extras;
}

if ($bBizproc)
{
	$arWorkflowTemplates = CBPDocument::GetWorkflowTemplatesForDocumentType(array("iblock", "CIBlockDocument", "iblock_".$IBLOCK_ID));
	foreach ($arWorkflowTemplates as $arTemplate)
	{
		$arHeader[] = array(
			"id" => "WF_".$arTemplate["ID"],
			"content" => $arTemplate["NAME"],
		);
	}
	$arHeader[] = array(
		"id" => "BIZPROC",
		"content" => GetMessage("IBEL_A_BP_H"),
		"default" => false,
	);
	$arHeader[] = array(
		"id" => "BP_PUBLISHED",
		"content" => GetMessage("IBLOCK_FIELD_BP_PUBLISHED"),
		"sort" => "status",
		"default" => true,
	);
}

$lAdmin->AddHeaders($arHeader);

$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();

$arSelectedProps = Array();
foreach($arProps as $i => $arProperty)
{
	$k = array_search("PROPERTY_".$arProperty['ID'], $arSelectedFields);
	if($k!==false)
	{
		$arSelectedProps[] = $arProperty;
		if($arProperty["PROPERTY_TYPE"] == "L")
		{
			$arSelect[$arProperty['ID']] = Array();
			$rs = CIBlockProperty::GetPropertyEnum($arProperty['ID']);
			while($ar = $rs->GetNext())
				$arSelect[$arProperty['ID']][$ar["ID"]] = $ar["VALUE"];
		}
		elseif($arProperty["PROPERTY_TYPE"] == "G")
		{
			$arSelect[$arProperty['ID']] = Array();
			$rs = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$arProperty["LINK_IBLOCK_ID"]));
			while($ar = $rs->GetNext())
				$arSelect[$arProperty['ID']][$ar["ID"]] = str_repeat(" . ", $ar["DEPTH_LEVEL"]).$ar["NAME"];
		}
		unset($arSelectedFields[$k]);
	}
}

$arSelectedFields[] = "ID";
$arSelectedFields[] = "CREATED_BY";
$arSelectedFields[] = "LANG_DIR";
$arSelectedFields[] = "LID";
$arSelectedFields[] = "WF_PARENT_ELEMENT_ID";
$arSelectedFields[] = "ACTIVE";

if(in_array("LOCKED_USER_NAME", $arSelectedFields))
	$arSelectedFields[] = "WF_LOCKED_BY";
if(in_array("USER_NAME", $arSelectedFields))
	$arSelectedFields[] = "MODIFIED_BY";
if(in_array("PREVIEW_TEXT", $arSelectedFields))
	$arSelectedFields[] = "PREVIEW_TEXT_TYPE";
if(in_array("DETAIL_TEXT", $arSelectedFields))
	$arSelectedFields[] = "DETAIL_TEXT_TYPE";
if(in_array("CATALOG_QUANTITY_TRACE", $arSelectedFields))
	$arSelectedFields[] = "CATALOG_QUANTITY_TRACE_ORIG";

$arSelectedFields[] = "LOCK_STATUS";
$arSelectedFields[] = "WF_NEW";
$arSelectedFields[] = "WF_STATUS_ID";
$arSelectedFields[] = "DETAIL_PAGE_URL";
$arSelectedFields[] = "SITE_ID";
$arSelectedFields[] = "CODE";
$arSelectedFields[] = "EXTERNAL_ID";

$arSelectedFieldsMap = array();
foreach($arSelectedFields as $field)
	$arSelectedFieldsMap[$field] = true;

if ($bCatalog)
{
	if (is_array($arCatGroup) && !empty($arCatGroup))
	{
		$boolPriceInc = false;
		foreach($arCatGroup as &$CatalogGroups)
		{
			if (in_array("CATALOG_GROUP_".$CatalogGroups["ID"], $arSelectedFields))
			{
				$arFilter["CATALOG_SHOP_QUANTITY_".$CatalogGroups["ID"]] = 1;
				$boolPriceInc = true;
			}
		}
		unset($CatalogGroups);
		if ($boolPriceInc)
		{
			$bCurrency = CModule::IncludeModule('currency');
			if ($bCurrency)
			{
				$rsCurrencies = CCurrency::GetList(($by1="sort"), ($order1="asc"));
				while ($arCurrency = $rsCurrencies->GetNext(true,true))
				{
					$arCurrencyList[] = $arCurrency;
				}
			}
		}
		unset($boolPriceInc);
	}
}

$wf_status_id = "";
if($bWorkFlow && (strpos($find_el_status_id, "-") !== false))
{
	$ar = explode("-", $find_el_status_id, 2);
	$wf_status_id = $ar[1];
}

if($wf_status_id)
{
	$rsData = CIBlockElement::GetList(
		Array($by=>$order),
		$arFilter,
		false,
		false,
		$arSelectedFields
	);
	while($arElement = $rsData->Fetch())
	{
		if($wf_status_id!==false)
		{
			$LAST_ID = CIBlockElement::WF_GetLast($arElement['ID']);
			if($LAST_ID!=$arElement['ID'])
			{
				$rsData2 = CIBlockElement::GetList(
						Array(),
						Array(
							"ID"=>$LAST_ID,
							"SHOW_HISTORY"=>"Y"
							),
						false,
						Array("nTopCount"=>1),
						array("ID","WF_STATUS_ID")
					);
				if($arRes = $rsData2->Fetch())
				{
					if($arRes["WF_STATUS_ID"]!=$wf_status_id)
						continue;
				}
			}
			else
				continue;
		}
		$arResult[]=$arElement;
	}
	$rsData = new CDBResult();
	$rsData->InitFromArray($arResult);
	$rsData = new CAdminResult($rsData, $sTableID);
}
else
{
	if(isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "excel")
		$arNavParams = false;
	else
		$arNavParams = array("nPageSize"=>CAdminResult::GetNavSize($sTableID));

	$rsData = CIBlockElement::GetList(
		array($by=>$order),
		$arFilter,
		false,
		$arNavParams,
		$arSelectedFields
	);
	$rsData->SetTableID($sTableID);
	$wf_status_id = false;
}

$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(htmlspecialcharsbx($arIBlock["ELEMENTS_NAME"])));
$arRows = array();

$bSearch = CModule::IncludeModule('search');

function GetElementName($ID)
{
	$ID = IntVal($ID);
	static $cache = array();
	if(!array_key_exists($ID, $cache))
	{
		$rsElement = CIBlockElement::GetList(Array(), Array("ID"=>$ID, "SHOW_HISTORY"=>"Y"), false, false, array("ID","IBLOCK_ID","NAME"));
		$cache[$ID] = $rsElement->GetNext();
	}
	return $cache[$ID];
}
function GetIBlockTypeID($IBLOCK_ID)
{
	$IBLOCK_ID = IntVal($IBLOCK_ID);
	if (0 > $IBLOCK_ID)
		$IBLOCK_ID = 0;
	static $cache = array();
	if(!array_key_exists($IBLOCK_ID, $cache))
	{
		$rsIBlock = CIBlock::GetByID($IBLOCK_ID);
		if(!($cache[$IBLOCK_ID] = $rsIBlock->GetNext()))
			$cache[$IBLOCK_ID] = array("IBLOCK_TYPE_ID"=>"");
	}
	return $cache[$IBLOCK_ID]["IBLOCK_TYPE_ID"];
}

while($arRes = $rsData->NavNext(true, "f_"))
{
	$arRes_orig = $arRes;
	// in workflow mode show latest changes
	if($bWorkFlow)
	{
		$LAST_ID = CIBlockElement::WF_GetLast($arRes['ID']);
		if($LAST_ID!=$arRes['ID'])
		{
			$rsData2 = CIBlockElement::GetList(
					array(),
					array(
						"ID"=>$LAST_ID,
						"SHOW_HISTORY"=>"Y"
						),
					false,
					array("nTopCount"=>1),
					$arSelectedFields
				);
			if (isset($arCatGroup))
			{
				$arRes_tmp = array();
				foreach($arRes as $vv => $vval)
				{
					if(substr($vv, 0, 8) == "CATALOG_")
						$arRes_tmp[$vv] = $arRes[$vv];
				}
			}

			$arRes = $rsData2->NavNext(true, "f_");
			if (isset($arCatGroup))
				$arRes = array_merge($arRes, $arRes_tmp);

			$f_ID = $arRes_orig["ID"];
		}
		$lockStatus = $arRes_orig['LOCK_STATUS'];
	}
	elseif($bBizproc)
	{
		$lockStatus = CIBlockDocument::IsDocumentLocked($f_ID, "") ? "red" : "green";
	}
	else
	{
		$lockStatus = "";
	}
	if(in_array("CATALOG_QUANTITY_TRACE", $arSelectedFields))
	{
		$arRes['CATALOG_QUANTITY_TRACE'] = $arRes['CATALOG_QUANTITY_TRACE_ORIG'];
		$f_CATALOG_QUANTITY_TRACE = $f_CATALOG_QUANTITY_TRACE_ORIG;
	}

	$arRes['lockStatus'] = $lockStatus;
	$arRes["orig"] = $arRes_orig;
	$edit_url = CIBlock::GetAdminElementEditLink($IBLOCK_ID, $arRes_orig['ID'], array(
		"find_section_section" => $find_section_section,
		'WF' => 'Y',
	));
	$arRows[$f_ID] = $row = $lAdmin->AddRow($f_ID, $arRes, $edit_url, GetMessage("IBEL_A_EDIT"));
 
	$boolEditPrice = false;
	$boolEditPrice = CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $f_ID, "element_edit_price");

	$row->AddViewField("ID", '<a href="'.$edit_url.'" title="'.GetMessage("IBEL_A_EDIT_TITLE").'">'.$f_ID.'</a>');

	if(isset($f_LOCKED_USER_NAME) && $f_LOCKED_USER_NAME)
		$row->AddViewField("LOCKED_USER_NAME", '<a href="user_edit.php?lang='.LANGUAGE_ID.'&ID='.$f_WF_LOCKED_BY.'" title="'.GetMessage("IBEL_A_USERINFO").'">'.$f_LOCKED_USER_NAME.'</a>');
	if(isset($f_USER_NAME) && $f_USER_NAME)
		$row->AddViewField("USER_NAME", '<a href="user_edit.php?lang='.LANGUAGE_ID.'&ID='.$f_MODIFIED_BY.'" title="'.GetMessage("IBEL_A_USERINFO").'">'.$f_USER_NAME.'</a>');
	if(isset($f_CREATED_USER_NAME) && $f_CREATED_USER_NAME)
		$row->AddViewField("CREATED_USER_NAME", '<a href="user_edit.php?lang='.LANGUAGE_ID.'&ID='.$f_CREATED_BY.'" title="'.GetMessage("IBEL_A_USERINFO").'">'.$f_CREATED_USER_NAME.'</a>');

	if($bWorkFlow || $bBizproc)
	{
		$lamp = '<span class="adm-lamp adm-lamp-in-list adm-lamp-'.$lockStatus.'"></span>';
		if($lockStatus=='red' && $arRes_orig['LOCKED_USER_NAME']!='')
			$row->AddViewField("LOCK_STATUS", $lamp.$arRes_orig['LOCKED_USER_NAME'].$unlock);
		else
			$row->AddViewField("LOCK_STATUS", $lamp);
	}

	if($bBizproc)
		$row->AddCheckField("BP_PUBLISHED", false);

	$row->arRes['props'] = array();
	$arProperties = array();
	if(count($arSelectedProps) > 0)
	{
		$rsProperties = CIBlockElement::GetProperty($IBLOCK_ID, $arRes["ID"]);
		while($ar = $rsProperties->GetNext())
		{
			if(!array_key_exists($ar["ID"], $arProperties))
				$arProperties[$ar["ID"]] = array();
			$arProperties[$ar["ID"]][$ar["PROPERTY_VALUE_ID"]] = $ar;
		}
	}

	foreach($arSelectedProps as $aProp)
	{
		$arViewHTML = array();
		$arEditHTML = array();
		if(strlen($aProp["USER_TYPE"])>0)
			$arUserType = CIBlockProperty::GetUserType($aProp["USER_TYPE"]);
		else
			$arUserType = array();
		$max_file_size_show=100000;

		$last_property_id = false;
		foreach($arProperties[$aProp["ID"]] as $prop_id => $prop)
		{
			$prop['PROPERTY_VALUE_ID'] = intval($prop['PROPERTY_VALUE_ID']);
			$VALUE_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].']['.$prop['PROPERTY_VALUE_ID'].'][VALUE]';
			$DESCR_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].']['.$prop['PROPERTY_VALUE_ID'].'][DESCRIPTION]';
			//View part
			if(array_key_exists("GetAdminListViewHTML", $arUserType))
			{
				$arViewHTML[] = call_user_func_array($arUserType["GetAdminListViewHTML"],
					array(
						$prop,
						array(
							"VALUE" => $prop["~VALUE"],
							"DESCRIPTION" => $prop["~DESCRIPTION"]
						),
						array(
							"VALUE" => $VALUE_NAME,
							"DESCRIPTION" => $DESCR_NAME,
							"MODE"=>"iblock_element_admin",
							"FORM_NAME"=>"form_".$sTableID,
						),
					));
			}
			elseif($prop['PROPERTY_TYPE']=='N')
				$arViewHTML[] = $prop["VALUE"];
			elseif($prop['PROPERTY_TYPE']=='S')
				$arViewHTML[] = $prop["VALUE"];
			elseif($prop['PROPERTY_TYPE']=='L')
				$arViewHTML[] = $prop["VALUE_ENUM"];
			elseif($prop['PROPERTY_TYPE']=='F')
			{
				$arViewHTML[] = CFileInput::Show('NO_FIELDS['.$prop['PROPERTY_VALUE_ID'].']', $prop["VALUE"], array(
					"IMAGE" => "Y",
					"PATH" => "Y",
					"FILE_SIZE" => "Y",
					"DIMENSIONS" => "Y",
					"IMAGE_POPUP" => "Y",
					"MAX_SIZE" => $maxImageSize,
					"MIN_SIZE" => $minImageSize,
					), array(
						'upload' => false,
						'medialib' => false,
						'file_dialog' => false,
						'cloud' => false,
						'del' => false,
						'description' => false,
					)
				);
			}
			elseif($prop['PROPERTY_TYPE']=='G')
			{
				if(intval($prop["VALUE"])>0)
				{
					$rsSection = CIBlockSection::GetList(Array(), Array("ID" => $prop["VALUE"]));
					if($arSection = $rsSection->GetNext())
					{
						$arViewHTML[] = $arSection['NAME'].
						' [<a href="'.
						htmlspecialcharsbx(CIBlock::GetAdminSectionEditLink($arSection['IBLOCK_ID'], $arSection['ID'])).
						'" title="'.GetMessage("IBEL_A_SEC_EDIT").'">'.$arSection['ID'].'</a>]';
					}
				}
			}
			elseif($prop['PROPERTY_TYPE']=='E')
			{
				if($t = GetElementName($prop["VALUE"]))
				{
					$arViewHTML[] = $t['NAME'].
					' [<a href="'.htmlspecialcharsbx(CIBlock::GetAdminElementEditLink($t['IBLOCK_ID'], $t['ID'], array(
						"find_section_section" => $find_section_section,
						'WF' => 'Y',
					))).'" title="'.GetMessage("IBEL_A_EL_EDIT").'">'.$t['ID'].'</a>]';
				}
			}
			//Edit Part
			$bUserMultiple = $prop["MULTIPLE"] == "Y" &&  array_key_exists("GetPropertyFieldHtmlMulty", $arUserType);
			if($bUserMultiple)
			{
				if($last_property_id != $prop["ID"])
				{
					$VALUE_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].']';
					$arEditHTML[] = call_user_func_array($arUserType["GetPropertyFieldHtmlMulty"], array(
						$prop,
						$arProperties[$prop["ID"]],
						array(
							"VALUE" => $VALUE_NAME,
							"MODE"=>"iblock_element_admin",
							"FORM_NAME"=>"form_".$sTableID,
						)
					));
				}
			}
			elseif(array_key_exists("GetPropertyFieldHtml", $arUserType))
			{
				$arEditHTML[] = call_user_func_array($arUserType["GetPropertyFieldHtml"],
					array(
						$prop,
						array(
							"VALUE" => $prop["VALUE"],
							"DESCRIPTION" => $prop["DESCRIPTION"],
						),
						array(
							"VALUE" => $VALUE_NAME,
							"DESCRIPTION" => $DESCR_NAME,
							"MODE"=>"iblock_element_admin",
							"FORM_NAME"=>"form_".$sTableID,
						),
					));
			}
			elseif($prop['PROPERTY_TYPE']=='N' || $prop['PROPERTY_TYPE']=='S')
			{
				if($prop["ROW_COUNT"] > 1)
					$html = '<textarea name="'.$VALUE_NAME.'" cols="'.$prop["COL_COUNT"].'" rows="'.$prop["ROW_COUNT"].'">'.$prop["VALUE"].'</textarea>';
				else
					$html = '<input type="text" name="'.$VALUE_NAME.'" value="'.$prop["VALUE"].'" size="'.$prop["COL_COUNT"].'">';
				if($prop["WITH_DESCRIPTION"] == "Y")
					$html .= ' <span title="'.GetMessage("IBLOCK_ELEMENT_EDIT_PROP_DESC").'">'.GetMessage("IBLOCK_ELEMENT_EDIT_PROP_DESC_1").
						'<input type="text" name="'.$DESCR_NAME.'" value="'.$prop["DESCRIPTION"].'" size="18"></span>';
				$arEditHTML[] = $html;
			}
			elseif($prop['PROPERTY_TYPE']=='L' && ($last_property_id!=$prop["ID"]))
			{
				$VALUE_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].'][]';
				$arValues = array();
				foreach($arProperties[$prop["ID"]] as $g_prop)
				{
					$g_prop = intval($g_prop["VALUE"]);
					if($g_prop > 0)
						$arValues[$g_prop] = $g_prop;
				}
				if($prop['LIST_TYPE']=='C')
				{
					if($prop['MULTIPLE'] == "Y" || count($arSelect[$prop['ID']]) == 1)
					{
						$html = '<input type="hidden" name="'.$VALUE_NAME.'" value="">';
						foreach($arSelect[$prop['ID']] as $value => $display)
						{
							$html .= '<input type="checkbox" name="'.$VALUE_NAME.'" id="id'.$uniq_id.'" value="'.$value.'"';
							if(array_key_exists($value, $arValues))
								$html .= ' checked';
							$html .= '>&nbsp;<label for="id'.$uniq_id.'">'.$display.'</label><br>';
							$uniq_id++;
						}
					}
					else
					{
						$html = '<input type="radio" name="'.$VALUE_NAME.'" id="id'.$uniq_id.'" value=""';
						if(count($arValues) < 1)
							$html .= ' checked';
						$html .= '>&nbsp;<label for="id'.$uniq_id.'">'.GetMessage("IBLOCK_ELEMENT_EDIT_NOT_SET").'</label><br>';
						$uniq_id++;
						foreach($arSelect[$prop['ID']] as $value => $display)
						{
							$html .= '<input type="radio" name="'.$VALUE_NAME.'" id="id'.$uniq_id.'" value="'.$value.'"';
							if(array_key_exists($value, $arValues))
								$html .= ' checked';
							$html .= '>&nbsp;<label for="id'.$uniq_id.'">'.$display.'</label><br>';
							$uniq_id++;
						}
					}
				}
				else
				{
					$html = '<select name="'.$VALUE_NAME.'" size="'.$prop["MULTIPLE_CNT"].'" '.($prop["MULTIPLE"]=="Y"?"multiple":"").'>';
					$html .= '<option value=""'.(count($arValues) < 1? ' selected': '').'>'.GetMessage("IBLOCK_ELEMENT_EDIT_NOT_SET").'</option>';
					foreach($arSelect[$prop['ID']] as $value => $display)
					{
						$html .= '<option value="'.$value.'"';
						if(array_key_exists($value, $arValues))
							$html .= ' selected';
						$html .= '>'.$display.'</option>'."\n";
					}
					$html .= "</select>\n";
				}
				$arEditHTML[] = $html;
			}
			elseif($prop['PROPERTY_TYPE']=='F' && ($last_property_id != $prop["ID"]))
			{
				if($prop['MULTIPLE'] == "Y")
				{
					$inputName = array();
					foreach($arProperties[$prop["ID"]] as $g_prop)
					{
						$inputName['FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].']['.$g_prop['PROPERTY_VALUE_ID'].'][VALUE]'] = $g_prop["VALUE"];
					}

					$arEditHTML[] = CFileInput::ShowMultiple($inputName, 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].'][n#IND#]', array(
						"IMAGE" => "Y",
						"PATH" => "Y",
						"FILE_SIZE" => "Y",
						"DIMENSIONS" => "Y",
						"IMAGE_POPUP" => "Y",
						"MAX_SIZE" => $maxImageSize,
						"MIN_SIZE" => $minImageSize,
						), false, array(
							'upload' => true,
							'medialib' => false,
							'file_dialog' => false,
							'cloud' => false,
							'del' => true,
							'description' => $prop["WITH_DESCRIPTION"]=="Y",
						)
					);
				}
				else
				{
					$arEditHTML[] = CFileInput::Show($VALUE_NAME, $prop["VALUE"], array(
						"IMAGE" => "Y",
						"PATH" => "Y",
						"FILE_SIZE" => "Y",
						"DIMENSIONS" => "Y",
						"IMAGE_POPUP" => "Y",
						"MAX_SIZE" => $maxImageSize,
						"MIN_SIZE" => $minImageSize,
						), array(
							'upload' => true,
							'medialib' => false,
							'file_dialog' => false,
							'cloud' => false,
							'del' => true,
							'description' => $prop["WITH_DESCRIPTION"]=="Y",
						)
					);
				}
			}
			elseif(($prop['PROPERTY_TYPE']=='G') && ($last_property_id!=$prop["ID"]))
			{
				$VALUE_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].'][]';
				$arValues = array();
				foreach($arProperties[$prop["ID"]] as $g_prop)
				{
					$g_prop = intval($g_prop["VALUE"]);
					if($g_prop > 0)
						$arValues[$g_prop] = $g_prop;
				}
				$html = '<select name="'.$VALUE_NAME.'" size="'.$prop["MULTIPLE_CNT"].'" '.($prop["MULTIPLE"]=="Y"?"multiple":"").'>';
				$html .= '<option value=""'.(count($arValues) < 1? ' selected': '').'>'.GetMessage("IBLOCK_ELEMENT_EDIT_NOT_SET").'</option>';
				foreach($arSelect[$prop['ID']] as $value => $display)
				{
					$html .= '<option value="'.$value.'"';
					if(array_key_exists($value, $arValues))
						$html .= ' selected';
					$html .= '>'.$display.'</option>'."\n";
				}
				$html .= "</select>\n";
				$arEditHTML[] = $html;
			}
			elseif($prop['PROPERTY_TYPE']=='E')
			{
				$VALUE_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].']['.$prop['PROPERTY_VALUE_ID'].']';
				if($t = GetElementName($prop["VALUE"]))
				{
					$arEditHTML[] = '<input type="text" name="'.$VALUE_NAME.'" id="'.$VALUE_NAME.'" value="'.$prop["VALUE"].'" size="5">'.
					'<input type="button" value="..." onClick="jsUtils.OpenWindow(\'iblock_element_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$prop["LINK_IBLOCK_ID"].'&amp;n='.urlencode($VALUE_NAME).'\', 600, 500);">'.
					'&nbsp;<span id="sp_'.$VALUE_NAME.'" >'.$t['NAME'].'</span>';
				}
				else
				{
					$arEditHTML[] = '<input type="text" name="'.$VALUE_NAME.'" id="'.$VALUE_NAME.'" value="" size="5">'.
					'<input type="button" value="..." onClick="jsUtils.OpenWindow(\'iblock_element_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$prop["LINK_IBLOCK_ID"].'&amp;n='.urlencode($VALUE_NAME).'\', 600, 500);">'.
					'&nbsp;<span id="sp_'.$VALUE_NAME.'" ></span>';
				}
			}
			$last_property_id = $prop['ID'];
		}
		$table_id = md5($f_ID.':'.$aProp['ID']);
		if($aProp["MULTIPLE"] == "Y")
		{
			$VALUE_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].'][n0][VALUE]';
			$DESCR_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].'][n0][DESCRIPTION]';
			if(array_key_exists("GetPropertyFieldHtmlMulty", $arUserType))
			{
			}
			elseif(array_key_exists("GetPropertyFieldHtml", $arUserType))
			{
				$arEditHTML[] = call_user_func_array($arUserType["GetPropertyFieldHtml"],
					array(
						$prop,
						array(
							"VALUE" => "",
							"DESCRIPTION" => "",
						),
						array(
							"VALUE" => $VALUE_NAME,
							"DESCRIPTION" => $DESCR_NAME,
							"MODE"=>"iblock_element_admin",
							"FORM_NAME"=>"form_".$sTableID,
						),
					));
			}
			elseif($prop['PROPERTY_TYPE']=='N' || $prop['PROPERTY_TYPE']=='S')
			{
				if($prop["ROW_COUNT"] > 1)
					$html = '<textarea name="'.$VALUE_NAME.'" cols="'.$prop["COL_COUNT"].'" rows="'.$prop["ROW_COUNT"].'"></textarea>';
				else
					$html = '<input type="text" name="'.$VALUE_NAME.'" value="" size="'.$prop["COL_COUNT"].'">';
				if($prop["WITH_DESCRIPTION"] == "Y")
					$html .= ' <span title="'.GetMessage("IBLOCK_ELEMENT_EDIT_PROP_DESC").'">'.GetMessage("IBLOCK_ELEMENT_EDIT_PROP_DESC_1").'<input type="text" name="'.$DESCR_NAME.'" value="" size="18"></span>';
				$arEditHTML[] = $html;
			}
			elseif($prop['PROPERTY_TYPE']=='F')
			{
			}
			elseif($prop['PROPERTY_TYPE']=='E')
			{
				$VALUE_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].'][n0]';
				$arEditHTML[] = '<input type="text" name="'.$VALUE_NAME.'" id="'.$VALUE_NAME.'" value="" size="5">'.
					'<input type="button" value="..." onClick="jsUtils.OpenWindow(\'iblock_element_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$prop["LINK_IBLOCK_ID"].'&amp;n='.urlencode($VALUE_NAME).'\', 600, 500);">'.
					'&nbsp;<span id="sp_'.$VALUE_NAME.'" ></span>';
			}

			if(
				$prop["PROPERTY_TYPE"] !== "G"
				&& $prop["PROPERTY_TYPE"] !== "L"
				&& $prop["PROPERTY_TYPE"] !== "F"
				&& !$bUserMultiple
			)
				$arEditHTML[] = '<input type="button" value="'.GetMessage("IBLOCK_ELEMENT_EDIT_PROP_ADD").'" onClick="addNewRow(\'tb'.$table_id.'\')">';
		}
		if(count($arViewHTML) > 0)
		{
			if($prop["PROPERTY_TYPE"] == "F")
				$row->AddViewField("PROPERTY_".$aProp['ID'], implode("", $arViewHTML));
			else
				$row->AddViewField("PROPERTY_".$aProp['ID'], implode(" / ", $arViewHTML));
		}

		if(count($arEditHTML) > 0)
			$row->arRes['props']["PROPERTY_".$aProp['ID']] = array("table_id"=>$table_id, "html"=>$arEditHTML);
	}

	if ($bCatalog)
	{
		if (isset($arCatGroup) && !empty($arCatGroup))
		{
			$row->arRes['price'] = array();
			foreach($arCatGroup as &$CatGroup)
			{
				if (array_key_exists("CATALOG_GROUP_".$CatGroup["ID"], $arSelectedFieldsMap))
				{
					$price = "";
					$sHTML = "";
					$selectCur = "";
					if ($bCurrency)
					{
						$price = CurrencyFormat($arRes["CATALOG_PRICE_".$CatGroup["ID"]],$arRes["CATALOG_CURRENCY_".$CatGroup["ID"]]);
						if ($USER->CanDoOperation('catalog_price') && $boolEditPrice)
						{
							$selectCur = '<select name="CATALOG_CURRENCY['.$f_ID.']['.$CatGroup["ID"].']" id="CATALOG_CURRENCY['.$f_ID.']['.$CatGroup["ID"].']"';
							if (intval($arRes["CATALOG_EXTRA_ID_".$CatGroup["ID"]])>0)
								$selectCur .= ' disabled="disabled" readonly="readonly"';
							if ($CatGroup["BASE"]=="Y")
								$selectCur .= ' onchange="top.ChangeBaseCurrency('.$f_ID.')"';
							$selectCur .= '>';
							foreach ($arCurrencyList as &$arOneCurrency)
							{
								$selectCur .= '<option value="'.$arOneCurrency["CURRENCY"].'"';
								if ($arOneCurrency["~CURRENCY"] == $arRes["CATALOG_CURRENCY_".$CatGroup["ID"]])
									$selectCur .= ' selected';
								$selectCur .= '>'.$arOneCurrency["CURRENCY"].'</option>';
							}
							unset($arOneCurrency);
							$selectCur .= '</select>';
						}
					}
					else
					{
						$price = $arRes["CATALOG_PRICE_".$CatGroup["ID"]]." ".$arRes["CATALOG_CURRENCY_".$CatGroup["ID"]];
					}

					$row->AddViewField("CATALOG_GROUP_".$CatGroup["ID"], $price);

					if ($USER->CanDoOperation('catalog_price') && $boolEditPrice)
					{
						$sHTML = '<input type="text" size="5" id="CATALOG_PRICE['.$f_ID.']['.$CatGroup["ID"].']" name="CATALOG_PRICE['.$f_ID.']['.$CatGroup["ID"].']" value="'.$arRes["CATALOG_PRICE_".$CatGroup["ID"]].'"';
						if ($CatGroup["BASE"]=="Y")
							$sHTML .= ' onchange="top.ChangeBasePrice('.$f_ID.')"';
						if (intval($arRes["CATALOG_EXTRA_ID_".$CatGroup["ID"]])>0)
							$sHTML .= ' disabled readonly';
						$sHTML .= '> '.$selectCur;
						if(intval($arRes["CATALOG_EXTRA_ID_".$CatGroup["ID"]])>0)
							$sHTML .= '<input type="hidden" id="CATALOG_EXTRA['.$f_ID.']['.$CatGroup["ID"].']" name="CATALOG_EXTRA['.$f_ID.']['.$CatGroup["ID"].']" value="'.$arRes["CATALOG_EXTRA_ID_".$CatGroup["ID"]].'">';

						$sHTML .= '<input type="hidden" name="CATALOG_old_PRICE['.$f_ID.']['.$CatGroup["ID"].']" value="'.$arRes["CATALOG_PRICE_".$CatGroup["ID"]].'">';
						$sHTML .= '<input type="hidden" name="CATALOG_old_CURRENCY['.$f_ID.']['.$CatGroup["ID"].']" value="'.$arRes["CATALOG_CURRENCY_".$CatGroup["ID"]].'">';
						$sHTML .= '<input type="hidden" name="CATALOG_PRICE_ID['.$f_ID.']['.$CatGroup["ID"].']" value="'.$arRes["CATALOG_PRICE_ID_".$CatGroup["ID"]].'">';
						$sHTML .= '<input type="hidden" name="CATALOG_QUANTITY_FROM['.$f_ID.']['.$CatGroup["ID"].']" value="'.$arRes["CATALOG_QUANTITY_FROM_".$CatGroup["ID"]].'">';
						$sHTML .= '<input type="hidden" name="CATALOG_QUANTITY_TO['.$f_ID.']['.$CatGroup["ID"].']" value="'.$arRes["CATALOG_QUANTITY_TO_".$CatGroup["ID"]].'">';

						$row->arRes['price']["CATALOG_GROUP_".$CatGroup["ID"]] = $sHTML;
					}
				}
			}
			unset($CatGroup);
		}
	}

	if ($bBizproc)
	{
		$arDocumentStates = CBPDocument::GetDocumentStates(
			array("iblock", "CIBlockDocument", "iblock_".$IBLOCK_ID),
			array("iblock", "CIBlockDocument", $f_ID)
		);

		$arRes["CURRENT_USER_GROUPS"] = $USER->GetUserGroupArray();
		if ($arRes["CREATED_BY"] == $USER->GetID())
			$arRes["CURRENT_USER_GROUPS"][] = "Author";
		$row->arRes["CURRENT_USER_GROUPS"] = $arRes["CURRENT_USER_GROUPS"];

		$arStr = array();
		$arStr1 = array();
		foreach ($arDocumentStates as $kk => $vv)
		{
			$canViewWorkflow = CIBlockDocument::CanUserOperateDocument(
				CBPCanUserOperateOperation::ViewWorkflow,
				$USER->GetID(),
				$f_ID,
				array("AllUserGroups" => $arRes["CURRENT_USER_GROUPS"], "DocumentStates" => $arDocumentStates, "WorkflowId" => $kk)
			);
			if (!$canViewWorkflow)
				continue;

			$arStr1[$vv["TEMPLATE_ID"]] = $vv["TEMPLATE_NAME"];
			$arStr[$vv["TEMPLATE_ID"]] .= "<a href=\"/bitrix/admin/bizproc_log.php?ID=".$kk."\">".(strlen($vv["STATE_TITLE"]) > 0 ? $vv["STATE_TITLE"] : $vv["STATE_NAME"])."</a><br />";

			if (strlen($vv["ID"]) > 0)
			{
				$arTasks = CBPDocument::GetUserTasksForWorkflow($USER->GetID(), $vv["ID"]);
				foreach ($arTasks as $arTask)
				{
					$arStr[$vv["TEMPLATE_ID"]] .= GetMessage("IBEL_A_BP_TASK").":<br /><a href=\"bizproc_task.php?id=".$arTask["ID"]."\" title=\"".$arTask["DESCRIPTION"]."\">".$arTask["NAME"]."</a><br /><br />";
				}
			}
		}

		$str = "";
		foreach ($arStr as $k => $v)
		{
			$row->AddViewField("WF_".$k, $v);
			$str .= "<b>".(strlen($arStr1[$k]) > 0 ? $arStr1[$k] : GetMessage("IBEL_A_BP_PROC"))."</b>:<br />".$v."<br />";
		}

		$row->AddViewField("BIZPROC", $str);
	}
}

$boolIBlockElementAdd = CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $find_section_section, "section_element_bind");

$arElementOps = CIBlockElementRights::UserHasRightTo(
	$IBLOCK_ID,
	array_keys($arRows),
	"",
	CIBlockRights::RETURN_OPERATIONS
);
$availQuantityTrace = COption::GetOptionString("catalog", "default_quantity_trace", 'N');
$arQuantityTrace = array(
	"D" => GetMessage("IBEL_DEFAULT_VALUE")." (".($availQuantityTrace=='Y' ? GetMessage("IBEL_YES_VALUE") : GetMessage("IBEL_NO_VALUE")).")",
	"Y" => GetMessage("IBEL_YES_VALUE"),
	"N" => GetMessage("IBEL_NO_VALUE"),
);
foreach($arRows as $f_ID => $row)
{
	$edit_url = CIBlock::GetAdminElementEditLink($IBLOCK_ID, $row->arRes['orig']['ID'], array(
		"find_section_section" => $find_section_section,
		'WF' => 'Y',
	));

	if(array_key_exists("PREVIEW_TEXT", $arSelectedFieldsMap))
		$row->AddViewField("PREVIEW_TEXT", ($row->arRes["PREVIEW_TEXT_TYPE"]=="text" ? htmlspecialcharsex($row->arRes["PREVIEW_TEXT"]) : HTMLToTxt($row->arRes["PREVIEW_TEXT"])));
	if(array_key_exists("DETAIL_TEXT", $arSelectedFieldsMap))
		$row->AddViewField("DETAIL_TEXT", ($row->arRes["DETAIL_TEXT_TYPE"]=="text" ? htmlspecialcharsex($row->arRes["DETAIL_TEXT"]) : HTMLToTxt($row->arRes["DETAIL_TEXT"])));

	if(isset($arElementOps[$f_ID]) && isset($arElementOps[$f_ID]["element_edit"]))
	{
		if(isset($arElementOps[$f_ID]) && isset($arElementOps[$f_ID]["element_edit_price"]))
		{
			if(isset($row->arRes['price']) && is_array($row->arRes['price']))
				foreach($row->arRes['price'] as $price_id => $sHTML)
					$row->AddEditField($price_id, $sHTML);
		}

		$row->AddCheckField("ACTIVE");
		$row->AddInputField("NAME", array('size'=>'35'));
		$row->AddViewField("NAME", '<a href="'.$edit_url.'" title="'.GetMessage("IBEL_A_EDIT_TITLE").'">'.htmlspecialcharsex($row->arRes["NAME"]).'</a>');
		$row->AddInputField("SORT", array('size'=>'3'));
		$row->AddInputField("CODE");
		$row->AddInputField("EXTERNAL_ID");
		if ($bSearch)
		{
			$row->AddViewField("TAGS", htmlspecialcharsex($row->arRes["TAGS"]));
			$row->AddEditField("TAGS", InputTags("FIELDS[".$f_ID."][TAGS]", $row->arRes["TAGS"], $arIBlock["SITE_ID"]));
		}
		else
		{
			$row->AddInputField("TAGS");
		}
		$row->AddCalendarField("DATE_ACTIVE_FROM");
		$row->AddCalendarField("DATE_ACTIVE_TO");
		if($arWFStatus)
		{
			$row->AddSelectField("WF_STATUS_ID", $arWFStatus);
			if($row->arRes['orig']['WF_NEW']=='Y' || $row->arRes['WF_STATUS_ID']=='1')
				$row->AddViewField("WF_STATUS_ID", htmlspecialcharsex($arWFStatus[$row->arRes['WF_STATUS_ID']]));
			else
				$row->AddViewField("WF_STATUS_ID", '<a href="'.$edit_url.'" title="'.GetMessage("IBEL_A_ED_TITLE").'">'.htmlspecialcharsex($arWFStatus[$row->arRes['WF_STATUS_ID']]).'</a> / <a href="'.htmlspecialcharsbx(CIBlock::GetAdminElementEditLink($IBLOCK_ID, $row->arRes['orig']['ID'], array(
					"find_section_section" => $find_section_section,
					'view' => (!isset($arElementOps[$f_ID]) || !isset($arElementOps[$f_ID]["element_edit_any_wf_status"])? 'Y': null)
				))).'" title="'.GetMessage("IBEL_A_ED2_TITLE").'">'.htmlspecialcharsex($arWFStatus[$row->arRes['orig']['WF_STATUS_ID']]).'</a>');
		}
		if (array_key_exists("PREVIEW_PICTURE", $arSelectedFieldsMap))
		{
			$row->AddFileField("PREVIEW_PICTURE", array(
				"IMAGE" => "Y",
				"PATH" => "Y",
				"FILE_SIZE" => "Y",
				"DIMENSIONS" => "Y",
				"IMAGE_POPUP" => "Y",
				"MAX_SIZE" => $maxImageSize,
				"MIN_SIZE" => $minImageSize,
				), array(
					'upload' => true,
					'medialib' => false,
					'file_dialog' => false,
					'cloud' => true,
					'del' => true,
					'description' => false,
				)
			);
		}
		if (array_key_exists("DETAIL_PICTURE", $arSelectedFieldsMap))
		{
			$row->AddFileField("DETAIL_PICTURE", array(
				"IMAGE" => "Y",
				"PATH" => "Y",
				"FILE_SIZE" => "Y",
				"DIMENSIONS" => "Y",
				"IMAGE_POPUP" => "Y",
				"MAX_SIZE" => $maxImageSize,
				"MIN_SIZE" => $minImageSize,
				), array(
					'upload' => true,
					'medialib' => false,
					'file_dialog' => false,
					'cloud' => true,
					'del' => true,
					'description' => false,
				)
			);
		}
		if(array_key_exists("PREVIEW_TEXT", $arSelectedFieldsMap))
		{
			$sHTML = '<input type="radio" name="FIELDS['.$f_ID.'][PREVIEW_TEXT_TYPE]" value="text" id="'.$f_ID.'PREVIEWtext"';
			if($row->arRes["PREVIEW_TEXT_TYPE"]!="html")
				$sHTML .= ' checked';
			$sHTML .= '><label for="'.$f_ID.'PREVIEWtext">text</label> /';
			$sHTML .= '<input type="radio" name="FIELDS['.$f_ID.'][PREVIEW_TEXT_TYPE]" value="html" id="'.$f_ID.'PREVIEWhtml"';
			if($row->arRes["PREVIEW_TEXT_TYPE"]=="html")
				$sHTML .= ' checked';
			$sHTML .= '><label for="'.$f_ID.'PREVIEWhtml">html</label><br>';
			$sHTML .= '<textarea rows="10" cols="50" name="FIELDS['.$f_ID.'][PREVIEW_TEXT]">'.htmlspecialcharsbx($row->arRes["PREVIEW_TEXT"]).'</textarea>';
			$row->AddEditField("PREVIEW_TEXT", $sHTML);
		}
		if(array_key_exists("DETAIL_TEXT", $arSelectedFieldsMap))
		{
			$sHTML = '<input type="radio" name="FIELDS['.$f_ID.'][DETAIL_TEXT_TYPE]" value="text" id="'.$f_ID.'DETAILtext"';
			if($row->arRes["DETAIL_TEXT_TYPE"]!="html")
				$sHTML .= ' checked';
			$sHTML .= '><label for="'.$f_ID.'DETAILtext">text</label> /';
			$sHTML .= '<input type="radio" name="FIELDS['.$f_ID.'][DETAIL_TEXT_TYPE]" value="html" id="'.$f_ID.'DETAILhtml"';
			if($row->arRes["DETAIL_TEXT_TYPE"]=="html")
				$sHTML .= ' checked';
			$sHTML .= '><label for="'.$f_ID.'DETAILhtml">html</label><br>';

			$sHTML .= '<textarea rows="10" cols="50" name="FIELDS['.$f_ID.'][DETAIL_TEXT]">'.htmlspecialcharsbx($row->arRes["DETAIL_TEXT"]).'</textarea>';
			$row->AddEditField("DETAIL_TEXT", $sHTML);
		}
		foreach($row->arRes['props'] as $prop_id => $arEditHTML)
			$row->AddEditField($prop_id, '<table id="tb'.$arEditHTML['table_id'].'" border=0 cellpadding=0 cellspacing=0><tr><td nowrap>'.implode("</td></tr><tr><td nowrap>", $arEditHTML['html']).'</td></tr></table>');

		if (isset($arElementOps[$f_ID]["element_edit_price"]) && $USER->CanDoOperation('catalog_price'))
		{
			$row->AddInputField("CATALOG_QUANTITY");
			$row->AddSelectField("CATALOG_QUANTITY_TRACE", $arQuantityTrace);
			$row->AddInputField("CATALOG_WEIGHT");
		}
		elseif ($USER->CanDoOperation('catalog_read'))
		{
			$row->AddInputField("CATALOG_QUANTITY", false);
			$row->AddSelectField("CATALOG_QUANTITY_TRACE", $arQuantityTrace, false);
			$row->AddInputField("CATALOG_WEIGHT", false);
		}
	}
	else
	{
		$row->AddCheckField("ACTIVE", false);
		$row->AddViewField("NAME", '<a href="'.$edit_url.'" title="'.GetMessage("IBEL_A_EDIT_TITLE").'">'.htmlspecialcharsex($row->arRes["NAME"]).'</a>');
		$row->AddInputField("SORT", false);
		$row->AddInputField("CODE", false);
		$row->AddInputField("EXTERNAL_ID", false);
		$row->AddViewField("TAGS", htmlspecialcharsex($row->arRes["TAGS"]));
		$row->AddCalendarField("DATE_ACTIVE_FROM", false);
		$row->AddCalendarField("DATE_ACTIVE_TO", false);
		if ($arWFStatus)
		{
			$row->AddViewField("WF_STATUS_ID", htmlspecialcharsex($arWFStatus[$row->arRes['WF_STATUS_ID']]));
		}
		if ($bCatalog)
		{
			$row->AddInputField("CATALOG_QUANTITY", false);
			$row->AddSelectField("CATALOG_QUANTITY_TRACE", $arQuantityTrace, false);
			$row->AddInputField("CATALOG_WEIGHT", false);
		}
		if (array_key_exists("PREVIEW_PICTURE", $arSelectedFieldsMap))
		{
			$row->AddViewFileField("PREVIEW_PICTURE", array(
				"IMAGE" => "Y",
				"PATH" => "Y",
				"FILE_SIZE" => "Y",
				"DIMENSIONS" => "Y",
				"IMAGE_POPUP" => "Y",
				"MAX_SIZE" => $maxImageSize,
				"MIN_SIZE" => $minImageSize,
				)
			);
		}
		if (array_key_exists("DETAIL_PICTURE", $arSelectedFieldsMap))
		{
			$row->AddViewFileField("DETAIL_PICTURE", array(
				"IMAGE" => "Y",
				"PATH" => "Y",
				"FILE_SIZE" => "Y",
				"DIMENSIONS" => "Y",
				"IMAGE_POPUP" => "Y",
				"MAX_SIZE" => $maxImageSize,
				"MIN_SIZE" => $minImageSize,
				)
			);
		}
	}

	$arActions = array();

	if($row->arRes["ACTIVE"] == "Y")
	{
		$arActive = array(
			"TEXT" => GetMessage("IBEL_A_DEACTIVATE"),
			"ACTION" => $lAdmin->ActionDoGroup($row->arRes['orig']['ID'], "deactivate", $sThisSectionUrl),
			"ONCLICK" => "",
		);
	}
	else
	{
		$arActive = array(
			"TEXT" => GetMessage("IBEL_A_ACTIVATE"),
			"ACTION" => $lAdmin->ActionDoGroup($row->arRes['orig']['ID'], "activate", $sThisSectionUrl),
			"ONCLICK" => "",
		);
	}

	if($bWorkFlow)
	{
		if(isset($arElementOps[$f_ID]) && isset($arElementOps[$f_ID]["element_edit_any_wf_status"]))
			$STATUS_PERMISSION = 2;
		else
			$STATUS_PERMISSION = CIBlockElement::WF_GetStatusPermission($row->arRes["WF_STATUS_ID"]);

		$intMinPerm = 2;

		$arUnLock = array(
			"ICON" => "unlock",
			"TEXT" => GetMessage("IBEL_A_UNLOCK"),
			"TITLE" => GetMessage("IBLOCK_UNLOCK_ALT"),
			"ACTION" => "if(confirm('".GetMessageJS("IBLOCK_UNLOCK_CONFIRM")."')) ".$lAdmin->ActionDoGroup($row->arRes['orig']['ID'], "unlock", $sThisSectionUrl),
			"ONCLICK" => "",
		);

		if($row->arRes['orig']['LOCK_STATUS'] == "red")
		{
			if (CWorkflow::IsAdmin())
				$arActions[] = $arUnLock;
		}
		else
		{
			/*
			 * yellow unlock
			 * edit
			 * copy
			 * history
			 * view (?)
			 * edit_orig (?)
			 * delete
			 */
			if (
				isset($arElementOps[$f_ID])
				&& isset($arElementOps[$f_ID]["element_edit"])
				&& (2 <= $STATUS_PERMISSION)
			)
			{
				if ($row->arRes['orig']['LOCK_STATUS'] == "yellow")
				{
					$arActions[] = $arUnLock;
					$arActions[] = array("SEPARATOR"=>true);
				}

				$arActions[] = array(
					"ICON" => "edit",
					"TEXT" => GetMessage("IBEL_A_CHANGE"),
					"DEFAULT" => true,
					"ACTION" => $lAdmin->ActionRedirect(CIBlock::GetAdminElementEditLink($IBLOCK_ID, $row->arRes['orig']['ID'], array(
						"find_section_section" => $find_section_section,
						'WF' => 'Y',
					))),
					"ONCLICK" => "",
				);
				$arActions[] = $arActive;
			}

			if (
				$boolIBlockElementAdd
				&& (2 <= $STATUS_PERMISSION)
			)
			{
				$arActions[] = array(
					"ICON" => "copy",
					"TEXT" => GetMessage("IBEL_A_COPY_ELEMENT"),
					"ACTION" => $lAdmin->ActionRedirect(CIBlock::GetAdminElementEditLink($IBLOCK_ID, $row->arRes['orig']['ID'], array(
						"find_section_section" => $find_section_section,
						'WF' => 'Y',
						'action' => 'copy',
					))),
					"ONCLICK" => "",
				);
			}

			if(!defined("CATALOG_PRODUCT"))
			{
				$arActions[] = array(
					"ICON" => "history",
					"TEXT" => GetMessage("IBEL_A_HIST"),
					"TITLE" => GetMessage("IBLOCK_HISTORY_ALT"),
					"ACTION" => $lAdmin->ActionRedirect('iblock_history_list.php?ELEMENT_ID='.$row->arRes['orig']['ID'].$sThisSectionUrl),
					"ONCLICK" => "",
				);
			}

			if (strlen($row->arRes['DETAIL_PAGE_URL']) > 0)
			{
				$tmpVar = CIBlock::ReplaceDetailUrl($row->arRes['orig']["DETAIL_PAGE_URL"], $row->arRes['orig'], true, "E");

				if (
					$row->arRes['orig']['WF_NEW'] == "Y"
					&& isset($arElementOps[$f_ID])
					&& isset($arElementOps[$f_ID]["element_edit"])
					&& 2 <= $STATUS_PERMISSION
				) // not published, under workflow
				{
					$arActions[] = array("SEPARATOR"=>true);
					$arActions[] = array(
						"ICON" => "view",
						"TEXT" => GetMessage("IBLOCK_EL_ADMIN_VIEW_WF"),
						"TITLE" => GetMessage("IBEL_A_ORIG"),
						"ACTION" => $lAdmin->ActionRedirect(htmlspecialcharsbx($tmpVar).((strpos($tmpVar, "?") !== false) ? "&" : "?")."show_workflow=Y"),
						"ONCLICK" => "",
					);
				}
				elseif ($row->arRes["WF_STATUS_ID"] > 1)
				{
					if (isset($arElementOps[$f_ID])
						&& isset($arElementOps[$f_ID]["element_edit"])
						&& isset($arElementOps[$f_ID]["element_edit_any_wf_status"]))
					{
						$arActions[] = array("SEPARATOR"=>true);
						$arActions[] = array(
							"ICON" => "view",
							"TEXT" => GetMessage("IBLOCK_EL_ADMIN_VIEW"),
							"TITLE" => GetMessage("IBEL_A_ORIG"),
							"ACTION" => $lAdmin->ActionRedirect(htmlspecialcharsbx($tmpVar)),
							"ONCLICK" => "",
						);

						$arActions[] = array(
							"ICON" => "view",
							"TEXT" => GetMessage("IBLOCK_EL_ADMIN_VIEW_WF"),
							"TITLE" => GetMessage("IBEL_A_ORIG"),
							"ACTION" => $lAdmin->ActionRedirect(htmlspecialcharsbx($tmpVar).((strpos($tmpVar, "?") !== false) ? "&" : "?")."show_workflow=Y"),
							"ONCLICK" => "",
						);
					}
				}
				else
				{
					if (isset($arElementOps[$f_ID])
						&& isset($arElementOps[$f_ID]["element_edit"])
						&& 2 <= $STATUS_PERMISSION
					)
					{
						$arActions[] = array("SEPARATOR"=>true);
						$arActions[] = array(
							"ICON" => "view",
							"TEXT" => GetMessage("IBLOCK_EL_ADMIN_VIEW"),
							"TITLE" => GetMessage("IBEL_A_ORIG"),
							"ACTION" => $lAdmin->ActionRedirect(htmlspecialcharsbx($tmpVar)),
							"ONCLICK" => "",
						);
					}
				}
			}

			if ($row->arRes["WF_STATUS_ID"] > 1
				&& isset($arElementOps[$f_ID])
				&& isset($arElementOps[$f_ID]["element_edit"])
				&& isset($arElementOps[$f_ID]["element_edit_any_wf_status"])
			)
			{
				$arActions[] = array(
					"ICON" => "edit_orig",
					"TEXT" => GetMessage("IBEL_A_ORIG_ED"),
					"TITLE" => GetMessage("IBEL_A_ORIG_ED_TITLE"),
					"ACTION" => $lAdmin->ActionRedirect(CIBlock::GetAdminElementEditLink($IBLOCK_ID, $row->arRes['orig']['ID'], array(
						"find_section_section" => $find_section_section,
					))),
					"ONCLICK" => "",
				);
			}

			if (
				isset($arElementOps[$f_ID])
				&& isset($arElementOps[$f_ID]["element_delete"])
				&& (2 <= $STATUS_PERMISSION)
			)
			{
				if (!isset($arElementOps[$f_ID]["element_edit_any_wf_status"]))
					$intMinPerm = CIBlockElement::WF_GetStatusPermission($row->arRes["WF_STATUS_ID"], $f_ID);
				if (2 <= $intMinPerm)
				{
					if (!empty($arActions))
						$arActions[] = array("SEPARATOR"=>true);
					$arActions[] = array(
						"ICON" => "delete",
						"TEXT" => GetMessage('MAIN_DELETE'),
						"TITLE" => GetMessage("IBLOCK_DELETE_ALT"),
						"ACTION" => "if(confirm('".GetMessageJS('IBLOCK_CONFIRM_DEL_MESSAGE')."')) ".$lAdmin->ActionDoGroup($row->arRes['orig']['ID'], "delete", $sThisSectionUrl),
						"ONCLICK" => "",
					);
				}
			}
		}
	}
	elseif($bBizproc)
	{
		$bWritePermission = CIBlockDocument::CanUserOperateDocument(
			CBPCanUserOperateOperation::WriteDocument,
			$USER->GetID(),
			$f_ID,
			array(
				"IBlockId" => $IBLOCK_ID,
				"AllUserGroups" => $row->arRes["CURRENT_USER_GROUPS"],
				"DocumentStates" => $arDocumentStates,
			)
		);

		$bStartWorkflowPermission = CIBlockDocument::CanUserOperateDocument(
			CBPCanUserOperateOperation::StartWorkflow,
			$USER->GetID(),
			$f_ID,
			array(
				"IBlockId" => $IBLOCK_ID,
				"AllUserGroups" => $row->arRes["CURRENT_USER_GROUPS"],
				"DocumentStates" => $arDocumentStates,
			)
		);

		if(
			$bStartWorkflowPermission
			|| (
				isset($arElementOps[$f_ID])
				&& isset($arElementOps[$f_ID]["element_bizproc_start"])
			)
		)
		{
			$arActions[] = array(
				"ICON" => "",
				"TEXT" => GetMessage("IBEL_A_BP_RUN"),
				"ACTION" => $lAdmin->ActionRedirect('iblock_start_bizproc.php?document_id='.$f_ID.'&document_type=iblock_'.$IBLOCK_ID.'&back_url='.urlencode($APPLICATION->GetCurPageParam("", array("mode", "table_id"))).''),
				"ONCLICK" => "",
			);
		}

		if ($row->arRes['lockStatus'] == "red")
		{
			if (CBPDocument::IsAdmin())
			{
				$arActions[] = Array(
					"ICON" => "unlock",
					"TEXT" => GetMessage("IBEL_A_UNLOCK"),
					"TITLE" => GetMessage("IBEL_A_UNLOCK_ALT"),
					"ACTION" => "if(confirm('".GetMessageJS("IBEL_A_UNLOCK_CONFIRM")."')) ".$lAdmin->ActionDoGroup($f_ID, "unlock", $sThisSectionUrl),
					"ONCLICK" => "",
				);
			}
		}
		elseif ($bWritePermission)
		{
			$arActions[] = array(
				"ICON" => "edit",
				"TEXT" => GetMessage("IBEL_A_CHANGE"),
				"DEFAULT" => true,
				"ACTION" => $lAdmin->ActionRedirect(CIBlock::GetAdminElementEditLink($IBLOCK_ID, $f_ID, array(
					"find_section_section" => $find_section_section,
					'WF'=>'Y',
				))),
				"ONCLICK" => "",
			);
			$arActions[] = $arActive;

			$arActions[] = array(
				"ICON" => "copy",
				"TEXT" => GetMessage("IBEL_A_COPY_ELEMENT"),
				"ACTION" => $lAdmin->ActionRedirect(CIBlock::GetAdminElementEditLink($IBLOCK_ID, $f_ID, array(
					"find_section_section" => $find_section_section,
					'WF'=>'Y',
					'action'=>'copy',
				))),
				"ONCLICK" => "",
			);

			if(!defined("CATALOG_PRODUCT"))
			{
				$arActions[] = array(
					"ICON" => "history",
					"TEXT" => GetMessage("IBEL_A_HIST"),
					"TITLE" => GetMessage("IBEL_A_HISTORY_ALT"),
					"ACTION" => $lAdmin->ActionRedirect('iblock_bizproc_history.php?document_id='.$f_ID.'&back_url='.urlencode($APPLICATION->GetCurPageParam("", array())).''),
					"ONCLICK" => "",
				);
			}

			$arActions[] = array("SEPARATOR"=>true);
			$arActions[] = array(
				"ICON" => "delete",
				"TEXT" => GetMessage('MAIN_DELETE'),
				"TITLE" => GetMessage("IBLOCK_DELETE_ALT"),
				"ACTION" => "if(confirm('".GetMessageJS('IBLOCK_CONFIRM_DEL_MESSAGE')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete", $sThisSectionUrl),
				"ONCLICK" => "",
			);
		}
	}
	else
	{
		if(
			isset($arElementOps[$f_ID])
			&& isset($arElementOps[$f_ID]["element_edit"])
		)
		{
			$arActions[] = array(
				"ICON" => "edit",
				"TEXT" => GetMessage("IBEL_A_CHANGE"),
				"DEFAULT" => true,
				"ACTION" => $lAdmin->ActionRedirect(CIBlock::GetAdminElementEditLink($IBLOCK_ID, $row->arRes['orig']['ID'], array(
					"find_section_section" => $find_section_section,
				))),
				"ONCLICK" => "",
			);
			$arActions[] = $arActive;
		}

		if ($boolIBlockElementAdd && isset($arElementOps[$f_ID])
			&& isset($arElementOps[$f_ID]["element_edit"]))
		{
			$arActions[] = array(
				"ICON" => "copy",
				"TEXT" => GetMessage("IBEL_A_COPY_ELEMENT"),
				"ACTION" => $lAdmin->ActionRedirect(CIBlock::GetAdminElementEditLink($IBLOCK_ID, $row->arRes['orig']['ID'], array(
					"find_section_section" => $find_section_section,
					'action'=>'copy',
				))),
				"ONCLICK" => "",
			);
		}

		if(strlen($row->arRes['DETAIL_PAGE_URL']) > 0)
		{
			$tmpVar = CIBlock::ReplaceDetailUrl($row->arRes['orig']["DETAIL_PAGE_URL"], $row->arRes['orig'], true, "E");
			$arActions[] = array(
				"ICON" => "view",
				"TEXT" => GetMessage("IBLOCK_EL_ADMIN_VIEW"),
				"TITLE" => GetMessage("IBEL_A_ORIG"),
				"ACTION" => $lAdmin->ActionRedirect(htmlspecialcharsbx($tmpVar)),
				"ONCLICK" => "",
			);
		}

		if(
			isset($arElementOps[$f_ID])
			&& isset($arElementOps[$f_ID]["element_delete"])
		)
		{
			if (!empty($arActions))
				$arActions[] = array("SEPARATOR"=>true);
			$arActions[] = array(
				"ICON" => "delete",
				"TEXT" => GetMessage('MAIN_DELETE'),
				"TITLE" => GetMessage("IBLOCK_DELETE_ALT"),
				"ACTION" => "if(confirm('".GetMessageJS('IBLOCK_CONFIRM_DEL_MESSAGE')."')) ".$lAdmin->ActionDoGroup($row->arRes['orig']['ID'], "delete", $sThisSectionUrl),
				"ONCLICK" => "",
			);
		}
	}

	if(!empty($arActions))
		$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

$arGroupActions = array();
foreach($arElementOps as $id => $arOps)
{
	if(isset($arOps["element_delete"]))
	{
		$arGroupActions["delete"] = GetMessage("MAIN_ADMIN_LIST_DELETE");
		break;
	}
}
foreach($arElementOps as $id => $arOps)
{
	if(isset($arOps["element_edit"]))
	{
		$arGroupActions["activate"] = GetMessage("MAIN_ADMIN_LIST_ACTIVATE");
		$arGroupActions["deactivate"] = GetMessage("MAIN_ADMIN_LIST_DEACTIVATE");
		break;
	}
}

$arParams = array();

if($arIBTYPE["SECTIONS"] == "Y")
{
	$sections = '<div id="section_to_move" style="display:none"><select name="section_to_move" size="1">';
	$sections .= '<option value="-1">'.GetMessage("MAIN_NO").'</option>';
	$sections .= '<option value="0">'.GetMessage("IBLOCK_UPPER_LEVEL").'</option>';
	$rsSections = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$IBLOCK_ID));
	while($ar = $rsSections->GetNext())
	{
		$sections .= '<option value="'.$ar["ID"].'">'.str_repeat(" . ", $ar["DEPTH_LEVEL"]).$ar["NAME"].'</option>';
	}
	$sections .= '</select></div>';

	$arGroupActions["section"] = GetMessage("IBEL_A_MOVE_TO_SECTION");
	$arGroupActions["add_section"] = GetMessage("IBEL_A_ADD_TO_SECTION");
	$arGroupActions["section_chooser"] = array("type" => "html", "value" => $sections);

	$arParams["select_onchange"] = "BX('section_to_move').style.display = (this.value == 'section' || this.value == 'add_section'? 'block':'none');";
}

if($bWorkFlow)
{
	$arGroupActions["unlock"] = GetMessage("IBEL_A_UNLOCK_ACTION");
	$arGroupActions["lock"] = GetMessage("IBEL_A_LOCK_ACTION");

	$statuses = '<div id="wf_status_id" style="display:none">'.SelectBox("wf_status_id", CWorkflowStatus::GetDropDownList("N", "desc")).'</div>';
	$arGroupActions["wf_status"] = GetMessage("IBEL_A_WF_STATUS_CHANGE");
	$arGroupActions["wf_status_chooser"] = array("type" => "html", "value" => $statuses);

	$arParams["select_onchange"] .= "BX('wf_status_id').style.display = (this.value == 'wf_status'? 'block':'none');";
}
elseif($bBizproc)
{
	$arGroupActions["unlock"] = GetMessage("IBEL_A_UNLOCK_ACTION");
}

$lAdmin->AddGroupActionTable($arGroupActions, $arParams);

$sLastFolder = '';
$lastSectionId = array();
if(!defined("CATALOG_PRODUCT"))
{
	$chain = $lAdmin->CreateChain();
	if($arIBTYPE["SECTIONS"]=="Y")
	{
		$chain->AddItem(array(
			"TEXT" => htmlspecialcharsex($arIBlock["NAME"]),
			"LINK" => htmlspecialcharsbx(CIBlock::GetAdminSectionListLink($IBLOCK_ID, array('find_section_section'=>0))),
		));
		$lastSectionId[] = 0;

		if($find_section_section > 0)
		{
			$sLastFolder = htmlspecialcharsbx(CIBlock::GetAdminSectionListLink($IBLOCK_ID, array('find_section_section'=>0)));
			$nav = CIBlockSection::GetNavChain($IBLOCK_ID, $find_section_section);
			while($ar_nav = $nav->GetNext())
			{
				$sLastFolder = htmlspecialcharsbx(CIBlock::GetAdminSectionListLink($IBLOCK_ID, array('find_section_section'=>$ar_nav["ID"])));
				$lastSectionId[] = $ar_nav["ID"];
				$chain->AddItem(array(
					"TEXT" => $ar_nav["NAME"],
					"LINK" => $sLastFolder,
				));
			}
		}
	}
	$lAdmin->ShowChain($chain);
}

$aContext = array();

if ($boolIBlockElementAdd)
{
	$aContext[] = array(
		"ICON" => "btn_new",
		"TEXT" => htmlspecialcharsbx($arIBlock["ELEMENT_ADD"]),
		"LINK" => CIBlock::GetAdminElementEditLink($IBLOCK_ID, 0, array(
			'IBLOCK_SECTION_ID'=>$find_section_section,
			'find_section_section'=>$find_section_section,
		)),
		"LINK_PARAM" => "",
		"TITLE" => GetMessage("IBEL_A_ADDEL_TITLE"),
	);
}

if(strlen($sLastFolder) > 0)
{
	$aContext[] = array(
		"TEXT" => GetMessage("IBEL_A_UP"),
		"LINK" => CIBlock::GetAdminElementListLink($IBLOCK_ID, array(
			'find_section_section'=>$lastSectionId[count($lastSectionId)-2],
		)),
		"LINK_PARAM" => "",
		"TITLE" => GetMessage("IBEL_A_UP_TITLE"),
	);
}

if($bBizproc && IsModuleInstalled("bizprocdesigner"))
{
	$bCanDoIt = CBPDocument::CanUserOperateDocumentType(
		CBPCanUserOperateOperation::CreateWorkflow,
		$USER->GetID(),
		array("iblock", "CIBlockDocument", "iblock_".$IBLOCK_ID)
	);

	if($bCanDoIt)
	{
		$aContext[] = array(
			"TEXT" => GetMessage("IBEL_BTN_BP"),
			"LINK" => 'iblock_bizproc_workflow_admin.php?document_type=iblock_'.$IBLOCK_ID.'&lang='.LANGUAGE_ID.'&back_url_list='.urlencode($REQUEST_URI),
			"LINK_PARAM" => "",
		);
	}
}


$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle($arIBlock["NAME"].": ".$arIBlock["ELEMENTS_NAME"]);
$APPLICATION->AddHeadScript('/bitrix/js/iblock/iblock_edit.js');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

//We need javascript not in excel mode
if((!isset($_REQUEST["mode"]) || $_REQUEST["mode"]=='list' || $_REQUEST["mode"]=='frame') && $bCatalog && $bCurrency)
{
	?><script type="text/javascript">
		top.arCatalogShowedGroups = new Array();
		top.arExtra = new Array();
		top.arCatalogGroups = new Array();
		top.BaseIndex = '';
	<?
	if (is_array($arCatGroup) && !empty($arCatGroup))
	{
		$i = 0;
		$j = 0;
		foreach($arCatGroup as &$CatalogGroups)
		{
			if(in_array("CATALOG_GROUP_".$CatalogGroups["ID"], $arSelectedFields))
			{
				echo "top.arCatalogShowedGroups[".$i."]=".$CatalogGroups["ID"].";\n";
				$i++;
			}
			if ($CatalogGroups["BASE"]!="Y")
			{
				echo "top.arCatalogGroups[".$j."]=".$CatalogGroups["ID"].";\n";
				$j++;
			}
			else
			{
				echo "top.BaseIndex=".$CatalogGroups["ID"].";\n";
			}
		}
		unset($CatalogGroups);
	}
	if (is_array($arCatExtra) && !empty($arCatExtra))
	{
		$i=0;
		foreach($arCatExtra as &$CatExtra)
		{
			echo "top.arExtra[".$CatExtra["ID"]."]=".$CatExtra["PERCENTAGE"].";\n";
			$i++;
		}
		unset($CatExtra);
	}
	?>
		top.ChangeBasePrice = function(id)
		{
			for(var i = 0, cnt = top.arCatalogShowedGroups.length; i < cnt; i++)
			{
				var pr = top.document.getElementById("CATALOG_PRICE["+id+"]"+"["+top.arCatalogShowedGroups[i]+"]");
				if(pr.disabled)
				{
					var price = top.document.getElementById("CATALOG_PRICE["+id+"]"+"["+top.BaseIndex+"]").value;
					if(price > 0)
					{
						var extraId = top.document.getElementById("CATALOG_EXTRA["+id+"]"+"["+top.arCatalogShowedGroups[i]+"]").value;
						var esum = parseFloat(price) * (1 + top.arExtra[extraId] / 100);
						var eps = 1.00/Math.pow(10, 6);
						esum = Math.round((esum+eps)*100)/100;
					}
					else
						var esum = "";

					pr.value = esum;
				}
			}
		}

		top.ChangeBaseCurrency = function(id)
		{
			var currency = top.document.getElementById("CATALOG_CURRENCY["+id+"]["+top.BaseIndex+"]");
			for(var i = 0, cnt = top.arCatalogShowedGroups.length; i < cnt; i++)
			{
				var pr = top.document.getElementById("CATALOG_CURRENCY["+id+"]["+top.arCatalogShowedGroups[i]+"]");
				if(pr.disabled)
				{
					pr.selectedIndex = currency.selectedIndex;
				}
			}
		}
	</script>
	<?
}

CJSCore::Init('file_input');
echo $CAdminCalendar_ShowScript;
?>
<form method="GET" name="find_form" id="find_form" action="<?echo $APPLICATION->GetCurPage()?>">
<?
$arFindFields = Array();
$arFindFields["IBEL_A_F_ID"] = GetMessage("IBEL_A_F_ID");

if($arIBTYPE["SECTIONS"]=="Y")
	$arFindFields["IBEL_A_F_PARENT"] = GetMessage("IBEL_A_F_PARENT");

$arFindFields["IBEL_A_F_MODIFIED_WHEN"] = GetMessage("IBEL_A_F_MODIFIED_WHEN");
$arFindFields["IBEL_A_F_MODIFIED_BY"] = GetMessage("IBEL_A_F_MODIFIED_BY");
$arFindFields["IBEL_A_F_CREATED_WHEN"] = GetMessage("IBEL_A_F_CREATED_WHEN");
$arFindFields["IBEL_A_F_CREATED_BY"] = GetMessage("IBEL_A_F_CREATED_BY");

if($bWorkFlow)
	$arFindFields["IBEL_A_F_STATUS"] = GetMessage("IBEL_A_F_STATUS");

$arFindFields["IBEL_A_F_ACTIVE_FROM"] = GetMessage("IBEL_A_ACTFROM");
$arFindFields["IBEL_A_F_ACTIVE_TO"] = GetMessage("IBEL_A_ACTTO");
$arFindFields["IBEL_A_F_ACT"] = GetMessage("IBEL_A_F_ACT");
$arFindFields["IBEL_A_F_NAME"] = GetMessage("IBEL_A_F_NAME");
$arFindFields["IBEL_A_F_DESC"] = GetMessage("IBEL_A_F_DESC");
$arFindFields["IBEL_A_CODE"] = GetMessage("IBEL_A_CODE");
$arFindFields["IBEL_A_EXTERNAL_ID"] = GetMessage("IBEL_A_EXTERNAL_ID");
$arFindFields["IBEL_A_TAGS"] = GetMessage("IBEL_A_TAGS");

foreach($arProps as $arProp)
	if($arProp["FILTRABLE"]=="Y" && $arProp["PROPERTY_TYPE"]!="F")
		$arFindFields["IBEL_A_PROP_".$arProp["ID"]] = $arProp["NAME"];

if ($boolSKU && $boolSKUFiltrable)
{
	foreach($arSKUProps as $arProp)
	{
		if($arProp["FILTRABLE"]=="Y" && $arProp["PROPERTY_TYPE"]!="F" && $arCatalog['OFFERS_PROPERTY_ID'] != $arProp['ID'])
		{
			$arFindFields["IBEL_A_SUB_PROP_".$arProp["ID"]] = ('' != $strSKUName ? $strSKUName.' - ' : '').$arProp["NAME"];
		}
	}
}

$oFilter = new CAdminFilter($sTableID."_filter", $arFindFields);
?><script type="text/javascript">
var arClearHiddenFields = new Array();
function applyFilter(el)
{
	BX.adminPanel.showWait(el);
	<?=$sTableID."_filter";?>.OnSet('<?echo CUtil::JSEscape($sTableID)?>', '<?echo CUtil::JSEscape($APPLICATION->GetCurPage().'?type='.urlencode($type).'&IBLOCK_ID='.urlencode($IBLOCK_ID).'&lang='.urlencode(LANGUAGE_ID).'&')?>');
	return false;
}

function deleteFilter(el)
{
	BX.adminPanel.showWait(el);
	if (0 < arClearHiddenFields.length)
	{
		for (var index = 0; index < arClearHiddenFields.length; index++)
		{
			if (undefined != window[arClearHiddenFields[index]])
			{
				if ('ClearForm' in window[arClearHiddenFields[index]])
				{
					window[arClearHiddenFields[index]].ClearForm();
				}
			}
		}
	}
	<?=$sTableID."_filter"?>.OnClear('<?echo CUtil::JSEscape($sTableID)?>', '<?echo CUtil::JSEscape($APPLICATION->GetCurPage().'?type='.urlencode($type).'&IBLOCK_ID='.urlencode($IBLOCK_ID).'&lang='.urlencode(LANGUAGE_ID).'&')?>');
	return false;
}
</script><?
$oFilter->Begin();
?>
	<tr>
		<td><b><?=GetMessage("MAIN_ADMIN_LIST_FILTER_1ST_NAME")?></b></td>
		<td><input type="text" name="find_el" title="<?=GetMessage("MAIN_ADMIN_LIST_FILTER_1ST")?>" value="<?echo htmlspecialcharsex($find_el)?>" size="30">
			<select name="find_el_type">
				<option value="name"<?if($find_el_type=="name") echo " selected"?>><?echo GetMessage("IBEL_A_F_NAME")?></option>
				<option value="desc"<?if($find_el_type=="desc") echo " selected"?>><?echo GetMessage("IBEL_A_F_DESC")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("IBLOCK_FILTER_FROMTO_ID")?></td>
		<td nowrap>
			<input type="text" name="find_el_id_start" size="10" value="<?echo htmlspecialcharsex($find_el_id_start)?>">
			...
			<input type="text" name="find_el_id_end" size="10" value="<?echo htmlspecialcharsex($find_el_id_end)?>">
		</td>
	</tr>

	<?if($arIBTYPE["SECTIONS"]=="Y"):?>
	<tr>
		<td><?echo GetMessage("IBLOCK_FIELD_SECTION_ID")?>:</td>
		<td>
			<select name="find_section_section">
				<option value="-1"><?echo GetMessage("IBLOCK_VALUE_ANY")?></option>
				<option value="0"<?if($find_section_section=="0")echo" selected"?>><?echo GetMessage("IBLOCK_UPPER_LEVEL")?></option>
				<?
				$bsections = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$IBLOCK_ID));
				while($ar = $bsections->GetNext()):
					?><option value="<?echo $ar["ID"]?>"<?if($ar["ID"]==$find_section_section)echo " selected"?>><?echo str_repeat("&nbsp;.&nbsp;", $ar["DEPTH_LEVEL"])?><?echo $ar["NAME"]?></option><?
				endwhile;
				?>
			</select><br>
			<input type="checkbox" name="find_el_subsections" value="Y"<?if($find_el_subsections=="Y")echo" checked"?>> <?echo GetMessage("IBLOCK_INCLUDING_SUBSECTIONS")?>
		</td>
	</tr>
	<?endif?>

	<tr>
		<td><?echo GetMessage("IBLOCK_FIELD_TIMESTAMP_X")?>:</td>
		<td><?echo CalendarPeriod("find_el_timestamp_from", htmlspecialcharsex($find_el_timestamp_from), "find_el_timestamp_to", htmlspecialcharsex($find_el_timestamp_to), "find_form", "Y")?></font></td>
	</tr>

	<tr>
		<td><?=GetMessage("IBLOCK_FIELD_MODIFIED_BY")?>:</td>
		<td><input type="text" name="find_el_modified_user_id" value="<?echo htmlspecialcharsex($find_el_modified_user_id)?>" size="3">&nbsp;<?
		$gr_res = CIBlock::GetGroupPermissions($IBLOCK_ID);
		$res = Array(1);
		foreach($gr_res as $gr=>$perm)
			if($perm>"R")
				$res[] = $gr;
			$res = CUser::GetList($byx="NAME", $orderx="ASC", Array("GROUP_MULTI"=>$res));
		?><select name="find_el_modified_by">
		<option value=""><?echo GetMessage("IBLOCK_VALUE_ANY")?></option><?
		while($arr = $res->Fetch())
			echo "<option value='".$arr["ID"]."'".($find_el_modified_by==$arr["ID"]?" selected":"").">(".htmlspecialcharsex($arr["LOGIN"].") ".$arr["NAME"]." ".$arr["LAST_NAME"])."</option>";
		?></select>
		</td>
	</tr>

	<tr>
		<td><?echo GetMessage("IBLOCK_EL_ADMIN_DCREATE")?>:</td>
		<td><?echo CalendarPeriod("find_el_created_from", htmlspecialcharsex($find_el_created_from), "find_el_created_to", htmlspecialcharsex($find_el_created_to), "find_form", "Y")?></td>
	</tr>

	<tr>
		<td><?echo GetMessage("IBLOCK_EL_ADMIN_WCREATE")?></td>
		<td><input type="text" name="find_el_created_user_id" value="<?echo htmlspecialcharsex($find_el_created_user_id)?>" size="3">&nbsp;<?
		$gr_res = CIBlock::GetGroupPermissions($IBLOCK_ID);
		$res = Array(1);
		foreach($gr_res as $gr=>$perm)
			if($perm>"R")
				$res[] = $gr;
		$res = CUser::GetList($byx="NAME", $orderx="ASC", Array("GROUP_MULTI"=>$res));
		?><select name="find_el_created_by">
		<option value=""><?echo GetMessage("IBLOCK_VALUE_ANY")?></option><?
		while($arr = $res->Fetch())
			echo "<option value='".$arr["ID"]."'".($find_el_created_by==$arr["ID"]?" selected":"").">(".htmlspecialcharsex($arr["LOGIN"].") ".$arr["NAME"]." ".$arr["LAST_NAME"])."</option>";
		?></select>
		</td>
	</tr>

	<?if($bWorkFlow):?>
	<tr>
		<td><?=GetMessage("IBLOCK_FIELD_STATUS")?></td>
		<td><input type="text" name="find_el_status_id" value="<?echo htmlspecialcharsex($find_el_status_id)?>" size="3">
		<select name="find_el_status">
		<option value=""><?=GetMessage("IBLOCK_VALUE_ANY")?></option>
		<?
		$rs = CWorkflowStatus::GetDropDownList("Y");
		while($arRs = $rs->GetNext())
		{
			?><option value="<?=$arRs["REFERENCE_ID"]?>"<?if($find_el_status == $arRs["~REFERENCE_ID"])echo " selected"?>><?=$arRs["REFERENCE"]?></option><?
		}
		?>
		</select></td>
	</tr>
	<?endif?>

	<tr>
		<td><?echo GetMessage("IBEL_A_ACTFROM")?>:</td>
		<td><?echo CalendarPeriod("find_el_date_active_from_from", htmlspecialcharsex($find_el_date_active_from_from), "find_el_date_active_from_to", htmlspecialcharsex($find_el_date_active_from_to), "find_form")?></td>
	</tr>

	<tr>
		<td><?echo GetMessage("IBEL_A_ACTTO")?>:</td>
		<td><?echo CalendarPeriod("find_el_date_active_to_from", htmlspecialcharsex($find_el_date_active_to_from), "find_el_date_active_to_to", htmlspecialcharsex($find_el_date_active_to_to), "find_form")?></td>
	</tr>

	<tr>
		<td><?echo GetMessage("IBLOCK_FIELD_ACTIVE")?>:</td>
		<td>
			<select name="find_el_active">
				<option value=""><?=htmlspecialcharsex(GetMessage('IBLOCK_VALUE_ANY'))?></option>
				<option value="Y"<?if($find_el_active=="Y")echo " selected"?>><?=htmlspecialcharsex(GetMessage("IBLOCK_YES"))?></option>
				<option value="N"<?if($find_el_active=="N")echo " selected"?>><?=htmlspecialcharsex(GetMessage("IBLOCK_NO"))?></option>
			</select>
		</td>
	</tr>

	<tr>
		<td><?echo GetMessage("IBLOCK_FIELD_NAME")?>:</td>
		<td><input type="text" name="find_el_name" value="<?echo htmlspecialcharsex($find_el_name)?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("IBLOCK_EL_ADMIN_DESC")?></td>
		<td><input type="text" name="find_el_intext" value="<?echo htmlspecialcharsex($find_el_intext)?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?></td>
	</tr>

	<tr>
		<td><?=GetMessage("IBEL_A_CODE")?>:</td>
		<td><input type="text" name="find_el_code" value="<?echo htmlspecialcharsex($find_el_code)?>" size="30"></td>
	</tr>
	<tr>
		<td><?=GetMessage("IBEL_A_EXTERNAL_ID")?>:</td>
		<td><input type="text" name="find_el_external_id" value="<?echo htmlspecialcharsex($find_el_external_id)?>" size="30"></td>
	</tr>
	<tr>
		<td><?=GetMessage("IBEL_A_TAGS")?>:</td>
		<td>
			<?
			if ($bSearch):
				echo InputTags("find_el_tags", $find_el_tags, $arIBlock["SITE_ID"]);
			else:
			?>
				<input type="text" name="find_el_tags" value="<?echo htmlspecialcharsex($find_el_tags)?>" size="30">
			<?endif?>
		</td>
	</tr>
	<?

function _ShowGroupPropertyField2($name, $property_fields, $values)
{
	if(!is_array($values)) $values = Array();

	$res = "";
	$result = "";
	$bWas = false;
	$sections = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$property_fields["LINK_IBLOCK_ID"]));
	while($ar = $sections->GetNext())
	{
		$res .= '<option value="'.$ar["ID"].'"';
		if(in_array($ar["ID"], $values))
		{
			$bWas = true;
			$res .= ' selected';
		}
		$res .= '>'.str_repeat(" . ", $ar["DEPTH_LEVEL"]).$ar["NAME"].'</option>';
	}
	$result .= '<select name="'.$name.'[]" size="'.($property_fields["MULTIPLE"]=="Y" ? "5":"1").'" '.($property_fields["MULTIPLE"]=="Y"?"multiple":"").'>';
	$result .= '<option value=""'.(!$bWas?' selected':'').'>'.GetMessage("IBLOCK_ELEMENT_EDIT_NOT_SET").'</option>';
	$result .= $res;
	$result .= '</select>';
	return $result;
}

foreach($arProps as $arProp):
	if($arProp["FILTRABLE"]=="Y" && $arProp["PROPERTY_TYPE"]!="F"):
?>
<tr>
	<td><?=$arProp["NAME"]?>:</td>
	<td>
		<?if(array_key_exists("GetAdminFilterHTML", $arProp["PROPERTY_USER_TYPE"])):
			echo call_user_func_array($arProp["PROPERTY_USER_TYPE"]["GetAdminFilterHTML"], array(
				$arProp,
				array("VALUE" => "find_el_property_".$arProp["ID"]),
			));
		elseif($arProp["PROPERTY_TYPE"]=='S'):?>
			<input type="text" name="find_el_property_<?=$arProp["ID"]?>" value="<?echo htmlspecialcharsex(${"find_el_property_".$arProp["ID"]})?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
		<?elseif($arProp["PROPERTY_TYPE"]=='N' || $arProp["PROPERTY_TYPE"]=='E'):?>
			<input type="text" name="find_el_property_<?=$arProp["ID"]?>" value="<?echo htmlspecialcharsex(${"find_el_property_".$arProp["ID"]})?>" size="30">
		<?elseif($arProp["PROPERTY_TYPE"]=='L'):?>
			<select name="find_el_property_<?=$arProp["ID"]?>">
				<option value=""><?echo GetMessage("IBLOCK_VALUE_ANY")?></option>
				<option value="NOT_REF"><?echo GetMessage("IBLOCK_ELEMENT_EDIT_NOT_SET")?></option><?
				$dbrPEnum = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array("PROPERTY_ID"=>$arProp["ID"]));
				while($arPEnum = $dbrPEnum->GetNext()):
				?>
					<option value="<?=$arPEnum["ID"]?>"<?if(${"find_el_property_".$arProp["ID"]} == $arPEnum["ID"])echo " selected"?>><?=$arPEnum["VALUE"]?></option>
				<?
				endwhile;
		?></select>
		<?
		elseif($arProp["PROPERTY_TYPE"]=='G'):
			echo _ShowGroupPropertyField2('find_el_property_'.$arProp["ID"], $arProp, ${'find_el_property_'.$arProp["ID"]});
		endif;
		?>
	</td>
</tr>
<?
	endif;
endforeach;

if ($boolSKU && $boolSKUFiltrable)
{
	foreach($arSKUProps as $arProp)
	{
		if($arProp["FILTRABLE"]=="Y" && $arProp["PROPERTY_TYPE"]!="F" && $arCatalog['OFFERS_PROPERTY_ID'] != $arProp['ID'])
		{
?>
<tr>
	<td><? echo ('' != $strSKUName ? $strSKUName.' - ' : ''); ?><? echo $arProp["NAME"]?>:</td>
	<td>
		<?if(array_key_exists("GetAdminFilterHTML", $arProp["PROPERTY_USER_TYPE"])):
			echo call_user_func_array($arProp["PROPERTY_USER_TYPE"]["GetAdminFilterHTML"], array(
				$arProp,
				array("VALUE" => "find_sub_el_property_".$arProp["ID"]),
			));
		elseif($arProp["PROPERTY_TYPE"]=='S'):?>
			<input type="text" name="find_sub_el_property_<?=$arProp["ID"]?>" value="<?echo htmlspecialcharsex(${"find_sub_el_property_".$arProp["ID"]})?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
		<?elseif($arProp["PROPERTY_TYPE"]=='N' || $arProp["PROPERTY_TYPE"]=='E'):?>
			<input type="text" name="find_sub_el_property_<?=$arProp["ID"]?>" value="<?echo htmlspecialcharsex(${"find_sub_el_property_".$arProp["ID"]})?>" size="30">
		<?elseif($arProp["PROPERTY_TYPE"]=='L'):?>
			<select name="find_sub_el_property_<?=$arProp["ID"]?>">
				<option value=""><?echo GetMessage("IBLOCK_VALUE_ANY")?></option>
				<option value="NOT_REF"><?echo GetMessage("IBLOCK_ELEMENT_EDIT_NOT_SET")?></option><?
				$dbrPEnum = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array("PROPERTY_ID"=>$arProp["ID"]));
				while($arPEnum = $dbrPEnum->GetNext()):
				?>
					<option value="<?=$arPEnum["ID"]?>"<?if(${"find_sub_el_property_".$arProp["ID"]} == $arPEnum["ID"])echo " selected"?>><?=$arPEnum["VALUE"]?></option>
				<?
				endwhile;
		?></select>
		<?
		elseif($arProp["PROPERTY_TYPE"]=='G'):
			echo _ShowGroupPropertyField2('find_sub_el_property_'.$arProp["ID"], $arProp, ${'find_sub_el_property_'.$arProp["ID"]});
		endif;
		?>
	</td>
</tr>
<?
		}
	}
}
?>
<?
$oFilter->Buttons();
?><span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="set_filter" value="<? echo GetMessage("admin_lib_filter_set_butt"); ?>" title="<? echo GetMessage("admin_lib_filter_set_butt_title"); ?>" onClick="return applyFilter(this);"></span>
<span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="del_filter" value="<? echo GetMessage("admin_lib_filter_clear_butt"); ?>" title="<? echo GetMessage("admin_lib_filter_clear_butt_title"); ?>" onClick="deleteFilter(this); return false;"></span>
<?
$oFilter->End();
?>
</form>
<?
$lAdmin->DisplayList();
?>
<?if($bWorkFlow || $bBizproc):?>
	<?echo BeginNote();?>
	<span class="adm-lamp adm-lamp-green"></span> - <?echo GetMessage("IBLOCK_GREEN_ALT")?></br>
	<span class="adm-lamp adm-lamp-yellow"></span> - <?echo GetMessage("IBLOCK_YELLOW_ALT")?></br>
	<span class="adm-lamp adm-lamp-red"></span> - <?echo GetMessage("IBLOCK_RED_ALT")?></br>
	<?echo EndNote();?>
<?endif;?>
<?
if(CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_edit") && !defined("CATALOG_PRODUCT"))
{
	echo
		BeginNote(),
		GetMessage("IBEL_A_IBLOCK_MANAGE_HINT"),
		' <a href="'.htmlspecialcharsbx('iblock_edit.php?type='.urlencode($type).'&lang='.urlencode(LANGUAGE_ID).'&ID='.urlencode($IBLOCK_ID).'&admin=Y&return_url='.urlencode(CIBlock::GetAdminElementListLink($IBLOCK_ID, array('find_section_section' => intval($find_section_section))))).'">',
		GetMessage("IBEL_A_IBLOCK_MANAGE_HINT_HREF"),
		'</a>',
		EndNote()
	;
}
?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>