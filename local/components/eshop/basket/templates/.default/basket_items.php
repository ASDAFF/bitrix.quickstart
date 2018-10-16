<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?=ShowError($arResult["ERROR_MESSAGE"]);?>
<form method="post" action="<?=$APPLICATION->GetCurPageParam();?>" name="basket">
<?=bitrix_sessid_post();?>
<table class="beono-basket">
<thead>
	<tr>
		<?if ($arParams["AJAX_ACTIONS"]!="Y"):?>
		<th>
			<input title="<?=GetMessage('BEONO_BASKET_SELECT_ALL')?>" type="checkbox" class="beono-basket-action_all" name="action_allitems" value="true"/>
		</th>
		<?endif;?>
		<?if (in_array("IMAGE", $arParams["COLUMNS_LIST"])):?>
			<th></th>
		<?endif;?>
		<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
			<th class="beono-basket-item-name"><?=GetMessage("BEONO_BASKET_NAME")?></th>
		<?endif;?>
		<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
			<th class="beono-basket-item-props"><?=GetMessage("BEONO_BASKET_PROPS")?></th>
		<?endif;?>
		<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
			<th class="beono-basket-item-weight"><?=GetMessage("BEONO_BASKET_WEIGHT")?></th>
		<?endif;?>
		<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
			<th class="beono-basket-item-quantity"><?=GetMessage("BEONO_BASKET_QUANTITY")?></th>
		<?endif;?>
		<?if ($arParams["AJAX_ACTIONS"]=="Y"):?>
		<th></th>
		<?endif;?>
		<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
			<th class="beono-basket-item-price"><?=GetMessage("BEONO_BASKET_PRICE")?></th>
		<?endif;?>
	</tr>
