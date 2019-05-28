<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	// we dont trust input params, so validation is required
	$legalColors = array(
		'green' => true,
		'yellow' => true,
		'red' => true,
		'gray' => true
	);
	// default colors in case parameters unset
	$defaultColors = array(
		'N' => 'green',
		'P' => 'yellow',
		'F' => 'gray',
		'PSEUDO_CANCELLED' => 'red'
	);

	foreach ($arParams as $key => $val)
		if(strpos($key, "STATUS_COLOR_") !== false && !$legalColors[$val])
			unset($arParams[$key]);

	// to make orders follow in right status order
	foreach($arResult['INFO']['STATUS'] as $id => $stat)
	{
		$arResult['INFO']['STATUS'][$id]["COLOR"] = $arParams['STATUS_COLOR_'.$id] ? $arParams['STATUS_COLOR_'.$id] : (isset($defaultColors[$id]) ? $defaultColors[$id] : 'gray');
		$arResult["ORDER_BY_STATUS"][$id] = array();
	}
	$arResult["ORDER_BY_STATUS"]["PSEUDO_CANCELLED"] = array();

	$arResult["INFO"]["STATUS"]["PSEUDO_CANCELLED"] = array(
		"NAME" => GetMessage('SPOL_PSEUDO_CANCELLED'),
		"COLOR" => $arParams['STATUS_COLOR_PSEUDO_CANCELLED'] ? $arParams['STATUS_COLOR_PSEUDO_CANCELLED'] : (isset($defaultColors['PSEUDO_CANCELLED']) ? $defaultColors['PSEUDO_CANCELLED'] : 'gray')
	);

	foreach ($arResult["INFO"]['DELIVERY'] as $key => $arDelivery) {
		if(mb_strlen($arDelivery['STORE'])>0){
			$arStores = unserialize($arDelivery['STORE']);
			foreach ($arStores as $key2 => $value) {
				if(!$arResult["INFO"]['STORES'][$value]){
					$dbStore = CCatalogStore::GetList(array(), array("ID" => $value), false, false, array("ID", "XML_ID", "TITLE", "DESCRIPTION"));						
					if ($arStore = $dbStore -> GetNext()){
						$arResult["INFO"]['STORES'][$value] = $arStore;
					}					
				}
			}
		}
	}

	if(is_array($arResult["ORDERS"]) && !empty($arResult["ORDERS"]))
		foreach ($arResult["ORDERS"] as $order)
		{
			$order['HAS_DELIVERY'] = intval($order["ORDER"]["DELIVERY_ID"]) || strpos($order["ORDER"]["DELIVERY_ID"], ":") !== false;

			$stat = $order['ORDER']['CANCELED'] == 'Y' ? 'PSEUDO_CANCELLED' : $order["ORDER"]["STATUS_ID"];
			$color = $arParams['STATUS_COLOR_'.$stat];
			$order['STATUS_COLOR_CLASS'] = empty($color) ? 'gray' : $color;

			foreach($order["BASKET_ITEMS"] as $k=>$item){ 
				if(empty($item["DETAIL_PAGE_URL"])){
					$res = CCatalogProduct::GetByIDEx((int)$item["PRODUCT_ID"]);
					$order["BASKET_ITEMS"][$k]["DETAIL_PAGE_URL"] = $res["DETAIL_PAGE_URL"];
					if(empty($order["BASKET_ITEMS"][$k]["DETAIL_PAGE_URL"])){
						$res = CIBlockElement::GetList(Array(), Array("?NAME"=>$item["NAME"]), false, Array("nPageSize"=>1));
						if($ob = $res->GetNextElement()){ 
							$order["BASKET_ITEMS"][$k]["DETAIL_PAGE_URL"] = $ob->fields["DETAIL_PAGE_URL"];
						}
					}
				}
			}
			$arResult["ORDER_BY_STATUS"][$stat][] = $order;
		}
?>