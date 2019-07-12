<?php
//получаем форму редактирования товара

$props = array();
$nameProduct = '';
$price = '';
$sections = array();
$sectionName = '';
$sectionID = '';
$productOffers = array();
$sizesRelations = array();
$sizesColors = array();

$currentMaterial = array("ID" => '', "NAME" => '' );
$currentBrand = array("ID" => '', "NAME" => '' );

//deb($_REQUEST);
if ($action == "get_edit_form") {

	$actionVar = "edit_product";
	// получаем товар по ID
	$res = CIBlockElement::GetByID($productID);

	if ($obRes = $res->GetNextElement()) {

		$arProduct = $obRes->GetFields();
		//deb($arProduct);

		// название товара - берется для названия элемента в приоритетном размещении
		$nameProduct = $arProduct['NAME'];
		// получаем Секцию
		$rsSection = CIBlockSection::GetByID($arProduct['IBLOCK_SECTION_ID']);

		if ($arSection = $rsSection -> GetNext()) {
			//deb($arSection);
			$sectionID = $arProduct['IBLOCK_SECTION_ID'];
			$sectionName = $arSection["NAME"];

			// получаем родителькие разделы
			$rsPath = GetIBlockSectionPath($iblockID, $sectionID);
			while($arPath=$rsPath->GetNext()) {
				//echo $arPath["NAME"].'<br />';
				$sections[] = array("CODE" => $arPath["CODE"], "NAME" => $arPath["NAME"]);
			}
		}

		//deb($sections);


		//CModule::IncludeModule("sale");
		// получаем цену товара
		$price = GetCatalogProductPrice($arProduct['ID'], 1);

		// свойства товара
		$props = $obRes->GetProperties();
		//deb($props);
		$HEADLINE = GetMessage("AJAX_FORM_EDIT");
	}
	if (!empty($props["MATERIAL"]["VALUE"])) {
		$res = CIBlockElement::GetByID($props["MATERIAL"]["VALUE"])->Fetch();

		$currentMaterial["ID"] = $res["ID"];
		$currentMaterial["NAME"] = $res["NAME"];
	}
	if (!empty($props["VENDOR"]["VALUE"])) {
		$res = CIBlockElement::GetByID($props["VENDOR"]["VALUE"])->Fetch();

		$currentBrand["ID"] = $res["ID"];
		$currentBrand["NAME"] = $res["NAME"];
	}
	// получим тп
	$arFilter = array();
	$arFilter["IBLOCK_CODE"] = "products_offers";
	$arFilter["PROPERTY_CML2_LINK"] = $arProduct["ID"];
	//deb($arFilter);
	$rsElement = CIBlockElement::GetList(false, $arFilter, false, false );

	while ($data = $rsElement -> GetNextElement()) {

		$offer = $data->GetFields();
		$offer["props"] = $data->GetProperties();
		$ar_res = CCatalogProduct::GetByID($offer["ID"]);

		$offer["QUANTITY"] = $ar_res["QUANTITY"];
		$ar_res = CPrice::GetBasePrice($offer["ID"]);
		$offer["PRICE"] = $ar_res["PRICE"];
		//deb($ar_res);
		$productOffers[] = $offer;
		// группируем тп по размерам
		if (!empty($offer['props']['STD_SIZE']["VALUE"])) {
			$index = $offer['props']['STD_SIZE']["VALUE"];
			$sizesRelations[$index][] = $offer;
		}

	}
	
} else {
	
	$actionVar = "add_product";
	$HEADLINE = GetMessage("AJAX_FORM_ADD");
}
$arResult = array();

$arFilter = array("ACTIVE" => "Y" , "IBLOCK_CODE"=> "samples");

$res = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false);

while ($ob = $res->Fetch()) {
	$arResult["SAMPLES"][] = $ob;
}
// получим список цветов
$arFilter = array("ACTIVE" => "Y" , "IBLOCK_CODE"=> "colors");
$res = CIBlockElement::GetList(Array(), $arFilter, false);

while ($ob = $res->Fetch()) {

	$arResult["COLORS"][$ob["ID"]] = array("ID"=> $ob["ID"], "NAME"=> $ob["NAME"]);
}
// получим список размеров
$arFilter = array("ACTIVE" => "Y" , "IBLOCK_CODE"=> "std_sizes");
$res = CIBlockElement::GetList(Array(), $arFilter, false);

