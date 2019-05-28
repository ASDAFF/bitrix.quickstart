<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/admin_tool.php");

IncludeModuleLangFile(__FILE__);
ClearVars("u_");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");

$arStatusList = False;
$arFilter = array("LID" => LANG, "ID" => "N");
$arGroupByTmpSt = false;
if ($saleModulePermissions < "W")
{
	$arFilter["GROUP_ID"] = $GLOBALS["USER"]->GetUserGroupArray();
	$arFilter["PERM_UPDATE"] = "Y";
	$arGroupByTmpSt = array("ID", "NAME", "MAX" => "PERM_UPDATE");
}
$dbStatusList = CSaleStatus::GetList(
		array(),
		$arFilter,
		$arGroupByTmpSt,
		false,
		array("ID", "NAME")
		);
$arStatusList = $dbStatusList->Fetch();

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D" OR ($saleModulePermissions < "W" AND $arStatusList["PERM_UPDATE"] != "Y"))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(!CBXFeatures::IsFeatureEnabled('SaleAccounts'))
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$ID = IntVal($_GET["USER_ID"]);

//reorder
if(isset($_REQUEST["reorder"]) && IntVal($_REQUEST["reorder"]) > 0)
{
	$ORDER_ID = IntVal($_REQUEST["reorder"]);
	$lid = trim($_REQUEST["lid"]);
	$arID = array();
	$urlProduct = "";

	$dbBasketList = CSaleBasket::GetList(
		array("PRODUCT_ID" => "ASC"),
		array("LID" => $lid, "ORDER_ID" => $ORDER_ID),
		false,
		false,
		array('PRODUCT_ID', 'ID', 'QUANTITY')
	);
	while ($arBasket = $dbBasketList->Fetch())
		$urlProduct .= "&product[".$arBasket["PRODUCT_ID"]."]=".$arBasket["QUANTITY"];

	LocalRedirect("/bitrix/admin/sale_order_new.php?user_id=".CUtil::JSEscape($ID)."&lang=".LANG."&LID=".CUtil::JSEscape($lid).CUtil::JSEscape($urlProduct));
}

//USER INFO
$userFIO = "";
$dbUser = CUser::GetByID($ID);
if($arUser = $dbUser->ExtractFields("u_"))
{
	$userFIO = $u_NAME;
	if (strlen($u_LAST_NAME) > 0)
	{
		if (strlen($userFIO) > 0)
			$userFIO .= " ";
		$userFIO .= $u_LAST_NAME;
	}
	if (strlen($u_SECOND_NAME) > 0)
	{
		if (strlen($userFIO) > 0)
			$userFIO .= " ";
		$userFIO .= $u_SECOND_NAME;
	}
}

$userAdres = "";
$strUserGroup = "";
if(!empty($arUser))
{
	//user group
	$arUserGroups = CUser::GetUserGroup($ID);
	$dbGroups = CGroup::GetList(($b = "c_sort"), ($o = "asc"), array("ANONYMOUS" => "N"));
	while ($arGroups = $dbGroups->Fetch())
	{
		if (in_array($arGroups["ID"], $arUserGroups))
			$strUserGroup .= htmlspecialcharsbx($arGroups["NAME"])."<br>";
	}

	//user adres
	if (strlen($u_PERSONAL_STATE) > 0)
		$userAdres .= $u_PERSONAL_STATE;
	if (strlen($u_PERSONAL_CITY) > 0)
	{
		if (strlen($userAdres) > 0)
			$userAdres .= ", ";
		$userAdres .= $u_PERSONAL_CITY;
	}
	if (strlen($u_PERSONAL_STREET) > 0)
	{
		if (strlen($userAdres) > 0)
			$userAdres .= ", ";
		$userAdres .= $u_PERSONAL_STREET;
	}

	//ALL SITES
	$arSites = array();
	$rsSites = CSite::GetList($oby="id", $oorder="asc", array());
	while ($arSite = $rsSites->Fetch())
		$arSites[$arSite["ID"]] = $arSite;
}


$viewedMessage = '';
$basketMessage = '';
$viewedError = '';
$basketError = '';

//ACTIONS
//viewed
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'viewed_apply' && IntVal($_REQUEST["viewed_id"]) > 0 && strlen($_REQUEST["viewed_lid"]) > 0 && $saleModulePermissions >= "W")
{
	$PRODUCT_ID = IntVal($_REQUEST["viewed_id"]);
	$LID = trim($_REQUEST["viewed_lid"]);

	if (CModule::IncludeModule("catalog"))
	{
		/** @var $productProvider IBXSaleProductProvider */
		if ($productProvider = CSaleBasket::GetProductProvider(array("MODULE" => "catalog", "PRODUCT_PROVIDER_CLASS" => "CCatalogProductProvider")))
		{
			$arViews = $productProvider::GetProductData(array(
				"PRODUCT_ID" => $PRODUCT_ID,
				"QUANTITY"   => 1,
				"RENEWAL"    => "N",
				"SITE_ID"    => $LID
			));
		}
		else
		{
			$arViews = CSaleBasket::ExecuteCallbackFunction(
				'CatalogBasketCallback',
				'catalog',
				$PRODUCT_ID,
				1,
				$LID
			);
		}

		if (count($arViews) <= 0)
		{
			$viewedError = GetMessage('BUYER_VIEWED_ERROR_PRODUCT_FAIL');
			$viewedMessage = "";
		}
		else
		{
			$dbProduct = CIBlockElement::GetList(array(), array("ID" => $PRODUCT_ID), false, false, array('ID', 'NAME', "XML_ID", "IBLOCK_EXTERNAL_ID"));
			$arProduct = $dbProduct->Fetch();
			$arViews['NAME'] = $arProduct["NAME"];
			$arViews['PRODUCT_ID'] = $arProduct["ID"];
			$arViews['MODULE'] = "catalog";
			$arViews["LID"] = $LID;
		}
	}
	else
	{
		$dbViewsList = CSaleViewedProduct::GetList(
					array(),
					array("FUSER_ID" => $arFuserItems["ID"], "PRODUCT_ID" => $PRODUCT_ID),
					false,
					false
		);
		$arViews = $dbViewsList->Fetch();
	}

	if (strlen($viewedError) <= 0)
	{
		$arFields = array("PROPS" => array());

		$arParentSku = CCatalogSku::GetProductInfo($PRODUCT_ID);
		if ($arParentSku && count($arParentSku) > 0)
		{
			$arPropsSku = array();

			$dbProduct = CIBlockElement::GetList(array(), array("ID" => $PRODUCT_ID), false, false, array('IBLOCK_ID'));
			$arProduct = $dbProduct->Fetch();

			$dbOfferProperties = CIBlock::GetProperties($arProduct["IBLOCK_ID"], array(), array("!XML_ID" => "CML2_LINK"));
			while($arOfferProperties = $dbOfferProperties->Fetch())
				$arPropsSku[] = $arOfferProperties["CODE"];

			$arFields["PROPS"] = CIBlockPriceTools::GetOfferProperties(
							$PRODUCT_ID,
							$arParentSku["IBLOCK_ID"],
							$arPropsSku
						);
		}

		$arFields["USER_ID"] = $ID;
		$arFields["PRODUCT_ID"] = $PRODUCT_ID;
		$arFields["PRICE"] = $arViews["PRICE"];
		$arFields["CURRENCY"] = $arViews["CURRENCY"];
		$arFields["QUANTITY"] = 1;
		$arFields["LID"] = $arViews["LID"];
		$arFields["DETAIL_PAGE_URL"] = $arViews["DETAIL_PAGE_URL"];
		$arFields["NAME"] = $arViews["NAME"];
		$arFields["CAN_BUY"] = "Y";
		$arFields["TYPE"] = $arViews["TYPE"];

		$arFields["MODULE"] = $arViews["MODULE"];
		if ($arViews["MODULE"] == "catalog")
		{
			$arFields["CALLBACK_FUNC"] = "CatalogBasketCallback";
			$arFields["ORDER_CALLBACK_FUNC"] = "CatalogBasketOrderCallback";
			$arFields["CANCEL_CALLBACK_FUNC"] = "CatalogBasketCancelCallback";
			$arFields["PAY_CALLBACK_FUNC"] = "CatalogPayOrderCallback";
			$arFields["PRODUCT_PROVIDER_CLASS"] = "CCatalogProductProvider";
		}

		if(strlen($arProduct["IBLOCK_EXTERNAL_ID"]) > 0)
		{
			$arFields["CATALOG_XML_ID"] = $arProduct["IBLOCK_EXTERNAL_ID"];
			$arFields["PROPS"][] = Array(
					"NAME" => "Catalog XML_ID",
					"CODE" => "CATALOG.XML_ID",
					"VALUE" => $arProduct["IBLOCK_EXTERNAL_ID"],
				);
		}
		if(intVal($arProduct["XML_ID"]) > 0)
		{
			$arFields["PRODUCT_XML_ID"] = $arProduct["XML_ID"];
			$arFields["PROPS"][] = Array(
					"NAME" => "Product XML_ID",
					"CODE" => "PRODUCT.XML_ID",
					"VALUE" => $arProduct["XML_ID"],
				);
		}

		$arFuserItems = CSaleUser::GetList(array("USER_ID" => $ID));
		if (count($arFuserItems) > 0)
			$arFields["FUSER_ID"] = $arFuserItems["ID"];

		if (!CSaleBasket::Add($arFields))
		{
			$viewedError = GetMessage('BUYER_VIEWED_ADD_ERROR');
			$viewedMessage = "";
		}
		else
			$viewedMessage = GetMessage('BUYER_VIEWED_MESS_BASKET');
	}
}

