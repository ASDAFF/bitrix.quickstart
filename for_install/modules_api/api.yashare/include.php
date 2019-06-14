<?
/**
 * Created by Tuning-Soft
 * http://tuning-soft.ru/
 *
 * NOTE: Requires PHP version 5.3 or later
 *
 * @package   CApiYashare
 * @author    Anton Kuchkovsky <support@tuning-soft.ru>
 * @copyright © 1984-2015 Tuning-Soft
 *
 * @vesion    1.0.0
 * @date      2015-08-23
 */

global $MESS;
IncludeModuleLangFile(__FILE__);

$MODULE_ID = basename(dirname(__FILE__));

Class CApiYashare
{
	public static function getPageUrl($get_uri = true)
	{
		$protocol = (($_SERVER["SERVER_PORT"] == 443 || strtolower($_SERVER["HTTPS"]) == "on") ? "https://" : "http://");
		$host     = $_SERVER['SERVER_NAME'];

		if ($get_uri)
			$url = $protocol . $host . GetRequestUri();
		else
			$url = $protocol . $host . GetPagePath();

		return $url;
	}

	function getPageDescription($truncate_length = 0, $elementID = 0)
	{
		$arElement = array();
		if ($elementID && CModule::IncludeModule('iblock'))
		{
			$obElement = new CIBlockElement();
			$dbRes     = $obElement->GetList(array(), array('=ID' => $elementID), false, false, array('ID', 'IBLOCK_ID', 'PREVIEW_TEXT', 'DETAIL_TEXT'));
			$arElement = $dbRes->Fetch();

			$arElement['RETURN_TEXT'] = $arElement['PREVIEW_TEXT'] ? $arElement['PREVIEW_TEXT'] : $arElement['DETAIL_TEXT'];
		}

		if ($truncate_length && $arElement['RETURN_TEXT'])
		{
			$obParser                 = new CTextParser();
			$arElement['RETURN_TEXT'] = strip_tags($arElement['RETURN_TEXT']);
			$arElement['RETURN_TEXT'] = $obParser->html_cut($arElement['RETURN_TEXT'], $truncate_length);
		}

		return $arElement['RETURN_TEXT'];
	}
}

?>
