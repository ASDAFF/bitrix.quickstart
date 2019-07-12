<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Кнопки");
?>
<span class="button">Default</span>
<span class="button transparent">Default transparent</span>
<span class="button transparent grey_br">Default grey</span>
<br/><br/><br/>
<span class="button vbig_btn">Big</span>
<span class="button vbig_btn transparent">Big transparent</span>
<span class="button vbig_btn transparent grey_br">Big grey</span>
<br/><br/><br/>
<span class="button small">Small</span>
<span class="button small transparent">Small transparent</span>
<span class="button small transparent grey_br">Small grey</span>
<br/><br/><br/>
<span class="button vbig_btn wides">Big wide</span>
<span class="button vbig_btn wides transparent">Big transparent wide</span>
<span class="button vbig_btn wides transparent grey_br">Big grey wide</span>
<br/><br/><br/>
<span class="button big_btn bold">Big bold</span>
<span class="button big_btn bold transparent">Big bold transparent</span>
<span class="button big_btn bold transparent grey_br">Big bold grey</span>
<br/><br/><br/>
<div style="max-width: 200px" class="wides">
	<span class="button big_btn bold type_block">Big bold block</span>
</div>
<br/><br/><br/>
<div style="max-width: 200px" class="wides">
	<span class="button big_btn bold transparent type_block">Big bold block</span>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>