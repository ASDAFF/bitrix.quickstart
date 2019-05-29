<?
CModule::IncludeModule("lightweb.components");
global $DBType;

global 	$LWCOMPONENTS;
		$LWCOMPONENTS['PLUGIN_DIR']='/bitrix/modules/lightweb.components/plugin.libs/';
		$LWCOMPONENTS['EXTENSION_DIR']='/bitrix/modules/lightweb.components/extension.libs/';

$arClasses=array(
	'CLWComponents'=>'classes/general/CLWComponents.php',
	'CLWTools'=>'classes/general/CLWTools.php',
	'CLWOption'=>'classes/general/CLWOption.php'
);

CModule::AddAutoloadClasses("lightweb.components",$arClasses);

Class CLightwebComponents 
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
			return;

		$MODULE_ID = basename(dirname(__FILE__));

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
	}
}
?>