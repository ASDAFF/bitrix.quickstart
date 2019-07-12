<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="order-checkout-block order-checkout-payment">
	<h4><?php echo GetMessage('SOA_TEMPL_PAY_SYSTEM') ?></h4>
	<?php foreach ($arResult['PAY_SYSTEM'] as $arPaySystem): ?>
		<div class="order-checkout-payment-item clearfix">
			<input type="radio" id="ID_PAY_SYSTEM_ID_<?php echo $arPaySystem['ID'] ?>" name="PAY_SYSTEM_ID" value="<?php echo $arPaySystem['ID'] ?>" onclick="changePaySystem();"
				<?php if ($arPaySystem['CHECKED'] == 'Y' && !($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y' && $arResult['USER_VALS']['PAY_CURRENT_ACCOUNT'] == 'Y')) echo ' checked="checked"'; ?>>
			<label for="ID_PAY_SYSTEM_ID_<?php echo $arPaySystem['ID'] ?>" onclick="BX('ID_PAY_SYSTEM_ID_<?php echo $arPaySystem["ID"] ?>').checked = true;changePaySystem();">
				<?php if ($arParams['SHOW_PAYMENT_SERVICES_NAMES'] != 'N'): ?>
					<span><?php echo $arPaySystem['PSA_NAME'] ?></span>
				<?php endif; ?>
				<p>
					<?php
					if (intval($arPaySystem['PRICE']) > 0)
						echo str_replace('#PAYSYSTEM_PRICE#', SaleFormatCurrency(roundEx($arPaySystem['PRICE'], SALE_VALUE_PRECISION), $arResult['BASE_LANG_CURRENCY']), GetMessage('SOA_TEMPL_PAYSYSTEM_PRICE'));
					elseif (strlen(trim(strip_tags($arPaySystem['DESCRIPTION']))) > 0)
						echo $arPaySystem['DESCRIPTION'];
					?>
				</p>
			</label>
		</div>
		<?php if (count($arResult["PAY_SYSTEM"]) == 1): ?>
			<input type="hidden" name="PAY_SYSTEM_ID" value="<?php echo $arPaySystem['ID'] ?>">
		<?php endif; ?>
	<?php endforeach; ?>
</div>

<script type="text/javascript">
	function changePaySystem(param)
	{
		if (BX("account_only") && BX("account_only").value == 'Y') // PAY_CURRENT_ACCOUNT checkbox should act as radio
		{
			if (param == 'account')
			{
	if (BX("PAY_CURRENT_ACCOUNT"))
		{ 					BX("PAY_CURRENT_ACCOUNT").checked = true;
			BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
				BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');

					// deselect all other
					var el = document.getElementsByName("PAY_SYSTEM_ID");
					for (var i = 0; i < el.length; i++)
						el[i].checked = false;
				}
			}
			else
			{
					BX("PAY_CURRENT_ACCOUNT").checked = false;
			BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked"); 	 	 BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
			}
		}
				else if (BX("account_only") && BX("account_only").value == 'N')
		{
			if (param == 'account')
			{
				if (BX("PAY_CURRENT_ACCOUNT"))
				{
		BX("PAY_CURRENT_ACCOUNT").checked = !BX("PAY_CURRENT_ACCOUNT").checked;

			if (BX("PAY_CURRENT_ACCOUNT").checked)
				{
					BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
						BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
					}
				else
					{
						BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
						BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
					}
				}
			}
		}

					submitForm();
					}
</script>