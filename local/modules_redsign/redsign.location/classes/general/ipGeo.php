<?
define("IP_CITY_FILENAME", "/bitrix/modules/redsign.location/ip2country/cities.txt");
define("IP_COUNTRY_FILENAME", "/bitrix/modules/redsign.location/ip2country/cidr_optim.txt");
IncludeModuleLangFile(__FILE__);

class IPGeoBase 
{
	private $fhandleCIDR, $fhandleCities, $fSizeCIDR, $fsizeCities;
	
	function __construct($CIDRFile = false, $CitiesFile = false)
	{
		if(!$CIDRFile)
		{
			$CIDRFile = $_SERVER["DOCUMENT_ROOT"].IP_COUNTRY_FILENAME;			
		}
		if(!$CitiesFile)
		{
			$CitiesFile = $_SERVER["DOCUMENT_ROOT"].IP_CITY_FILENAME;			
		}
		$this->fhandleCIDR = fopen($CIDRFile, 'r') or die("Cannot open $CIDRFile");
		$this->fhandleCities = fopen($CitiesFile, 'r') or die("Cannot open $CitiesFile");
		$this->fSizeCIDR = filesize($CIDRFile);
		$this->fsizeCities = filesize($CitiesFile);
	}

	private function getCityByIdx($idx)
	{
		global $APPLICATION;
		rewind($this->fhandleCities);
		while(!feof($this->fhandleCities))
		{
			$str = fgets($this->fhandleCities);
			$arRecord = explode("\t", trim($str));
			if (SITE_CHARSET == "UTF-8")
			{
				$arRecord = $APPLICATION->ConvertCharsetArray($arRecord , "Windows-1251", SITE_CHARSET);
			}
			if($arRecord[0] == $idx)
			{
				return array(	'city' => $arRecord[1],
								'region' => $arRecord[2],
								'district' => $arRecord[3],
								'lat' => $arRecord[4],
								'lng' => $arRecord[5]);
			}
		}
		return false;
	}
	
	function Get_real_ip()
	{
		$ip = FALSE;
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			for ($i = 0; $i < count($ips); $i++)
			{
				if (!preg_match("/^(10|172\\.16|192\\.168)\\./", $ips[$i]))
				{
					$ip = $ips[$i];
					break;
				}
			}
		}
		return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}
	
	function getRecord()
	{
		$ip = IPGeoBase::Get_real_ip();
		$ip = sprintf('%u', ip2long($ip));
		
		rewind($this->fhandleCIDR);
		$rad = floor($this->fSizeCIDR / 2);
		$pos = $rad;

		while(fseek($this->fhandleCIDR, $pos, SEEK_SET) != -1)			
		{
			if($rad) 
			{
				$str = fgets($this->fhandleCIDR);	
							
			}
			else
			{
				rewind($this->fhandleCIDR);
			}

			$str = fgets($this->fhandleCIDR);
	
			if(!$str)
			{
				return false;
			}

			$arRecord = explode("\t", trim($str));

			$rad = floor($rad / 2);
			if(!$rad && ($ip < $arRecord[0] || $ip > $arRecord[1]))
			{
				return false;
			}

			if($ip < $arRecord[0])
			{
				$pos -= $rad;
			}
			elseif($ip > $arRecord[1])
			{
				$pos += $rad;
			}
			else
			{
				$result = array('range' => $arRecord[2], 'cc' => $arRecord[3]);

				if($arRecord[4] != '-' && $cityResult = $this->getCityByIdx($arRecord[4]))
				{
					$result += $cityResult;
				}
				return $result;
			}
		}
		return false;		
	}
}