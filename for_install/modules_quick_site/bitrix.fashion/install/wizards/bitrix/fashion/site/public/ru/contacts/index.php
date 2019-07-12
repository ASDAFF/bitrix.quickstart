<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");?>

<p><?=GetMessage("ADDRESS")?> <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/locality.php"), false);?></span>, <span class="street-address"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/street_address.php"), false);?></p>
<p>Телефоны: <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></p>
<br />
<iframe width="900" height="600" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/?ie=UTF8&amp;t=m&amp;vpsrc=6&amp;ll=59.933,30.314026&amp;spn=0.206411,0.617294&amp;z=11&amp;output=embed"></iframe>
<br /><br /><br />

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>