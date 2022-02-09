<?
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2002-2006 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$CATALOG_RIGHT = $APPLICATION->GetGroupRight("catalog");
if ($CATALOG_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/prolog.php");

ClearVars("str_iblock_");
ClearVars("s_");

$sTableID = "tbl_sale_product_search";

$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$IBLOCK_ID = IntVal($IBLOCK_ID);

$LID = htmlspecialcharsbx($LID);
if (strlen($LID) <= 0)
	$LID = false;

$dbIBlock = CIBlock::GetByID($IBLOCK_ID);
if (!($arIBlock = $dbIBlock->Fetch()))
{
	$arFilterTmp = array("MIN_PERMISSION"=>"R");

	$dbItem = CCatalog::GetList();
	while($arItems = $dbItem->Fetch())
		$arFilterTmp["ID"][] = $arItems["IBLOCK_ID"];

	$events = GetModuleEvents("sale", "OnProductSearchFormIBlock");
	if ($arEvent = $events->Fetch())
		$arFilterTmp = ExecuteModuleEventEx($arEvent, Array($arFilterTmp));

	$dbIBlock = CIBlock::GetList(Array("NAME"=>"ASC"), $arFilterTmp);
	$arIBlock = $dbIBlock->Fetch();
	$IBLOCK_ID = IntVal($arIBlock["ID"]);
}

$func_name = preg_replace("/[^a-zA-Z0-9_-]/is", "", $func_name);

$BlockPerm = CIBlock::GetPermission($IBLOCK_ID);
$bBadBlock = ($BlockPerm < "R");

$BUYER_ID = IntVal($BUYER_ID);
$arBuyerGroups = CUser::GetUserGroup($BUYER_ID);

$QUANTITY = IntVal($QUANTITY);
if ($QUANTITY <= 0)
	$QUANTITY = 1;

if (!$bBadBlock)
{
	$arFilterFields = array(
		"IBLOCK_ID",
		"filter_section",
		"filter_subsections",
		"filter_id_start",
		"filter_id_end",
		"filter_timestamp_from",
		"filter_timestamp_to",
		"filter_active",
		"filter_intext",
		"filter_product_name",
		"filter_xml_id"
	);

	$lAdmin->InitFilter($arFilterFields);

	if ($IBLOCK_ID <= 0)
	{
		$dbItem = CCatalog::GetList(array(), array("IBLOCK_TYPE_ID" => "catalog"));
		$arItems = $dbItem->Fetch();
		$IBLOCK_ID = IntVal($arItems["ID"]);
	}

	$arFilter = array(
		"WF_PARENT_ELEMENT_ID" => false,
		"IBLOCK_ID" => $IBLOCK_ID,
		"SECTION_ID" => $filter_section,
		"ACTIVE" => $filter_active,
		"%NAME" => $filter_product_name,
		"%SEARCHABLE_CONTENT" => $filter_intext,
		"SHOW_NEW" => "Y"
	);

	if (IntVal($filter_section) < 0 || strlen($filter_section) <= 0)
		unset($arFilter["SECTION_ID"]);
	elseif ($filter_subsections=="Y")
	{
		if ($arFilter["SECTION_ID"]==0)
			unset($arFilter["SECTION_ID"]);
		else
			$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
	}

	if (!empty(${"filter_id_start"})) $arFilter[">=ID"] = ${"filter_id_start"};
	if (!empty(${"filter_id_end"})) $arFilter["<=ID"] = ${"filter_id_end"};
	if (!empty(${"filter_timestamp_from"})) $arFilter["DATE_MODIFY_FROM"] = ${"filter_timestamp_from"};
	if (!empty(${"filter_timestamp_to"})) $arFilter["DATE_MODIFY_TO"] = ${"filter_timestamp_to"};
	if (!empty(${"filter_xml_id"})) $arFilter["XML_ID"] = ${"filter_xml_id"};

	$dbResultList = CIBlockElement::GetList(
		array($by => $order),
		$arFilter,
		false,
		false,
		${"filter_count_for_show"}
	);

	$dbResultList = new CAdminResult($dbResultList, $sTableID);
	$dbResultList->NavStart();

	$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("sale_prod_search_nav")));

	$arHeaders = array(
		array("id"=>"ID", "content"=>"ID", "sort"=>"id", "default"=>true),
		array("id"=>"NAME", "content"=>GetMessage("SPS_NAME"), "sort"=>"name", "default"=>true),
		array("id"=>"QUANTITY", "content"=>GetMessage("SOPS_QUANTITY"), "default"=>true, "align" => "right"),
		array("id"=>"PRICE", "content"=>GetMessage("SOPS_PRICE"), "default"=>true, "align" => "right"),
		array("id"=>"ACT", "content"=>GetMessage("SOPS_ACT"), "default"=>true),
	);

	$lAdmin->AddHeaders($arHeaders);

	$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

	$arDiscountCoupons = array();
	if (isset($BUYER_COUPONS) && strlen($BUYER_COUPONS) > 0)
	{
		$arBuyerCoupons = explode(",", $BUYER_COUPONS);
		for ($i = 0; $i < count($arBuyerCoupons); $i++)
		{
			$arBuyerCoupons[$i] = Trim($arBuyerCoupons[$i]);
			if (strlen($arBuyerCoupons[$i]) > 0)
				$arDiscountCoupons[] = $arBuyerCoupons[$i];
		}
	}
	if(CModule::IncludeModule("sale") && strlen($LID) > 0)
	{
		$BASE_LANG_CURR = CSaleLang::GetLangCurrency($LID);
		$arCurFormat = CCurrencyLang::GetCurrencyFormat($BASE_LANG_CURR);
		$priceValutaFormat = str_replace("#", '', $arCurFormat["FORMAT_STRING"]);
	}

	while ($arItems = $dbResultList->NavNext(true, "f_"))
	{
		$row =& $lAdmin->AddRow($f_ID, $arItems);

		$row->AddField("ID", $f_ID);
		$row->AddField("NAME", $f_NAME);

		$fieldValue = "";
		$nearestQuantity = $QUANTITY;
		$arPrice = CCatalogProduct::GetOptimalPrice($f_ID, $nearestQuantity, $arBuyerGroups, "N", array(), $LID, $arDiscountCoupons);

		if (!$arPrice || count($arPrice) <= 0)
		{
			if ($nearestQuantity = CCatalogProduct::GetNearestQuantityPrice($f_ID, $nearestQuantity, $arBuyerGroups))
				$arPrice = CCatalogProduct::GetOptimalPrice($f_ID, $nearestQuantity, $arBuyerGroups, "N", array(), $LID, $arDiscountCoupons);
		}

		if (!$arPrice || count($arPrice) <= 0)
		{
			$fieldValue = "&nbsp;";
		}
		else
		{
			$currentPrice = $arPrice["PRICE"]["PRICE"];
			$currentBasePrice = $arPrice["PRICE"]["PRICE"];

			if($arPrice["PRICE"]["VAT_INCLUDED"] == "N" && DoubleVal($arPrice["PRICE"]["VAT_RATE"]) > 0 )
					$currentPrice = (1+DoubleVal($arPrice["PRICE"]["VAT_RATE"])) * $currentPrice;

			$currentDiscount = 0.0;
			if (isset($arPrice["DISCOUNT"]) && count($arPrice["DISCOUNT"]) > 0)
			{
				if ($arPrice["DISCOUNT"]["VALUE_TYPE"]=="F")
				{
					if ($arPrice["DISCOUNT"]["CURRENCY"] == $arPrice["PRICE"]["CURRENCY"])
						$currentDiscount = $arPrice["DISCOUNT"]["VALUE"];
					else
						$currentDiscount = CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT"]["VALUE"], $arPrice["DISCOUNT"]["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
				}
				elseif ($arPrice["DISCOUNT"]["VALUE_TYPE"]=="S")
				{
					if ($arPrice["DISCOUNT"]["CURRENCY"] == $arPrice["PRICE"]["CURRENCY"])
						$currentDiscount = $arPrice["DISCOUNT"]["VALUE"];
					else
						$currentDiscount = CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT"]["VALUE"], $arPrice["DISCOUNT"]["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
				}
				else
				{
					$currentDiscount = $currentPrice * $arPrice["DISCOUNT"]["VALUE"] / 100.0;

					if (doubleval($arPrice["DISCOUNT"]["MAX_DISCOUNT"]) > 0)
					{
						if ($arPrice["DISCOUNT"]["CURRENCY"] == $arPrice["PRICE"]["CURRENCY"])
							$maxDiscount = $arPrice["DISCOUNT"]["MAX_DISCOUNT"];
						else
							$maxDiscount = CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT"]["MAX_DISCOUNT"], $arPrice["DISCOUNT"]["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
						$maxDiscount = roundEx($maxDiscount, CATALOG_VALUE_PRECISION);

						if ($currentDiscount > $maxDiscount)
							$currentDiscount = $maxDiscount;
					}
				}

				$currentDiscount = roundEx($currentDiscount, CATALOG_VALUE_PRECISION);
				if ($arPrice["DISCOUNT"]["VALUE_TYPE"]=="S")
				{
					$currentPrice = $currentDiscount;
				}
				else
				{
					$currentPrice = $currentPrice - $currentDiscount;
				}
			}
			$vatRate = $arPrice["PRICE"]["VAT_RATE"];
			$fieldValue = FormatCurrency($currentPrice, $arPrice["PRICE"]["CURRENCY"]);
			if (DoubleVal($nearestQuantity) != DoubleVal($QUANTITY))
				$fieldValue .= str_replace("#CNT#", $nearestQuantity, GetMessage("SOPS_PRICE1"));
		}

		if(strlen($BASE_LANG_CURR) <= 0)
		{
			$arCurFormat = CCurrencyLang::GetCurrencyFormat($arPrice["PRICE"]["CURRENCY"]);
			$priceValutaFormat = str_replace("#", '', $arCurFormat["FORMAT_STRING"]);
		}

		$row->AddField("PRICE", $fieldValue);

		$arCatalogProduct = CCatalogProduct::GetByID($f_ID);
		$balance = FloatVal($arCatalogProduct["QUANTITY"]);
		$row->AddField("QUANTITY", $balance);

		$URL = CIBlock::ReplaceDetailUrl($arItems["DETAIL_PAGE_URL"], $arItems, true);

		$arPriceType = GetCatalogGroup($arPrice["PRICE"]["CATALOG_GROUP_ID"]);
		$PriceType = $arPriceType["NAME_LANG"];

		$productImg = "";
		if($arItems["PREVIEW_PICTURE"] != "")
			$productImg = $arItems["PREVIEW_PICTURE"];
		elseif($arItems["DETAIL_PICTURE"] != "")
			$productImg = $arItems["DETAIL_PICTURE"];

		$ImgUrl = "";
		if ($productImg != "")
		{
			$arFile = CFile::GetFileArray($productImg);
			$productImg = CFile::ResizeImageGet($arFile, array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);
			$ImgUrl = $productImg["src"];
		}
		
		$currentTotalPrice = ($currentPrice + $currentDiscount) * $QUANTITY;

		$discountPercent = 0;
		if ($currentDiscount > 0)
			$discountPercent = IntVal(($currentDiscount * 100) / $currentTotalPrice);

		if (CModule::IncludeModule('sale'))
		{

			if (strlen($BASE_LANG_CURR) > 0 && $BASE_LANG_CURR != $arPrice["PRICE"]["CURRENCY"])
			{
				$currentTotalPrice = roundEx(CCurrencyRates::ConvertCurrency($currentTotalPrice, $arPrice["PRICE"]["CURRENCY"], $BASE_LANG_CURR), SALE_VALUE_PRECISION);
				$currentPrice = roundEx(CCurrencyRates::ConvertCurrency($currentPrice, $arPrice["PRICE"]["CURRENCY"], $BASE_LANG_CURR), SALE_VALUE_PRECISION);
				$currentBasePrice = roundEx(CCurrencyRates::ConvertCurrency($currentBasePrice, $arPrice["PRICE"]["CURRENCY"], $BASE_LANG_CURR), SALE_VALUE_PRECISION);
				$currentDiscount = roundEx(CCurrencyRates::ConvertCurrency($currentDiscount, $arPrice["PRICE"]["CURRENCY"], $BASE_LANG_CURR), SALE_VALUE_PRECISION);
				$arPrice["PRICE"]["CURRENCY"] = $BASE_LANG_CURR;
			}

			$currentTotalPriceFormat = SaleFormatCurrency($currentTotalPrice, $arPrice["PRICE"]["CURRENCY"]);
			$summaFormated = SaleFormatCurrency(($currentPrice * $QUANTITY), $arPrice["PRICE"]["CURRENCY"]);
		}
		else
		{
			$currentTotalPriceFormat = CurrencyFormatNumber($currentTotalPrice, $arPrice["PRICE"]["CURRENCY"]);
			$summaFormated = CurrencyFormatNumber(($currentPrice * $QUANTITY), $arPrice["PRICE"]["CURRENCY"]);
		}

		$urlEdit = "/bitrix/admin/iblock_element_edit.php?ID=".$arItems["ID"]."&type=catalog&lang=".LANG."&IBLOCK_ID=".$arItems["IBLOCK_ID"]."&find_section_section=".$arItems["IBLOCK_SECTION_ID"];

		$bCanBuy = true;
		if ($arCatalogProduct["CAN_BUY_ZERO"]!="Y" && ($arCatalogProduct["QUANTITY_TRACE"]=="Y" && doubleval($arCatalogProduct["QUANTITY"])<=0))
			$bCanBuy = false;

		$arParams = "{'id' : '".$arItems["ID"]."',
			'name' : '".CUtil::JSEscape($arItems["NAME"])."',
			'url' : '".CUtil::JSEscape($URL)."',
			'urlEdit' : '".CUtil::JSEscape($urlEdit)."',
			'urlImg' : '".CUtil::JSEscape($ImgUrl)."',
			'price' : '".CUtil::JSEscape($currentPrice)."',
			'priceFormated' : '".CUtil::JSEscape(CurrencyFormatNumber($currentPrice, $arPrice["PRICE"]["CURRENCY"]))."',
			'valutaFormat' : '".CUtil::JSEscape($priceValutaFormat)."',
			'priceDiscount' : '".CUtil::JSEscape($currentDiscount)."',
			'priceBase' : '".CUtil::JSEscape($currentBasePrice)."',
			'priceBaseFormat' : '".CUtil::JSEscape(CurrencyFormatNumber($currentBasePrice, $arPrice["PRICE"]["CURRENCY"]))."',
			'priceTotalFormated' : '".CUtil::JSEscape($currentTotalPriceFormat)."',
			'discountPercent' : '".CUtil::JSEscape($discountPercent)."',
			'summaFormated' : '".CUtil::JSEscape($summaFormated)."',
			'quantity' : '".CUtil::JSEscape($QUANTITY)."',
			'module' : 'catalog',
			'currency' : '".CUtil::JSEscape($arPrice["PRICE"]["CURRENCY"])."',
			'weight' : '".DoubleVal($arCatalogProduct["WEIGHT"])."',
			'vatRate' : '".DoubleVal($vatRate)."',
			'priceType' : '".CUtil::JSEscape($PriceType)."',
			'balance' : '".CUtil::JSEscape($balance)."',
			'catalogXmlID' : '".CUtil::JSEscape($arIBlock["XML_ID"])."',
			'productXmlID' : '".CUtil::JSEscape($f_XML_ID)."',
			'callback' : 'CatalogBasketCallback',
			'orderCallback' : 'CatalogBasketOrderCallback',
			'cancelCallback' : 'CatalogBasketCancelCallback',
			'payCallback' : 'CatalogPayOrderCallback'}";

		$events = GetModuleEvents("sale", "OnProductSearchForm");
		if ($arEvent = $events->Fetch())
			$arParams = ExecuteModuleEventEx($arEvent, Array($f_ID, $arParams));
		$arParams = "var el".$arItems["ID"]." = ".$arParams;

		if($bCanBuy)
			$row->AddField("ACT", "<script>".$arParams."</script><a href=\"javascript:void(0)\" onClick=\"SelEl(el".$arItems["ID"].")\">".GetMessage("SPS_SELECT")."</a>");
		else
			$row->AddField("ACT", "<a href=\"javascript:void(0)\" onClick=\"showCanBuy()\">".GetMessage("SPS_SELECT")."</a>");

		$arActions = array();
		$arActions[] = array(
			"ICON"=>"",
			"TEXT"=>GetMessage("SPS_SELECT"),
			"DEFAULT"=>true,
			"ACTION"=> ($bCanBuy) ? "SelEl(el".$arItems["ID"].");" : "showCanBuy();",

		);
		$row->AddActions($arActions);
	}

	$lAdmin->AddFooter(
		array(
			array(
				"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
				"value" => $dbResultList->SelectedRowsCount()
			),
		)
	);
}
else
{
	echo ShowError(GetMessage("SPS_NO_PERMS").".");
}

