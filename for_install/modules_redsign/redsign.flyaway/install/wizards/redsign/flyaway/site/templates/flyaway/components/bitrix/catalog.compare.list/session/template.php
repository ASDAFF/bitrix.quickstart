<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$frame = $this->createFrame()->begin('');

$arrIDs = array();

foreach ($arResult as $arItem) {
	$arrIDs[$arItem['ID']] = 'Y';
}

?>
<?php $this->SetViewTarget("inheadcompare_mobile"); ?>
<a class="inmenucompare" href="<?=SITE_DIR.'catalog/'.$arParams["COMPARE_URL"]?>">
    <i class="fa fa-align-left"></i> 
    <div class="js-compareinfo" style="display: inline;">
        <span class="js-comparelist-count"><span class="count"><?=count($arResult)?></span></span>
    </div>
<a/>
<?php $this->EndViewTarget(); ?>
<script>
	rsFlyaway.compare = <?print(count($arrIDs) > 0 ? json_encode($arrIDs) : '{}')?>;
	rsFlyaway.count_compare = <?=(count($arResult))?>;
</script><?
