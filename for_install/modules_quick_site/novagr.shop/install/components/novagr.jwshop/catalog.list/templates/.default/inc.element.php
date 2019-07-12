<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
	/*
	* шаблон для вывода детальной карточки элемента каталога
	*/
?>
<?
	if (isset($arResult['ELEMENT']))
	{
		$val = $arResult['ELEMENT'];
?>
<div class="detailed-box col3-list box-left">
	<div class="left">
		<div class="image">
			<div class="label-card">
<?
			if($val['PROPERTY_SPECIALOFFER_VALUE'] == GetMessage("NOVAGR_SHOP_DA"))
				echo'<div class="card-spec-min"></div>';
			if($val['PROPERTY_NEWPRODUCT_VALUE'] == GetMessage("NOVAGR_SHOP_DA"))
				echo'<div class="card-new-min"></div>';
			if($val['PROPERTY_SALELEADER_VALUE'] == GetMessage("NOVAGR_SHOP_DA"))
				echo'<div class="card-lider-min"></div>';
?>
			</div>
			<div id="photos">
<?
		$PicPath = NOVAGR_JSWSHOP_TEMLATE_DIR."images/nophoto.png";
		if ( isset($val['PROPERTY_PHOTOS_VALUE']) )
		{
			$i = 0;
			foreach($val['PROPERTY_PHOTOS_VALUE'] as $subval)
			{
				$i++;
?>
				<a href="<?=$arResult['PHOTO'][ $subval ]['DETAIL_PICTURE'];?>" rel="gallery" class="detailLink" style="<? if($i > 1)echo'display:none;';?>">
					<img <? if($i <= 1) echo'id="detailImg"';?> width="280" height="373" alt="<?=$val['NAME']?>" src="<?=$arResult['PHOTO'][ $subval ]['DETAIL_PICTURE'];?>">
				</a>
<?
			}
		}else{
?>
				<a href="<?=$PicPath;?>" rel="gallery" id="detailImg" class="detailLink" style="<? if($i++ > 1)echo'display:none;';?>">
					<img width="280" height="373" <?=$val['NAME']?> src="<?=$PicPath;?>">
				</a>
<?	
		}
?>
			</div>
			<div class="thumbs" id="thumbs">
<?
			if(isset($val['PROPERTY_PHOTOS_VALUE']))
			foreach ($val['PROPERTY_PHOTOS_VALUE'] as $subval)
			{
?>
				<img class="previewImg" src="<?=$arResult['PHOTO'][$subval]['PREVIEW_PICTURE'];?>" width="90" height="120" alt="" href="<?=$arResult['PHOTO'][$subval]['DETAIL_PICTURE'];?>"  />
<?
			}
?>
			</div>
		</div>
	</div>
	
	<div class="right">
		<div class="name">
			<h1 class="title"><?=$val['NAME']?></h1>
		</div>
		<div class="clear"></div>
		<span class="clear">&nbsp;</span>
<?
			if($val['DETAIL_TEXT'] != "")
			{
?>
		<div class="description">
			<?=$val['DETAIL_TEXT'];?>
			<img width="15" height="14" alt="" src="<?=SITE_TEMPLATE_PATH?>/images/icon_comment_end.png" />
		</div>
<?
		}
?>		
		<div class="table">
<?
	if( !empty($val['PROPERTY_VENDOR_NAME']) )
	{
?>
			<div class="line">
				<div class="name"><?=GetMessage("NOVAGR_SHOP_BREND")?></div>
				<div class="value"> 
					<a href="<?=$arParams['BRAND_ROOT'];?><?=$val['PROPERTY_VENDOR_CODE'];?>/"><?=$val['PROPERTY_VENDOR_NAME'];?></a>
				</div>
				<div class="clear"></div>
			</div>
<?
	}
?>
<?
	if( !empty($val['PROPERTY_SKU_VALUE']) )
	{
?>
			<div class="line">
				<div class="name"><?=GetMessage("NOVAGR_SHOP_ARTIKUL")?></div>
				<div class="value"><?=$val['PROPERTY_SKU_VALUE'];?></div>
				<div class="clear"></div>
			</div>
<?
	}
?>
<?
	if( !empty($val['PROPERTY_MATERIAL_NAME']) )
	{
?>
			<div class="line">
				<div class="name"><?=GetMessage("NOVAGR_SHOP_MATERIAL")?></div>
				<div class="value"><?=$val['PROPERTY_MATERIAL_NAME'];?></div>
				<div class="clear"></div>
			</div>
<?
	}
?>
<?
	if( !empty($val['PROPERTY_SAMPLES_VALUE']) )
	{
?>
			<div class="line">
				<div class="name"><?=GetMessage("NOVAGR_SHOP_RISUNOK")?></div>
				<div class="value">
<?
		foreach($val['PROPERTY_SAMPLES_VALUE'] as $subkey => $subval)
			echo $val['mixData'][ $subval ]." / ";
?>
				</div>
				<div class="clear"></div>
			</div>
<?
	}
?>
		</div>
<?		
		if (count($val['COLOR']))
		{
?>
		<div class="choice-color"><?=GetMessage("NOVAGR_SHOP_VYBERITE_CVET")?></div>
		<div data-toggle="buttons-radio" class="btn-group color-ch tooltip-demo">
			<div id="color-ch" class="bs-docs-tooltip-examples">
<?
			foreach ($val['COLOR'] as $subval) 
			{
?>
				<button data-original-title="<?=$subval['NAME'];?>" class="btn" data-placement="top" rel="tooltip" data-color="<?=$subval['ID'];?>" type="button"><img width="35" height="33" alt="<?=$subval['NAME'];?>" src="<?=$arResult['PHOTO'][ $subval['ID'] ]['PREVIEW_PICTURE'];?>"></button>
<?
			}
?>			
			</div>
		</div>
<?
		}
?>
		<div class="choice-size"><?=GetMessage("NOVAGR_SHOP_VYBERITE_RAZMER")?></div>
		<div id="size-table">
			<a data-toggle="modal" href="#myModal8"><?=GetMessage("NOVAGR_SHOP_TABLICA_RAZMEROV")?></a>
		</div>
		
		<div class="tab-choice tooltip-demo">
			<div class="bs-docs-tooltip-examples">
<?
		if (count($val['STD_SIZE']))
		{
/*

			// сортируем массив с размерами от меньшего к большему
			$sortArray = array();
			foreach ($arResult["CURRENT_ELEMENT"]["STD_SIZE"] as $key => $size)
			{
				unset($arResult["CURRENT_ELEMENT"]["STD_SIZE"][$key]);
				// получаем ключ для сортировки массива размеров
				// если сортировка совпала - прибавляем + 1
				$keyForSort = $arResult['mixData'][$key]['SORT'];
				
				while (in_array($keyForSort, $sortArray))
					$keyForSort = $keyForSort+1;
				$sortArray[] = $keyForSort;
				$arResult["CURRENT_ELEMENT"]["STD_SIZE"][$keyForSort] = $size;
			}	
			ksort($arResult["CURRENT_ELEMENT"]["STD_SIZE"]);
*/
?>
				<ul class="nav nav-tabs size-tab" id="myTab">
<?
				$i = 0;
				foreach ($val['STD_SIZE'] as $key => $subval)
				{
?>
					<li id="li_<?=$subval['ID'];?>" <?=( $i==0 ? 'class="active"' : '')?>><a data-size="<?=$subval['ID'];?>"  href="#tab<?=$subval['ID'];?>"><?=$subval['NAME'];?></a></li><?
					$i++;
				}
?>
				</ul>
			</div>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/product.js?<?=Novagroup_Classes_General_Main::getVersion()?>"></script>
<?php 			
		}
		
			?>
			<div class="tab-content" id="myTabContent">
			<?php 
			$i = 0;
			//deb($arResult["CURRENT_ELEMENT"]["STD_SIZE"]);
			foreach ($arResult["CURRENT_ELEMENT"]["STD_SIZE"] as $key => $size) {
				// текущий выбранный размер
				if ($i==0) {
					$currentSize = $arResult['mixData'][$size["SIZE"]]['ID'];
				}
				
				?>
				<div id="tab<?=$arResult['mixData'][$size["SIZE"]]['ID']?>" class="tab-pane fade<?=( $i==0 ? ' active in' : '')?>">
					<div class="active-p">
    					<div class="post-p">	
    					<?php 
    					foreach ($size["REAL_SIZES"] as $k => $v) {
    						?><span><?=$arParams["SIZES_CODES"][$k]?></span><?php 
    					}
    					?>
    					</div>
    					<div class="post">
    					<?php 
    					foreach ($size["REAL_SIZES"] as $k => $v) {
    						?><span class="size-ar"><?=$v?> <?=GetMessage("NOVAGR_SHOP_SM")?></span> <?php 
    					}		
						?>
    					</div>
    				</div>
    			</div>		
    		<?php 
    			$i++;
			}	
    		 
			
			foreach ($arResult["OFFERS"] as $item) {
					
				/*deb($item["ID"]);
				deb($item["CATALOG_PRICE_1"]);// LINK_ELEMENT_ID
				deb($item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]);
				deb($item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]);
				deb($item["DISPLAY_PROPERTIES"]["PHOTOS"]["VALUE"]);*/
				//deb($item["ID"]);
			}
			?>
					<script>
					
					$(document).ready(function(){
						
						<?php 
						$dataForJs = array();						
						// инициализируем js объект для работы с товарными предложениями

						$foundFirstSizeFlag = false;
						foreach ($arResult["OFFERS"] as $item) {
							
							if ($item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"] == $currentSize && $foundFirstSizeFlag == false) {			
								?>
								var curPhotosSmall = [];
								var curPhotosBig = [];
								<?php 
								foreach ($item["DISPLAY_PROPERTIES"]["PHOTOS"]["VALUE"] as $photoId) {
									?>
									curPhotosSmall.push('<?=$arResult['PREVIEW_PICTURE'][$photoId]?>');
									curPhotosBig.push('<?=$arResult['DETAIL_PICTURE'][$photoId]?>');
									<?php 
								}		
								?>
								product.init(<?=$item["ID"]?>,'<?=$item["CATALOG_PRICE_1"]?>','<?=$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]?>','<?=$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]?>', '<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]['NAME']?>', '<?=$arResult['PREVIEW_PICTURE'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]?>', curPhotosSmall, curPhotosBig, '<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]]['NAME']?>');
								<?
								$foundFirstSizeFlag = true;
							} else {
								$dataForJs[] = $item;
							}

						}
						foreach ($dataForJs as $item) {
							?>
							var curPhotosSmall = [];
							var curPhotosBig = [];
							<?php 
							foreach ($item["DISPLAY_PROPERTIES"]["PHOTOS"]["VALUE"] as $photoId) {
								?>
								curPhotosSmall.push('<?=$arResult['PREVIEW_PICTURE'][$photoId]?>');
								curPhotosBig.push('<?=$arResult['DETAIL_PICTURE'][$photoId]?>');
								<?php 
							}		
							?>
							
							product.addToSet(<?=$item["ID"]?>,'<?=$item["CATALOG_PRICE_1"]?>','<?=$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]?>','<?=$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]?>', '<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]['NAME']?>', '<?=$arResult['PREVIEW_PICTURE'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]?>', curPhotosSmall, curPhotosBig, '<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]]['NAME']?>');
							<?php
						}	
						?>

						// обработчик клика по размеру
						$("#myTab a").live("click", function(){
							var sizeId = $(this).data("size");
							product.changeSize(sizeId);
							$("#myTab a[href^='#tab"+sizeId+"']").tab('show');
							return false;
						});
						// обработчик клика по цвету					
						$("#color-ch button").live("click", function(){
							var colorId = $(this).data("color");
							product.changeColor(colorId);
							
						});
						// тултипы
						$('.tooltip-demo').tooltip({
							selector: "button,li[rel=tooltip]"
						});
						// выполняем клик по первому цвету
						$('#color-ch ').find('button').first().trigger('click');
						
					});
					</script>	
			</div>
			<?	//class="active-p">	// class="tab-pane fade active in">
		
		?>
		 <div class="actual-price"><span id="sum"><?//=$val['PRICE']?></span></div>	
         <div class="adbasket"><a id="btnsel" class="btnsel" href="<?//echo $arResult["ADD_URL"]?>"><i class="icon-plus"></i> <?=GetMessage("NOVAGR_SHOP_DOBAVITQ_V_KORZINU")?></a></div>
         
         <div class="clear"></div>
		<h2><?=GetMessage("NOVAGR_SHOP_O_BRENDE")?></h2>
			<?=$val['PROPERTY_VENDOR_DETAIL_TEXT'];?>
		</div>
		<div class="clear"></div>
	</div>
	<!-- end -->
</div>


<div id="content" class="col3-list stuff-box">
<!-- 1 block -->
<?php 
$countElems = count($arResult["RECOMMEND_ELEMENTS"]);
if ($countElems > 0) {
	?>

<div class="card-item-pr">
	<div class="head">
		<h2 class="title"><?=GetMessage("NOVAGR_SHOP_VAM_TAKJE_MOJET_PONR")?></h2>
		<div class="clear"></div>
	</div>
	<div class="list">
		<div class="line even">
			<div class="item_number">				
				<div class="item-block">
					
					<?php 
					//$i = 0;
					$j = 0;
					foreach($arResult["RECOMMEND_ELEMENTS"] as $val) {
						
						if($arResult['PREVIEW_PICTURE'][ $val["PROPERTIES"]['PHOTOS']["VALUE"][0] ] == "")
							$arResult['PREVIEW_PICTURE'][ $val["PROPERTIES"]['PHOTOS']["VALUE"][0] ] = NOVAGR_JSWSHOP_TEMLATE_DIR."images/nophoto.png";
						
						if ($j==2) {
							$j = 0;
							?></div><div class="item-block"><?php 
						}
						
						?>
					<div  class="item"><?//=$i?> <?//=j?>
						<?php 
						
						?><div class="over">
							<div class="preview">
								<a href="<?=$val["DETAIL_PAGE_URL"]?>"><img src="<?=$arResult['PREVIEW_PICTURE'][ $val["PROPERTIES"]['PHOTOS']["VALUE"][0] ]?>" width="177" height="240" alt="" /></a>
								<div class="info-boxover">
									<div class="middle">
										<h4 class="title"><?=$val['NAME']?></h4>
										<div class="descr">
											<div class="gallery">
											<?
											$ctr = 0;
											if (count($val["PROPERTIES"]['PHOTOS']["VALUE"]) == 0 )
											{
											?>
														<a href="<?=$val["DETAIL_PAGE_URL"]?>"><img src="<?=NOVAGR_JSWSHOP_TEMLATE_DIR?>images/nophoto.png";?>" width="68" height="90" alt="" /></a>
							<?
													}
													foreach($val["PROPERTIES"]['PHOTOS']["VALUE"] as $subval)
													{
														if ($ctr++ > 2)break;
											?>
													<a href="<?=$val["DETAIL_PAGE_URL"]?>"><img src="<?=$arResult['PREVIEW_PICTURE'][$subval];?>" width="68" height="90" alt="" /></a>
									<?
													}
											?>

											</div>
											
											<p><a href="<?=$val["DETAIL_PAGE_URL"]?>"><?=GetMessage("NOVAGR_SHOP_PODROBNEE")?></a></p>
										</div>
										<div class="clear"></div>
										<div class="others gallery"></div>
									</div>
									<div class="bottom"></div>
								</div>
								<div class="name"><?=$val['NAME']?></div>
								<div class="price">
									<div class="actual"><?=$val['PRICE']?> <span class="rubles"><?=GetMessage("NOVAGR_SHOP_RUB")?></span></div> <span><?=$arResult['mixData'][$val["PROPERTIES"]['VENDOR']["VALUE"] ]['NAME']?></span>
								</div>
							</div>
							
						</div>
					</div>
					<?php 
						//$i++;
						$j++;
					}
					?>
					
					
			</div>
			
			
		</div>
	</div>
</div>
</div>

<?php

}

?>

<!--2 block -->

<?$APPLICATION->IncludeComponent("bitrix:sale.viewed.product", "demoshop", array(
	"VIEWED_COUNT" => "4",
	"VIEWED_NAME" => "Y",
	"VIEWED_IMAGE" => "Y",
	"VIEWED_PRICE" => "Y",
	"VIEWED_CANBUY" => "Y",
	"VIEWED_CANBUSKET" => "Y",
	"VIEWED_IMG_HEIGHT" => "100",
	"VIEWED_IMG_WIDTH" => "100",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"SET_TITLE" => "N"
	),
	false
);?>

	<!-- end block -->
</div>

	
<div id="myModal8" class="modal hide fade size-tab-my mod-size" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h3 id="myModalLabel8"><?=GetMessage("NOVAGR_SHOP_TABLICA_RAZMEROV1")?></h3>
            </div>
            <div class="modal-body">
            	<div>
	<img width="150" height="150" align="left" src="/upload/images/articles/womens_clothes.jpg"> 
<h3 style="color: rgb(55, 55, 55); margin-top: 0px; margin-right: 0px; margin-bottom: 15px; margin-left: 0px; font-family: Tahoma, Geneva, sans-serif; "><?=GetMessage("NOVAGR_SHOP_JENSKAA_ODEJDA")?></h3>
 
<p style="color: rgb(97, 97, 97); margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; font-family: Tahoma, Geneva, sans-serif; font-size: 12px; "><strong><?=GetMessage("NOVAGR_SHOP_ROST")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_ROST_IZMERAETSA_PO_V")?>. 
  <br>
 
  <br>
 <strong><?=GetMessage("NOVAGR_SHOP_OBHVAT_GRUDI")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_MERKA_SNIMAETSA_PO_V")?>. 
  <br>
 
  <br>
 <strong><?=GetMessage("NOVAGR_SHOP_OBHVAT")?></strong><strong><?=GetMessage("NOVAGR_SHOP_BEDER")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_SANTIMETROVAA_LENTA")?></p>
 
<p style="color: rgb(97, 97, 97); margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; font-family: Tahoma, Geneva, sans-serif; font-size: 12px; "> 
  </p><table width="750px" cellspacing="0" cellpadding="5" class="size_t">
    <tbody> 
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_MARKIROVKA")?></td><td><strong>XXS</strong></td><td><strong>XS</strong></td><td><strong>S</strong></td><td><strong>M</strong></td><td><strong>L</strong></td><td><strong>XL</strong></td><td><strong>XXL</strong></td><td><strong>XXXL</strong></td><td><strong>XXXXL</strong></td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_ROST1")?></td><td>164</td><td>170</td><td>170</td><td>176</td><td>176</td><td>176</td><td>182</td><td>182</td><td>182</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OBHVAT_GRUDI1")?></td><td>73-80</td><td>77-84</td><td>81-88</td><td>85-92</td><td>89-96</td><td>93-100</td><td>104</td><td>108</td><td>112</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OBHVAT_BEDER")?></td><td>81-88</td><td>85-92</td><td>89-96</td><td>93-100</td><td>97-104</td><td>101-108</td><td>108-110</td><td>112</td><td>116</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_RAZMER")?></td><td><strong>38-40</strong></td><td><strong>40-42</strong></td><td><strong>42-44</strong></td><td><strong>44-46</strong></td><td><strong>46-48</strong></td><td><strong>49-50</strong></td><td><strong>51-52</strong></td><td><strong>54</strong></td><td><strong>56</strong></td></tr>
     </tbody>
   </table>
 
  <br>
 <p></p>
 
