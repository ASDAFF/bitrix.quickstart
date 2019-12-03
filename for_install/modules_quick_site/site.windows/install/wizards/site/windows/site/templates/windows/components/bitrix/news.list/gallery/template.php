<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jquery.isotope.js'></script>

<div class="news-list">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

<ul id="portfolio-grid" class="filterable-portfolio thumbnails portfolio-3cols" data-cols="3cols">
<script>
	jQuery(document).ready(function($) {
		var $container = jQuery('#portfolio-grid'),
			items_count = jQuery(".portfolio_item").size();

		$container.imagesLoaded( function(){
			setColumnWidth();
			$container.isotope({
				itemSelector : '.portfolio_item',
				hiddenClass : 'portfolio_hidden',
				resizable : false,
				transformsEnabled : true,
				layoutMode: 'fitRows'
			});
		});

		function getNumColumns(){
			var $folioWrapper = jQuery('#portfolio-grid').data('cols');

			if($folioWrapper == '2cols') {
				var winWidth = jQuery("#portfolio-grid").width(),
					column = 2;
				if (winWidth<380) column = 1;
				return column;
			}

			else if ($folioWrapper == '3cols') {
				var winWidth = jQuery("#portfolio-grid").width(),
					column = 3;
				if (winWidth<380) column = 1;
				else if(winWidth>=380 && winWidth<788) column = 2;
				else if(winWidth>=788 && winWidth<1160) column = 3;
				else if(winWidth>=1160) column = 3;
				return column;
			}

			else if ($folioWrapper == '4cols') {
				var winWidth = jQuery("#portfolio-grid").width(),
					column = 4;
				if (winWidth<380) column = 1;
				else if(winWidth>=380 && winWidth<788) column = 2;
				else if(winWidth>=788 && winWidth<1160) column = 3;
				else if(winWidth>=1160) column = 4;
				return column;
			}
		}

		function setColumnWidth(){
			var columns = getNumColumns(),
				containerWidth = jQuery("#portfolio-grid").width(),
				postWidth = containerWidth/columns;
			postWidth = Math.floor(postWidth);

			jQuery(".portfolio_item").each(function(index){
				jQuery(this).css({"width":postWidth+"px"});
			});
		}

		function arrange(){
			setColumnWidth();
			$container.isotope('reLayout');
		}

		jQuery(window).on("debouncedresize", function( event ) {
			arrange();
		});

		// Filter projects
		$('.filter a').click(function(){
			var $this = $(this).parent('li');
			// don't proceed if already active
			if ( $this.hasClass('active') ) {
				return;
			}

			var $optionSet = $this.parents('.filter');
			// change active class
			$optionSet.find('.active').removeClass('active');
			$this.addClass('active');

			var selector = $(this).attr('data-filter');
			$container.isotope({ filter: selector });

			var hiddenItems = 0,
				showenItems = 0;
			jQuery(".portfolio_item").each(function(){
				if ( jQuery(this).hasClass('portfolio_hidden') ) {
					hiddenItems++;
				};
			});

			showenItems = items_count - hiddenItems;
			if ( ($(this).attr('data-count')) > showenItems ) {
				jQuery(".pagination__posts").css({"display" : "block"});
			} else {
				jQuery(".pagination__posts").css({"display" : "none"});
			}
			return false;
		});
	});
</script>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<li class="portfolio_item  term_id_20 term_id_22  term_id_17">
		<div class="portfolio_item_holder">
		<figure class="thumbnail thumbnail__portfolio">
		<a href="<?echo $arItem["DETAIL_PICTURE"]['SRC'];?>" class="image-wrap" title="<?echo $arItem["NAME"]?>" rel="prettyPhoto">
		<img src="<?echo $arItem["PREVIEW_PICTURE"]["SRC"];?>" alt="Image Format"/>
		<span class="zoom-icon"></span> </a>
		</figure>
		 <div class="caption caption__portfolio">
		<h3><a href=""><?echo $arItem['NAME'];?></a></h3>
		</div>
		</div>
	</li>
<?endforeach;?>
</ul>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
