<?
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Config\Option;

require($_SERVER["DOCUMENT_ROOT"].Option::get("soobwa_comments", "path")."/admin/soobwa_comments_list.php");
?>