<p style="color: rgb(97, 97, 97); margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; font-family: Tahoma, Geneva, sans-serif; font-size: 12px; "><img width="150" height="150" align="left" src="/upload/images/articles/womens_underwear.jpg"> </p>

<h3 style="color: rgb(55, 55, 55); margin-top: 0px; margin-right: 0px; margin-bottom: 15px; margin-left: 0px; "><?=GetMessage("NOVAGR_SHOP_JENSKOE_NIJNEE_BELQE")?></h3>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong><?=GetMessage("NOVAGR_SHOP_OBHVAT_GRUDI")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_MERKA_SNIMAETSA_PO_V1")?></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong><?=GetMessage("NOVAGR_SHOP_RAZMER_GRUDI")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_IZMERQTE_SNACALA_OBH")?></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong>                                      <?=GetMessage("NOVAGR_SHOP_OBHVAT_TALII")?></strong> 
  <br>
                                      <?=GetMessage("NOVAGR_SHOP_SANTIMETROVAA_LENTA1")?>. 
  <br>
 
  <br>
 <strong>                                      <?=GetMessage("NOVAGR_SHOP_OBHVAT")?></strong><strong><?=GetMessage("NOVAGR_SHOP_BEDER")?></strong> 
  <br>
                                       <?=GetMessage("NOVAGR_SHOP_SANTIMETROVAA_LENTA")?></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "> 
  </p><table width="750px" cellspacing="0" cellpadding="5"  class="size_t"> 
    <tbody> 
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_MARKIROVKA")?></td><td><strong>P</strong> (30)</td><td><strong>S</strong> (32)</td><td><strong>M</strong> (34)</td><td><strong>L </strong>(36)</td><td><strong>XL</strong> (38)</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OBHVAT_GRUDI1")?></td><td>75-80</td><td>80-85</td><td>85-90</td><td>90-100</td><td>100-110</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OBHVAT_POD_GRUDQU")?></td><td>65</td><td>70</td><td>75</td><td>80</td><td>85</td></tr>
     
      <tr><td><?=GetMessage("NOVAGR_SHOP_RAZMER_GRUDI1")?></td><td>AA-A</td><td>A-B</td><td>B-C</td><td>C-D</td><td>D-DD</td></tr>
     
      <tr><td><?=GetMessage("NOVAGR_SHOP_OBHVAT_TALII1")?></td><td>51-57</td><td>57-64</td><td>64-71</td><td>71-79</td><td>79-86</td></tr>
     
      <tr><td><?=GetMessage("NOVAGR_SHOP_OBHVAT_BEDER")?></td><td>79-86</td><td>86-91</td><td>91-99</td><td>99-104</td><td>104-112</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_RAZMER")?></td><td><strong>40-42</strong></td><td><strong>42-44</strong></td><td><strong>44-46</strong></td><td><strong>46-48</strong></td><td><strong>48-50</strong></td></tr>
     </tbody>
   </table>
 
  <br>
 <p></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><img width="150" height="150" align="left" src="/upload/images/articles/mens_clothes.jpg"> </p>

