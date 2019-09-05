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

$arResult['BUYERS'] = [];

if($arParams['BUYER_PERSONAL_TYPE'])
{
	$innProps = unserialize(Option::get('sotbit.b2bshop', 'ORDER_PROP_INN'));
	if(!is_array($innProps))
	{
		$innProps = [];
	}
	$orgProps = unserialize(Option::get('sotbit.b2bshop', 'ORDER_PROP_ORG_NAME'));
	if(!is_array($orgProps))
	{
		$orgProps = [];
	}
	
	$idBuyers = [];
	$ptBuyers = [];
	$rs = CSaleOrderUserProps::GetList(
			array("DATE_UPDATE" => "DESC"),
			array(
					"PERSON_TYPE_ID" => $arParams['BUYER_PERSONAL_TYPE'],
					"USER_ID" => (int)$USER->GetID()
			)
	);
	while($buyer = $rs->Fetch())
	{
		$ptBuyers[$buyer['ID']] = $buyer['PERSON_TYPE_ID'];
		$idBuyers[]=$buyer['ID'];
	}
	
	if($idBuyers)
	{
		$rs = \Bitrix\Sale\Internals\UserPropsValueTable::getList(
			array(
					'filter' => array(
							"USER_PROPS_ID" => $idBuyers,
							'ORDER_PROPS_ID' => array_merge($innProps,$orgProps)
					),
					"select" => array("ORDER_PROPS_ID",'USER_PROPS_ID','VALUE')
			)
		);
		while($prop = $rs->Fetch())
		{
			if(in_array($prop['ORDER_PROPS_ID'],$innProps))
			{
				$arResult['BUYERS'][$prop['USER_PROPS_ID']]['INN'] = $prop['VALUE'];
			}
			if(in_array($prop['ORDER_PROPS_ID'],$orgProps))
			{
				$arResult['BUYERS'][$prop['USER_PROPS_ID']]['ORG'] = $prop['VALUE'];
			}
		}
		foreach($ptBuyers as $idBuyer => $pt)
		{
			$arResult['BUYERS'][$idBuyer]['PERSON_TYPE_ID'] = $pt;
		}
	}
	
	$arResult['TRUE_PT'] = false;
	foreach($arResult["PERSON_TYPE"] as $pt)
	{
		if($pt['CHECKED'] == 'Y' && in_array($pt['ID'],$arParams['BUYER_PERSONAL_TYPE']))
		{
			$arResult['TRUE_PT'] = true;
		}
	}
	if(!$arResult['BUYERS'])
	{
		$arResult['TRUE_PT'] = false;
	}
	
	foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $idProfile => $profile)
	{
		if($profile['CHECKED'] == 'Y' && $arResult['BUYERS'][$idProfile])
		{
			$arResult['BUYERS'][$idProfile]['CHECKED'] = 'Y';
			break;
		}
	}
}

?>