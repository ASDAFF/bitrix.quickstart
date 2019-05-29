<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//IncludeModuleLangFile("/bitrix/modules/iarga.cleverbasket/template.php");?>
<script src="<?=$templateFolder?>/js/jquery-1.8.2.min.js"></script>
<script src="<?=$templateFolder?>/js/card.js"></script>
<script src="<?=$templateFolder?>/js/custom-form-elements.js"></script>

<link href="<?=$templateFolder?>/styles/card.css" rel="stylesheet" type="text/css">

<!--[if IE]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!--[if lte IE 7]>
	<link href="<?=$templateFolder?>/styles/base.ie.css" rel="stylesheet">
<![endif]-->
<!--[if lt IE 9]>
    <script src="<?=$templateFolder?>/js/respond.min.js"></script>
<![endif]-->


<?

$tf = $templateFolder;
global $USER;
$user = $USER->GetByID($USER->GetID())->GetNext();
foreach($_COOKIE as $i=>$val) if(preg_match("#order_#",$i)) $_COOKIE[preg_replace("#order_#","",$i)] = $val;?>
<div class="card-iarga">
	<a href="#order_now" class="bt_ia checkout-button_ia"><?=GetMessage('PROCESS_ORDER')?> <img src="<?=$templateFolder?>/images/icon-checkout.png" alt=""></a>
	<div class="total-pay_ia"><span><?=GetMessage('TOTAL_PAY')?></span> <strong></strong> <i class="sep_ia"></i></div><!--.total-pay-end-->

	<h1><?=GetMessage('BASKET')?></h1>

	<div class="card_ia">    
		<?foreach($arResult["ITEMS"]["AnDelCanBuy"] as $item):
			$good = GetIBlockElement($item['PRODUCT_ID']);
			if($good['PREVIEW_PICTURE']=="" && $good['DETAIL_PICTURE']!="") $good['PREVIEW_PICTURE'] = $good['DETAIL_PICTURE'];
			if($good['PREVIEW_PICTURE']=="" && $good['PROPERTIES']['photo']['VALUE'][0]!="") $good['PREVIEW_PICTURE'] = $good['PROPERTIES']['photo']['VALUE'][0];?>
			<div class="item_ia">
				<?if($good['PREVIEW_PICTURE']):?>
					<div class="img_ia">			
						<span class="preview_ia"><a href="<?=$good['DETAIL_PAGE_URL']?>"><img src="<?=iarga::res($good['PREVIEW_PICTURE'],170,135,1)?>" alt=""></a></span>
					</div><!--.img-end-->
				<?endif;?>
				<div class="summary_ia">
					<div class="manipulation_ia">
						<div class="select-number_ia">
							<a href="#" class="minus_ia">&ndash;</a> <input type="text" class="inp-text_ia in_cart_ia" value="<?=$item['QUANTITY']?>" data-rel="<?=$good['ID']?>"> <a href="#" class="plus_ia">+</a>
						</div><!--.select-number-end-->
						<a href="#" class="link-remove-card_ia" data-rel="<?=$good['ID']?>"><?=GetMessage('DEL')?></a>
					</div><!--.manipulation-end-->
					<div class="description-preview_ia">
						<h2><a href="<?=$good['DETAIL_PAGE_URL']?>"><?=$good['NAME']?></a></h2>
						<p class="price_ia"><?=iarga::prep($item['PRICE'])?> <?=GetMessage('VALUTE_MEDIUM')?></p>	
						<p><?=$good['PREVIEW_TEXT']?></p>
					</div><!--.description-preview-end-->
				</div>			
				<div class="clr_ia"></div>
				
			</div><!--.item-end-->
		<?endforeach;?>		


	</div><!--.card-end-->

	         
		
		<form action="<?=$templateFolder?>/order.php" class="uniform_ia autosave_ia" data-rel="order">
			<input type="hidden" id="b_template_ia" value="<?=$templateFolder?>">
			<input type="hidden" name="order_redirect_ia" value="<?=$arParams['PATH_TO_ORDER']?>">
			<div class="checkout_ia">   
				<h2><?=GetMessage("PROCESS_ORDER")?></h2>
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
								<span class="input_ia">
									<input type="radio" name="PERSON_TYPE" class="styled" value="<?=$type['ID']?>" <?=$checked?>>
								</span>
								<span class="description_ia">
									<strong><?=$type['NAME']?></strong>
								</span>
							</label>
							<?if($i<$types->SelectedRowsCount()):?><div class="hr_ia"></div><?endif;?>
						<?endwhile;?>
					</dd>
				</dl>
				<div class="order_props_ia">
					<?include($_SERVER['DOCUMENT_ROOT'].$tf.'/props.php')?>
				</div>
			</div><!--.checkout-end-->
			<div class="total-container_ia">
				<a href="#submit" class="bt_ia confirm-button submit_ia" name="order_now"><?=GetMessage('CONFIRM')?> <img src="<?=$templateFolder?>/images/icon-confirm.png" alt=""></a>
				<p class="error do_order_ia"></p>
				<div class="total-pay_ia"><span><?=GetMessage('TOTAL_PAY')?></span> <strong></strong></div><!--.total-pay-end-->
			</div>
		</form>
	
	<div class="nocard_ia">
		<?=($_SERVER['SERVER_NAME']=="www.nachisto.ru")?GetMessage("EMPTY_BASKET_NACH"):GetMessage("EMPTY_BASKET")?>	
	</div>
	<div style="display:none" class="info-amount_ia"></div>
</div>
