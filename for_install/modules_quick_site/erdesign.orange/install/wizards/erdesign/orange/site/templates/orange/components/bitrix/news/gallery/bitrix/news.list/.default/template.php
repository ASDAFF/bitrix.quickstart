<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>




			    <section id="content" class="span8 blog posts">

			    
			    <?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>

			    <?$file = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width'=>354, 'height'=>216), BX_RESIZE_IMAGE_EXACT, true);           ?>
				    <article class="post"  id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				    	<div class="post-offset">
				    	<figure class="post-icon"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title=""><img src="<?=$file[src]?>" alt=""/></a></figure>
				    	<h1><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title=""><?echo $arItem["NAME"];?></a></h1>
				    	<!-- <time datetime="2013-01-09T15:19:59+00:00" class="entry-date">added: 9th January 2013</time> -->
				    	<p><?echo $arItem["PREVIEW_TEXT"];?></p>
				    	<div class="post-options">
				    	<div class="ks_social">
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
				    		
				    		<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-medium more-link"><!-- <span class="icon icon-bubble-1"></span>  --><span class="resp"><?=GetMessage("MORE")?></span></a>
				    	</div>
				    	</div>
				    </article><!-- /post -->
				    
				 <?endforeach;?>

				    <div class="clearfix"></div>


				  
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
				    
			    </section><!-- /content -->
			   
			   







