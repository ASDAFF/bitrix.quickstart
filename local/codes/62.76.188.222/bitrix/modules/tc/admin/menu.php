<?php
  
  $aMenu[] = array(
    "parent_menu" => "global_menu_services", 
    "sort"        => "10",  
    "url"         => "cards_search.php?lang=".LANGUAGE_ID,  
    "text"        => 'Бонусные карты',     
    "title"       => 'Бонусные карты',
    "icon"        => "sale_menu_icon_statistic", 
    "page_icon"   => "sale_menu_icon_statistic", 
    "items_id"    => "menu_kudin_options",  
  //  "items"       => $items, 
  );

  $aMenu[] = array(
    "parent_menu" => "global_menu_services", 
    "sort"        => "10",  
    "url"         => "/bitrix/admin/moderate.php",  
    "text"        => 'Комментарии к товарам',     
    "title"       => 'Комментарии к товарам',
    "icon"        => "sale_menu_icon_statistic", 
    "page_icon"   => "sale_menu_icon_statistic", 
    "items_id"    => "menu_kudin_options",  
  //  "items"       => $items, 
  );

return $aMenu;
 