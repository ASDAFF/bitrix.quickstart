<div id="post-7" class="post-7 page type-page status-publish hentry page">
<div class="row">
	<div class="span12"><div class="sm_hr"></div>
		<h2><?=GetMessage('ACCESS_SERVICES')?></h2>
		<ul class="posts-grid row-fluid unstyled block-output">
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?>

				<li class="span4">
					<figure class="featured-thumbnail thumbnail">
					<a href="<?=$arItem['DETAIL_PICTURE']["SRC"]?>" title="<?=$arItem['NAME']?>" rel="prettyPhoto-735130142">
							<img src="<?=$arItem['PREVIEW_PICTURE']["SRC"]?>" alt="<?=$arItem['NAME']?>">
							<span class="zoom-icon"></span>
						</a>
					</figure>
					<div class="clear"></div>
					<h5><a href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a></h5>
					<p class="excerpt"><?=$arItem['PREVIEW_TEXT']?>...</p></li>

				<?endforeach;?>

		</ul>
		<div class="sm_hr"></div>
	</div>
	<div class="clear"></div>
</div>