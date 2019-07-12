<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$this->setFrameMode(true);

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$this->SetViewTarget("location");
  ?><span class="headline-location hidden-xs"><?
      ShowMessage($arResult['ERROR_MESSAGE']);
      ?><form action="<?=$arResult['ACTION_URL']?>" method="POST" id="locationinhead" class="headline-location__form"><?
          $frame = $this->createFrame('locationinhead',false)->begin();
          $frame->setBrowserStorage(true);
          echo bitrix_sessid_post();
          ?><span class="dropdown"><?
              ?><input type="hidden" name="<?=$arParams['REQUEST_PARAM_NAME']?>" value="Y" /><?
              ?><input type="hidden" name="PARAMS_HASH" value="<?=$arParams['PARAMS_HASH']?>" /><?
              /*?><input type="radio" name="<?=$arParams["CITY_ID"]?>" value="<?=$arResult["LOCATION"]["ID"]?>" <?
              ?>id="rsloc_<?=$arResult["LOCATION"]["ID"]?>" checked="checked" /><?*/
              ?><span><?=Loc::getMessage('RS.MONOPOLY.YOUR_CITY')?>: </span><?
              ?><a class="dropdown-toggle popup_link headline-location__sity JS-Popup-Wide" id="ddLocationMenu" href="<?=SITE_DIR?>cities/"><span><?=$arResult['LOCATION']['CITY_NAME']?></span><i class="fa fa-angle-down"></i></a><?
              /*if(is_array($arResult["LOCATIONS"]) && count($arResult["LOCATIONS"])>0) {
                  ?><ul class="dropdown-menu views-box drop-panel headline-location__list" aria-labelledby="ddLocationMenu"><?
                      foreach($arResult["LOCATIONS"] as $arLocation){
                          ?><li class="views-item"><a href="#" onclick="document.getElementById('rsloc_<?= $arLocation["ID"] ?>').checked='checked'; document.getElementById('locationinhead').submit(); return false;"><?
                              ?><label for="rsloc_<?= $arLocation["ID"] ?>"><?= $arLocation["CITY_NAME"] ?></label><?
                              ?><input type="radio" name="<?= $arParams["CITY_ID"] ?>" value="<?= $arLocation["ID"] ?>" <?
                              ?>id="rsloc_<?= $arLocation["ID"] ?>" /><?
                          ?></a></li><?
                      }
                      ?><li class="views-item"><a class="JS-Popup-Wide" href="<?=SITE_DIR?>cities/" title="<?=Loc::getMessage('RS.MONOPOLY.YOUR_CITY')?>"><?=Loc::getMessage('RS.MONOPOLY.ALL_CITIES')?></a></li><?
                  ?></ul><?
              }*/
          ?></span><?
          $frame->beginStub();
          ?><span><?=Loc::getMessage('RS.MONOPOLY.YOUR_CITY')?>: </span></a><?
          $frame->end();
      ?></form><?
  ?></span><?
$this->EndViewTarget();
