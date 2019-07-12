<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?php echo $arResult['BUYER_STORE'] ?>">
<? if (!empty($arResult['DELIVERY'])): ?>
	<div class="order-checkout-block order-checkout-delivery">
		<h4><?php echo GetMessage('SOA_TEMPL_DELIVERY') ?></h4>
		<?php foreach ($arResult['DELIVERY'] as $delivery_id => $arDelivery): ?>
			<?php if ($delivery_id !== 0 && intval($delivery_id) <= 0): ?>
				<?php foreach ($arDelivery['PROFILES'] as $profile_id => $arProfile): ?>
					<div class="order-checkout-delivery-item clearfix">
						<input type="radio" id="ID_DELIVERY_<?= $delivery_id ?>_<?= $profile_id ?>" name="<?= htmlspecialcharsbx($arProfile["FIELD_NAME"]) ?>"
							value="<?= $delivery_id . ':' . $profile_id; ?>" <?= $arProfile['CHECKED'] == 'Y' ? 'checked="checked"' : ''; ?> onclick="submitForm();">
						<label for="ID_DELIVERY_<?= $delivery_id ?>_<?= $profile_id ?>">
							<div class="bx_description">
								<span onclick="BX('ID_DELIVERY_<?= $delivery_id ?>_<?= $profile_id ?>').checked = true;<?= $extraParams ?>submitForm();">
									<?= htmlspecialcharsbx($arDelivery['TITLE']) . ' (' . htmlspecialcharsbx($arProfile['TITLE']) . ')'; ?>
								</span>
								<span class="bx_result_price">
									<?php if ($arProfile["CHECKED"] == "Y" && doubleval($arResult["DELIVERY_PRICE"]) > 0): ?>
										<div><?= GetMessage("SALE_DELIV_PRICE") ?>:&nbsp;<?= $arResult["DELIVERY_PRICE_FORMATED"] ?></div>
										<?php
										if ((isset($arResult["PACKS_COUNT"]) && $arResult["PACKS_COUNT"]) > 1)
										{
											echo GetMessage('SALE_PACKS_COUNT') . ': ' . $arResult["PACKS_COUNT"];
										}
										?>
									<?php else: ?>
										<?
										$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
											"NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
											"DELIVERY" => $delivery_id,
											"PROFILE" => $profile_id,
											"ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
											"ORDER_PRICE" => $arResult["ORDER_PRICE"],
											"LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
											"LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
											"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
											"ITEMS" => $arResult["BASKET_ITEMS"],
											"EXTRA_PARAMS_CALLBACK" => $extraParams
											), null, array('HIDE_ICONS' => 'Y'));
										?>
									<?php endif; ?>
								</span>
								<p onclick="BX('ID_DELIVERY_<?= $delivery_id ?>_<?= $profile_id ?>').checked = true; submitForm();">
									<? if (strlen($arProfile['DESCRIPTION']) > 0): ?>
										<?= nl2br($arProfile['DESCRIPTION']) ?>
									<? else: ?>
										<?= nl2br($arDelivery['DESCRIPTION']) ?>
									<? endif; ?>
								</p>
							</div>
						</label>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<?php $clickHandler = "onClick = \"BX('ID_DELIVERY_ID_" . $arDelivery["ID"] . "').checked=true;submitForm();\""; ?>
				<div class="order-checkout-delivery-item clearfix">
					<input type="radio" id="ID_DELIVERY_ID_<?= $arDelivery['ID'] ?>" name="<?= htmlspecialcharsbx($arDelivery["FIELD_NAME"]) ?>"
						value="<?= $arDelivery['ID'] ?>"<? if ($arDelivery['CHECKED'] == "Y") echo ' checked'; ?> onclick="submitForm();">
					<label for="ID_DELIVERY_ID_<?= $arDelivery['ID'] ?>">
						<div class="bx_description">
							<div class="name"><span><?= htmlspecialcharsbx($arDelivery['NAME']) ?></span></div>
							<span class="bx_result_price">
								<?php if (strlen($arDelivery['PERIOD_TEXT']) > 0): ?>
									<?php echo $arDelivery['PERIOD_TEXT']; ?><br>
								<?php endif; ?>
								<?= GetMessage('SALE_DELIV_PRICE'); ?>: <?= $arDelivery['PRICE_FORMATED'] ?>
							</span>
							<p>
								<?php if (strlen($arDelivery["DESCRIPTION"]) > 0): ?>
									<?php echo $arDelivery['DESCRIPTION'] ?>
								<?php endif; ?>
							</p>
						</div>
					</label>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<script type="text/javascript">
	function fShowStore(id, showImages, formWidth, siteId)
	{
		var strUrl = '<?= $templateFolder ?>' + '/map.php';
		var strUrlPost = 'delivery=' + id + '&showImages=' + showImages + '&siteId=' + siteId;
		var storeForm = new BX.CDialog({
			'title': '<?= GetMessage('SOA_ORDER_GIVE') ?>',
			head: '', 'content_url': strUrl, 'content_post': strUrlPost,
			'width': formWidth,
			'height': 450,
			'resizable': false, 'draggable': false
		});

		var button = [
			{
				title: '<?= GetMessage('SOA_POPUP_SAVE') ?>',
				id: 'crmOk',
				'action': function()
				{
					GetBuyerStore();
					BX.WindowManager.Get().Close();
				}
			},
			BX.CDialog.btnCancel
		];
		storeForm.ClearButtons();
		storeForm.SetButtons(button);
		storeForm.Show();
	}

	function GetBuyerStore()
	{
		BX('BUYER_STORE').value = BX('POPUP_STORE_ID').value;
		//BX('ORDER_DESCRIPTION').value = '<?= GetMessage("SOA_ORDER_GIVE_TITLE") ?>: '+BX('POPUP_STORE_NAME').value;
		BX('store_desc').innerHTML = BX('POPUP_STORE_NAME').value;
		BX.show(BX('select_store'));
	}

	function showExtraParamsDialog(deliveryId)
	{
		var strUrl = '<?= $templateFolder ?>' + '/delivery_extra_params.php';
		var formName = 'extra_params_form';
		var strUrlPost = 'deliveryId=' + deliveryId + '&formName=' + formName;

		if (window.BX.SaleDeliveryExtraParams) {
			for (var i in window.BX.SaleDeliveryExtraParams)
			{
				strUrlPost += '&' + encodeURI(i) + '=' + encodeURI(window.BX.SaleDeliveryExtraParams[i]);
			}
		}
		var paramsDialog = new BX.CDialog({
			'title': '<?= GetMessage('SOA_ORDER_DELIVERY_EXTRA_PARAMS') ?>',
			head: '',
			'content_url': strUrl, 'content_post': strUrlPost,
			'width': 500,
			'height': 200,
			'resizable': true,
			'draggable': false
		});

		var button = [{
				title: '<?= GetMessage('SOA_POPUP_SAVE') ?>',
				id: 'saleDeliveryExtraParamsOk',
				'action': function()
				{
					insertParamsToForm(deliveryId, formName);
					BX.WindowManager.Get().Close();
				}
			},
			BX.CDialog.btnCancel
		];

		paramsDialog.ClearButtons();
		paramsDialog.SetButtons(button);
		//paramsDialog.adjustSizeEx();
		paramsDialog.Show();
	}

	function insertParamsToForm(deliveryId, paramsFormName) {
		var orderForm = BX("ORDER_FORM"), paramsForm = BX(paramsFormName);
		wrapDivId = deliveryId + "_extra_params";

		var wrapDiv = BX(wrapDivId);
		window.BX.SaleDeliveryExtraParams = {};

		if (wrapDiv)
			wrapDiv.parentNode.removeChild(wrapDiv);

		wrapDiv = BX.create('div', {props: {id: wrapDivId}});

		for (var i = paramsForm.elements.length - 1; i >= 0; i--) {
			var input = BX.create('input', {
				props: {
					type: 'hidden',
					name: 'DELIVERY_EXTRA[' + deliveryId + '][' + paramsForm.elements[i].name + ']',
					value: paramsForm.elements[i].value
				}}
			);

			window.BX.SaleDeliveryExtraParams[paramsForm.elements[i].name] = paramsForm.elements[i].value;

			wrapDiv.appendChild(input);
		}

		orderForm.appendChild(wrapDiv);

		BX.onCustomEvent('onSaleDeliveryGetExtraParams', [window.BX.SaleDeliveryExtraParams]);
	}
</script>