<?
	use Webprofy\Bitrix\IBlock\IBlock;
	use Webprofy\Bitrix\IBlock\Element;
	use Webprofy\Bitrix\IBlock\Section;

    use Webprofy\Bitrix\Getter;

	class ITS{
		
		static function addElement($data){
			CModule::IncludeModule('iblock');
			$e = new CIBlockElement();
			$properties = array();
			if(isset($data['p'])){
				if(is_string($data['p'])){
					$data['p'] = self::getListStringToArray($data['p']);
				}
				self::replaceShortenIndeces($data['p']);
				foreach($data['p'] as $name => $value){
					list($name, $type, $other1) = explode(':', $name);
					switch($type){
						case 'html':
						case 'text':
							$value = array(
								'VALUE' => array(
									'TYPE' => strtoupper($type),
									'TEXT' => $value
								)
							);
							break;

						case 'file':
							if(is_array($value) && isset($value['tmp_name'])){
								break;
							}
							$value = array(
								'name' => $other1,
								'tmp_name' => $value,
							);
							break;
					}
					$properties[$name] = $value;
				}
			}
			$fields = array(
				'MODIFIED_BY' => 1,
				'IBLOCK_ID' => 506,
				'ACTIVE' => 'N',
				'CODE' => 'random_'.mt_rand(0, 10000),
				'NAME' => '(без названия)',
				'PROPERTY_VALUES' => $properties
			);
			if(isset($data['f'])){
				if(is_string($data['f'])){
					$data['f'] = self::getListStringToArray($data['f']);
				}
				self::replaceShortenIndeces($data['f']);
				$fields = array_merge($fields, $data['f']);
			}
			if($data['debug']){
				WP::log($fields);
			}
			return $e->Add($fields);
		}

		static function &last(&$a){
			return $a[count($a) - 1];
		}
		/*
			WP::attr(array(
				'href' => 'http://ya.ru',
				'data-no-need' => null,
				'class' => 'super',
				'data-empty' => ''
			)); // возвращает ' href="http://ya.ru" class="super" data-empty=""'
		*/
		static function el($type, $a){
			echo '<'.$type.' '.self::attr($a).'/>';
		}
		static function attr($a){
			$result = '';
			foreach($a as $i => $v){
				if($v === null){
					continue;
				}
				$result .= ' '.$i.'="'.$v.'"';
			}
			return $result;
		}
		static function loadScript($name){
			global $APPLICATION;
			$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.$name);
		}
		/*
			Функция для кеширования 
			Пример: 
			$arResult = WP::cache('c_component_name', 3600000, function(){
				return superHardCalculation();
			});
		*/
		static function cache($name, $time, $callback){
			if(is_array($name)){
				$sname = '';
				foreach($name as $value){
					$sname .= '_'.$value;
				}
				$name = substr($sname, 1);
			}
			$cache = new CPHPCache;
			if($time === null){
				$time = 3600000;
			}

			if($cache->InitCache($time, $name, "/cache_dir") && !(isset($_REQUEST['clear_cache']) && $_REQUEST['clear_cache'] == 'Y')){
				extract($cache->GetVars());
			} else {
				if($cache->StartDataCache($time, $name, "/cache_dir")){
					$result = $callback();
					$cache->EndDataCache(array(
						"result" => $result
					));
				}
			}
			return $result;
		}

		private static $signs = array(
			'\>\=',
			'\<\=',
			'\=',
			'\>',
			'\<',
			'\>\<',
			'\!'
		);
		
		private static function getListStringToArray($s){
			$result = array();
			foreach(explode(';', $s) as $expression){
				$expression = trim($expression);
				preg_match(
					'/^(.*?)('.implode('|', self::$signs).')(.*)$/',
					$expression,
					$m
				);

				list($noneed, $key, $func, $value) = array_map('trim', $m);

				if(strpos($value, ',') > 0){
					$value = array_map('trim', explode(',', $value));
				}

				if($func == '='){
					$func = '';
				}
				$result[$func.$key] = $value;
			}
			return $result;
		}

		private static function replaceShortenIndeces(&$a){
			foreach(array(
				'iblock' => 'IBLOCK_ID',
				'section' => 'SECTION_ID',
				'id' => 'ID'
			) as $before => $after){
				if(isset($a[$before])){
					$a[$after] = $a[$before];
					unset($a[$before]);
				}
			}
		}
	}
?>