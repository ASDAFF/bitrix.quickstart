<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
$template = '/bitrix/templates/iarga.shopplus100.main';
IncludeTemplateLangFile($template.'/header.php');
include($_SERVER['DOCUMENT_ROOT'].$template."/inc/functions.php");
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

$arElement = GetIBlockElement($_POST['id']);
if($arElement['PREVIEW_PICTURE']=='') $arElement['PREVIEW_PICTURE'] = $arElement['DETAIL_PICTURE'];

$price = iarga::getprice($arElement['ID']);
$oldprice = $arElement['PROPERTIES']['oldprice']['VALUE'];
?>
<div class="title-container">
	<p class="title"><a href='#close'><?=$arElement['NAME']?></a></p>
	<p class="price">
		<?if($oldprice):?>
			<p class="price"><span class="old"><?=iarga::prep($oldprice)?> <?=GetMessage("VALUTE_MEDIUM")?></span> <span class="new"><?=iarga::prep($price)?> <?=GetMessage("VALUTE_MEDIUM")?></span></p>
		<?else:?>
			<?=iarga::prep($price)?> <?=GetMessage("VALUTE_MEDIUM")?>
		<?endif;?>
	</p>
</div><!--.title-container-end-->


<div class="royalSlider rsDefault">
	<?if($arElement['PREVIEW_PICTURE']>0):?>
		<a class="rsImg" data-rsBigImg="<?=iarga::res($arElement['PREVIEW_PICTURE'],900,900,1)?>" href="<?=iarga::res($arElement['PREVIEW_PICTURE'],400,400,1)?>"></a>
	<?endif;?>
	<?foreach($arElement['PROPERTIES']['photo']['VALUE'] as $i=>$photo):?>
		<a class="rsImg" data-rsBigImg="<?=iarga::res($photo,900,900,1)?>" href="<?=iarga::res($photo,400,400,1)?>"></a>
	<?endforeach;?>				
</div><!--.royalSlider-end-->

<div class="description-extended">
	<p><?=$arElement['DETAIL_TEXT']?></p>
	<div class="features">
		<?$n = 0;
		$maxn = 6;
		if(sizeof($arElement['PROPERTIES']['vars']['VALUE']) + sizeof($arElement['PROPERTIES']['props']['VALUE']) <= 9) $maxn = 9;
		foreach($arElement['PROPERTIES']['vars']['VALUE'] as $i=>$var):
			$n++;?>
				<?if($n == $maxn):?>
					<div class="hide">
						<span class="ellipsis">...</span>
							<div>
				<?endif;?>
			<p><span><?=$var?></span></p>
		<?endforeach;?>
		<?foreach($arElement['PROPERTIES']['props']['VALUE'] as $i=>$prop):
			$n++;?>
				<?if($n == $maxn):?>
					<div class="hide">
						<span class="ellipsis">...</span>
							<div>
				<?endif;?>
			<p><span><?=$prop?> -</span> <?=$arElement['PROPERTIES']['props']['DESCRIPTION'][$i]?></p>
		<?endforeach;?>
		<?if($n > $maxn):?>
				</div>
			</div><!--.hide-end-->
			<a href="#" class="dashed show-link"><?=GetMessage('SHOW_ALL')?></a>
		<?endif;?>
	</div><!--.features-end-->
</div><!--.description-extended-end-->