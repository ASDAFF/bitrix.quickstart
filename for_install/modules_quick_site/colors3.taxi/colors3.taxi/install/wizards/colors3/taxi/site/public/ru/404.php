<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("ERROR_PAGE_TPL", "YES");
$APPLICATION->SetTitle("404 Страница не найдена");
$APPLICATION->SetPageProperty("keywords_inner", "404 Страница не найдена");
$APPLICATION->SetPageProperty("title", "404 Страница не найдена");
$APPLICATION->SetPageProperty("keywords", "404 Страница не найдена");
$APPLICATION->SetPageProperty("description", "404 Страница не найдена");?><?$APPLICATION->SetTitle("Ошибка 404");
?>




<div class="row head">
	<div class="span4 logo" style="top:0px;">
		<a title="Перейти на главную страницу" href="/"></a>
	</div>
	<div class="span8 clearfix">
		<div class="row">
			<h1>Ошибка 404</h1>
		</div>
		<div class="row">
			<p>Такой страницы не существует.<br />
			Вероятно у неё изменился путь или она была удалена с сервера.</p>
		</div>
		<div class="row">
			<a title="Перейти на главную страницу" href="/">
				<span>Перейти на главную страницу</span>
			</a>
		</div> 
    </div>
</div>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>