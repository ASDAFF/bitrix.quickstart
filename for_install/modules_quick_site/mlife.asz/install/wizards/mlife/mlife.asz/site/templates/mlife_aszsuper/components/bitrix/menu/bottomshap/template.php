<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);

if (empty($arResult["ALL_ITEMS"]))
	return;
//echo'<pre>';print_r($arResult);echo'</pre>';
?>

<div id="bottomShapMenu">
	<ul class="level1">
		<?foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns):?>     <!-- first level-->
		<li class="mlf_levelmenu_1<?if($arResult["ALL_ITEMS"][$itemID]["IS_PARENT"]==1){?> parent<?}?>" id="item<?=$itemID?>">
			<a href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>">
				<?=$arResult["ALL_ITEMS"][$itemID]["TEXT"]?>
			</a>
		</li>
		<?endforeach;?>
	</ul>
	<?foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns):?>
	<?if($arResult["ALL_ITEMS"][$itemID]["IS_PARENT"]==1){?>
	<div class="level2" id="subd_item<?=$itemID?>">
		<?if (is_array($arColumns) && count($arColumns) > 0):?>
			<div class="mlf_children_block column<?=count($arColumns)?>">
				<?foreach($arColumns as $key=>$arRow):?>
				<ul class="childrenBlocker">
					<?foreach($arRow as $itemIdLevel_2=>$arLevel_3):?>  <!-- second level-->
						<li class="parent">
							<a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>">
								<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"]?>
							</a>
						<?if (is_array($arLevel_3) && count($arLevel_3) > 0):?>
							<ul>
							<?foreach($arLevel_3 as $itemIdLevel_3):?>	<!-- third level-->
								<li>
									<a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["LINK"]?>"><?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["TEXT"]?></a>
								</li>
							<?endforeach;?>
							</ul>
						<?endif?>
						</li>
					<?endforeach;?>
				</ul>
				<?endforeach;?>
			</div>
		<?endif?>
	</div>
	<?}?>
	<?endforeach;?>
</div>

<script>
	$(document).on('mouseenter','#bottomShapMenu ul.level1 > li > a',function(event) {
		$("#bottomShapMenu .level2").hide();
		$("#bottomShapMenu ul.level1 .parent").removeClass('active');
		if ($(event.target).closest("#bottomShapMenu ul.level1").length){
			var link = $(event.target).closest("#bottomShapMenu ul.level1 .parent");
			var link2 = $(event.target).closest("#bottomShapMenu .level2");
			if(link.length){
				if(link.hasClass('active')){
					$("#bottomShapMenu .level2").hide();
					$("#bottomShapMenu ul.level1 .parent").removeClass('active');
				}else{
					$("#bottomShapMenu ul.level1 .parent").removeClass('active');
					link.addClass('active');
					$("#bottomShapMenu .level2").hide();
					$("#subd_"+link.attr('id')).css({'display':'block'});
				}
				event.preventDefault();
			}
		}else{
			$("#bottomShapMenu .level2").hide();
			$("#bottomShapMenu ul.level1 .parent").removeClass('active');
		}
		event.stopPropagation();
	});
	$(document).on('click',function(event) {
		if ($(event.target).closest("#bottomShapMenu ul.level1").length){
		
		}else{
			$("#bottomShapMenu .level2").hide();
			$("#bottomShapMenu ul.level1 .parent").removeClass('active');
		}
		event.stopPropagation();
	});
	$(document).on('mouseenter', ':not(#bottomShapMenu *)', function(event) {
		$("#bottomShapMenu .level2").hide();
		$("#bottomShapMenu ul.level1 .parent").removeClass('active');
		
		event.stopPropagation();
	});
	
	$("#bottomShapMenu ul.level1").flexMenu({
		'linkText' : 'еще...',
		'linkTitle' : 'еще...',
	});
	
</script>