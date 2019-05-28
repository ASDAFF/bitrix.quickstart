<?
use Bitrix\Main\Entity\Query as Query;
use Bitrix\Main\Loader as Loader;
use Bitrix\Catalog\CatalogViewedProductTable as ViewedProducts;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!Loader::includeModule("sale"))
	return false;

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	return false;

if (strlen($arGadgetParams["SITE_ID"]) > 0)
{
	if (strlen($arGadgetParams["TITLE_STD"]) <= 0)
	{
		$rsSites = CSite::GetByID($arGadgetParams["SITE_ID"]);
		if ($arSite = $rsSites->GetNext())
			$arGadget["TITLE"] .= " / [".$arSite["ID"]."] ".$arSite["NAME"];
	}
}

$arGadgetParams["RND_STRING"] = randString(8);

$arFilter = array();
if (strlen($arGadgetParams["SITE_ID"]) > 0)
{
	$arFilter["LID"] = $arGadgetParams["SITE_ID"];
	$arGadgetParams["RND_STRING"] = $arGadgetParams["SITE_ID"].'_'.$arGadgetParams["RND_STRING"];
}
$cache_time = 0;
if($arGadgetParams["PERIOD"] == "WEEK")
{
	$arFilter[">=DATE_INSERT"] = ConvertTimeStamp(AddToTimeStamp(Array("DD" => -7)));
	$cache_time = 60*60*4;
}
elseif(strlen($arGadgetParams["PERIOD"]) <= 0 || $arGadgetParams["PERIOD"] == "MONTH")
{
	$arFilter[">=DATE_INSERT"] = ConvertTimeStamp(AddToTimeStamp(Array("MM" => -1)));
	$cache_time = 60*60*12;
}
elseif($arGadgetParams["PERIOD"] == "QUATER")
{
	$arFilter[">=DATE_INSERT"] = ConvertTimeStamp(AddToTimeStamp(Array("MM" => -4)));
	$cache_time = 60*60*24;
}
elseif($arGadgetParams["PERIOD"] == "YEAR")
{
	$arFilter[">=DATE_INSERT"] = ConvertTimeStamp(AddToTimeStamp(Array("YYYY" => -1)));
	$cache_time = 60*60*24;
}
if(!isset($arGadgetParams["LIMIT"]) || (int)$arGadgetParams["LIMIT"] <= 0)
	$arGadgetParams["LIMIT"] = 5;

