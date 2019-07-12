<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
##############################################################
// zamenyaem ssylki v opisanii na softeffect.ru dlya DEMO dannyh
// udalit' esli DEMO dannye bolee ne ispol'zuyutsya
preg_match_all('|src=\"([^\"]*)\"|', $arResult['TEXT'], $matches);
foreach ($matches[1] as $value) {
	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$value) && strpos($value, 'http://')===FALSE) {
		$arResult['TEXT'] = str_replace($value, "http://softeffect.ru".$value, $arResult['TEXT']);
	}
}

preg_match_all('|src=\"([^\"]*)\"|', $arResult['MORE']['TEXT'], $matches);
foreach ($matches[1] as $value) {
	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$value) && strpos($value, 'http://')===FALSE) {
		$arResult['MORE']['TEXT'] = str_replace($value, "http://softeffect.ru".$value, $arResult['MORE']['TEXT']);
	}
}

preg_match_all('|src=\"([^\"]*)\"|', $arResult['MORE']['TEXT_MINI'], $matches);
foreach ($matches[1] as $value) {
	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$value) && strpos($value, 'http://')===FALSE) {
		$arResult['MORE']['TEXT_MINI'] = str_replace($value, "http://softeffect.ru".$value, $arResult['MORE']['TEXT_MINI']);
	}
}

preg_match_all('|src=\"([^\"]*)\"|', $arResult['MANUF']['TEXT'], $matches);
foreach ($matches[1] as $value) {
	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$value) && strpos($value, 'http://')===FALSE) {
		$arResult['MANUF']['TEXT'] = str_replace($value, "http://softeffect.ru".$value, $arResult['MANUF']['TEXT']);
	}
}

preg_match_all('|src=\"([^\"]*)\"|', $arResult['PREVIEW_TEXT'], $matches);
foreach ($matches[1] as $value) {
	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$value) && strpos($value, 'http://')===FALSE) {
		$arResult['PREVIEW_TEXT'] = str_replace($value, "http://softeffect.ru".$value, $arResult['PREVIEW_TEXT']);
	}
}
##############################################################
?>
<script type="text/javascript">
	jsAjaxUtil.InsertDataToNode('<?=SITE_DIR?>include/1click-order-sbox/1click-order-sbox.php?TEXT_POLE=<?=urlencode($arResult['NAME'])?>&ELEMENT=<?=$arResult['ID']?>', 'oneclick', false);
	jsAjaxUtil.InsertDataToNode('<?=SITE_DIR?>include/rating/rating-sbox.php?NAME=<?=$arResult['NAME']?>&ELEMENT=<?=$arResult['ID']?>', 'rating', false);
</script>

