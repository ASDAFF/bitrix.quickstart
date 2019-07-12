<?php 
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

?>

<div class="loss-menu-right loss-menu-right_count views basketinhead">
    
	<a class="selected dropdown-toggle" href="<?=$arParams['PATH_TO_BASKET']?>"  id="dropdown_basket_link">
		<i class="fa fa-shopping-cart"></i>
        <span class="count"><?=$arResult['NUM_PRODUCTS']?></span>
    </a>
</div>