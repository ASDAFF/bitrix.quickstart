<?php
if (!$USER->IsAdmin()) {
	return;
}

IncludeModuleLangFile(__FILE__);

AddEventHandler('main', 'OnBuildGlobalMenu', 'ASDMailTplOnBuildGlobalMenu');
function ASDMailTplOnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu) {
	foreach ($aModuleMenu as &$arParent) {
		if ($arParent['items_id']=='menu_system' && !empty($arParent['items'])) {
			foreach ($arParent['items'] as &$arItem) {
				if ($arItem['items_id']=='menu_templates' && !empty($arItem['items'])) {
					$arItem['items'][] = array(
						'text' => GetMessage('ASD_MT_MENU_ITEM'),
						'title' => GetMessage('ASD_MT_MENU_ITEM_TITLE'),
						'url' => 'asd_mailtpl_list.php?lang='.LANG,
						'more_url' => array('asd_mailtpl_edit.php')
					);
				}
			}
		}
   }
}