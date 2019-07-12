<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") && CModule::IncludeModule("iblock"))
{
    $action=$_REQUEST['action'];
    if (($action == "ADD2BASKET" || $action == "BUY") && IntVal($_REQUEST['id'])>0)
    {     
	$product_properties=array();	
	if($_REQUEST['ADD_PROPS'] && $_REQUEST["prop"])
	{
		$PRODUCT_PROPERTIES=explode(",",$_REQUEST['ADD_PROPS']);	
		$resArProp=array();
		foreach($PRODUCT_PROPERTIES as $key=>$pid)
		{
			if($_REQUEST["prop"][$pid])
			{
				if(defined("BX_UTF"))
					$resArProp[$pid]=$_REQUEST["prop"][$pid];
				else
					$resArProp[$pid]=iconv('UTF-8','windows-1251',$_REQUEST["prop"][$pid]);
			}		
			else
				unset($PRODUCT_PROPERTIES[$key]);	
		}	
		if(is_array($resArProp))
		{					
			$product_properties = CIBlockPriceTools::CheckProductProperties(
							'#igrushka_IBLOCK_ID#',
							$_REQUEST['id'],
							$PRODUCT_PROPERTIES,
							$resArProp
			);
			if(!is_array($product_properties))
				ShowError("Ошибка добавления в корзину");	
					
		}	
	}
	
	if(IntVal($_REQUEST['quantity'])==0)
		$_REQUEST['quantity']=1;	
        Add2BasketByProductID($_REQUEST['id'], $_REQUEST['quantity'], $product_properties);
    }
}

$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", ".default", array(
   "PATH_TO_BASKET" => SITE_DIR."personal/cart/",
   "PATH_TO_ORDER" => SITE_DIR."personal/order/make/",  
   ),
   false,
   array('')
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>