<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (StrLen($arResult["ERROR_MESSAGE"])<=0)
{
	if(is_array($arResult["WARNING_MESSAGE"]) && !empty($arResult["WARNING_MESSAGE"]))
	{
		foreach($arResult["WARNING_MESSAGE"] as $v)
		{
			echo ShowError($v);
		}
	}
	$arUrlTempl = Array(
		"delete" => $APPLICATION->GetCurPage()."?action=delete&id=#ID#",
		"shelve" => $APPLICATION->GetCurPage()."?action=shelve&id=#ID#",
		"add" => $APPLICATION->GetCurPage()."?action=add&id=#ID#",
	);

	?>
	<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form">
		<?
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delay.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_notavail.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribe.php");
		?>
	</form>
	<?
}
else
{
?>
	<div class="table_wrapper">
		<p class="empty_cart_title"><?=GetMessage("SHOES_EMPTY_CART")?></p>
		<img class="empty_cart" src="/bitrix/components/bagmet/mobile.basket/templates/.default/images/shopping-cart.png">
	</div>
<?
	//ShowError($arResult["ERROR_MESSAGE"]);
}
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