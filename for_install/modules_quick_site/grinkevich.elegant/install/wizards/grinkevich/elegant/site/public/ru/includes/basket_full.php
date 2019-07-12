<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
{	$id = intval($_GET['id']);
	$qty = intval($_GET['qty']);

	$props = !empty($_GET['prop']) && is_array($_GET['prop']) ? $_GET['prop'] : null;
	$params = null;

	if ($props)
	{		$params = array();		foreach ($props as $k=>$v)
		{			$params[] = array('NAME' => $k, 'VALUE' => $v);		}	}


    if (($action == "ADD2BASKET" || $action == "BUY") && ($id > 0))
    {
        Add2BasketByProductID($id, 1, null, $params);
		//if ($action == "BUY") LocalRedirect("basket.php");


		$ids = str_replace('id' . $id, '', $_COOKIE['cart']);
		$ids = str_replace('id', '', $ids);

		$ids = array_filter(array_unique(explode(',', $ids)));
		$ids[] = $id;

		$cart = 'id' . join(',id', $ids);

		setcookie('cart', $cart, time()+86400*7, '/');
//		setcookie('cart', '', time()+86400*7, '/');
//		echo $cart;
    }
    else
    {    	if ($action == 'delete')
    	{    		$cart = str_replace('id' . $id, '', $_COOKIE['cart']);
    		$cart = str_replace(',,', '', $cart);    		setcookie('cart', $cart, time()+86400*7, '/');    	}
    	$APPLICATION->IncludeComponent("bitrix:eshop.sale.basket.basket", ".default", array(
			"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
			"COLUMNS_LIST" => array(
				0 => "NAME",
				1 => "PROPS",
				2 => "PRICE",
				3 => "QUANTITY",
				4 => "DELETE",
				5 => "DELAY"
			),
			"AJAX_MODE" => "Y",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"PATH_TO_ORDER" => SITE_DIR."personal/cart/order/",
			"PATH_TO_CART" => SITE_DIR."personal/cart/",

			"HIDE_COUPON" => "N",
			"QUANTITY_FLOAT" => "N",
			"PRICE_VAT_SHOW_VALUE" => "N",
			"SET_TITLE" => "Y",
			"AJAX_OPTION_ADDITIONAL" => ""
			),
			false
		);    }
}
?>