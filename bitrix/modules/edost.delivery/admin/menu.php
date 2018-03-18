<?
IncludeModuleLangFile(__FILE__);

if ($APPLICATION->GetGroupRight('sale') == 'D') return false;

$aMenu = array(
	'parent_menu' => 'global_menu_store', // 'global_menu_settings' - раздел 'настройки'
	'section' => 'edost_delivery',
	'sort' => 105,
	'text' => 'eDost',
	'title' => GetMessage('EDOST_ADMIN_TITLE'),
	'url' => 'edost.php?lang='.LANGUAGE_ID,
	'icon' => 'edost_menu_icon',
	'page_icon' => 'edost_page_icon',
	'items_id' => 'edost',
//	'module_id' => 'edost.delivery', // идентификатор модуля, к которому относится меню
	'more_url' => array('edost.php'),
	'items' => array()
);

return $aMenu;
?>