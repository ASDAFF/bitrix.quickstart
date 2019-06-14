<?

	namespace Webprofy\Regional;

	use Webprofy\Regional\Main as RegionalMain;

	class Region{
		private $properties = array();
		function __construct($highloadData){
			$settings = RegionalMain::$settings['region'];
			foreach($settings['fields'] as $i => $v){
				$value = $highloadData[$v];
				if($i == 'code' && !$value){
					$value = $highloadData['ID'];
				}
				$this->properties[$i] = $value;
			}
			if(isset($this->properties['lnglatzoom'])){
				list($lng, $lat, $zoom) = explode($settings['separators']['lnglat'], $this->properties['lnglatzoom']);
				$this->properties['lng'] = floatval(trim($lng));
				$this->properties['lat'] = floatval(trim($lat));
				$this->properties['zoom'] = floatval(trim($zoom));
				unset($this->properties['lnglatzoom']);
			}
		}

		function getRegionURL(){
			return './?'.RegionalMain::$settings['indeces']['get'].'='.$this->prop('code');
		}

		function getRedirectURL(){
    		global $APPLICATION;
			$domain = $this->properties['domain'];
			if(!$domain){
				$domain = RegionalMain::$settings['default']['domain'];
			}
    		$page = $APPLICATION->GetCurPage();
    		if($page == 'index.php'){
    			$page = '';
    		}
    		$params = trim(DeleteParam(array(RegionalMain::$settings['indeces']['get'], "sessid")));
    		$params = $params ? '?'.$params : '';

			return "http://".$domain.$page.$params;
		}

		function setIfDomain($domain){
			if($domain != $this->properties['domain']){
				return false;
			}
			$this->set();
			return true;
		}

		function set($redirect = false){
			$indeces = RegionalMain::$settings['indeces'];
			$code = $this->properties['code'];
			$_SESSION[$indeces['session']] = $code;
			$_GET[$indeces['get']] = $code;
			if($redirect){
				setcookie(
					$indeces['cookie'],
					$region,
					time()+60*60*24*30,
					'/'
				);
				LocalRedirect($this->getRedirectURL());
			}
		}

		function prop($index){
			return $this->properties[$index];
		}

		function setActive(){
			$this->properties['active'] = true;
		}

		function loadFile(){
			global $APPLICATION;
			$a = pase_url($APPLICATION->GetCurUri("", true));
			$dir = pathinfo($a['path'], PATHINFO_DIRNAME);
			$name = pathinfo($a['path'], PATHINFO_FILENAME);
			$ext = pathinfo($a['path'], PATHINFO_EXTENSION);

			if(strtolower($ext) == "php"){
				$file = $_SERVER["DOCUMENT_ROOT"].$dir."/".$name.".".$this->properties['code'].".".$ext;
				if(file_exists($file)){
					$_SERVER["REAL_FILE_PATH"] = $url;
					include_once($file);
					die();
				}
			}
		}

		function excluded_p($p, $includeName = 'REGION_IN', $excludeName = 'REGION_OUT'){
			return $this->excluded(
				$p[$includeName]['VALUE'],
				$p[$excludeName]['VALUE']
			);
		}
		function excluded($includeCodes = false, $excludeCode = false){
    		$code = $this->properties['code'];

    		$ok = true;

    		foreach($includeCodes as $code_){
    			if($code == $code_){
    				$ok = true;
    				break;
    			}
    			$ok = false;
    		}

    		if(!$ok){
    			return true;
    		}

    		foreach($excludeCode as $code_){
    			if($code == $code_){
    				$ok = false;
    				break;
    			}
    		}

    		if(!$ok){
    			return true;
    		}

    		return false;
    	}
	}