//basket
if (isset($_REQUEST['apply']) && isset($_REQUEST['action']) && $saleModulePermissions >= "W" && check_bitrix_sessid())
{
	$arID = array();
	$arFields = array();

	if($arFuserItems = CSaleUser::GetList(array("USER_ID" => $ID)))
	{
		$arFields["FUSER_ID"] = $arFuserItems["ID"];

		if (!isset($_REQUEST["action_target"]) || $_REQUEST["action_target"] != "selected")
		{
			if(isset($_REQUEST['PRODUCT_ID']) && count($_REQUEST['PRODUCT_ID']) > 0)
			{
				if(!is_array($_REQUEST['PRODUCT_ID']))
					$arID = array($_REQUEST['PRODUCT_ID']);
				else
					$arID = $_REQUEST['PRODUCT_ID'];
			}

			//delete default ID
			foreach($arID as $lid => $arProduct)
			{
				foreach ($arProduct as $key => $val)
				{
					if (!in_array($val, $_POST["ID"]))
						unset($arID[$lid][$key]);
				}
			}
		}
		else
		{
			$arBasketActionFilter = array("FUSER_ID" => $arFields["FUSER_ID"], "ORDER_ID" => "NULL", "CAN_BUY" => "Y");
			if (strlen($filter_basket_lid)>0)
				$arBasketActionFilter["LID"] = trim($filter_basket_lid);

			$dbBasketEl = CSaleBasket::GetList(
							array(),
							$arBasketActionFilter,
							false,
							false,
							array('ID', 'PRODUCT_ID', 'LID')
					);
			while($arBasketEl = $dbBasketEl->Fetch())
				$arID[$arBasketEl["LID"]][] = $arBasketEl["PRODUCT_ID"];
		}

		$LID = "";
		foreach($arID as $key => $val)
		{
			if ($LID == "")
				$LID = $key;

			if ($LID != $key)
			{
				if (isset($_REQUEST['basket_apply']))
					$basketError = GetMessage('BUYER_VIEWED_BASKET_ERROR_TO_ORDER');

				break;
			}
		}

		if ($basketError == '' && $basketError == '')
		{
			switch ($_REQUEST['action'])
			{
				case "order_basket":
					if (count($arID[$LID]) > 0)
					{
						$urlProduct = "";
						$arIDProd = array();
						foreach ($arID[$LID] as $PRODUCT_ID)
						{
							$arIDProd[] = $PRODUCT_ID;
						}

						$dbBasketEl = CSaleBasket::GetList(
										array(),
										array("LID" => $LID, "FUSER_ID" => $arFields["FUSER_ID"], "PRODUCT_ID" => $arIDProd, "ORDER_ID" => "NULL"),
										false,
										false,
										array('CAN_BUY', "SUBSCRIBE", "DELAY", "PRODUCT_ID", "QUANTITY")
								);
						while($arBasketEl = $dbBasketEl->Fetch())
						{
							$urlProduct .= "&product[".$arBasketEl["PRODUCT_ID"]."]=".$arBasketEl["QUANTITY"];
							if ($arBasketEl["CAN_BUY"] != "Y" || $arBasketEl["DELAY"] == "Y" || $arBasketEl["SUBSCRIBE"] == "Y")
							{
								$basketError = GetMessage('BUYER_BASKET_ORDER_ERROR');
								break;
							}
						}

						if (strlen($basketError) <= 0)
						{
							echo "<script language=\"JavaScript\">";
							echo "window.parent.location.href = '/bitrix/admin/sale_order_new.php?user_id=".CUtil::JSEscape($ID)."&lang=".LANG."&LID=".CUtil::JSEscape($LID).CUtil::JSEscape($urlProduct)."';";
							echo "</script>";
							exit;
						}
					}
					else
					{
						if(isset($_REQUEST['basket_apply']) && isset($_REQUEST['BASKET_ID']))
							$basketError = GetMessage('BUYER_BASKET_MESS_NULL');
					}
					break;
				case "delay_y":
				case "delay_n":
					$arFields["DELAY"] = (($_REQUEST['action']=="delay_y") ? "Y" : "N");

					if (is_array($arID[$LID]))
					{
						foreach ($arID[$LID] as $PRODUCT_ID)
						{
							$dbBasketEl = CSaleBasket::GetList(
									array(),
									array("LID" => $LID, "FUSER_ID" => $arFields["FUSER_ID"], "PRODUCT_ID" => $PRODUCT_ID, "ORDER_ID" => "NULL"),
									false,
									false,
									array('ID')
							);
							$arBasketEl = $dbBasketEl->Fetch();

							if (!CSaleBasket::Update($arBasketEl["ID"], $arFields))
								$basketError = GetMessage('BUYER_BASKET_ADD_ERROR');

						}//end foreach
					}
					break;

				case "delete_basket":
					if (is_array($arID[$LID]))
					{
						foreach ($arID[$LID] as $PRODUCT_ID)
						{
							$dbBasketEl = CSaleBasket::GetList(
									array(),
									array("LID" => $LID, "FUSER_ID" => $arFields["FUSER_ID"], "PRODUCT_ID" => $PRODUCT_ID, "ORDER_ID" => "NULL"),
									false,
									false,
									array('ID')
							);
							$arBasketEl = $dbBasketEl->Fetch();

							CSaleBasket::Delete($arBasketEl["ID"]);
						}
					}
					break;
			}
		}
	}
}

$pageTitle = "";
if(!empty($arUser))
	$pageTitle = " \"(".htmlspecialcharsBack($u_LOGIN).") ".htmlspecialcharsBack($userFIO)."\"";
$APPLICATION->SetTitle(GetMessage("BUYER_TITLE").$pageTitle);

