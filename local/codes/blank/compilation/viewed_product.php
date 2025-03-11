<?
// в модуле интернет-магазина нужно включить на стройку - "Использовать совместимость для просмотренных товаров каталога"
$countViewedProducts = 0;
		$GLOBALS['arViewedProducts'] = array();
		if(\Bitrix\Main\Loader::includeModule("catalog") && \Bitrix\Main\Loader::includeModule("sale"))
		{
		   $arFilter["FUSER_ID"] = CSaleBasket::GetBasketUserID();
		   if(\Bitrix\Main\Config\Option::get("sale", "viewed_capability", "") == "Y")
		   {
		      $viewedIterator = \Bitrix\Catalog\CatalogViewedProductTable::getList(
		         array(
		            "filter" => $arFilter,
		            "select" => array(
		               "ID", "PRODUCT_ID"
		            ),
		            "order" => array("DATE_VISIT" => "DESC"),
		         )
		      );
		
		      while($row = $viewedIterator->fetch())
		      {
		         $GLOBALS['arViewedProducts'][] = $row['PRODUCT_ID'];
		         $countViewedProducts++;
		      }
		   }
		}

?>
