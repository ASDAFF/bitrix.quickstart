<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="msearch">
	<form action="<?php echo $arResult['FORM_ACTION'] ?>" method="get">
		<input type="text" name="q" placeholder="<?php echo $arParams['PLACEHOLDER_TEXT'] ?>">
		<div class="search-icon">
			<input type="submit" value="&nbsp;">
		</div>
	</form>
</div>