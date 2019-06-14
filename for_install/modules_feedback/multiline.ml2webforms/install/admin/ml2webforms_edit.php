<?
if (file_exists($_SERVER["DOCUMENT_ROOT"]."/local/modules/multiline.ml2webforms/admin/ml2webforms_edit.php")) {
    require($_SERVER["DOCUMENT_ROOT"]."/local/modules/multiline.ml2webforms/admin/ml2webforms_edit.php");
} else {
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/multiline.ml2webforms/admin/ml2webforms_edit.php");
}