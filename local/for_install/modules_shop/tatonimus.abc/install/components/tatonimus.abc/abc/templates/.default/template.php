<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->SetTemplateCSS("style.css");
if (!empty($arResult['ABC'])) {
?>
<ul class="catalogAbcLabels">
<?
    foreach ($arResult['ABC'] as $letter => $arAbc) {
        ?><li><a href="#abc<?=$letter?>"><?=$letter?></a></li><?
    }
?>
</ul>
<ul class="catalogAbcList">
<?
    foreach ($arResult['ABC'] as $letter => $arAbc) {
?>
    <li id="abc<?=$letter?>">
        <span class="catalogAbcLetter"><?=$letter?></span>
        <ul class="catalogAbcGroup">
<?
        foreach ($arAbc as $value) {
            ?><li><a href="<?=$value['URL']?>"><?=$value['NAME']?><? if ($value['CNT'] > 0) {?> (<?=intval($value['CNT'])?>)<? } ?></a></li><?
        }
?>
        </ul>
    </li>
<?
    }
?>
</ul>
<? } else { ?>
<p><?=GetMessage('TABC_EMPTY_LIST')?></p>
<? } ?>