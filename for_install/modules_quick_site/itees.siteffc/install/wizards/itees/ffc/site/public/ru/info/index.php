<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Информация");
?>
<?$APPLICATION->IncludeComponent("bitrix:menu", "page_menu", Array(
	"ROOT_MENU_TYPE" => "left",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => "",
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N",
	),
	false
);?>
<br />
<p>В этом разделе для Вас собрана полезная информация по работе транспортно-экспедиционных компаний.</p>
<p>Здесь Вы найдете всевозможные справочники, нормативные документы, правила, инструкции и многое другое.</p>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>