<?
	namespace Webprofy\Regional;

	/*
		1. Добавить в OnPageStart:
			WP::get('region')->init();
	*/
	class Main{

		/* static */ 
		protected static
			$instance = null;

		public static
			$settings = array(
				'highload' => 4,
				'default' => array(
					'region' => 'msk',
					'domain' => 'moshoztorg.ru'
				),
				'region' => array(
					'fields' => array(
						'name' => 'UF_NAME',
						'lnglatzoom' => 'UF_DESCRIPTION',
						//'lat' => '',
						//'lng' => '',
						//'zoom' => '',
						'code' => 'UF_XML_ID',
						//'domain' => '',
						'phones' => 'UF_FULL_DESCRIPTION',
					),
					'separators' => array(
						'lnglat' => ',',
						'phone' => '.'
					)
				),
				'indeces' => array(
					'get' => 'set_region',
					'session' => 'Webprofy_Regional_region_code',
					'cookie' => 'region_code'
				)
			);

		static function getInstance(){
	        if (null === self::$instance) {
	            self::$instance = new self();
	        }
	        return self::$instance;
    	}

    	/* dynamic */

		private 
			$regions = array(),
    		$region = null,
			$defaultRegion = null;

		private function __construct(){
			foreach(\WP::getHLElements(self::$settings['highload']) as $element){
				$region = new Region($element);
				$this->regions[$region->prop('code')] = $region;
			}

			$this->defaultRegion = $this->regions[self::$settings['default']['region']];

			if(($code = $_SESSION['region']) && isset($this->regions[$code])){
				$this->region = $this->regions[$code];
				$this->region->setActive();
			}

			$this->init();
		}

		function all(){
			return $this->regions;
		}

		function byCode($code){
			return empty($this->regions[$code]) ? null : $this->regions[$code];
		}

		function init(){ 
			// if(strpos($_SERVER["REQUEST_URI"], "/bitrix/") !== false){
			// 	return;
			// }

			$code = null;
			$set = true;
			$redirect = false;
			$indeces = self::$settings['indeces'];

			if(isset($_GET[$indeces['get']])){
				$code = $_GET[$indeces['get']];
				$redirect = true;
			}
			elseif(isset($_COOKIE[$indeces['cookie']])){
				$code = $_COOKIE[$indeces['cookie']];
			}
			elseif(isset($_SESSION[$indeces['session']])){
				$code = $_SESSION[$indeces['session']];
				$set = false;
			}
			else{
				$this->region = $this->byIP();
			}

			if($code){
				$code = preg_replace("/[^a-z]+/", "", $code);
				$this->region = $this->regions[$code];
				if($set && $this->region){
					$this->region->set($redirect);
				}
			}

			if(!$this->region){
				$this->region = $this->defaultRegion;
				$this->defaultRegion->set();
			}
			$this->region->setActive();
		}

		function cur(){
			return $this->region;
		}

		function byIP(){
			$geoBase = new \Webprofy\Regional\IPGeoBase();
			$geo = $geoBase->getRecord($_SERVER['REMOTE_ADDR']);
			$closest = null;
			$min = -1;
			
			foreach(array('city', 'region') as $index){
				foreach($this->regions as $region) {
					if($region->prop('name') == $geo[$index]){
						return $region;
					}
				}
			}
			
			foreach($this->regions as $region){
				$lng = (float) $region->prop('lng') - (float) $geo['lng'];
				$lat = (float) $region->prop('lat') - (float) $geo['lat'];
				$distance = $lat * $lat + $lng * $lng;
				if($distance < $min || $min < 0){
					$min = $distance;
					$closest = $region;
				}
			}


			$min = sqrt($min);
			$proximity = ($min < 0.150 ? 'exact' : ($min < 0.5 ? 'close' : ($min < 1 ? 'far' : 'very far')));
			
			return $closest;
		}
	}
?>