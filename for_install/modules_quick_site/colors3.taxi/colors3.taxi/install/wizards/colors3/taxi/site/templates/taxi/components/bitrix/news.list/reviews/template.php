<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h1><?=$APPLICATION->GetTitle('title')?></h1>

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>

	<div class="review" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<p><?echo $arItem["PREVIEW_TEXT"];?></p>
		<?endif;?>

		<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
			<span class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
		<?endif?>
		<div class="clearfix review_info">
			<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
				<div class="ar name"><?echo $arItem["NAME"]?></div>
			<?endif;?>

			<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>

				<?if($arProperty["DISPLAY_VALUE"] == '1'):?>
					<div class="stars">
			            <ul>
			                <li class="star star1 active">
			                    <a></a>
			                </li>
			            </ul>
			        </div>
				<?elseif ($arProperty["DISPLAY_VALUE"] == '2') :?>
					<div class="stars">
			            <ul>
			                <li class="star star2 active">
			                    <a></a>
			                </li>
			            </ul>
			        </div>
			    <?elseif ($arProperty["DISPLAY_VALUE"] == '3') :?>
			    	<div class="stars">
			            <ul>
			                <li class="star star3 active">
			                    <a></a>
			                </li>
			            </ul>
			        </div>
			    <?elseif ($arProperty["DISPLAY_VALUE"] == '4') :?>
			    	<div class="stars">
			            <ul>
			                <li class="star star4 active">
			                    <a></a>
			                </li>
			            </ul>
			        </div>
			    <?elseif ($arProperty["DISPLAY_VALUE"] == '5') :?>
			    	<div class="stars">
			            <ul>
			                <li class="star star5 active">
			                    <a></a>
			                </li>
			            </ul>
			        </div>
			    <?endif;?>
				
			<?endforeach;?>

		</div>
	</div>
<?endforeach;?>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>