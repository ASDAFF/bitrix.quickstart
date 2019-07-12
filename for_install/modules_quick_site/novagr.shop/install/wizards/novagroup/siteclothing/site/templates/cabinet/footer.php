<?php 


$countProducts = 0;
if (!defined("COUNT_PRODUCTS_SET")) {

	$countProducts = getCountProducts();
	$APPLICATION->SetPageProperty("count_products", $countProducts);
}



if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
		<div class="hfooter"></div>
	</div>
</div>
<footer>
<div class="footer-officeDealer">
        
        <div class="copy"><a href="<?=SITE_DIR?>"><?=GetMessage("T_COPYRIGHT_LABEL")?></a>, &copy;&nbsp;2012. <?=GetMessage("T_RIGHT_PROTECTED")?>. <span class="right"><?=GetMessage("T_DEVELOPED_IN")?> <a target="_blank" href="http://trendylist.ru/">TrendyList</a></span></div>
       
</div>
</footer>
</body>
</html>