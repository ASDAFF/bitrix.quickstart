<?php
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler(
    'main',
    'OnBuildGlobalMenu',
    'rsOnBuildGlobalMenuHandler'
);

function rsOnBuildGlobalMenuHandler(&$arGlobalMenu, &$arModuleMenu) {
    global $APPLICATION;
    
    $moduleId = 'redsign.devfunc';
    
    if ($APPLICATION->GetGroupRight($moduleId) >= 'R') {
        
        $GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/panel/redsign.devfunc/menu.css");
        
        $arGlobalMenu[] = array(
            'menu_id' => 'redsign',
            'sort' => 1000,
            'text' => Loc::getMessage('RS_MENU_GLOBAL_TEXT'),
            'title' => Loc::getMessage('RS_MENU_GLOBAL_TITLE'),
            'icon' => 'redsign_menu_global',
            'items_id' => 'global_redsign_menu_items',
            'items' => array(
                array(
                    'sort' => 100,
                    'text' => Loc::getMessage('RS_MENU_CONTROL_PANEL_TEXT'),
                    'title' => Loc::getMessage('RS_MENU_CONTROL_PANEL_TITLE'),
                    'url' => '/bitrix/admin/redsign_control_panel.php',
                    'icon' => 'redsign_menu_control_panel_icon',
                    "page_icon" => "redsign_menu_control_panel_page_icon",
                    'items_id' => 'redsign_menu_control_panel_items',
                )
            )
        );
    }
}