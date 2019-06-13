<?
Class CKaycomOneplaceseo 
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

				while(false !== $item = readdir($dir))
				{
					if (in_array($item,array('.','..','menu.php')))
						continue;

					if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$MODULE_ID.'_'.$item))
						file_put_contents($file,'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$MODULE_ID.'/admin/'.$item.'");?'.'>');

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
	
	
	function onPageLoad(){
		global $APPLICATION;
		
		if(!CModule::IncludeModule("iblock")) return;
		
		
		
		$el = CIBlockElement::GetList(
			array(	
				"SORT" 	=> "ASC",
				"ID"	=> "ASC"
			),
			array(
				"NAME" => $APPLICATION->GetCurPage(false),
				"IBLOCK_CODE" => "kaycom_ONEPLACESEO"
			),
			false,
			array(
				"nTopCount" => 1
			),
			array(
				"ID",
				"IBLOCK_ID",
				"NAME",
				"PROPERTY_TITLE",
				"PROPERTY_KEYWORDS",
				"PROPERTY_DESCRIPTION"
			)
		);
		
		if($el = $el->GetNext()){
			if($el["PROPERTY_TITLE_VALUE"]){
				$APPLICATION->SetPageProperty("title", $el["PROPERTY_TITLE_VALUE"]);
				$APPLICATION->SetTitle($el["PROPERTY_TITLE_VALUE"]);
			}
			if($el["PROPERTY_KEYWORDS_VALUE"]){
				$APPLICATION->SetPageProperty("keywords", $el["PROPERTY_KEYWORDS_VALUE"]);
			}
			if($el["PROPERTY_DESCRIPTION_VALUE"]){
				$APPLICATION->SetPageProperty("description", $el["PROPERTY_DESCRIPTION_VALUE"]);
			}
		}
	}
	
}



?>
