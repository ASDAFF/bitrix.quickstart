<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вопрос-ответ");
?>


<p>В этом разделе вы можете найти ответы на многие вопросы, касающиеся работы нашего сайта. Если вы не нашли интересующей вас информации, то можете отправить нам запрос с помощью <a href="#SITE_DIR#about/contacts/">формы обратной связи</a>.</p>
 <?$APPLICATION->IncludeComponent("bitrix:support.faq.element.list", ".default", array(
	"IBLOCK_TYPE" => "services",
	"IBLOCK_ID" => "#FAQ_IBLOCK_ID#",
	"SECTION_ID" => "#FAQ_SECTION_ID#",
	"SHOW_RATING" => "N",
	"RATING_TYPE" => "",
	"PATH_TO_USER" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "N",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_GROUPS" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>