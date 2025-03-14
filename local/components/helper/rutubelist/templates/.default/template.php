<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?if($arResult["MESSAGE"] != ""):?>
<?=$arResult["MESSAGE"];?>
<?endif;?>

<?if($arParams["DISPLAY_TOP_PAGER"] == "Y"):?>
<?=$arResult["NAV_STRING"]?>
<?endif;?>
 <?
if ($arResult['VIDEO']) {
$LINE_ELEMENT_COUNT=$arParams["VIDEO_LINE"];
?>
<div class="is-content">

<? foreach ($arResult['VIDEO'] as $v):?>

<?php $modwidth = round (100 / $LINE_ELEMENT_COUNT); 
$modwidth1 = $modwidth - 2;
?>

	<div class="is-element" style="width:<?php echo $modwidth1.'%';?>">

<a class="openvideo fancybox.iframe" href="https://rutube.ru/play/embed/<?=$v['id']?>" >

<div class="vid" style="background: url(<? echo  $v['imgs'];  ?>);">  <span></span></div>
<?if($arParams["WIN1251"]!="N"):?>
<? $v['title'] = iconv('utf8', 'cp1251', $v['title']);?>
<?endif;?>
<div class="is-title"><?=$v['title']?></div>
</a>


      </div>

   <?endforeach; ?>
</div>

<?php
} else {
    echo GetMessage("INKOREKT");
}
?>

<?if($arParams["DISPLAY_BOTTOM_PAGER"] == "Y"):?>
<br><?=$arResult["NAV_STRING"]?>
<?endif;?>

