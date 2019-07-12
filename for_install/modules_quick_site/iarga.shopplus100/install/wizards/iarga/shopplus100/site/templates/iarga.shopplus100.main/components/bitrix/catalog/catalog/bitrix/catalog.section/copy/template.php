<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
include($_SERVER['DOCUMENT_ROOT'].$templateFolder."/filter.php");
global $templateFolder;?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>

<?=(CSite::InDir("/favorite/"))?'<div id="sortable">':''?>  
<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
	<?
	$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
	$price = iarga::getprice($arElement['ID']);
	$oldprice = $arElement['PROPERTIES']['oldprice']['VALUE'];
	$action = CIBlockElement::GetById($arElement['PROPERTIES']['action']['VALUE'])->GetNext();
	if($arElement['PREVIEW_PICTURE']=='') $arElement['PREVIEW_PICTURE'] = $arElement['DETAIL_PICTURE'];?>
	<?=(CSite::InDir("/favorite/"))?'<div class="ui-state-default">':''?>
	<div class="item<?=($arResult["NAV_STRING"]=='' && $cell>=sizeof($arResult["ITEMS"])-1)?' last':''?>" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
		<div class="forload"></div>
		<?if($arElement['PREVIEW_PICTURE']>0):?>
			<div class="img">			
				<span class="preview"><a href="<?=$arElement['DETAIL_PAGE_URL']?>"><img src="<?=iarga::res($arElement['PREVIEW_PICTURE'],172,125,1)?>" alt="<?=$arElement['NAME']?>"></a></span>
				<?if($action):?>
					<strong class="type action question"><a href="<?=$action['DETAIL_PAGE_URL']?>" title="<?=$action['NAME']?>"><?=GetMessage("STOCK")?> <img src="<?=$templateFolder?>/images/icon-question.png" alt="?" style="max-width: 18px;"></a><i></i></strong>
				<?endif;?>
			</div><!--.img-end-->
		<?endif;?>
		<div class="summary">
			<div class="description-preview">
				<h2><a href="<?=$arElement['DETAIL_PAGE_URL']?>" data-rel="<?=$arElement['ID']?>"><?=$arElement['NAME']?></a></h2>
				<?if($oldprice):?>
					<p class="price"><span class="old"><?=iarga::prep($oldprice)?><?=GetMessage("VALUTE_MEDIUM")?></span> <span class="new"><?=iarga::prep($price)?><?=GetMessage("VALUTE_MEDIUM")?></span></p>
				<?else:?>
					<p class="price"><?=iarga::prep($price)?><?=GetMessage("VALUTE_MEDIUM")?></p>
				<?endif;?>
				<p><?=$arElement['PREVIEW_TEXT']?></p>
			</div><!--.description-preview-end-->
			<a href="#" class="bt_card to_cart" data-rel="<?=$arElement['ID']?>"><?=GetMessage("TO_BASKET")?></a>
			<div class="amount-card" data-rel="<?=$arElement['ID']?>">
				<span><?=GetMessage("NUM")?>:</span>
				<input type="text" class="inp-text" value="1">
				<span class="remove-card"><a href="#"><?=GetMessage("DEL")?></a></span>
			</div><!--.amount-card-end-->
			<?if(CSite::InDir("/favorite/")):?>
				<a href="#" class="remove-favorites" data-rel="<?=$arElement['ID']?>"><?=GetMessage("DEL_FAV")?></a>
			<?else:?>
				<a href="#" class="add-favorites to_fav" data-rel="<?=$arElement['ID']?>"><?=GetMessage("TO_FAV")?></a>
			<?endif;?>
		</div><!--.summary-end-->
		<?if(CSite::InDir("/favorite/")):?>
			<div class="sortable-control">
				<a href="#" class="bg_t"><i></i></a>
				<b></b>
				<a href="#" class="bg_b"><i></i></a>
			</div><!--.sortable-control-end-->
		<?endif;?>
		<?=($cell<sizeof($arResult['ITEMS'])-1)?'<div class="clr">':''?></div>
		
	</div><!--.item-end-->
	<?=(CSite::InDir("/favorite/"))?'</div>':''?>
<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>
<?=(CSite::InDir("/favorite/"))?'</div>':''?>

<?if(sizeof($arResult["ITEMS"])<1 && !CSite::InDir("/favorite/")):?>
	<p><?=GetMessage("NOT_FOUND")?></p>
	<?if($arResult['ID'] > 0):
		if(preg_match('#\?(.*)#',$_SERVER['REQUEST_URI'],$mat)) $q = '?'.preg_replace('#SECTION_ID=([0-9]+)#','',$mat[1]);?>
		<p><?=GetMessage("ROOT_SEARCH",Array("LINK"=>$arResult['LIST_PAGE_URL'].$q))?></p>
	<?endif;?>
<?elseif(sizeof($arResult["ITEMS"])<1 && CSite::InDir("/favorite/")):?>
	<p><?=GetMessage("FAV_IS_EMPTY")?></p>
	<p><?=GetMessage("GO_TO_CATH",Array("LINK"=>'/catalog/'))?></p>
<?endif;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>