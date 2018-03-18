<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
if(!$arResult['error_msg']){
?>
<script type="text/javascript">
var $j = jQuery.noConflict();
$j(document).ready(function(){

		$j(".bg_mlife_photo_row a").fancybox({
			prevEffect	: 'none',
			nextEffect	: 'none',
			'padding' : 0,
			<?if($arParams['WIEV_TRUMB']==1){?>
			helpers	: {
				title	: {
					type: 'outside'
				},
				 thumbs	: {
					 width	: <?=$arParams['FANCY_TRUMB_WIDTH']?>,
					 height	: <?=$arParams['FANCY_TRUMB_HEIGHT']?>
				 }
			}
			<?}?>
		});
});
</script>
<?
echo '<div class="wrap_mlifegalery">';
	foreach($arResult['photo'] as $photo) {
		echo '<div class="photo_row"><div class="bg_mlife_photo_row"><a href="'.$photo['src_big'].'" rel="'.$arParams['ID_IST'].$arParams['ID_GAL'].'"><img src="'.$photo['src'].'"/></a></div></div>';
	}
echo'</div>';

if($arParams['READMORE']==1){
echo '<div class="readvkbut"><a target="_blank" href="'.$arResult['show_readmore_href'].'">'.GetMessage("MLIFE_VKG_READMORE_SHOW").'</a></div>';
}

}else {
	echo $arResult['error_msg'];
}
?>