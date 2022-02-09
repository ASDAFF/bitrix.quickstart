<?php
define("NEED_AUTH", true); 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Дисконтные и бонусные карты");
?>

<?$APPLICATION->IncludeComponent(
	"devteam:bonus_card",
	"",
        array());

?>
       
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>