$lAdmin->CheckListMode();


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/
$APPLICATION->SetTitle(GetMessage("SPS_SEARCH_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");

?>

<script language="JavaScript">
<!--
function SelEl(arParams)
{
	window.opener.<?= $func_name ?>(<?= IntVal($index) ?>, arParams, <?= IntVal($IBLOCK_ID) ?>);
	window.close();
}

function showCanBuy()
{
	alert('<?=GetMessageJS("SPS_CAN_BUY_NOT")?>');

}
//-->
</script>

<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
	<input type="hidden" name="__BX_CRM_QUERY_STRING_PREFIX" value="<?echo $APPLICATION->GetCurPage() ?>?">
	<input type="hidden" name="field_name" value="<?echo htmlspecialcharsbx($field_name)?>">
	<input type="hidden" name="field_name_name" value="<?echo htmlspecialcharsbx($field_name_name)?>">
	<input type="hidden" name="field_name_url" value="<?echo htmlspecialcharsbx($field_name_url)?>">
	<input type="hidden" name="alt_name" value="<?echo htmlspecialcharsbx($alt_name)?>">
	<input type="hidden" name="form_name" value="<?echo htmlspecialcharsbx($form_name)?>">
	<input type="hidden" name="func_name" value="<?echo htmlspecialcharsbx($func_name)?>">
	<input type="hidden" name="index" value="<?echo htmlspecialcharsbx($index)?>">
	<input type="hidden" name="BUYER_ID" value="<?echo htmlspecialcharsbx($BUYER_ID)?>">
	<input type="hidden" name="QUANTITY" value="<?echo htmlspecialcharsbx($QUANTITY)?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="hidden" name="LID" value="<?echo $LID?>">
<?
$arIBTYPE = CIBlockType::GetByIDLang($arIBlock["IBLOCK_TYPE_ID"], LANG);

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		"find_iblock_id" => GetMessage("SPS_CATALOG"),
		"find_id" => "ID (".GetMessage("SPS_ID_FROM_TO").")",
		"find_xml_id" => GetMessage("SPS_XML_ID"),
		"find_time" => GetMessage("SPS_TIMESTAMP"),
		"find_section" => ($arIBTYPE["SECTIONS"]=="Y"? GetMessage("SPS_SECTION") : null),
		"find_active" => GetMessage("SPS_ACTIVE"),
		"find_name" => GetMessage("SPS_NAME"),
		"find_descr" => GetMessage("SPS_DESCR"),
	)
);
$oFilter->SetDefaultRows("find_name");

