<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? $deliveries = json_decode($_POST["DATA"], true); ?>

<? //$deliveries = preg_replace("/st yle/", "style", ); ?>


<div class="order-form">
	<h4>Способ доставки</h4>
	<ul class="deliveries">
		<? foreach($deliveries as $deliveryId => $arDelivery): ?>
			<? // Хз вот, почему именно меньше нуля. так в шаблоне было ?>
			<li>
			<? if($deliveryId !== 0 && intval($deliveryId) <= 0): ?>
					<? foreach ($arDelivery["PROFILES"] as $profileId => $arProfile): ?>
						<input type="radio" class="delivery-radio" id="ID_DELIVERY_<?=$deliveryId?>_<?=$profileId?>" name="<?=$arProfile["FIELD_NAME"]?>" value="<?=$deliveryId.":".$profileId;?>" <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?> />
						<div class="radio-column">
							<label for="ID_DELIVERY_<?=$deliveryId?>_<?=$profileId?>">
								<b><?=$arProfile["TITLE"]?></b>
							</label>
							<?if (strlen($arProfile["DESCRIPTION"]) > 0):?>
								<? $arProfile["DESCRIPTION"] = preg_replace("/st yle/", "style", $arProfile["DESCRIPTION"]); ?>
								<? $arProfile["DESCRIPTION"] = preg_replace("/on click/", "onclick", $arProfile["DESCRIPTION"]); ?>
								<?=$arProfile["DESCRIPTION"];?>
							<?endif;?>
						</div>
					<? endforeach; ?>
			<? else: ?>
				<input type="radio" class="delivery-radio" id="ID_DELIVERY_ID_<?=$arDelivery['ID']?>" name="<?=$arDelivery['FIELD_NAME']?>" value="<?=$arDelivery["ID"] ?>" <?if ($arDelivery["CHECKED"]=="Y") echo " checked";?> />
				<div class="radio-column">
					<label for="ID_DELIVERY_ID_<?= $arDelivery['ID'] ?>">
						<strong><?=$arDelivery["NAME"];?></strong>
					</label>	
					<? if (strlen($arDelivery["PERIOD_TEXT"])>0): ?>
						<? echo $arDelivery["PERIOD_TEXT"]; ?>
					<? endif; ?>

					<?=GetMessage("SALE_DELIV_PRICE");?> 
					<?=$arDelivery["PRICE_FORMATED"]?>
								
					<? if (strlen($arDelivery["DESCRIPTION"])>0): ?>
						<p><?=$arDelivery["DESCRIPTION"]?></p>
					<? endif; ?>
				</div>
			<? endif; ?>
			</li>
		<? endforeach; ?>
	</ul>
</div>