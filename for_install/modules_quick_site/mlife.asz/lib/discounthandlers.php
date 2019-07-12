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

class DiscountHandlers {

	public static function BasketOnBeforeAdd(Entity\Event $event){
		
		$result = new Entity\EventResult();
		
		if(\Mlife\Asz\BasketTable::$discountHandler){
			
			\Mlife\Asz\PriceDiscount::$type = 'basket';
			
			$fields = $event->getParameter("fields");
			
			$modify = array();
			
			global $USER;
			
			if(!isset($fields['DISCOUNT_VAL'])){
				
				$arDiscount = \Mlife\Asz\PriceDiscount::getDiscountProducts(
					array($fields["PROD_ID"]=>$fields['PRICE_VAL']),false,
					$USER->GetUserGroupArray(),
					SITE_ID
				);
				if(isset($arDiscount[$fields["PROD_ID"]]['DISCOUNT'])){
					$modify['DISCOUNT_VAL'] = $arDiscount[$fields["PROD_ID"]]['DISCOUNT'];
					$modify['DISCOUNT_CUR'] = \Mlife\Asz\CurencyFunc::getBaseCurency(SITE_ID);
				}
				
			}
			
			$result->modifyFields($modify);
			
			\Mlife\Asz\PriceDiscount::$type = false;
		
		}
		
		return $result;
		
	}
	
	public static function BasketOnBeforeUpdate(Entity\Event $event){
		
		$result = new Entity\EventResult();
		
		if(\Mlife\Asz\BasketTable::$discountHandler){
			
			\Mlife\Asz\PriceDiscount::$type = 'basket';
			
			$id = $event->getParameter("id");
			$id = $id["ID"];
			
			$fields = $event->getParameter("fields");
			if(!isset($fields["PROD_ID"])){
				$fields = \Mlife\Asz\BasketTable::getById($id)->Fetch();
			}
			
			$modify = array();
			
			global $USER;
			
			$arDiscount = \Mlife\Asz\PriceDiscount::getDiscountProducts(
				array($fields["PROD_ID"]=>$fields['PRICE_VAL']),false,
				$USER->GetUserGroupArray(),
				SITE_ID
			);
			if(isset($arDiscount[$fields["PROD_ID"]]['DISCOUNT'])){
				$modify['DISCOUNT_VAL'] = $arDiscount[$fields["PROD_ID"]]['DISCOUNT'];
				$modify['DISCOUNT_CUR'] = \Mlife\Asz\CurencyFunc::getBaseCurency(SITE_ID);
			}
			
			$result->modifyFields($modify);
			
			\Mlife\Asz\PriceDiscount::$type = false;
			
		}
		
		return $result;
		
	}

}