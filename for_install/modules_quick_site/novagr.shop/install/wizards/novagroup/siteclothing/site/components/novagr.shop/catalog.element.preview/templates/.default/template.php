<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var CBitrixComponentTemplate $this
 */
$this->setFrameMode(true);
?>
<?php
    if (count($arResult['ELEMENTS'])) {
        $val = array_pop($arResult['ELEMENTS']);
        //add size params
        $PROPERTY_STD_SIZE = array();
        if(is_array($arParams['arOfferRequest']))
        {
            foreach($arParams['arOfferRequest'] as $rOffer)
            {
                if (isset($rOffer['PROPERTY_STD_SIZE']))
                {
                    $PROPERTY_STD_SIZE[] = (int)$rOffer['PROPERTY_STD_SIZE'];
                }
            }
        }
        $detail = $val['DETAIL_PAGE_URL'];
        $parseDetailPage = parse_url($detail);
        $path = ($parseDetailPage['path']) ? $parseDetailPage['path'] : '';
        $query = ($parseDetailPage['query']) ? $parseDetailPage['query'] : '';
        if (count($PROPERTY_STD_SIZE) >0 ) {
            $detail = $path.'?cs='.implode('-',$PROPERTY_STD_SIZE);
        } else {
            $detail = $path;
        }
		//$detail = $path.'?cs='.implode('-',$PROPERTY_STD_SIZE).'&'.$query;

    ?>                      

    <div class="preview">

        <div class="min-catal-img">
            <div class="card-lider-m new-min">
                <a class="labelLink" href="<?=$val['DETAIL_PAGE_URL'];?>"><?$APPLICATION->IncludeComponent(
                    "novagroup:catalog.timetobuy",
                    "label",
                    Array(
                        "IBLOCK_ID"=>$val['IBLOCK_ID'],
                        "ID"=>$val['ID']
                    )
                );?></a>
                <?
                    if( !empty($val['PROPERTIES']['SPECIALOFFER']['VALUE']) )
                        echo'<a class="labelLink" href="'.$val['DETAIL_PAGE_URL'].'"><div class="card-spec-min"></div></a>';
                    if( !empty($val['PROPERTIES']['NEWPRODUCT']['VALUE']) )
                        echo'<a class="labelLink" href="'.$val['DETAIL_PAGE_URL'].'"><div class="card-new-min"></div></a>';
                    if( !empty($val['PROPERTIES']['SALELEADER']['VALUE']) )
                        echo'<a class="labelLink" href="'.$val['DETAIL_PAGE_URL'].'"><div class="card-lider-min"></div></a>';
                ?>
            </div>    
            <?php 
				$CURRENT_ELEMENT_COLORS = (is_array($arResult['CURRENT_ELEMENT']["COLORS"]) && count($arResult['CURRENT_ELEMENT']["COLORS"])>0) ? $arResult['CURRENT_ELEMENT']["COLORS"] : array( 0 => array ( 'ID' => 0 ) );
               
                foreach ($CURRENT_ELEMENT_COLORS as $key => $color) {
                    $style = ($key == 0 ) ? '' : 'display:none;';

                    //$detailLink = $detail ."#color-".$color['ID'] . "-" . $val['ID'];
                    $detailLink = $detail;
                    ?>

					<a style="<?=$style?>" href="<?=$detailLink?>" class="detail-card catalog-preview-color-element-<?=$val['ID']?> catalog-preview-color-element-<?=$val['ID']?>-<?=$color['ID']?>">

<? $html = $APPLICATION->IncludeComponent(
                                "novagroup:catalog.element.photo",
                                "common",
                                Array(
                                    "CATALOG_IBLOCK_ID" => $val['IBLOCK_ID'],
                                    "CATALOG_ELEMENT_ID" => $val['ID'],
                                    "PHOTO_ID" =>  $color['ID'] ,
                                    "PHOTO_WIDTH" => "177",
                                    "PHOTO_HEIGHT" => "236",
									"I_FROM_CATALOG" => "Y"
                                ),
                                false,
                                Array(
                                    'ACTIVE_COMPONENT' => 'Y',
                                    //"HIDE_ICONS"=>"Y"
                                )
);
$rsSeoData = new \Bitrix\Iblock\InheritedProperty\ElementValues($val["IBLOCK_ID"], $val['ID']);
$arSeoData = $rsSeoData->getValues();

?><img src='<?=strip_tags($html);?>' title="<?=$arSeoData['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'];?>" alt="<?=$arSeoData['ELEMENT_PREVIEW_PICTURE_FILE_ALT'];?>" width="177" height="236">
                </a>
                <?php if ($arParams['DISABLE_QUICK_VIEW'] !== 'Y'): ?>
                <span style="cursor:pointer;<?=$style?>" class="link-popover-card catalog-preview-color-element-<?=$val['ID']?> catalog-preview-color-element-<?=$val['ID']?>-<?=$color['ID']?>">
                    <a style="text-decoration: none;" href="<?=$detail?>#color-<?=$color['ID']?>-<?=$val['ID']?>" data-name="<?=$val['ID']?>" class="quickViewLink"><?=GetMessage('QUICK_VIEW')?></a>
                </span>
                <?php endif;
                }
            ?>
        </div>

        <?php 
            foreach ($CURRENT_ELEMENT_COLORS as $key => $color) {
                $style = ($key == 0 ) ? '' : 'display:none;';
            ?>
            <div style="<?=$style?>" class="name catalog-preview-color-element-<?=$val['ID']?> catalog-preview-color-element-<?=$val['ID']?>-<?=$color['ID']?>"><a class="detail-card" href="<?=$detailLink?>"><?=$val['NAME']?></a></div>
            <?php  
            }
        $SIZE_BY_COLOR_CSS_CLASS = array();
        if(is_array($arResult["OFFERS"]))
        {
			foreach($arResult["OFFERS"] as $offer){
				//echo (int)$offer['PROPERTY_STD_SIZE_ID']."@";
				//$csID = ($key == 0 ) ? ' active-color' : '';
                $SIZE_BY_COLOR_CSS_CLASS[(int)$offer['PROPERTY_COLOR_ID']][] = 'button-size-button-'.(int)$offer['PROPERTY_STD_SIZE_ID'];
            }
        }
        ?>
        <div class="color-catalog">
            <?php 
                if (is_array($arResult['CURRENT_ELEMENT']["COLORS"]))
                    foreach ($arResult['CURRENT_ELEMENT']["COLORS"] as $key => $color) {
                        $addClass = ($key == 0 ) ? ' active-color' : '';

                        $SIZE_COLOR_CSS_CLASS = array();
                        if(is_array($arResult["OFFERS"]))
                        {
                            foreach($arResult["OFFERS"] as $offer){
                                if((int)$offer['PROPERTY_COLOR_ID'] == $color['ID'])
                                {
                                    $SIZE_COLOR_CSS_CLASS[$color['ID']][] = 'button-color'.(int)$offer['PROPERTY_COLOR_ID'].'-size'.(int)$offer['PROPERTY_STD_SIZE_ID'];
                                }

                            }
                        }
                    ?>
<?
		$html = $APPLICATION->IncludeComponent(
                        "novagroup:catalog.element.photo",
                        "path_177_236",
                        Array(
                            "CATALOG_IBLOCK_ID" => $val['IBLOCK_ID'],
                            "CATALOG_ELEMENT_ID" => $val['ID'],
                            "PHOTO_ID" => $color['ID'],
                            "PHOTO_WIDTH" => "177",
                            "PHOTO_HEIGHT" => "236",
							"I_FROM_CATALOG" => "Y"
                        ),
                        false,
                        Array(
                            'ACTIVE_COMPONENT' => 'Y',
                            //"HIDE_ICONS"=>"Y"
                        )
                    );
?>
                    <span data-pic='<?=$html;?>' data-color-id="<?=$val['ID']?>" data-color-code="<?=$color['ID']?>" data-name="data-color-button-<?=$val['ID']?>-<?=$color['ID']?>" class="<? if(isset($SIZE_BY_COLOR_CSS_CLASS[$color['ID']])) echo implode(' ',$SIZE_BY_COLOR_CSS_CLASS[$color['ID']]); ?> button-color-button-<?=$color['ID']?> color-min<?=$addClass?>"><span class="b-C"><span><img src="<?=CFile::GetPath($color['PREVIEW_PICTURE'])?>"  width="12" height="10" alt="<?=htmlspecialchars($color["NAME"],null,SITE_CHARSET)?>" ></span></span></span>
                    <?php
                }
            ?>
        </div>
        <?php
        if (is_array($arResult['CURRENT_ELEMENT']["COLORS"]))
        foreach ($arResult['CURRENT_ELEMENT']["COLORS"] as $key => $color) {
        $price =  $arResult['OBJECT_PRICE']->getPriceByColor($color['ID']);
        if (isset($price['PRINT_OLD_PRICE'])) {
            ?>
            <div style="<?=($key>0)?$style:""?>" class="price catalog-preview-color-element-<?=$val['ID']?> catalog-preview-color-element-<?=$val['ID']?>-<?=$color['ID']?>">
                <div class="actual discount"><a href="<?= $detail ?>"><?= $price['FROM'] . $price['PRINT_PRICE']; ?></a></div>
                <div class="actual old-price"><a href="<?= $detail ?>"><?= $price['FROM'] .$price['PRINT_OLD_PRICE']; ?></a></div>
            </div>
        <?php
        } else {
            ?>
            <div style="<?=($key>0)?$style:""?>" class="price catalog-preview-color-element-<?=$val['ID']?> catalog-preview-color-element-<?=$val['ID']?>-<?=$color['ID']?>">
                <div class="actual default-value"><a href="<?= $detail ?>"><?= $price['FROM'] .$price['PRINT_PRICE']; ?></a></div>

            </div>
        <?php
        }
        }
		if ($arResult["PRODUCT_IN_STOCK"] == 0 && $arParams["SHOW_SUBSCRIBED"] == "Y") {    
        ?>
        	<div class="bottom-balloon">
        		<h6><?=GetMessage('OUT_OF_STOCK')?></h6>
        		<button id="btn_<?=$arResult["OFFERS"][0]['ID']?>" class="btn btn-danger notify" data-elem-id="<?=$arResult["OFFERS"][0]['ID']?>"><?=GetMessage('NOTIFY_WHEN_AVAILABLE')?></button>
			</div>
		<?php 
        }
		?>
        <?$APPLICATION->IncludeComponent(
            "novagroup:catalog.timetobuy",
            "preview",
            Array(
                "IBLOCK_ID"=>$val['IBLOCK_ID'],
                "ID"=>$val['ID']
            )
        );?>
    </div>
    <?php
    }
?>