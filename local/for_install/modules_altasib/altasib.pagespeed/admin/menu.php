<?
IncludeModuleLangFile(__FILE__);
if($APPLICATION->GetGroupRight("rinsvent.fastauth") != "D")
{
    $arItemsETMenu[] = array(
        "text" => GetMessage("ALTASIB_PAGESPEED_PROCESS"),
        "url" => "/bitrix/admin/altasib_pagespeed.php?lang=".LANG,
        "title" => GetMessage("ALTASIB_PAGESPEED_PROCESS_ALT")
    );

    if(!empty($arItemsETMenu))
    {
        $aMenu = array(
            "parent_menu" => "global_menu_services",
            "section" => "altasib_pagespeed",
            "sort" => 1000,
            "text" => GetMessage("ALTASIB_PAGESPEED_CONTROL"),
            "url"  => "/bitrix/admin/altasib_pagespeed.php?lang=".LANG,
            "title"=> GetMessage("ALTASIB_PAGESPEED_CONTROL"),
            "icon" => "altasib_pagespeed_menu_icon",
            "page_icon" => "altasib_pagespeed_page_icon",
            "items_id" => "menu_altasib_pagespeed",
            "items" => $arItemsETMenu
        );

        return $aMenu;
    }
}
return false;
?>
