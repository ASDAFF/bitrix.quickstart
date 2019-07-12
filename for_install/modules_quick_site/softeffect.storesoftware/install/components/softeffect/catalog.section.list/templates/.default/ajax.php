<?
CComponentUtil::__IncludeLang($arResultComponent['AJAX_PATH']['TEMPLATE_FOLDER'], 'ajax.php');

if ($_REQUEST['ajax']=='Y') { ?>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.tools.min.js"></script>
<? } ?>
<? if ($filterCount>1) { ?>
	<div class="sidebox" id="box-filter-orig">
		<h3 id="filter">
			<?=GetMessage('SE_CATALOGSECTIONLIST_INCLUDE_AJAX_FILTER')?>
		</h3>
		<div id="navcategoriescontent1">
			<div class="left-slider-sky">
				<ul>
					<?
					$i=0;
					foreach ($arPortfolioFilter as $key => $value) { ?>
						<? if ((count($value["LIST"]) > 1)) { ?>
							<li class="opened" id='<?=$i?>'>
								<strong><?=$value["NAME"];?></strong>
								<ul>
									<? foreach ($value["LIST"] as $keyL => $valueL) { ?>
										<li>
											<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td valign="top">
														<input type="checkbox" 
															id="pfcb-<?=$valueL["ID"];?>" 
															name="PROPERTY[<?=$value["CODE"]?>][]" 
															value="<?=$valueL["ID"];?>" 
															onchange="changeInput(); return false;"
															<? if (in_array($valueL["ID"], $_REQUEST['PROPERTY'][$value["CODE"]])) { ?>checked="checked"<? } ?>
															<? if (!in_array($valueL["ID"], $propViewAll[$value['CODE']]) || (in_array($valueL["ID"], $propViewAll[$value['CODE']]) && !in_array($valueL["ID"], $propView[$value['CODE']])/* && !isset($_REQUEST['PROPERTY'][$arPortfolioFilter[$cycle]['CODE']])*/)) { ?>disabled="true"<? } ?>
														/>
													</td>
													<td>
														<label for="pfcb-<?=$valueL["ID"];?>"><?=$valueL["VALUE"];?></label>
													</td>
												</tr>
											</table>
										</li>
									<? } ?>
								</ul>
							</li>
							<?
							$i++;
						}
					} ?>
				</ul>
				<div id="pf-reset"><a href="#" title="<?=GetMessage('SE_CATALOGSECTIONLIST_INCLUDE_AJAX_CLEAR')?>" onclick="refreshSky(); return false;"><?=GetMessage('SE_CATALOGSECTIONLIST_INCLUDE_AJAX_CLEAR')?></a></div>
			</div>
		</div>
		<div class="boxfooter"></div>
	</div>
<? } ?>
<ul class="tabs">
	<? foreach ($arResultNEW as $typeLic => $arElement) { ?>
		<li><a href="#<?=str_replace(' ', '_', $typeLic)?>" tabs="<?=str_replace(' ', '_', $typeLic)?>"><?=$typeLic?></a></li>
	<? } ?>
</ul>
<div class="panes">
	<? foreach ($arResultNEW as $typeLic => $arElementData) { ?>
		<div>
			<? foreach ($arElementData as $edition => $arElementData2) { 
				if (strlen($edition)>0) { ?>
					<h2><?=$edition?></h2>
				<? } ?>
				<div class="editionBlock">
					<? foreach ($arElementData2 as $idEl => $arElement) { ?>
						<div class="searchprd clearfix">
							<table width="50" height="50" cellpadding="0" cellspacing="0" border="0" class="prdimagebox"><tbody><tr><td valign="middle">
		     					<a href="<?=$arElement['URL']?>" title="<?=$arElement["NAME"];?>">	  
									<img src="<?=$arElement['PICTURE']?>" width="50"  alt="<?=$arElement["NAME"];?>" title="<?=$SEO_MES.$arElement["NAME"];?>">
		       					</a>
		          			</td></tr></tbody></table>
						    <p class="item"><a href="<?=$arElement['URL']?>" title="<?=$SEO_MES.$arElement["NAME"];?>"><?=$arElement["NAME"];?></a></p>
						    <? if (strlen($arElement['DELIVERY_TIME'])>0) { ?><p class="itemdetails"><?=GetMessage('SE_CATALOGSECTIONLIST_INCLUDE_AJAX_TIME_DELIVERY')?>: <strong><?=$arElement['DELIVERY_TIME']?></strong></p><? } ?>
		                    <div class="controls noprint">
								<table cellspacing="0" cellpadding="0"><tbody><tr>
									<td>
										<input type="hidden" value="<?=$arElement['ID']?>" name="ID">
										<input type="hidden" value="add" name="action">
									</td>
									<td style="padding-right: 3px;"> 
										<input style="text-align:center; font-size:11px;" class='inp' type="text" class="qty" size="2" maxlength="2" value="1" name="QTY">
										<a class='btn' title="<?=$SEO_MES.$arElement["NAME"];?>" id="<?=$arElement['ID']?>" style='cursor:pointer;' onclick="basket(this); return false;"><?=GetMessage('SE_CATALOGSECTIONLIST_INCLUDE_AJAX_BUY')?></a>
									</td>
								</tr></tbody></table>
							</div>
							<? if (strlen($arElement['ARTICLE'])>0) { ?><p class="itemdetails"><?=GetMessage('SE_CATALOGSECTIONLIST_INCLUDE_AJAX_ARTICUL')?>: <?=$arElement['ARTICLE']?></p><? } ?>
							<div>
								<?=GetMessage('SE_CATALOGSECTIONLIST_INCLUDE_AJAX_PRICE')?>:&nbsp;<? if ($arElement['OLD_PRICE']!=0) { ?><del><b><font size="1"><?=SaleFormatCurrency($arElement['OLD_PRICE'], "RUB");?></font></b></del><? }
								if ($arElement['DISCOUNT']) { ?>&nbsp;<?=GetMessage('SE_CATALOGSECTIONLIST_INCLUDE_AJAX_DISCOUNT')?> - <b><font size="1"><?=$arElement['DISCOUNT']?>%</font></b><? }
								?>&nbsp;<span class="pricenovat_detail"><?=$arElement['PRICE'];?></span>
							</div>
						</div>
					<? } ?>
				</div>
			<? } ?>
		</div>
	<? } ?>
