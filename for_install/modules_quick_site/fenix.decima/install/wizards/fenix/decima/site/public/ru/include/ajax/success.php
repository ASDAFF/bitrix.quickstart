<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); 
if(intval($_REQUEST['id'])>0):
__IncludeLang(__DIR__."/lang/ru/success.php");
    if (!function_exists('margin'))
    {
        function margin($height_fact, $height, $width_fact, $width){
            
            $result='';
            $result.=($height_fact<$height)?abs(($height-$height_fact)/2):'0';$result.='px ';
            $result.=($width_fact<$width)?abs($width-(($width-$width_fact)/2)-$width_fact):'0';$result.='px '; 
            $result.=($height_fact<$height)?abs(($height-$height_fact)/2):'0';$result.='px ';
            $result.=($width_fact<$width)?abs(($width-$width_fact)/2):'0';$result.='px; ';
            if($width_fact>$height_fact)$result.='width:'.$width.'px; height:'.$height.'px;';
            elseif($height_fact>$width_fact)$result.='width:'.$width.'px; height:'.$height.'px;';
            /*style="margin:<?=($height_fact<$arParams['DISPLAY_IMG_HEIGHT'])?abs(($arParams['DISPLAY_IMG_HEIGHT']-$arItem["PICTURE"]["HEIGHT"])/2):'0'?>px <?=abs($arParams['DISPLAY_IMG_WIDTH']-(($arParams['DISPLAY_IMG_WIDTH']-$arItem["PICTURE"]["WIDTH"])/2)-$arItem["PICTURE"]["WIDTH"])?>px  <?=($arItem["PICTURE"]["HEIGHT"]<$arParams['DISPLAY_IMG_HEIGHT'])?abs(($arParams['DISPLAY_IMG_HEIGHT']-$arItem["PICTURE"]["HEIGHT"])/2):'0'?>px <?=($arItem["PICTURE"]["WIDTH"]<$arParams['DISPLAY_IMG_WIDTH'])?abs(($arParams['DISPLAY_IMG_WIDTH']-$arItem["PICTURE"]["WIDTH"])/2):'0'?>px; <?if($arItem["PICTURE"]["WIDTH"]>$arItem["PICTURE"]["HEIGHT"] && $arItem["PICTURE"]["WIDTH"]>$arParams['DISPLAY_IMG_WIDTH'])echo 'width="'.$arParams['DISPLAY_IMG_WIDTH'].'"';elseif($arItem["PICTURE"]["HEIGHT"]>$arItem["PICTURE"]["WIDTH"] && $arItem["PICTURE"]["HEIGHT"]>$arParams['DISPLAY_IMG_HEIGHT'])echo 'height="'.$arParams['DISPLAY_IMG_HEIGHT'].'"'?>"
            */
            return $result;
        }
    }
  $id=$_REQUEST['id'];   
  $siteid=$_REQUEST['site']?$_REQUEST['site']:SITE_ID;
  $arSite = CSite::GetByID($siteid)->Fetch();
  CModule::includeModule('iblock');  CModule::includeModule('catalog');   CModule::includeModule('sale');
  $dbBasketItems = CSaleBasket::GetList(array(),array("FUSER_ID" => CSaleBasket::GetBasketUserID(),"LID" => $siteid,"PRODUCT_ID" => $id,"ORDER_ID" => "NULL"),false,false,array());
  if($arItem = $dbBasketItems->Fetch()):
  $db_res = CSaleBasket::GetPropsList(array("SORT" => "ASC","NAME" => "ASC"),array("BASKET_ID" => $arItem['ID']));
  while ($ar_res = $db_res->Fetch())if($ar_res['CODE']!='CATALOG.XML_ID' && $ar_res['CODE']!='PRODUCT.XML_ID')$arItems['PROPS'][$ar_res['ID']]=$ar_res;
  $res = CIBlockElement::GetList(Array(), Array("ID"=>IntVal($arItem['PRODUCT_ID'])), false, false, Array('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO'));
    if($ob = $res->GetNext()){
        $img=$ob['PREVIEW_PICTURE']?$ob['PREVIEW_PICTURE']:($ob['DETAIL_PICTURE'])?$ob['DETAIL_PICTURE']:$ob['PROPERTY_MORE_PHOTO'][0];
        if(!$img){
            $mxResult = CCatalogSku::GetProductInfo($arItem['PRODUCT_ID']);
            if (is_array($mxResult))
            {   
                $parentres = CIBlockElement::GetList(Array(), Array("ID"=>IntVal($mxResult['ID'])), false, false, Array('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO'));
                if($obparent = $parentres->GetNext()){
                    $img=$obparent['PREVIEW_PICTURE']?$obparent['PREVIEW_PICTURE']:($obparent['DETAIL_PICTURE'])?$obparent['DETAIL_PICTURE']:$obparent['PROPERTY_MORE_PHOTO'][0];
                }
            }
        }
        if($img)$file = CFile::ResizeImageGet($img, array('width'=>190, 'height'=>190), BX_RESIZE_IMAGE_PROPORTIONAL, true); 
        $arItem['PICTURE']=Array('ID'=>$img, 'SRC'=>$file['src'], 'HEIGHT'=>$file['height'], 'WIDTH'=>$file['width']);    
    }
