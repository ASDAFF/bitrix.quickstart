<?
class CAllUserCounter
{
	protected static $counters = false;

	public static function GetValue($user_id, $code, $site_id = SITE_ID)
	{
		global $DB;
		$user_id = intval($user_id);

		if ($user_id <= 0)
			return false;

		$arCodes = self::GetValues($user_id, $site_id);
		if (isset($arCodes[$code]))
			return intval($arCodes[$code]);
		else
			return 0;
	}

	public static function GetValues($user_id, $site_id = SITE_ID)
	{
		global $DB, $CACHE_MANAGER;

		$user_id = intval($user_id);
		if ($user_id <= 0)
			return array();

		if(!isset(self::$counters[$user_id][$site_id]))
		{

			if (
				CACHED_b_user_counter !== false
				&& $CACHE_MANAGER->Read(CACHED_b_user_counter, "user_counter".$user_id, "user_counter")
			)
			{
				$arAll = $CACHE_MANAGER->Get("user_counter".$user_id);
				if(is_array($arAll))
				{
					foreach($arAll as $arItem)
					{
						if ($arItem["SITE_ID"] == $site_id || $arItem["SITE_ID"] == '**')
						{
							if (!isset(self::$counters[$user_id][$site_id][$arItem["CODE"]]))
								self::$counters[$user_id][$site_id][$arItem["CODE"]] = 0;
							self::$counters[$user_id][$site_id][$arItem["CODE"]] += $arItem["CNT"];
						}
					}
				}
			}
			else
			{
				$strSQL = "
					SELECT CODE, SITE_ID, CNT
					FROM b_user_counter
					WHERE USER_ID = ".$user_id;

				$dbRes = $DB->Query($strSQL, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				$arAll = array();
				while ($arRes = $dbRes->Fetch())
				{
					$arAll[] = $arRes;
					if ($arRes["SITE_ID"] == $site_id || $arRes["SITE_ID"] == '**')
					{
						if (!isset(self::$counters[$user_id][$site_id][$arRes["CODE"]]))
							self::$counters[$user_id][$site_id][$arRes["CODE"]] = 0;
						self::$counters[$user_id][$site_id][$arRes["CODE"]] += $arRes["CNT"];
					}
				}
				if (CACHED_b_user_counter !== false)
					$CACHE_MANAGER->Set("user_counter".$user_id, $arAll);
			}
		}

		return self::$counters[$user_id][$site_id];
	}

	public static function GetLastDate($user_id, $code, $site_id = SITE_ID)
	{
		global $DB;

		$user_id = intval($user_id);
		if ($user_id <= 0 || strlen($code) <= 0)
			return 0;

		$strSQL = "
			SELECT ".$DB->DateToCharFunction("LAST_DATE", "FULL")." LAST_DATE
			FROM b_user_counter
			WHERE USER_ID = ".$user_id."
			AND (SITE_ID = '".$site_id."' OR SITE_ID = '**')
			AND CODE = '".$DB->ForSql($code)."'
		";

		$result = 0;
		$dbRes = $DB->Query($strSQL, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($arRes = $dbRes->Fetch())
			$result = MakeTimeStamp($arRes["LAST_DATE"]);

		return $result;
	}

	public static function ClearAll($user_id, $site_id = SITE_ID)
	{
		global $DB, $CACHE_MANAGER;

		$user_id = intval($user_id);
		if ($user_id <= 0)
			return false;

		$strSQL = "
			DELETE FROM b_user_counter
			WHERE USER_ID = ".$user_id."
			AND (SITE_ID = '".$site_id."' OR SITE_ID = '**')";
		$dbRes = $DB->Query($strSQL, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($site_id == '**')
		{
			if (self::$counters)
				unset(self::$counters[$user_id]);
		}
		else
		{
			if (self::$counters)
				unset(self::$counters[$user_id][$site_id]);
		}

		$CACHE_MANAGER->Clean("user_counter".$user_id, "user_counter");

		return true;
	}

	public static function ClearByTag($tag, $code, $site_id = SITE_ID)
	{
		global $DB, $CACHE_MANAGER;

		if (strlen($tag) <= 0 || strlen($code) <= 0)
			return false;

		$strSQL = "
			DELETE FROM b_user_counter
			WHERE TAG = '".$DB->ForSQL($tag)."' AND CODE = '".$DB->ForSQL($code)."'
			AND (SITE_ID = '".$site_id."' OR SITE_ID = '**')";
		$dbRes = $DB->Query($strSQL, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		self::$counters = false;
		$CACHE_MANAGER->CleanDir("user_counter");

		return true;
	}

	// legacy function
	public static function GetValueByUserID($user_id, $site_id = SITE_ID, $code = "**")
	{
		return self::GetValue($user_id, $code, $site_id);
	}
	public static function GetCodeValuesByUserID($user_id, $site_id = SITE_ID)
	{
		return self::GetValues($user_id, $site_id);
	}
	public static function GetLastDateByUserAndCode($user_id, $site_id = SITE_ID, $code = "**")
	{
		return self::GetLastDate($user_id, $code, $site_id);
	}
}
?>