$oFilter->Begin();

?>
	<tr>
		<td><?= GetMessage("SPS_CATALOG") ?>:</td>
		<td>
			<select name="IBLOCK_ID">
			<?
			$catalogID = Array();
			$dbItem = CCatalog::GetList();
			while($arItems = $dbItem->Fetch())
				$catalogID[] = $arItems["IBLOCK_ID"];
			$db_iblocks = CIBlock::GetList(Array("ID"=>"ASC"), Array("ID" => $catalogID));
			while ($db_iblocks->ExtractFields("str_iblock_"))
			{
				?><option value="<?=$str_iblock_ID?>"<?if($IBLOCK_ID==$str_iblock_ID)echo " selected"?>><?=$str_iblock_NAME?> [<?=$str_iblock_LID?>] (<?=$str_iblock_ID?>)</option><?
			}
			?>
			</select>
		</td>
	</tr>

	<tr>
		<td>ID (<?= GetMessage("SPS_ID_FROM_TO") ?>):</td>
		<td>
			<input type="text" name="filter_id_start" size="10" value="<?echo htmlspecialcharsex($filter_id_start)?>">
			...
			<input type="text" name="filter_id_end" size="10" value="<?echo htmlspecialcharsex($filter_id_end)?>">
		</td>
	</tr>

	<tr>
		<td nowrap><?= GetMessage("SPS_XML_ID") ?>:</td>
		<td nowrap>
			<input type="text" name="filter_xml_id" size="50" value="<?echo htmlspecialcharsex(${"filter_xml_id"})?>">
		</td>
	</tr>

	<tr>
		<td nowrap><?= GetMessage("SPS_TIMESTAMP") ?>:</td>
		<td nowrap><? echo CalendarPeriod("filter_timestamp_from", htmlspecialcharsex($filter_timestamp_from), "filter_timestamp_to", htmlspecialcharsex($filter_timestamp_to), "form1")?></td>
	</tr>

