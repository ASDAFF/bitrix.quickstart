<?
IncludeModuleLangFile(__FILE__);

class CRS_Location
{
	function GetCityName()
	{
		global $APPLICATION;
		if(CModule::IncludeModule('statistic'))
		{
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) || isset($_SERVER['HTTP_X_REAL_IP'])) 
			{
				foreach(array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP') as $key => $value) 
				{
					if(isset($_SERVER[$value]) &&  strlen($_SERVER[$value]) > 0 &&  strpos($_SERVER[$value], "127.") !== 0) 
					{
						if($p = strrpos($_SERVER[$value], ",")) 
						{ 
							$_SERVER["REMOTE_ADDR"] = $REMOTE_ADDR = trim(substr($_SERVER[$value], $p+1)); 
							$_SERVER["HTTP_X_FORWARDED_FOR"] = substr($_SERVER[$value], 0, $p); 
						} else {
							$_SERVER["REMOTE_ADDR"]= $REMOTE_ADDR = $_SERVER[$value]; 
						}
						break;
					}
				}
			}
			$name = array();
			// city by IP-address
			$obCity = new CCity();
			$arCity = $obCity->GetFullInfo();
			foreach($arCity as $FIELD_ID => $arField)
			{
				if($FIELD_ID == 'IP_ADDR'){
					$name["IP_ADDR"] = $arField["VALUE"];
				} elseif($FIELD_ID == 'COUNTRY_CODE'){
					$name["COUNTRY_CODE"] = $arField["VALUE"];
				} elseif($FIELD_ID == 'COUNTRY_NAME'){
					if($name["COUNTRY_CODE"] == 'RU'){
						$name["COUNTRY_NAME"] = GetMessage("COUNTRY_NAME_RU");
					}elseif($name["COUNTRY_CODE"] == 'UK'){
						$name["COUNTRY_NAME"] = GetMessage("COUNTRY_NAME_UK");
					}else{
						$name["COUNTRY_NAME"] = $arField["VALUE"];
					}
				} elseif($FIELD_ID == 'REGION_NAME'){
					$name["REGION_NAME"] = $arField["VALUE"];
				} elseif($FIELD_ID == 'CITY_NAME'){
					$name["CITY_NAME"] = $arField["VALUE"];
				}
			}
		} else {
			$gb = new IPGeoBase();
			$data = $gb->getRecord();
			if($data['cc'] == 'RU'){
				$name["COUNTRY_NAME"] = GetMessage("COUNTRY_NAME_RU");
			} elseif($data['cc'] == 'UK'){
				$name["COUNTRY_NAME"] = GetMessage("COUNTRY_NAME_UK");
			}
			$name["COUNTRY_CODE"] = $data['cc'];
			$name["CITY_NAME"] = $data['city'];
			$name["REGION_NAME"] = $data['region'];
			
		}
		
		return $name;
	}
}
?>