while ($ob = $res->Fetch()) {

	$arResult["SIZES"][$ob["ID"]] = $ob["NAME"];
}
// получим список брэндов
$arFilter = array("ACTIVE" => "Y" , "IBLOCK_CODE"=> "vendor");
$res = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false);

while ($ob = $res->Fetch()) {

	$arResult["BRANDS"][$ob["ID"]] = $ob["NAME"];
}
// получим список материалов
$arFilter = array("ACTIVE" => "Y" , "IBLOCK_CODE"=> "materials");
$res = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false);

while ($ob = $res->Fetch()) {

	$arResult["MATERIALS"][$ob["ID"]] = $ob["NAME"];
}
?>
<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">
		&times;
	</button>
	<h2 id="myModalgroup1"><?=$HEADLINE?></h2>
</div>
<div class="modal-body">
	<div class="row officeDealer">
		<div class="span10">
			<ul class="breadcrumb">
			<?php

			if (count($sections)>0) {
				foreach ($sections as $section) {
				?>
				<li><a href="/catalog/<?=$section["CODE"]?>/"><?=$section["NAME"]?></a><span class="divider">/</span></li>
				<?
				}
			}
			?>
				<li class="active"><?=$arProduct['NAME']?></li>
			</ul>
			<form id="productEditForm" method="post" action="<?=$_SERVER["PHP_SELF"]?>" class="form-inline product-form">
			<input type="hidden" name="product_id" value="<?=$productID?>" />
			<input type="hidden" name="action" value="<?=$actionVar?>" />
			<input type="hidden" name="iblid" id="iblid_hidden" value="<?=$iblockID?>" />
			<input type="hidden" id="show_picture" name="show_picture" value="0" />
			<fieldset>
			<div class="well lab-left">
				<div class="row">
					<div class="valid-f left">
						<div class="control-group">
							<label for="name_product" class="control-label"> <?=GetMessage("AJAX_FORM_NAME")?> <span class="arrow-required">*</span> </label>
							<div class="controls">
								<input type="text" id="name_product" value="<?=$arProduct['NAME']?>" name="name" data-val="true" data-val-required="<?=GetMessage("AJAX_FORM_FILL_FIELD")?>" class="span5">
								<span class="field-validation-valid help-inline" data-valmsg-for="name_product" data-valmsg-replace="true"> </span>
							</div>
						</div>
					</div>
					<div class="right valid-f">
						<div class="control-group">
							<label for="price_product" class="control-label"> <?=GetMessage("AJAX_FORM_PRICE_ALL")?></label>
							<div class="controls">
								<input type="text" class="span1" value="<?=( !empty($price["PRICE"]) ? $price["PRICE"] : '')?>" id="price_product" name="price" data-val="true" data-val-required="<?=GetMessage("AJAX_FORM_FILL_FIELD")?>">
								<span class="field-validation-valid help-inline" data-valmsg-for="price_product" data-valmsg-replace="true"> </span>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="valid-f name-produkt-wrap left" for="input7">
						<div class="control-group">
							<div class="fl-left">
								<a id="addGroup" href="#ListTree" data-toggle="modal" class="name-label"><?=GetMessage("AJAX_FORM_CHOOSE_GROUP")?> <span class="arrow-required">*</span></a>
							</div>
			
							<input type="hidden" name="section" id="section" value="<?=$sectionID?>">
							<span data-val-required="<?=GetMessage("AJAX_FORM_FILL_FIELD")?>" data-val="true" value="" name="section_name" id="input7"> <span class="name-produkt span2" id="section_name"><?=$sectionName?></span> <span id="section_name_span" data-valmsg-replace="true" data-valmsg-for="section_name" class="field-validation-valid help-inline"></span> </span>
						</div>
					</div>
					<div class="right valid-f">
						<div class="control-group">
							<?php /*<label class="control-label"> ID товара на taobao <span class="arrow-required">*</span> </label>
							<div class="controls">
								<input type="text" data-val-required="<?=GetMessage("AJAX_FORM_FILL_FIELD")?>" data-val="true" value="<?=$props["ID_PRODUCT_TAOBAO"]["VALUE"]?>" name="ID_PRODUCT_TAOBAO" id="ID_PRODUCT_TAOBAO" class="span3">
								<span data-valmsg-replace="true" data-valmsg-for="ID_PRODUCT_TAOBAO" class="field-validation-valid help-inline"> </span>
							</div>
							*/?>
						</div>
						
					</div>
				</div>
				<div class="row">
					<div class="control-group valid-f country">
						<div class="left">
							<label class="lab-left" id="windowLabel"><?=GetMessage("AJAX_FORM_SAMPLE")?> <span class="arrow-required">*</span></label>
							<div class="controls">
								<select name="samples" id="samples" class="selectpicker show-tick span3" style="display: none;">
								<?php
								if (count($arResult["SAMPLES"])) {
									foreach ($arResult["SAMPLES"] as $window) {
										if ($window["ID"] == $props["SHOP_WINDOW"]["VALUE"]) $selected = "selected" ;
										else $selected = '';
									?>
									<option <?=$selected?> value="<?=$window["ID"]?>"><?=$window["NAME"]?></option>
									<?php
									}
								}
								?></select>
							</div>
						</div>
						<div class="right">
							<label>Страна</label>
							<div class="controls">
								<select name="COUNTRY" id="COUNTRY" class="selectpicker show-tick span3" style="display: none;">
								<option value="-1"><?=GetMessage("AJAX_FORM_CHOOSE_COUNTRY")?></option>
								<?php
								$arFilter = array();
								$arFilter["IBLOCK_CODE"] = 'countries';
								$rsElement = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false );
								while ($data = $rsElement -> GetNext()) {
									$sel = "";
									if (!empty($props["COUNTRY"]["VALUE"])) {
										if ($data["ID"] == $props["COUNTRY"]["VALUE"]) $sel = "selected";
									}
									?><option <?=$sel?> value="<?=$data["ID"]?>"><?=$data["NAME"]?></option><?
								}
								?>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="clear">&nbsp;</div>
				<div class="row-section country autosuggest temp">
				<div class="control-group">
					<div class="left">
						<label class="control-label" id="brandLabel" ><?=GetMessage("AJAX_FORM_BRAND")?> <span class="arrow-required">*</span></label>
						<div class="controls">
							<select name="brand_id" id="brand_id" data-send-btn="<?=GetMessage("LABEL_BTN_SEND")?>" data-title-mess="<?=GetMessage("AJAX_FORM_TITLE_MESS")?>" class="span3" data-add-label="<?=GetMessage("ADD_VALUE_LABEL")?>" data-add-mess="<?=GetMessage("AJAX_FORM_ADD_BRAND")?>" data-search-container="add_brand" data-ajax-handler="<?=$_SERVER["PHP_SELF"]?>">
							<?php 
							foreach ($arResult["BRANDS"] as $brandID => $brandName) {
							?>
							<option value="<?=$brandID?>"><?=$brandName?></option>
							<?php 
							}
        					?>
    						</select>
						</div>
						<div class="clear">&nbsp;</div>

					</div>
					<div class="right">
						<label class="control-label" id="materLabel" ><?=GetMessage("AJAX_FORM_MATERIAL")?> <span class="arrow-required">*</span></label>
						<div class="controls">
							
							<select name="material_id" id="material_id" data-send-btn="<?=GetMessage("LABEL_BTN_SEND")?>" data-title-mess="<?=GetMessage("AJAX_FORM_TITLE_MESS")?>" class="span3" data-add-label="<?=GetMessage("ADD_VALUE_LABEL")?>" data-add-mess="<?=GetMessage("AJAX_FORM_ADD_MATERIAL")?>" data-search-container="add_material" data-ajax-handler="<?=$_SERVER["PHP_SELF"]?>">
							<?php 
							foreach ($arResult["MATERIALS"] as $id => $name) {
							?>
							<option value="<?=$id?>"><?=$name?></option>
							<?php 
							}
        					?>
    						</select>
						</div>
						<div class="clear">&nbsp;</div>
						<label for="textarea"><?=GetMessage("AJAX_FORM_MATERIAL_D")?></label>
						<?php 
						//deb($props['MATERIAL_DESC']);
						?>
						<div class="controls">
							<textarea class="input-xlarge" id="MATERIAL_DESC" name="MATERIAL_DESC" rows="3">
							<?=(!empty($props['MATERIAL_DESC']['VALUE']["TEXT"]) ? $props['MATERIAL_DESC']['VALUE']["TEXT"] : '')?></textarea>
						</div>
					</div>
				</div>
				<div class="clear">&nbsp;</div>
			</div>
		</div>

		<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th class="size_tabl"><?=GetMessage("AJAX_FORM_SIZES")?></th>
				<th class="size_tabl"><?=GetMessage("AJAX_FORM_REAL_SIZES")?></th>
				<th>&nbsp;</th>
				<th class="size_tabl"><?=GetMessage("AJAX_FORM_DEL")?></th>
			</tr>
		</thead>
		<tbody id="sizes_body">
		<?php

		if (count($sizesRelations)) {
			
			foreach ($sizesRelations as $sizeID => $offers) {
				
				$i = 1;
				foreach ($offers as $offer) {
					
					//deb($offer['props']);
					$sizesColors[$sizeID][$offer['ID']] = array(
							'COLOR'=>$offer['props']['COLOR']["VALUE"],
							'QUANTITY'=>$offer['QUANTITY'],
							'PRICE' => $offer['PRICE']
					);
					if ($i==1) {

						?>
						<tr id="tr-for-size-<?=$sizeID?>" class="sizeTR" data-size-id="<?=$sizeID?>">
						<td><?=$arResult["SIZES"][$offer['props']['STD_SIZE']["VALUE"]]?>
						<input type="hidden" name="DIMENSION_STD[<?=$sizeID?>]" value="<?=$sizeID?>" />
						</td>
						<td class="size_tabl">
						<?php
			            //deb($DIMENSION_REAL[$key]);
			            foreach ($offer['props'] as $k => $v) {
			            	//deb($k);
			            	//deb( $v);
			            	if (startsWith($k, "REAL_")) {
			            		?>
			            		<span> <label><?=$v["NAME"]?></label>
								<input type="text" title="выбрать" value="<?=$v["VALUE"]?>" size="5" name="<?=$k?>[<?=$sizeID?>]">
								</span>
			            		<?
			            	}
			            }
			            $i++;
						?>
						</td>
						<?php
					}
				}
				//deb($sizesColors);
				?>
				<td class="wrap-choice">
					<div id="wrapchoice_<?=$sizeID?>">
					<?php
					foreach ($sizesColors[$sizeID] as $offerID => $value) {

						?>
						<div class="table-choice">
						<div class="dell-choice"><a href="#" class="btn btn-danger">&times;</a></div>
						<table class="my-table">
						<tr>
						<td>
						<div class="control-group my-td">
							<label><?=GetMessage("AJAX_FORM_COLOR")?></label>
							<div class="controls">
								<?php

								$colorID = '';
								$colorName = '';
								foreach ($arResult["COLORS"] as $id => $color) {


									if (!empty($value["COLOR"]) && $value["COLOR"] == $id) {
										$colorID = $id;
										$colorName = $color["NAME"];
									}

								}
								
								?>
								<select name="colorOffer[<?=$sizeID?>][<?=$offerID?>]" class="span3 colorSelect" data-title-mess="<?=GetMessage("AJAX_FORM_TITLE_MESS")?>" data-color-id="<?=$colorID?>" data-color-name="<?=$colorName?>" data-add-label="<?=GetMessage("ADD_VALUE_LABEL")?>" data-send-btn="<?=GetMessage("LABEL_BTN_SEND")?>" data-add-mess="<?=GetMessage("AJAX_FORM_ADD_COLOR")?>" data-search-container="add_color<?=$offerID?>" data-ajax-handler="<?=$_SERVER["PHP_SELF"]?>">
								<?php
								$j++;
								foreach ($arResult["COLORS"] as $id => $color) {

									$sel = "";
									if (!empty($value["COLOR"]) && $value["COLOR"] == $id) $sel = 'selected';

									?><option <?=$sel?> value="<?=$id?>"><?=$color["NAME"]?></option><?php

								}
								?>
								</select>
								<?php ?>
							</div>
						</div>
						<div class="control-group my-td">
							<label><?=GetMessage("AJAX_FORM_QUANTINY")?></label>
							<div class="controls">
								<input class="span1" name="quantityOffer[<?=$sizeID?>][<?=$offerID?>]" type="text" value="<?=$value["QUANTITY"]?>">
							</div>
						</div>
						<div class="valid-f price-valid">
							<div class="control-group my-td">
								<label><?=GetMessage("AJAX_FORM_PRICE")?> <span class="arrow-required">*</span></label>
								<div class="controls">
									<input class="span1 priceOf" type="text" data-val-required="<?=GetMessage("AJAX_FORM_FILL_FIELD")?>" data-val="true" value="<?=$value["PRICE"]?>" name="priceOffer[<?=$sizeID?>][<?=$offerID?>]">
									<!--  <span class="field-validation-valid help-inline" data-valmsg-replace="true" data-valmsg-for="inputnew"> </span>-->
								</div>
							</div>
						</div>
						
						
						</td>
						</tr>
						</table>
						</div>
						<?php
					}
					?>
					</div>
					<p class="valid-f ">
						<span data-val-required="<?=GetMessage("AJAX_FORM_FILL_FIELD")?>" data-val="true" value="" name="colorSizeInput<?=$sizeID?>" id="colorSizeInput<?=$sizeID?>">
						<a href="#" data-size-id="<?=$sizeID?>" class="colorAdd name-label"><?=GetMessage("LABEL_ADD_COLOR")?> <span class="arrow-required">*</span></a><span data-valmsg-replace="true" data-valmsg-for="colorSizeInput<?=$sizeID?>" class="field-validation-valid help-inline"></span>
						</span>
					</p>
				</td>
				<td class="tooltip-demo"><a href="#" class="btn btn-danger del-size" data-size-id="<?=$sizeID?>" rel="tooltip" data-placement="top" data-original-title="Удалить"><i class="icon-remove icon-white"></i></a></td>
	          </tr>
	          <?php
			}
		}
		?>
		</tbody>
		</table>
		<div >
			<a href="#modal_div" id="choose_size" class="btn btn-info">
			<?=GetMessage("AJAX_FORM_SIZE_ADD")?>
			</a>
		</div>

		<div class="row lab">
			<div class="span6">
				<div class="control-group">
					<div class="controls">
					<label class="control-label">Title</label>
					<input type="text" name="TITLE" value="<?=$props["TITLE"]["VALUE"]?>" class="span4">
				</div>
			</div>
			<div class="control-group">

				<div class="controls">
					<label class="control-label">Header1</label>
					<input type="text" name="HEADER1" value="<?=$props["HEADER1"]["VALUE"]?>" class="span4">
				</div>
			</div>
			<div class="control-group">

				<div class="controls">
					<label class="control-label">Keywords</label>
					<input type="text" name="KEYWORDS" value="<?=$props["KEYWORDS"]["VALUE"]?>" class="span4">
				</div>
			</div>
			<div class="control-group">

				<div class="controls">
					<label class="control-label">Meta_description</label>
					<input type="text" name="META_DESCRIPTION" value="<?=$props["META_DESCRIPTION"]["VALUE"]?>" class="span4">
				</div>
			</div>
			<div class="control-group" id="div_preview">

				<label class="wysiw"><?=GetMessage("AJAX_FORM_DETAIL")?></label>
				
				<textarea id="DETAIL_TEXT" name="DETAIL_TEXT" class="textarea span6" placeholder="<?=GetMessage("AJAX_FORM_ENTER_TEXT")?>"><?=$arProduct['DETAIL_TEXT']?></textarea>
				

			</div>
		</div>
									<div class="span4">
										<div class="control_clear control-group">
											<div class="controls">
												<label id="fileLabel" for="fileInput" class="fileinput"><?=GetMessage("AJAX_FORM_DETAIL_PHOTO")?> <span class="arrow-required">*</span></label>
												<div class="maininp card-pr">
													<input type="file" id="fileInput" class="right_b" size="30" name="fileInput">
												</div>
											</div>
										</div>
										<p><?=GetMessage("AJAX_FORM_PHOTO")?></p>

						<div id="photo_block">
							<?php
							$Photos = array();
							$j = 1;
							for ($i = 1; $i <= 10; $i++) {
								//deb($props["PHOTONAME_COLOR_".$i]["VALUE"]);
								//deb($props["PHOTO_COLOR_".$i]["VALUE"]);
								if (!empty($props["PHOTO_COLOR_".$i]["VALUE"])) {

									foreach ($props["PHOTO_COLOR_".$i]["VALUE"] as $photo) {
										if (!empty($photo))	 {
											$src = CFile::GetPath($photo);

											//$Photos[$photo]["elem_id"] = $data["ID"];
											//$Photos[$photo]["elem_name"] = $data["NAME"];
											$Photos[$photo]["elem_pic"] = $src;
											//$Photos[$photo]["action_flag"] = "";
											$Photos[$photo]["elem_color"] = $props["PHOTONAME_COLOR_".$i]["VALUE"];
											//$Photos[$photo]["elem_sample"] = "";
									?>
									<div class="photo_block">
										<div class="photo_l">
											<div class="fl_photo">
												<img width="101" height="135" src="<?=$src?>">
											</div>
											<div class="bot_sel">
												<div class="control-group">

													<label class="lab-left"><?=GetMessage("AJAX_FORM_CHOOSE_COLOR")?> <span class="arrow-required">*</span></label>
													<div class="controls span">
														<select class="selectpickerP show-tick" data-photo-id="<?=$photo?>">
															<option value="0"><?=GetMessage("AJAX_FORM_CHOOSE_COLOR")?></option>
															<?php
															$j++;
															foreach ($arResult["COLORS"] as $id => $color) {


																$sel = "";
																if (!empty($props["PHOTONAME_COLOR_".$i]["VALUE"]) && $props["PHOTONAME_COLOR_".$i]["VALUE"] == $id)  {
																	$sel = 'selected';
																	?><option <?=$sel?> value="<?=$id?>"><?=$color["NAME"]?></option><?php
																}

															}
															?>

														</select>
													</div>
												</div>
											</div>
											<div class="bot_sel tooltip-demo">
												<span class="span"><a data-photo-id="<?=$photo?>" class="btn btn-danger delPhoto" href="#" rel="tooltip" data-placement="top" data-original-title="Удалить"><i class="icon-remove icon-white"></i></a></span>
											</div>
										</div>
									</div>
									<?
										}
									}
								}
								//echo $i;
							}
							?>
						</div>
					</div>
				</div>

				<button class="btn btn-success save-btn form-actions" id="saveProductBtn"  type="submit"><?=GetMessage("AJAX_FORM_SAVE")?></button>
			</fieldset>
			 <input type="hidden" name="photo_object" id="photo_object" value="">
			</form>
		</div>
	</div>