if(!empty($arUser))
{
	$dbStatus = CSaleStatus::GetList(array(), array(), false, false, array('ID', 'NAME', 'LID'));
	$arStatusOrder = array();
	while ($arStatus = $dbStatus->Fetch())
		$arStatusOrder[$arStatus["ID"]] = $arStatus;


	//MAIN INFORMATION
	$sTableID_tab1 = "tbl_sale_buyers_profile_tab1";
	$oSort_tab1 = new CAdminSorting($sTableID_tab1);
	$lAdmin_tab1 = new CAdminList($sTableID_tab1, $oSort_tab1);

	$arFilter = array("USER_ID" => $ID);

	$dbOrderList = CSaleOrder::GetList(
		array('DATE_INSERT' => 'DESC'),
		$arFilter,
		false,
		array("nTopCount" => 10),
		array('ID', 'DATE_INSERT', 'PAYED', 'DATE_PAYED', 'CANCELED', 'DATE_CANCELED', 'PRICE', 'CURRENCY', "STATUS_ID", "DATE_STATUS", "LID")
	);

	$dbOrderList = new CAdminResult($dbOrderList, $sTableID_tab1);
	$dbOrderList->NavStart();
	$lAdmin_tab1->NavText($dbOrderList->GetNavPrint(GetMessage('BUYER_ORDER_LIST')));

	$mainOrderHeader = array(
		array("id"=>"ID", "content"=>ID, "sort"=>"", "default"=>true),
		array("id"=>"STATUS_ID","content"=>GetMessage("BUYER_LAST_H_STATUS"), "sort"=>"", "default"=>true),
		array("id"=>"PAYED", "content"=>GetMessage("BUYER_LAST_H_PAYED"), "sort"=>"", "default"=>true),
		array("id"=>"CANCELED", "content"=>GetMessage("BUYER_LAST_H_CANCEL"), "sort"=>"", "default"=>true),
		array("id"=>"PRODUCT", "content"=>GetMessage("BUYER_LAST_H_PRODUCT"), "sort"=>"", "default"=>true),
		array("id"=>"PRICE", "content"=>GetMessage("BUYER_LAST_H_PRICE"), "sort"=>"", "default"=>true),
		array("id"=>"DATE_INSERT", "content"=>GetMessage("BUYER_LAST_H_DATE"), "sort"=>"", "default"=>true),
	);
	if (count($arSites) > 1)
		$mainOrderHeader[] = array("id"=>"LID", "content"=>GetMessage("BUYER_BH_LID"), "sort"=>"LID", "default"=>true);

	$lAdmin_tab1->AddHeaders($mainOrderHeader);

	while ($arOrderMain = $dbOrderList->Fetch())
	{
		$row =& $lAdmin_tab1->AddRow($arOrderMain["ID"], $arOrderMain, '', '');
		$orderLink = "<a href=\"sale_order_detail.php?ID=".$arOrderMain["ID"]."&lang=".LANG."\">".$arOrderMain["ID"]."</a>";
		$row->AddField("ID", $orderLink);

		$basketCount = 0;
		$dbBasketCount = CSaleBasket::GetList(array(), array("ORDER_ID" => $arOrderMain["ID"]));
		while ($arBasket = $dbBasketCount->GetNext())
		{
			if (!CSaleBasketHelper::isSetItem($arBasket))
				$basketCount++;
		}

		$status = "[".$arOrderMain["STATUS_ID"]."] ".htmlspecialcharsbx($arStatusOrder[$arOrderMain["STATUS_ID"]]["NAME"])."<br />".$arOrderMain["DATE_STATUS"];
		$row->AddField("STATUS_ID", $status);

		$payed = (($arOrderMain["PAYED"] == "Y") ? GetMessage("BUYERS_PAY_YES") : GetMessage("BUYERS_PAY_NO"));
		if (strlen($arOrderMain["DATE_PAYED"]) > 0)
			$payed .= "<br>".$arOrderMain["DATE_PAYED"];
		$row->AddField("PAYED", $payed);

		$cancel = (($arOrderMain["CANCELED"] == "Y") ? GetMessage("BUYER_LAST_YES") : GetMessage("BUYER_LAST_NO"));
		if (strlen($arOrderMain["DATE_CANCELED"]) > 0)
			$cancel .= "<br>".$arOrderMain["DATE_CANCELED"];
		$row->AddField("CANCELED", $cancel);
		$row->AddField("PRODUCT", $basketCount);
		$row->AddField("PRICE", SaleFormatCurrency($arOrderMain["PRICE"], $arOrderMain["CURRENCY"]));

		if (count($arSites) > 1)
			$row->AddField("LID", "[".$arOrderMain["LID"]."] ".htmlspecialcharsbx($arSites[$arOrderMain["LID"]]["NAME"])."");
	}

	if($_REQUEST["table_id"]==$sTableID_tab1)
		$lAdmin_tab1->CheckListMode();
	//END MAIN INFO


	//BUYERS PROFILE
	$sTableID_tab2 = "tbl_sale_buyers_profile_tab2";
	$oSort_tab2 = new CAdminSorting($sTableID_tab2);
	$lAdmin_tab2 = new CAdminList($sTableID_tab2, $oSort_tab2);

	$arPErsonTypes = array();
	$db_ptype = CSalePersonType::GetList(($by1="SORT"), ($order1="ASC"));
	while ($ptype = $db_ptype->Fetch())
		$arPErsonTypes[$ptype["ID"]] = $ptype;

	if (!isset($_REQUEST["by"]))
		$arProfSort = array("PERSON_TYPE_ID" => "ASC", "DATE_UPDATE" => "DESC");
	else
		$arProfSort[$by] = $order;

	$dbProfileList = CSaleOrderUserProps::GetList(
				$arProfSort,
				array("USER_ID" => $ID),
				false,
				false,
				array("ID", "NAME", "PERSON_TYPE_ID", "DATE_UPDATE")
	);

	$dbProfileList = new CAdminResult($dbProfileList, $sTableID_tab2);
	$dbProfileList->NavStart();
	$lAdmin_tab2->NavText($dbProfileList->GetNavPrint(GetMessage('BUYER_PERSON_LIST')));

	$personHeader = array(
		array("id"=>"NAME", "content"=>GetMessage("BUYER_P_NAME"), "sort"=>"NAME", "default"=>true),
		array("id"=>"PERSON_TYPE_ID","content"=>GetMessage("BUYER_P_PERSONTYPE"), "sort"=>"PERSON_TYPE_ID", "default"=>true),
		array("id"=>"DATE_UPDATE", "content"=>GetMessage("BUYER_P_DATE_UPDATE"), "sort"=>"DATE_UPDATE", "default"=>true),
	);

	$lAdmin_tab2->AddHeaders($personHeader);

	while ($arProfList = $dbProfileList->GetNext())
	{
		$row =& $lAdmin_tab2->AddRow($arProfList["ID"], $arProfList, "sale_buyers_profile_edit.php?id=".$arProfList["ID"]."&lang=".LANG, GetMessage("BUYER_P_PROFILE_EDIT"));
		$row->AddField("NAME", "[".$arProfList["ID"]."] <a href=\"/bitrix/admin/sale_buyers_profile_edit.php?id=".$arProfList["ID"]."&lang=".LANG."\">".$arProfList["NAME"]."</a>");
		$row->AddField("PERSON_TYPE_ID", htmlspecialcharsbx($arPErsonTypes[$arProfList["PERSON_TYPE_ID"]]["NAME"]));

		if (count($arSites) > 1)
			$row->AddField("LID", "[".$arProfList["LID"]."] ".htmlspecialcharsbx($arSites[$arProfList["LID"]]["NAME"])."");
	}

	if($_REQUEST["table_id"]==$sTableID_tab2)
		$lAdmin_tab2->CheckListMode();
	//END BUYERS PROFILE


	//BUYERS ORDERS
	$sTableID_tab3 = "tbl_sale_buyers_profile_tab3";
	$oSort_tab3 = new CAdminSorting($sTableID_tab3);
	$lAdmin_tab3 = new CAdminList($sTableID_tab3, $oSort_tab3);

	//FILTER ORDER
	$arFilterFields = array(
		"filter_order_lid",
		"filter_order_status",
		"filter_order_payed",
		"filter_order_price",
		"filter_date_order_from",
		"filter_date_order_to",
		"filter_summa_to",
		"filter_summa_from",
		"filter_order_prod_name",
	);
	$lAdmin_tab3->InitFilter($arFilterFields);

	if (!isset($_REQUEST["by"]))
		$arOrderSort = array("DATE_INSERT" => "DESC");
	else
		$arOrderSort[$by] = $order;

	if ($by == "PAYED")
		$arOrderSort["DATE_PAYED"] = $order;

	$arOrderFilter = array("USER_ID" => $ID);

	if (strlen($filter_order_lid)>0)
		$arOrderFilter["LID"] = trim($filter_order_lid);
	if (isset($filter_order_status) && !is_array($filter_order_status) && strlen($filter_order_status) > 0)
		$filter_order_status = array($filter_order_status);
	if (isset($filter_order_status) && is_array($filter_order_status) && count($filter_order_status) > 0)
	{
		$filterOrderCount = count($filter_order_status);
		for ($i = 0; $i < $filterOrderCount; $i++)
		{
			$filter_order_status[$i] = Trim($filter_order_status[$i]);
			if (strlen($filter_order_status[$i]) > 0)
				$arOrderFilter["STATUS_ID"][] = $filter_order_status[$i];
		}
	}
	if (strlen($filter_order_payed)>0)
		$arOrderFilter["PAYED"] = Trim($filter_order_payed);

	if (strlen($filter_order_delivery)>0)
		$arOrderFilter["ALLOW_DELIVERY"] = Trim($filter_order_delivery);

	if (strlen($filter_date_order_from)>0)
	{
		$dateFrom = MkDateTime(FmtDate($filter_date_order_from,"D.M.Y"),"d.m.Y");

		if ($dateFrom)
			$arOrderFilter["DATE_FROM"] = Trim($filter_date_order_from);
	}

	if (strlen($filter_date_order_to) > 0)
	{
		if ($arDate = ParseDateTime($filter_date_order_to, CSite::GetDateFormat("FULL", SITE_ID)))
		{
			if (StrLen($filter_date_order_to) < 11)
			{
				$arDate["HH"] = 23;
				$arDate["MI"] = 59;
				$arDate["SS"] = 59;
			}

			$filter_date_order_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
			$arOrderFilter["DATE_TO"] = $filter_date_order_to;
		}
		else
			$filter_date_order_to = "";
	}

	if(strlen(trim($filter_date_order_from_DAYS_TO_BACK))>0)
	{
		$dateBack = IntVal($filter_date_order_from_DAYS_TO_BACK);
		$arOrderFilter["DATE_FROM"] = ConvertTimeStamp(AddToTimeStamp(array("DD" => "-".$dateBack), mktime(0, 0, 0, date("n"), date("j"), date("Y"))), "SHORT");
	}

	if (strlen($filter_order_date_up_from)>0)
	{
		$dateFrom = MkDateTime(FmtDate($filter_order_date_up_from,"D.M.Y"),"d.m.Y");

		if ($dateFrom)
			$arOrderFilter["DATE_UPDATE_FROM"] = trim($filter_order_date_up_from);
	}
	if (strlen($filter_order_date_up_to) > 0)
	{
		if ($arDate = ParseDateTime($filter_order_date_up_to, CSite::GetDateFormat("FULL", SITE_ID)))
		{
			if (StrLen($filter_order_date_up_to) < 11)
			{
				$arDate["HH"] = 23;
				$arDate["MI"] = 59;
				$arDate["SS"] = 59;
			}

			$filter_order_date_up_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
			$arOrderFilter["DATE_UPDATE_TO"] = $filter_order_date_up_to;
		}
		else
			$filter_order_date_up_to = "";
	}

	if (strlen($filter_summa_from) > 0)
	{
		$arOrderFilter[">=PRICE"] = FloatVal($filter_summa_from);
	}
	if (strlen($filter_summa_to) > 0)
	{
		$arOrderFilter["<=PRICE"] = FloatVal($filter_summa_to);
	}
	if (strlen($filter_order_prod_name) > 0)
	{
		$arOrderFilter["%BASKET_NAME"] = $filter_order_prod_name;
	}

	$dbOrderList = CSaleOrder::GetList(
		$arOrderSort,
		$arOrderFilter,
		false,
		false,
		array("ID", "LID", "STATUS_ID", "DATE_STATUS", "PAYED", "DATE_PAYED", "PRICE", "DATE_UPDATE", "DATE_INSERT", "CURRENCY", "ALLOW_DELIVERY", "DATE_ALLOW_DELIVERY")
	);

	$dbOrderList = new CAdminResult($dbOrderList, $sTableID_tab3);
	$dbOrderList->NavStart();
	$lAdmin_tab3->NavText($dbOrderList->GetNavPrint(GetMessage('BUYER_ORDER_LIST')));

	$orderHeader = array(
		array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
		array("id"=>"STATUS_ID","content"=>GetMessage("BUYERS_H_STATUS"), "sort"=>"STATUS_ID", "default"=>true),
		array("id"=>"PAYED", "content"=>GetMessage("BUYERS_H_PAID"), "sort"=>"PAYED", "default"=>true),
		array("id"=>"ALLOW_DELIVERY", "content"=>GetMessage("BUYER_LAST_H_ALLOW_DELIVERY"), "sort"=>"", "default"=>true),
		array("id"=>"PRODUCT", "content"=>GetMessage("BUYERS_H_ALL_PRODUCT"), "sort"=>"", "default"=>true),
		array("id"=>"PRICE", "content"=>GetMessage("BUYERS_H_SUM"), "sort"=>"PRICE", "default"=>true),
		array("id"=>"DATE_INSERT", "content"=>GetMessage("BUYERS_H_DATE_INSERT"), "sort"=>"DATE_INSERT", "default"=>true),
	);

	if (count($arSites) > 1)
		$orderHeader[] = array("id"=>"LID", "content"=>GetMessage("BUYERS_H_SITE"), "sort"=>"LID", "default"=>true);

	$lAdmin_tab3->AddHeaders($orderHeader);

	while ($arOrder = $dbOrderList->Fetch())
	{
		$row =& $lAdmin_tab3->AddRow($arOrder["ID"], $arOrder, "sale_order_detail.php?ID=".$arOrder["ID"]."&lang=".LANG, GetMessage("BUYERS_ORDER_EDIT"));

		$orderLink = "<a href=\"sale_order_detail.php?ID=".$arOrder["ID"]."&lang=".LANG."\">".$arOrder["ID"]."</a>";
		$row->AddField("ID", $orderLink);

		$status_id = "<a title=\"".GetMessage('BUYERS_ORDER_DETAIL_PAGE')."\" href=\"/bitrix/admin/sale_order_detail.php?ID=".$arOrder["ID"]."&lang=".LANG."\">".GetMessage('BUYERS_PREF').$arOrder["ID"]."</a>";
		$status_id .= "<input type=\"hidden\" name=\"table_id\" value=\"".$sTableID_tab3."\">";
		$row->AddField("STATUS_ID", $status_id);

		$payed = (($arOrder["PAYED"] == "Y") ? GetMessage("BUYERS_PAY_YES") : GetMessage("BUYERS_PAY_NO"));
		if (strlen($arOrder["DATE_PAYED"]) > 0)
			$payed .= "<br>".$arOrder["DATE_PAYED"];
		$row->AddField("PAYED", $payed);

		$allowDelivery = (($arOrder["ALLOW_DELIVERY"] == "Y") ? GetMessage("BUYERS_PAY_YES") : GetMessage("BUYERS_PAY_NO"));
		if (strlen($arOrder["DATE_ALLOW_DELIVERY"]) > 0)
			$allowDelivery .= "<br>".$arOrder["DATE_ALLOW_DELIVERY"];
		$row->AddField("ALLOW_DELIVERY", $allowDelivery);

		$status = "[".$arOrder["STATUS_ID"]."] ".htmlspecialcharsbx($arStatusOrder[$arOrder["STATUS_ID"]]["NAME"])."<br />".$arOrder["DATE_STATUS"];
		$row->AddField("STATUS_ID", $status);

		$dbBasketList = CSaleBasket::GetList(array("SET_PARENT_ID" => "DESC", "TYPE" => "DESC"), array("ORDER_ID" => $arOrder["ID"]));
		$orderProduct = "";
		$arBasketItems = array();
		while ($arBasketOrder = $dbBasketList->Fetch())
			$arBasketItems[] = $arBasketOrder;

		$arBasketItems = getMeasures($arBasketItems);

		foreach ($arBasketItems as $arBasketOrder)
		{
			$measure = isset($arBasketOrder["MEASURE_TEXT"]) ? $arBasketOrder["MEASURE_TEXT"] : GetMessage("BUYERS_UNIT");

			$class = "";
			$hidden = "";
			if (CSaleBasketHelper::isSetItem($arBasketOrder))
			{
				$class = "class=\"set_item_".$arBasketOrder["SET_PARENT_ID"]."\"";
				$hidden = "style=\"display:none\"";
			}

			$orderProduct .= "<div ".$class." ".$hidden."><a href=\"".htmlspecialcharsbx($arBasketOrder["DETAIL_PAGE_URL"])."\">".htmlspecialcharsbx($arBasketOrder["NAME"])."</a> - ".$arBasketOrder["QUANTITY"]." ".$measure."<br />";

			$dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arBasketOrder["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
			while($arProp = $dbProp -> GetNext())
			{
				$orderProduct .= "<div><small>".$arProp["NAME"].": ".$arProp["VALUE"]."</small></div>";
			}

			if (CSaleBasketHelper::isSetParent($arBasketOrder))
				$orderProduct .= "<a href=\"javascript:void(0);\" class=\"dashed-link show-set-link\" id=\"set_toggle_link_".$arBasketOrder["SET_PARENT_ID"]."\" onclick=\"fToggleSetItems(".$arBasketOrder["ID"].", 'set_toggle_link_');\">".GetMessage("BUYER_F_SHOW_SET")."</a>";

			$orderProduct .= "</div>";
		}

		$row->AddField("PRODUCT", $orderProduct);
		$row->AddField("PRICE", SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"]));

		if (count($arSites) > 1)
			$row->AddField("LID", "[".$arOrder["LID"]."] ".htmlspecialcharsbx($arSites[$arOrder["LID"]]["NAME"])."");

		$arActions = array();
		$arActions[] = array("ICON"=>"view", "TEXT"=>GetMessage("BUYERS_ORDER_EDIT"),"ACTION"=>$lAdmin_tab3->ActionRedirect("sale_order_detail.php?ID=".$arOrder["ID"]."&lang=".LANG), "DEFAULT"=>true);
		$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("BUYER_PD_REORDER"),"ACTION"=>$lAdmin_tab3->ActionRedirect("/bitrix/admin/sale_buyers_profile.php?USER_ID=".$ID."&lang=".LANG."&reorder=".$arOrder["ID"]."&lid=".$arOrder["LID"]));

		$row->AddActions($arActions);
	}

	if($_REQUEST["table_id"]==$sTableID_tab3)
		$lAdmin_tab3->CheckListMode();
	//END BUYERS ORDER


	//BUYERS BASKET
	$sTableID_tab4 = "t_stat_list_tab4";
	$oSort_tab4 = new CAdminSorting($sTableID_tab4);
	$lAdmin_tab4 = new CAdminList($sTableID_tab4, $oSort_tab4);

	//FILTER BASKET
	$arFilterFields = array(
		"basket_status",
		"filter_basket_lid",
		"basket_name_product",
	);
	$lAdmin_tab4->InitFilter($arFilterFields);

	if (!isset($_REQUEST["by"]))
		$arBasketSort = array("DATE_INSERT" => "DESC", "LID" => "ASC");
	else
		$arBasketSort[$by] = $order;

	$arBasketFilter = array("USER_ID" => $ID, "ORDER_ID" => "NULL");

	if (strlen($filter_basket_lid)>0)
		$arBasketFilter["LID"] = trim($filter_basket_lid);

	if (strlen(trim($basket_status)) > 0)
	{
		if ($basket_status == "avail")
		{
			$arBasketFilter["CAN_BUY"] = "Y";
			$arBasketFilter["DELAY"] = "N";
		}
		elseif ($basket_status == "no")
		{
			$arBasketFilter["CAN_BUY"] = "N";
			$arBasketFilter["SUBSCRIBE"] = "N";
		}
		elseif ($basket_status == "delay")
		{
			$arBasketFilter["CAN_BUY"] = "Y";
			$arBasketFilter["DELAY"] = "Y";
		}
		elseif ($basket_status == "subscribe")
		{
			$arBasketFilter["CAN_BUY"] = "N";
			$arBasketFilter["SUBSCRIBE"] = "Y";
		}
	}
	if (strlen(trim($basket_name_product)) > 0)
		$arBasketFilter["%NAME"] = $basket_name_product;

	CAdminMessage::ShowNote($basketMessage);
	CAdminMessage::ShowMessage($basketError);

	//update price
	$arCacheFuser = array();
	$arUpdateFilter = $arBasketFilter;
	$arUpdateFilter["!CALLBACK_FUNC"] = '';
	$dbBasketList = CSaleBasket::GetList(
			$arBasketSort,
			$arUpdateFilter,
			false,
			false,
			array('FUSER_ID', 'LID')
	);
	while ($arBasket = $dbBasketList->Fetch())
	{
		if (!in_array($arBasket["FUSER_ID"], $arCacheFuser))
		{
			$arCacheFuser[] = $arBasket["FUSER_ID"];
			CSaleBasket::UpdateBasketPrices($arBasket["FUSER_ID"], $arBasket["LID"]);
		}
	}

	$dbBasketList = CSaleBasket::GetList(
			array_merge(array("SET_PARENT_ID" => "DESC", "TYPE" => "DESC"), $arBasketSort),
			$arBasketFilter,
			false,
			false,
			array('*')
	);

	$dbBasketList = new CAdminResult($dbBasketList, $sTableID_tab4);
	$dbBasketList->NavStart();
	$lAdmin_tab4->NavText($dbBasketList->GetNavPrint(GetMessage('BUYER_BASKET_BASKET')));

	$BasketHeader = array(
		array("id"=>"DATE_INSERT", "content"=>GetMessage("BUYER_BH_DATE_INSERT"), "sort"=>"DATE_INSERT", "default"=>true),
		array("id"=>"NAME","content"=>GetMessage("BUYER_BH_NAME"), "sort"=>"NAME", "default"=>true),
		array("id"=>"DELAY", "content"=>GetMessage("BUYER_BH_DELAY"), "sort"=>"DELAY", "default"=>true),
		array("id"=>"PRICE", "content"=>GetMessage("BUYER_BH_PRICE"), "sort"=>"PRICE", "default"=>true),
		array("id"=>"QUANTITY", "content"=>GetMessage("BUYER_BH_QUANTITY"), "sort"=>"QUANTITY", "default"=>true),
	);
	if (count($arSites) > 1)
		$BasketHeader[] = array("id"=>"LID", "content"=>GetMessage("BUYER_BH_LID"), "sort"=>"LID", "default"=>true);

	$lAdmin_tab4->AddHeaders($BasketHeader);

	$arSetData = array();
	$arBasketData = array();
	while ($arBasket = $dbBasketList->GetNext())
	{
		if (CSaleBasketHelper::isSetItem($arBasket))
		{
			$arSetData[$arBasket["SET_PARENT_ID"]][] = $arBasket;
			continue;
		}

		$arBasketData[] = $arBasket;
	}

	foreach ($arBasketData as $arBasket)
	{
		$row =& $lAdmin_tab4->AddRow($arBasket["PRODUCT_ID"], $arBasket, '', '');

		$status = "";
		if($arBasket["DELAY"] == "N" && $arBasket["CAN_BUY"] == "Y")
			$status = GetMessage("BUYER_B_STATUS_ADD");
		if($arBasket["DELAY"] == "Y" && $arBasket["CAN_BUY"] == "Y")
			$status = GetMessage("BUYER_B_STATUS_DELAY");
		if($arBasket["CAN_BUY"] == "N" && $arBasket["SUBSCRIBE"] == "N")
			$status = GetMessage("BUYER_B_STATUS_NO");
		if($arBasket["CAN_BUY"] == "N" && $arBasket["SUBSCRIBE"] == "Y")
			$status = GetMessage("BUYER_B_STATUS_NOTIFY");
		$row->AddField("DELAY", $status);

		$name = "<a href=\"".$arBasket["DETAIL_PAGE_URL"]."\">".$arBasket["NAME"]."</a>
			<input type=\"hidden\" value=\"".$arBasket["PRODUCT_ID"]."\" name=\"PRODUCT_ID[".$arBasket["LID"]."][]\" />";
		$name .= "<input type=\"hidden\" name=\"table_id\" value=\"".$sTableID_tab4."\">";

		$dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arBasket["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
		while($arProp = $dbProp -> GetNext())
		{
			$name .= "<div><small>".$arProp["NAME"].": ".$arProp["VALUE"]."</small></div>";
		}

		if (CSaleBasketHelper::isSetParent($arBasket))
		{
			$name .= "<br/><a href=\"javascript:void(0);\" class=\"dashed-link show-set-link\" id=\"set_toggle_link_b2".$arBasket["SET_PARENT_ID"]."\" onclick=\"fToggleSetItems('b2".$arBasket["ID"]."', 'set_toggle_link_');\">".GetMessage("BUYER_F_SHOW_SET")."</a><br/>";

			if (!empty($arSetData) && array_key_exists($arBasket["ID"], $arSetData))
			{
				$name .= "<div class=\"set_item_b2".$arBasket["ID"]."\" style=\"display:none\">";
				foreach ($arSetData[$arBasket["ID"]] as $set)
					$name .= "<p style=\"display:inline; font-style:italic\">".$set["NAME"]."</p><br/>";
				$name .= "</div>";
			}
		}

		$row->AddField("NAME", $name);
		$row->AddField("PRICE", SaleFormatCurrency($arBasket["PRICE"], $arBasket["CURRENCY"]));

		if (count($arSites) > 1)
			$row->AddField("LID", "[".$arBasket["LID"]."] ".htmlspecialcharsbx($arSites[$arBasket["LID"]]["NAME"])."");
	}

	$lAdmin_tab4->AddGroupActionTable(
		array(
			"order_basket" => GetMessage("BUYER_PD_ORDER"),
			"delay_y" => GetMessage("BUYER_PD_DELAY_Y"),
			"delay_n" => GetMessage("BUYER_PD_DELAY_N"),
			"delete_basket" => GetMessage("BUYER_PD_DELETE"),
		)
	);
	if($_REQUEST["table_id"]==$sTableID_tab4)
		$lAdmin_tab4->CheckListMode();
	//END BUYERS BASKET


	//BUYERS VIEWED PRODUCT
	$sTableID_tab5 = "t_stat_list_tab5";
	$oSort_tab5 = new CAdminSorting($sTableID_tab5);
	$lAdmin_tab5 = new CAdminList($sTableID_tab5, $oSort_tab5);

	//FILTER VIEWED
	$arFilterFields = array(
		"filter_viewed_date_visit",
		"filter_date_visit_from",
		"filter_date_visit_to",
		"filter_viewed_lid",
	);
	$lAdmin_tab5->InitFilter($arFilterFields);

	$arFilter = array();
	$arFuserItems = CSaleUser::GetList(array("USER_ID" => $ID));
	$arFilter["FUSER_ID"] = $arFuserItems["ID"];

	if (strlen($filter_viewed_lid)>0)
		$arFilter["LID"] = trim($filter_viewed_lid);

	if(strlen(trim($filter_date_visit_from))>0)
	{
		$arFilter["DATE_FROM"] = FmtDate($filter_date_visit_from,"D.M.Y");
	}
	if(strlen(trim($filter_date_visit_to))>0)
	{
		if ($arDate = ParseDateTime($filter_date_visit_to, CSite::GetDateFormat("FULL", SITE_ID)))
		{
			if (StrLen($filter_date_visit_to) < 11)
			{
				$arDate["HH"] = 23;
				$arDate["MI"] = 59;
				$arDate["SS"] = 59;
			}

			$filter_date_visit_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
			$arFilter["DATE_TO"] = $filter_date_visit_to;
		}
		else
		{
			$filter_date_visit_to = "";
		}
	}

	if(strlen(trim($filter_date_visit_from_DAYS_TO_BACK))>0)
	{
		$dateBack = IntVal($filter_date_visit_from_DAYS_TO_BACK);
		$arFilter["DATE_FROM"] = ConvertTimeStamp(AddToTimeStamp(array("DD" => "-".$dateBack), mktime(0, 0, 0, date("n"), date("j"), date("Y"))), "SHORT");
	}

	CAdminMessage::ShowNote($viewedMessage);
	CAdminMessage::ShowMessage($viewedError);


	$newFilter = array();
	foreach($arFilter as $key => $value)
	{
		if($key == "DATE_FROM")
		{
			$newFilter['>=DATE_VISIT'] = $value;
		}
		elseif($key == "DATE_TO")
		{
			$newFilter['<DATE_VISIT'] = $value;
		}
		else
		{
			$newFilter[$key] = $value;
		}
	}

	if(\Bitrix\Main\Loader::includeModule("catalog"))
	{
		$viewedQuery = new \Bitrix\Main\Entity\Query(\Bitrix\Catalog\CatalogViewedProductTable::getEntity());
		$viewedQuery->setSelect(array(
			"PRODUCT_ID",
			"DATE_VISIT",
			"SITE_ID",
			"VIEW_COUNT",
			"NAME" => "ELEMENT.NAME",
			"PRICE" => "PRODUCT.PRICE",
			"QUANTITY" => "PRODUCT.QUANTITY",
			"CURRENCY" => "PRODUCT.CURRENCY",
			"RATE" => "PRODUCT.CURRENT_CURRENCY_RATE",
			"CURRENCY_RATE" => "PRODUCT.CURRENT_CURRENCY_RATE_CNT"
		))->setfilter($newFilter);
		$viewedIterator = $viewedQuery->exec();
	}
	else
		$viewedIterator = new CDBResult();



	$dbViewsList = new CAdminResult($viewedIterator, $sTableID_tab5);
	$dbViewsList->NavStart();
	$lAdmin_tab5->NavText($dbViewsList->GetNavPrint(GetMessage('BUYER_PRODUCT_LIST')));

	$viewedHeader = array(
		array("id"=>"DATE_VISIT", "content"=>GetMessage("BUYER_V_DATE_INSERT"), "sort"=>"DATE_VISIT", "default"=>true),
		array("id"=>"NAME","content"=>GetMessage("BUYER_V_NAME"), "sort"=>"NAME", "default"=>true),
		array("id"=>"PRICE", "content"=>GetMessage("BUYER_V_PRICE"), "sort"=>"PRICE", "default"=>true),
		array("id"=>"QUANTITY", "content"=>GetMessage("BUYER_V_QUANTITY"), "sort"=>"", "default"=>true),
	);

	if (count($arSites) > 1)
		$viewedHeader[] = array("id"=>"SITE_ID", "content"=>GetMessage("BUYER_V_LID"), "sort"=>"SITE_ID", "default"=>true);

	$lAdmin_tab5->AddHeaders($viewedHeader);

	$arProductId = array();
	$arCatalogProductId = array();
	$arViewsData = array();
	$mapViewByProduct = array();
	$viewCount = 0;
	while ($arViews = $dbViewsList->Fetch())
	{
		$arViewsData[$viewCount] = $arViews;
		$elementID = (int)$arViews["PRODUCT_ID"];
		if (!isset($mapViewByProduct[$elementID]))
			$mapViewByProduct[$elementID] = array();
		$mapViewByProduct[$elementID][] = $viewCount;
		$arProductId[$arViews["PRODUCT_ID"]] = $arViews["PRODUCT_ID"];
		$arCatalogProductId[] = $arViews["PRODUCT_ID"];
		$viewCount++;
	}
	unset($arViews, $dbViewsList, $viewCount);

	// Get product name
	if (!empty($arCatalogProductId))
	{
		$elementIterator = CIblockElement::GetList(
			array(),
			array("ID" => $arCatalogProductId),
			false,
			false,
			array('ID', 'IBLOCK_ID', 'DETAIL_PAGE_URL')
		);
		while($element = $elementIterator->GetNext())
		{
			$elementID = (int)$element['ID'];
			if (isset($mapViewByProduct[$elementID]) && !empty($mapViewByProduct[$elementID]))
			{
				foreach ($mapViewByProduct[$elementID] as &$viewCount)
				{
					$arViewsData[$viewCount]['DETAIL_PAGE_URL'] = $element['~DETAIL_PAGE_URL'];
				}
				unset($viewCount);
			}
		}
		unset($element, $elementIterator);
	}

	// collect iblock info about all products in the static class member. Will be used in the CSaleProduct::GetProductSku
	if (!empty($arProductId))
		CSaleProduct::GetProductListIblockInfo($arProductId);
	// Resort items by params
	if (!isset($_REQUEST["by"]))
		$arViewSort = array("DATE_VISIT" => SORT_DESC);
	else
		$arViewSort[$_REQUEST["by"]] = ($_REQUEST["order"] == "asc" ? SORT_ASC : SORT_DESC);

	Bitrix\Main\Type\Collection::sortByColumn($arViewsData, $arViewSort);

	foreach ($arViewsData as $arViews)
	{
		$row =& $lAdmin_tab5->AddRow($arViews["PRODUCT_ID"], $arViews, '', '');

		$name = "[".$arViews["PRODUCT_ID"]."] <a href=\"".$arViews["DETAIL_PAGE_URL"]."\">".$arViews["NAME"]."</a>";
		if (floatVal($arViews["PRICE"]) <= 0)
			$name .= "<div class=\"dont_can_buy\">(".GetMessage('BUYER_DONT_CAN_BUY').")</div>";
		$name .= "<input type=\"hidden\" name=\"table_id\" value=\"".$sTableID_tab5."\">";

		// get set items
		/** @var $productProvider IBXSaleProductProvider */
		if ($productProvider = CSaleBasket::GetProductProvider($arViews))
		{
			if (method_exists($productProvider, "GetSetItems"))
			{
				$arSets = $productProvider::GetSetItems($arViews["PRODUCT_ID"], CSaleBasket::TYPE_SET);

				if (!empty($arSets))
				{
					$name .= "<br/><a href=\"javascript:void(0);\" class=\"dashed-link show-set-link\" id=\"set_toggle_link_b3".$arBasket["SET_PARENT_ID"]."\" onclick=\"fToggleSetItems('b3".$arBasket["ID"]."', 'set_toggle_link_');\">".GetMessage("BUYER_F_SHOW_SET")."</a><br/>";

					$name .= "<div class=\"set_item_b3".$arBasket["ID"]."\" style=\"display:none\">";
					foreach ($arSets as $arSetData)
					{
						foreach ($arSetData["ITEMS"] as $setItem)
						{
							$name .= "<br/>[".$setItem["ITEM_ID"]."] <a style=\"font-style: italic\" href=".$setItem["DETAIL_PAGE_URL"].">".$setItem["NAME"]."</a>";
						}
					}
					$name .= "</div>";
				}
			}
		}

		$row->AddField("NAME", $name);

		$QUANTITY = "&nbsp;";
		if (isset($arViews["QUANTITY"]))
			$QUANTITY = $arViews["QUANTITY"];

		$row->AddField("QUANTITY", $QUANTITY);

		$price = "&nbsp;";
		if (floatval($arViews["PRICE"]) > 0)
			$price = SaleFormatCurrency($arViews["PRICE"], $arViews["CURRENCY"]);
		$row->AddField("PRICE", $price);


		if (count($arSites) > 1)
			$row->AddField("LID", "[".$arViews["LID"]."] ".htmlspecialcharsbx($arSites[$arViews["LID"]]["NAME"])."");

		$arResult = CSaleProduct::GetProductSku($ID, $arViews['SITE_ID'], $arViews["PRODUCT_ID"], $arViews["NAME"]);

		$arResult["POPUP_MESSAGE"] = array(
				"PRODUCT_ADD_TO_ORDER" => GetMessage('BUYER_PD_ORDER'),
				"PRODUCT_ADD_TO_BASKET" => GetMessage('BUYER_PD_DELAY_N'),
				"PRODUCT_NOT_TO_ORDER" => GetMessage('BUYER_DONT_CAN_BUY'),
				"PRODUCT_PRICE_FROM" => GetMessage('BUYERS_FROM')
			);

		if (count($arResult["SKU_ELEMENTS"]) > 0):
			$linkOrder = "showOfferPopup(".CUtil::PhpToJsObject($arResult['SKU_ELEMENTS']).", ".CUtil::PhpToJsObject($arResult['SKU_PROPERTIES']).", 'order', ".CUtil::PhpToJsObject($arResult["POPUP_MESSAGE"]).");";
			$linkBasket = "showOfferPopup(".CUtil::PhpToJsObject($arResult['SKU_ELEMENTS']).", ".CUtil::PhpToJsObject($arResult['SKU_PROPERTIES']).", 'basket', ".CUtil::PhpToJsObject($arResult["POPUP_MESSAGE"]).");";
		else:
			$linkOrder = 'BX.adminPanel.Redirect([], \'/bitrix/admin/sale_order_new.php?user_id='.$ID.'&lang='.LANG.'&LID='.$arViews["SITE_ID"].'&product['.$arViews["PRODUCT_ID"].']=1\', event);';
			$linkBasket = 'fAddToBasketViewed('.$arViews["PRODUCT_ID"].', \''.$arViews["SITE_ID"].'\')';
		endif;

		$arActions = array();
		$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("BUYER_PD_ORDER"), "ACTION"=>$linkOrder, "DEFAULT"=>true);
		$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("BUYER_PD_DELAY_N"), "ACTION"=>$linkBasket);

		if (floatval($arViews["PRICE"]) > 0)
			$row->AddActions($arActions);
	}

	if($_REQUEST["table_id"]==$sTableID_tab5)
		$lAdmin_tab5->CheckListMode();

	//END VIEWED


	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

	$siteLID = "";
	$arSiteMenu = array();
	$arSitesShop = array();
	$arSitesTmp = array();
	$rsSites = CSite::GetList($b="id", $o="asc", Array("ACTIVE" => "Y"));
	while ($arSite = $rsSites->Fetch())
	{
		$site = COption::GetOptionString("sale", "SHOP_SITE_".$arSite["ID"], "");
		if ($arSite["ID"] == $site)
			$arSitesShop[] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);

		$arSitesTmp[] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);
	}

	$rsCount = count($arSitesShop);
	if ($rsCount <= 0)
	{
		$arSitesShop = $arSitesTmp;
		$rsCount = count($arSitesShop);
	}

	if ($rsCount === 1)
		$siteLID = "&LID=".$arSitesShop[0]["ID"]."&user_id=".$ID;
	else
	{
		foreach ($arSitesShop as $key => $val)
		{
			$arSiteMenu[] = array(
				"TEXT" => $val["NAME"]." (".$val["ID"].")",
				"ACTION" => "window.location = 'sale_order_new.php?lang=".LANGUAGE_ID."&user_id=".$ID."LID=".$val["ID"]."';"
			);
		}
	}

	$arMenu = array(
		array(
			"TEXT"=>GetMessage("BUYER_LIST"),
			"LINK" => "/bitrix/admin/sale_buyers.php?lang=".LANGUAGE_ID.GetFilterParams("filter_"),
			"ICON" => "btn_list",
		),
		array(
			"TEXT"=>GetMessage("BUYER_NEW_ORDER"),
			"LINK" => "/bitrix/admin/sale_order_new.php?lang=".LANGUAGE_ID.$siteLID,
			"TITLE"=>GetMessage("BUYER_NEW_ORDER"),
			"ICON" => "btn_new",
			"MENU" => $arSiteMenu
		),
	);

	$context = new CAdminContextMenu($arMenu);
	$context->Show();


	$aTabs = array(
		array(
			"DIV" => "tab1",
			"TAB" => GetMessage("BUYER_INFO"),
			"ICON"=>"",
			"TITLE"=>GetMessage("BUYER_INFO_DESC"),
		),
		array(
			"DIV" => "tab2",
			"TAB" => GetMessage("BUYER_PROFILE"),
			"ICON"=>"",
			"TITLE"=>GetMessage("BUYER_PROFILE_DESC"),
		),
		array(
			"DIV" => "tab3",
			"TAB" => GetMessage("BUYER_ORDER"),
			"ICON"=>"",
			"TITLE"=>GetMessage("BUYER_ORDER_DESC"),
		),
		array(
			"DIV" => "tab4",
			"TAB" => GetMessage("BUYER_BASKET"),
			"ICON"=>"",
			"TITLE"=>GetMessage("BUYER_BASKET_DESC"),
		),
		array(
			"DIV" => "tab5",
			"TAB" => GetMessage("BUYER_LOOKED"),
			"ICON"=>"",
			"TITLE"=>GetMessage("BUYER_LOOKED_DESC"),
		),

	);

	$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
	$tabControl->Begin();
	?>

	<br />
		<?$tabControl->BeginNextTab();?>
		<tr>
			<td colspan="2">
				<table border="0" cellspacing="0" cellpadding="0" width="100%" class="adm-detail-content-table edit-table">
				<tr class="heading">
					<td colspan="2"><?=GetMessage("BUYER_ALL_INFO")?></td>
				</tr>
				<tr>
					<td class="adm-detail-content-cell-l" width="40%"><?=GetMessage("BUYER_FILED_LOGIN")?>:</td>
					<td class="adm-detail-content-cell-r">
						<div><a href="/bitrix/admin/user_edit.php?ID=<?=$u_ID?>&lang=<?=LANG?>"><?=$u_LOGIN?></a></div>
					</td>
				</tr>
				<?if(strlen($userFIO) > 0):?>
					<tr>
						<td class="adm-detail-content-cell-l"><?=GetMessage("BUYER_FILED_FIO")?>:</td>
						<td class="adm-detail-content-cell-r">
							<div><?=$userFIO?></div>
						</td>
					</tr>
				<?endif;?>
				<tr>
					<td class="adm-detail-content-cell-l"><?=GetMessage("BUYER_FILED_MAIL")?>:</td>
					<td class="adm-detail-content-cell-r">
						<div><a href="mailto:<?=$u_EMAIL?>"><?=$u_EMAIL?></a></div>
					</td>
				</tr>
				<?if(strlen($u_PERSONAL_PHONE) > 0):?>
					<tr>
						<td class="adm-detail-content-cell-l"><?=GetMessage("BUYER_FILED_PHONE")?>:</td>
						<td class="adm-detail-content-cell-r">
							<div><a href="callto:<?=$u_PERSONAL_PHONE?>"><?=$u_PERSONAL_PHONE?></a></div>
						</td>
					</tr>
				<?endif;?>
				<?if(strlen($u_PERSONAL_MOBILE) > 0):?>
					<tr>
						<td class="adm-detail-content-cell-l"><?=GetMessage("BUYER_FILED_PHONE_M")?>:</td>
						<td class="adm-detail-content-cell-r">
							<div><a href="callto:<?=$u_PERSONAL_MOBILE?>"><?=$u_PERSONAL_MOBILE?></a></div>
						</td>
					</tr>
				<?endif;?>
				<tr>
					<td class="adm-detail-content-cell-l"><?=GetMessage("BUYER_FILED_REG")?>:</td>
					<td class="adm-detail-content-cell-r">
						<div><?=$u_DATE_REGISTER?></div>
					</td>
				</tr>
				<tr>
					<td class="adm-detail-content-cell-l"><?=GetMessage("BUYER_FILED_LAST_LOGIN")?>:</td>
					<td class="adm-detail-content-cell-r">
						<div><?=$u_LAST_LOGIN?></div>
					</td>
				</tr>
				<tr>
					<td class="adm-detail-content-cell-l" valign="top"><?=GetMessage("BUYER_FILED_GROUP")?>:</td>
					<td class="adm-detail-content-cell-r">
						<div><?=$strUserGroup?></div>
					</td>
				</tr>
				<?if (strlen($userAdres) > 0):?>
				<tr>
					<td class="adm-detail-content-cell-l" valign="top"><?=GetMessage("BUYER_FILED_ADRES")?>:</td>
					<td class="adm-detail-content-cell-r">
						<div><?=$userAdres?></div>
					</td>
				</tr>
				<?endif;?>

				<?
				$arStatOrder = array();
				$arStatOrder["PAYED"] = array();
				$arStatOrder["ALL"] = array();

				$arStatAllSites = array();
				$arFilter = array("USER_ID" => $ID);
				$dbOrderStat = CSaleOrder::GetList(
					array("LID" => "ASC"),
					$arFilter,
					array("LID"),
					false,
					array("LID")
				);
				while ($arStat = $dbOrderStat->Fetch())
					$arStatAllSites[$arStat["LID"]] = $arStat["CNT"];

				$statSummary = "";
				$arFilter = array("USER_ID" => $ID, "PAYED" => "Y");
				$dbOrderStat = CSaleOrder::GetList(
					array("CURRENCY" => "ASC", "LID" => "ASC"),
					$arFilter,
					array("LID", "CURRENCY", "SUM" => "PRICE"),
					false,
					array("LID", "CURRENCY", "SUM" => "PRICE")
				);
				while ($arStat = $dbOrderStat->Fetch())
				{
					$statSummary .= "<tr>";
					$statSummary .= "<td colspan=\"2\" align=\"center\" style=\"text-align:center;font-weight:bold;font-size:14px;color:rgb(75, 98, 103);\">".htmlspecialcharsbx($arSites[$arStat["LID"]]["NAME"])."</td>";
					$statSummary .= "</tr>";

					$statSummary .= "<tr>";
					$statSummary .= "<td class=\"adm-detail-content-cell-l\" width=\"40%\">".GetMessage("BUYER_FILED_ORDER_COUNT").":</td>";
					$statSummary .= "<td class=\"adm-detail-content-cell-r\">";
					$statSummary .= "<div>".$arStat["CNT"]." / ".$arStatAllSites[$arStat["LID"]]."</div>";
					$statSummary .= "</td>";
					$statSummary .= "</tr>";

					$statSummary .= "<tr>";
					$statSummary .= "<td class=\"adm-detail-content-cell-l\">".GetMessage("BUYER_FILED_ORDER_SUM").":</td>";
					$statSummary .= "<td class=\"adm-detail-content-cell-r\">";
					$statSummary .= "<div>".SaleFormatCurrency($arStat["PRICE"], $arStat["CURRENCY"])."</div>";
					$statSummary .= "</td>";
					$statSummary .= "</tr>";

					$userOrderAvePayed = 0;
					if ($arStat["CNT"] > 0)
						$userOrderAvePayed = roundEx(($arStat["PRICE"] / $arStat["CNT"]), SALE_VALUE_PRECISION);

					$statSummary .= "<tr>";
					$statSummary .= "<td class=\"adm-detail-content-cell-l\">".GetMessage("BUYER_FILED_ORDER_AVE").":</td>";
					$statSummary .= "<td class=\"adm-detail-content-cell-r\">";
					$statSummary .= "<div>".SaleFormatCurrency($userOrderAvePayed, $arStat["CURRENCY"])."</div>";
					$statSummary .= "</td>";
					$statSummary .= "</tr>";
				}
				if ($statSummary != '')
				{
					echo "<tr class=\"heading\">
						<td colspan=\"2\">".GetMessage('BUYER_G_STATISTIC')."</td>
					</tr>";
					echo $statSummary;
				}

				$arFilter = array("USER_ID" => $ID);
				?>
				<tr class="heading">
					<td colspan="2"><?=GetMessage("BUYER_G_LAST_ORDER")?></td>
				</tr>
				</table>

				<?$lAdmin_tab1->DisplayList();?>
			</td>
		</tr>
		<?$tabControl->EndTab();?>

		<?$tabControl->BeginNextTab();?>
		<tr>
			<td colspan="2">
				<?$lAdmin_tab2->DisplayList(array("FIX_HEADER" => false, "FIX_FOOTER" => false));?>
			</td>
		</tr>
		<?$tabControl->EndTab();?>

		<?$tabControl->BeginNextTab();?>
		<tr>
			<td colspan="2">
				<form name="find_form3" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
					<input type="hidden" name="USER_ID" value="<?=$ID?>">
					<?
					$arFilterFieldsTmp = array(
						GetMessage("BUYER_F_DATE_UPDATE"),
						GetMessage("BUYER_F_LID"),
						GetMessage("BUYER_F_STATUS"),
						GetMessage("BUYER_F_PAYED"),
						GetMessage("BUYER_F_DELIVERY"),
						GetMessage("BUYER_F_PRICE"),
						GetMessage("BUYER_F_NAME_PRODUCT"),
					);
					$oFilter = new CAdminFilter(
						$sTableID_tab3."_filter",
						$arFilterFieldsTmp
					);
					$oFilter->Begin();

					$selectLID = "<select name=\"filter_order_lid\">";
					$selectLID .= "<option value=\"\">(".GetMessage('BUYER_VIEW_F_ALL').")</option>";
					foreach ($arSites as $arSite)
					{
						$selected = "";
						if ($arSite["ID"] == $filter_order_lid)
							$selected = "selected";
						$selectLID .= "<option value=\"".$arSite["ID"]."\" ".$selected." >".htmlspecialcharsbx("[".$arSite["ID"]."]".$arSite["NAME"])."</option>";
					}
					$selectLID .= "</select>";
					?>
					<tr>
						<td><?echo GetMessage("BUYER_F_DATE_INSERT");?>:</td>
						<td>
							<?echo CalendarPeriod("filter_date_order_from", $filter_date_order_from, "filter_date_order_to", $filter_date_order_to, "find_form3", "Y")?>
							<input type="hidden" name="USER_ID" value="<?=$ID?>" >
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("BUYER_F_DATE_UPDATE");?>:</td>
						<td>
							<?echo CalendarPeriod("filter_order_date_up_from", $filter_order_date_up_from, "filter_order_date_up_to", $filter_order_date_up_to, "find_form3", "Y")?>
						</td>
					</tr>
					<tr>
						<td><?=GetMessage('BUYER_VIEW_F_LID')?>:</td>
						<td>
							<?echo $selectLID?>
							<input type="hidden" name="USER_ID" value="<?=$ID?>" >
						</td>
					</tr>
					<tr>
						<td valign="top"><?echo GetMessage("BUYER_F_STATUS")?>:<br /><img src="/bitrix/images/sale/mouse.gif" width="44" height="21" border="0" alt=""></td>
						<td valign="top">
							<select name="filter_order_status[]" multiple size="3">
								<option value="">(<?=GetMessage('BUYER_BASKET_F_STATUS_ALL')?>)</option>
								<?
								$dbStatusList = CSaleStatus::GetList(
										array("SORT" => "ASC"),
										array("LID" => LANGUAGE_ID),
										false,
										false,
										array("ID", "NAME", "SORT")
									);
								while ($arStatusList = $dbStatusList->GetNext())
								{
									?><option value="<?=$arStatusList["ID"] ?>"<?if (is_array($filter_order_status) && in_array($arStatusList["ID"], $filter_order_status)) echo " selected"?>>[<?= $arStatusList["ID"] ?>] <?= $arStatusList["NAME"] ?></option><?
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("BUYER_F_PAYED")?>:</td>
						<td>
							<select name="filter_order_payed">
								<option value="">(<?echo GetMessage("BUYERS_PAY_ALL")?>)</option>
								<option value="Y"<?if ($filter_order_payed=="Y") echo " selected"?>><?echo GetMessage("BUYERS_PAY_YES")?></option>
								<option value="N"<?if ($filter_order_payed=="N") echo " selected"?>><?echo GetMessage("BUYERS_PAY_NO")?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("BUYER_F_DELIVERY")?>:</td>
						<td>
							<select name="filter_order_delivery">
								<option value="">(<?echo GetMessage("BUYERS_PAY_ALL")?>)</option>
								<option value="Y"<?if ($filter_order_delivery=="Y") echo " selected"?>><?echo GetMessage("BUYERS_PAY_YES")?></option>
								<option value="N"<?if ($filter_order_delivery=="N") echo " selected"?>><?echo GetMessage("BUYERS_PAY_NO")?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("BUYER_F_PRICE")?>:</td>
						<td>
							<span style="position:absolute;padding-top:5px;"><?=GetMessage('BUYER_F_PRICE_FROM');?></span>&nbsp;<input type="text" size="7" maxlength="10" name="filter_summa_from" value="<?=htmlspecialcharsbx($filter_summa_from)?>">&nbsp;
							<?=GetMessage('BUYER_F_PRICE_TO');?>&nbsp;<input type="text" size="7" name="filter_summa_to" maxlength="10" value="<?=htmlspecialcharsbx($filter_summa_to)?>">
						</td>
					</tr>
					<tr>
						<td><?echo GetMessage("BUYER_F_NAME_PRODUCT")?>:</td>
						<td>
							<? CUtil::DecodeUriComponent($filter_order_prod_name);?>
							<input type="text" name="filter_order_prod_name" value="<?=htmlspecialcharsbx($filter_order_prod_name)?>" size="42">
						</td>
					</tr>
					<?
					$oFilter->Buttons(
						array(
							"table_id" => $sTableID_tab3,
							"url" => $APPLICATION->GetCurPageParam(),
							"form" => "find_form3"
						)
					);
					$oFilter->End();?>
				</form>
				<?$lAdmin_tab3->DisplayList(array("FIX_HEADER" => false, "FIX_FOOTER" => false));?>
			</td>
		</tr>
		<?$tabControl->EndTab();?>

		<?$tabControl->BeginNextTab();?>
		<tr>
			<td colspan="2">
				<form name="find_form4" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
					<input type="hidden" name="USER_ID" value="<?=$ID?>">
					<?
					$arFilterFieldsTmp = array();
					if (count($arSites) > 1)
						$arFilterFieldsTmp[] = GetMessage('BUYER_BASKET_F_LID');
					$arFilterFieldsTmp[] = GetMessage('BUYER_BASKET_F_NAME');

					$oFilter4 = new CAdminFilter(
						$sTableID_tab4."_filter",
						$arFilterFieldsTmp
					);
					$oFilter4->Begin();
					?>
					<tr>
						<td><?=GetMessage('BUYER_BASKET_F_STATUS')?>:</td>
						<td>
							<select name="basket_status">
								<option value="">(<?=GetMessage('BUYER_BASKET_F_STATUS_ALL')?>)</option>
								<option value="avail" <?=($basket_status=="avail" ? 'selected' : '')?> ><?=GetMessage('BUYER_BASKET_F_STATUS_AVAIL')?></option>
								<option value="delay" <?=($basket_status=="delay" ? 'selected' : '')?>><?=GetMessage('BUYER_BASKET_F_STATUS_DELAY')?></option>
								<option value="no" <?=($basket_status=="no" ? 'selected' : '')?>><?=GetMessage('BUYER_BASKET_F_STATUS_NO')?></option>
								<option value="subscribe" <?=($basket_status=="subscribe" ? 'selected' : '')?>><?=GetMessage('BUYER_BASKET_F_STATUS_SUB')?></option>
							</select>
							<input type="hidden" name="USER_ID" value="<?=$ID?>" >
						</td>
					</tr>
					<?
					$selectLID = "<select name=\"filter_basket_lid\">";
					$selectLID .= "<option value=\"\">(".GetMessage('BUYER_VIEW_F_ALL').")</option>";
					foreach ($arSites as $arSite)
					{
						$selected = "";
						if ($arSite["ID"] == $filter_basket_lid)
							$selected = "selected";
						$selectLID .= "<option value=\"".$arSite["ID"]."\" ".$selected." >".htmlspecialcharsbx("[".$arSite["ID"]."]".$arSite["NAME"])."</option>";
					}
					$selectLID .= "</select>";

					if (count($arSites) > 1)
					{
					?>
					<tr>
						<td><?=GetMessage('BUYER_BASKET_F_LID')?>:</td>
						<td>
							<?echo $selectLID?>
						</td>
					</tr>
					<?
					}
					?>
					<tr>
						<td><?=GetMessage('BUYER_BASKET_F_NAME')?>:</td>
						<td>
							<? CUtil::DecodeUriComponent($basket_name_product);?>
							<input type="text" name="basket_name_product" size="48" value="<?=htmlspecialcharsbx($basket_name_product)?>" >
						</td>
					</tr>
					<?
					$oFilter4->Buttons(
						array(
							"table_id" => $sTableID_tab4,
							"url" => $APPLICATION->GetCurPageParam(),
							"form" => "find_form4"
						)
					);
					$oFilter4->End();?>
				</form>
				<?$lAdmin_tab4->DisplayList(array("FIX_HEADER" => false, "FIX_FOOTER" => false));?>
			</td>
		</tr>
		<?$tabControl->EndTab();?>

		<?$tabControl->BeginNextTab();?>
		<tr>
			<td colspan="2">
				<form name="find_form5" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
					<input type="hidden" name="USER_ID" value="<?=$ID?>">

					<?$arFilterFieldsTmp = array();
					if (count($arSites) > 1)
						$arFilterFieldsTmp[] = GetMessage('BUYER_VIEW_F_LID');

					$oFilter5 = new CAdminFilter(
						$sTableID_tab5."_filter",
						$arFilterFieldsTmp
					);
					$oFilter5->Begin();

					$selectLID = "<select name=\"filter_viewed_lid\">";
					$selectLID .= "<option value=\"\">(".GetMessage('BUYER_VIEW_F_ALL').")</option>";
					foreach ($arSites as $arSite)
					{
						$selected = "";
						if ($arSite["ID"] == $filter_viewed_lid)
							$selected = "selected";
						$selectLID .= "<option value=\"".$arSite["ID"]."\" ".$selected." >".htmlspecialcharsbx("[".$arSite["ID"]."]".$arSite["NAME"])."</option>";
					}
					$selectLID .= "</select>";
					?>
					<tr>
						<td><?=GetMessage('BUYER_VIEW_F_DATE')?>:</td>
						<td>
							<?echo CalendarPeriod("filter_date_visit_from", $filter_date_visit_from, "filter_date_visit_to", $filter_date_visit_to, "find_form5", "Y")?>
							<input type="hidden" name="USER_ID" value="<?=$ID?>" >
						</td>
					</tr>
					<?if (count($arSites) > 1):?>
					<tr>
						<td><?=GetMessage('BUYER_VIEW_F_LID')?>:</td>
						<td>
							<?echo $selectLID?>
						</td>
					</tr>
					<?endif?>
					<?
					$oFilter5->Buttons(
						array(
							"table_id" => $sTableID_tab5,
							"url" => $APPLICATION->GetCurPageParam(),
							"form" => "find_form5"
						)
					);
					$oFilter5->End();
					?>
					</form>
				<?$lAdmin_tab5->DisplayList(array("FIX_HEADER" => false, "FIX_FOOTER" => false));?>

				<div class="sale_popup_form" id="popup_form_sku_order" style="display:none;">
					<table width="100%">
						<tr><td></td></tr>
						<tr>
							<td><small><span id="listItemPrice"></span>&nbsp;<span id="listItemOldPrice"></span></small></td>
						</tr>
						<tr>
							<td><hr></td>
						</tr>
					</table>

					<table width="100%" id="sku_selectors_list">
						<tr>
							<td colspan="2"></td>
						</tr>
					</table>

					<span id="prod_order_button"></span>
					<input type="hidden" name="viewed_id_apply" id="viewed_id_apply" value="" >
					<input type="hidden" name="viewed_lid_apply" id="viewed_lid_apply" value="" >
					<input type="hidden" name="sku_to_basket_apply" id="sku_to_basket_apply" value="N" >
					<input type="hidden" name="viewed_url_action" id="viewed_url_action" value="" >
				</div>

				<script>
						var wind = new BX.PopupWindow('popup_sku', this, {
							offsetTop : 10,
							offsetLeft : 0,
							autoHide : true,
							closeByEsc : true,
							closeIcon : true,
							titleBar : true,
							draggable: {restrict:true},
							titleBar: {content: BX.create("span", {html: '', 'props': {'className': 'sale-popup-title-bar'}})},
							content : BX("popup_form_sku_order"),
							buttons: [
								new BX.PopupWindowButton({
									text : '<?=GetMessageJS('BUYER_CAN_BUY_NOT');?>',
									id : "popup_sku_save",
									events : {
										click : function() {
											if (BX('sku_to_basket_apply').value == "Y")
											{
												if (BX('viewed_id_apply').value.length > 0 && BX('viewed_lid_apply').value.length > 0)
												{
													fAddToBasketViewed(BX('viewed_id_apply').value, BX('viewed_lid_apply').value);
													wind.close();
												}
											}

											if (BX('viewed_url_action').value != '')
												jsUtils.Redirect([], BX('viewed_url_action').value);
										}
									}
								}),
								new BX.PopupWindowButton({
									text : '<?=GetMessageJS('BUYER_POPUP_CLOSE');?>',
									id : "popup_sku_cancel",
									events : {
										click : function() {
											wind.close();
										}
									}
								})
							]
						});

						function fAddToBasketViewed(product_id, lid)
						{
							t_stat_list_tab5.GetAdminList('/bitrix/admin/sale_buyers_profile.php?USER_ID=<?=$ID?>&lang=<?=LANGUAGE_ID?>&action=viewed_apply&viewed_id='+product_id+'&viewed_lid='+lid);
							t_stat_list_tab4.GetAdminList('/bitrix/admin/sale_buyers_profile.php?USER_ID=<?=$ID?>&lang=<?=LANGUAGE_ID?>');
						}

						function showOfferPopup(arSKU, arProperties, type, message)
						{
							BX.message(message);
							wind.show();
							buildSelect("sku_selectors_list", 0, arSKU, arProperties, type);
							var properties_num = arProperties.length;
							var lastPropCode = arProperties[properties_num-1].CODE;
							addHtml(lastPropCode, arSKU, type);
						}
						function buildSelect(cont_name, prop_num, arSKU, arProperties, type)
						{
							var properties_num = arProperties.length;
							var lastPropCode = arProperties[properties_num-1].CODE;

							for (var i = prop_num; i < properties_num; i++)
							{
								var q = BX('prop_' + i);
								if (q)
									q.parentNode.removeChild(q);
							}

							var select = BX.create('SELECT', {
								props: {
									name: arProperties[prop_num].CODE,
									id :  arProperties[prop_num].CODE
								},
								events: {
									change: (prop_num < properties_num-1)
										? function() {
											buildSelect(cont_name, prop_num + 1, arSKU, arProperties, type);
											if (this.value != "null")
												BX(arProperties[prop_num+1].CODE).disabled = false;
											addHtml(lastPropCode, arSKU, type);
										}
										: function() {
											if (this.value != "null")
												addHtml(lastPropCode, arSKU, type)
										}
								}
							});
							if (prop_num != 0) select.disabled = true;

							var ar = [];
							select.add(new Option(arProperties[prop_num].NAME, 'null'));

							for (var i = 0; i < arSKU.length; i++)
							{
								if (checkSKU(arSKU[i], prop_num, arProperties) && !BX.util.in_array(arSKU[i][prop_num], ar))
								{
									select.add(new Option(
											arSKU[i][prop_num],
											prop_num < properties_num-1 ? arSKU[i][prop_num] : arSKU[i]["ID"]
									));
									ar.push(arSKU[i][prop_num]);
								}
							}

							var cont = BX.create('tr', {
								props: {id: 'prop_' + prop_num},
								children:[
									BX.create('td', {html: arProperties[prop_num].NAME + ': '}),
									BX.create('td', { children:[
										select
									]}),
								]
							});

							var tmp = BX.findChild(BX(cont_name), {tagName:'tbody'}, false, false);

							tmp.appendChild(cont);

							if (prop_num < properties_num-1)
								buildSelect(cont_name, prop_num + 1, arSKU, arProperties, type);
						}

						function checkSKU(SKU, prop_num, arProperties)
						{
							for (var i = 0; i < prop_num; i++)
							{
								code = BX.findChild(BX('popup_sku'), {'attr': {name: arProperties[i].CODE}}, true, false).value;
								if (SKU[i] != code)
									return false;
							}
							return true;
						}

						function addHtml(lastPropCode, arSKU, type)
						{
							var selectedSkuId = BX(lastPropCode).value;
							var btnText = '';

							BX('popup-window-titlebar-popup_sku').innerHTML = '<span class="sale-popup-title-bar">'+arSKU[0]["PRODUCT_NAME"]+'</span>';
							BX("listItemPrice").innerHTML = BX.message('PRODUCT_PRICE_FROM')+arSKU[0]["MIN_PRICE"];
							BX("listItemOldPrice").innerHTML = '';

							for (var i = 0; i < arSKU.length; i++)
							{
								if (arSKU[i]["ID"] == selectedSkuId)
								{
									BX('popup-window-titlebar-popup_sku').innerHTML = '<span class="sale-popup-title-bar">'+arSKU[i]["NAME"]+'</span>';

									if (arSKU[i]["DISCOUNT_PRICE"] != "")
									{
										BX("listItemPrice").innerHTML = arSKU[i]["DISCOUNT_PRICE_FORMATED"]+" "+arSKU[i]["VALUTA_FORMAT"];
										BX("listItemOldPrice").innerHTML = arSKU[i]["PRICE_FORMATED"]+" "+arSKU[i]["VALUTA_FORMAT"];
										summaFormated = arSKU[i]["DISCOUNT_PRICE_FORMATED"];
										price = arSKU[i]["DISCOUNT_PRICE"];
										priceFormated = arSKU[i]["DISCOUNT_PRICE_FORMATED"];
										priceDiscount = arSKU[i]["PRICE"] - arSKU[i]["DISCOUNT_PRICE"];
									}
									else
									{
										BX("listItemPrice").innerHTML = arSKU[i]["PRICE_FORMATED"]+" "+arSKU[i]["VALUTA_FORMAT"];
										BX("listItemOldPrice").innerHTML = "";
										summaFormated = arSKU[i]["PRICE_FORMATED"];
										price = arSKU[i]["PRICE"];
										priceFormated = arSKU[i]["PRICE_FORMATED"];
										priceDiscount = 0;
									}

									if (arSKU[i]["CAN_BUY"] == "Y")
									{
										if (type == 'basket')
										{
											BX('viewed_id_apply').value = arSKU[i]["ID"];
											BX('viewed_lid_apply').value = arSKU[i]["LID"];
											BX('sku_to_basket_apply').value = "Y";
											btnText = BX.message('PRODUCT_ADD_TO_BASKET');
										}
										else
										{
											BX('sku_to_basket_apply').value = "N";
											BX('viewed_url_action').value = '/bitrix/admin/sale_order_new.php?user_id='+arSKU[i]["USER_ID"]+'&lang=<?=LANG?>&LID='+arSKU[i]["LID"]+'&product['+arSKU[i]["ID"]+']=1';
											btnText = BX.message('PRODUCT_ADD_TO_ORDER');
										}

										BX.findChild(BX('popup_sku_save'), {'attr': {class: 'popup-window-button-text'}}, true, false).innerHTML = btnText;
									}
									else
										BX.findChild(BX('popup_sku_save'), {'attr': {class: 'popup-window-button-text'}}, true, false).innerHTML = BX.message('PRODUCT_NOT_TO_ORDER');
								}

								if (arSKU[i]["ID"] == selectedSkuId)
									break;
							}
						}

						function fToggleSetItems(setParentId, linkId)
						{
							var elements = document.getElementsByClassName('set_item_' + setParentId);
							var hide = false;

							for (var i = 0; i < elements.length; ++i)
							{
								if (elements[i].style.display == 'none' || elements[i].style.display == '')
								{
									elements[i].style.display = 'block';
									hide = true;
								}
								else
									elements[i].style.display = 'none';
							}

							if (hide)
								BX(linkId + setParentId).innerHTML = '<?=GetMessage("BUYER_F_HIDE_SET")?>';
							else
								BX(linkId + setParentId).innerHTML = '<?=GetMessage("BUYER_F_SHOW_SET")?>';
						}
				</script>
			</td>
		</tr>
		<?$tabControl->EndTab();?>

	<?$tabControl->End();?>
<?
}
else
{
	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(GetMessage("BUYER_NO_USER"));
}

require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>