<h3 style="color: rgb(55, 55, 55); margin-top: 0px; margin-right: 0px; margin-bottom: 15px; margin-left: 0px; "><?=GetMessage("NOVAGR_SHOP_MUJSKAA_ODEJDA")?></h3>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong><?=GetMessage("NOVAGR_SHOP_ROST")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_ROST_IZMERAETSA_PO_V")?>. 
  <br>
 
  <br>
 <strong><?=GetMessage("NOVAGR_SHOP_OBHVAT_GRUDI")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_MERKA_SNIMAETSA_PO_V")?>. 
  <br>
 
  <br>
 <strong>                                      <?=GetMessage("NOVAGR_SHOP_OBHVAT")?></strong><strong><?=GetMessage("NOVAGR_SHOP_BEDER")?></strong> 
  <br>
                                      <?=GetMessage("NOVAGR_SHOP_SANTIMETROVAA_LENTA")?></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "> 
  </p><table width="750px" cellspacing="0" cellpadding="5"  class="size_t"> 
    <tbody> 
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_MARKIROVKA")?></td><td><strong>XS</strong></td><td><strong>S</strong></td><td><strong>M</strong></td><td><strong>L</strong></td><td><strong>XL</strong></td><td><strong>XXL</strong></td><td><strong>XXXL</strong></td><td><strong>XXXXL</strong></td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_ROST1")?></td><td>170</td><td>176</td><td>182</td><td>182</td><td>188</td><td>188</td><td>188</td><td>188</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OBHVAT_GRUDI1")?></td><td>92</td><td>96</td><td>100</td><td>104</td><td>108</td><td>112</td><td>116</td><td>120</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OBHVAT_BEDER")?></td><td>80</td><td>84</td><td>88</td><td>92</td><td>96</td><td>100</td><td>104</td><td>108</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_RAZMER")?></td><td><strong>46</strong></td><td><strong>48</strong></td><td><strong>50</strong></td><td><strong>52</strong></td><td><strong>54</strong></td><td><strong>56</strong></td><td><strong>58</strong></td><td><strong>60 
            <br>
           </strong></td></tr>
     </tbody>
   </table>
 
  <br>
 <p></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><img width="150" height="150"  align="left" src="/upload/images/articles/mens_underwear.jpg"> </p>

