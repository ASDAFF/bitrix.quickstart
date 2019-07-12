<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$tf = $templateFolder;
global $templateFolder;
global $USER;
$user = $USER->GetByID($USER->GetID())->GetNext();
foreach($_COOKIE as $i=>$val) if(preg_match("#order_#",$i)) $_COOKIE[preg_replace("#order_#","",$i)] = $val;?>
<a href="#order_now" class="bt_green checkout-button"><?=GetMessage('PROCESS_ORDER')?> <img src="<?=$templateFolder?>/images/icon-checkout.png" alt=""></a>
<div class="total-pay"><span><?=GetMessage('TOTAL_PAY')?></span> <strong></strong> <i class="sep"></i></div><!--.total-pay-end-->

<h1><?=GetMessage('BASKET')?></h1>
<input type="hidden" id="b_template" value="<?=$tf?>">
<div class="card">    
	<?foreach($arResult["ITEMS"]["AnDelCanBuy"] as $item):
		$good = CIBlockElement::GetById($item['PRODUCT_ID'])->GetNext();?>
		<div class="item">
			<?if($good['PREVIEW_PICTURE']):?>
				<div class="img">			
					<span class="preview"><a href="<?=$good['DETAIL_PAGE_URL']?>"><img src="<?=iarga::res($good['PREVIEW_PICTURE'],170,135,1)?>" alt=""></a></span>
				</div><!--.img-end-->
			<?endif;?>
			<div class="summary">
				<div class="manipulation">
					<div class="select-number">
						<a href="#" class="minus">&ndash;</a> <input type="text" class="inp-text" value="<?=$item['QUANTITY']?>" data-rel="<?=$good['ID']?>"> <a href="#" class="plus">+</a>
					</div><!--.select-number-end-->
					<a href="#" class="link-remove-card" rel="<?=$good['ID']?>"><?=GetMessage('DEL')?></a>
				</div><!--.manipulation-end-->
				<div class="description-preview">
					<h2><a href="<?=$good['DETAIL_PAGE_URL']?>"><?=$good['NAME']?></a></h2>
					<p class="price"><?=iarga::prep($item['PRICE'])?> <?=GetMessage('VALUTE_MEDIUM')?></p>	
					<p><?=$good['PREVIEW_TEXT']?></p>
				</div><!--.description-preview-end-->
			</div>			
			<div class="clr"></div>
			
		</div><!--.item-end-->
	<?endforeach;?>		


</div><!--.card-end-->

<div class="checkout">            
	<h2><?=GetMessage("PROCESS_ORDER")?></h2>
	<form action="<?=SITE_DIR?>inc/ajax/order.php" class="uniform autosave" data-rel="order">
		<dl>
			<dt><?=GetMessage("SELECT_PAYTYPE")?></dt>
			<?$types = CSalePersonType::GetList(Array("SORT"=>"ASC"),Array("ACTIVE"=>"Y","LID" => SITE_ID));?>
			<dd class="PERSON_TYPE">
				<?$i = 0;
				while($type = $types->GetNext()):
					$i++;
					$checked = ($type['ID']==$_COOKIE['PERSON_TYPE'] || ($_COOKIE['PERSON_TYPE']=='' && $i==1))?'checked':'';
					if($checked!='') $person_type = $type;?>
					<label>
						<span class="input">
							<input type="radio" name="PERSON_TYPE" class="styled" value="<?=$type['ID']?>" <?=$checked?>>
						</span>
						<span class="description">
							<strong><?=$type['NAME']?></strong>
						</span>
					</label>
					<?if($i<$types->SelectedRowsCount()):?><div class="hr"></div><?endif;?>
				<?endwhile;?>
			</dd>
		</dl>
		<div class="order_props">
			<?include($_SERVER['DOCUMENT_ROOT'].$tf.'/props.php');?>
		</div>
		
		<a href="#submit" class="bt_green confirm-button submit" name="order_now"><?=GetMessage('CONFIRM')?> <img src="<?=$templateFolder?>/images/icon-confirm.png" alt=""></a>
		<p class="error do_order"></p>
		<div class="total-pay"><span><?=GetMessage('TOTAL_PAY')?></span> <strong></strong></div><!--.total-pay-end-->

	</form>
</div><!--.checkout-end-->
<div class="nocard">
	<?=GetMessage("EMPTY_BASKET",Array("LINK"=>SITE_DIR."catalog/"))?>	
</div>

