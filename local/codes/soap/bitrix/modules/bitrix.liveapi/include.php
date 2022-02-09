<?
Class CBitrixLiveapi 
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		$MODULE_ID = basename(dirname(__FILE__));
		$aMenu = array(
			//"parent_menu" => "global_menu_services",
			"parent_menu" => "global_menu_settings",
			"section" => $MODULE_ID,
			"sort" => 50,
			"text" => $MODULE_ID,
			"title" => '',
			"url" => "partner_modules.php?module=".$MODULE_ID,
			"icon" => "",
			"page_icon" => "",
			"items_id" => $MODULE_ID."_items",
			"more_url" => array(),
			"items" => array()
		);

		if (file_exists($path = dirname(__FILE__).'/admin'))
		{
			if ($dir = opendir($path))
			{
				$arFiles = array();

				while($item = readdir($dir))
				{
					if (in_array($item,array('.','..','menu.php')))
						continue;

					$arFiles[] = $item;
				}

				sort($arFiles);

				foreach($arFiles as $item)
					$aMenu['items'][] = array(
						'text' => $item,
						'url' => $MODULE_ID.'_'.$item,
						'module_id' => $MODULE_ID,
						"title" => "",
					);
			}
		}
		$aModuleMenu[] = $aMenu;
	}

	function OnAdminPageLoad()
	{
		if (
			strpos($r = $_SERVER['REQUEST_URI'],'/bitrix/admin/') === 0 
			&&
			file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/liveapi/liveapi.js')
			&& 
			$GLOBALS['APPLICATION']->GetTitle()
		)
			echo '<script src="/bitrix/js/liveapi/liveapi.js"></script>';
	}
}
?>
