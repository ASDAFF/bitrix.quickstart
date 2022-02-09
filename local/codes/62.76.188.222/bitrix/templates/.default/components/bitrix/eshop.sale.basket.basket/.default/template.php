<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();



if (StrLen($arResult["ERROR_MESSAGE"])<=0)
{
	$arUrlTempl = Array(
		"delete" => $APPLICATION->GetCurPage()."?action=delete&id=#ID#",
		"shelve" => $APPLICATION->GetCurPage()."?action=shelve&id=#ID#",
		"add" => $APPLICATION->GetCurPage()."?action=add&id=#ID#",
	);
        
        ?>
 <form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form">
   
<section class="b-detail">
    <div class="b-detail-content">
        <div class="b-basket-button">
            <a class="b-catalog-sort__link b-catalog-sort__active" href="#"><span>готовые к заказу</span></a>
            <a class="b-catalog-sort__link" href="#"><span>отложенные</span></a>
        </div>
        
        
<? include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");  ?>
        
        
        <div class="b-total-price clearfix">
            <div class="b-total-price-coupon"><input type="text" placeholder="код купона для скидки" class="b-text"></div>
            <div class="b-total-price-text">
                <span class="b-total-price-text__text">Общая стоимость:</span>
                <span class="b-price">20 597</span>
            </div>
        </div>
        <div class="b-checkout-button"><button class="b-button">Оформить заказ</button></div>
    </div>
</section>
    
    
    
    </form>
            
            
            
<?
}
else
{
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
	//ShowNote($arResult["ERROR_MESSAGE"]);
}


return; 
 
if (StrLen($arResult["ERROR_MESSAGE"])<=0)
{
	$arUrlTempl = Array(
		"delete" => $APPLICATION->GetCurPage()."?action=delete&id=#ID#",
		"shelve" => $APPLICATION->GetCurPage()."?action=shelve&id=#ID#",
		"add" => $APPLICATION->GetCurPage()."?action=add&id=#ID#",
	);
	?>
	<script>
	function ShowBasketItems(val)
	{
		if(val == 2)
		{
			if(document.getElementById("id-cart-list"))
				document.getElementById("id-cart-list").style.display = 'none';
			if(document.getElementById("id-shelve-list"))
				document.getElementById("id-shelve-list").style.display = 'block';
			if(document.getElementById("id-subscribe-list"))
				document.getElementById("id-subscribe-list").style.display = 'none';
			if(document.getElementById("id-na-list"))
				document.getElementById("id-na-list").style.display = 'none';
		}
		else if(val == 3)
		{
			if(document.getElementById("id-cart-list"))
				document.getElementById("id-cart-list").style.display = 'none';
			if(document.getElementById("id-shelve-list"))
				document.getElementById("id-shelve-list").style.display = 'none';
			if(document.getElementById("id-subscribe-list"))
				document.getElementById("id-subscribe-list").style.display = 'block';
			if(document.getElementById("id-na-list"))
				document.getElementById("id-na-list").style.display = 'none';
		}
		else if (val == 4)
		{
			if(document.getElementById("id-cart-list"))
				document.getElementById("id-cart-list").style.display = 'none';
			if(document.getElementById("id-shelve-list"))
				document.getElementById("id-shelve-list").style.display = 'none';
			if(document.getElementById("id-subscribe-list"))
				document.getElementById("id-subscribe-list").style.display = 'none';
			if(document.getElementById("id-na-list"))
				document.getElementById("id-na-list").style.display = 'block';
		}
		else
		{
			if(document.getElementById("id-cart-list"))
				document.getElementById("id-cart-list").style.display = 'block';
			if(document.getElementById("id-shelve-list"))
				document.getElementById("id-shelve-list").style.display = 'none';
			if(document.getElementById("id-subscribe-list"))
				document.getElementById("id-subscribe-list").style.display = 'none';
			if(document.getElementById("id-na-list"))
				document.getElementById("id-na-list").style.display = 'none';
		}
	}
	</script>
	<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form">
		<? 
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php"); 
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delay.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribe.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_notavail.php");
		 ?>
	</form>
	<? 
	 
}
else
{
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
	//ShowNote($arResult["ERROR_MESSAGE"]);
}
?>