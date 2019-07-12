<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>
<script>
	var obEshopBasket = new JSEshopBasket("<?=$this->GetFolder()?>/ajax.php", "<?=SITE_ID?>");
</script>
<span id="sale-basket-basket-line-container">
<?
$frame = $this->createFrame("sale-basket-basket-line-container", false)->begin();
	if (intval($arResult["NUM_PRODUCTS"])>0)
	{
		?><a href="<?=$arParams["PATH_TO_BASKET"]?>" class="bj-logo-space__icon bj-cart-icon" id="bx_cart_block">
			<span class="glyphicon glyphicon-shopping-cart" data-toggle="tooltip" data-placement="bottom" title="<?=GetMessage("CART")?>"></span>
			<span class="bj-cart-icon__num" id="bx_cart_num"><?echo intval($arResult["NUM_PRODUCTS"])?></span>
		</a>
		<?
	}
	else
	{
		?><a href="<?=$arParams["PATH_TO_BASKET"]?>" class="bj-logo-space__icon bj-cart-icon" id="bx_cart_block">
			<span class="glyphicon glyphicon-shopping-cart" data-toggle="tooltip" data-placement="bottom" title="<?=GetMessage("CART")?>"></span>
			<span class="bj-cart-icon__num" id="bx_cart_num">0</span>
		</a><?
	}
$frame->beginStub();
	?><a href="<?=$arParams["PATH_TO_BASKET"]?>" class="bj-logo-space__icon bj-cart-icon" id="bx_cart_block">
			<span class="glyphicon glyphicon-shopping-cart" data-toggle="tooltip" data-placement="bottom" title="<?=GetMessage("CART")?>"></span>
			<span class="bj-cart-icon__num" id="bx_cart_num">0</span>
		</a><?
$frame->end();
?>
</span>