<h1><?=$arResult['H1']?></h1>
<div class="content clearfix model" id="productoverview">
	<?
	$secid=intval($arSec['ID']);
	
	if ($arProp['MORE_PHOTO']["VALUE"][0]!="") {
		$lenght=count($arProp['MORE_PHOTO']["VALUE"]);
		
		for ($i=0; $i<count($arProp['MORE_PHOTO']["VALUE"]); $i++) {
			$arResult['PICTURE'][$i+1]["FULL"]=CFile::GetPath($arProp['MORE_PHOTO']['VALUE'][$i]);
			$arResult['PICTURE'][$i+1]["MINI"]=CFile::GetPath($arProp['MORE_PHOTO']['VALUE'][$i]);
		}
	};
	if ($arResult['PICTURE'][0]["FULL"]=='') {
		$bdlel=CIBlockSection::GetNavChain($IB_CATALOG, $secid);
		$lel=$bdlel->GetNext();
		
		$arResult['PICTURE'][0]["FULL"]=CFile::GetPath($lel["PICTURE"]);
		$arResult['PICTURE'][0]["MINI"]=CFile::GetPath($lel["PICTURE"]);
	}
	?>
	
	<script type="text/javascript">
	   	$(document).ready(function(){
	   		$('.news-detail').removeClass('news-detail')
	   	});	
	</script>
	<div class="images col image_gallery">
		<div class="view p" id="photo_0">
			<a rel="shadowbox[portfolio]" class="biglink highslide " href="<?=$arResult['PICTURE'][0]['FULL']?>" style="">
				<img border="0" class="full-image" width="<?=$arResult['widthP']?>" alt="<?=$SEO_MES.$arResult['H1']?>" title="<?=$SEO_MES.$arResult['H1']?>" src="<?=$arResult['PICTURE'][0]['FULL']?>">
			</a>
		</div>
		<? foreach ($arResult['PICTURE'] as $key=>$photo) {
			if ($key!=0) { ?>
				<div style="display:none" class="view p" id="photo_<?=$key?>">
					<a href="<?=$photo['FULL']?>" rel="shadowbox[portfolio]" class="biglink highslide ">
						<img border="0" class="full-image" width="<?=$arResult['widthP']?>" alt="<?=$SEO_MES.$arResult['H1']?>" title="<?=$SEO_MES.$arResult['H1']?>" src="<?=$photo['MINI']?>" alt="" />
					</a>
				</div>
			<? }
		} ?>
		<div class="navigation">
			<div class="back_step" tooltip="Preview picture"></div>
			<ul class="previews">
				<? foreach ($arResult['PICTURE'] as $key=>$photo) { ?>
					<li<? if ($key==0) {?> class="active"<? } ?> id="Nav_<?=$key?>">
						<a onclick="createSimpleImageListerSmall(<?=$key?>); return false;" href="<?=$photo['FULL']?>" title="<?=$SEO_MES.$arResult['H1']?>">
							<img border="0" alt="<?=$SEO_MES.$arResult['H1']?>" src="<?=$photo['MINI']?>" title="<?=$SEO_MES.$arResult['H1']?>"> 
						</a>
					</li>
				<? } ?>
			</ul>
			<div class="fwd_step" tooltip="Next picture"></div>
		</div>
		<!--Акция-->
		<br/>
	</div>
	<script type="text/javascript">
		function createSimpleImageListerSmall(id){
			$('.images.col.image_gallery .view.p').css('display','none');
			$('.images.col.image_gallery #photo_'+id).css('display','block');
			$('.images.col.image_gallery .navigation li').removeClass('active');
			$('.images.col.image_gallery #Nav_'+id).addClass('active');
		}
		
		$('.fwd_step').click(function(){
			var active = $('.navigation').find(".active");
			var li;
			var idx = (li = $('.navigation').find("li")).index(active);
			if (idx < li.length - 1 && idx >= 0) {
				idx = idx + 1;
				$('.images.col.image_gallery .navigation li').removeClass('active');
				$(li[idx]).addClass('active');
				$('.images.col.image_gallery .view.p').css('display','none');
				$('.images.col.image_gallery #photo_'+idx).css('display','block');
			}
		});
		
		
		$('.back_step').click(function(){
			var active = $('.navigation').find(".active");
			var li;
			var idx = (li = $('.navigation').find("li")).index(active);
			if (idx >= 1 ) {
				idx = idx - 1;
				$('.images.col.image_gallery .navigation li').removeClass('active');
				$(li[idx]).addClass('active');
				$('.images.col.image_gallery .view.p').css('display','none');
				$('.images.col.image_gallery #photo_'+idx).css('display','block');
			}
		});
	</script>
	<div class="right-float-good">
		<? if (strlen($arResult['MORE']['TEXT_MINI'])>0) { ?><div class="text-mini"><?=$arResult['MORE']['TEXT_MINI']?></div><? } ?>
		<ul class="listunln clearfix" id="quickspecs">
			<? if ($arResult['PREVIEW_TEXT']) {?><li><?=$arResult['PREVIEW_TEXT']?><br/><br/></li><? } ?>
			<? if (strlen($arResult['MORE']['TEXT_MINI'])>0) { ?><li><span class="lhdr"> </span><br class="clearfloat"></li><? } ?>
			<? if ($arResult['PLATFORM_NAME']!='') {?><li style="padding-top:0px;"><span class="lhdr imgalfix"><?=GetMessage('SE_CATALOGELEMENT_PLATFORM')?>: </span><img width="16" height="15" title="<?='купить '.$arResult['H1']?>" alt="<?=$SEO_MES.$arResult['H1']?>" src="<?=SITE_DIR?>images/logo_os_<?=$arResult['PLATFORM']?>.gif"> <?=$arResult['PLATFORM_NAME']?> <br class="clearfloat"></li><? } ?>
			<li><span class="lhdr"><?=GetMessage('SE_CATALOGELEMENT_ALLPROD');?>: </span><a title="<?=GetMessage('SE_CATALOGELEMENT_VIEWGOODS');?> <?=$arResult['MANUF']['NAME']?>" href="<?=$arResult['MANUF']['URL']?>"><?=$arResult['MANUF']['NAME']?></a><br class="clearfloat"></li>
			<li><span class="lhdr"><?=GetMessage('SE_CATALOGELEMENT_GARANTIYA')?>: </span><?=GetMessage('SE_CATALOGELEMENT_FROMMANUF')?><br class="clearfloat"></li>
			<? if ($arResult['ARTICLE']!='-') {?><li><span class="lhdr"><?=GetMessage('SE_CATALOGELEMENT_ARTICUL')?>: </span><?=$arResult['ARTICLE']?></li><? } ?>
			<li><span class="lhdr"><?=GetMessage('SE_CATALOGELEMENT_ALLPRICE')?>:  </span><a href="<?=$arResult['MORE']['URL']?>" alt=" <?=$SEO_MES.$arResult['MORE']['NAME']?>" title="<?=$SEO_MES.$arResult['MORE']['NAME']?>"><?=$arResult['MORE']['NAME']?></a></li>
			<? if ($arResult['USERS_QUANTITY']) {?><li><span class="lhdr"><?=GetMessage('SE_CATALOGELEMENT_USERS')?>: </span><?=$arResult['USERS_QUANTITY']?></li><? } ?>
			
			<? if ($arResult['PERIOD']) {?><li><span class="lhdr"><?=GetMessage('SE_CATALOGELEMENT_UPDATES')?>: </span><?=$arResult['PERIOD']?></li><? } ?>
			<? if ($arResult['FORMAT']) {?><li><span class="lhdr"><?=GetMessage('SE_CATALOGELEMENT_DELIVERY')?>: </span><?=$arResult['FORMAT'];?></li><? } ?>
			<? if ($arResult['DELIVERY_TIME']) {?><li><span class="lhdr"><?=GetMessage('SE_CATALOGELEMENT_DELIVERY_DATE')?>: </span><?=$arResult['DELIVERY_TIME']?></li><? } ?>
		</ul>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", ".default", array(
		"ELEMENT_ID" => $arResult['ID'],
		"STORE_PATH"  =>  "",
		"MAIN_TITLE"  =>  GetMessage('SE_CATALOGELEMENT_STOREAMOUNT').":",
	),
	$component
);?>
		<div align="center" class="pricebox clearfix">
			<form enctype="multipart/form-data" method="post" action="<?=SITE_DIR?>basket/" name="AddToBasket">
				<table cellspacing="0" cellpadding="0" width="100%">
					<tbody>
						<tr>
							<td class="nomr"><?=GetMessage('SE_CATALOGELEMENT_PRICE')?>:</td>
							<td>			
								<?if ($arResult['OLD_PRICE']!=$arResult['PRICE']) { ?>
									<del><?=SaleFormatCurrency($arResult['OLD_PRICE'], "RUB");?></del>
								<? }
								if ($arResult['DISCOUNT']>0) { ?>
									<b> - <?=$arResult['DISCOUNT']?> %<b>
								<? } ?>
								<div class="pricenovat_detail"><?=SaleFormatCurrency($arResult['PRICE'], "RUB");?></div>
							</td>
							<td class="noprint">
								<input type="hidden" value="<?=$arResult['ID']?>" name="ID" />
								<input type="hidden" value="add" name="action" />
								<label for="qty"><?=GetMessage('SE_CATALOGELEMENT_COUNT')?>:</label>
								<input type="text" maxlength="2" value="1" name="QTY" id="qty" />
							</td>
							<td class="noprint">
								<input type="submit" name="add" class="btn do pt-1 pb-1 pl-10 pr-10 wd-92" alt="<?=htmlspecialcharsback($arResult['NAME'])?>" title="<?=htmlspecialcharsback($arResult['NAME'])?>" value="<?=GetMessage('SE_CATALOGELEMENT_BUY')?>"<? if ($arResult['PRICE']<=0) { ?> disabled="true"<? } ?> />
							<br/>
							<div class="iconbox order">
								<a class="modalInput" href="#" rel="#oneclick" name="<?=$arResault["NAME"]?>"><?=GetMessage('SE_CATALOGELEMENT_ONELICK')?></a>
							</div>				
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
		<div style="margin-left: 24px; float: left;width: 400px;">
			<?=GetMessage('SE_CATALOGELEMENT_BUY_NOTICE')?>
		</div>
		<div style="color: #787878; margin-left: 24px; float: left; margin-top: 15px; width: 425px;">
			<?=GetMessage('SE_CATALOGELEMENT_SHARE')?>:<br />			
			<?$APPLICATION->IncludeComponent("bitrix:main.share", ".default", array(
	"HIDE" => "N",
	"HANDLERS" => array(
		0 => "lj",
		1 => "delicious",
		2 => "twitter",
		3 => "facebook",
		4 => "mailru",
		5 => "vk",
	),
	"PAGE_URL" => $APPLICATION->GetCurDir(),
	"PAGE_TITLE" => $APPLICATION->GetTitle()
	),
	false
);?>
		</div>
	</div>
	<div class="clearfloat"></div>
	<br />
