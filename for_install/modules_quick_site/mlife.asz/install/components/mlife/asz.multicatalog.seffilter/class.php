<?
class MlifeAszMulticatalogFilterComponent extends CBitrixComponent{
	
	public static $cacheTemplateUrl = 'filter_';
	
	/*public function onPrepareComponentParams($arParams) {
		
		self::$cacheTemplateUrl = $arParams["FILTER_URLPARAM"];
		
		return $arParams;
		
	}*/
	
	public static function getUrlparamcur($propcode,$arCur,$val) {
		
		$active = false;
		
		if(is_array($arCur["PROPERTY_".$propcode])){
			$url = implode("-or-",$arCur["PROPERTY_".$propcode]);
			if(!in_array($val,$arCur["PROPERTY_".$propcode])) {
				$url .= '-or-'.$val;
			}else{
				$active = true;
			}
			if($active) {
				$url = preg_replace("#(?:^(?:".$val.")$)|(?:(.*)(?:-or-".$val.")$)|(?:(?:^".$val."-or-)(.*))|(?:(.*)(?:-or-".$val.")(-or-.*))#","$1$2",$url);
			}
			$url = urlencode($url);
			return array($url,$active);
		}else{
			return array($val,$active);
		}
		
	}
	
	public static function getActiveUrlforValue($startUrlArray,$propcode,$val,$valCodes) {

		$active = false;
		
		$newStartUrl = array();
		
		if(is_array($startUrlArray[$propcode])){
			
			foreach($startUrlArray[$propcode] as $itm) {
				
				if($itm==$val) {
					$active = true;
				}else{
					$newStartUrl[] = $itm;
				}
			}
			
			if(!$active) $newStartUrl[] = $val;
			
		}else{
			$newStartUrl[] = $val;
		}
		
		$startUrlArray[$propcode] = $newStartUrl;
		
		$url = MlifeAszMulticatalogFilterComponent::makeUrl($startUrlArray,$valCodes);
		
		return array($url,$active);
		
	}
	
	public static function getActiveUrlforValueMode4($startUrlArray,$propcode,$val,$valCodes) {
		
		$active = false;
		
		$newStartUrl = array();
		
		if(is_array($startUrlArray[$propcode])){
			
			foreach($startUrlArray[$propcode] as $itm) {
				
				if($itm==$val) {
					$active = true;
				}else{
					$newStartUrl[] = $itm;
				}
			}
			
			if(!$active) {
				$newStartUrl = array_merge($startUrlArray[$propcode],array($val));
			}
			
		}else{
			$newStartUrl[] = $val;
		}
		
		$startUrlArray[$propcode] = $newStartUrl;
		
		$url = MlifeAszMulticatalogFilterComponent::makeUrl($startUrlArray,$valCodes);
		
		return array($url,$active);
		
	}
	
	public static function getActiveUrlforValueMode5($startUrlArray,$propcode,$val,$valCodes) {
		
		$active = false;
		
		$newStartUrl = array();
		
		if(is_array($startUrlArray[$propcode])){
			
			foreach($startUrlArray[$propcode] as $itm) {
				
				if($itm==$val) {
					$active = true;
				}
			}
			
			if(!$active) $newStartUrl[] = $val;
			
		}else{
			$newStartUrl[] = $val;
		}
		
		$startUrlArray[$propcode] = $newStartUrl;
		
		$url = MlifeAszMulticatalogFilterComponent::makeUrl($startUrlArray,$valCodes);
		
		return array($url,$active);
		
	}
	
	public static function getstartUrlParamArray($arCur,$arOfferCur) {
		
		$arParameters = array();
		
		if(is_array($arCur)){
			foreach($arCur as $key=>$val) {
				if(is_array($val)){
					$arParameters[str_replace("PROPERTY_","",$key)] = $val;
				}else{
					$arParameters[str_replace("PROPERTY_","",$key)] = array($val);
				}
			}
		}
		if(is_array($arOfferCur)){
			foreach($arOfferCur as $key=>$val) {
				if(is_array($val)){
					$arParameters[str_replace("PROPERTY_","",$key)] = $val;
				}else{
					$arParameters[str_replace("PROPERTY_","",$key)] = array($val);
				}
			}
		}

		return $arParameters;
		
	}
	
	public static function makeUrl($arParams,$valCodes) {
		
		$url = ''.self::$cacheTemplateUrl;
		
		if(is_array($arParams)) {
			
			foreach($arParams as $key=>$val) {
				if(!empty($val)){
					if($valCodes[$key]["PROPERTY_TYPE"]=="N"){
					$url .= "".$valCodes[$key]["CODE"].'-'.implode(',',$val)."/";
					}else{
					foreach($val as &$valueNew){
						$valueNew = $valCodes[$key]["VALUES_ID"][$valueNew];
					}
					$url .= "".$valCodes[$key]["CODE"].'-'.implode('-or-',$val)."/";
					}
					
				}
							
			}
			
		}
		$url = mb_strtolower($url);
		return $url;
		
	}
	
	//сортирует параметры в адресе от параметров компонента
	public function getCanonikalUrl($active,$url,$arSortFilter){
		
		$arUrls = explode("/",$url);
		//print_r($arUrls );
		
		$arUrlsStr = array();
		foreach($arUrls as $key=>$url){
			$arUrlsStr[$key] = preg_replace("/^(\w+)(.*?)$/is","$1",$url);
		}
		
		$url = $active.self::$cacheTemplateUrl;
		foreach($arSortFilter as $val){
			$key = array_search($val,$arUrlsStr);
			if($key!==false) $url .= $arUrls[$key]."/";
		}
		if($url = $active.self::$cacheTemplateUrl) $url = $active;
		
		return $url;
		
	}
	
}
?>