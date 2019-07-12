<?php
use \Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

$this->setFrameMode(false);
?>
<div class="row constructor-wrapper" id="set_constructor">
  <div class="section-cart col-md-12">
    <h2 class="product-content__title"><?=Loc::getMessage("SET_CONSTRUCTOR_TITLE")?></h2>
  </div>  
  <div class = " col-md-12 set-section">
  
    <div class = "set-constructor js-constructor"
      data-iblockid="<?=$arParams['IBLOCK_ID']?>"
      data-ajaxpath="<?=$this->GetFolder();?>/ajax.php"
      data-lid="<?=SITE_ID?>" 
      data-setOffersCartProps = "<?=CUtil::PhpToJSObject($arParams["OFFERS_CART_PROPERTIES"])?>" 
      data-currency = "<?=$arResult['ELEMENT']['PRICE_CURRENCY']?>" 
    >
      <div class="row">
        <div class = "col col-md-12">
          <div 
            class = "selected-items owlslider set"
                data-margin = "24"
                data-nav = "true"
                data-loop = "false"
                data-responsive = '{"0":{"items":"2"},"768":{"items":"3"}, "956":{"items":"5"}}'
          >
            <? $arItem = $arResult["ELEMENT"]; ?>
            <div class = "set-item item" 
              data-elementid="<?=$arItem['ID']?>"
              data-price = "<?=$arItem['PRICE_DISCOUNT_VALUE']?>"
              data-oldprice = "<?=$arItem['PRICE_VALUE']?>"
              data-discount = "<?=$arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE']?>
            ">
              <div class = "set-pic">
                <a href = "<?=$arItem['DETAIL_PAGE_URL']?>">
                  <img src = "<?=$arItem["DETAIL_PICTURE"]["src"] ? $arItem["DETAIL_PICTURE"]["src"] : $arResult['NO_PHOTO']['src']?>" alt = "<?=$arItem["NAME"]?>">
                </a>
              </div>
              <div class = "set-data">
                <div class = "set-name">
                  <a class = "aprimary" href = "<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem["NAME"]?></a>
                </div>
                <div class="hidden-xs set__category separator"></div>
                <div class = "set-prices">
                  <? if($arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE'] > 0): ?>
                    <div class = "set-price__old">
                      <?=$arItem['PRICE_PRINT_VALUE']?>
                    </div>
                    <div class = "set-price__cool">
                      <?=$arItem['PRICE_PRINT_DISCOUNT_VALUE']?>
                    </div>
                  <? else: ?>
                    <div class = "price cool">
                      <?=$arItem['PRICE_PRINT_VALUE']?>
                    </div>
                  <? endif; ?>
                </div>  
              </div>
              <div class="clearfix"></div>
            </div>
            <? foreach ($arResult["SET_ITEMS"]["DEFAULT"] as $index => $arItem): ?>
              <div class = "set-item item" 
                data-elementid="<?=$arItem['ID']?>"
                data-price = "<?=$arItem['PRICE_DISCOUNT_VALUE']?>"
                data-oldprice = "<?=$arItem['PRICE_VALUE']?>"
                data-discount = "<?=$arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE']?>
              ">
                <div class = "remove"><i class="fa fa-close"></i></div>
                <div class = "set-pic">
                  <a href = "<?=$arItem['DETAIL_PAGE_URL']?>">
                    <img src = "<?=!empty($arItem["DETAIL_PICTURE"]["src"]) ? $arItem["DETAIL_PICTURE"]["src"]:$arResult["NO_PHOTO"]["src"]?>" alt = "<?=$arItem["NAME"]?>">
                  </a>
                </div>
                <div class = "set-data">
                  <div class = "set-name">
                    <a class = "aprimary" href = "<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem["NAME"]?></a>
                  </div>
                  <div class="hidden-xs set__category separator"></div>
                  <div class = "set-prices">
                    <? if($arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE'] > 0): ?>
                      <div class = "set-price__new-cool">
                        <?=$arItem['PRICE_PRINT_DISCOUNT_VALUE']?>
                      </div>
                      <div class = "set-price__old">
                        <?=$arItem['PRICE_PRINT_VALUE']?>
                      </div>
                    <? else: ?>
                      <div class = "set-price__cool">
                        <?=$arItem['PRICE_PRINT_VALUE']?>
                      </div>
                    <? endif; ?>
                  </div>  
                </div>
                <div class="clearfix"></div>
                <div class = "separator plus"></div>
              </div>  
            <? endforeach; ?>
            <div class = "set-item item item-right" 
                data-elementid="<?=$arItem['ID']?>"
                data-price = "<?=$arItem['PRICE_DISCOUNT_VALUE']?>"
                data-oldprice = "<?=$arItem['PRICE_VALUE']?>"
                data-discount = "<?=$arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE']?>
              ">
            <div class="panel">
            <div class="panel-body set-panel">
              <? if($arResult['SET_ITEMS']['OLD_PRICE'] != 0): ?>
                <div class="contact-price">
                  <?=Loc::getMessage('CONTRACT_PRICE');?>:
                </div> 
                <div class = "set-panel-price__old"><?=$arResult['SET_ITEMS']['OLD_PRICE']; ?></div>
                <div class = "set-panel-price__cool"><?=$arResult['SET_ITEMS']['PRICE']; ?></div>
              <? endif; ?>
              <div class = "text-danger">
                <?=Loc::getMessage('YOUR_PROFIT');?>: 
              </div>
              <div class = "set-price__discount"><?=$arResult['SET_ITEMS']['PRICE_DISCOUNT_DIFFERENCE']?></div>
            </div>
            <a class = "btn btn-primary set_add2basket btn2"><?=Loc::getMessage('IN_BASKET')?></a>
            <a class = "btn btn-default JS-Popup-Ajax setbuy1click btn-button" 
              href = "<?=SITE_DIR?>forms/buy1click/" 
              title="<?=Loc::getMessage('BUY_1CLICK')?>"><?=Loc::getMessage('BUY_1CLICK')?></a>
          </div>
          <a href = "#set_constructor" class = "my-sets_link set-link"><?=Loc::getMessage('MY_SET')?></a>
        </div> 
        </div>

          </div>
        </div>

        <?/*?><div class = "col col-md-3">  
          <div class="panel">
            <div class="panel-body set-panel">
              <? if($arResult['SET_ITEMS']['OLD_PRICE'] != 0): ?>
                <div class="contact-price">
                  <?=Loc::getMessage('CONTRACT_PRICE');?>:
                </div> 
                <div class = "set-panel-price__cool"><?=$arResult['SET_ITEMS']['PRICE']; ?></div>
                <div class = "set-panel-price__old"><?=$arResult['SET_ITEMS']['OLD_PRICE']; ?></div>
              <? endif; ?>
              <div class = "text-danger">
                <?=Loc::getMessage('YOUR_PROFIT');?>: 
              </div>
              <div class = "set-price__discount"><?=$arResult['SET_ITEMS']['PRICE_DISCOUNT_DIFFERENCE']?></div>
            </div>
            <a class = "btn btn-primary set_add2basket btn2"><?=Loc::getMessage('IN_BASKET')?></a>
            <a class = "btn btn-default fancyajax fancybox.ajax setbuy1click btn-button" 
              href = "<?=SITE_DIR?>forms/buy1click/" 
              title="<?=Loc::getMessage('BUY_1CLICK')?>"><?=Loc::getMessage('BUY_1CLICK')?></a>
          </div>
        	<a href = "#set_constructor" class = "my-sets_link set-link"><?=Loc::getMessage('MY_SET')?></a>
      	</div> 
      </div> 
      <?*/?>
      <div class = "allitems owlslider set my-set owl" style = "display: none;" data-margin = "24" data-nav = "true" data-loop = "false" data-responsive = '{"0":{"items":"2"},"768":{"items":"3"}, "956":{"items":"5"}}'>
      	<? foreach (array("DEFAULT", "OTHER") as $type): ?> 
      	  <? foreach ($arResult["SET_ITEMS"][$type] as $arItem): ?>
      	  	<div class = "set-item item" 
      	    	data-elementid="<?=$arItem['ID']?>"
      	      data-price = "<?=$arItem['PRICE_DISCOUNT_VALUE']?>"
      	      data-oldprice = "<?=$arItem['PRICE_VALUE']?>"
      	      data-discount = "<?=$arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE']?>
      	    ">
      	    	<div class = "checkbox <?=$type=="DEFAULT"?"selected":""?>"></div>
      	      <div class = "set-pic">
      	      	<a href = "<?=$arItem['DETAIL_PAGE_URL']?>">
      	        	<img src = "<?=!empty($arItem["DETAIL_PICTURE"]["src"]) ? $arItem["DETAIL_PICTURE"]["src"]:$arResult["NO_PHOTO"]["src"]?>" alt = "<?=$arItem["NAME"]?>">
      	        </a>
      	      </div>
      	      <div class = "set-data">
      	        <div class = "set-name">
      	          <a class = "aprimary" href = "<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem["NAME"]?></a>
      	        </div>
      	        <div class="hidden-xs set__category separator"></div>
      	        <div class = "set-prices">
                  <? if($arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE'] > 0): ?>
                  	<div class = "set-price__old">
                      <?=$arItem['PRICE_PRINT_VALUE']?>
                    </div>
                    <div class = "set-price__new-cool">
                      <?=$arItem['PRICE_PRINT_DISCOUNT_VALUE']?>
                    </div>
                  <? else: ?>
                  	<div class = "set-price__cool">
                    	<?=$arItem['PRICE_PRINT_VALUE']?>
                  	</div>
                 	<? endif; ?>
                </div> 
      	      </div>
              <div class="clearfix"></div>
      	    </div>  
      	  <? endforeach; ?>
      	<? endforeach; ?>
      </div>
    </div>
  </div>
</div>