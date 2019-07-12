<? require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?> 
<h2><strong></strong>Информация для связи</h2>
<br />
<p><strong>Телефон:</strong> #SALE_PHONE#</p>
<br />
<p>
	<strong>E-mail (круглосуточно):</strong>  
	<br /><br />
	Информационная служба - <?$APPLICATION->IncludeComponent(
	"softeffect:mail.antispam",
	"",
	Array(
		"EMAIL" => "info@#SERVER_NAME#",
		"LINK" => "Y",
		"ELEMENT_CLASS" => ""
	)
);?>
	<br />
	Техническая поддержка - <?$APPLICATION->IncludeComponent(
	"softeffect:mail.antispam",
	"",
	Array(
		"EMAIL" => "support@#SERVER_NAME#",
		"LINK" => "Y",
		"ELEMENT_CLASS" => ""
	)
);?>
	<br />
	Отдел продаж - <?$APPLICATION->IncludeComponent(
	"softeffect:mail.antispam",
	"",
	Array(
		"EMAIL" => "sale@#SERVER_NAME#",
		"LINK" => "Y",
		"ELEMENT_CLASS" => ""
	)
);?>
	<br /><br />
</p>

<p><b>Skype:</b> #SKYPE#</p>
<br />
<p><b>ICQ:</b> #ICQ#</p>
<br />
<p><b>Адрес:</b> #SHOP_ADR#</p>
<br />
<br />
<?require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>