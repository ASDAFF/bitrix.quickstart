<?
if (!$USER->IsAdmin())
	return;

IncludeModuleLangFile(__FILE__);

AddEventHandler('main', 'OnBuildGlobalMenu', 'ASDFavoriteOnBuildGlobalMenu');
function ASDFavoriteOnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
{
	foreach($aModuleMenu as $k => $v)
	{
		if($v['parent_menu']=='global_menu_settings' && ($v['icon']=='fav_menu_icon' || $v['icon']=='fav_menu_icon_yellow'))
		{
			if (!strlen($aModuleMenu[$k]['items_id']))
				$aModuleMenu[$k]['items_id'] = 'asd_fav_menu_icon';
			if (empty($aModuleMenu[$k]['items']))
			{
				$aModuleMenu[$k]['items'] = array();
				$aModuleMenu[$k]['items'][] = Array(
												'text' => $aModuleMenu[$k]['text'],
												'title' => $aModuleMenu[$k]['title'],
												'url' => $aModuleMenu[$k]['url'],
												'more_url' => $aModuleMenu[$k]['more_url'],
												);
				unset($aModuleMenu[$k]['more_url']);
			}
			$aModuleMenu[$k]['items'][] = Array(
											'text' => GetMessage('asd_mod_fav_types'),
											'title' => GetMessage('asd_mod_fav_types_title'),
											'url' => 'asd_fav_types_list.php?lang='.LANGUAGE_ID,
											'more_url' => array('asd_fav_types_edit.php'),
											);
			break;
		}
   }
}
?>