<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);
?>
<?php if(!empty($arResult)): ?>
<ul class="nav-tabs">
	<?php foreach($arResult as $arItem): ?>
		<?php if ($arParams['MAX_LEVEL'] == 1 && $arItem['DEPTH_LEVEL'] == 1): ?>
			<li <?php if($arItem['SELECTED']): ?>class="active"<?php endif; ?>>
				<a href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
<?php endif; ?>