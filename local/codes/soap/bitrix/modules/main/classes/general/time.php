<?
IncludeModuleLangFile(__FILE__);

class CTimeZone
{
	protected static $enabled = 1;
	protected static $useTimeZones = false;

	public static function Possible()
	{
		return class_exists('DateTime');
	}

	public static function Enabled()
	{
		if(self::$enabled > 0 && self::Possible())
		{
			if(self::$useTimeZones === false)
				self::$useTimeZones = COption::GetOptionString("main", "use_time_zones", "N");
			if(self::$useTimeZones == "Y")
				return true;
		}
		return false;
	}

	public static function Disable()
	{
		self::$enabled --;
	}

	public static function Enable()
	{
		self::$enabled ++;
	}

	private static function __tzsort($a, $b)
	{
		if($a['offset'] == $b['offset'])
			return strcmp($a['timezone_id'], $b['timezone_id']);
		return ($a['offset'] < $b['offset']? -1 : 1);
	}

	public static function GetZones()
	{
		$aTZ = array();
		static $aExcept = array("Etc/", "GMT", "UTC", "UCT", "HST", "PST", "MST", "CST", "EST", "CET", "MET", "WET", "EET", "PRC", "ROC", "ROK", "W-SU");
		foreach(DateTimeZone::listIdentifiers() as $tz)
		{
			foreach($aExcept as $ex)
				if(strpos($tz, $ex) === 0)
					continue 2;
			try
			{
				$oTz = new DateTimeZone($tz);
				$aTZ[$tz] = array('timezone_id'=>$tz, 'offset'=>$oTz->getOffset(new DateTime("now", $oTz)));
			}
			catch(Exception $e){}
		}

		uasort($aTZ, array('CTimeZone', '__tzsort'));

		$aZones = array(""=>GetMessage("tz_local_time"));
		foreach($aTZ as $z)
			$aZones[$z['timezone_id']] = '(UTC'.($z['offset'] <> 0? ' '.($z['offset'] < 0? '-':'+').sprintf("%02d", ($h = floor(abs($z['offset'])/3600))).':'.sprintf("%02d", abs($z['offset'])/60 - $h*60) : '').') '.$z['timezone_id'];

		return $aZones;
	}

	public static function SetAutoCookie()
	{
		$cookie_prefix = COption::GetOptionString('main', 'cookie_name', 'BITRIX_SM');
		$autoTimeZone = trim($GLOBALS["USER"]->GetParam("AUTO_TIME_ZONE"));
		if($autoTimeZone == "Y" || ($autoTimeZone == "" && COption::GetOptionString("main", "auto_time_zone", "N") == "Y"))
		{
			$GLOBALS["APPLICATION"]->AddHeadString(
				'<script type="text/javascript">var bxDate = new Date(); document.cookie="'.$cookie_prefix.'_TIME_ZONE="+bxDate.getTimezoneOffset()+","+Math.round(bxDate.getTime()/1000)+",'.time().'; path=/; expires=Fri, 01-Jan-2038 00:00:00 GMT"</script>', true
			);
		}
		elseif(isset($_COOKIE[$cookie_prefix."_TIME_ZONE"]))
		{
			unset($_COOKIE[$cookie_prefix."_TIME_ZONE"]);
			setcookie($cookie_prefix."_TIME_ZONE", "", time()-3600, "/");
		}
	}

	public static function GetOffset()
	{
		if(!self::Enabled())
			return 0;

		try //possible DateTimeZone incorrect timezone
		{
			$localTime = new DateTime();
			$localOffset = $localTime->getOffset();
	
			$autoTimeZone = '';
			if(is_object($GLOBALS["USER"]))
				$autoTimeZone = trim($GLOBALS["USER"]->GetParam("AUTO_TIME_ZONE"));

			if($autoTimeZone == "N")
			{
				//manually set time zone
				$userZone = $GLOBALS["USER"]->GetParam("TIME_ZONE");
				$userTime = ($userZone <> ""? new DateTime(null, new DateTimeZone($userZone)) : $localTime);
				$userOffset = $userTime->getOffset();
			}
			else
			{
				//auto time zone from cookies
				$cookie_prefix = COption::GetOptionString('main', 'cookie_name', 'BITRIX_SM');
				if(
					array_key_exists($cookie_prefix."_TIME_ZONE", $_COOKIE)
					&& $_COOKIE[$cookie_prefix."_TIME_ZONE"] <> ''
					&& (
						$autoTimeZone == "Y"
						|| (
							$autoTimeZone == ''
							&& COption::GetOptionString("main", "auto_time_zone", "N") == "Y"
						)
					)
				)
				{
					$arCookie = explode(",", $_COOKIE[$cookie_prefix."_TIME_ZONE"]);
					if($arCookie[1] >= $arCookie[2] && $arCookie[1] <= ($arCookie[2]+30*60) || $arCookie[1] <= $arCookie[2] && $arCookie[1] >= ($arCookie[2]-30*60))
					{
						//correct tz - offset from JS "as is"
						$userOffset = -($arCookie[0])*60;
					}
					elseif($arCookie[1] > ($arCookie[2]+30*60))
					{
						//incorrect tz - try to determine offset
						$diff = ($arCookie[1] - $arCookie[2]) % 3600;
						return ($arCookie[1] + ($diff < 1800? -$diff : 3600-$diff)) - $arCookie[2];
					}
					elseif($arCookie[1] < ($arCookie[2]-30*60))
					{
						//incorrect tz - try to determine offset
						$diff = ($arCookie[2] - $arCookie[1]) % 3600;
						return ($arCookie[1] - ($diff < 1800? -$diff : 3600-$diff)) - $arCookie[2];
					}
				}
				else
				{
					//default server time zone
					$serverZone = COption::GetOptionString("main", "default_time_zone", "");
					$serverTime = ($serverZone <> ""? new DateTime(null, new DateTimeZone($serverZone)) : $localTime);
					$userOffset = $serverTime->getOffset();
				}
			}
		}
		catch(Exception $e)
		{
			return 0;
		}
		return $userOffset - $localOffset;
	}
}
?>