$obCache = new CPHPCache;
$cache_id = "admin_products_".md5(serialize($arFilter))."_".$arGadgetParams["LIMIT"];
if ($obCache->InitCache($cache_time, $cache_id, "/"))
{
	$arResult = $obCache->GetVars();
}
else
{
	$cacheStart = false;
	if ($cache_time > 0)
	{
		$cacheStart = $obCache->StartDataCache();
	}
	$arResult = array();
	$arResult["SEL"] = array();
	$arFilter["PAYED"] = "Y";
	$dbR = CSaleProduct::GetBestSellerList("AMOUNT", array(), $arFilter, $arGadgetParams["LIMIT"]);
	while($arR = $dbR->Fetch())
	{
		$arResult["SEL"][] = $arR;
	}

	// VIEWED
	$arResult["VIEWED"] = array();

	if (!Loader::includeModule("catalog"))
	{
		return;
	}
	$arFilter[">=DATE_VISIT"] = $arFilter[">=DATE_INSERT"];
	unset($arFilter[">=DATE_INSERT"]);
	if(isset($arFilter['LID']))
	{
		$arFilter['SITE_ID'] = $arFilter['LID'];
		unset($arFilter['LID']);
	}
	unset($arFilter['PAYED']);

	$viewedQuery = new Query(ViewedProducts::getEntity());
	$viewedQuery->setSelect(array(
		"PRODUCT_ID",
		"NAME" => "ELEMENT.NAME",
		"PRICE" => "PRODUCT.PRICE",
		"CURRENCY" => "PRODUCT.CURRENCY",
		"RATE" => "PRODUCT.CURRENT_CURRENCY_RATE",
		"CURRENCY_RATE" => "PRODUCT.CURRENT_CURRENCY_RATE_CNT"
	))->setfilter($arFilter);
	$viewedIterator = $viewedQuery->exec();

	$viewedProducts = array();
	while($row = $viewedIterator->fetch())
	{
		$row['VIEW_COUNT'] = 1;
		if((int)$row['CURRENCY_RATE'] > 0)
		{
			$row['SORT_PRICE'] = $row['PRICE'] * $row['RATE'] / (int)($row['CURRENCY_RATE']);
		}
		else
			$row['SORT_PRICE'] = $row['PRICE'] * $row['RATE'];

		if (!isset($viewedProducts[$row['PRODUCT_ID']]))
		{
			$viewedProducts[$row['PRODUCT_ID']] = $row;
		}
		else
		{
			$viewedProducts[$row['PRODUCT_ID']]['VIEW_COUNT']++;
			if ($viewedProducts[$row['PRODUCT_ID']]['SORT_PRICE'] > $row['SORT_PRICE'])
			{
				$viewedProducts[$row['PRODUCT_ID']]['SORT_PRICE'] = $row['SORT_PRICE'];
				$viewedProducts[$row['PRODUCT_ID']]['PRICE'] = $row['PRICE'];
				$viewedProducts[$row['PRODUCT_ID']]['CURRENCY'] = $row['CURRENCY'];
				$viewedProducts[$row['PRODUCT_ID']]['CURRENCY_RATE'] = $row['CURRENCY_RATE'];
				$viewedProducts[$row['PRODUCT_ID']]['RATE'] = $row['RATE'];
			}
		}
	}

	unset($row);

	$productsMap = ViewedProducts::getProductsMap(array_keys($viewedProducts));

	// Group by Parent product id
	$groupViewedProducts = array();
	foreach($viewedProducts as $product)
	{
		$parentId = $productsMap[$product['PRODUCT_ID']];
		if(!isset($groupViewedProducts[$parentId]))
		{
			$groupViewedProducts[$parentId] = $product;
		}
		else
		{
			$groupViewedProducts[$parentId]['VIEW_COUNT'] += $product['VIEW_COUNT'];
			// Min Price
			if((float)$groupViewedProducts[$parentId]['SORT_PRICE'] > (float)$product['SORT_PRICE'])
			{
				$groupViewedProducts[$parentId]['PRICE'] = $product['PRICE'];
				$groupViewedProducts[$parentId]['CURRENCY'] = $product['CURRENCY'];
			}
		}
	}
	$groupViewedProducts = array_values($groupViewedProducts);

	\Bitrix\Main\Type\Collection::sortByColumn($groupViewedProducts, array("VIEW_COUNT" => SORT_DESC));
	$groupViewedProducts = array_slice($groupViewedProducts, 0, $arGadgetParams['LIMIT']);
	$arResult['VIEWED'] = $groupViewedProducts;

	if ($cacheStart)
	{
		$obCache->EndDataCache($arResult);
	}
}

?><script type="text/javascript">
	var gdSaleProductsTabControl_<?=$arGadgetParams["RND_STRING"]?> = false;
	BX.ready(function(){
		gdSaleProductsTabControl_<?=$arGadgetParams["RND_STRING"]?> = new gdTabControl('bx_gd_tabset_sale_products_<?=$arGadgetParams["RND_STRING"]?>');
	});
</script><?