</div>
<?php

$buffer = ob_get_contents();
ob_end_clean();

$result = array();
$result['result'] = 'OK';
$result['html'] = $buffer;
$result['material'] = $currentMaterial;
$result['brand'] = $currentBrand;
// вставляем в начало
array_unshift($arResult["COLORS"], array("ID"=>0, "NAME"=>GetMessage("AJAX_FORM_CHOOSE_COLOR")));
$result['colors'] = $arResult["COLORS"];
$result['photos'] = $Photos;


if ($siteUTF8 == false) {
	foreach ($result as $key => $item) {
		
		if (is_string($result[$key])) {
			$result[$key] = iconv('windows-1251', 'UTF-8', $result[$key]);
		} elseif (is_array($result[$key])) {
			
			foreach ($result[$key] as $k => $v) {
				if (is_string($v)) {
					$result[$key][$k] = iconv('windows-1251', 'UTF-8', $v);
				} elseif (is_array($v))  {
					//deb($result[$key]);
					
					foreach ($v as $k2 => $v2) {
						if (is_string($v2)) {
							$result[$key][$k][$k2] = iconv('windows-1251', 'UTF-8', $v2);
						} elseif (is_array($v2)) {
							
							foreach ($v2 as $k3 => $v3) {
								if (is_string($v3)) {
									$result[$key][$k][$k2][$k3] = iconv('windows-1251', 'UTF-8', $v3);
								} else {
									
								}
							}
						}
					}				
				}
			}
		}
	}
}


$resultJson = json_encode($result);
die($resultJson);
?>