<h3 style="color: rgb(55, 55, 55); margin-top: 0px; margin-right: 0px; margin-bottom: 15px; margin-left: 0px; "><?=GetMessage("NOVAGR_SHOP_MUJSKOE_NIJNEE_BELQE")?></h3>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong><?=GetMessage("NOVAGR_SHOP_OBHVAT")?></strong><strong><?=GetMessage("NOVAGR_SHOP_BEDER")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_SANTIMETROVAA_LENTA")?></p>

<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; ">
  <br>
</p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "> 
  </p><table width="750px" cellspacing="0" cellpadding="5"  class="size_t"> 
    <tbody> 
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_MARKIROVKA")?></td><td><strong>XS</strong></td><td><strong>S</strong></td><td><strong>M</strong></td><td><strong>L</strong></td><td><strong>XL</strong></td><td><strong>XXL</strong></td><td><strong>XXXL</strong></td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OBHVAT_BEDER")?></td><td>92-96</td><td>96-100</td><td>100-104</td><td>104-108</td><td>108-112</td><td>112-124</td><td>124-136</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_RAZMER")?></td><td><strong>44-46</strong></td><td><strong>46-48</strong></td><td><strong>48-50</strong></td><td><strong>50-52</strong></td><td><strong>52-54</strong></td><td><strong>54-60</strong></td><td><strong>60-66</strong></td></tr>
     </tbody>
   </table>
 
  <br>
 <p></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><img width="150" height="150"  align="left" src="/upload/images/articles/womens_shoes.jpg"> </p>

