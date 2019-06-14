<?php
IncludeModuleLangFile(__FILE__);

$SUP_RIGHT = $APPLICATION->GetGroupRight('support');

if($SUP_RIGHT>'D')
{
    $aMenu[] = array(
        'parent_menu' => 'global_menu_services',
        'section' => 'unisender.integration',
        'sort' => 1000,
        'text' => GetMessage('MAIN_MENU_UNISENDER'),
        'title' => GetMessage('MAIN_MENU_UNISENDER_ALT'),
        'icon' => 'unisender_menu_icon',
        'page_icon' => 'unisender_page_icon',
        'items_id' => 'menu_unisender',
        'items' => array(
            array(
                'text' => GetMessage('MAIN_MENU_UNISENDER_EXPORT'),
                'title' => GetMessage('MAIN_MENU_UNISENDER_EXPORT_ALT'),
                'url' => 'unisender_index.php?lang='.LANGUAGE_ID,
                'more_url' => array('unisender_export.php')
            ),
            array(
                'text' => GetMessage('MAIN_MENU_UNISENDER_IMPORT'),
                'title' => GetMessage('MAIN_MENU_UNISENDER_IMPORT_ALT'),
                'url' => 'unisender_import.php?lang='.LANGUAGE_ID,
                'more_url' => array('unisender_import.php')
            ),
            array(
                'text' => GetMessage('MAIN_MENU_UNISENDER_CREATE_FORM'),
                'title' => GetMessage('MAIN_MENU_UNISENDER_CREATE_FORM_ALT'),
                'url' => 'unisender_create_form.php?lang='.LANGUAGE_ID,
                'more_url' => array('unisender_create_form.php')
            ),
            array(
                'text' => GetMessage('MAIN_MENU_UNISENDER_CREATE_LIST_FIELD'),
                'title' => GetMessage('MAIN_MENU_UNISENDER_CREATE_LIST_FIELD_ALT'),
                'url' => 'unisender_create_list_field.php?lang='.LANGUAGE_ID,
                'more_url' => array('unisender_create_list_field.php')
            )
        )
    );

    return $aMenu;
}

?>