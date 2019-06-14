<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

					<div class="box_main"><div class="demo"><div class="box_left">
					<ul>

					<?foreach ($arResult["ITEMS"] as $arElement):?>
						<li style="float: left; list-style: none; margin: 10px 10px 10px 0;">

							<a href="<?=$arElement["BIG_IMG"]["SRC"]?>"  rel="gallery"  class="pirobox_gall" title="<?=$arElement["NAME"]?>">

									<img src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" alt="" />

							</a>
						</li>
						
					<?endforeach;?>
					</ul>
					
</div>
</div>
					
</div>

					<div class="clear"></div>


<script type="text/javascript" src="/bitrix/components/point/gallery/templates/.default/js/jquery.min.js"></script>
<script type="text/javascript" src="/bitrix/components/point/gallery/templates/.default/js/jquery-ui-1.8.2.custom.min.js"></script>
<script type="text/javascript" src="/bitrix/components/point/gallery/templates/.default/js/pirobox_extended.js"></script>
					
<script type="text/javascript">
$(document).ready(function() {
	$().piroBox_ext({
	piro_speed : 700,
		bg_alpha : 0.5,
		piro_scroll : true // pirobox always positioned at the center of the page
	});
});
</script>