<h3 style="color: rgb(55, 55, 55); margin-top: 0px; margin-right: 0px; margin-bottom: 15px; margin-left: 0px; "><?=GetMessage("NOVAGR_SHOP_JENSKAA_OBUVQ")?></h3>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong><?=GetMessage("NOVAGR_SHOP_DLINA_STOPY")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_VSTANQTE_NA_LIST_BUM")?></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "> 
  </p><table width="750px" cellspacing="0" cellpadding="5"  class="size_t"> 
    <tbody> 
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_DLINA_STOPY1")?></td><td>20.5</td><td>22</td><td>22.5</td><td>23</td><td>23.5</td><td>24</td><td>24.5</td><td>25</td><td>25.5</td><td>26</td><td>26.5</td><td>27</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_RAZMER")?></td><td><strong>33.5</strong></td><td><strong>34</strong></td><td><strong>34.5</strong></td><td><strong>35</strong></td><td><strong>35.5</strong></td><td><strong>36.5</strong></td><td><strong>37</strong></td><td><strong>37.5</strong></td><td><strong>38</strong></td><td><strong>39</strong></td><td><strong>39.5</strong></td><td><strong>40</strong></td></tr>
     </tbody>
   </table>
 
  <br>
 <p></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><img width="150" height="150"  align="left" src="/upload/images/articles/womens_shoes.jpg"> </p>
 
