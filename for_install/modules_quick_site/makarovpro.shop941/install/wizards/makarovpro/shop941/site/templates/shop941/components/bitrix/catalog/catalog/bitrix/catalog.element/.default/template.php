<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-element">
    <div class="yashare-auto-init" data-yashareL10n="ru"
         data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"

         ></div>
    <div class="group">
        <div class="left-block">
            <div class="picture-block">
                <div class="picture-big">
                    <?$resizebig = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array("width"=>520, "height"=>400), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
   
                        <img src="<?echo $resizebig['src']?>" width="<?echo $resize['width']?>" height="<?echo $resize['height']?>" alt="<?= $arResult["NAME"] ?>" />
                  
                </div>

 
            </div>
        </div>
        <div class="right-block">
            <h3><?=GetMessage("CATALOG_CHAR")?></h3>
            <ul class="char group">
                <?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
                <li><?= $arProperty["NAME"] ?>: <strong><?
                        if(is_array($arProperty["DISPLAY_VALUE"])):
                        echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
                        elseif($pid=="MANUAL"):
                        ?><a href="<?= $arProperty["VALUE"] ?>"><?= GetMessage("CATALOG_DOWNLOAD") ?></a><?
                        else:
                        echo $arProperty["DISPLAY_VALUE"];?></strong></li>
                <?endif?>
                <?endforeach?>


            </ul>
            <ul class="price-block group">
                <li><strong><?=GetMessage("CATALOG_PRICE")?></strong></li>
                <? if ($arResult["DISPLAY_PROPERTIES"]["OLD_PRICE"]){?><li class="old"><?=$arResult["DISPLAY_PROPERTIES"]["OLD_PRICE"]["VALUE"];?> <? if ($arResult["DISPLAY_PROPERTIES"]["ED_IZM"]){?>/<?=$arResult["DISPLAY_PROPERTIES"]["ED_IZM"]["VALUE"];?><?}?></li><?}?> 
                <li class="new">


                    <?foreach($arResult["PRICES"] as $code=>$arPrice):?>
                    <?if($arPrice["CAN_ACCESS"]):?>

                    <?if($arParams["PRICE_VAT_SHOW_VALUE"] && ($arPrice["VATRATE_VALUE"] > 0)):?>
                    <?if($arParams["PRICE_VAT_INCLUDE"]):?>
                    (<?echo GetMessage("CATALOG_PRICE_VAT")?>)
                    <?else:?>
                    (<?echo GetMessage("CATALOG_PRICE_NOVAT")?>)
                    <?endif?>
                    <?endif;?>
                    <?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
                    <?= $arPrice["PRINT_VALUE"] ?><?= $arPrice["PRINT_DISCOUNT_VALUE"] ?>
                    <?if($arParams["PRICE_VAT_SHOW_VALUE"]):?>
                    <?= GetMessage("CATALOG_VAT") ?><?= $arPrice["DISCOUNT_VATRATE_VALUE"] > 0 ? $arPrice["PRINT_DISCOUNT_VATRATE_VALUE"] : GetMessage("CATALOG_NO_VAT") ?>
                    <?endif;?>
                    <?else:?>
                    <?= $arPrice["PRINT_VALUE"] ?>
                    <?if($arParams["PRICE_VAT_SHOW_VALUE"]):?>
                    <?= GetMessage("CATALOG_VAT") ?><?= $arPrice["VATRATE_VALUE"] > 0 ? $arPrice["PRINT_VATRATE_VALUE"] : GetMessage("CATALOG_NO_VAT") ?>
                    <?endif;?>
                    <?endif?>

                    <?endif;?>
                    <?endforeach;?><? if ($arResult["DISPLAY_PROPERTIES"]["ED_IZM"]){?>/<?=$arResult["DISPLAY_PROPERTIES"]["ED_IZM"]["VALUE"];?><?}?>

                    

                </li>
                <li class="bay"><a href="<?echo $arResult["ADD_URL"]?>" rel="nofollow"><img src="<?= SITE_TEMPLATE_PATH ?>/images/cart.png" width="16" height="16" alt="cart"/><?= GetMessage("CATALOG_ADD_TO_BASKET") ?></a></li>
                
            </ul>
        </div>
    </div>

    
        <br>
		<br>
            <h2><?=GetMessage("CATALOG_TABS_DESCR")?></h2>
			<br>
            <article>
                <?= $arResult["DETAIL_TEXT"] ?>
            </article>
        

   
</div>


<?if(is_array($arResult["SECTION"])):?>
<br /><a href="<?= $arResult["SECTION"]["SECTION_PAGE_URL"] ?>"><?= GetMessage("CATALOG_BACK") ?></a>
<?endif?>
