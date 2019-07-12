<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $listTitle;?>
<?if(sizeof($arResult["ITEMS"]) > 0):?>
	<div class="hot-deals">
		<div class="bg">
			<div class="wrapper">

				<h3><?=($listTitle!="")?$listTitle:GetMessage("HOTPOINTS")?></h3>
				
				<ul class="column-wrap">
					<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
					<?
					$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
					$price = iarga::getprice($arElement['ID']);
					if(!$arElement['PREVIEW_PICTURE']> 0) $arElement['PREVIEW_PICTURE'] = $arElement['DETAIL_PICTURE'];?>			
						<li class="box" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
							<div class="narrow">
								<div class="wrap">
									<div class="title">
										<span class="price"><?=iarga::prep($price)?><?=GetMessage("VALUTE_SMALL")?></span>
										<a href="<?=$arElement['DETAIL_PAGE_URL']?>"><?=$arElement['NAME']?><i></i></a>
									</div><!--.title-end-->
									<?if($arElement['PREVIEW_PICTURE']>0):?>
										<div class="img">
											<span><a href="<?=$arElement['DETAIL_PAGE_URL']?>"><b><img src="<?=iarga::res($arElement['PREVIEW_PICTURE'],171,154,1)?>" alt=""></b></a></span>
										</div><!--.img-end-->
									<?endif;?>
									<?if($arElemet['PROPERTIES']['discount']['VALUE'] > 0):?>
										<strong class="type discount"><?=GetMessage("DISCOUNT")?><i></i></strong>
									<?endif;?>
								</div><!--.wrap-end-->
								<ul>
									<?foreach($arElement['PROPERTIES']['vars']['VALUE'] as $var):?>
										<li><?=$var?></li>
									<?endforeach;?>
								</ul>
							</div><!--.narrow-end-->
						<!--.box-end-->
					<?endforeach;?>

				</ul><!--.column-wrap-end-->
				
			</div><!--.wrapper-end-->
		</div><!--.bg-end-->
	</div><!--.hot-deals-end-->
<?endif;?>