<h3 style="color: rgb(55, 55, 55); margin-top: 0px; margin-right: 0px; margin-bottom: 15px; margin-left: 0px; "><?=GetMessage("NOVAGR_SHOP_MUJSKAA_OBUVQ")?></h3>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong><?=GetMessage("NOVAGR_SHOP_DLINA_STOPY")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_VSTANQTE_NA_LIST_BUM")?></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "> 
  </p><table width="750px" cellspacing="0" cellpadding="5"  class="size_t"> 
    <tbody> 
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_DLINA_STOPY1")?></td><td>24.5</td><td>25</td><td>25.5</td><td>26</td><td>26.5</td><td>27</td><td>27.5</td><td>28</td><td>28.5</td><td>29</td><td>29.5</td><td>30</td><td>31</td><td>32</td><td>33</td><td>34</td><td>35</td><td>36</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_RAZMER")?></td><td><strong>38</strong></td><td><strong>39</strong></td><td><strong>39.5</strong></td><td><strong>40</strong></td><td><strong>41</strong></td><td><strong>41.5</strong></td><td><strong>42</strong></td><td><strong>43</strong></td><td><strong>43.5</strong></td><td><strong>44</strong></td><td><strong>44.5</strong></td><td><strong>45</strong></td><td><strong>46.5</strong></td><td><strong>47.5</strong></td><td><strong>48.5</strong></td><td><strong>49.5</strong></td><td><strong>50.5</strong></td><td><strong>51.5</strong></td></tr>
     </tbody>
   </table>
 
  <br>
 <p></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><img width="150" height="150"  align="left" src="/upload/images/articles/gloves.jpg"> </p>
 
