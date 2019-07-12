<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class PriceDiscount
{
	
	public static $arDiscounts = array();
	
	public static $type = false; //если обработчик вызван при добавлении или изменении корзины тут будет 'basket'
	
	public static function getDiscountProducts($arProductsPrices, $iblock = false, $arGroups = false, $siteId=null){
		
		$prodIds = array_keys($arProductsPrices);
		
		//получаем инфоблоки товаров
		if(!$iblock) {
			
			$arProductIblock = array();
			
			$res = \Mlife\Asz\ElementTable::getList(
				array(
					'select' => array("IBLOCK_ID","ID"),
					'filter' => array("ID"=>$prodIds),
				)
			);
			$arIb = array();
			while($arProd = $res->Fetch()){
				$arProductIblock[$arProd["ID"]] = $arProd["IBLOCK_ID"];
				$arIb[$arProd["IBLOCK_ID"]] = $arProd["IBLOCK_ID"];
				$iblock = $arProd["IBLOCK_ID"];
			}
			if(count($arIb)>1){
				$iblock = false;
			}else{
				$arIb = array();
			}
		}
		
		//получаем категории товаров
		$res = \Mlife\Asz\ElementSectionTable::getList(
			array(
				'select' => array("IBLOCK_ELEMENT_ID","IBLOCK_SECTION_ID"),
				'group' => array("IBLOCK_ELEMENT_ID","IBLOCK_SECTION_ID"),
				'filter' => array("IBLOCK_ELEMENT_ID"=>$prodIds),
			)
		);
		$arProductCategories = array();
		$arAllCats = array();
		while($arProd = $res->Fetch()){
			$arProductCategories[$arProd['IBLOCK_ELEMENT_ID']][] = $arProd["IBLOCK_SECTION_ID"];
			$nav = \CIBlockSection::GetNavChain(false, $arProd["IBLOCK_SECTION_ID"]);
			while($arItem = $nav->Fetch()){
				$arProductCategories[$arProd['IBLOCK_ELEMENT_ID']][] = $arItem['ID'];
				$arAllCats[$arItem['ID']] = $arItem['ID'];
			}
			$arAllCats[$arProd["IBLOCK_SECTION_ID"]] = $arProd["IBLOCK_SECTION_ID"];
		}
		
		$arDiscounts = array();
		
		$event = new \Bitrix\Main\Event("mlife.asz", "OnBeforeGetDiscountProducts",array(
			'price'=>$arProductsPrices,
			'iblock' => $iblock,
			'groups' => $arGroups,
			'site' => $siteId,
			));
		$event->send();
		if ($event->getResults()){
			foreach($event->getResults() as $evenResult){
				if($evenResult->getResultType() == \Bitrix\Main\EventResult::SUCCESS){
					$setdiscount = $evenResult->getParameters();
					if($setdiscount) {
						$arDiscounts = $setdiscount;
					}
				}
			}
		}
		
		if(!$arDiscounts) {
			
			$arDiscounts = array();
			
			//получаем все доступные скидки для инфоблоков, категорий, товаров
			//TODO заменить нули на null (CATEGORY_ID), проверить на кой сделал там ноль, возможно по другому сложно
			$res = \Mlife\Asz\DiscountTable::getList(
				array(
					'select' => array("*"),
					'filter' => array(
						array(
							"LOGIC" => "OR",
							array("IBLOCK_ID"=>(!$iblock) ? $arIb : $iblock, "CATEGORY_ID"=>"0", "PRODUCT_ID"=> false),
							array("CATEGORY_ID"=> $arAllCats, "PRODUCT_ID"=> false),
							array("CATEGORY_ID"=> "0", "PRODUCT_ID"=> $prodIds),
						),
						"ACTIVE" => "Y",
						"<DATE_START" => new \Bitrix\Main\Type\DateTime(),
						">DATE_END" => new \Bitrix\Main\Type\DateTime(),
					),
					'order' => array("PRIOR"=>"DESC")
				)
			);
			
			$sort = 100;
			while($arDiscount = $res->Fetch()){
				$sort = $sort+10;
				if(!empty($arDiscount["GROUPS"]) && $arGroups){
					foreach($arGroups as $gid){
						if(in_array($gid,$arDiscount["GROUPS"])){
							$arDiscounts[$sort] = $arDiscount;
							break;
						}
					}
				}else{
					$arDiscounts[$sort] = $arDiscount;
				}
			}
		
		}
		
		$event = new \Bitrix\Main\Event("mlife.asz", "OnAfterGetDiscountProducts",array('discounts'=>$arDiscounts));
		$event->send();
		if ($event->getResults()){
			foreach($event->getResults() as $evenResult){
				if($evenResult->getResultType() == \Bitrix\Main\EventResult::SUCCESS){
					$arDiscounts = $evenResult->getParameters();
				}
			}
		}
		
		self::$arDiscounts = $arDiscounts;
		
		//если нет скидок возвращаем цены
		if(empty($arDiscounts)){
			$finPrice = array();
			foreach($arProductsPrices as $prodId=>$price){
				$finPrice[$prodId] = array(
					'PRICE' => $price,
					'DISCOUNT' => "0.00",
					'DISCOUNT_PRICE' => $price,
					'DISCOUNTID' => false,
				);
			}
			return $finPrice;
		}
		
		$finPrice = array();
		//расчитываем скидку для товаров
		foreach($prodIds as $prodId){
			foreach($arDiscounts as $discount){
				if(!is_array($finPrice[$prodId])) {
					$pricerr = $arProductsPrices[$prodId];
				}else{
					$pricerr = $finPrice[$prodId];
				}
				if($discount["PRODUCT_ID"]==$prodId){
					if($skid = self::getDiscountValue($pricerr,$discount,$prodId,$siteId)){
						$finPrice[$prodId] = $skid;
						if($discount['PRIORFIX']=="Y") break;
					}
				}elseif($discount["CATEGORY_ID"]>0 && !$discount["PRODUCT_ID"]){
					if(in_array($discount["CATEGORY_ID"],$arProductCategories[$prodId])){
						if($skid = self::getDiscountValue($pricerr,$discount,$prodId,$siteId)){
							$finPrice[$prodId] = $skid;
							if($discount['PRIORFIX']=="Y") break;
						}
					}
				}elseif($discount['IBLOCK_ID'] && !$iblock && !$discount["PRODUCT_ID"]){
					if($discount["IBLOCK_ID"]==$arProductIblock[$prodId]){
						if($skid = self::getDiscountValue($pricerr,$discount,$prodId,$siteId)){
							$finPrice[$prodId] = $skid;
							if($discount['PRIORFIX']=="Y") break;
						}
					}
				}elseif($iblock && $discount['IBLOCK_ID'] && !$discount["PRODUCT_ID"]){
					if($discount["IBLOCK_ID"]==$iblock){
						if($skid = self::getDiscountValue($pricerr,$discount,$prodId,$siteId)){
							$finPrice[$prodId] = $skid;
							if($discount['PRIORFIX']=="Y") break;
						}
					}
				}
			}
		}
		
		return $finPrice;
		
	}
	
	public static function getDiscountValue($arProd,$discount,$el,$siteId){
		
		if(!is_array($arProd)) {
			$arProd = array(
				'PRICE' => $arProd,
				'DISCOUNT' => "0.00",
				'DISCOUNT_PRICE' => $arProd,
				'DISCOUNTID' => false,
			);
		}
		
		if($discount["TIP"]==1){ //сумма
			$discountVal = $discount['VALUE'];
			if($discountVal<=0) return false;
		}elseif($discount["TIP"]==2){ //процент
			$discountVal = ($arProd['PRICE']*$discount['VALUE'])/100;
			if(($discountVal>$discount['MAXSUMM']) && $discount['MAXSUMM']>0) $discountVal = $discount['MAXSUMM'];
			if($discountVal<=0) return false;
		}elseif($discount["TIP"]==3){ //тип
			$discountVal = 0;
			$arProdBasePrice = \Mlife\Asz\CurencyFunc::getPriceBase(array(intval($discount['VALUE'])),array($el),$siteId);
			if(is_array($arProdBasePrice[$el])) $discountVal = $arProd['PRICE'] - $arProdBasePrice[$el]["VALUE"];
			if($discountVal<=0) return false;
		}
		$discountVal = round($discountVal,2);
		$arProd['DISCOUNT'] = $arProd['DISCOUNT'] + $discountVal;
		$arProd['DISCOUNTID'][] = $discount["ID"];
		$arProd['DISCOUNT_PRICE'] = $arProd['PRICE'] - $arProd['DISCOUNT'];
		
		return $arProd;
		
	}
	
}
?>