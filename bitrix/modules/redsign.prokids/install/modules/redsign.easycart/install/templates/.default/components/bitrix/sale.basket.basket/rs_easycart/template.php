<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="rsec_thistab_basket"><?
	$normalCount = IntVal( count($arResult['ITEMS']['AnDelCanBuy']) );

	if(strlen($arResult['ERROR_MESSAGE'])<=0 && $normalCount>0)
	{
		?><form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form"><?
			include($_SERVER['DOCUMENT_ROOT'].$templateFolder.'/basket_items.php');
			?><input type="hidden" name="BasketRefresh" value="BasketRefresh" /><?
		?></form><?
	} else {
		?><div class="rsec_emptytab rsec_clearfix"><?
			?><div class="rsec_emptytab_icon"><?=$arResult['ERROR_MESSAGE']?></div><?
		?></div><?
	}
	
	$this->SetViewTarget('rsec_basketheadlink');
		?><div class="rsec_orlink"><a class="rsec_basket rsec_changer" href="#rsec_basket"><i class="rsec_iconka"></i><span class="rsec_name"><?=GetMessage('SALE_EC_HEADER_LINK_PRODS')?></span><span class="rsec_color">&nbsp;<span class="rsec_normalCount"><?=$normalCount?></span></span> &nbsp;<span class="rsec_name"><?=GetMessage('SALE_SUM')?></span><span class="rsec_color rsec_sum">&nbsp;<span class="rsec_allSum_FORMATED"><?=$arResult['allSum_FORMATED']?></span></span></a></div><?
	$this->EndViewTarget();
?></div><?
