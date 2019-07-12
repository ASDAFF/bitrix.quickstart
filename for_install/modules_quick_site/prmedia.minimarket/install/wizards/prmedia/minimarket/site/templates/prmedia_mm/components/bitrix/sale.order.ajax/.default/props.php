<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php include("$tpl/props_format.php"); ?>
<?php $bHideProps = true; ?>
<?php if (is_array($arResult['ORDER_PROP']['USER_PROFILES']) && !empty($arResult['ORDER_PROP']['USER_PROFILES']) || $arParams['ALLOW_NEW_PROFILE'] == 'Y'): ?>
	<?php if ($arParams['ALLOW_NEW_PROFILE'] != 'Y' && count($arResult['ORDER_PROP']['USER_PROFILES']) == 1): ?>
		<?php foreach ($arResult['ORDER_PROP']['USER_PROFILES'] as $arUserProfiles): ?>
			<input type="hidden" name="PROFILE_ID" id="ID_PROFILE_ID" value="<?php echo $arUserProfiles['ID'] ?>" />
		<?php endforeach; ?>
	<?php else: ?>
			<div class="order-checkout-block order-checkout-props order-checkout-props-block">
			<h4><?php echo GetMessage('SOA_TEMPL_EXISTING_PROFILE') ?></h4>
			<div class="order-section-profile">
				<select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="SetContact(this.value)">
					<?php if ($arParams['ALLOW_NEW_PROFILE'] == 'Y'): ?>
						<option value="0"><?php echo GetMessage('SOA_TEMPL_PROP_NEW_PROFILE') ?></option>
					<?php endif; ?>
					<?php if (!empty($arResult['ORDER_PROP']['USER_PROFILES'])): ?>
						<?php foreach ($arResult['ORDER_PROP']['USER_PROFILES'] as $arUserProfiles): ?>
							<option value="<?php echo $arUserProfiles['ID'] ?>"<?php if ($arUserProfiles['CHECKED'] == 'Y') echo ' selected'; ?>><?= $arUserProfiles['NAME'] ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
		</div>
	<?php endif; ?>
<?php else: ?>
	<?php $bHideProps = false; ?>
<?php endif; ?>
<div class="order-checkout-block order-checkout-props order-checkout-props-last">
	<h4><?= GetMessage('SOA_TEMPL_BUYER_INFO') ?></h4>
	<div id="sale_order_props" <?php echo !$bHideProps ? "style='display:none;'" : '' ?>>
		<?
		PrintPropsForm($arResult['ORDER_PROP']['USER_PROPS_N'], $arParams['TEMPLATE_LOCATION']);
		PrintPropsForm($arResult['ORDER_PROP']['USER_PROPS_Y'], $arParams['TEMPLATE_LOCATION']);
		?>
	</div>
</div>
<div style="display:none;">
	<?
	$APPLICATION->IncludeComponent(
		'bitrix:sale.ajax.locations', $arParams['TEMPLATE_LOCATION'], array(
		'AJAX_CALL' => 'N',
		'COUNTRY_INPUT_NAME' => 'COUNTRY_tmp',
		'REGION_INPUT_NAME' => 'REGION_tmp',
		'CITY_INPUT_NAME' => 'tmp',
		'CITY_OUT_LOCATION' => 'Y',
		'LOCATION_VALUE' => '',
		'ONCITYCHANGE' => 'submitForm()',
		), null, array('HIDE_ICONS' => 'Y')
	);
	?>
</div>