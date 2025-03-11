<?php
			function getFinalPriceInCurrency($item_id, $sale_currency = 'UAH') {
			    global $USER;			
			    // Do item have offers?
			    if(CCatalogSku::IsExistOffers($item_id)) {
			
			        // Find price in offers
			        $res = CIBlockElement::GetByID($item_id);
			
			        if($ar_res = $res->GetNext()) {
			
			            if(isset($ar_res['IBLOCK_ID']) && $ar_res['IBLOCK_ID']) {
			
			                // Find all offers
			                $offers = CIBlockPriceTools::GetOffersArray(array(
			                    'IBLOCK_ID' => $ar_res['IBLOCK_ID'],
			                    'HIDE_NOT_AVAILABLE' => 'Y',
			                    'CHECK_PERMISSIONS' => 'Y'
			                ), array($item_id), null, null, null, null, null, null, array('CURRENCY_ID' => $sale_currency), $USER->getId(), null);
			
			                foreach($offers as $offer) {
			
			                    $price = CCatalogProduct::GetOptimalPrice($offer['ID'], 1, $USER->GetUserGroupArray(), 'N');
			                    if(isset($price['PRICE'])) {
			
			                        if($price['PRICE']['CURRENCY'] != $sale_currency){
			                            $price['PRICE']['PRICE'] = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE'], $price['PRICE']['CURRENCY'], $sale_currency);
			                            $price['PRICE']['CURRENCY'] = $sale_currency;
			                        }
			
			                        $price['PRICE']['PRICE_WITH_DISCOUNT'] = $price['PRICE']['PRICE'];
			                        $currency_code = $price['PRICE']['CURRENCY'];
			
			                        // Find discounts and calculate price with discounts
			                        $arDiscounts = CCatalogDiscount::GetDiscountByProduct($item_id, $USER->GetUserGroupArray(), "N");
			                        if(is_array($arDiscounts) && sizeof($arDiscounts) > 0) {
			                            $price['PRICE']['PRICE_WITH_DISCOUNT'] = CCatalogProduct::CountPriceWithDiscount($price['PRICE']['PRICE_WITH_DISCOUNT'], $sale_currency, $arDiscounts);
			                        }
			
			                        // Stop cycle, use found value
			                        break;
			                    }			
			                }
			            }
			        }			
			    } else {			
			        // Simple product, not trade offers
			        $price = CCatalogProduct::GetOptimalPrice($item_id, 1, $USER->GetUserGroupArray(), 'N');

			        // Got price?
			        if(!$price || !isset($price['PRICE'])) {
			            return false;
			        }
			
			        if($price['PRICE']['CURRENCY'] != $sale_currency){
			            $price['PRICE']['PRICE'] = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE'], $price['PRICE']['CURRENCY'], $sale_currency);
			   $price['DISCOUNT_PRICE'] = CCurrencyRates::ConvertCurrency($price['DISCOUNT_PRICE'], $price['PRICE']['CURRENCY'], $sale_currency);
			            $price['PRICE']['CURRENCY'] = $sale_currency;  
			        }
			
			        // Change currency code if found
			        if(isset($price['CURRENCY'])) {
			            $currency_code = $price['CURRENCY'];
			        }
			        if(isset($price['PRICE']['CURRENCY'])) {
			            $currency_code = $price['PRICE']['CURRENCY'];
			        }
			  
			        // Get final price
			        $price['PRICE']['PRICE_WITH_DISCOUNT'] = $price['DISCOUNT_PRICE'];
			
			        // Find discounts and calculate price with discounts
			        $arDiscounts = CCatalogDiscount::GetDiscountByProduct($item_id, $USER->GetUserGroupArray(), "N", 2);
			        if(is_array($arDiscounts) && sizeof($arDiscounts) > 0) {
			            $price['PRICE']['PRICE_WITH_DISCOUNT'] = CCatalogProduct::CountPriceWithDiscount($price['PRICE']['PRICE_WITH_DISCOUNT'], $sale_currency, $arDiscounts);
			        }			
			    }			
			    // Convert to sale currency if needed
			    if($currency_code != $sale_currency) {
			        $price['PRICE']['PRICE_WITH_DISCOUNT'] = CCurrencyRates::ConvertCurrency($price['PRICE']['PRICE_WITH_DISCOUNT'], $sale_currency, $sale_currency);
			    }
			 
			    return $price;			
			}



$prodsSet = CCatalogProductSet::getAllSetsByProduct($kitsIDs[0], CCatalogProductSet::TYPE_SET);

?>
