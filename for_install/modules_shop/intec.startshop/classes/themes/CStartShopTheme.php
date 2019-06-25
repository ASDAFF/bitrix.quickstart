<?
	class CStartShopTheme 
	{
		public static $MODULE_THEMES_PATH_RELATIVE = '/bitrix/themes/intec.startshop';
		
		public static function SetColors($arSetColors, $sSiteID = SITE_ID)
		{
			if (!empty($sSiteID))
			{
				$dbSite = CSite::GetList($by = "sort", $sort = "asc", array("ID" => $sSiteID));
				
				if ($dbSite->Fetch())
				{
					if (!class_exists('lessc'))
						include_once(dirname(__FILE__).'/lessc.inc.php');

					$obLess = new lessc;
					
					try
					{
						$obLess->setVariables($arSetColors);
						
						if (!is_dir($_SERVER['DOCUMENT_ROOT'].static::$MODULE_THEMES_PATH_RELATIVE.'/sites/')) {
							mkdir($_SERVER['DOCUMENT_ROOT'].static::$MODULE_THEMES_PATH_RELATIVE.'/sites/', 0777, true);
						}
						
						$obLess->compileFile(__DIR__.'/../../themes/theme.less', $_SERVER['DOCUMENT_ROOT'].static::$MODULE_THEMES_PATH_RELATIVE.'/sites/'.$sSiteID.'.css');
					}
					catch (exception $e)
					{
						echo "Less Error: ".$e->getMessage();
					}
				}
			}
		}
		
		public static function ApplyTheme($sSiteID = SITE_ID)
		{
			global $APPLICATION;
			
			require_once($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT.'/modules/intec.startshop/web/include.php');
			$APPLICATION->SetAdditionalCSS(static::$MODULE_THEMES_PATH_RELATIVE.'/css/controls.css');
			$APPLICATION->SetAdditionalCSS(static::$MODULE_THEMES_PATH_RELATIVE.'/plugins/fancybox/jquery.fancybox.css');
			$APPLICATION->AddHeadScript(static::$MODULE_THEMES_PATH_RELATIVE.'/js/controls.js');
			$APPLICATION->AddHeadScript(static::$MODULE_THEMES_PATH_RELATIVE.'/js/functions.js');
			$APPLICATION->AddHeadScript(static::$MODULE_THEMES_PATH_RELATIVE.'/plugins/fancybox/jquery.fancybox.pack.js');

			if (!empty($sSiteID))
			{
				$dbSite = CSite::GetList($by = "sort", $sort = "asc", array("ID" => $sSiteID));
				
				if ($dbSite->Fetch())
					$APPLICATION->SetAdditionalCSS(static::$MODULE_THEMES_PATH_RELATIVE.'/sites/'.$sSiteID.'.css');
			}
		}
	}
?>