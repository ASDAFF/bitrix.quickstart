<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (method_exists($this, 'setFrameMode')) {
	$this->setFrameMode(true);
}
?>
<?if (count($arResult) == 1):?>
	<img src="<?= $arResult[0]['PICTURE']['SRC']?>" alt="<?= $arResult[0]['NAME']?>" id="asd_slider_pics" />
<?elseif (!empty($arResult)):?>
	<?$fID = __asd_pics_slider_inc();?>
	<script type="text/javascript">
		if (typeof requestTimeout != 'function') {
			window.requestTimeout = function(fn, delay) {
				return setTimeout(fn, delay);
			}
		}

		var iFirstPic<?=$fID?> = 0;
		var iCuurId<?=$fID?> = 0;
		var oImg<?=$fID?> = new Image();
		var aPics<?=$fID?> = new Array();
	<?foreach ($arResult as $i => $arItem):?>
		var aItem = new Array();
		aItem['src'] = '<?= $arItem['PICTURE']['SRC']?>';
		aItem['width'] = '<?= $arItem['PICTURE']['WIDTH']?>';
		aItem['height'] = '<?= $arItem['PICTURE']['HEIGHT']?>';
		aPics<?=$fID?>[<?= $i?>] = aItem;
	<?endforeach;?>
		function ASD_ChangePic_<?=$fID?>()
		{
			iFirstPic<?=$fID?>++;
			if (!aPics<?=$fID?>[iFirstPic<?=$fID?>])
				iFirstPic<?=$fID?> = 0;
			if (aPics<?=$fID?>[iFirstPic<?=$fID?>+1])
				oImg<?=$fID?>.src = aPics<?=$fID?>[iFirstPic<?=$fID?>+1]['src'];

			$('#asd_slider_pics<?=$fID?>_' + (iCuurId<?=$fID?>)).fadeOut('600');

			$('#asd_slider_pics<?=$fID?>_' + (iCuurId<?=$fID?>==0 ? 1 : 0)).attr('src', aPics<?=$fID?>[iFirstPic<?=$fID?>]['src']);
			$('#asd_slider_pics_wrapper<?=$fID?>').css('width', aPics<?=$fID?>[iFirstPic<?=$fID?>]['width']);
			$('#asd_slider_pics_wrapper<?=$fID?>').css('height', aPics<?=$fID?>[iFirstPic<?=$fID?>]['height']);

			$('#asd_slider_pics<?=$fID?>_' + (iCuurId<?=$fID?>==0 ? 1 : 0)).fadeIn('200');

			iCuurId<?=$fID?> = iCuurId<?=$fID?>==0 ? 1 : 0;

			requestTimeout(ASD_ChangePic_<?=$fID?>, <?=$arParams['TIME']?>*1000);
		}
		$(document).ready(function (){
			oImg<?=$fID?>.src = aPics<?=$fID?>[1]['src'];
			requestTimeout(ASD_ChangePic_<?=$fID?>, <?=$arParams['TIME']?>*1000);
		});
	</script>
	<?if ($arParams['LINK'] != ''){?><a href="<?= $arParams['LINK']?>"><?}?>
	<div style="position: relative; width: <?= $arResult[0]['PICTURE']['WIDTH']?>px; height: <?= $arResult[0]['PICTURE']['HEIGHT']?>px;" id="asd_slider_pics_wrapper<?=$fID?>">
		<img src="<?= $arResult[0]['PICTURE']['SRC']?>" alt="" id="asd_slider_pics<?=$fID?>_0" style="position: absolute; z-index: 0;" />
		<img src="<?= $arResult[1]['PICTURE']['SRC']?>" alt="" id="asd_slider_pics<?=$fID?>_1" style="position: absolute; display: none; z-index: 1;" />
	</div>
	<?if ($arParams['LINK'] != ''){?></a><?}?>

<?endif;?>