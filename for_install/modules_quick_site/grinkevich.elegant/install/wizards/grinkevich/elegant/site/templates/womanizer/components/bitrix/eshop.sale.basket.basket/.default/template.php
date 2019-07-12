<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (StrLen($arResult["ERROR_MESSAGE"]) <= 0){
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
	<?if( $_REQUEST["show"] == "2" || $_REQUEST["show"] == "3" || $_REQUEST["show"] == "4" ){?>
		ShowBasketItems(<?=(int)$_REQUEST["show"]?>);
	<?}?>
	</script>
	<form method="post" action="<?= $arParams["PATH_TO_CART"]; ?>" name="basket_form" id="basket_form">
		<?
		//if ($arResult["ShowReady"]=="Y")
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");

		//if ($arResult["ShowDelay"]=="Y")
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delay.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribe.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_notavail.php");
		//if ($arResult["ShowNotAvail"]=="Y")
			//include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_notavail.php");
		?>
	</form>
	<?
}else{
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
	//ShowNote($arResult["ERROR_MESSAGE"]);
}
?>