<?
if ($arIBTYPE["SECTIONS"]=="Y"):
?>
		<tr>
			<td nowrap valign="top"><?= GetMessage("SPS_SECTION") ?>:</td>
			<td nowrap>
				<select name="filter_section">
					<option value="">(<?= GetMessage("SPS_ANY") ?>)</option>
					<option value="0"<?if($filter_section=="0")echo" selected"?>><?= GetMessage("SPS_TOP_LEVEL") ?></option>
					<?
					$bsections = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$IBLOCK_ID));
					while($bsections->ExtractFields("s_")):
						?><option value="<?echo $s_ID?>"<?if($s_ID==$filter_section)echo " selected"?>><?echo str_repeat("&nbsp;.&nbsp;", $s_DEPTH_LEVEL)?><?echo $s_NAME?></option><?
					endwhile;
					?>
				</select><br>
				<input type="checkbox" name="filter_subsections" value="Y"<?if($filter_subsections=="Y")echo" checked"?>> <?= GetMessage("SPS_INCLUDING_SUBS") ?>
			</td>
		</tr>
<?
endif;
?>

	<tr>
		<td nowrap><?= GetMessage("SPS_ACTIVE") ?>:</td>
		<td nowrap>
			<select name="filter_active">
				<option value=""><?=htmlspecialcharsex("(".GetMessage("SPS_ANY").")")?></option>
				<option value="Y"<?if($filter_active=="Y")echo " selected"?>><?=htmlspecialcharsex(GetMessage("SPS_YES"))?></option>
				<option value="N"<?if($filter_active=="N")echo " selected"?>><?=htmlspecialcharsex(GetMessage("SPS_NO"))?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td nowrap><?= GetMessage("SPS_NAME") ?>:</td>
		<td nowrap>
			<input type="text" name="filter_product_name" value="<?echo htmlspecialcharsex($filter_product_name)?>" size="30">
		</td>
	</tr>
	<tr>
		<td nowrap><?= GetMessage("SPS_DESCR") ?>:</td>
		<td nowrap>
			<input type="text" name="filter_intext" size="50" value="<?echo htmlspecialcharsex(${"filter_intext"})?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
<?
$oFilter->Buttons();
?>
<input type="submit" name="set_filter" value="<?echo GetMessage("prod_search_find")?>" title="<?echo GetMessage("prod_search_find_title")?>">
<input type="submit" name="del_filter" value="<?echo GetMessage("prod_search_cancel")?>" title="<?echo GetMessage("prod_search_cancel_title")?>">
<?
$oFilter->End();
?>

<table>
<tr>
	<td><?= GetMessage("SOPS_COUPON") ?>:</td>
	<td><input type="text" name="BUYER_COUPONS" size="30" value="<?= htmlspecialcharsbx($BUYER_COUPONS) ?>"></td>
	<td><input type="submit" value="<?= GetMessage("SOPS_APPLY") ?>"></td>
</tr>
</table>
<br>
</form>

<?
$lAdmin->DisplayList();
?>
<br>
<input type="button" class="typebutton" value="<?= GetMessage("SPS_CLOSE") ?>" onClick="window.close();">
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>