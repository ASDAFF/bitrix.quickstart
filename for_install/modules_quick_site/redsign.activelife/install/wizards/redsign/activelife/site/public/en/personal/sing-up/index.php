<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Sing up");
?>
<?php
$sSorterPath = $_SERVER['DOCUMENT_ROOT'].'#SITE_DIR#include/personal/sing-up/index.php';
if (file_exists($sSorterPath)) {
    include($sSorterPath);    
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>