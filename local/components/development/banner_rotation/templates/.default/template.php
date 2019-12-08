<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arParams['JQUERY']=="Y"):?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<?endif;?>
<?if(is_array($arResult['BANNERS']) && !empty($arResult['BANNERS'])):?>
<? $block_id = 'beono_banner_slider_'.randString(5, 'abcdef01234');?>
<div id="<?=$block_id;?>" class="<?=$arResult['SLIDER_CSS_CLASS']?>" style="<?if($arParams['WIDTH']):?>width:<?=$arParams['WIDTH'];?>;<?endif;?><?if($arParams['HEIGHT']):?>height:<?=$arParams['HEIGHT'];?><?endif;?>">
	<?if($arParams['PAGER_STYLE'] != 'none' && count($arResult['BANNERS'])>1):?>
	<div class="beono-banner_slider-pager">
		<div class="beono-banner_slider-pager-inner">
		<?foreach($arResult['BANNERS'] as $key=>$arBanner):?>
				<?if($arParams['PAGER_STYLE']=='digits') {
					$page_value = ($key+1);
				} elseif(in_array($arParams['PAGER_STYLE'], array('text', 'amazon'))) {
					$page_value = $arBanner['FIELDS']['NAME'];
				} elseif($arParams['PAGER_STYLE']=='thumbs') {
					$arFileTmp = CFile::ResizeImageGet($arBanner['FIELDS']['IMAGE_ID'], array("width" => 64, "height" => 64), BX_RESIZE_IMAGE_PROPORTIONAL, true);		
					$page_value = '<img width="'.$arFileTmp['width'].'" height="'.$arFileTmp['height'].'" src="'.$arFileTmp['src'].'" />';
				} else {
					$page_value = '&nbsp'; 
				}?>
			<a <?if($key==0):?>class="active"<?endif;?> href="#" title="<?=$arBanner['FIELDS']['NAME'];?>"><?=$page_value?></a>
		<?endforeach;?>
		</div>
	</div>
	<?endif;?>
	<div class="beono-banner_slider-wrapper">
	<?foreach($arResult['BANNERS'] as $key=>$arBanner):?>
		<?if($arParams['PAGER_STYLE']=='thumbs'):?>
		<div class="beono-banner_slider-item">			
			<?$arImage = CFile::GetFileArray($arBanner['FIELDS']['IMAGE_ID']);?>
			<img alt="<?=$arBanner['FIELDS']['NAME'];?>" width="<?=$arImage['WIDTH'];?> height="<?=$arImage['HEIGHT']?>" src="<?=$arImage['SRC'];?>" />
			<p>
			<?if($arBanner['FIELDS']['URL']):?>
				<a href="<?=$arBanner['FIELDS']['URL'];?>" <?if($arBanner['FIELDS']["URL_TARGET"]):?>target="<?=$arBanner['FIELDS']["URL_TARGET"];?>"<?endif;?>><?=$arBanner['FIELDS']['NAME'];?></a><br/>
			<?else:?>
				<?=$arBanner['FIELDS']['NAME'];?><br/>
			<?endif;?>
			<?if($arBanner['FIELDS']['IMAGE_ALT']):?>
				<span><?=$arBanner['FIELDS']['IMAGE_ALT'];?></span>
			<?endif;?>
			</p>			
		</div>
		<?else:?>
		<div class="beono-banner_slider-item"><?=$arBanner['HTML'];?></div>
		<?endif;?>
	<?endforeach;?>
	</div>	
</div>
<?endif;?>
<?if(count($arResult['BANNERS'])>1):?>
<script type="text/javascript">	
/* <![CDATA[ */
(function($) {
	var obBeonoRotation = new Beono_Banner_Rotation ({
		id : '<?=$block_id;?>',
		transition_speed: <?=$arParams['TRANSITION_SPEED'];?>,
		transition_interval: <?=$arParams['TRANSITION_INTERVAL'];?>,
		effect: '<?=$arParams['EFFECT'];?>',
		stop_on_focus: <?=($arParams['STOP_ON_FOCUS']=='Y')?'true':'false';?>
	});
	<?if($arParams['PAGER_STYLE']=='amazon'):?>
	obBeonoRotation.onAfterShowBanner = function(banner_index) {
		if ($('.beono-banner_slider-cursor', this.context).length < 1) {
			$('.beono-banner_slider-pager', this.context).append('<img class="beono-banner_slider-cursor" src="<?=$this->GetFolder();?>/images/mzn-arrow.png">');
		}
		var cursor_position = $('a.active', this.context).position();
		var cursor_position_left = (cursor_position.left-135) + ($('a.active', this.context).width()/2);

		$('.beono-banner_slider-cursor', this.context).css({			
			//'top': $('.beono-banner_slider-pager', this.context).outerHeight()+'px'		
		}).animate({ 'left': cursor_position_left + 'px'}, this.transition_speed); 
	};
	<?endif;?>
	obBeonoRotation.init();

	
})(jQuery);
/* ]]> */
</script>
<?endif;?>