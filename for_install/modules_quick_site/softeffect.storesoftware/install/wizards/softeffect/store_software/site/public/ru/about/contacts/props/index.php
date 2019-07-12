<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Реквизиты");
?> 
<div id="content-text">
	<h5>Информация для договоров</h5>
	<strong>
	<br />
	#SHOP_NAME#</strong>
	<br />
	<strong>ИНН</strong> #SHOP_INN#   
	<br />
	<strong>КПП</strong> #SHOP_KPP#   
	<br />
	<strong>ОГРН</strong> #SHOP_OGRN#   
	<br />
	<strong>ОКПО</strong> #SHOP_OKPO#
	<br />
	<strong>Р\С</strong> #SHOP_KS#   
	<br />
	<strong>К\С</strong> #SHOP_KS#   
	<br />
	<strong>Банк</strong> #SHOP_BANK#
	<br />
	<strong>БИК</strong> #SHOP_BANKREKV#   
	<br /><br />
	<strong>Юридический адрес: </strong><br />
	#SHOP_ADR#
	<br /><br />
	<strong>Фактический адрес: </strong><br />
	#SHOP_LOCATION#
	<br />
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>