<?

$items[] = array(
    "url" => "remainslist.php",
    "text" => 'Сопоставления',
    "title" => 'Сопоставления',
    "icon" => "iblock_menu_icon_types",
    "page_icon" => "iblock_page_icon_types",
    "items_id" => "menu_rem_options4",
);

$items[] = array(
    "url" => "remainslog.php",
    "text" => 'Результаты',
    "title" => 'Результаты',
    "icon" => "iblock_menu_icon_types",
    "page_icon" => "iblock_page_icon_types",
    "items_id" => "menu_rem_options2",
);

$aMenu = array(
    "parent_menu" => "global_menu_services",
    "sort" => "10",
    "url" => "remainsoptions.php?lang=" . LANGUAGE_ID,
    "text" => 'Подгрузчик остатков',
    "title" => 'Подгрузчик остатков',
    "icon" => "sale_menu_icon_statistic",
    "page_icon" => "sale_menu_icon_statistic",
    "items_id" => "menu_kudin_options1",
    "items" => $items,
);

return $aMenu;


