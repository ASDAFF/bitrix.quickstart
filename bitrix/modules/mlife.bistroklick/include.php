<?
class CMlifeBistrockick {

	public function getname($var) {
		return htmlspecialcharsEx($var);
	}
	
	public function getref($date_ref,$key,$ref,$count) {
		if ($count==1) return md5($date_ref.$key.$ref);
		if ($count==2) return md5(($date_ref-3600).$key.$ref);
	}
	
	public function checkspam($referer_start,$ref1,$ref2) {
		if($referer_start==$ref1 || $referer_start==$ref2) {
			return 0;
		}
		else {
			return 1;
		}
	}
	
	public function mlife_bistroklick_macros_replace($arFields, $template) {
		if (is_array($arFields) && count($arFields)==0) return $template;
		foreach($arFields as $key=>$value) {
			$template = str_replace('#'.$key.'#',$value,$template);
		}
		return $template;
	}
	
	public function fieldarr($var1,$var2,$var3,$var4,$var5,$var6,$var7) {
		$arFields = array(
		   "LID" => $var1,
		   "PERSON_TYPE_ID" => $var2,
		   "PAYED" => "N",
		   "CANCELED" => "N",
		   "STATUS_ID" => "N",
		   "PRICE" => $var3,
		   "CURRENCY" => $var4,
		   "USER_ID" => $var5,
		   /*"PAY_SYSTEM_ID" => 3,
		   "DELIVERY_ID" => 2,*/
		   //TODO можно подумать и добавить выбор доставки и оплаты
		   "USER_DESCRIPTION" => $var6,
		   "COMMENTS" => $var7
		);
		return $arFields;
	}
	
	public function formatTovar($product_id,$arResult,$arProduct,$offer_code) {
		if(isset($arResult['OFFERS']) && is_array($arResult['OFFERS']) && count($arResult['OFFERS'])>0 && $offer_code>0) {
	
		foreach($arResult['OFFERS'] as $arOffer) {
			if($offer_code==$arOffer['ID']){
				$offerAr = $arOffer;
				break;
			}
		}
		
			$arProduct["ID"] = $offerAr['ID'];
			$arProduct["LID"] = $offerAr['LID'];
			$arProduct['CATALOG_PRICE_ID_1'] = $offerAr['PRICES'][0]['ID'];
			$arProduct['CATALOG_PRICE_1'] = $offerAr['PRICES'][0]['DISCOUNT_PRICE'];
			$arProduct['CATALOG_CURRENCY_1'] = $offerAr['PRICES'][0]['CURRENCY'];
			$arProduct['NAME'] = $offerAr['NAME'];
			$arProduct['DETAIL_PAGE_URL'] = $arResult['TOVAR']['DETAIL_PAGE_URL'];
			$arProduct['CATALOG_WEIGHT'] = 0;
			if(isset($offerAr['PROP']) && is_array($offerAr['PROP'])) {
				$arProduct['PROP'] = $offerAr['PROP'];
			}
	
		}elseif($product_id>0) {
			
			$arProduct["ID"] = $arResult['TOVAR']['ID'];
			$arProduct["LID"] = $arResult['TOVAR']['LID'];
			$arProduct['CATALOG_PRICE_ID_1'] = $arResult['TOVAR']['PRICES'][0]['ID'];
			$arProduct['CATALOG_PRICE_1'] = $arResult['TOVAR']['PRICES'][0]['DISCOUNT_PRICE'];
			$arProduct['CATALOG_CURRENCY_1'] = $arResult['TOVAR']['PRICES'][0]['CURRENCY'];
			$arProduct['NAME'] = $arResult['TOVAR']['NAME'];
			$arProduct['DETAIL_PAGE_URL'] = $arResult['TOVAR']['DETAIL_PAGE_URL'];
			$arProduct['CATALOG_WEIGHT'] = 0;
			
		}
		
		return $arProduct;
		
	}
	
	public function getuserforEmail($arResult) {
		$user = new CUser;
		$newuser = false;
		$rsUsers = CUser::GetList(($by="ID"), ($order="desc"), array("EMAIL" => $arResult['SEND']['email']), array('SELECT'=> array(),'NAV_PARAMS'=> array('nPageSize'=>1),"FIELD" => array('ID')));
		if($arUser = $rsUsers->Fetch()) /*mprint($arUser); die();*/$newuser = $arUser['ID'];
		return $newuser;
	}
	
	public function getFieldsOrder($arProduct,$newuser,$ORDER_ID) {
			$arFields = array(
				  "PRODUCT_ID" => $arProduct['ID'],
				  "PRODUCT_PRICE_ID" => $arProduct['CATALOG_PRICE_ID_1'],
				  "PRICE" => $arProduct['CATALOG_PRICE_1'],
				  "CURRENCY" => $arProduct['CATALOG_CURRENCY_1'],
				  "QUANTITY" => 1,
				  "LID" => $arProduct["LID"],
				  "WEIGHT" => $arProduct['CATALOG_WEIGHT'],
				  "DELAY" => "N",
				  "CAN_BUY" => "Y",
				  "NAME" => $arProduct['NAME'],
				  "MODULE" => "mlife_bistroklick",
				  "NOTES" => "",
				  "DETAIL_PAGE_URL" => $arProduct['DETAIL_PAGE_URL'],
				  "FUSER_ID" => $newuser,
				  "ORDER_ID" => $ORDER_ID
			);
			return $arFields;
	}
	

}
?>