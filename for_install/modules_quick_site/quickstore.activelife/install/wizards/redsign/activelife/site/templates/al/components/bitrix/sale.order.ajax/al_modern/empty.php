<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
?>

<div class="empty-cart">
    <div class="empty-cart__image" style="background-image: url(<?=$templateFolder?>/images/empty_cart.svg)"></div>
    <div class="empty-cart__text">
        <?=Loc::getMessage("EMPTY_BASKET_TITLE")?>
    </div>
    <div class="empty-cart__descr">
        <?=Loc::getMessage(
    		'EMPTY_BASKET_HINT',
    		array(
    			'#A1#' => '<a href="/">',
    			'#A2#' => '</a>'
		));?>
    </div>
</div>