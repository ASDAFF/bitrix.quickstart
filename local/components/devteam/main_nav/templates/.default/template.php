<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
die();

$arrNums = array(1 => 'first', 'second', 'third', 'fourth');

$num = 0;

function hasChildren__($arResult, $id) {
    foreach ($arResult['IBLOCKS'] as $iblock_id => $iblock_arr)
        foreach ($iblock_arr['SECTIONS'] as $section)
            if ($section['IBLOCK_SECTION_ID'] == $id)
                return true;
    return false;
}

function findLastFkey($ar) {
    foreach ($ar as $k_ => $section) {
        if ($section['DEPTH_LEVEL'] != 1)
            continue;
        $lastFk = $k_;
    }
    return $lastFk;
}
?>
<ul class="b-menu clearfix">
<?
foreach ($arResult['IBLOCKS'] as $iblock_id => $iblock_arr) {
$num++;
?>    
<li class="b-menu__item <? if ($num > 1) { ?>m-item__<?= $arrNums[$num]; ?><? } ?> <? if ($arResult['SELECTED'] == $iblock_id) { ?> b-menu__selected<? } ?>">
<a href="<?= $iblock_arr["IBLOCK"]["LIST_PAGE_URL"] ?>" class="b-menu__link"><?= $iblock_arr["IBLOCK"]['NAME']; ?></a>
<? if ($iblock_arr['SECTIONS']) { ?>
<div class="b-menu_level2 m-menu__<?= $arrNums[$num]; ?>"> 
<?
$sectCnt = 0;

$last_k = findLastFkey($iblock_arr['SECTIONS']);

foreach ($iblock_arr['SECTIONS'] as $k_ => $section) {
if ($section['DEPTH_LEVEL'] != 1)
continue;

$sectCnt++;
if ($sectCnt > 3)
$sectCnt = 1;

if ($sectCnt == 1) {
$childrens = array();
?>   
<div class="b-level2__line clearfix">
<? } ?>
<?
if (hasChildren__($arResult, $section['ID'])) {
$childrens[] = $section['ID'];
?>
<a href="#ITEM_<?= $section['ID'] ?>" class="b-level2__item b-level2__has-child"><span><?= $section['NAME'] ?></span></a>
<? } else { ?>
<a href="<?= $section['SECTION_PAGE_URL'] ?>" class="b-level2__item"><span><?= $section['NAME'] ?></span></a>
<? } ?> 
<? if ($sectCnt == 3 || $k_ == $last_k) { ?> </div> 
<? if (count($childrens)) {
foreach ($childrens as $children) {
?><div class="b-menu-level3" id="ITEM_<?= $children; ?>"><?
$childrenCnt = 0;
foreach ($iblock_arr['SECTIONS'] as $section) {

    if ($section['IBLOCK_SECTION_ID'] != $children)
        continue;

    $childrenCnt++;
    if ($childrenCnt > 3)
        $childrenCnt = 1;

    if ($childrenCnt == 1) {
        ?>
   <div class="b-level3__line clearfix">
    <? } ?> <a href="<?= $section['SECTION_PAGE_URL'] ?>" class="b-level3__item"><span><?= $section['NAME'] ?></span></a>
<? if ($childrenCnt == 3) { ?>    
 </div>
<? } 
 } ?>
<? if ($childrenCnt != 3 && $childrenCnt != 0) { ?>
</div>
<? } ?>
</div>
<? } 
 } 
 } 
 } 
 if ($sectCnt != 3 && $sectCnt != 0 && $k_ == $last_k) { ?>
</div>
<? } ?>
</div>
<? } ?>
</li>
<?
}
?></ul>