<?
/**
 * GeoIP
 * @package 6sec.ru
 * @copyright 20012-2015 Web Lab 6sec.ru
 */
namespace Ss\Geoip;

class Handlers
{
	/**
	 * Fills in the fields Country, City and Region of values cookies
	 * @param unknown $arFields
	 */
	public static function OnBeforeUserAddHandler(&$arFields)
	{
		global $APPLICATION;

		$cookie = $APPLICATION->get_cookie('SS_GEO_IP');

		if(!empty($cookie))
		{
			$arItems = explode('|', $cookie);

			$arCountries = GetCountryArray();

			if(empty($arFields['PERSONAL_COUNTRY']))
			{
				foreach ($arCountries['reference'] as $id => $country)
				{
					if ($arItems[0] == $country)
					{
						$arFields['PERSONAL_COUNTRY'] = $arCountries['reference_id'][$id];
					}
				}
			}

			if (!empty($arItems[1]) && empty($arFields['PERSONAL_STATE']))
			{
				$arFields['PERSONAL_STATE'] = $arItems[1];
			}

			if (!empty($arItems[2]) && empty($arFields['PERSONAL_CITY']))
			{
				$arFields['PERSONAL_CITY'] 	= $arItems[2];
			}
		}
	}

	/**
	 * Set cookie from the user location
	 */
	public static function onPrologHandler()
	{
		global $APPLICATION;

		$cookie = $APPLICATION->get_cookie("SS_GEO_IP");

		if(empty($cookie))
		{
			$GeoIP = new Geoip;
			$GeoIP->SetCookie();
		}
	}
}
?>