<?
namespace MHT;
use WP;

foreach(array(
			'iblock',
			'catalog',
			'sale',
			'askaron.ibvote'
		) as $module){
	\CModule::IncludeModule($module);
}

global $DB;
$DB->Query("
	CREATE TABLE IF NOT EXISTS `mht_favorites` (
	  `ID` int(11) NOT NULL AUTO_INCREMENT,
	  `GOOD_ID` int(11) NOT NULL,
	  `USER_ID` varchar(255) NOT NULL,
	  PRIMARY KEY (`ID`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;
");

class Product{
	function __construct($fields, $properties = null, $arParams = array()){
		if(!$properties){
			$properties = $fields['PROPERTIES'];
		}
		$this->fields = $fields;
		$this->properties = $properties;
		$this->arParams = $arParams;
        

		$this->sBaseUnit =  $this->prop("CML2_BASE_UNIT");
		if (empty($this->sBaseUnit)) $this->sBaseUnit = "шт";

		// $this->log();
	}


	function getFinalPriceInCurrency($item_id, $sale_currency = 'RUB') {

		global $USER;

		$currency_code = 'RUB';

		// Проверяем, имеет ли товар торговые предложения?
		if(\CCatalogSku::IsExistOffers($item_id)) {

			// Пытаемся найти цену среди торговых предложений
			$res = \CIBlockElement::GetByID($item_id);

			if($ar_res = $res->GetNext()) {

				if(isset($ar_res['IBLOCK_ID']) && $ar_res['IBLOCK_ID']) {

					// Ищем все тогровые предложения
					$offers = \CIBlockPriceTools::GetOffersArray(array(
						'IBLOCK_ID' => $ar_res['IBLOCK_ID'],
						'HIDE_NOT_AVAILABLE' => 'Y',
						'CHECK_PERMISSIONS' => 'Y'
					), array($item_id), null, null, null, null, null, null, array('CURRENCY_ID' => $sale_currency), $USER->getId(), null);

					foreach($offers as $offer) {

						$price = \CCatalogProduct::GetOptimalPrice($offer['ID'], 1, $USER->GetUserGroupArray(), 'N');
						if(isset($price['PRICE'])) {

							$final_price = $price['PRICE']['PRICE'];
							$currency_code = $price['PRICE']['CURRENCY'];

							// Ищем скидки и высчитываем стоимость с учетом найденных
							$arDiscounts = \CCatalogDiscount::GetDiscountByProduct($item_id, $USER->GetUserGroupArray(), "N");
							if(is_array($arDiscounts) && sizeof($arDiscounts) > 0) {
								$final_price = \CCatalogProduct::CountPriceWithDiscount($final_price, $currency_code, $arDiscounts);
							}

							// Конец цикла, используем найденные значения
							break;
						}

					}
				}
			}

		} else {

			// Простой товар, без торговых предложений (для количества равному 1)
			$price = \CCatalogProduct::GetOptimalPrice($item_id, 1, $USER->GetUserGroupArray(), 'N');

			// Получили цену?
			if(!$price || !isset($price['PRICE'])) {
				return false;
			}

			// Меняем код валюты, если нашли
			if(isset($price['CURRENCY'])) {
				$currency_code = $price['CURRENCY'];
			}
			if(isset($price['PRICE']['CURRENCY'])) {
				$currency_code = $price['PRICE']['CURRENCY'];
			}

            // Получаем итоговую цену
            if ($price['DISCOUNT_PRICE']) {
                $final_price = $price['DISCOUNT_PRICE'];
            } else {

                $final_price = $price['PRICE']['PRICE'];
                // Ищем скидки и пересчитываем цену товара с их учетом
                $arDiscounts = \CCatalogDiscount::GetDiscountByProduct($item_id, $USER->GetUserGroupArray(), "N", 2);
                if (is_array($arDiscounts) && sizeof($arDiscounts) > 0) {
                    $final_price = \CCatalogProduct::CountPriceWithDiscount($final_price, $currency_code, $arDiscounts);
                }
            }

		}

		// Если необходимо, конвертируем в нужную валюту
		if($currency_code != $sale_currency) {
			$final_price = \CCurrencyRates::ConvertCurrency($final_price, $currency_code, $sale_currency);
		}

		return $final_price;

	}

	static function byID($id){
		$product = null;
		\WP::elements(array(
			'filter' => array(
				'ID' => $id,
				'IBLOCK_ID' => $iblock
			),
			'each' => function($f, $p) use (&$product){
				$product = new Product($f, $p);
				return false;
			}
		));
		return $product;
	}

	static function byIDActive($id){
		$product = null;
		\WP::elements(array(
			'filter' => array(
				'ID' => $id,
				'IBLOCK_ID' => $iblock,
				'ACTIVE'	=> 'Y'
			),
			'each' => function($f, $p) use (&$product){
				$product = new Product($f, $p);
				return false;
			}
		));
		return $product;
	}

	function prop($name){
		return $this->properties[$name]['VALUE'];
	}

	private $cache = array();
	function get($name, $index = null){
		if(isset($this->cache[$name])){
			$result = $this->cache[$name];
		}
		else{
			$result = $this->_get($name);
			$this->cache[$name] = $result;
		}

		if($index === null){
			return $result;
		}
		return $result[$index];
	}

	static function getSelect(){
		$result = array();
		foreach(self::$getData as $i => $a){
			$result[$i] = array();
			foreach($a as $j => $v){
				$result[$i][] = $v;
			}
		}

		$result['f'] = array_merge($result['f'], array(
			'CODE',
			'DETAIL_PICTURE',
			'PREVIEW_PICTURE',
		));

		$result['p'] = array_merge($result['p'], array(
			'IS_OFFER',
			'IS_FROM_GERMAN',
			'IS_NEW',
			'IS_IN_STOCK'
		));

		return $result;
	}

	private static $getData = array(
		'f' => array(
			'active' => 'ACTIVE',
			'name' => 'NAME',
			'description' => 'DETAIL_TEXT',
			'short-description' => 'PREVIEW_TEXT',
			'link' => 'DETAIL_PAGE_URL',
			'iblock' => 'IBLOCK_ID',
			'id' => 'ID',
			'section-id' => 'IBLOCK_SECTION_ID',
			'buy-id' => 'BASKET_ID',
			// 'buy' => 'BUY_URL',
			'buy-amount' => 'QUANTITY',
			'amount' => 'QUANTITY',
			'root-url' => 'LIST_PAGE_URL',
			'delay' => 'DELAY',
			'iblock-code' => 'IBLOCK_CODE'
		),
		'p' => array(
			'article' => 'CML2_ARTICLE',
			'traits' => 'CML2_TRAITS',
			'brand' => 'CML2_MANUFACTURER',
			'rating-people' => 'vote_count'
		)
	);

	private $productData = null;
	function getProductData(){
		if($this->productData == null){
			\CModule::IncludeModule('catalog');
			$this->productData = \CCatalogProduct::GetByID($this->get('id'));
		}
		return $this->productData;
	}

	function _get($name){
		foreach(array(
					array(
						'f',
						'fields'
					),
					array(
						'p',
						'properties',
						'VALUE'
					)
				) as $a){
			list($i, $j, $n) = $a;
			$o = self::$getData[$i];
			if(!isset($o[$name])){
				continue;
			}

			$k = $o[$name];
			$o = $this->{$j};
			$o = $o[$k];

			if($n){
				return $o[$n];
			}
			return $o;
		}

		switch($name){
			case 'total-amount':
				$data = $this->getProductData();
				return $data['QUANTITY'];
			case 'faving':
				return $this->get('fav-id') > 0;

			case 'fav-id':
/*				return WP::bit(array(
					'of' => 'basket',
					'f' => array(
						'FUSER_ID' => '%BASKET_USER_ID',
						'PRODUCT_ID' => $this->get('id'),
						'DELAY' => 'Y'
					),
					'sel' => 'ID',
					'one' => 'f.ID'
				));*/
				global $DB;
				$user_id = self::fav_user();
				$good_id = $this->get('id');
				$result = $DB->Query("SELECT COUNT(`ID`) as 'ID' FROM `mht_favorites` WHERE `GOOD_ID` = '".$good_id."' AND `USER_ID` = '".$user_id."';");
				$ID = $result->Fetch();
				if($ID["ID"] > 0)
					return $good_id;
				else
					return 0;

				break;
			case 'fav_cache':
				global $DB;
				$products = array();
				$user_id = self::fav_user();
				$result = $DB->Query("SELECT `GOOD_ID` FROM `mht_favorites` WHERE `USER_ID` = '".$user_id."';");
				while($el = $result->Fetch()) {
					$products[] = $el['GOOD_ID'];
				}
				return implode(',', $products);

				break;

			case 'ids-attr':
				return 'data-ids="'.$this->get('id').':'.$this->get('iblock').':'.$this->get('fav-id').'"';

			case 'buy':
				//if(!isset($this->fields['BUY_URL'])){
				$this->fields['BUY_URL'] = $this->get('link').'index.php?action=BUY&id='.$this->get('id');
				//}
				return $this->fields['BUY_URL'];

			case 'offer':
				return $this->properties['IS_OFFER']['VALUE'] == 'Y';

			case 'germany':
				return $this->properties['IS_FROM_GERMAN']['VALUE'] == 'Y';

			case 'new':
				return $this->properties['IS_NEW']['VALUE'] == 'Y';

			case 'compare-url':
				return "/catalog/".$this->get('iblock-code')."/compare/";

			case 'comparing':
				return count($_SESSION['CATALOG_COMPARE_LIST'][$this->get('iblock')]['ITEMS'][$this->get('id')]) > 0;

			case 'can-buy':
				return $this->get('price-num') > 0;

			case 'big-image':
				return $this->getImage('big');

			case 'big-image-hover':
				return $this->getImage('big', 'hover');

			case 'image':
			case 'small-image':
				return $this->getImage('small');

			case 'image-hover':
			case 'small-image-hover':
				return $this->getImage('small', 'hover');

			case 'rating':
				return $this->properties['rating']['VALUE'] * 100 / 5;

			case 'itemcode':
				foreach ($this->properties['CML2_TRAITS']['DESCRIPTION'] as $i => $val) {
					if($val == 'Код')
						return $this->properties['CML2_TRAITS']['VALUE'][$i];
				}
				break;

			/*				case 'old-price-num':
                            case 'price-num':
                                $result = $this->getOneOf(array(
                                    array('FULL_PRICE'),
                                    array('PRICE'),
                                    array('MIN_PRICE', 'VALUE')
                                ));

                                if($result === null){
                                    $this->setPriceData();
                                    $result = $this->fields['PRICE'];
                                }

                                return $result;*/

			case 'old-price-num':
				$result = $this->getOneOf(array(
					array('FULL_PRICE'),
					array('PRICE'),
					array('MIN_PRICE', 'VALUE')
				));

				if($result === null){
					$this->setPriceData();
					$result = $this->fields['PRICE'];
				}

				if(!empty($this->fields["PROPERTIES"])){
					$properties = $this->fields["PROPERTIES"];
				}elseif(!empty($this->fields["CATALOG"]) && !empty($this->fields["CATALOG"]["PROPERTIES"])){
					$properties = $this->fields["CATALOG"]["PROPERTIES"];
				}elseif(!empty($this->properties)){
					$properties = $this->properties;
				}
				
				if(
					!empty($properties) &&
					!empty($properties["SAYT_AKTSIONNYY_TOVAR"]) &&
					$properties["SAYT_AKTSIONNYY_TOVAR"]["VALUE_XML_ID"] == 'true' &&
					!empty($properties["OLD_PRICE_1"]) &&
					!empty($properties["OLD_PRICE_1"]["VALUE"]) &&
					(
						empty($result) ||
						(float)$properties["OLD_PRICE_1"]["VALUE"]>(float)$result
					)
				){
					$result = (float)$properties["OLD_PRICE_1"]["VALUE"];
				}
							
				$old_price = $this->getFinalPriceInCurrency($this->fields['ID']);
				
				if(!empty($this->fields["PROPERTIES"])){
					$properties = $this->fields["PROPERTIES"];
				}elseif(!empty($this->fields["CATALOG"]) && !empty($this->fields["CATALOG"]["PROPERTIES"])){
					$properties = $this->fields["CATALOG"]["PROPERTIES"];
				}elseif(!empty($this->properties)){
					$properties = $this->properties;
				}
				
				if(
					!empty($properties) &&
					!empty($properties["SAYT_AKTSIONNYY_TOVAR"]) &&
					$properties["SAYT_AKTSIONNYY_TOVAR"]["VALUE_XML_ID"] == 'true' &&
					!empty($properties["OLD_PRICE_1"]) &&
					!empty($properties["OLD_PRICE_1"]["VALUE"]) &&
					(
						empty($result) ||
						(float)$properties["OLD_PRICE_1"]["VALUE"]>(float)$result
					)
				){
					$result = (float)$properties["OLD_PRICE_1"]["VALUE"];
				}
				
				if($result == $old_price) {
					$result = '';
				}

				return $result;

			case 'price-num':
				$result = $this->getFinalPriceInCurrency($this->fields['ID']);
				return $result;

			case 'currency':
				$result = $this->getOneOf(array(
					array('MIN_PRICE', 'CURRENCY'),
					array('CURRENCY')
				));
				if($result === null){
					$this->setPriceData();
					$result = $this->fields['CURRENCY'];
				}
				return $result;

			case 'price':
				return $this->formatPrice($this->get('price-num'));

			case 'old-price':
				return $this->formatPrice($this->get('old-price-num'));

			case 'buy-amount-price':
				$price = $this->formatPrice($this->get('price-num'));
				$price = preg_replace("/\D/", "", $price);
				return $this->formatPrice($price * $this->get('buy-amount'));
			case 'buy-amount-price-bitrix':

				return $this->formatPriceDecimals($this->get('price-num') * $this->get('buy-amount'));
                //return $this->formatPriceDecimals(($this->get('price-num') - $this->fields['DISCOUNT_PRICE']) * $this->get('buy-amount'));

			case 'isnew':
				return $this->properties['SAYT_NOVINKA']['VALUE_XML_ID'] == 'true';

			case 'isaction':
				return $this->properties['SAYT_AKTSIONNYY_TOVAR']['VALUE_XML_ID'] == 'true';

			case 'isblackfriday':
				return $this->properties['SAYT_BLACK_FRIDAY_TOVAR']['VALUE_XML_ID'] == 'true';

			case 'isonlyinternet':
				return $this->properties['TOLKO_V_INTERNET_MAGAZINE']['VALUE_XML_ID'] == 'true';
				
			case 'isactive':
				return $this->get('active') == 'Y';

			case 'article':
				return $this->properties['CML2_ARTICLE']['VALUE'];

			case 'iblock-name':
				$res = \CIBlock::GetByID($this->get('iblock'));
				$ar_res = $res->GetNext();
				return $ar_res['NAME'];

			case 'section-path':
				$ob = GetIBlockSectionPath($this->get('iblock'), $this->get('section-id'));
				$arSectionPath = array();
				$arSectionPath[] = array(
					"IBLOCK_ID" => $this->get('id'),
					"NAME" => $this->get('iblock-name'),
					"SECTION_PAGE_URL" => $this->get('root-url')
				);
				while($ar = $ob->GetNext()){
					$arSectionPath[] = $ar;
				}
				return $arSectionPath;
		}

		return null;
	}


	private function setPriceData(){
		$price = \CPrice::GetBasePrice($this->get('id'));
		if(!$price){
			$this->fields['CAN_BUY'] = 'N';
			return;
		}
		$this->fields['PRICE'] = floatval($price['PRICE']);
		$this->fields['CURRENCY'] = $price['CURRENCY'];
	}

	private function getOneOf($indecesList){
		foreach($indecesList as $indeces){
			$o = $this->fields;
			foreach($indeces as $index){
				if(isset($o[$index])){
					$o = $o[$index];
				}
				else{
					$o = null;
					break;
				}
			}
			if(!$o !== null){
				return $o;
			}
		}
		return null;
	}

	function log(){
		\WP::log(array(
			'fields' => $this->fields,
			'properties' => $this->properties,
		));
	}

	function moreFields($fields){
		foreach($fields as $i => $v){
			if(isset($this->fields[$i])){
				continue;
			}
			$this->fields[$i] = $v;
		}
		if(isset($fields['ID']) && isset($fields['PRODUCT_ID'])){
			$this->fields['BASKET_ID'] = $fields['ID'];
		}

		return $this;
	}

	static function formatPrice($price){

	    //если пришла строка вида 3 909 руб.
        if ( is_string($price)) {
            $price = str_replace('руб.','',$price);
            $price = str_replace(' ','',$price);
            $price = floatval($price);
        }

		return number_format(ceil($price), 0, '.', ' ');
	}
    static function formatPriceDecimals($price){
        return number_format(ceil($price), 2, '.', ' ');
    }
    static function formatPriceBitrix($price){
        //CModule::IncludeModule("currency");
        //CModule::IncludeModule("sale");
	    //$price = CCurrencyLang::CurrencyFormat($price, "RUB");
	    return $price;
    }

	function resizeImage($id, $size = 'big'){
		$sizes = array(
			'small' => array(
				'width' => 250,
				'height' => 210
			),
			'big' => array(
				'width' => 465,
				'height' => 330
			),
			'original' => array(
				'width' => 1200,
				'height' => 1200
			),
		);
		
		$image = false;


		$arWaterMark = Array(
			array(
				"name" => "watermark",
				"position" => "center", // Положение
				"type" => "image",
				"size" => "real",
				"file" => $_SERVER["DOCUMENT_ROOT"].'/watermark.png', // Путь к картинке
				"fill" => "exact",
				"alpha_level"=> 10,
			)
		);


		if(!empty($id)){
			if(isset($sizes[$size])){

				if ($size=='big' || $size=='original'){
					//добавляем водный знак
					$image = \CFile::ResizeImageGet($id, array('width'=>$sizes[$size]["width"], 'height'=>$sizes[$size]["height"]), BX_RESIZE_IMAGE_PROPORTIONAL, true,$arWaterMark);

				}else {
					$image = \CFile::ResizeImageGet(
						$id,
						array('width' => $sizes[$size]["width"], 'height' => $sizes[$size]["height"]),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
				}


			}
			if(!$image){
				$r = \CFile::GetFileArray($id);
				if($r){
					$image = array(
						'width' => $r['WIDTH'],
						'height' => $r['HEIGHT'],
						'src' => $r['SRC'],
					);
				}
			}
		}
		if(!$image){
			$image = array(
				'src' => '/local/templates/mht/components/bitrix/catalog/mht/bitrix/catalog.section/.default/images/no_photo.png',
				'alt' => '',
				'width' => 150,
				'height' => 150,
				'default' => true
			);			
		}
		
		return $image;
		
		/*
		if(!empty($id)){
			if(isset($sizes[$size])){
				return \CFile::ResizeImageGet($id, array('width'=>$sizes[$size]["width"], 'height'=>$sizes[$size]["height"]), BX_RESIZE_IMAGE_PROPORTIONAL, true); 		
			}else{
				$r = \CFile::GetFileArray($id);
				return array(
					'width' => $r['WIDTH'],
					'height' => $r['HEIGHT'],
					'src' => $r['SRC'],
				);
			}
		}else{
			return array(
				'src' => '/local/templates/mht/components/bitrix/catalog/mht/bitrix/catalog.section/.default/images/no_photo.png',
				'alt' => '',
				'width' => 150,
				'height' => 150,
				'default' => true
			);
		}*/
		/*
		if(isset($sizes[$size])){
			$hadResized = WP::hasResized(
				$id,
				$sizes[$size]
			); 

			$img = \CFile::ResizeImageGet(
				$id,
				$sizes[$size]
			);

			\WP::get('wideimage');

			if(!$hadResized){
				try{
					$image = \WideImage::load($_SERVER['DOCUMENT_ROOT'].\CFile::GetPath($id));
					$image = $image->resize(
						$sizes[$size]['width'],
						$sizes[$size]['height'],
						'outside'
					);
					//$image = $image->applyFilter(IMG_FILTER_BRIGHTNESS, 5);
					//$image = $image->applyFilter(IMG_FILTER_CONTRAST,  -10);
					$image->saveToFile($_SERVER['DOCUMENT_ROOT'].$img['src'], 90);
				}
				catch(Exception $e){
					\WP::log($e);
				}
			}
			return $img;
		}

		$r = \CFile::GetFileArray($id);
		return array(
			'width' => $r['WIDTH'],
			'height' => $r['HEIGHT'],
			'src' => $r['SRC'],
		);
		*/
	}

	function getImage($size = 'big', $type = 'normal'){
		if($type == 'hover'){
			$id = $this->properties['MORE_PHOTO']['VALUE'][0];
		}
		if(empty($id)){
			$id = $this->fields['DETAIL_PICTURE'];
		}
		$alt = '';

		if(is_array($id)){
			$alt = $id['ALT'];
			$id = $id['ID'];
		}

		if(empty($id)){
			$id = $this->properties['MORE_PHOTO']['VALUE'][0];
		}

		if(empty($id)){
			return array(
				'src' => '/local/templates/mht/components/bitrix/catalog/mht/bitrix/catalog.section/.default/images/no_photo.png',
				'alt' => '',
				'width' => 150,
				'height' => 150,
				'default' => true
			);
		}
		$image = $this->resizeImage($id, $size);
		$image['alt'] = $alt;
		return $image;
	}

	function getHoverImage($path){
		$image = \WideImage\WideImage::load($path);
		$destination = CTempFile::GetFileName($path);
		$image = $image->crop('50%-100', '50%-125', 200, 250);
		$image->saveToFile($destination);
		return $destination;
	}

	function getCharacteristics(){
		$exclude = array(
			'vote_count',
			'vote_sum',
			'rating',
			'IS_OFFER',
			'IS_IN_STOCK',
			'CML2_ARTICLE',
			'CML2_TRAITS',
			'CML2_BASE_UNIT',
			'CML2_TAXES',
			'IS_FROM_GERMAN',
			'IS_SHOW_ON_MAIN',
			'MORE_PHOTO',
			'IS_NEW'
		);

		if(!empty($this->properties))

			$size = array(
				'RAZMEROBSHCHIY' => 'size',
				'EDIZMRAZMERAOBSHCHIY' => 'units'
			);

		$sizes = array();

		$result = array();
		foreach($this->properties as $property){
			if(in_array($property['CODE'], $exclude)){
				continue;
			}
			if(isset($size[$property['CODE']])){
				$sizes[$property['CODE']] = $size;
				continue;
			}
			// \WP::log($property);
			// $property['NAME'] .= '('.$property['CODE'].')';
			$result[] = $property;
		}
		return $result;
	}

	private function addToBasket($additional = null){
		\CModule::IncludeModule("sale");
		$price = $this->get('price-num');
		$currency = $this->get('currency');
		if(
			!$price &&
			is_array($additional) &&
			isset($additional['DELAY']) &&
			$additional['DELAY'] == 'Y'
		){
			//$price = 1;
			$currency = 'RUB';
		}
		$fields = array(
			'PRODUCT_ID' => $this->get('id'),
			'PRICE' => $price,
			'CURRENCY' => $currency,
			'LID' => SITE_ID,
			'NAME' => $this->get('name')
		);


		if(is_array($additional)){
			$fields = array_merge($fields, $additional);
		}
		return \CSaleBasket::Add($fields);
	}

	function buy($amount = 1){
		$this->addToBasket(array(
			'DELAY' => 'N',
			'QUANTITY' => intval($amount)
		));
	}

	function fav_user(){

		global $APPLICATION, $USER;

		$cookie_id = $APPLICATION->get_cookie("USER_FAVORITE_ID");

		if($cookie_id != '') {
			$user_id = $cookie_id;
		} else {
			if($USER->IsAuthorized()) {
				$user_id = $USER->GetID();
			} else {
				$user_id = md5($_SERVER['REMOTE_ADDR'].time());
				$APPLICATION->set_cookie("USER_FAVORITE_ID", $user_id, time()+2592000);
			}
		}
		return $user_id;
	}

	function fav(){
		/*return $this->addToBasket(array(
			'DELAY' => 'Y',
			'QUANTITY' => 0,
			'CAN_BUY' => 'Y'
		));*/

		global $DB, $APPLICATION, $USER;

		$user_id = self::fav_user();

		if($user_id) {
			$good_id = $_REQUEST['id'];
			$action  = $_REQUEST['action'];

			switch($action) {
				case 'fav-add':
					$result = $DB->Query("SELECT COUNT(`ID`) as 'ID' FROM `mht_favorites` WHERE `GOOD_ID` = '".$good_id."' AND `USER_ID` = '".$user_id."';");
					$ID = $result->Fetch();
					if($ID["ID"] == 0) {
						$result = $DB->Query("INSERT INTO `mht_favorites` (`GOOD_ID`,`USER_ID`) VALUES ('".$good_id."','".$user_id."');");
					}
					break;
				case 'fav-remove':
					$result = $DB->Query("DELETE FROM `mht_favorites` WHERE `GOOD_ID` = '".$good_id."' AND `USER_ID` = '".$user_id."';");
					break;
			}
		}
	}

	private $sections = null;
	function getSections(){
		if($this->sections == null){
			$code = '';
			$this->sections = WP::bit(array(
				'of' => 'element-sections',
				'id' => $this->get('id'),
				'skip-filter' => true,
				'map' => function($d, $f) use (&$code){
					$code .= $f['CODE'].'/';
					return array(
						'name' => $f['NAME'],
						'link' => $f['LIST_PAGE_URL'].$code
					);
				}
			));
		}
		return $this->sections;
	}

	function html($type, $data){
		// echo $type;
		ob_start();
		$rating = $this->get('rating');
		if($data['tpl']){
			$id = \WP::getEditElementID($this->get('iblock'), $this->get('id'), $data['tpl'], true);
		}
		$canBuy = $this->get('can-buy');


        /*
         * добавляем скидку
         */
        $intPercent = 0;
        global $USER;

        
        //для детальной отдельно если еще не получилось
        if (!$intPercent) {

            if ($this->get('old-price')) {

                $oldPrice = intval(str_replace(' ','',$this->get('old-price')));
                $newPrice = intval(str_replace(' ','',$this->get('price')));

                if ( $newPrice < $oldPrice ) {
                    $intPercent = round( ( $oldPrice - $newPrice ) / ( $oldPrice / 100 ) );
                }
            }
        }


        /**
         * добавили
         */

		switch($type){
			case 'basket':
				?>
				<?if($this->get("delay") != 'Y'){?>
				<?
				$id_ = $this->get('id');
				$amount = $this->get('buy-amount');

				?>
				<div class="row js-height-fit" data-id="<?=$this->get('buy-id')?>">
					<input type="hidden" id="QUANTITY_<?=$id_?>" name="QUANTITY_<?=$id_?>" value="<?=$amount?>" />

					<div class="col product_name_block">
						<?=$this->html('images-zoom')?>
						<a href="<?=$this->get('link')?>" class="product_name js-to-middle"><?=$this->get('name')?></a>
					</div><!--
								--><div class="col one_price_block">
						<div class="product_price">
							<?
							$old_price = $this->get('old-price');
							if($old_price > 0) {
								?>
								<span class="old-price-block"><?=$old_price?> <span class="rub"><span>рублей</span></span></span><br>
								<?
							}
							?>
							<span class="product_price_value js-price"
                                data-value="<?=$this->get('price-num')?>"
                                data-discount="<?=$this->fields['DISCOUNT_PRICE']?>"
                            ></span> <span class="rub"><span>рублей</span></span>
						</div>
					</div><!--
								--><div class="col count_selector_block">
						<input class="count_selector" id="QUANTITY_INPUT_<?=$id_?>" name="QUANTITY_INPUT_<?=$id_?>" value="<?=$amount?>"><span class="unit"><?=$this->sBaseUnit?></span>
					</div><!--
								--><div class="col all_price_block">
						<div class="product_price"><span class="product_price_value js-price-total"></span> <span class="rub"><span>рублей</span></span></div>
					</div><!--
								--><div class="col remove_block">
						<a href="#" class="remove">&times;</a>
					</div>
				</div>
			<?}?>
				<?
				break;

			case 'images-zoom':
				?><a class="zoom image_zoom" href="<?=$this->get('big-image', 'src')?>">
				<img alt="<?=$this->get('name')?>, артикул <?=$this->get('article')?>" src="<?=$this->get('small-image', 'src')?>" width="50" height="50">
				</a><?
				break;

			case 'images':
				$size = empty($data['size']) ? 'small' : $data['size'];
				$showLink = !(empty($data['nolink']) ? false : !!$data['nolink']);
				$class = 'product_image';
				$attrs = '';
				$zoomy = false;
				if(
					!empty($data['zoomy']) &&
					$data['zoomy'] == true
				){
					$image = $this->getImage('original');
					if(empty($image['default']) || $image['default'] != true){
						$zoomy = true;
						$class .= ' js-zoomy';
					}
				}

				$attrs .= ' class="'.$class.'"';
                foreach($this->properties['MORE_PHOTO']['VALUE'] as $id) {
                    $original = $this->resizeImage($id, 'original');
                    $src[] = $original['src'];
                    $big[] = $this->resizeImage($id);
                }
                $src[] = $image['src'];
				?>
				<div <?=$attrs?>>

					<? if($zoomy){ ?>
						<div class="zoomy js-zoomy-pane">
							<img class="img js-big-img" src="<?=$image['src'];?>" alt="<?=$this->get('name')?>, артикул <?=$this->get('article')?>" data-imgs="<?=implode(";",$src)?>"  itemprop="image">
						</div>
					<? } ?>

                        <? if($showLink){ ?>
                            <a href="<?=$this->get('link')?>" title="<?=$this->get('name')?>">
						<? } ?>

                                <img class="product_image_original" src="<?=$this->get($size.'-image', 'src')?>" alt="анонс <?=$this->get('name')?>, артикул <?=$this->get('article')?>" itemprop="image">
                                <img class="product_image_hover" src="<?=$this->get($size.'-image', 'src')?>" alt="анонс <?=$this->get('name')?>, артикул <?=$this->get('article')?>" itemprop="image">
						        <?/*<img class="product_image_hover" src="<?=$this->get($size.'-image-hover', 'src')?>" alt="">*/?>
                        <? if($showLink){ ?>
					        </a>
				        <? } ?>
				</div>
				<?
				break;

			case 'recent':
				?>
				<div class="product <?=$data['i'] == 5 ? 'six' : ''?>" <?=$this->get('ids-attr')?>>
				<div class="product_wrapper">
					<div class="product_brand"><?=$this->get('brand')?></div>
					<div class="product_description"><?=$this->get('name')?></div>

                    <? if ( $intPercent ) { ?>
                        <div class="status-wrap"><div class="status-DISCOUNT">-<?=$intPercent?>%</div></div>
                    <? } ?>

					<?=$this->html('images')?>
					<?/*<div class="product_assessment_block mini" data-progress="16"><div style="width:35%" class="product_assessment"></div></div>*/?>
					<div class="product_prices_block">
						<div class="product_price_block">
							<div class="product_price"><span class="product_price_value"><?=$this->get('price')?></span><span class="rub"><span>рублей</span></span></div><a class="product_cart" data-id="<?=$this->get('id')?>" href="<?=$this->get('buy')?>" onmousedown="try { rrApi.addToBasket(<?=$this->get('id')?>) } catch(e) {}"></a>
						</div>
					</div>
				</div>
                </div>
				<?
				break;

			case 'order':
				$id_ = $this->get('id');
				$amount = $this->get('buy-amount');
				?>
				<div class="row" data-id="<?=$this->get('buy-id')?>">
					<div class="col product_name_block">
						<?=$this->html('images-zoom')?>
						<a href="<?=$this->get('link')?>" class="product_name"><?=$this->get('name')?></a>
					</div><!--
		                    --><div class="col one_price_block">
						<div class="product_price">
							<?
							$old_price = $this->get('old-price');
							if($old_price > 0) {
								?>
								<span class="old-price-block"><?=$old_price?> <span class="rub"><span>рублей</span></span></span><br>
								<?
							}
							?>

                            <span class="product_price_value js-price" data-value="<?=$this->get('price-num')?>"><?=$this->get('price-num')?></span> <span class="rub"><span>рублей</span></span>

						</div>
					</div><!--
		                    --><div class="col one_price_block">
						<div class="product_price"><?=$amount?> <?=$this->sBaseUnit?></div>
					</div><!--
		                    --><div class="col all_price_block">
						<div class="product_price"><span class="product_price_value js-price-total"><?=$this->get('buy-amount-price-bitrix')?></span> <span class="rub"><span>рублей</span></span></div>
					</div><?/*<!--
		                    --><div class="col remove_block">
		                    	<a href="#" class="remove js-remove-from-basket">&times;</a>
		                    </div>*/?>
				</div>
				<?
				break;

			case 'compare':
				?>
				<div class="col"><div class="product" <?=$this->get('ids-attr')?>>
						<div class="product_name js-fit-1"><a href="<?=$this->get('link')?>"><?=$this->get('name')?></a> <a href="/ajax.php?action=compare-delete&amp;id1=<?=$this->get('iblock').'&amp;id2='.$this->get('id')?>" class="remove">&times;</a></div>
						<?=$this->html('images')?>
						<div class="product_prices_block">
							<div class="product_price_block">
								<div class="product_price"><span class="product_price_value"><?=$this->get('price')?></span><span class="rub"><span>рублей</span></span></div>
								<a href="<?=$this->get('buy')?>" class="product_cart" data-id="<?=$this->get('id')?>" onmousedown="try { rrApi.addToBasket(<?=$this->get('id')?>) } catch(e) {}"></a> </div>
						</div>
					</div></div>
				<?
				break;

			case 'search':
				?><div class="row product"><!--
	                 --><div class="col product_name_block"><!-- 
	                     --><?=$this->html('images-zoom')?><div class="product_name">
					<div class="product_name__category"><ul>
							<?
							$arSections = $this->get('section-path');
							foreach ($arSections as $arSection) {
								?><li><a href="<?= $arSection['SECTION_PAGE_URL']?>"><?= $arSection['NAME'] ?></a></li><?
							}
							?>
						</ul></div>
					<div class="product_name__title"><a href="<?=$this->get('link')?>"><?=$this->get('name')?></a></div>
				</div><!--
	                 --></div><?
				if($price = $this->get('price')){
					?><div class="col one_price_block">
					<div class="product_price">
						<span class="product_price_value"><?=$price?></span>
				                    	<span class="rub">
				                    		<span>рублей</span>
				                    	</span>
						<a href="<?=$this->get('buy')?>" class="product_cart" data-id="<?=$this->get('id')?>" onmousedown="try { rrApi.addToBasket(<?=$this->get('id')?>) } catch(e) {}"></a>
					</div>
					</div>
					<div class="clear"></div><?
				}
				?></div><?
				break;

			case 'rating':

                $ratingCount = $this->get('rating-people') ? $this->get('rating-people') : 0;

				?>
				<div class="product_assessment_block <?=isset($data['class']) ? $data['class'] : ''?> <?=\MHT::voted($this->get('id')) ? 'voted' : ''?>" data-id="<?=$this->get('id')?>" <?=( $ratingCount > 0 ? 'itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"' : '')?>> <div class="product_assessment" style="width:<?=$this->get('rating')?>%"></div>

                    <? if ($ratingCount > 0) { ?>
                    <meta itemprop="worstRating" content="0"/>
                    <meta itemprop="ratingValue" content="<?=$this->get('rating')?>">
                    <meta itemprop="bestRating" content="100"/>
                    <meta itemprop="ratingCount" content="<?=$ratingCount?>"/>
                    <? } ?>

                </div>
				<?/*
				        	if($data['people']){
				        		?><div class="product_share_count"><?=$this->get('rating-people')?></div><?
				        	}*/
				?>
				<?
				break;

			case 'main':
				global $APPLICATION;
				$sections = $this->getSections();
				$category = $sections[0];
				?>

				<? // Нужно для работы гугл аналитики // ?>
				<div class="product-element-js-info" style="display: none;"
					 data-id="<?=$this->get('id')?>"
					 data-name="<?=$this->get('name')?>"
					 data-price="<?=$this->get('price-num')?>"
					 data-category=""
					 data-sku="<?=$this->get('article')?>"
					></div>

				<div class="only-mobile">
					<a class="category-link" href="<?=$category['link']?>"><?=$category['name']?></a>
				</div>

				<div class="h1_block">
					<h1>
						<span><?=$this->get('name')?></span>
                        <meta itemprop="name" content="<?=$this->get('name')?>"/>
			
						<div class="rating">
							<?=$this->html('rating', array(
								'people' => true
							))?>
						</div>
					</h1>
				</div>

                <? if ( $intPercent ) { ?>
                    <div class="status-wrap status-detail"><div class="status-DISCOUNT">-<?=$intPercent?>%</div></div>
                <? } ?>

				<div class="about">
					<div class="product" <?=$this->get('ids-attr')?>>
						
						<div class="product_labels">
							<?if($this->get("isaction")){?><a href="/catalog/offers/" class="product_new">акция</a><?}?>
							<?if($this->get("isnew")){?><a href="/catalog/new/" class="product_new">новинка</a><?}?>
                            <?if($this->get("isonlyinternet")){?><span class="product_new">Только в интернет магазине</span><?}?>
						</div>
						
						<? if($this->get('germany')){ ?>
							<div class="made_in">
								<img alt="" src="/img/catalog-element/made_german.png" width="96" height="21">
							</div>
						<? } ?>


						<?=$this->html('images', array(
							'size' => 'big',
							'nolink' => true,
							'zoomy' => true,
                            'MORE_PHOTO' => $this->properties['MORE_PHOTO']['VALUE']
						))?>

						<div class="product_images">
							<?
							$id_detail = $this->fields['DETAIL_PICTURE']['ID'];
							if($id_detail > 0) {
								$original = $this->resizeImage($id_detail,'original');
								$src = $original['src']; //\CFile::GetPath($id_detail); //
								$big = $this->resizeImage($id_detail);
								?><div class="image" data-href="<?=$src?>" data-big="<?=$big['src']?>"><img alt="<?=$this->get('name')?>, артикул <?=$this->get('article')?>" src="<?=$src?>"></div><?
							}

							foreach($this->properties['MORE_PHOTO']['VALUE'] as $id){
								$original = $this->resizeImage($id,'original');
								//$src = \CFile::GetPath($id); 
								$src = $original['src']; //\CFile::GetPath($id_detail); //
								$big = $this->resizeImage($id);
								?><div class="image" data-href="<?=$src?>" data-big="<?=$big['src']?>"><img alt="<?=$this->get('name')?>, артикул <?=$this->get('article')?>" src="<?=$src?>"></div><?
							}
							foreach($this->properties['VIDEO_LINK_IMAGE']['VALUE'] as $id){
								$img = \CFile::GetFileArray($id);
								?><div class="video" data-href="<?=$img['DESCRIPTION']?>"><img alt="" src="<?=$img['SRC']?>"></div><?
							}
							?>
						</div>

						<?/*
					            <div class="product_tabs">
					                <ul class="product_tabs_link">
					                    <?/*<li class="active"><a href="#">наличие</a></li><!--*//*?>
<!--
					                    --><li class="actve"><a href="#">оплата и доставка</a></li><!--
					                    --><li><a href="#">отзывы</a> <span>12</span></li>
					                </ul>
					                <div class="product_tabs_block">
					                	<div class="product_tab">
					                        <div class="dealers_block">
					                            <div class="dealer four">
					                                <a href="#">
					                                    <div class="dealer_street">серпуховский<br/>вал</div>
					                                    <div class="dealer_build"><span class="dealer_build_number">13</span></div>
					                                    <div class="dealer_time_title">время работы</div>
					                                    <div class="dealer_time">8 - 22</div>
					                                    <div class="dealer_count"><span class="dealer_count_value">2</span> шт.</div>
					                                    <div class="dealer_map"><span>на карте</span></div>
					                                </a>
					                            </div><!--
					                            --><div class="dealer">
					                                <a href="#">
					                                    <div class="dealer_street">мытищи<br/>коммунистическая</div>
					                                    <div class="dealer_build"><span class="dealer_build_number">10</span> к.<span class="dealer_build_number">1</span></div>
					                                    <div class="dealer_time_title">время работы</div>
					                                    <div class="dealer_time">9 - 21</div>
					                                    <div class="dealer_count"><span class="dealer_count_value">248</span> шт.</div>
					                                    <div class="dealer_map"><span>на карте</span></div>
					                                </a>
					                            </div><!--
					                            --><div class="dealer">
					                                <a href="#">
					                                    <div class="dealer_street">ул.<br/>саратовская</div>
					                                    <div class="dealer_build"><span class="dealer_build_number">3</span> к<span class="dealer_build_number">1</span></div>
					                                    <div class="dealer_time_title">время работы</div>
					                                    <div class="dealer_time">8 - 22</div>
					                                    <div class="dealer_count"><span class="dealer_count_value">2</span> шт.</div>
					                                    <div class="dealer_map"><span>на карте</span></div>
					                                </a>
					                            </div>
					                        </div>
					                	</div>
					                    <div class="product_tab">
					                	
					                	</div>
					                    <div class="product_tab">
					                	
					                	</div>
					                </div>
					            </div>
					            */?>

					</div><!--
					        --><div class="description js-buy-holder" data-id="<?=$this->get('id')?>">
						<? if($canBuy){ ?>
							<div class="buttons only-mobile">
								<a href="<?=$this->get('buy')?>" class="product_catalog-element js-buy" onmousedown="try { rrApi.addToBasket(<?=$this->get('id')?>) } catch(e) {}">в корзину</a>
							</div>
							<div class="product_prices_block product_prices_block__mod">
								<?
								$old_price = $this->get('old-price');
								$price = $this->get('price');
								if(($old_price > 0) && ($price != $old_price )) {
									?>
									<div class="product_old_price"><?=$old_price?><span class="rub"> <span>рублей</span></span></div>
									<?
								}
								?>
								<div class="product_price_block" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
									<div class="product_price product_price__mod"><span class="product_price_value" itemprop="price" content="<?=$price?>"><?=$price?> </span><span class="rub"><span>рублей</span></span><meta itemprop="priceCurrency" content="RUB"><meta itemprop="availability" href="http://schema.org/InStock" content="В наличии"></div>
								</div>
							</div>
						<? } else { ?>
							<div class="not-in-stock"  itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								Нет в наличии
                                <meta itemprop="priceCurrency" content="RUB">
                                <meta itemprop="price" content="<?=$price?>">
                                <meta itemprop="availability" href="http://schema.org/OutOfStock" content="Отсутствует">
							</div>

						<? } ?>

						<?
						?>

						<div class="short_link js-compare-switch comparing compare-<?=$this->get('comparing') ? 'yes' : 'no'?> js-fav-switch faving fav-<?=$this->get('faving') ? 'yes' : 'no'?>">
							<ul>
								<?
								$itemcode = $this->get('itemcode');
								if($itemcode) {
									?><li>код: <span><?=$itemcode?></span></li><?
								} ?><li>артикул: <span><?=$this->get('article')?></span></li><!--
					                    <li>
									<a class="js-compare-change compare-yes" href="/ajax.php?action=compare-delete&amp;id1=<?=$this->get('iblock').'&amp;id2='.$this->get('id')?>">убрать из сравнения</a>
									<a class="js-compare-change compare-no" href="<?=$this->get('root-url')?>compare/?action=ADD_TO_COMPARE_RESULT&amp;id=<?=$this->get('id')?>">в сравнение</a>
								</li>

								<li class="compare-yes">
									<a href="<?=$this->get('root-url')?>compare/">к сравнению</a>
								</li>

					                    --><li>
									<a href="#" class="js-fav-change fav-no" data-id="<?=$this->get('id')?>">в избранное</a>
									<a href="#" class="js-fav-change fav-yes" data-id="<?=$this->get('id')?>">убрать из избранного</a>
								</li><!--data-id="<?=$this->get('fav-id')?>"

					                    	--><li class="fav-yes">
									<a href="/personal/favorite/">к избранному</a>
								</li>
							</ul>
						</div>

						<? if($canBuy){ ?>
							<div class="amount">
								<input class="count_selector js-amount" data-max="<?=$this->get('total-amount')?>" value="1"><span class="unit"><?=$this->sBaseUnit?></span>
							</div>
							<div class="buttons not-mobile">
								<a href="<?=$this->get('buy')?>" class="product_catalog-element js-buy async-buy" onmousedown="try { rrApi.addToBasket(<?=$this->get('id')?>) } catch(e) {}">в корзину</a>
								<!--<a href="#" class="one-click" data-hayhop="#one_click_buy" data-title="Заказать в один клик">Заказать в один клик</a>-->
								<div id="one_click_buy" style="display:none">
									<?
									$form = \MHT::form('one-click');
									$form->getField('product')->setValue($this->get('id'));
									$form->getField('price')->setValue($this->get('price'));
									$form->getField('code')->setValue($this->get('itemcode'));
									$form->getField('productname')->setValue($this->get('name'));
									$form->getField('count')->setValue(1);
									echo $form->html();
									?>
								</div>
							</div>
						<? } else { ?>
							<div class="buttons not-mobile">
								<a href="#" class="product_catalog-element notify" data-hayhop="#notify" data-title="Сообщить о поступлении">
									Сообщить о поступлении
								</a>
								<div id="notify" style="display:none">
									<?
									$form = \MHT::form('notify');
									$form->getField('product')->setValue($this->get('id'));
									$form->getField('price')->setValue($this->get('price'));
									$form->getField('code')->setValue($this->get('itemcode'));
									$form->getField('productname')->setValue($this->get('name'));
									echo $form->html();
									?>
								</div>
							</div>
						<? } ?>
						<div class="description_text" data-text-cut data-text-cut-show="Показать описание целиком" data-text-cut-hide="Свернуть описание" data-text-cut-lines="5">
							<p itemprop="description"><?=$this->get('description')?></p>
						</div>
						<div class="features">
							<table id="product_properties_table">
								<?
								$i = 0;
								$predef = array(
									'Y' => 'да',
									'N' => 'нет',
								);

								foreach($this->getCharacteristics() as $property){
																		
									$value = $property['VALUE'];

									if(
										!$value ||
										preg_match('/^CML2_/', $property['NAME']) ||
										preg_match('/^CML2_BAR_CODE/', $property['CODE']) ||
										preg_match('/^MARKET/', $property['CODE']) ||
										preg_match('/^Сайт_/iU', $property['NAME']) ||
										preg_match('/^Импортер/iU', $property['NAME']) ||
										preg_match('/^Производитель и адре/iU', $property['NAME']) ||
										preg_match('/^Китай/iU', $property['VALUE']) ||
										preg_match('/^Прием претен/iU', $property['NAME']) ||
										preg_match('/^СайтБезСкидки/iU', $property['NAME']) ||
										preg_match('/^Овощи/iU', $property['NAME']) ||
										preg_match('/^Тариф AdmitAd/iU', $property['NAME']) ||
										preg_match('/^Дата произ/iU', $property['NAME']) ||
										preg_match('/^Только/iU', $property['NAME']) ||
										preg_match('/^Старая цена/iU', $property['NAME'])

									){
										continue;
									}

									if(is_array($value)){
										$value = implode(', ',$value);
									}
									if(isset($predef[$value])){
										$value = $predef[$value];
									}
									if($property['CODE'] == 'CML2_MANUFACTURER'){
										$value = '<a href="/brand/'.strtolower(\Cutil::translit($property['VALUE'],"ru")).'/">'.$value.'</a>';
									}

									$property['NAME'] = str_replace(
										array(
											"ДлиннаОбщий",
											"ШиринаОбщий",
											"ВысотаОбщий",
											"МатериалОбщий",
											"ОбъемОбщий"
										),
										array(
											"Длина",
											"Ширина",
											"Высота",
											"Материал",
											"Объем"
										),
										$property['NAME']
									);

								?><tr<?=$i > 19 ? ' class="hidden" style="display:none"' : ''?>><td><span><?=$property['NAME']?></span></td><td><?=$value?></td></tr><?
									$i++;
								}
								?>
							</table>
							<? if($i > 19){ ?>
								<a href="#" class="all_features" onclick="$('#product_properties_table .hidden').show(); $(this).hide(); return false;">все характеристики</a>
							<? } ?>
						</div>
					</div>
				</div>
				<div class="share-block">
					<div class="share-block__item is-vkontakte" data-type="vkontakte"></div>
					<div class="share-block__item is-odnoklassniki" data-type="odnoklassniki"></div>
					<div class="share-block__item is-facebook" data-type="facebook"></div>
					<div class="share-block__item is-twitter" data-type="twitter"></div>
				</div>
				<?
                /*<div class="pluso" data-background="transparent" data-options="medium,round,line,horizontal,counter,theme=02" data-services="vkontakte,odnoklassniki,facebook,twitter"></div>*/?>

                <div class="catalog_page gtinnerpage">
                <div class="catalog_block">
                <div class="catalog">
                <div data-retailrocket-markup-block="58886fad9872e50cf036e819" data-product-id="<?=$this->fields['ID']?>">
                </div>
                    </div>
                    </div>
</div>


                <?
                /*
				$APPLICATION->IncludeComponent('mht:same_products', '', array(
					'IBLOCK_ID' => $this->fields['IBLOCK_ID'],
					'SECTION_ID' => $this->fields['IBLOCK_SECTION_ID'],
					'ELEMENT_ID' => $this->fields['ID'],
                ));
				*/?>
				<?
                //if ( $GLOBALS['USER']->IsAdmin() ) {
					\MHT::showRecentlyViewed('mht_element_detail');
				//}

				break;

			case 'label':
				?>
				<?if($this->get("isaction")){?><a href="/catalog/offers/" class="product_new">акция</a><?}?>
				<?if($this->get("isnew")){?><a href="/catalog/new/" class="product_new">новинка</a><?}?>
                <?if($this->get("isonlyinternet")){?><span class="product_new">Только в интернет магазине</span><?}?>
				<?
				break;

			case 'catalog':
				if($this->get('isactive')){

					$fit = floor($data['i'] / 12) + 1;
					$class = 'product';
					foreach(array(
								array(2, 'even'),
								array(4, 'four'),
								array(3, 'third'),
								array(6, 'six line-last'),
								array(8, 'eight'),
							) as $a){
						list($v, $n) = $a;
						if($data['i'] % $v == $v - 1){
							$class .= ' '.$n;
						}
					}

					$data['line'] = isset($data['line']) ? $data['line'] : 12;
					?><div
					class="product<?/*=$class?> <?if($this->get("isaction")||$this->get("isnew")){?>special<?}*/?>"
					data-fit-group="product-<?=floor($data['i'] / $data['line']) + 1?>"
					data-compare-url="<?= $this->get('compare-url')?>"
                        <?=$this->get('ids-attr')?>

					data-fav-id="<?=$this->get('fav-id')?>"
					>
                    <div class="product_wrapper">
					<?/*<div class="product_brand"><a href="<?=$this->get('link')?>"><?=$this->get('brand')?></a></div>*/?>
                    
					<?if(empty($data['HIDE_ACTION_LABEL']) || $data['HIDE_ACTION_LABEL'] != "Y"){?>
						<?if($this->get("isaction")){?><a href="/catalog/offers/" class="product_new">акция</a><?}?>
					<?}?>
					<?if(empty($data['HIDE_NEW_LABEL']) || $data['HIDE_NEW_LABEL'] != "Y"){?>
						<?if($this->get("isnew")){?><a href="/catalog/new/" class="product_new">новинка</a><?}?>
					<?}?>

					<div data-click-me="<?=$this->get('link')?>" class="product_brand" data-fit-element="brand"><?=$this->get('brand')?></div>
					<div data-click-me="<?=$this->get('link')?>" class="product_name"><?=$this->get('itemcode')?>&nbsp;</div>
					<div data-click-me="<?=$this->get('link')?>" class="product_description" data-fit-element="name"><a class="ablack" href='<?=$this->get('link')?>'><?=$this->get('name')?></a></div>


					<div class="rating">
						<?=$this->html('rating', array(
							'people' => true
						))?>
					</div>
                    
                    
                
                    <? if ( $intPercent ) { ?>
                    <div class="status-wrap"><div class="status-DISCOUNT">-<?=$intPercent?>%</div></div>
                    <? } ?>

					<div data-fit-element="image" data-fit-by=".product_image" class="image-holder">
						<?=$this->html('images')?>
					</div>


					<?=$this->html('rating', array(
					'class' => 'mini'
				))?>

					<div class="product_prices_block">
						<? if($this->get('price-num') > 1){ ?>
							<?=$this->html('price', array(
								'old' => true
							))?>
							<div class="product_price_block">
								<div class="product_price"><span class="product_price_value"><?=$this->get('price')?> </span><span class="rub"><span>рублей</span></span></div>
								<a href="<?=$this->get('buy')?>" class="product_cart" data-id="<?=$this->get('id')?>" onmousedown="try { rrApi.addToBasket(<?=$this->get('id')?>) } catch(e) {}"></a>
							</div>
						<? } else { ?>
							<div class="not_in_stock">Скоро в наличии</div>
						<? } ?>
					</div>
					</div>
					</div>
					<?
				}
				break;

			case 'favorites':

					$fit = floor($data['i'] / 12) + 1;
					$class = 'product';
					foreach(array(
								array(2, 'even'),
								array(4, 'four'),
								array(3, 'third'),
								array(6, 'six line-last'),
								array(8, 'eight'),
							) as $a){
						list($v, $n) = $a;
						if($data['i'] % $v == $v - 1){
							$class .= ' '.$n;
						}
					}

					$data['line'] = isset($data['line']) ? $data['line'] : 12;
					?><div
					class="product<?/*=$class?> <?if($this->get("isaction")||$this->get("isnew")){?>special<?}*/?>"
					data-fit-group="product-<?=floor($data['i'] / $data['line']) + 1?>"
					data-compare-url="<?= $this->get('compare-url')?>"
                        <?=$this->get('ids-attr')?>

					data-fav-id="<?=$this->get('fav-id')?>"
					>
                    <div class="product_wrapper">
					<?/*<div class="product_brand"><a href="<?=$this->get('link')?>"><?=$this->get('brand')?></a></div>*/?>

					<?if(empty($data['HIDE_ACTION_LABEL']) || $data['HIDE_ACTION_LABEL'] != "Y"){?>
						<?if($this->get("isaction")){?><a href="/catalog/offers/" class="product_new">акция</a><?}?>
					<?}?>
					<?if(empty($data['HIDE_NEW_LABEL']) || $data['HIDE_NEW_LABEL'] != "Y"){?>
						<?if($this->get("isnew")){?><a href="/catalog/new/" class="product_new">новинка</a><?}?>
					<?}?>

					<div data-click-me="<?=$this->get('link')?>" class="product_brand" data-fit-element="brand"><?=$this->get('brand')?></div>
					<div data-click-me="<?=$this->get('link')?>" class="product_name"><?=$this->get('itemcode')?>&nbsp;</div>
					<div data-click-me="<?=$this->get('link')?>" class="product_description" data-fit-element="name"><?=$this->get('name')?></div>


					<div class="rating">
						<?=$this->html('rating', array(
							'people' => true
						))?>
						<?=$this->html('rating', array(
							'class' => 'mini'
						))?>
					</div>








                    <? if ( $intPercent ) { ?>
                    <div class="status-wrap"><div class="status-DISCOUNT">-<?=$intPercent?>%</div></div>
                    <? } ?>

					<div data-fit-element="image" data-fit-by=".product_image" class="image-holder">
						<?=$this->html('images')?>
					</div>


					<?=$this->html('rating', array(
					'class' => 'mini'
				))?>

					<div class="product_prices_block">
						<? if($this->get('price-num') > 1){ ?>
							<?=$this->html('price', array(
								'old' => true
							))?>
							<div class="product_price_block">
								<div class="product_price"><span class="product_price_value"><?=$this->get('price')?></span><span class="rub"><span>рублей</span></span></div>
								<a href="<?=$this->get('buy')?>" class="product_cart" data-id="<?=$this->get('id')?>" onmousedown="try { rrApi.addToBasket(<?=$this->get('id')?>) } catch(e) {}"></a>
							</div>
						<? } else { ?>
							<div class="not_in_stock">Скоро в наличии</div>
						<? } ?>
					</div>
					</div>
					</div>





				<?
				break;

			case 'price':
			    $old_price = $this->get('old-price');
                $orig_price = $this->get('price');

				$price = $data['old'] ? $old_price : $orig_price;
				if(!$price){
					break;
				}
				if ($old_price == $orig_price) {
				    break;
                }
				?><div class="product_old_price"><?=$price?> <span class="rub"><span>рублей</span></span></div><?
				break;

			case 'popular':

			default:
				$description = $this->get('short-description');
				?><div class="product <?=($data['i'] == 2 ? 'tree' : '')?>" data-fit-group="product" <?=$this->get('ids-attr')?>>
				<?=$this->html('label')?>
				<a href="<?=$this->get('link')?>">
					<div data-fit-element="title">
						<div class="product_brand"><?=$this->get('brand')?></div>
						<?/*<div class="product_name js-fit-1">
									<?=$this->get('name')?>
								</div>*/?>
						<div class="product_description"><?=$this->get('name')?></div>
					</div>
				</a>
				<?=$this->html('rating')?>

                <? if ( $intPercent ) { ?>
                <div class="status-wrap"><div class="status-DISCOUNT">-<?=$intPercent?>%</div></div>
                <? } ?>

				<div data-fit-element="image" data-fit-by=".product_image">
					<?=$this->html('images')?>
				</div>
				<?
				$oldPrice = $this->get('old-price');
				$price = $this->get('price');
				if($price || $oldPrice){
					?>
					<div class="product_prices_block">
						<? if(($oldPrice > 0) && ($price != $oldPrice )) { ?>
							<div class="product_old_price">
								<?=$oldPrice?><span class="rub"><span>рублей</span></span>
							</div>
						<? } ?>

						<? if($price){ ?>
							<div class="product_price_block">
								<div class="product_price">
													<span class="product_price_value">
														<?=$price?>
													</span>
													<span class="rub">
														<span>рублей</span>
													</span>
								</div>
								<a
									href="<?=$this->get('buy')?>"
									class="product_cart"
									data-id="<?=$this->get('id')?>"
									onmousedown="try { rrApi.addToBasket(<?=$this->get('id')?>) } catch(e) {}"
									></a>
							</div>
						<? } ?>
					</div>
					<?
				}
				?>
				</div><?
				break;
		}
		return ob_get_clean();
	}
}
?>