</div>
<div class="clearfloat"></div>
<ul class="tabs">
	<? if (strlen($arResult['TEXT'])>0 || strlen($arResult['MORE']['TEXT'])>0) { ?><li><a href="#descr"><?=GetMessage('SE_CATALOGELEMENT_DESCR')?></a></li><? } ?>
	<? if (strlen($resCompare['DETAIL_TEXT']))  { ?><li><a href="#can" class="double-string"><?=GetMessage('SE_CATALOGELEMENT_COMPARE_RED')?></a></li><? } ?>
	<? if ($arResult['arResultType']) { ?><li><a href="#var" class="double-string"><?=GetMessage('SE_CATALOGELEMENT_BUY_VARIANTS')?></a></li><? } ?>
	<? if ($arResult['RATING_OBJ'])  { ?><li id="reviews"><a href="#reviews" ><?=GetMessage('SE_CATALOGELEMENT_REVIEWS')?></a></li><? } ?>
	<? if (strlen($arResult['CAN'])>0) { ?><li><a href="#can"><?=GetMessage('SE_CATALOGELEMENT_VOZM')?></a></li><? } ?>
	<? if ($arResult['UF_BOOKS']) { ?><li><a href="#teach"><?=GetMessage('SE_CATALOGELEMENT_BOOKS')?></a></li><? } ?>