</div>

<input type="hidden" name="ajax" value="Y" />
<input type="submit" class="butt-hidden" style='display:none' />
<input type="hidden" id="PAGEN" name="PAGEN_1" value="" />
<script type="text/javascript">
	function hide () {
		$("#navcategoriescontent1").slideToggle("slow",function  () {
			if ($("#tog_img").attr('src')=="<?=SITE_DIR?>images/buttons/btn_collapsed.gif") {
				$("#tog_img").attr("src","<?=SITE_DIR?>images/buttons/btn_expanded.gif")
			} else {
				$("#tog_img").attr('src',"<?=SITE_DIR?>images/buttons/btn_collapsed.gif")
			};
		});
	}
	
	function hide2 () {
		$("#navcategoriescontent").slideToggle("slow",function  () {
			if($("#tog_img2").attr('src')=="<?=SITE_DIR?>images/buttons/btn_collapsed.gif"){
  				$("#tog_img2").attr("src","<?=SITE_DIR?>images/buttons/btn_expanded.gif")
  			} else {
  				$("#tog_img2").attr('src',"<?=SITE_DIR?>images/buttons/btn_collapsed.gif")
  			};
		});
	}
	
	function basket(obj){ 
		var id=$(obj).attr('id');
		var qty=$(obj).prev('.inp').attr('value');
		var url=location.href;
		var postvars = {ID:id,QTY:qty,action:'add',URL:url};

		$.post("<?=SITE_DIR?>basket/index.php", postvars,function(data) {
			window.location.href = '<?=SITE_DIR?>basket/';
		});
	}
	
	function doThis() {
		$('.butt-hidden').click();
		//console.log('doThis();');
	}
	
	<? if ($_REQUEST['ajax']!='Y') { // если пришли по фильтру в URL  ?>
		if ($(".left-slider-sky input:checkbox:checked").length>0) { doThis(); }
	<? } ?>
	
	function changeInput() {
		doThis();
	}
    
	function refreshSky() {
		$("#box-filter-orig input").removeAttr('checked');
		doThis();
		return false;
    };
    
	function doPagen (numPage) {
		document.getElementById('PAGEN').value=numPage;
		doThis();
	}
	
	$(function() {
		<? if ($_REQUEST['ajax']=='Y') {  ?>
			$('.left-slider-sky').removeAttr('style');
		<? } ?>
		
		$('#leftside #box-filter').height($("#box-filter-orig").height()+4);
		$("#box-filter-orig").css('display', 'block');
	});
	
	$("ul.tabs").tabs("div.panes > div", {
		effect: 'fade',
		tabs: 'li',
		fadeOutSpeed: 100
	});
	<? if ($_REQUEST['ajax']=='Y') {  ?>
		// изменение якоря
		$('ul.tabs a').click(function () {
			window.location.hash=$(this).attr('href');
		});
		
		// открываем вкладку из якоря
		if (window.location.hash!='' && window.location.hash!='#') {
			$('a[href='+window.location.hash+']').click();
		}
	<? } ?>
</script>
<br class="clearBoth" />