<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Доставка");
?> 
<p align="justify">Мы доставляем товар курьером по городу, либо &quot;Почтой России&quot; в регионы.
  <br />
</p>
 
<div align="justify"> </div>
 
<div align="justify"> </div>
 
<p align="justify"><b>Служба доставки</b>: 8 (000) 000-00-00</p>
 
<div align="justify"> </div>
 
<p align="justify"><b>Электронная почта</b>: <?=COption::GetOptionString("main","email_from")?><a href="mailto:<?=COption::GetOptionString("main","email_from")?>" ></a></p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>