<?
/**
 * Company developer: ALTASIB
 * Developer: adumnov
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * @package bitrix
 * @subpackage altasib.geobase
 * @copyright (c) 2006-2015 ALTASIB
 */

Class CAltasibGeoBaseIPTools extends CAltasibGeoBaseIP
{
	function ParseXML($text){

		if (strlen($text) > 0)
		{
			require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");
			$objXML = new CDataXML();
			$res = $objXML->LoadString($text);
			if($res !== false)
			{
				$arRes = $objXML->GetArray();
			}
		}
		$arRes = current($arRes);
		$arRes = $arRes["#"];
		$arRes = current($arRes);

		$ar = Array();

		foreach($arRes as $key => $arVal){
			foreach($arVal["#"] as $title => $Tval){
				$ar[$key][$title] = $Tval["0"]["#"];
			}
		}
		return ($ar[0]);
	}

	function GetGeoData($ip){
		$arData = CAltasibGeoBaseIP::GetGeoDataIpgeobase_ru($ip);
		if(!$arData)
			if(!$arData = CAltasibGeoBaseIP::GetGeoDataGeoip_Elib_ru($ip))
				return false;
		return $arData;
	}

}
?>