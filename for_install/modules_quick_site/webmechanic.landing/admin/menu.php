<?
IncludeModuleLangFile(__FILE__); // в menu.php точно так же можно использовать языковые файлы

if($APPLICATION->GetGroupRight("form")>"D") // проверка уровня доступа к модулю веб-форм
{
  // сформируем верхний пункт меню
  $aMenu = array(
    "parent_menu" => "global_menu_settings", // поместим в раздел "Настройки"
    "sort"        => 100,                    // вес пункта меню
    "url"         => "webmechanic.landing_landing.php?lang=".LANGUAGE_ID,  // ссылка на пункте меню
    "text"        => GetMessage('webmechanic_menu_title'),       // текст пункта меню
    "title"       => GetMessage('webmechanic_menu_title'), // текст всплывающей подсказки
    "icon"        => "form_menu_icon", // малая иконка
    "page_icon"   => "form_page_icon", // большая иконка
    "items_id"    => "menu_webforms",  // идентификатор ветви
    "items"       => array(),          // остальные уровни меню сформируем ниже.
  );

 

  // вернем полученный список
  return $aMenu;
}
// если нет доступа, вернем false
return false;
?>