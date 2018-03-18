<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?foreach($arResult["ITEMS"] as $key=>$Item){$Item_ids[$key] = $Item["ID"];} $count2=count($arResult["ITEMS"])-1;?>
<?$slide_speed = $arParams["SLIDE_SPEED"]*1000;?>
<?$slide_fade = $arParams["SLIDE_FADE"]*10;?>

<script type="text/javascript" src="<?=$templateFolder?>/opacity.js"></script>

<script language="javascript" type="text/javascript">
global_key2 = 0;
sliding2 = true;
fadeOpacity.addRule('oR2', 0.1, 1, <?echo$slide_fade;?>);
fadeOpacity.addRule('oR1', 1, 0.1, 10);

function rc_mo2(key2){
    var rc_img2 = document.getElementById('rc_img2_'+global_key2);
    rc_img2.style.display='none';
    fadeOpacity('rc_img2_'+global_key2, 'oR1');

	var rc_img2 = document.getElementById('rc_img2_'+key2);
    rc_img2.style.display='block';
    fadeOpacity('rc_img2_'+key2, 'oR2');
    global_key2 = key2;
}

function rc_slide2(count2){
    if (sliding2 == true){
    	if (global_key2 == count2){
            var rc_img2 = document.getElementById('rc_img2_'+global_key2);
            rc_img2.style.display='none';
            fadeOpacity('rc_img2_'+global_key2, 'oR1');
			var rc_img2 = document.getElementById('rc_img2_'+0);
            rc_img2.style.display='block';
            fadeOpacity('rc_img2_'+0, 'oR2');
    		global_key2 = 0;
    	}else{
            var key2 = global_key2;
            key2 = ++key2;
            var rc_img2 = document.getElementById('rc_img2_'+global_key2);
            rc_img2.style.display='none';
            fadeOpacity('rc_img2_'+global_key2, 'oR1');
        	var rc_img2 = document.getElementById('rc_img2_'+key2);
       		rc_img2.style.display='block';
            fadeOpacity('rc_img2_'+key2, 'oR2');
    		global_key2 = key2;
    	}
    }
	setTimeout("rc_slide2('<?echo$count2;?>')",<?echo$slide_speed;?>);
}

setTimeout("rc_slide2('<?echo$count2;?>')",<?echo$slide_speed;?>);

</script>

<?foreach($arResult["ITEMS"] as $key=>$Item){?>
<?$file = CFile::ResizeImageGet($arResult["ITEMS"][$key]["DETAIL_PICTURE"]["ID"], array('height'=>$arParams[SLIDE_HEIGHT], 'width'=>$arParams[SLIDE_WIDTH]), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
	<div style="display:<?if($key==0){?>block;<?}else{?>none;<?}?> background: url(<?=$file["src"]?>) left top no-repeat; width:<?=$arParams['SLIDE_WIDTH']?>px; height:<?=$arParams['SLIDE_HEIGHT']?>px;" id="rc_img2_<?=$key?>">
		<table cellpadding="0" cellspacing="0" width="100%" height="<?=$file['height']?>">
        <tr><td valign="top" align="left" id="slide_show_title">
        	<span class="sst_name"><?=$Item["NAME"]?></span><br/>
        </td></tr>
        <tr><td width="100%" height="50px" valign="bottom" align="right">
			<table id="slide_show_slider" cellpadding="0" cellspacing="0"><tr><td valign="middle" align="right">
			<?foreach($arResult["ITEMS"] as $key2=>$Item2):?>
    			<img class="img_bar" src="<?=$Item2["PREVIEW_PICTURE"]["SRC"]?>" id="li2_<?=$key2?>" onMouseOut="sliding2=true; this.style.border='solid 2px #000000';" onMouseOver="sliding2=false; this.style.border='solid 2px #ffffff';" onClick="rc_mo2('<?echo$key2;?>');">
			<?endforeach;?>
    		</td></tr></table>
  		</td></tr></table>
	</div>
<?}?>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>
