<?
/**
* 
* @author dev2fun (darkfriend)
* @copyright darkfriend
* @version 0.1.1
* 
*/
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$path = \Bitrix\Main\Loader::getLocal('modules/dev2fun.imagecompress/admin/dev2fun_imagecompress_files.php');
if(file_exists($path)) {
    include $path;
} else {
    ShowMessage('dev2fun_imagecompress_files.php not found!');
}
?>