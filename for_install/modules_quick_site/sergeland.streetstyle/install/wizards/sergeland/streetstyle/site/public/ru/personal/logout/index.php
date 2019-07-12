<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
$USER->Logout();
LocalRedirect(SITE_DIR);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>