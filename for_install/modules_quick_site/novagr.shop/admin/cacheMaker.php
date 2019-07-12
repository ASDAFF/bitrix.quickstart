<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php");

$adminModel = new Novagroup_Classes_General_Admin(__FILE__);

if ($filePath = $adminModel->getFilePath()) {
    __IncludeLang($adminModel->getLangPath());
    include($filePath);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>