</thead>	
<tbody>
	<?
	foreach($arResult["ITEMS"]['CURRENT'] as $arBasketItems):?>
		<tr>
			<?if ($arParams["AJAX_ACTIONS"]!="Y"):?>
			<td class="beono-basket-action">				
				<input type="checkbox" class="beono-basket-action" name="action_items[]" value="<?=$arBasketItems['ID']?>" />							
			</td>
			<?endif;?>
			<?if (in_array("IMAGE", $arParams["COLUMNS_LIST"])):?>
			<td class="beono-basket-item-image">
				<?
				if($arBasketItems["IMAGE"]):
				$image=CFile::ResizeImageGet($arBasketItems["IMAGE"], array('width'=> $arParams['IMAGE_WIDTH'], 'height'=> $arParams['IMAGE_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				?>
				<a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>">
					<img class="beono-basket-item-image" src="<?=$image['src']?>" width="<?=$image['width']?>" height="<?=$image['height']?>" alt="<?=$arBasketItems["NAME"];?>" />
				</a>
				<?endif;?>
			</td>
			<?endif;?>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td class="beono-basket-item-name">
				<?
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
					?><a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?
				endif;
				?><?=$arBasketItems["NAME"]?><?
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
					?></a><?
				endif;
				?>
				 <?if(in_array("DATE", $arParams["COLUMNS_LIST"])):?><br/><small><?=GetMessage("BEONO_BASKET_DATE_INSERT")?> <?=FormatDate("x", MakeTimeStamp($arBasketItems["DATE_INSERT"], 'YYYY-MM-DD HH:MI:SS'));?></small><?endif;?>
				</td>
			<?endif;?>
			<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
				<td class="beono-basket-item-pops">
				<?
				foreach($arBasketItems["PROPS"] as $val)
				{
					echo $val["NAME"].": ".$val["VALUE"]."<br />";
				}
				?>
				</td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td class="beono-basket-item-weight"><?=$arBasketItems["WEIGHT_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td class="beono-basket-item-quantity">
				<a href="">&minus;</a>
				<input autocomplete="off" min="1" max="9999999" maxlength="7" type="text" name="quantity[<?=$arBasketItems["ID"]?>]" value="<?=$arBasketItems["QUANTITY"]?>"/>
				<a href="">+</a>
				</td>
			<?endif;?>
			<?if ($arParams["AJAX_ACTIONS"]=="Y"):?>
			<td class="beono-basket-action">				
				<?if(in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
					<?if($_GET['tab']==''):?>
						<a href="<?=$APPLICATION->GetCurPageParam(bitrix_sessid_get().'&basketrefresh=true&action=delay&action_items[]='.$arBasketItems['ID'].'&tab='.$arResult['TAB'], array('tab', 'action', 'action_items'));?>"><?=ToLower(GetMessage("BEONO_BASKET_DELAY"))?></a>
					<?elseif ($_GET['tab']=='delayed'):?>
						<a href="<?=$APPLICATION->GetCurPageParam(bitrix_sessid_get().'&basketrefresh=true&action=recover&action_items[]='.$arBasketItems['ID'].'&tab='.$arResult['TAB'], array('tab', 'action', 'action_items'));?>"><?=ToLower(GetMessage("BEONO_BASKET_RECOVER"))?></a>
					<?endif;?>
				<?endif;?>							
				<a title="<?=GetMessage("BEONO_BASKET_DELETE")?>" href="<?=$APPLICATION->GetCurPageParam(bitrix_sessid_get().'&basketrefresh=true&action=delete&action_items[]='.$arBasketItems['ID'].'&tab='.$arResult['TAB'], array('tab', 'action', 'action_items'));?>">&times;</a>
			</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td class="beono-basket-item-price">				
					<span title="<?=$arBasketItems["NOTES"]?>"><?=$arBasketItems["TOTAL_PRICE_FORMATED"]?></span>
					<?if($arBasketItems["QUANTITY"] > 1):?>
					 <br/><small><?=$arBasketItems["PRICE_FORMATED"]?> &times; <?=$arBasketItems["QUANTITY"]?></small>
					<?endif;?>
					<?if($arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]):?>
					 <br/><small><?=GetMessage("BEONO_BASKET_DISCOUNT")?>: <?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></small>
					<?endif;?>			
				</td>
			<?endif;?>
		</tr>
	<?endforeach;?>
</tbody>
<tfoot>
	<tr>
		<?if (in_array("IMAGE", $arParams["COLUMNS_LIST"]) && $arParams["AJAX_ACTIONS"]!="Y"):?>
		<td colspan="3">
		<?else:?>
		<td colspan="2">
		<?endif;?>
		<?if ($arParams["AJAX_ACTIONS"]!="Y"):?>
			<div id="beono_basket_action">
			<?if($_GET['tab']=='' && in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<input name="action" type="radio" value="delay" id="beono_basket_delay"/><label for="beono_basket_delay"><?=GetMessage("BEONO_BASKET_DELAY")?></label>
			<?elseif($_GET['tab']=='delayed'):?>
				<input name="action" type="radio" value="recover" id="beono_basket_recover"/><label for="beono_basket_recover"><?=GetMessage("BEONO_BASKET_RECOVER")?></label>
			<?endif;?>
				<input name="action" type="radio" value="delete" id="beono_basket_delete"/><label for="beono_basket_delete"><?=GetMessage("BEONO_BASKET_DELETE")?></label>
			</div>
		<?endif;?>
		</td>
		<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
			<td>&nbsp;</td>
		<?endif;?>
		<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
			<td class="beono-basket-item-weight"><?if($_GET['tab']==''):?><?=$arResult["allWeight_FORMATED"]?><?endif;?></td>
		<?endif;?>
		<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
			<td>&nbsp;</td>
		<?endif;?>		
		<?if ($arParams["AJAX_ACTIONS"]=="Y"):?>
			<td>&nbsp;</td>
		<?endif;?>
		<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
			<td class="beono-basket-item-price">
			<?if($_GET['tab']==''):?>
				<b title="<?=GetMEssage('BEONO_BASKET_ITOGO');?>"><?=$arResult["allSum_FORMATED"]?></b>			
				<?if($arResult['DISCOUNT_VALUE_FORMATED']):?>
					<br/><small><?=GetMessage('BEONO_BASKET_DISCOUNT')?>: <?=$arResult['DISCOUNT_VALUE_FORMATED'];?></small>
				<?endif;?>								
				<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
					<br/><small><?=GetMessage('BEONO_BASKET_VAT_INCLUDED')?>: <?=$arResult["allVATSum_FORMATED"]?></small>
				<?endif;?>
				<?if($arParams['DELIVERY_PRICE']):?>
					<br/><small>(<?=GetMessage('BEONO_BASKET_DELIVERY_PRICE')?>: <?=$arParams['DELIVERY_PRICE'];?>)</small>
				<?endif;?>
			<?endif;?>
			</td>
		<?endif;?>
	</tr>
</tfoot>
</table>
<?if($arParams["AJAX_ACTIONS"]!="Y" || ($arParams["AJAX_ACTIONS"]=="Y" && $_GET['tab']=='')):?>
<table>
	<tr>	
		<td class="beono-basket-coupon">
			<?if ($arParams["HIDE_COUPON"] != "Y" && $_GET['tab']==''):?>
			<label for="beono_basket_coupon"><?=GetMessage("BEONO_BASKET_COUPON_PROMT")?> <input autocomplete="off" type="text" id="beono_basket_coupon" name="coupon" value="<?=$arResult["COUPON"]?>" size="20">
			<?endif;?>
		</td>	
		<td>
			<input class="beono-basket-button" type="submit" value="<?echo GetMessage("BEONO_BASKET_REFRESH")?>" name="basketrefresh"/>
		</td>
		<?if($_GET['tab']==''):?>
		<td>
			<input class="beono-basket-button beono-basket-button_order" type="submit" value="<?echo GetMessage("BEONO_BASKET_ORDER")?>" name="basketorder"/>
		</td>
		<?endif;?>
	</tr>
</table>
<?endif;?>
</form>