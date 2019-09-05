<?

	if(count($arResult["PERSON_TYPE"]) > 1)	{
		?>
		<b><?=GetMessage("SOA_TEMPL_PERSON_TYPE")?></b>
		<table class="sale_order_full_table">
		<tr>
		<td>
		<?
		foreach($arResult["PERSON_TYPE"] as $v) {
			?>
			<input type="radio" id="PERSON_TYPE_<?= $v["ID"] ?>" name="PERSON_TYPE" value="<?= $v["ID"] ?>"<?if ($v["CHECKED"]=="Y") echo " checked=\"checked\"";?> onClick="submitForm()">
			<label for="PERSON_TYPE_<?= $v["ID"] ?>">
				<?= $v["NAME"] ?>
			</label>
			<?
		}
		?>
		<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>">
		</td></tr></table>
		<?
	} else {
		if(IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"]) > 0) {
			?>
			<input type="hidden" name="PERSON_TYPE" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
			<input type="hidden" name="PERSON_TYPE_OLD" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
			<?
		} else {
			foreach($arResult["PERSON_TYPE"] as $v) {
				?>
				<input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>">
				<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>">
				<?
			}
		}
	}

?>