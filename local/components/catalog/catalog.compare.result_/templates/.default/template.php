<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<script src="/js/libs/tinyscrollbar/jquery.tinyscrollbar.js"></script>
<link rel="stylesheet" href="/js/libs/tinyscrollbar/tinyscrollbar.css">
<div class="b-compare-header__wrapper">
	<div class="b-compare-header clearfix">
		<label class="b-checkbox" id="m-changes__show"><input type="checkbox" name="checkbox_gp_1" checked />Скрыть одинаковые<br />характеристики</label>
		<div class="b-compare-header__product">
			<div id="scrollbarX">
				<div class="viewport">
	   <div class="overview b-compare__product b-clearfix"> 
					  <?foreach($arResult['ITEMS'] as $item){?> 
						<div class="b-compare__item">
							<button class="b-compare__delete" data-id="<?=$item['ID'];?>"></button>
							<div class="b-compare__image"><img height="<?=$item['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$item["NAME"];?>" src="<?=$item['PREVIEW_PICTURE']['SRC']?>"></div>
							<div class="b-compare__link"><a href="<?=$item["DETAIL_PAGE_URL"];?>"><?=$item["NAME"];?></a></div>
							<div class="b-compare__price"><span class="b-price"><?=$item['PRICE']["PRICE"];?></span></div>
							<div class="b-compare__buy"><button class="b-button buy_" data-id="<?=$item['ID'];?>"><span class="b-catalog-list_item__cart">Купить</span></button></div>
						</div> 
					  <? } ?>  
					</div>
				</div>
				<div class="scrollbar"><div class="track"><div class="thumb"></div></div></div>
			</div>
		</div>
	</div>
</div>
<div class="b-compare-body m-changes__show clearfix">
    <div class="b-compare-name"> 
 <? foreach($arResult['ITEMS'][0]['PROPERTIES'] as $prop){ ?>
   <div class="b-compare-name__title<?if($prop['CHANGES']){ ?>  m-compare__changes<?}?>"><?=$prop['NAME'];?><i class="b-sidebar-menu__line"></i></div>
<? } ?> 
    </div>
    <div class="b-compare-value">
        <div class="viewport" id="b-compare__table">
            <div class="overview b-compare__product b-clearfix">
          <?foreach($arResult['ITEMS'] as $item){?>  
                <div class="b-compare__item">
 <?foreach($item['PROPERTIES'] as $prop){?>
 <div class="b-compare-name__title<?if($prop['CHANGES']){?>  m-compare__changes<?}?>"><?if($prop['VALUE']){?><?=$prop['VALUE'];?><?} else {?> • <?}?><i class="b-sidebar-menu__line"></i></div>
 <?}?>            
              </div>
            <?}?> 
            </div>
        </div>
    </div>
</div> 
 