<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?> 
<script type="text/javascript">
$(function(){
	$(".map-container").gMap({
		//maptype: google.maps.MapTypeId.HYBRID,
		zoom: 17,
		scrollwheel: false,
		latitude:  55.805222, 
		longitude: 37.568071,
		markers: [{
					latitude:  55.805000, 
					longitude: 37.568039,
					html: "<table width=\"300\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><img src=\"#SITE_DIR#images/plaza.jpg\"></td><td><i>Бизнес-центр &laquo;Плаза&raquo;.</i><br><i>Мы находимся здесь!</i><div style=\"border-top:1px dashed #D98A36;margin:7px 0 0;padding-top:5px;\">#COMPANY_SCHEDULE#</div></td></tr></table>",
					popup: true,
					icon: { 
							 image: "#SITE_DIR#images/gmap_pin_orange.png",
							 iconsize: [26, 46],
							 iconanchor: [12, 46],
						  } 					
				 }]
	});								
});
</script>
 
<p>
	<b>Телефон:</b> #COMPANY_TELEPHONE#
	<br />
	<b>Адрес:</b> #SHOP_ADR#
</p>
 
<div class="map-container" style="width:920px;height:390px;border:1px solid #ccc;color:#000;margin-bottom:50px;overflow: hidden;"></div>


<h3>Уважаемые покупатели!</h3>
 Прежде чем задать свой вопрос, обратите внимание на раздел <a href="#SITE_DIR#faq/" >Помощь покупателю</a>. Возможно, там уже есть исчерпывающая информация по решению вашей проблемы.
<p></p>
 <?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	"",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => "#EMAIL#",
		"REQUIRED_FIELDS" => array(),
		"EVENT_MESSAGE_ID" => array()
	)
);?> 
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>