<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
	if( count($arResult['ITEMS']) == 0 )
	{
?>

	<div class="list-basket">
		<span>Ваша корзина пуста</span>
		<a class="btn" href="/catalog/women_s_clothing/">Перейти в каталог</a>
	</div>
<?
	}else{
?>
	<div class="list-basket">
		<form name="basket_form" action="/cabinet/order/make/" method="post">
			<input id="basketOrderButton2" class="btn bt3" type="submit" name="BasketOrder" value="Оформить заказ">
			<a class="btn bt2" data-toggle="modal" href="#oneClick">Купить в один клик</a>
			<a class="btn bt2" href="/cabinet/cart/">Обзор корзины</a>
		</form>
		<div id="slider5">
			<a href="#" class="buttons prev disable"><span class="icon-chevron-up"></span></a>
			<div class="viewport">
				<ul class="overview">
<?
		foreach( $arResult['ITEMS'] as $val )
		{
?>

					<li>
						<span class="generabl">
							<span class="close">&times;</span>
							<span class="depiction">
                                <a href="<?=$val['DETAIL_PAGE_URL'];?>" target="_blank">
                                	<span class="block-img-b"><img alt="<?=$val['NAME'];?>" src="<?=$val['PREVIEW_PICTURE'];?>"></span>
								</a>
								<a class="wrapped-depiction" href="<?=$val['DETAIL_PAGE_URL'];?>">
									<span class="name-mini"><?=$val['NAME'];?></span> <span><?=$val['VENDOR']?></span>
								</a>
							</span>
							<span class="price-basket">
<?
			if($val['DISCOUNT_PRICE'] > 0)
			{
?>
								<span class="actual discount"><a href="javascript:void(0);"><?=$val['PRICE_FORMATED'];?></a></span>
								<span class="old-price"><a href="javascript:void(0);"><?=$val['DISCOUNT_PRICE'];?> руб</a></span>
<?
			}else{
?>
								<span><a href="javascript:void(0);"><?=$val['PRICE_FORMATED'];?></a></span>
<?
			}
?>
							</span>
							<span class="color-basket">
								<span class="button-size-button-118button-color13-size118 button-color-button-13 color-min active-color"><span class="b-C"><span><img width="12" height="10" title="<?=$val['COLOR_NAME'];?>" alt="<?=$val['COLOR_NAME'];?>" src="<?=$val['COLOR_PIC'];?>"></span></span></span>
								<span><?=$val['SIZE'];?></span>
							</span>
						</span>
					</li>
<?
		}
?>
				</ul>
			</div>
			<a href="#" class="buttons next"><span class="icon-chevron-down"></span></a>
		</div>
	</div>
<?
	}
?>