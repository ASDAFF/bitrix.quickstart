<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
	$properties = $arResult["ORDER_PROP"]["USER_PROPS_N"];
?>
<div class="order-form">
	<h4>Информация о покупателе</h4>
	<table class="labelfield-table" cellpadding="0">
		<? foreach($properties as $property): ?>
			<tr class="hidden" id="row_<?=$property['CODE']?>">
				<? // LABEL ?>
				<td class="labelfield-name">
					<span><?=$property["NAME"]?></span>
				</td>
				
				<? // FIELD ?>
				<td class="labelfield-input">
					<? if($property["TYPE"] == "TEXT"): ?>
						<? if($property["IS_EMAIL"] == "Y" && !is_null($USER->getEmail())): ?>
							<input id="orderfield_<?=$property['CODE']?>" type="text" class="orderfield-text" name="<?=$property["FIELD_NAME"]?>" value="<?=$USER->getEmail();?>">
						<? else: ?>
							<input id="orderfield_<?=$property['CODE']?>" type="text" class="orderfield-text" name="<?=$property["FIELD_NAME"]?>">
						<? endif; ?>
					<? endif; ?>

					<? if($property["TYPE"] == "LOCATION"): ?>
						<input type="hidden" id="<?=$property["FIELD_ID"]?>" name="<?=$property["FIELD_NAME"]?>">
						<input type="text" class="orderfield-location" id="<?=$property["FIELD_ID"].'_VAL'?>">
					<? endif; ?>

					<? if($property["TYPE"] == "CHECKBOX"): ?>
						<? // TODO: add code for CHECKBOX property ?>
					<? endif; ?>

					<? if($property["TYPE"] == "SELECT"): ?>
						<? // TODO: add code for SELECT property ?>
					<? endif; ?>

					<? if($property["TYPE"] == "MULTISELECT"): ?>
						<? // TODO: add code for MULTISELECT property ?>
					<? endif; ?>

					<? if($property["TYPE"] == "TEXTAREA"): ?>
						<? // TODO: add code for TEXTAREA property ?>
					<? endif; ?>

					<? if($property["TYPE"] == "RADIO"): ?>
						<? // TODO: add code for RADIO property ?>
					<? endif; ?>
				</td>
			</tr>
		<? endforeach; ?>
	</table>
</div>