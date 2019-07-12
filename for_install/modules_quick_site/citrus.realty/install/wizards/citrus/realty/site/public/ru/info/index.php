<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
LocalRedirect($APPLICATION->GetCurDir() . 'articles/', false, "301 Moved permanently");
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>