</ul>
<div class="panes">
	<? if (strlen($arResult['TEXT'])>0) { ?>
		<div>
			<?=$arResult['TEXT']?>
			<div class="iconbox-down">
				<a class="modalInput" href="#" rel="#oneclick" name="<?=$arResault["NAME"]?>"><img src="<?=SITE_DIR?>images/oneClick.png" alt="" /></a>
			</div>	
		</div>
	<? } elseif (strlen($arResult['MORE']['TEXT'])>0) { ?>
		<div>
			<?=$arResult['MORE']['TEXT']?>
			<div class="iconbox-down">
				<a class="modalInput" href="#" rel="#oneclick" name="<?=$arResault["NAME"]?>"><img src="<?=SITE_DIR?>images/oneClick.png" alt="" /></a>
			</div>		
		</div>
	<? } ?>
	
	<? if (strlen($resCompare['DETAIL_TEXT'])) { ?><div><?=$resCompare['DETAIL_TEXT']?></div><? } ?>
	
	<? if ($arResult['arResultType']) { ?>
		<div>
			<? foreach ($arResult['arResultType'] as $key=>$value ) { ?>
				<h2><?=$key?></h2><br/>
				<? foreach ($value as $key2=>$arElement ) {?>
					<div>
						<div class="searchprd clearfix">
							<div class="var">
								<a href="<?=$arElement['URL']?>" title="<?=$SEO_MES.$arElement["NAME"];?>"><?=$arElement["NAME"];?></a><br/><br/>
								<?=GetMessage('SE_CATALOGELEMENT_PRICE')?>: <span class="pricenovat"><?=$arElement['PRICE'];?> </span> 
							</div>  
							<div class="controls-var noprint">
								<form enctype="multipart/form-data" method="post" action="<?=SITE_DIR?>basket/" name="AddToBasket">
									<table cellspacing="0" cellpadding="0">
										<tbody>
											<tr></tr>
											<tr>
												<td>
													<input type="hidden" value="<?=$arElement['ID']?>" name="ID">
													<input type="hidden" value="add" name="action">
												</td>
												<td style="padding-right: 3px;"> 
													<input style="text-align:center; font-size:11px;" class='inp' type="text" class="qty" size="2" maxlength="2" value="1" name="QTY">
													<input type="submit" name="add" class="btn do pt-1 pb-1 pl-10 pr-10" alt="<?=htmlspecialcharsback($arElement['NAME'])?>" title="<?=htmlspecialcharsback($arElement['NAME'])?>" value="<?=GetMessage('SE_CATALOGELEMENT_BUY')?>"<? if ($arElement['PRICE']<=0) { ?> disabled="true"<? } ?> />
												</td>
											</tr>
										</tbody>
									</table>
								</form>
							</div>
						</div>
					</div>
				<? } ?>
				<br/>
			<? } ?>
		</div>
	<? } ?>
	
	<? if ($arResult['RATING_OBJ']) { ?>
		<div>
			<div class="pricebox">
				<div  style="text-align:left;padding-left:5px; padding-top: 10px;">
					<span class="reviews_like">:) - <?=$arResult['RATING']['CNT']['LIKE'];?></span> &nbsp;  &nbsp;	
					<span class="pricenovat">:( - <?=$arResult['RATING']['CNT']['DISLIKE'];?> </span> &nbsp;	 &nbsp;	
					<b>:-| - <?=$arResult['RATING']['CNT']['LIKE'];?></b>
				</div>
				<div class="iconbox order" style="text-align:right;vertical-align: top;">
					<a class="modalInput" href="#" rel="#rating" name="<?=$arResault["NAME"]?>"><?=GetMessage('SE_CATALOGELEMENT_DO_REVIEW')?></a>							
				</div>	
			</div>
			<br/>
			<? foreach ($arResult['RATING_OBJ']->arResult as $key=>$value ) { ?>
				<div class="searchprd clearfix">
					<table class="table_reviews" width="100%">
						<tr>
							<td class="first">
								<b><?=$value['PROPERTY_AUTOR_VALUE'];?></b><br/>
								<?=$value['PROPERTY_CITY_VALUE'];?><br/>
							</td>
							<td class="second">
								<div>
									<? if ($value['PROPERTY_RATING_ENUM_ID']!==NULL) {
										if ($value['PROPERTY_RATING_ENUM_ID']==307) {?>
										<span class="reviews_like"><?=$value['PROPERTY_RATING_VALUE'];?></span><br/>
										<? } else{ ?>
											<span class="pricenovat"><?=$value['PROPERTY_RATING_VALUE'];?></span><br/>	
										<?}
									} else { ?>
										<b><?=GetMessage('SE_CATALOGELEMENT_RATING_NETRAL')?></b><br/>
									<? } ?>
									<?=$value['PREVIEW_TEXT'];?>
									<span class="date"><?=$value['PROPERTY_DATE_VALUE'];?></span>
								</div>
							</td>
						</tr>
					</table>
					
				</div>
			<? } ?>
			<?if($arResult['RATING_OBJ']->IsNavPrint()) {
				echo "<p>"; $arResult['RATING_OBJ']->NavPrint(GetMessage('SE_CATALOGELEMENT_REVIEWS_NAV'), false, "tablebodytext", SITE_DIR."include/rating/nav_print.php"); echo "</p>";
			}?>
		</div>
	<? } ?>
	<? if (strlen($arResult['CAN'])>0) { ?><div><?=$arResult['CAN']?></div><? } ?>
	<? if ($arResult['UF_BOOKS']) { ?><div><?=$arResult['UF_BOOKS']?></div><? } ?>
