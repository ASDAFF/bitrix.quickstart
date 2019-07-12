<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$bOpen = false;
$count = $arResult["ITEMS"];
?>



<?//print_R($arResult["ITEMS"])?>

<?$c=count($arResult["ITEMS"]); $C_rows=ceil($c/4);?>
	
        <div><a href="/brand/"><strong><?=GetMessage('BRENDY');?></strong> (<?=GetMessage('VSE');?> <?=count($arResult["ITEMS"]);?>)</a></div>
        <hr>
		

<?for($j=0;$j<$C_rows;$j++):?>
<div class="row">
	<?$i=0;?>
	<div class="col-md-3 col-sm-6">
		<?if($arResult["ITEMS"][$j*4+$i]['ID']):?>
			<div class="media">
              <div class="media-left media-middle">
                <a href="<?=$arResult["ITEMS"][$j*4+$i]['DETAIL_PAGE_URL']?>">
					<?$img=CFile::ResizeImageGet($arResult["ITEMS"][$j*4+$i]['PROPERTIES']['MENU_PIC']['VALUE'], array('width'=>65, 'height'=>65), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                  <img class="media-object" alt="" src="<?=$img['src'];?>">
                </a>
              </div>
              <div class="media-body media-middle">
                <a href="<?=$arResult["ITEMS"][$j*4+$i]['DETAIL_PAGE_URL']?>"><?=$arResult["ITEMS"][$j*4+$i]['NAME']?></a>
              </div>
            </div>
		<?endif;?>
	</div>	
	<hr class="visible-xs-block clearfix">
	<?$i=1;?>
	<div class="col-md-3 col-sm-6">
		<?if($arResult["ITEMS"][$j*4+$i]['ID']):?>
			<div class="media">
              <div class="media-left media-middle">
                <a href="<?=$arResult["ITEMS"][$j*4+$i]['DETAIL_PAGE_URL']?>">
					<?$img=CFile::ResizeImageGet($arResult["ITEMS"][$j*4+$i]['PROPERTIES']['MENU_PIC']['VALUE'], array('width'=>65, 'height'=>65), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                  <img class="media-object" alt="" src="<?=$img['src'];?>">
                </a>
              </div>
              <div class="media-body media-middle">
                <a href="<?=$arResult["ITEMS"][$j*4+$i]['DETAIL_PAGE_URL']?>"><?=$arResult["ITEMS"][$j*4+$i]['NAME']?></a>
              </div>
            </div>
		<?endif;?>
	</div>	
    <div class="visible-xs-block visible-sm-block clearfix"></div>
    <hr class="visible-xs-block visible-sm-block">	
	<?$i=2;?>
	<div class="col-md-3 col-sm-6">
		<?if($arResult["ITEMS"][$j*4+$i]['ID']):?>
			<div class="media">
              <div class="media-left media-middle">
                <a href="<?=$arResult["ITEMS"][$j*4+$i]['DETAIL_PAGE_URL']?>">
					<?$img=CFile::ResizeImageGet($arResult["ITEMS"][$j*4+$i]['PROPERTIES']['MENU_PIC']['VALUE'], array('width'=>65, 'height'=>65), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                  <img class="media-object" alt="" src="<?=$img['src'];?>">
                </a>
              </div>
              <div class="media-body media-middle">
                <a href="<?=$arResult["ITEMS"][$j*4+$i]['DETAIL_PAGE_URL']?>"><?=$arResult["ITEMS"][$j*4+$i]['NAME']?></a>
              </div>
            </div>
		<?endif;?>
	</div>	
	<hr class="visible-xs-block clearfix">
	<?$i=3;?>
	<div class="col-md-3 col-sm-6">
		<?if($arResult["ITEMS"][$j*4+$i]['ID']):?>
			<div class="media">
              <div class="media-left media-middle">
                <a href="<?=$arResult["ITEMS"][$j*4+$i]['DETAIL_PAGE_URL']?>">
					<?$img=CFile::ResizeImageGet($arResult["ITEMS"][$j*4+$i]['PROPERTIES']['MENU_PIC']['VALUE'], array('width'=>65, 'height'=>65), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                  <img class="media-object" alt="" src="<?=$img['src'];?>">
                </a>
              </div>
              <div class="media-body media-middle">
                <a href="<?=$arResult["ITEMS"][$j*4+$i]['DETAIL_PAGE_URL']?>"><?=$arResult["ITEMS"][$j*4+$i]['NAME']?></a>
              </div>
            </div>
		<?endif;?>
	</div>

</div>
<hr>
<?//$i++;?>
<?endfor?>




