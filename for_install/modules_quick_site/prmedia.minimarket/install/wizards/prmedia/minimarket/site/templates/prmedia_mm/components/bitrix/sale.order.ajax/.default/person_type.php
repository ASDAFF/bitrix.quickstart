<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php if (count($arResult['PERSON_TYPE']) > 1): ?>
	<div class="order-checkout-block order-checkout-person-type">
		<h4><?php echo GetMessage('PRMEDIA_MM_SOA_PERSON_TYPE') ?></h4>
		<?php foreach ($arResult['PERSON_TYPE'] as $v): ?>
			<div class="order-section-radio">
				<input type="radio" id="PERSON_TYPE_<?php echo $v['ID'] ?>" name="PERSON_TYPE" value="<?php echo $v['ID'] ?>"<?php echo $v['CHECKED'] == 'Y' ? ' checked="checked"' : ''; ?> onClick="submitForm()"> 
				<label for="PERSON_TYPE_<?php echo $v['ID'] ?>"><?php echo $v['NAME'] ?></label>
			</div>
		<?php endforeach; ?>
		<input type="hidden" name="PERSON_TYPE_OLD" value="<?php echo $arResult['USER_VALS']['PERSON_TYPE_ID'] ?>" />
	</div>
<?php else: ?>
	<?php if (intval($arResult['USER_VALS']['PERSON_TYPE_ID']) > 0): ?>
		<span style="display:none;"> <!-- ie8 hidden fields fixed -->
			<input type="text" name="PERSON_TYPE" value="<?php echo intval($arResult['USER_VALS']['PERSON_TYPE_ID']) ?>" />
			<input type="text" name="PERSON_TYPE_OLD" value="<?php echo intval($arResult['USER_VALS']['PERSON_TYPE_ID']) ?>" />
		</span>
	<?php else: ?>
		<?php foreach ($arResult['PERSON_TYPE'] as $v): ?>
			<input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?php echo $v['ID'] ?>" />
			<input type="hidden" name="PERSON_TYPE_OLD" value="<?php echo $v['ID'] ?>" />
		<?php endforeach; ?>
	<?php endif; ?>
<?php endif; ?>