<h3 style="color: rgb(55, 55, 55); margin-top: 0px; margin-right: 0px; margin-bottom: 15px; margin-left: 0px; "><?=GetMessage("NOVAGR_SHOP_PERCATKI")?></h3>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong><?=GetMessage("NOVAGR_SHOP_OBHVAT_RUKI")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_IZMERQTE_SIRINU_LADO")?></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "> 
  </p><table width="750px" cellspacing="0" cellpadding="5"  class="size_t"> 
    <tbody> 
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_MARKIROVKA")?></td><td><strong>XXS</strong></td><td><strong>XS</strong></td><td><strong>S</strong></td><td><strong>M</strong></td><td><strong>L</strong></td><td><strong>XL</strong></td><td><strong>XXL</strong></td><td><strong>XXXL</strong></td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OBHVAT_RUKI1")?></td><td>15.2</td><td>16.5</td><td>17.8</td><td>19</td><td>20.3</td><td>21.6</td><td>22.9</td><td>24</td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_RAZMER")?></td><td><strong>6</strong></td><td><strong>6.5</strong></td><td><strong>7</strong></td><td><strong>7.5</strong></td><td><strong>8</strong></td><td><strong>8.5</strong></td><td><strong>9</strong></td><td><strong>9.5</strong></td></tr>
     </tbody>
   </table>
 
  <br>
 <p></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><img width="150" height="150"  align="left" src="/upload/images/articles/belts.jpg"> </p>
 