$aTabs = array(
	array(
		"DIV" => "bx_gd_sale_products1_".$arGadgetParams["RND_STRING"],
		"TAB" => GetMessage("GD_PRD_TAB_1"),
		"ICON" => "",
		"TITLE" => "",
		"ONSELECT" => "gdSaleProductsTabControl_".$arGadgetParams["RND_STRING"].".SelectTab('bx_gd_sale_products1_".$arGadgetParams["RND_STRING"]."');"
	),
	array(
		"DIV" => "bx_gd_sale_products2_".$arGadgetParams["RND_STRING"],
		"TAB" => GetMessage("GD_PRD_TAB_2"),
		"ICON" => "",
		"TITLE" => "",
		"ONSELECT" => "gdSaleProductsTabControl_".$arGadgetParams["RND_STRING"].".SelectTab('bx_gd_sale_products2_".$arGadgetParams["RND_STRING"]."');"
	)
);

$tabControl = new CAdminViewTabControl("salePrdTabControl_".$arGadgetParams["RND_STRING"], $aTabs);

?><div class="bx-gadgets-tabs-wrap" id="bx_gd_tabset_sale_products_<?=$arGadgetParams["RND_STRING"]?>"><?

	$tabControl->Begin();
	$tabsCount = count($aTabs);
	for($i = 0; $i < $tabsCount; $i++)
		$tabControl->BeginNextTab();
	$tabControl->End();

	?><div class="bx-gadgets-tabs-cont"><?
		for($i = 0; $i < $tabsCount; $i++)
		{
			?><div id="<?=$aTabs[$i]["DIV"]?>_content" style="display: <?=($i==0 ? "block" : "none")?>;" class="bx-gadgets-tab-container"><?
				if ($i == 0)
				{
					if (!empty($arResult["SEL"]))
					{
						?><table class="bx-gadgets-table">
							<tbody>
								<tr>
									<th><?=GetMessage("GD_PRD_NAME")?></th>
									<th><?=GetMessage("GD_PRD_QUANTITY")?></th>
									<th><?=GetMessage("GD_PRD_AV_PRICE")?></th>
									<th><?=GetMessage("GD_PRD_SUM")?></th>
								</tr><?
								foreach($arResult["SEL"] as $val)
								{
									?><tr>
										<td><?=htmlspecialcharsbx($val["NAME"])?></td>
										<td align="right"><?=IntVal($val["QUANTITY"])?></td>
										<td align="right" nowrap><?=CCurrencyLang::CurrencyFormat(DoubleVal($val["AVG_PRICE"]), $val["CURRENCY"], true)?></td>
										<td align="right" nowrap><?=CCurrencyLang::CurrencyFormat(DoubleVal($val["PRICE"]), $val["CURRENCY"], true)?></td>
									</tr><?
								}
							?></tbody>
						</table><?
					}
					else
					{
						?><div align="center" class="bx-gadgets-content-padding-rl bx-gadgets-content-padding-t"><?=GetMessage("GD_PRD_NO_DATA")?></div><?
					}
				}
				elseif ($i == 1)
				{
					if (!empty($arResult["VIEWED"]))
					{
						?><table class="bx-gadgets-table">
							<tbody>
								<tr>
									<th><?=GetMessage("GD_PRD_NAME")?></th>
									<th><?=GetMessage("GD_PRD_VIEWED")?></th>
									<th><?=GetMessage("GD_PRD_PRICE")?></th>
								</tr><?
								foreach($arResult["VIEWED"] as $val)
								{
									?><tr>
										<td><?=htmlspecialcharsbx($val["NAME"])?></td>
										<td align="right"><?=IntVal($val["VIEW_COUNT"])?></td>
										<td align="right" nowrap><?=(DoubleVal($val["PRICE"]) > 0 ? CCurrencyLang::CurrencyFormat(DoubleVal($val["PRICE"]), $val["CURRENCY"], true) : "")?></td>
									</tr><?
								}
							?></tbody>
						</table><?
					}
					else
					{
						?><div align="center" class="bx-gadgets-content-padding-rl bx-gadgets-content-padding-t"><?=GetMessage("GD_PRD_NO_DATA")?></div><?
					}
				}
			?></div><?
		}
	?></div>
</div>