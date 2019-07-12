<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//echo "<pre>"; print_r($arResult); echo "</pre>";
if (StrLen($arResult["ERROR_MESSAGE"])<=0)
{	
	$arUrlTempl = Array(
		"delete" => $APPLICATION->GetCurPage()."?action=delete&id=#ID#",
		"shelve" => $APPLICATION->GetCurPage()."?action=shelve&id=#ID#",
		"add" => $APPLICATION->GetCurPage()."?action=add&id=#ID#",
	);
	?>
	<form method="POST" action="<?=$APPLICATION->GetCurPage()?>" name="basket_form">
		<?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
        	?>
	</form>
	<?
}
else
	ShowNote($arResult["ERROR_MESSAGE"]);
?>