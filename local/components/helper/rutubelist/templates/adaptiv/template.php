<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if($arResult["MESSAGE"] != ""):?>
<?=$arResult["MESSAGE"];?>
<?endif;?>

<?if($arParams["DISPLAY_TOP_PAGER"] == "Y"):?>
<?=$arResult["NAV_STRING"]?>
<?endif;?>

<?if ($arResult['VIDEO']) {?>

<div class="breweries">
    <ul>
	<?foreach ($arResult['VIDEO'] as $v):?>
		<li>
		<a class="openvideo fancybox.iframe" href="https://rutube.ru/play/embed/<?=$v['id']?>" >
		<div class="vid" style="background: url(<? echo  $v['imgs'];  ?>);">  <span></span></div>
				<?if($arParams["WIN1251"]!="N"):?>
				<? $v['title'] = iconv('utf8', 'cp1251', $v['title']);?>
				<?endif;?>
				<p><?=$v['title']?></p>
		</a>
		</li>
<?endforeach; ?>
</ul>
</div>

<?} else {
    echo GetMessage("INKOREKT");
}
?>

<?if($arParams["DISPLAY_BOTTOM_PAGER"] == "Y"):?>
<br><?=$arResult["NAV_STRING"]?>
<?endif;?>

