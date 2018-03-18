<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("ghj2k2.mailinfo")!="D")
{
    $aMenu = array(
    "parent_menu" => "global_menu_services",
    "section" => "mailinfo",
    "sort" => 1000,
    "text" => GetMessage("MAIL_INFO_CONTROL_NAME"),
    "title" => GetMessage("MAIL_INFO_CONTROL_TITLE"),
    "icon" => "mailinfo_menu_icon",
    "page_icon" => "mailinfo_page_icon",
    "items_id" => "menu_mailinfo",
    "url" => "mailinfo_index.php?lang=".LANGUAGE_ID,
    "items" => array(
      array(
        "text" => GetMessage("MAIL_INFO_SUCCESS_NAME"),
        "title" => GetMessage("MAIL_INFO_SUCCESS_TITLE"),
        "url" => "mailinfo_success.php?lang=".LANGUAGE_ID,
        "page_icon" => "mailinfo_page_success",
        "more_url" => array(
          "mailinfo_view.php"
        ),
      ),
      array(
        "text" => GetMessage("MAIL_INFO_ERROR_NAME"),
        "title" => GetMessage("MAIL_INFO_ERROR_TITLE"),
        "url" => "mailinfo_error.php?lang=".LANGUAGE_ID,
        "page_icon" => "mailinfo_page_error",
        "more_url" => array(
          "mailinfo_view.php"
        ),
      ),
    )
  );
  return $aMenu;
}
return false;
?>
