<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

	<?
	if (IntVal($arResult["NUM_PRODUCTS"])>0)
	{
		?>
	
	
	<a href="<?=$arParams["PATH_TO_BASKET"]?>"><img src="<?= SITE_TEMPLATE_PATH ?>/images/cart.png" width="16" height="16" alt=""/><?=$arResult["PRODUCTS"];?></a>
	
		<?
	}
	else
	{
		?>
	
			<a href="<?=$arParams["PATH_TO_BASKET"]?>"><img src="<?= SITE_TEMPLATE_PATH ?>/images/cart.png" width="16" height="16" alt="cart"/><?=$arResult["ERROR_MESSAGE"]?></a>
		<?
	}
	if($arParams["SHOW_PERSONAL_LINK"] == "Y")
	{
		?>
	

		
		<?
	}
	?>
