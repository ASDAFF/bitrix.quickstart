<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var CBitrixComponentTemplate $this
 */
$this->setFrameMode(true);
?>
<!--start_html-->
<div class="col3-list stuff-box" id="elements">

	<div class="product-count-bottom">
		<span>
<?
	$str = $arResult['NavRecordCount'];
	if (substr( $str, strlen($str)-1, 1 ) == 1)
		echo GetMessage("FOUND_LABEL1");
	else
		echo GetMessage("FOUND_LABEL");
?> <?=$arResult['NavRecordCount'];?> <?=pluralForm($arResult['NavRecordCount'], GetMessage("IMAGERY_ONE"), GetMessage("IMAGERY_MANY"), GetMessage("IMAGERY_OF"))?></span>
<?
/*
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
*/
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
			$detPicPath = "/local/templates/demoshop/images/nophoto.png";
			$prePicPath1 = "/local/templates/demoshop/images/nophoto.png";
			$prePicPath2 = "/local/templates/demoshop/images/nophoto.png";
			$prePicPath3 = "/local/templates/demoshop/images/nophoto.png";
			if( isset($val['PROPERTY_PHOTOS_VALUE'][0]) )
				$detPicPath = $val['PROPERTY_PHOTOS_VALUE'][0];
			if( isset($val['PROPERTY_PHOTOS_VALUE'][1]) )
				$prePicPath1 = $val['PROPERTY_PHOTOS_VALUE'][1];
			if( isset($val['PROPERTY_PHOTOS_VALUE'][2]) )
				$prePicPath2 = $val['PROPERTY_PHOTOS_VALUE'][2];
			if( isset($val['PROPERTY_PHOTOS_VALUE'][3]) )
				$prePicPath3 = $val['PROPERTY_PHOTOS_VALUE'][3];
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
<?
	foreach($val['PROPERTY_PHOTOS_VALUE'] as $subval)
	{
$APPLICATION->IncludeComponent(
	"novagroup:ajaximgload",
	"",
	Array(
		"CALL_FROM_CATALOG"		=> "Y",
		"ATTRIBUTES"	=> array(
			"width"		=> 177,
			"height"	=> 240
		),
		"MICRODATA"		=> array(
			"imgid"	=> $subval
		),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "2592000",
	),
	false,
	Array(
		'ACTIVE_COMPONENT' => 'Y',
		//"HIDE_ICONS"=>"Y"
	)
);
		break;
	}
?>
								</a>
								<div class="name">
									<a class="detail-card" href="<?=$detail;?>"><?=$val['NAME'];?></a>
								</div>
								<div class="price">
                                    <?
                                    if ($arResult['OPT_USER'] == 1) {
                                        $priceID = $arParams['OPT_PRICE_ID'];
                                    } else {
                                        $priceID = false;
                                    }
                                    $fashion = new Novagroup_Classes_General_Fashion($arParams['FASHION_IBLOCK_ID'], $priceID);

                                    $prices = $fashion->getPriceByElement($val['ID']);
                                    if (isset($prices['OLD_PRICE'])) {
                                        ?>
                                        <div class="actual discount">
                                            <a class="detail-card" href="<?= $detail; ?>" rel="nofollow">
                                                <?= $prices['FROM'] . $prices['PRINT_PRICE']; ?>
                                            </a>
                                        </div>
                                        <div class="actual old-price">
                                            <a class="detail-card" href="<?= $detail; ?>" rel="nofollow">
                                                <?= $prices['FROM'] . $prices['PRINT_OLD_PRICE']; ?>
                                            </a>
                                        </div>
                                    <?
                                    } elseif ($prices['PRICE']>0) {
                                        ?>
                                        <div class="actual">
                                            <a class="detail-card" href="<?=$detail?>" rel="nofollow">
                                                <?= $prices['FROM'] . $prices['PRINT_PRICE']; ?>
                                            </a>
                                        </div>
                                    <?
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
	<span><?=GetMessage("FOUND_LABEL")?> <?=$arResult['NavRecordCount'];?> <?=pluralForm($arResult['NavRecordCount'], GetMessage("IMAGERY_ONE"), GetMessage("IMAGERY_MANY"), GetMessage("IMAGERY_OF"))?></span>
<?
/*
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
*/
?>
	</div>
<div class="bottom-p">
	<?=$arResult['NAV_STRING'];?>
	</div>
</div>
<!--end_html-->
<??>