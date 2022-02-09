<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?

global $USER;
$USER->Logout();
header("Location: /");

?>