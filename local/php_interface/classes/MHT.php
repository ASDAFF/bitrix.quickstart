<?
	
	class MHT{

		public function getBasketItemsAmount($russian = false)
		{
			$n = 0;

			CModule::IncludeModule('sale');

			WP::bit(array(
				'of' => 'basket',
				'f' => array(
					'LID' => SITE_ID,
					'FUSER_ID' => CSaleBasket::GetBasketUserID(true),
					'ORDER_ID' => 'NULL',
					'CAN_BUY' => 'Y',
					'DELAY' => 'N',
					'MODULE' => 'catalog'
				),
				'each' => function($e, $f) use (&$n){
					$n += $f['QUANTITY'];
				}
			));

			if(!$russian){
				return $n;
			}

			return $n.' товар'.WP::russianCountName($n, 'ов', '', 'а');

		}

		static function activateIBlocks(){
			$o = new MHT\IBlocksParser();
			$o->parse();
		}

		private static $defaultWeWillTell = array(
			array('Товары для дома', '/', array(
				array('Ремонт', '/'),
				array('Уборка', '/'),
				array('Готовка', '/', array(
					array('Посуда', '/'),
					array('Техника', '/'),
				)),
				array('Стирка', '/'),
			)),
			array('Товары для сада', '/', array(
				array('Озеленение', '/'),
				array('Удобрение', '/'),
			))
		);

		static function addWeWillTell($data, $parent = 0){
			if(func_num_args() == 0){
				$data = self::$defaultWeWillTell;
			}

			foreach($data as $a){
				list($name, $link, $childs) = $a;
				$id = WP::addElement(array(
					'f' => 'iblock=8; NAME='.$name,
					'p' => 'LINK='.$link.'; PARENT='.$parent,
					'debug' => 1
				));
				if(!empty($childs)){
					self::addWeWillTell($childs, $id);
				}
			}
		}
		static function setBread($data){
			$links = array();

			$links[] = array('Каталог', '/catalog/');
			if($iblock = CIBlock::GetList(array(), array(
				'ID' => $data['iblock']
			))->Fetch()){
				$links[] = array(
					$iblock['NAME'],
					$iblock['LIST_PAGE_URL']
				);
			}

			$list = CIBlockSection::GetNavChain(
				$data['iblock'],
				$data['section'],
				array(
					'NAME',
					'SECTION_PAGE_URL'
				)
			);

			while(($section = $list->GetNext()) !== false){
				$links[] = array(
					$section['NAME'],
					$section['SECTION_PAGE_URL']
				);
			}

			if(isset($data['element'])){
				WP::elements(array(
					'filter' => array(
						'ID' => $data['element']
					),
					'each' => function($element) use (&$links){
						$links[] = array(
							$element['NAME'],
							''
						);
						return false;
					}
				));
			}

			$result = array();
			foreach($links as $a){
				$result[] = array(
					'name' => $a[0],
					'link' => $a[1]
				);
			}

			$GLOBALS['custom-bread'] = $result;
		}

		static function tmpShowAllProps(){
			$data = array();
			MHT::eachCatalogIBlock(function($iblock) use (&$data){
				$properties = array();
				WP::properties(array(
					'sort' => array(
						'NAME' => 'ASC'
					),
					'filter' => array(
						'IBLOCK_ID' => $iblock['ID'],
					),
					'each' => function($p) use (&$properties){
						if(in_array($p['CODE'], array(
							'_1',
							'_2'
						))){
							return;
						}
						$properties[] = array(
							'name' => $p['NAME'],
							'code' => $p['CODE']
						);
					}
				));

				$data[] = array(
					'name' => $iblock['NAME'],
					'code' => $iblock['CODE'],
					'properties' => $properties
				);
			});

			foreach($data as $e){
				echo $e['name'].' | '.$e['code'].'<br/>';
				foreach($e['properties'] as $p){
					echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$p['name'].' | '.$p['code'].'<br/>';
				}
			}
		}
		
		static function afterUpload($iblock){
			self::addNeededProperties($iblock);
			self::setNeededPropertiesValues($iblock);
			self::activateSmartFilters($iblock);
			self::activateSearch($iblock);
			// self::setPrices($iblock);
		}

		static function showRecentlyViewed($tpl = 'mht'){
			global $APPLICATION;
			$APPLICATION->IncludeComponent(
			"bitrix:sale.viewed.product",
			    $tpl,
			    Array(
			        "VIEWED_COUNT" => "6",
			        "VIEWED_NAME" => "Y",
			        "VIEWED_IMAGE" => "Y",
			        "VIEWED_PRICE" => "Y",
			        "VIEWED_CURRENCY" => "default",
			        "VIEWED_CANBUY" => "Y",
			        "VIEWED_CANBASKET" => "Y",
			        "VIEWED_IMG_HEIGHT" => "150",
			        "VIEWED_IMG_WIDTH" => "150",
			        "BASKET_URL" => "/personal/basket.php",
			        "ACTION_VARIABLE" => "action",
			        "PRODUCT_ID_VARIABLE" => "id",
			        "SET_TITLE" => "N"
			    )
			);
		}

		static function showNewProducts($tpl = "mht") {}
		static function showActionProducts($tpl = "mht") {}

		static function addNeededProperties($iblock){
			CModule::IncludeModule('iblock');
			$p = new CIBlockProperty;
			$properties = array(
				array(
					'Новинка',
					'IS_NEW',
					'checkbox'
				),
				array(
					'Акция',
					'IS_OFFER',
					'checkbox'
				),
				array(
					'Есть на складе',
					'IS_IN_STOCK',
					'checkbox'
				),
				array(
					'Показывать на главной',
					'IS_SHOW_ON_MAIN',
					'checkbox'
				),
				array(
					'Сделано в Германии',
					'IS_FROM_GERMAN',
					'checkbox'
				),
				array(
					'Ссылки на видео',
					'VIDEO_LINK_IMAGE',
					'files'
				),
				array(
					'Количество голосов',
					'vote_count',
					'number'
				),
				array(
					'Сумма голосов',
					'vote_sum',
					'number'
				),
				array(
					'Рейтинг',
					'rating',
					'number'
				),
			);

			$exist = array();
			foreach($properties as $a){
				list($name, $code, $type) = $a;
				if(!WP::properties(array(
					'filter' => array(
						'iblock' => $iblock,
						'CODE' => $code
					),
					'each' => function($p){
						return false;
					}
				))){
					$exist[] = $code;
				}
			}

			foreach($properties as $a){
				list($name, $code, $type) = $a;
				if(in_array($code, $exist)){
					continue;
				}
				$f = array(
					"IBLOCK_ID" => $iblock,
					"ACTIVE" => "Y",
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"NAME" =>$name,
					"CODE" => $code,
					"DEFAULT_VALUE" => "",
					"SORT" => 1000
				);
				switch($type){
					case 'checkbox':
						$f = array_merge($f, array(
							"DEFAULT_VALUE" => "N",
							"USER_TYPE" => "SASDCheckbox",
							"USER_TYPE_SETTINGS" => 'a:1:{s:4:"VIEW";a:2:{s:1:"N";s:1:"N";s:1:"Y";s:1:"Y";}}',
						));
						break;

					case 'files':
						$f = array_merge($f, array(
							'PROPERTY_TYPE' => 'F',
							'WITH_DESCRIPTION' => 'Y',
							'MULTIPLE' => 'Y',
							'DEFAULT_VALUE' => '',
							''
						));
						break;

					case 'strings':
						$f = array_merge($f, array(
							'MULTIPLE' => 'Y',
							'DEFAULT_VALUE' => ''
						));
						break;

					case 'number':
						$f = array_merge($f, array(
							"PROPERTY_TYPE" => "N",
						));
						break;
				}
				$p->Add($f);
			}
		}

		static function setNeededPropertiesValues($iblock){
			WP::elements(array(
				'f' => 'iblock='.$iblock,
				'select' => array(
					'f' => 'ID',
					'p' => 'vote_count, vote_sum, rating'
				),
				'each' => function($f, $p, $i){
					if($p['vote_count'] > 0){
						return;
					}
					
					$rating = mt_rand(3, 5);
					$count = mt_rand(15, 35);
					CIBlockElement::SetPropertyValuesEx(
						$f['ID'],
						$f['IBLOCK_ID'],
						array(
							'vote_count' => $count,
							'vote_sum' => $count * $rating,
							'rating' => $rating
						)
					);
				}
			));
		}

		static function activateSmartFilters($iblock){
			CModule::IncludeModule('iblock');
			
			if(CIBlock::GetArrayByID(
				$iblock,
				"SECTION_PROPERTY"
			) !== "Y") {
			    $ib = new CIBlock;
			    $ib->Update(
			    	$iblock,
			    	array(
			    		"SECTION_PROPERTY" => "Y"
			    	)
			    );
			}

			WP::bit(array(
				'of' => 'properties',
				'filter' => array(
					'IBLOCK_ID' => $iblock,
					'CODE' => 'CML2_MANUFACTURER'
				),
				'each' => function($e, $f) use ($iblock){
					$p = new CIBlockProperty;
					if(!$p->Update(
						$f['ID'],
						array(
							'SMART_FILTER' => 'Y',
							'IBLOCK_ID' => $iblock
						)
					)){
						echo $p->LAST_ERROR;
					}
				}
			));
		}

		/*

		static function activateSmartFilters($iblock){
			$ids = array();

			WP::properties(array(
				'filter' => array(
					'IBLOCK_ID' => $iblock,
					'CODE' => 'CML2_MANUFACTURER'
				),
				'each' => function($p) use (&$ids){
					$ids[] = $p['ID'];
				}
			));

			if(empty($ids)){
				return;
			}

			if(CIBlock::GetArrayByID(
				$iblock,
				"SECTION_PROPERTY"
			) !== "Y") {
			    $ib = new CIBlock;
			    $ib->Update(
			    	$iblock,
			    	array(
			    		"SECTION_PROPERTY" => "Y"
			    	)
			    );
			}
			
			$p = new CIBlockProperty;
			foreach($ids as $id){
				$p->Update($id, array(
					'SMART_FILTER' => 'Y',
					'IBLOCK_ID' => $iblock
				));
			}
		}

		*/

		static function activateSearch($iblock){
			WP::bit(array(
				'of' => 'properties',
				'filter' => array(
					'IBLOCK_ID' => $iblock,
					'CODE' => 'CML2_ARTICLE'
				),
				'each' => function($e, $f) use (&$ids){
					$p = new CIBlockProperty;
					$p->Update(
						$f['ID'],
						array(
							'SEARCHABLE' => 'Y'
						)
					);
				}
			));
		}

		static function setRandomNeededPropertiesValues($iblock){
			foreach(array(
				'IS_NEW',
				'IS_OFFER',
			//	'IS_FROM_GERMAN'
			) as $code){
				$properties = array();
				$properties[$code] = 'Y';

				WP::bit(array(
					'of' => 'element',
					'sort' => 'RAND',
					'filter' => 'iblock='.$iblock,
					'max' => 25,
					'sel' => 'ID, IBLOCK_ID',
					'each' => function($d, $f) use (&$properties){
						CIBlockElement::SetPropertyValuesEx(
							$f['ID'],
							$f['IBLOCK_ID'],
							$properties
						);
					}
				));
			}

		}

		private static $formClasses = array(
			'complain' => 'MHT\Forms\Complain',
			'faq' => 'MHT\Forms\Faq',
			'vacancy' => 'MHT\Forms\Vacancy',
			'register' => 'MHT\Forms\Register',
			'forgot' => 'MHT\Forms\Forgot',
			'one-click' => 'MHT\Forms\OneClick',
			'login' => 'MHT\Forms\Login',
			'notify' => 'MHT\Forms\Notify',
			'ware' => 'MHT\Forms\Warehouse',
		);
		static function forms(){
			$forms = array();
			foreach(self::$formClasses as $i => $class){
				$forms[] = self::form($i);
			}
			return $forms;
		}
		static function form($type){
			if(isset(self::$formClasses[$type])){
				$class = self::$formClasses[$type];
				return new $class();
			}
			return null;
		}

		static function getCatalogIDs(){
			$ids = array();
			self::eachCatalogIBlock(function($iblock) use (&$ids){
				$ids[] = $iblock['ID'];
			});
			return $ids;
		}
		static function searchHipHopHTML(){
			global $APPLICATION;
			ob_start();
			?>
				<div class="hihop_search_block">
					<div class="wrapper">
					  	<div class="close"><span>Закрыть</span></div>
					  	<div class="logo-in-search"><img src="<?=SITE_TEMPLATE_PATH?>/images/logotype-black@2x.png" alt="МОСХОЗТОРГ" width="195" height="50" /></div>
					    <div class="field">
					    	<?$APPLICATION->IncludeComponent(
								"bitrix:search.title",
								"mht",
								Array(
									"COMPONENT_TEMPLATE" => ".default",
									"NUM_CATEGORIES" => "1",
									"TOP_COUNT" => "5",
									"ORDER" => "date",
									"USE_LANGUAGE_GUESS" => "Y",
									"CHECK_DATES" => "Y",
									"SHOW_OTHERS" => "N",
									"PAGE" => "#SITE_DIR#search/",
									"SHOW_INPUT" => "Y",
									"INPUT_ID" => "title-search-input",
									"CONTAINER_ID" => "title-search",
									"CATEGORY_0_TITLE" => "",
									"CATEGORY_0" => array("iblock_mht_products"),
									"CATEGORY_0_iblock_mht_products" => array("all"),
									"PRICE_CODE" => array(PRICE_CODE),
									"PRICE_VAT_INCLUDE" => "Y",
									"CONVERT_CURRENCY" => "N",
									"PREVIEW_WIDTH" => "75",
									"PREVIEW_HEIGHT" => "75",
									"CURRENCY_ID" => "RUB"
								)
							);?>
					    	<? /*
							
					    	<form method="get" action="/search/">
					        	<input type="text" placeholder="Поиск" name="q" class="search_field hayhopped" value="">
					        	<input type="submit" class="search_submit" value="">
					        </form>
					        
					        */ ?>
					       </div>
					    <div class="pre_result_list">
					    	<div class="col js-offer">
					        	<h3>Акции</h3>
					            <div class="products">
					            	<div class="product">
					                	<div class="image"><img class="js-image" src=""></div>
					                	<div class="text">
						                	<div class="name js-name"></div>
						                	<div class="price"><span class="js-price"></span> <span class="rub"><span>рублей</span></span></div>
					                	</div>
					                	<a href="#" class="js-link link"></a>
					                	<div class="clear"></div>
					                </div>
					            </div>
					        </div>
					        <div class="col js-new">
					        	<h3>Новинки</h3>
					            <div class="products">
					            	<div class="product">
					                	<div class="image"><img class="js-image" src=""></div>
					                	<div class="text">
						                	<div class="name js-name"></div>
						                	<div class="price"><span class="js-price"></span> <span class="rub"><span>рублей</span></span></div>
					                	</div>
					                	<a href="#" class="js-link link"></a>
					                	<div class="clear"></div>
					                </div>
					            </div>
					        </div>
					        <div class="col js-popular">
					        	<h3>Популярное</h3>
					            <div class="products">
					            	<div class="product">
					                	<div class="image"><img class="js-image" src=""></div>
					                	<div class="text">
						                	<div class="name js-name"></div>
						                	<div class="price"><span class="js-price"></span> <span class="rub"><span>рублей</span></span></div>
					                	</div>
					                	<a href="#" class="js-link link"></a>
					                	<div class="clear"></div>
					                </div>
					            </div>
					        </div>
					    </div>
					</div>
				</div>
			<?
			return ob_get_clean();
		}
		
		static function searchHTML($class = '', $withHipHop = true){
			ob_start();
			?>
				<div class="search_block <?=$class?>">
		            <input type="text" value="" class="search_field" name="q" placeholder="поиск по сайту"><input type="submit" value="" class="search_submit">
		        </div>
				<?
					if($withHipHop){
						echo self::searchHipHopHTML();
					}
				?>
			<?
			return ob_get_clean();
		}

		static function setPrices($iblock = 0){
			if(!$iblock){
				return false;
			}

			CModule::IncludeModule('catalog');
			
			$data = WP::elements(array(
				'f' => 'iblock='.$iblock,
				'select' => 'ID, IBLOCK_ID',
				'map' => function($f, $p, $i, &$e) use (&$data){
					$p = CPrice::GetList(
						array(),
						array('PRODUCT_ID' => $f['ID'])
					)->Fetch();

					CIBlockElement::SetPropertyValuesEx(
						$f['ID'],
						$f['IBLOCK_ID'],
						array(
							'IS_IN_STOCK' => (!empty($p) && floatval($p['PRICE']) > 0) ? 'Y' : 'N'
						)
					);

					if(!$p){
						$e['skip'] = true;
						return;
					}

					return array(
						$f['ID'],
						$p['PRICE'],
						$p['CURRENCY']
					);
				}
			));

			foreach($data as $a){
				CPrice::SetBasePrice($a[0], $a[1], $a[2]);
			}
		}
		static function eachCatalogIBlock($callback){
			CModule::IncludeModule('iblock');

			$list = CIBlock::GetList(array(
				'SORT'=>'ASC',
				'NAME' => 'ASC'
			), array(
				'TYPE' => 'mht_products',
				'ACTIVE' => 'Y'
			));

			while(($iblock = $list->Fetch()) !== false){
				if(!$iblock['LIST_PAGE_URL']){
					continue;
				}
				if($callback($iblock) === false){
					break;
				}
			}
		}

		private static	function checkVote($ELEMENT_ID, $arParams){
			global $APPLICATION;
			global $USER;
			
			$bVoted = false;
			
			if ( intval($ELEMENT_ID) > 0 )
			{
				$db_events = GetModuleEvents("askaron.ibvote", "OnStartCheckVoting");
				while($arEvent = $db_events->Fetch())
				{
					$bEventRes = ExecuteModuleEventEx($arEvent, array($ELEMENT_ID, $arParams) );
					if($bEventRes===false)
					{
						$bVoted = true;
						break;
					}
				}	
		
				if (!$bVoted && $arParams["SESSION_CHECK"] == "Y" )
				{
					$bVoted = (is_array($_SESSION["IBLOCK_RATING"]) && array_key_exists($ELEMENT_ID, $_SESSION["IBLOCK_RATING"]));
				}

				if (!$bVoted && $arParams["COOKIE_CHECK"] == "Y" )
				{	
					$arCookie = Array();
					$strCookie = $APPLICATION->get_cookie("ASKARON_IBVOTE_IBLOCK_RATING");

					if ( strlen( $strCookie ) > 0 )
					{
						$arCookie = unserialize( $strCookie );
					}
						
					$bVoted = (is_array($arCookie) && array_key_exists($ELEMENT_ID, $arCookie))? 1: 0;				
				}			
				
				if (!$bVoted && ( $arParams["IP_CHECK_TIME"] > 0) )
				{	
					if(CModule::IncludeModule("askaron.ibvote"))
					{				
						$bVoted = CAskaronIbvoteEvent::CheckVotingIP($ELEMENT_ID, $_SERVER["REMOTE_ADDR"], $arParams["IP_CHECK_TIME"] );
					}
				}
				
				if (!$bVoted && ( $arParams["USER_ID_CHECK_TIME"] > 0) )
				{	
					if ( $USER->IsAuthorized() )
					{
						if(CModule::IncludeModule("askaron.ibvote"))
						{				
							$bVoted = CAskaronIbvoteEvent::CheckVotingUserId($ELEMENT_ID, $USER->GetID(), $arParams["USER_ID_CHECK_TIME"] );
						}
					}
				}			
			}

			if ( $bVoted )
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}

		static function voted($id){
			CModule::IncludeModule("askaron.ibvote");
			global $USER;
			$time = 86400;
			
			if($uid = $USER->GetID()){
				return CAskaronIbvoteEvent::CheckVotingUserId(
					$id,
					$uid,
					$time
				);
			}

			return CAskaronIbvoteEvent::CheckVotingIP(
				$id,
				$_SERVER["REMOTE_ADDR"],
				$time
			);

		}

		static function vote($data){
			global $APPLICATION;
			global $USER;
			global $DB;
			CModule::IncludeModule('askaron.ibvote');
			$ELEMENT_ID = $data['id'];
			$RATING = $data['vote'];

			$arParams = array(
				'SESSION_CHECK' => 'N',
				'COOKIE_CHECK' => 'Y',
				'IBLOCK_ID' => WP::iblockByElement($ELEMENT_ID),
		        "DISPLAY_AS_RATING" => "rating",
		        "IBLOCK_TYPE" => "mht_products",
		        "ELEMENT_ID" => $ELEMENT_ID,
		        "MAX_VOTE" => "5",
		        "VOTE_NAMES" => array("1","2","3","4","5"),
		        "SET_STATUS_404" => "N",
		        "IP_CHECK_TIME" => "86400",
		        "USER_ID_CHECK_TIME" => "0",
		        "CACHE_TYPE" => "A",
		        "CACHE_TIME" => "36000000",
		        "CACHE_NOTES" => ""
			);
			if ( !($ELEMENT_ID > 0 && $ELEMENT_ID == $arParams["ELEMENT_ID"]) ){
				echo 'bad1';
				return;
			}

			if(!($RATING>0 && $RATING<=$arParams["MAX_VOTE"]))
			{
				echo 'bad2';
				return;
			}

			// not voted
			if( self::checkVote( $ELEMENT_ID, $arParams ) )		 
			{
				echo 'bad3';
				return;
			}

			// set flag "voted" (1.1.0)
			$bVoted = 1;

			if ($arParams["SESSION_CHECK"]=="Y")
			{
				if(!is_array($_SESSION["IBLOCK_RATING"]))
					$_SESSION["IBLOCK_RATING"] = Array();				
					
				$_SESSION["IBLOCK_RATING"][$ELEMENT_ID]=true;
			}
			
			if ($arParams["COOKIE_CHECK"]=="Y")
			{
				$strCookie = $APPLICATION->get_cookie("ASKARON_IBVOTE_IBLOCK_RATING");
				
				if ( strlen( $strCookie ) > 0 )
				{
					$arCookie = unserialize( $strCookie );
				}
				
				if ( !is_array($arCookie) )
					$arCookie = Array();
				
				$arCookie[$ELEMENT_ID] = true;
				$strCookie = serialize($arCookie);
				$APPLICATION->set_cookie("ASKARON_IBVOTE_IBLOCK_RATING", $strCookie);
			}
			
			$rsProperties = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $ELEMENT_ID, "value_id", "asc", array("ACTIVE"=>"Y"));
			$arProperties = array();
			while($arProperty = $rsProperties->Fetch())
			{
				if($arProperty["CODE"]=="vote_count")
					$arProperties["vote_count"] = $arProperty;
				elseif($arProperty["CODE"]=="vote_sum")
					$arProperties["vote_sum"] = $arProperty;
				elseif($arProperty["CODE"]=="rating")
					$arProperties["rating"] = $arProperty;
			}

			$obProperty = new CIBlockProperty;
			$res = true;
			if(!array_key_exists("vote_count", $arProperties))
			{
				$res = $obProperty->Add(array(
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"ACTIVE" => "Y",
					"PROPERTY_TYPE" => "N",
					"MULTIPLE" => "N",
					"NAME" => "vote_count",
					"CODE" => "vote_count",
				));
				if($res)
					$arProperties["vote_count"] = array("VALUE"=>0);
			}
			if($res && !array_key_exists("vote_sum", $arProperties))
			{
				$res = $obProperty->Add(array(
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"ACTIVE" => "Y",
					"PROPERTY_TYPE" => "N",
					"MULTIPLE" => "N",
					"NAME" => "vote_sum",
					"CODE" => "vote_sum",
				));
				if($res)
					$arProperties["vote_sum"] = array("VALUE"=>0);
			}
			if($res && !array_key_exists("rating", $arProperties))
			{
				$res = $obProperty->Add(array(
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"ACTIVE" => "Y",
					"PROPERTY_TYPE" => "N",
					"MULTIPLE" => "N",
					"NAME" => "rating",
					"CODE" => "rating",
				));
				if($res)
					$arProperties["rating"] = array("VALUE"=>0);
			}
			if($res)
			{
				$arProperties["vote_count"]["VALUE"] = intval($arProperties["vote_count"]["VALUE"])+1;
				$arProperties["vote_sum"]["VALUE"] = intval($arProperties["vote_sum"]["VALUE"])+$RATING;
				//rating = (SUM(vote)+31.25) / (COUNT(*)+10)
				$arProperties["rating"]["VALUE"] = round(($arProperties["vote_sum"]["VALUE"]+31.25/5*$arParams["MAX_VOTE"])/($arProperties["vote_count"]["VALUE"]+10),2);
				
				//$db_events = GetModuleEvents("askaron.ibvote", "OnBeforeRatingWrite");
				//while($arEvent = $db_events->Fetch())
				//{
				//	$bEventRes = ExecuteModuleEventEx(
				//		$arEvent, 
				//		array(
				//			$ELEMENT_ID,
				//			$arProperties["vote_count"]["VALUE"],
				//			$arProperties["vote_sum"]["VALUE"],
				//			&$arProperties["rating"]["VALUE"],
				//		) 
				//	);
				//}
				
				$DB->StartTransaction();
				CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $arParams["IBLOCK_ID"], array(
					"vote_count" => array(
						"VALUE" => $arProperties["vote_count"]["VALUE"],
						"DESCRIPTION" => $arProperties["vote_count"]["DESCRIPTION"],
					),
					"vote_sum" => array(
						"VALUE" => $arProperties["vote_sum"]["VALUE"],
						"DESCRIPTION" => $arProperties["vote_sum"]["DESCRIPTION"],
					),
					"rating" => array(
						"VALUE" => $arProperties["rating"]["VALUE"],
						"DESCRIPTION" => $arProperties["rating"]["DESCRIPTION"],
					),
				));
				$DB->Commit();
				
				if(CModule::IncludeModule("askaron.ibvote"))
				{
					$event = new CAskaronIbvoteEvent;
					$arEventFields = array(
						'ELEMENT_ID' =>  $ELEMENT_ID,
						'ANSWER' => $RATING,
						'USER_ID' => $USER->GetID(),
					);
					$event->add($arEventFields);
				}
				
				// $this->ClearResultCache(array($USER->GetGroups(), 1));
				// $this->ClearResultCache(array($USER->GetGroups(), 0));
				
				$clear_cache=COption::GetOptionString("askaron.ibvote", "clear_cache");
				if ( $clear_cache !== "N" )
				{
					if(defined("BX_COMP_MANAGED_CACHE"))
					{
						$GLOBALS["CACHE_MANAGER"]->ClearByTag("iblock_id_".$arParams["IBLOCK_ID"]);
					}						
				}
			}
		}
	}

?>