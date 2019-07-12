<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("#SITE_NAME#");
?>
<div class="logo"><a href="<?=SITE_DIR?>" rel="external"><img src="#SITE_DIR#images/logo.jpg"  /></a></div>
<div class="telephone"><a href="callto:#SALE_PHONE#">#SALE_PHONE#</a></div>
<br clear="all" />
<ul data-role="listview" data-inset="true" data-theme="c"> 
	<li><a href="catalog/">Каталог</a></li> 
	<li><a href="howto/">Как купить</a></li> 
	<li><a href="delivery/">Доставка</a></li> 
	<li><a href="news/">Новости</a></li> 
	<li><a href="about/">О магазине</a></li> 
	<li><a href="about/contacts/">Контакты</a></li> 
	<li><a href="personal/">Персональный раздел</a></li> 
</ul> 
<div data-role="controlgroup" data-type="horizontal">
	<a href="<?=SITE_DIR?>" data-role="button" data-icon="forward" data-iconpos="top" data-theme="c" rel="external">Перейти<br>на сайт</a>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>