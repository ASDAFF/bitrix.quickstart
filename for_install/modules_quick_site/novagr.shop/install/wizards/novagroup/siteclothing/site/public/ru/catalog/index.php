<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); global $USER;
?>
<?
Novagroup_Classes_General_Catalog::showCatalog();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>