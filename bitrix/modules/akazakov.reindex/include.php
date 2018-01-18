<?
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule("akazakov.reindex");

$hide_alert = COption::GetOptionString("akazakov.reindex","hide_alert");

if (!CModule::IncludeModule("search")){
	if($hide_alert!="Y") {
		CAdminNotify::DeleteByModule("akazakov.reindex");
		$ar = Array(
		   "MESSAGE" => GetMessage("AKAZAKOV_REINDEX_NE_USTANOVLEN_MODULQ"),
		   "TAG" => "REINDEX_MOD",
		   "MODULE_ID" => "akazakov.reindex",
		   "ENABLE_CLOSE" => "Y"
		);
		$ID = CAdminNotify::Add($ar);
		COption::SetOptionString("akazakov.reindex", 'hide_alert', 'Y');
	}
} else {
	COption::SetOptionString("akazakov.reindex", 'hide_alert', 'N');
	CAdminNotify::DeleteByModule("akazakov.reindex");
}


		 

Class CAkazakovReindex
{
	function ReindexOnPageStartHandler()
	{
		global $APPLICATION;
		//$cssdir = "themes/.default";
		//if (SM_VERSION>='12.0') $cssdir="panel/akazakov.reindex";
		$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/akazakov.reindex.css",true);
	}
	
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
			return;

		$MODULE_ID = basename(dirname(__FILE__));
		$aMenu = array(
			"parent_menu" => "global_menu_services",
			//"parent_menu" => "global_menu_settings",
			"section" => $MODULE_ID,
			"sort" => 50,
			"text" => GetMessage("AK_AUTO"),
			"title" => '',
//			"url" => "partner_modules.php?module=".$MODULE_ID,
			"icon" => "reindex_icon16",//"update_menu_icon_partner",
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
				
				$arTitles = array(
                    'settings.php' => GetMessage("AK_NAST"),

                );

                foreach($arFiles as $item)
                    $aMenu['items'][] = array(
                        'text' => $arTitles[$item],
                        'url' => $MODULE_ID.'_'.$item,
                        'module_id' => $MODULE_ID,
                        "title" => "",

                    );
			}
		}
		
		
		$aModuleMenu[] = $aMenu;
	}
}


//echo '<!--re--><style>'.($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$MODULE_ID.'/assets/reindex.css').'</style>';
function get_informer() {
	if (COption::GetOptionString("akazakov.reindex","informer")=="Y") return true;
	else return false;
}

AddEventHandler('main', 'OnAdminInformerInsertItems', 'OnAdminInformerInsertItemsHandler');
function OnAdminInformerInsertItemsHandler() {       
   $arParams = array(             
      'TITLE' => GetMessage('MODNAME'),             
      'COLOR' => 'peach',             
      'FOOTER' => '<a href="/bitrix/admin/akazakov.reindex_settings.php?tabControl_active_tab=about_window">'.GetMessage("AKAZAKOV_REINDEX_PROSMOTR_SOOBSENIA").'</a>',             
      'ALERT' => get_informer(), //false             
      'HTML' => GetMessage("AKAZAKOV_REINDEX_NOVOE_SOOBSENIE_OT_M"));
	  //'SORT' => 5);       
CAdminInformer::AddItem($arParams); }


?>
