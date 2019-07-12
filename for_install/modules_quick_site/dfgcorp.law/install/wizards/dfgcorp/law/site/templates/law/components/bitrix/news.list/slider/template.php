<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
		jQuery(window).load(function(){
			var widthOfLIs = 0;
			var pagerWidth = 0;
			if ( jQuery('#slides .pagination').length ) {
				pagerWidth = jQuery('#slides .pagination').css('width');
				pagerWidth = pagerWidth.replace( 'px', '' );
				jQuery('#slides .pagination li').each( function () {
					var LIwidth = jQuery(this).css('width');
					LIwidth = LIwidth.replace( 'px', '' );
					widthOfLIs += parseInt( LIwidth );
				});
			}
			var carouselArgs = {
				buttonNextHTML: null,
				buttonPrevHTML: null
			};
			if ( ( widthOfLIs > pagerWidth ) && ( pagerWidth > 0 ) ) {
				carouselArgs = {};			
			}
		
			jQuery('#slides').slides({
				autoHeight: true,
				effect: 'slide',
				container: 'slides_container',
				slideSpeed: 0.6 * 1000,
				generateNextPrev: false,
				generatePagination: false
			});
			
			jQuery('#slides .pagination ul').jcarousel( carouselArgs );
		});
//-->!]]>
</script>
	
<style type="text/css">
	.slide-nav li { width: 250px; }
</style>
<div id="slides">
	<div class="slides_container col-full">
	<?$bFirst = true;?>
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<div class="slide"<?if($bFirst):?> style="display:block;"<?endif?>>
			<div class="slide-content entry fl"<?if($bFirst):?> style="width:380px;"<?endif?>>
				<h2 class="title"><?=$arItem["NAME"]?></h2>
				<p><?=$arItem["PREVIEW_TEXT"];?></p>
				<?if(strlen($arItem["PROPERTIES"]["description"]["VALUE"])>0):?>
					<a class="slide-content-link" href="<?=$arItem["PROPERTIES"]["link"]["VALUE"]?>"><?=GetMessage("LINK_NAME");?></a>
				<?endif?>
			</div><!-- /.slide-content -->

			<div class="slide-image fr">
				<img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arItem["NAME"]?>" />
			</div><!-- /.slide-image -->

			<div class="fix"></div>
		</div><!-- /.slide -->
		<?$bFirst=false;?>
	<?endforeach;?>
	</div>
	<div class="slide-nav">
		<div class="pagination col-full">
			<ul>
				<?foreach($arResult["ITEMS"] as $arItem):?>
					<li>
						<a href="#"><span class="title"><?=$arItem["NAME"]?></span> 
							<span class="content"><?=$arItem["PROPERTIES"]["description"]["VALUE"]?></span>
						</a>
					</li>
				<?endforeach;?>
			</ul>
		</div>
	</div>
	<div id="slider-bg-shadow"></div>
</div><!-- /.slides_container -->