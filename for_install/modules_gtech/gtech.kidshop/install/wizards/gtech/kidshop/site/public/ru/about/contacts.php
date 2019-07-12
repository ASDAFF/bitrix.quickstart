<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Контактная информация Интернет-магазина \"Капитошка\"");
$APPLICATION->SetTitle("Контакты");
?> 
<p align="justify"><b>Адрес магазина: </b>675000, Амурская область, г.Благовещенск, ул. Костыльная 8/1, офис 32
  <br />
 </p>
 
<div align="justify"> </div>
 
<p align="justify"><b>Служба доставки</b>: 8 (000) 000-00-00</p>
 
<div align="justify"> <b>Электронная почта</b>: <a href="mailto:<?=COption::GetOptionString("main","email_from")?>"><?=COption::GetOptionString("main","email_from")?></a></div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>