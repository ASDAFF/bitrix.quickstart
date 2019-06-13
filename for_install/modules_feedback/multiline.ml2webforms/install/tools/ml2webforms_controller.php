<?
if (file_exists($_SERVER["DOCUMENT_ROOT"]."/local/modules/multiline.ml2webforms/tools/controller.php")) {
    require($_SERVER["DOCUMENT_ROOT"]."/local/modules/multiline.ml2webforms/tools/controller.php");
} else {
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/multiline.ml2webforms/tools/controller.php");
}