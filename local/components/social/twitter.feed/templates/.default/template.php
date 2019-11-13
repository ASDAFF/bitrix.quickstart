<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>

<script src="<?=$templateFolder?>/jquery.jscrollpane.min.js" type="text/javascript"></script>
<script src="<?=$templateFolder?>/jquery.mousewheel.js" type="text/javascript"></script>

<div class="twitter_container" style="width: <?=$arResult['WIDTH']?>px; height: <?=$arResult['HEIGHT']?>px;">
	<div class="twitter_line_top"><div class="twitter_line_top_left"></div><div class="twitter_line_top_center"><div class="twitter_line_top_right"></div></div></div>
	<div class="twitter_head">
    	<img src="<?=$templateFolder?>/images/twitter_icon.png" alt="Twitter" width="24" height="16" /> <?=$arResult['TITLE']?>
    </div>
    <div class="twitter_content preloader">  
    </div>
</div>