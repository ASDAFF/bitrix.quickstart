<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

$APPLICATION->AddHeadScript('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jquery/fancybox/jquery.fancybox-1.3.1.pack.js');
$APPLICATION->SetAdditionalCSS('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jquery/fancybox/jquery.fancybox-1.3.1.css');

if (CModule::IncludeModule('sale'))
{
	/*echo "<pre>";
		print_r($arResult);
		echo "</pre>";*/
	
	$dbBasketItems = CSaleBasket::GetList(
		array(
			"ID" => "ASC"
		),
		array(
			"PRODUCT_ID" => $arResult['ID'],
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL",
		),
		false,
		false,
		array()
	);

	while ($arBasket = $dbBasketItems->Fetch())
	{
		$arCurVals=array();
		$db_res = CSaleBasket::GetPropsList(
		        array(
		                "SORT" => "ASC",
		                "NAME" => "ASC"
		            ),
		        array("BASKET_ID" => $arBasket['ID'])
		    );
		while ($ar_res = $db_res->Fetch())
		{
			if(in_array($ar_res['CODE'], $arParams["PRODUCT_PROPERTIES"]))			
				$arCurVals[$ar_res['CODE']]=$ar_res['VALUE'];			
		}
		$countEqual=0;		
		foreach($arCurVals as $pid=>$val)
		{				
			if(is_array($arResult['PROPERTIES'][$pid]['VALUE']) && $arResult['PROPERTIES'][$pid]['VALUE'][0]==$val)
				$countEqual++;
			elseif($arResult['PROPERTIES'][$pid]['VALUE']==$val	)
				$countEqual++;
		}		
		
		if($arBasket["DELAY"] == "Y" && $countEqual==count($arCurVals))
			echo "<script type=\"text/javascript\">$(function() {disableAddToCart('catalog_add2cart_link', 'detail', '".GetMessage("CATALOG_IN_CART_DELAY")."')});</script>\r\n";
		elseif($countEqual>0 && $countEqual==count($arCurVals))
			echo "<script type=\"text/javascript\">$(function() {disableAddToCart('catalog_add2cart_link', 'detail', '".GetMessage("CATALOG_IN_BASKET")."')});</script>\r\n";
	}
}

if ($arParams['USE_COMPARE'])
{
	if (isset(
		$_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$arResult['ID']]
	))
	{
		echo '<script type="text/javascript">$(function(){disableAddToCompare(\'catalog_add2compare_link\', \''.GetMessage("CATALOG_IN_COMPARE").'\');})</script>';
	}
}

if (array_key_exists("PROPERTIES", $arResult) && is_array($arResult["PROPERTIES"]))
{
	$sticker = "";

	foreach (Array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER") as $propertyCode)
	{
		if (array_key_exists($propertyCode, $arResult["PROPERTIES"]) && intval($arResult["PROPERTIES"][$propertyCode]["PROPERTY_VALUE_ID"]) > 0)
			$sticker .= "&nbsp;<span class=\"sticker\">".$arResult["PROPERTIES"][$propertyCode]["NAME"]."</span>";
	}

	if ($sticker != "")
		$APPLICATION->SetPageProperty("ADDITIONAL_TITLE", $sticker);
}
if (count($arResult['OFFERS_IDS']) > 0 && CModule::IncludeModule('sale'))
{
	$arItemsInCompare = array();
	foreach ($arResult['OFFERS_IDS'] as $ID)
	{
		if (isset(
			$_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$ID]
		))
			$arItemsInCompare[] = $ID;
	}

	$dbBasketItems = CSaleBasket::GetList(
		array(
			"ID" => "ASC"
		),
		array(
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL",
			),
		false,
		false,
		array()
	);

	$arPageItems = array();
	$arPageItemsDelay = array();
	while ($arItem = $dbBasketItems->Fetch())
	{		
		if (in_array($arItem['PRODUCT_ID'], $arResult['OFFERS_IDS']))
		{
			if($arItem["DELAY"] == "Y")
				$arPageItemsDelay[] = $arItem['PRODUCT_ID'];
			else
				$arPageItems[] = $arItem['PRODUCT_ID'];
		}
	}

	if (count($arPageItems) > 0 || count($arPageItemsDelay) > 0)
	{
		echo '<script type="text/javascript">$(function(){'."\r\n";
		foreach ($arPageItems as $id) 
		{
			echo "disableAddToCart('catalog_add2cart_link_ofrs_".$id."', 'list', '".GetMessage("CATALOG_IN_BASKET")."');\r\n";
		}
		foreach ($arPageItemsDelay as $id) 
		{
			echo "disableAddToCart('catalog_add2cart_link_ofrs_".$id."', 'list', '".GetMessage("CATALOG_IN_CART_DELAY")."');\r\n";
		}
		echo '})</script>';
	}
	
	if (count($arItemsInCompare) > 0)
	{
		echo '<script type="text/javascript">$(function(){'."\r\n";
		foreach ($arItemsInCompare as $id) 
		{
			echo "disableAddToCompare('catalog_add2compare_link_ofrs_".$id."', '".GetMessage("CATALOG_IN_COMPARE")."');\r\n";
		}
		echo '})</script>';
	}
}

if($arResult['SECTION']['NAME'])
{
	$sect=" - ".$arResult['SECTION']['NAME'];	
}
else
	$sect=" - ".$APPLICATION->GetTitle("title");
$APPLICATION->SetPageProperty("browser_title", $arResult["NAME"].$sect);
$APPLICATION->SetTitle($arResult["NAME"]);

$YOU_HAVE_SEEN = $APPLICATION->get_cookie("YOU_HAVE_SEEN");
$arYOU_HAVE_SEEN=array();
if($YOU_HAVE_SEEN)
{
	$arYOU_HAVE_SEEN=unserialize($YOU_HAVE_SEEN);
	if(count($arYOU_HAVE_SEEN)==10)
		unset($arYOU_HAVE_SEEN[9]);
}
array_unshift($arYOU_HAVE_SEEN, $arResult['ID']);
$arYOU_HAVE_SEEN=array_unique($arYOU_HAVE_SEEN);
$APPLICATION->set_cookie("YOU_HAVE_SEEN", serialize($arYOU_HAVE_SEEN), time()+60*60*24*30*12*1);
?>