?>

<div class="inner">
<div class="popup-container" id="popup-form-inner" style="width:600px">
    <div class="popup-title">
        <div class="popup-title-text"><?=GetMessage('BASKET_ADD')?></div>
        <div class="popup-close close-w"><?=GetMessage('BASKET_CLOSE')?></div>
    </div>

    <div style="border-bottom-right-radius: 12px;border-bottom-left-radius: 12px;">
        <div class="inbasket_info_wrap">
            <div class="inbasket_pic_wrap"><img class="inbasket_pic"  style="padding:<?=margin($arItem['PICTURE']['HEIGHT'], 190, $arItem["PICTURE"]["WIDTH"], 190, false)?>" src="<?=$arItem['PICTURE']['SRC']?>"></div>
            <div class="inbasket_name_wrap">
                <a class="inbasket_name" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                    <?=$arItem['NAME']?> 
                </a>
                <?if($arItem['PROPS']):?>
                <div class="inbasket_info">
                <?foreach($arItem['PROPS'] as $arProp):?>  
                    <p><?=$arProp['NAME']?>: <?=$arProp['VALUE']?></p> 
                    <?endforeach?>                    
                <?endif?>                 
                    <p class="inbasket_price"><?=SaleFormatCurrency($arItem["PRICE"], $arItem["CURRENCY"])?></p>
                </div>
            </div>
        </div>
        <div style="clear: both;  padding: 9px 0;">
            <a href="#" id="continue" class="inbasket_cont_buy popup-close close-w"><span class="pseudo"><?=GetMessage('BASKET_CONTINUE')?></span></a>

            <a href="<?=$arSite['DIR']?>personal/cart/" class="btn btn-danger btn-block inbasket_order"><?=GetMessage('BASKET_ORDER')?></a>
        </div>
        <div>
            
            <?$APPLICATION->IncludeComponent("bitrix:sale.recommended.products", ".default", array(
            "ID" => $_REQUEST['id'],
            "MIN_BUYES" => "1",
            "HIDE_NOT_AVAILABLE" => "N",
            "PAGE_ELEMENT_COUNT" => "3",
            "CACHE_TYPE" => "N",
            "CACHE_TIME" => "86400",
            "PRICE_CODE" => array(
                0 => "BASE",
            ),
            "SHOW_PRICE_COUNT" => "1",
            "PRICE_VAT_INCLUDE" => "Y",
            "ACTION_VARIABLE" => "nonaction",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRODUCT_QUANTITY_VARIABLE" => "nonquantity",
            "ADD_PROPERTIES_TO_BASKET" => "N",
            "PRODUCT_PROPS_VARIABLE" => "nonprop",
            "PARTIAL_PRODUCT_PROPERTIES" => "N",
            "USE_PRODUCT_QUANTITY" => "N",
            "SHOW_PRODUCTS_2" => "Y",
            "DISPLAY_IMG_WIDTH" => "180",
            "DISPLAY_IMG_HEIGHT" => "150",
            "DISPLAY_IMG_PROP" => "Y"
            ),
            false
        );?>
        </div>


    </div>
</div>
</div>
<div class="overlay close-w"></div>
<?endif?>
<?endif?>