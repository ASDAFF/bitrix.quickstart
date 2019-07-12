<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Страница не найдена");
?>
<div class="page404">
	<div class="status">Ошибка 404</div>
	<div class="page404_head">Страница не найдена</div>
	<div class="page404_text">
		К сожалению, такой страницы не существует на нашем сайте.<br />
		Возможно вы ввели неправельный адрес или страница была удалена с сервера.<br /><br />
		Можете перейти на <a href="<?=SITE_DIR;?>">главную страницу</a> сайта.
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$("footer").remove();
	});
</script>
<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>