</div>

<!-- #productoverview -->
<? if ($arResult['RELATED'] ) { ?>
	<h2><?=GetMessage('SE_CATALOGELEMENT_POHOJ')?></h2>
	<div class="contentclose" id="relatedblock">
		<?
		$row=1;
		$i=1;
		foreach ($arResult['RELATED'] as $key => $value) { ?>
			<? if ($i==1) { ?><div class="clearfix" id="relatedrow1<?=$row?>"><? }  ?>
				<div class="related clearfix<? if ($i==2) { ?> nextcol<? } ?>">
					<table width="50" height="50" cellpadding="0" cellspacing="0" border="0" class="prdimagebox"><tr><td valign="middle">
						<a title="<?=$value['NAME']?>" href="<?=$value['URL']?>"><img width="50" src="<?=$value['PICTURE']?>" title="<?=$SEO_MES.$value['NAME']?>" alt="<?=$SEO_MES.$value['NAME']?>"></a>
					</td></tr></table>
					<p class="item"><a title=" <?=$SEO_MES.$value['NAME']?>" href="<?=$value['URL']?>"><?=$value['NAME']?></a></p>
					<p class="pricebox"><?=GetMessage('SE_CATALOGELEMENT_PRICE')?>: <span class="pricenovat"><?=SaleFormatCurrency($value['PRICE'], "RUB");?></span></p>
				</div>
			<? if ($i==2) { $i=1; $row++; ?></div><? } else { $i++; } 
		} 
		if ($i==2) {
			echo '<div class="related clearfix nextcol"></div></div>';
		}
		?>
	</div>
<? } ?>
<script type="text/javascript">
	$(function() {
		$('input[name=form_text_14]').attr('value', '<?=$arResult['TITLE']?>').parents('tr').css('display', 'none');
	});
</script>