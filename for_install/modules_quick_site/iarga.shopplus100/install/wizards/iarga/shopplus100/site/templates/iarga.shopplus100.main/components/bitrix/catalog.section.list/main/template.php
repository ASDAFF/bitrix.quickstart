<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$maxsub = 70;?>

<div class="search">
	<form action="/catalog/search.php">
		<a href="#" class="bt_gray submit"><?=GetMessage("FIND")?></a>
		<input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>">
		<input type="hidden" name="SECTION_ID" value="0">
		<label>
			<input type="text" name="key" class="repl tooltip" data-alt="<?=GetMessage("SEARCH_EXAMPLE")?>" value="<?=GetMessage("SEARCH_EXAMPLE")?>">
		</label>
	</form>
	<div class="clr"></div>
</div><!--.search-end-->

<div class="catalog-list ">
	
	<ul class="column-wrap">
		<?
		$CURRENT_DEPTH=$arResult["SECTION"]["DEPTH_LEVEL"]+1;
		foreach($arResult["SECTIONS"] as $i=>$arSection):
			$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
			$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
			$prev = $arResult["SECTIONS"][$i-1];
			$next = $arResult["SECTIONS"][$i+1];?>
			<?if($arSection['DEPTH_LEVEL']==1):
				$sub = false;
				$pic = (!$next || $next['DEPTH_LEVEL']==1) && $arSection['PICTURE']!='';// || !$next;?>
				<?if($prev['DEPTH_LEVEL'] > $arSection['DEPTH_LEVEL']):?>
							</nav>
						</div><!--.narrow-end-->
					
				<?endif;?>
				<li class="box <?=$pic?'view-pictures':''?>">
					<div class="narrow" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
						<?if($pic):?>
							<p class="img"><a href="<?=$arSection['SECTION_PAGE_URL']?>"><img src="<?=iarga::res($arSection['PICTURE'],80,80,1)?>" alt="<?=$arSection['NAME']?>"></a></p>
						<?endif;?>
						<p class="title"><a href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$arSection['NAME']?>"><strong><?=$arSection['NAME']?></strong></a></p>
						<?if($next['DEPTH_LEVEL']==2):?>
							<nav>
						<?else:?>
									<?if(!$pic || $arSection['PICTURE']==''):?></nav><?endif;?>
								</div><!--.narrow-end-->
							
						<?endif;?>
			<?elseif($arSection['DEPTH_LEVEL']==2):
				if(!$sub){ $sub = $prev; $cnt = $maxsub+3;}
				$cnt -= strlen($arSection['NAME']);
				if($cnt > 0):?>
					<a id="<?=$this->GetEditAreaId($arSection['ID']);?>" href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a>
				<?endif;?>
				<?if($cnt<=0 && $next['DEPTH_LEVEL']==1):?>
					<a href="<?=$sub['SECTION_PAGE_URL']?>">&hellip;</a>
				<?endif;?>
			<?endif;?>					
			<!--.box-end-->
		<?endforeach?>
		<?if($arResult["SECTIONS"][sizeof($arResult["SECTIONS"])-1]['DEPTH_LEVEL']>1):?>
					</nav>
				</div><!--.narrow-end-->
			
		<?endif;?>
	</ul><!--.column-wrap-end-->
	
</div><!--.catalog-list-end-->