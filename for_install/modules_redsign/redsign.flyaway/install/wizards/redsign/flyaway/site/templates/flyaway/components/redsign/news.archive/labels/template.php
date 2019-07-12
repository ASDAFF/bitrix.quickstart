<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

use Bitrix\Main\Localization\Loc;

?>
<?php if (is_array($arResult['YEARS']) && count($arResult['YEARS']) > 0): ?>

<div class="news-archive">
	<?php if ($arParams['SHOW_TITLE']): ?>
		<span class=""><?=Loc::getMessage('RS_FLYAWAY.RNA_FLYAWAY.ARCHIVE')?></span>
	<?php endif; ?>
	
	<ul class="news-archive__list list-inline">
		<li class="news-archive__item">
        	<a<?php if (!$arResult['HAS_SELECTED']): ?> class="btn btn2"<?php endif; ?> href="<?=$arParams['SEF_FOLDER']?>"><?=Loc::getMessage('RS_FLYAWAY.RNA_FLYAWAY.ALL')?></a>
    	</li>
    	<?php foreach ($arResult['YEARS'] as $iYear => $arYear): ?>
    		<?php $sYearId = $this->getEditAreaId($iYear); ?>
    		<li class="news-archive__item">
    			<?php if ($arParams['SHOW_YEARS']): ?>
            		<a<?php if ($arYear['SELECTED']): ?> class="btn btn2"<?php endif; ?> href="<?=$arYear['ARCHIVE_URL']?>"><?=$arYear['NAME']?> (<?=$arYear['COUNT']?>)</a>
        		<?php endif; ?>
    
        		<?php
        		if (
        		    $arParams['SHOW_MONTHS'] &&
    		        is_array($arYear['MONTHS']) && count($arYear['MONTHS']) > 0
        		):
        		?>
        			<ul>
        				<?php foreach ($arYear['MONTHS'] as $arMonth): ?>
        					<li class="news-archive__item">
    							<a class="btn btn2" href="<?=$arMonth['ARCHIVE_URL']?>"><?=$arMonth['NAME']?> (<?=$arMonth['COUNT']?>)</a>
    						</li>
        				<?php endforeach; ?>
        			</ul>
        		<?php endif; ?>

        	</li>
    	<?php endforeach; ?>
	</ul>
</div>

<?php endif; ?>