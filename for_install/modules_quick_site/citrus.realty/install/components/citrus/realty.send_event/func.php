<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

__IncludeLang($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/citrus/realty.send_event/lang/'.LANGUAGE_ID.'/fields.php');

if (!function_exists('CSE_htmlspecialchars')):
    function CSE_htmlspecialchars($mixed, $quote_style = ENT_QUOTES, $charset = false)
    {
		if ($charset === false)
			$charset = SITE_CHARSET;
        if (is_array($mixed))
        {
            foreach($mixed as $key => $value)
            {
                $mixed[$key] = CSE_htmlspecialchars($value, $quote_style, $charset); 
            } 
        }
        elseif (is_string($mixed))
        { 
            $mixed = htmlspecialcharsbx(htmlspecialchars_decode($mixed, $quote_style), $quote_style, $charset);
        } 
        return $mixed; 
    }
endif;

if (!function_exists('CSE_GetFields')):     
/*
$arFields = Array(
	"код пол€" => Array(
			"ACTIVE" => true, // выводить ли поле на форме, по-умолчанию true
			"ORIGINAL_TITLE" => "ќригинальное название пол€ или свойства",
			"TITLE" => "Ќазвание пол€ (подпись по-умолчанию)",
			"IS_REQUIRED" => false, // по-умолчанию false
			"TOOLTIP" => "", // подсказка по заполнению
			"IS_EMAIL" => false, // €вл€етс€ e-mail'ом, по-умолчанию false
	)
);
*/
	function CSE_GetFields($eventType)
	{
		static $__arCache = Array();
		$__cacheKey = $eventType;
		
		if (!array_key_exists($__cacheKey, $__arCache))
		{
			$arType = CEventType::GetByID($eventType, LANGUAGE_ID)->fetch();
			$arDescr = explode("\n", $arType["DESCRIPTION"]);

			$arFields = array();
			foreach ($arDescr as $field)
			{
				if (preg_match('/^#([^#]+)#/', $field, $arMatches))
					$arFields[$arMatches[1]] = array("ORIGINAL_TITLE" => $field);
			}

			$arFields = array_merge(
				$arFields,
				array("__CAPTCHA__" => array("IS_REQUIRED" => true, "ORIGINAL_TITLE" => GetMessage("CSE_F_CAPTCHA"), "TITLE"=> GetMessage("CSE_F_CAPTCHA"), "TOOLTIP" => GetMessage("CSE_F_CAPTCHA_TOOLTIP")))
			);

			$__arCache[$__cacheKey] = $arFields;
		}

		return $__arCache[$__cacheKey];
	}
endif;

if (!function_exists('htmlspecialcharsbx')):
	function htmlspecialcharsbx($string, $flags=ENT_COMPAT)
	{
		//shitty function for php 5.4 where default encoding is UTF-8
		return htmlspecialchars($string, $flags, (defined("BX_UTF")? "UTF-8" : "ISO-8859-1"));
	}
endif;

if (!function_exists('getSenderHeader')):
	/**
	 * ¬озвращает значение почтового заголовка Sender дл€ письма, отправл€емого с сайта с заданным Id
	 * 
	 * ≈сли в $from был передан адрес marat@citrus-soft.ru, то значением заголовка будет мыло вида marat@<адрес сайта> (из $_SERVER["HTTP_HOST"]),
 	 * иначе значение будет вз€то из:
	 * 	 1. настройки "E-Mail адрес по умолчанию" сайта (если этот email задан)
	 *   2. настройки "E-Mail администратора сайта (отправитель по умолчанию)" главного модул€ (если email сайта не задан).
	 * ≈сли полученный email совпадает с $from, то вернЄт пустую строку.
	 * 
 	 * @param array $siteId					Id сайта
  	 * @param string $from					«начение заголовка From
	 * @return string
	 */
	function getSenderHeader($siteId, $from)
	{
		$arSite = CSite::getById($siteId)->fetch();
		$defaultEmailFrom = COption::GetOptionString("main", "email_from");

		if (toLower($from) == "marat@citrus-soft.ru")
		{
			$url = "http://" . $_SERVER["HTTP_HOST"];
			$arUrl = parse_url($url);
			$sender = "marat@" . $arUrl["host"];
		}	
		elseif (strlen($arSite["EMAIL"]))
			$sender = $arSite["EMAIL"];
	    else
			$sender = $defaultEmailFrom;
		if ($sender == $from)
			$sender = "";

		return $sender;
	}
endif;
