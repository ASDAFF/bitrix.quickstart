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
	"��� ����" => Array(
			"ACTIVE" => true, // �������� �� ���� �� �����, ��-��������� true
			"ORIGINAL_TITLE" => "������������ �������� ���� ��� ��������",
			"TITLE" => "�������� ���� (������� ��-���������)",
			"IS_REQUIRED" => false, // ��-��������� false
			"TOOLTIP" => "", // ��������� �� ����������
			"IS_EMAIL" => false, // �������� e-mail'��, ��-��������� false
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
	 * ���������� �������� ��������� ��������� Sender ��� ������, ������������� � ����� � �������� Id
	 * 
	 * ���� � $from ��� ������� ����� marat@citrus-soft.ru, �� ��������� ��������� ����� ���� ���� marat@<����� �����> (�� $_SERVER["HTTP_HOST"]),
 	 * ����� �������� ����� ����� ��:
	 * 	 1. ��������� "E-Mail ����� �� ���������" ����� (���� ���� email �����)
	 *   2. ��������� "E-Mail �������������� ����� (����������� �� ���������)" �������� ������ (���� email ����� �� �����).
	 * ���� ���������� email ��������� � $from, �� ����� ������ ������.
	 * 
 	 * @param array $siteId					Id �����
  	 * @param string $from					�������� ��������� From
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
