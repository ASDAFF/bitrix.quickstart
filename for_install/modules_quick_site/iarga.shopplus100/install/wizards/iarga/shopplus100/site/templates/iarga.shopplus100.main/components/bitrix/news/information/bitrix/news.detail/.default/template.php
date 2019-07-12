<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $templateFolder;?>
           
<h1><?=$arResult['NAME']?></h1>

<article>
	<?=$arResult['DETAIL_TEXT']?>
</article>

<div class="other-articles">
	
	<ul class="column-wrap">
		<?if($arResult['PREV']):?>
			<li class="box">
				<div class="narrow">
					<p class="subtitle"><img src="<?=$templateFolder?>/images/icon-article-prev.gif" alt=""> <?=GetMessage("PREV_ARTICLE")?>:</p>
					<p class="title"><a href="<?=$arResult['PREV']['DETAIL_PAGE_URL']?>"><?=$arResult['PREV']['NAME']?></a></p>
				</div><!--.narrow-end-->
			<!--.box-end-->
		<?endif;?>
		<?if($arResult['NEXT']):?>
			<li class="box">
				<div class="narrow">
					<p class="subtitle"><img src="<?=$templateFolder?>/images/icon-article-next.gif" alt=""> <?=GetMessage("NEXT_ARTICLE")?>:</p>
					<p class="title"><a href="<?=$arResult['NEXT']['DETAIL_PAGE_URL']?>"><?=$arResult['NEXT']['NAME']?></a></p>
				</div><!--.narrow-end-->
			<!--.box-end-->
		<?endif;?>
	</ul><!--.column-wrap-end-->
	
</div><!--.other-articles-end-->

