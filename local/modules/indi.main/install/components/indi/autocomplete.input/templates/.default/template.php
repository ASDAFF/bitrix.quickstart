<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<input id="<?=$arResult["ID"]?>" class="<?=$arParams["CLASS"]?>" type="text" name="<?=$arParams["NAME"]?>" value="<?=$arParams["VALUE"]?>"/>
<script>
	$(function() {
		$('#<?=$arResult["ID"]?>').autocomplete({
			source: "<?=$arParams["CONTROLLER"]?>",
			minLength: 2
		});
	});
</script>