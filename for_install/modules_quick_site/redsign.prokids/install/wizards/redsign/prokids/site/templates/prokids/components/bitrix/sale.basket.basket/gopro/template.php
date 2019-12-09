<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->AddHeadScript($templateFolder.'/script.js');

include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/functions.php");

?><div class="basket"><?

	if(strlen($arResult['ERROR_MESSAGE'])<=0)
	{
		if(is_array($arResult['WARNING_MESSAGE']) && !empty($arResult['WARNING_MESSAGE']))
		{
			foreach($arResult['WARNING_MESSAGE'] as $v)
				echo ShowError($v);
		}
		
		?><form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form" id="basket_form"><?
			?><div id="basket_form_container"><?
				?><div class="bx_ordercart"><?
					
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delayed.php");
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribed.php");
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_not_available.php");
					
				?></div><?
			?></div><?
			?><input class="nonep hiddensubmit" type="submit" name="BasketRefresh" value="<?=GetMessage('SALE_ACCEPT')?>" /><?
			?><input type="hidden" name="BasketOrder" value="BasketOrder" /><?
		?></form><?
	} else {
		ShowError($arResult['ERROR_MESSAGE']);
	}

?></div><?
?><script>$('html').removeClass('hidedefaultwaitwindow');</script>