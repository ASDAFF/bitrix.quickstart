<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); IncludeTemplateLangFile(__FILE__);?>
<!--start_html-->
<div class="col3-list stuff-box" id="elements">

	<div class="product-count-bottom">
		<span><?
	$str = $arResult['NavRecordCount'];
	if (substr( $str, strlen($str)-1, 1 ) == 1)
		echo GetMessage("FOUND_LABEL1");
	else
		echo GetMessage("FOUND_LABEL");

?> <?=$arResult['NavRecordCount'];?> <?=Novagroup_Classes_General_Main::pluralForm($arResult['NavRecordCount'], GetMessage("IMAGERY_ONE"), GetMessage("IMAGERY_MANY"), GetMessage("IMAGERY_OF"))?></span>
<? 
	if( $arResult['NavRecordCount'] > 16)
	{
?>
<a
			class="npagesize <? if($arParams['nPageSize'] == 16){echo'active';}else{echo'incative';}?>"
			value="16"><?=GetMessage("FORMAT_LABEL")?> 16</a><a
			class="npagesize <? if($arParams['nPageSize'] == 160){echo'active';}else{echo'incative';}?>"
			value="160"><?=GetMessage("FORMAT_LABEL")?> 160</a>
<?
	}
?>
	</div>


	<div class="clear"></div>

	<div class="list">
		<div class="line">
			<div class="item_number">	
<?
	if ( isset($arResult['ELEMENT']) )
	{
		$row = 1;
		foreach($arResult['ELEMENT'] as $val)
		{
			$arButtons = CIBlock::GetPanelButtons(
					$arParams['FASHION_IBLOCK_ID'],
					$val["ID"],
					0,
					array("SECTION_BUTTONS"=>false, "SESSID"=>false)
			);
			$val["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
				
			$this->AddEditAction($val["ID"], $val["EDIT_LINK"], CIBlock::GetArrayByID($arParams['FASHION_IBLOCK_ID'], "ELEMENT_EDIT"));
			
			$detail = $arParams['FASHION_ROOT_PATH'].$val['CODE']."/";

			if ($row == 1)
			{
			?>
				<div class="item-block">
			<?
			}
			$idElem = $this->GetEditAreaId($val['ID']);
			?>
					<div class="item" id="<?=$idElem?>"
						data-catalog-id="<?=$val['ID']?>"
						data-iblock-id="<?=$arParams["FASHION_IBLOCK_ID"]?>">
						<div class="over">
							<div class="preview">
								<a class="detail-card" href="<?=$detail;?>" rel="nofollow">
                                    <img src="<?$APPLICATION->IncludeComponent(
                                        "novagroup:fashion.element.photo",
                                        "common",
                                        Array(
                                            "CATALOG_IBLOCK_ID" => $val['IBLOCK_ID'],
                                            "CATALOG_ELEMENT_ID" => $val['ID'],
                                            "PHOTO_WIDTH" => "177",
                                            "PHOTO_HEIGHT" => "236"
                                        ),
                                        false,
                                        Array(
                                            'ACTIVE_COMPONENT' => 'Y',
                                            "HIDE_ICONS" => "Y"
                                        )
                                    );?>" />
                                </a>
								<div class="name">
									<a class="detail-card" href="<?=$detail;?>"><?=$val['NAME'];?></a>
								</div>
								<div class="price">
			
<?
				$priceOld = floatval($val["TOTAL"]);
				$priceNew = floatval($val["DISCN"]);
				if ($priceNew < $priceOld)
				{
?>									
									<div class="actual discount">
										<a class="detail-card" href="<?=$detail;?>" rel="nofollow">
											<?=number_format($priceNew, 2, ".", " ");?>
											<span class="rubles"><?=$arResult['ELEMENT'][ $val['ID'] ]['CURRENCY_DISPLAY']?>.</span>
										</a>
									</div>
									<div class="actual old-price">
										<a class="detail-card" href="<?=$detail;?>" rel="nofollow">
											<?=number_format($priceOld, 2, ".", " ");?>
											<span class="rubles"><?=$arResult['ELEMENT'][ $val['ID'] ]['CURRENCY_DISPLAY']?>.</span>
										</a>
									</div>
<?
		} else {
			if ($val ['TOTAL'] > 0) {
				?>
									<div class="actual">
										<a class="detail-card" href="<?=$detail;?>" rel="nofollow">
											<?=number_format($val['TOTAL'], 2, ".", " ");?>
											<span class="rubles"><?=$arResult['ELEMENT'][ $val['ID'] ]['CURRENCY_DISPLAY']?>.</span>
										</a>
									</div>
<?
			}
		}
?>					
								</div>
							</div>
						</div>
					</div>	
<?
			if($row == 4)
			{
?>				
				</div>
<?
				$row = 1;
			}else $row++;
		}
		if($row <= 4){
?>
				</div>
<?	
		}
	}
?>
				<div class="clear"></div>

		</div>
	</div>
</div>

<div class="product-count-bottom bot-p">
	<span><?=GetMessage("FOUND_LABEL")?> <?=$arResult['NavRecordCount'];?> <?=Novagroup_Classes_General_Main::pluralForm($arResult['NavRecordCount'], GetMessage("IMAGERY_ONE"), GetMessage("IMAGERY_MANY"), GetMessage("IMAGERY_OF"))?></span>
<?
	if( $arResult['NavRecordCount'] > 16)
	{
?>
<a
		class="npagesize <? if($arParams['nPageSize'] == 16){echo'active';}else{echo'incative';}?>"
		value="16"><?=GetMessage("FORMAT_LABEL")?> 16</a><a
		class="npagesize <? if($arParams['nPageSize'] == 160){echo'active';}else{echo'incative';}?>"
		value="160"><?=GetMessage("FORMAT_LABEL")?> 160</a>
<?
	}
?>
	</div>
<div class="bottom-p">
	<?=$arResult['NAV_STRING'];?>
	</div>
</div>
<!--end_html-->