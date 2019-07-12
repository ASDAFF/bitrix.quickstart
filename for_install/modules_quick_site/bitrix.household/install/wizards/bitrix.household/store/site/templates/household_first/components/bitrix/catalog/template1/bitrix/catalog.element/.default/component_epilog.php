<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

$APPLICATION->AddHeadScript('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jquery/fancybox/jquery.fancybox-1.3.1.pack.js');
$APPLICATION->SetAdditionalCSS('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jquery/fancybox/jquery.fancybox-1.3.1.css');
?>

<?
if (isset($_GET['MID'])) $sel=":last"; else $sel=":first";
?>
<script type="text/javascript">
$(function () {
    var tabContainers = $('div.tabs > div');
    var sel='<?=$sel?>';
    var loc = window.location.hash.replace("#","");
    if(loc=='feedback')    
    	sel=":last";    
    tabContainers.hide().filter(sel).show();    
    
     $('div.tabs ul.technical a').click(function () {
	        tabContainers.hide();
	        tabContainers.filter(this.hash).show();
	        $('div.tabs ul.technical a').removeClass('active');
	        $(this).addClass('active');
	        return false;
	    }).filter(sel).click();   
});
</script>


<?if (CModule::IncludeModule('sale'))
{
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


	if ($arBasket = $dbBasketItems->Fetch())
	{
		if($arBasket["DELAY"] == "Y")
			echo "<script type=\"text/javascript\">$(function() {disableAddToCart('catalog_add2cart_link', 'detail', '".GetMessage("CATALOG_IN_CART_DELAY")."')});</script>\r\n"; 
		else
			echo "<script type=\"text/javascript\">$(function() {disableAddToCart('catalog_add2cart_link', 'detail', '".GetMessage("CATALOG_IN_BASKET")."')});</script>\r\n"; 
	}
}

if ($arParams['USE_COMPARE'])
	{
		if (isset(
			$_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$arResult['ID']]
		))
		{
			echo '<script type="text/javascript">$(function(){disableAddToCompare(\'catalog_add2compare_link_'.$arResult['ID'].'\', \''.GetMessage("CATALOG_IN_COMPARE").'\','.$arResult['ID'].', \''.$arParams["COMPARE_NAME"].'\',\''.$arParams["IBLOCK_ID"].'\');})</script>';
		}
	}
	
	echo '<script type="text/javascript">$(function(){'."\r\n";
        echo "UpdateCompare(".$arParams["IBLOCK_ID"].")";
	echo '})</script>';

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
		//if (in_array($arItem['PRODUCT_ID'], $arResult['IDS']))
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
			echo "disableAddToCart('catalog_add2cart_link_".$id."', 'list', '".GetMessage("CATALOG_IN_CART")."');\r\n";
		}
		foreach ($arPageItemsDelay as $id) 
		{
			echo "disableAddToCart('catalog_add2cart_link_".$id."', 'list', '".GetMessage("CATALOG_IN_CART_DELAY")."');\r\n";
		}
		echo '})</script>';
	}
?>
<?
if($arParams['ADD_PRODUSER_TO_TITLE']!="N")
{
	$res = CIBlockElement::GetByID($arResult["PROPERTIES"]["PRODUSER"]["~VALUE"]);
	if($ar_res = $res->GetNext())
	  $APPLICATION->SetTitle($ar_res["NAME"]." ".$arResult["NAME"]);
}
else
	$APPLICATION->SetTitle($arResult["NAME"]);
?>
<??>