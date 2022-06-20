<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult)):?>
<td id="looked">
	<div class="right">
		<span class="heading"><?=GetMessage("HEADING")?></span>
		<ul>
			<?foreach($arResult as $arElement):?>
			<li>
				<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="pic" target="_blank">
					<img src="<?=!empty($arElement["PICTURE"]["src"]) ? $arElement["PICTURE"]["src"] : SITE_TEMPLATE_PATH."/images/empty.png"?>" title="<?=$arElement["NAME"]?>"></a>
				<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="name" target="_blank"><?=substr($arElement["NAME"], 0, 80)?></a>
				<span class="price">		      
			        <?if(empty($arElement["SKU_PRICE"])):?>
	                    <?=str_replace(GetMessage("RUB"),'<span class="rouble">P<i>-</i></span>', $arElement["PRICE_FORMATED"]);?>
						<?=!empty($arElement["OLD_PRICE"]) ? '<s>'.$arElement["OLD_PRICE"].'</s>' : ''?>
                    <?else:?>
                        <?if(!empty($arElement["SKU_SHOW_FROM"])): echo GetMessage("FROM"); endif;?><?=str_replace(GetMessage("RUB"),'<span class="rouble">Р<i>-</i></span>', $arElement["SKU_PRICE"]);?>
                    <?endif;?>
				</span>
				<a href="#" class="<?=!empty($arElement["SKU"]) ? "addSku" : "addCart"?><?if(!$arElement["ADDCART"] && !$arElement["ADDSKU"]):?> disabled<?endif;?>" data-ibl="<?=$arElement["IBLOCK_ID"]?>" data-id="<?=$arElement["PRODUCT_ID"]?>"  data-reload="Y"><?=!empty($arElement["SKU"]) ? GetMessage("ADDSKU") : GetMessage("ADDCART")?></a>
			</li>
			<?endforeach;?>
		</ul>
	</div>
</td>
<?endif;?>