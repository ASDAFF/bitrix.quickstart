<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?// echo "<pre>"; print_r($arResult); echo "</pre>";?>
<section id="content" class="span8 blog posts">
<article class="post single">
				    	<div class="post-offset">
 <? //echo "<pre>"; print_r($arResult); echo "</pre>";?> 
  <?$file = CFile::ResizeImageGet($arResult['PREVIEW_PICTURE'], array('width'=>354, 'height'=>216), BX_RESIZE_IMAGE_EXACT, true);           ?>
<figure class="post-icon"><a rel="lightbox" href="<?=$file[src]?>"><img alt="" src="<?=$file[src]?>"></a></figure>
<h1><?=$arResult["NAME"]?></h1>
<?=$arResult["DETAIL_TEXT"]?>





<ul class="gallery">
 <?foreach($arResult["PROPERTIES"]["PHOTO"]["VALUE"] as $arphoto):?>
   <?$file1 = CFile::ResizeImageGet($arphoto, array('width'=>150, 'height'=>150), BX_RESIZE_IMAGE_EXACT, true);           ?>
     <?$file2 = CFile::ResizeImageGet($arphoto, array('width'=>400, 'height'=>400), BX_RESIZE_IMAGE_EXACT, true);           ?>
	<li><figure>
	<a rel="lightbox" href="<?=$file2[src]?>"><img src="<?=$file1[src]?>"/></a>
	</figure></li>
		 <?endforeach;?>

</ul>


<div class="post-options">
				    	
							<?$APPLICATION->IncludeComponent(
								"bitrix:main.share",
								"",
								Array(
									"HIDE" => "N",
									"HANDLERS" => array("delicious","facebook","lj","mailru","twitter","vk"),
									"PAGE_URL" => $arItem["DETAIL_PAGE_URL"],
									"PAGE_TITLE" => $arItem["NAME"],
									"SHORTEN_URL_LOGIN" => "",
									"SHORTEN_URL_KEY" => ""
								),
							false
							);?>
				    	
				    		
				    		
				    	</div>


</div></article>
</section>
