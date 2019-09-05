<?
use Bitrix\Main\Config\Option;

$all_vat = 0;
$all_quantity = 0;

$maskProps = unserialize(Option::get( 'sotbit.b2bshop', 'MASK_ORDER_PROPS', '' ));

if( !is_array( $maskProps ) )
{
	$maskProps = array();
}
if($arResult['ORDER_PROP'])
{
	foreach( $arResult['ORDER_PROP'] as $orderPropType => $orderProps )
	{
		if(in_array($orderPropType,array('USER_PROPS_Y','USER_PROPS_N')))
		{
			foreach($orderProps as $key => $orderProp)
			{
				if(in_array($orderProp['ID'], $maskProps))
				{
					$arResult['ORDER_PROP'][$orderPropType][$key]['MASK'] = 'Y';
					$arResult['SHOW_MASK'] = 'Y';
				}
			}
		}
	}
}
if( !empty( $arResult["BASKET_ITEMS"] ) )
{
	foreach( $arResult["BASKET_ITEMS"] as &$arItem )
	{
		if( $arItem["CAN_BUY"] == "Y" && $arItem["DELAY"] == "N" )
		{
			$all_quantity = $all_quantity + $arItem['QUANTITY'];
			$all_vat = $all_vat + $arItem['VAT_VALUE'];
		}
	}
	$arResult['TOTAL_VAT'] = CurrencyFormat($all_vat, CCurrency::GetBaseCurrency());
	$arResult['TOTAL_QUANTITY'] = $all_quantity;
}

$arResult['SDEK'] = 0;

$rs = \Bitrix\Sale\Delivery\Services\Table::getList(['filter' => ['CODE' => ['sdek:pickup']],'select' => ['ID']])->fetch();
if($rs['ID'] > 0)
{
	$arResult['SDEK'] = $rs['ID'];
}

?>