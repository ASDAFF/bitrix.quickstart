<?
define("ADMIN_MODULE_NAME", "catalog");
include(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/prolog.php"));
define("ADMIN_MODULE_ICON", '<a href="/bitrix/admin/cat_index.php?lang='.LANGUAGE_ID.'"><img src="/bitrix/images/catalog/catalog.gif" width="48" height="48" border="0" alt="'.GetMessage("CATALOG_ICON_TITLE").'" title="'.GetMessage("CATALOG_ICON_TITLE").'"></a>');
?>