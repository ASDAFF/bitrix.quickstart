<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->AddHeadString('<link href="/local/components/content/simple.slider.gallery/js/photoslider.css" type="text/css" rel="stylesheet" />',true)?>
<?$APPLICATION->AddHeadString('<script  type="text/javascript" src="/local/components/content/simple.slider.gallery/js/jquery.js"></script>',true)?>
<?$APPLICATION->AddHeadString('<script  type="text/javascript" src="/local/components/content/simple.slider.gallery/js/photoslider.js"></script>',true)?>

<div id="default" class="photoslider"></div>
<script type="text/javascript">
 $(document).ready(function(){
 	FOTO.Slider.baseURL = '<?="http://".$_SERVER["HTTP_HOST"]?>'; 
   
    FOTO.Slider.bucket = {  
        'default': {  
           <?foreach($arResult["ITEMS"] as $key=>$arItem):?> 
		    <?=$key?>: {'thumb': '<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>', 'main': '<?=$arItem["DETAIL_PICTURE"]["SRC"]?>', 'caption': '<?=$arItem["NAME"]?>'},
		   <?endforeach;?>
			
       }  
    };  
	
	FOTO.Slider.reload('default');
	
	<? if($arParams["SSG_PRELOAD_IMG"] == "Y"):?>
		FOTO.Slider.preloadImages('default'); 
	<? endif; ?>
	
	<? if($arParams["SSG_SLIDESHOW_MODE"] == "Y"):?>
		FOTO.Slider.enableSlideshow('default');
	<? endif;?>  
	
	<? if($arParams["SSG_ONLOAD_START"] == "Y"):?>
		FOTO.Slider.play('default');
	<? endif;?>  
 });
</script> 
