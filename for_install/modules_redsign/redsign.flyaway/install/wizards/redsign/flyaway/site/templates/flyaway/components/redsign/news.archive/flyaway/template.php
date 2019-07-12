<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

use Bitrix\Main\Localization\Loc;

?>
<?php if (is_array($arResult['YEARS']) && count($arResult['YEARS']) > 0): ?>
<section>
	<div class="nav-side__title"><?=Loc::getMessage('RS_FLYAWAY.RNA_FLYAWAY.ARCHIVE')?></div>
    <ul class="nav-side nav nav-list">
    	<?php foreach ($arResult['YEARS'] as $iYear => $arYear): ?>
    		<?php $sYearId = $this->getEditAreaId($iYear); ?>
        	<li class="nav-side__item level1">
        		<a class="nav-side__label element" href="<?=$arYear['ARCHIVE_URL']?>">
        			<?=$arYear['NAME']?> (<?=$arYear['COUNT']?>)
        			<i class="nav-side__icon collapsed" href="#<?=$sYearId?>" data-toggle="collapse"></i>
        		</a>
        		<?php if (is_array($arYear['MONTHS']) && count($arYear['MONTHS']) > 0): ?>
        			<ul class="nav-side__submenu nav-side__lvl2 lvl2 collapse" id="<?=$sYearId?>">
        				<?php foreach ($arYear['MONTHS'] as $arMonth): ?>
        					<li class="nav-side__item  level2">
        						<a class="nav-side__label element" href="<?=$arMonth['ARCHIVE_URL']?>">
        							<?=$arMonth['NAME']?> (<?=$arMonth['COUNT']?>)
        						</a>
        					</li>
        				<?php endforeach; ?>
        			</ul>
        		<?php endif; ?>
        	</li>
    	<?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>