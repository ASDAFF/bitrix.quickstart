<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

use Bitrix\Main\Localization\Loc;

?>
<?php if (is_array($arResult['YEARS']) && count($arResult['YEARS']) > 0): ?>
<section>
	<?php if ($arParams['SHOW_TITLE']): ?>
		<div class=""><?=Loc::getMessage('RS_FLYAWAY.RNA_FLYAWAY.ARCHIVE')?></div>
	<?php endif; ?>
    <ul>
    	<?php foreach ($arResult['YEARS'] as $iYear => $arYear): ?>
        	<li>
        		<?php if ($arParams['SHOW_YEARS']): ?>
            		<a href="<?=$arYear['ARCHIVE_URL']?>">
            			<?=$arYear['NAME']?> (<?=$arYear['COUNT']?>)
            		</a>
        		<?php endif; ?>

        		<?php
        		if (
        		    $arParams['SHOW_MONTHS'] &&
    		        is_array($arYear['MONTHS']) && count($arYear['MONTHS']) > 0
        		):
        		?>
        			<ul>
        				<?php foreach ($arYear['MONTHS'] as $arMonth): ?>
        					<li>
        						<a href="<?=$arMonth['ARCHIVE_URL']?>">
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