<h3 style="color: rgb(55, 55, 55); margin-top: 0px; margin-right: 0px; margin-bottom: 15px; margin-left: 0px; "><?=GetMessage("NOVAGR_SHOP_REMNI")?></h3>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong><?=GetMessage("NOVAGR_SHOP_OBHVAT_TALII")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_IZMERQTE_OBHVAT_TALI")?></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; ">
  <br>
</p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "> 
  </p><table width="750px" cellspacing="0" cellpadding="5"  class="size_t"> 
    <tbody> 
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_MARKIROVKA")?></td><td><strong>XXS</strong></td><td><strong>XS</strong></td><td><strong>S</strong></td><td><strong>M</strong></td><td><strong>L</strong></td><td><strong>XL</strong></td><td><strong>XXL</strong></td><td><strong>XXXL</strong></td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OBHVAT_TALII1")?></td><td>60-65</td><td>70-75</td><td>80-85</td><td>90-95</td><td>100</td><td>105</td><td>110</td><td>115</td></tr>
     </tbody>
   </table>
 
  <br>
 <p></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><img width="150" height="150"  align="left" src="/upload/images/articles/cap.jpg"> </p>
 
<h3 style="color: rgb(55, 55, 55); margin-top: 0px; margin-right: 0px; margin-bottom: 15px; margin-left: 0px; "><?=GetMessage("NOVAGR_SHOP_GOLOVNYE_UBORY")?></h3>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong><?=GetMessage("NOVAGR_SHOP_OKRUJNOSTQ_GOLOVY")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_CTOBY_NE_OSIBITQSA_S")?></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; ">
  <br>
</p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "> 
  </p><table width="750px" cellspacing="0" cellpadding="5"  class="size_t"> 
    <tbody> 
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_MARKIROVKA")?></td><td><strong>XXS</strong></td><td><strong>XS</strong></td><td><strong>S</strong></td><td><strong>M</strong></td><td><strong>L</strong></td><td><strong>XL</strong></td><td><strong>XXL</strong></td><td><strong>XXXL</strong></td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OKRUJNOSTQ_GOLOVY1")?></td><td>54</td><td>55</td><td>56</td><td>57</td><td>58</td><td>59</td><td>60</td><td>61</td></tr>
     </tbody>
   </table>
 
  <br>
 <p></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><img width="150" height="150"  align="left" src="/upload/images/articles/rings.jpg"> </p>
 
<h3 style="color: rgb(55, 55, 55); margin-top: 0px; margin-right: 0px; margin-bottom: 15px; margin-left: 0px; "><?=GetMessage("NOVAGR_SHOP_KOLQCA")?></h3>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "><strong><?=GetMessage("NOVAGR_SHOP_VNUTRENNIY_DIAMETR")?></strong> 
  <br>
 <?=GetMessage("NOVAGR_SHOP_CTOBY_OPREDELITQ_RAZ")?></p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; ">
  <br>
</p>
 
<p style="margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-bottom: 20px; "> 
  </p><table width="750px" cellspacing="0" cellpadding="5"  class="size_t"> 
    <tbody> 
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_MARKIROVKA")?></td><td><strong>XXS</strong></td><td><strong>XS</strong></td><td><strong>S</strong></td><td><strong>M</strong></td><td><strong>L</strong></td><td><strong>XL</strong></td><td><strong>XXL</strong></td><td><strong>XXXL</strong></td></tr>
     
      <tr><td width="100"><?=GetMessage("NOVAGR_SHOP_OKRUJNOSTQ_GOLOVY1")?></td><td>12.04-13.03</td><td>13.07-14.09</td><td>15.03-16.05</td><td>16.09</td><td>17.03-17.07</td><td>18.01-18.05</td><td>18.09-19.04</td><td>19.08-19.02 
          <br>
         </td></tr>
     </tbody>
   </table>
 	</div>
</div>
</div>
<?//=$arResult['SIZE_TABLE'];?>
<?
}
?>
<script>
	$(document).ready(function() {
		// обработчик наведения мыши на превью фоток
		$('.previewImg').mouseenter(function(){
			$('#detailImg').attr('src', $(this).attr('href'));
		});
		// раскраска полосок
		$(".line:even").addClass("even");
	});
</script>