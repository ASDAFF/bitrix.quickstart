<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Возможности");
?>
<div class="wrap_md_row">
	<div class="iblock md-33">
		<h3><a href="<?=SITE_DIR;?>info/more/typograpy/">Оформление</a></h3>
		<ul>
			<li>заголовки h1-h5</li>
			<li>нумерованные и маркерованные списки</li>
			<li>блок цитат</li>
			<li>таблица</li>
			<li>блок со скидкой</li>
		</ul>
	</div>
	<div class="iblock md-33">
		<h3><a href="<?=SITE_DIR;?>info/more/buttons/">Кнопки</a></h3>
		<ul>
			<li>большие, маленькие, обычные кнопки</li>
			<li>кнопки со сплошоной заливкой, только с рамкой</li>
			<li>кнопки с жирным текстом, с обычным текстом</li>
			<li>кнопки с цветом темы, с серым цветом</li>
			<li>широкие кнопки</li>
		</ul>
	</div>
	<div class="iblock md-33">
		<h3><a href="<?=SITE_DIR;?>info/more/elements/">Элементы</a></h3>
		<ul>
			<li>табы</li>
			<li>формы</li>
			<li>аккордионы</li>
			<li>постраничная навигациия</li>
		</ul>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>