<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?php  //echo "<pre>"; print_r($arResult); echo "</pre>";?>
<section class="container" id="content-container">
	<div class="row-fluid">



<section id="content" class="span12 posts">

				    <article class="post single">
				    	<div class="post-offset">
				    	<div class="row-fluid">
				    		<div class="span7">
				    		<?$file = CFile::ResizeImageGet($arResult['DETAIL_PICTURE'], array('width'=>533, 'height'=>377), BX_RESIZE_IMAGE_EXACT, true);           ?>
					    		<img src="<?=$file[src]?>" style="width: 533px;height;377px; border-left-width: 0px; margin-left: 70px; margin-top: 5px;"/>
				    		</div>
				    		
				    		<div class="span5">
				    		
						    	<h1><?echo $arResult["NAME"];?></h1>
						    	<?php
						    	$data=$arResult[PROPERTIES][DATA][VALUE];
						    	$data=explode(" ", $data);
						    	$time=explode(":", $data[1]);?>
						    	<time datetime="<?=$data[0]?> <span><?=$time[0]?>:<?=$time[1]?></span>" class="entry-date"><?=$data[0]?> <span><?=$time[0]?>:<?=$time[1]?></span></time>
						    <?echo $arResult["DETAIL_TEXT"];?>	
				    		</div>
				    	</div>
				    	
				    	<div class="post-options">
				    	<div class="ks_social">
							<?$APPLICATION->IncludeComponent(
								"bitrix:main.share",
								"",
								Array(
									"HIDE" => "N",
									"HANDLERS" => array("delicious","facebook","lj","mailru","twitter","vk"),
									"PAGE_URL" => $arResult["DETAIL_PAGE_URL"],
									"PAGE_TITLE" => $arResult["NAME"],
									"SHORTEN_URL_LOGIN" => "",
									"SHORTEN_URL_KEY" => ""
								),
							false
							);?>
				    	</div>
				    	</div>
				    	</div>
				    </article><!-- /post -->
				    
			    </section><!-- /content -->

	</div>
</section>
<!-- content-container -->


