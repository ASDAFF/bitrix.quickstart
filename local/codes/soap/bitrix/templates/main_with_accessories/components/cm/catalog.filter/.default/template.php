<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!$_REQUEST["ajax"] && @$_REQUEST["ajax"] != "Y"){
  $allprops = array_merge($arResult["arrProp"], $arResult["arrOfferProp"]);
  $props = array();
  foreach($allprops as $prop) {
    $props[$prop["CODE"]] = $prop;
  }
  foreach($arResult["ITEMS"] as $filtItem) {
    $arFiltName = explode("[", $filtItem["INPUT_NAME"]);
    $filtName = str_replace("]", "", $arFiltName[1]);
    if($filtName) $props[$filtName]["VALUES"] = $filtItem["~INPUT_VALUE"]; 
  }
  $price_id = 1;
  $price_from = intval($_REQUEST[$arParams["FILTER_NAME"] . "_cf"][$price_id["ID"]]["LEFT"]) ? intval($_REQUEST[$arParams["FILTER_NAME"] . "_cf"][$price_id["ID"]]["LEFT"]) : $arResult["MIN_PRICE"];
  $price_to = intval($_REQUEST[$arParams["FILTER_NAME"] . "_cf"][$price_id["ID"]]["RIGHT"]) ? intval($_REQUEST[$arParams["FILTER_NAME"] . "_cf"][$price_id["ID"]]["RIGHT"]) : $arResult["MAX_PRICE"];
?>
      <div class="filterPnl">
            <div class="col1">
                <div class="sizePrt">
                    <?$prop = "RAZMER"; $arProp = $props[$prop];?>
          <div class="ttl">
                        <?=$arProp["NAME"]?>
                    </div>
                    <!--<a class="openChooseSize" href="JavaScript:void(0);" onclick="jQuery('.popap_size').css('visibility','visible');">Выберите размер</a>-->
          <div class="sizes">
                    </div>
          <div class="clear"></div>
                    <div class="param_size">
                           <div class="popap_size">
                               <!--<a href="JavaScript:void(0);" onclick="jQuery('.popap_size').css('visibility','hidden');" class="btn_close">X</a>-->
                            <ul>
                            <?asort($arProp["VALUE_LIST"]);
                    foreach($arProp["VALUE_LIST"] as $value => $name) {?>
                    <li>
                      <a href="JavaScript:void(0);" size="<?=$value?>" name="__<?=$arResult["FILTER_NAME"]?>_op[<?=$prop?>][]"
                         <?if(in_array($value, $arProp["VALUES"])){?>class="available"<?}?>>
                        <?=$name?>
                      </a>
                    </li>
                  <?}?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="growthPrt">
                    <?$prop = "ROST"; $arProp = $props[$prop];?>
          <div class="ttl">
                        <?=$arProp["NAME"]?>
                    </div>
                    <?asort($arProp["VALUE_LIST"]);
            foreach($arProp["VALUE_LIST"] as $value => $name) {?>
            <div class="snglCheck">
                          <input type="checkbox" class="filterElem" name="__<?=$arResult["FILTER_NAME"]?>_op[<?=$prop?>][]" value="<?=$value?>" id="<?=$arResult["FILTER_NAME"]?><?=$prop?><?=$value?>" <?if(in_array($value, $arProp["VALUES"])){?>checked<?}?>/>
                          <label for="<?=$arResult["FILTER_NAME"]?><?=$prop?><?=$value?>"><?=$name?></label>
                      </div>
          <?}?>
                    <div class="clear"><!-- --></div>
                </div>
            </div>
            <div class="col2">
                <div class="pricePrt">
                    <div class="ttl">
                        Цена
                    </div>
                    <div class="priceRes">
                        <span>от</span>
                        <input type="text" value="<?=$price_from?>" id="minCost" name="__filtering_cf[<?=$price_id["ID"]?>][LEFT]" class="filterElem" />
                        <span>до</span>
                        <input type="text" value="<?=$price_to?>" id="maxCost" name="__filtering_cf[<?=$price_id["ID"]?>][RIGHT]" class="filterElem" />
                        <span>руб.</span>
                    </div>
                    <div></div>
          <div class="priceFilter">
                        <div class="bg" id="priceFilter" style="width:156px;"></div>
                        <div class="data">
                            <span class="minPrice"><?=number_format($arResult["MIN_PRICE"], 0, ".", " ")?></span>
                            <!--<span><?=number_format(floor($arResult["MAX_PRICE"] / 4), 0, ".", " ")?></span>
                            <span><?=number_format(floor($arResult["MAX_PRICE"] / 2), 0, ".", " ")?></span>
                            <span><?=number_format(floor($arResult["MAX_PRICE"] * 0.75), 0, ".", " ")?></span>-->
                            <span class="maxPrice"><?=number_format($arResult["MAX_PRICE"], 0, ".", " ")?></span>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col3">
                <div class="shopPrt">
                      <?$prop = "STORE"; $arProp = $props[$prop];?>
            <?//p($arProp);?>
            <div class="ttl">
                          <?=$arProp["NAME"]?>
                      </div>
                      <div class="select">
                          <div class="newListSelected newListSelFocus" tabindex="0">
                              <input type="text" class="selectedTxt filteractive" value="<?=($arProp["VALUES"]?$arProp["VALUE_LIST"][$arProp["VALUES"]]:"Выберите")?>" name="__<?=$arResult["FILTER_NAME"]?>_op[<?=$prop?>]" />
                          </div>
                          <ul class="newList" style="height: 255px; overflow-y: scroll;">
                          <?if($arProp["VALUES"]){?>
                  <li class="all">
                    <a href="">все</a>
                  </li>
                <?}?>
                          <?foreach($arProp["VALUE_LIST"] as $value => $name) {?>
                  <li><nobr><a href="<?=$value?>"><?=$name?></a></nobr></li>
                <?}?>
                          </ul>
                      </div>
                </div>
                <div class="search">
                    <?$prop = "NAME"; $arProp = $props[$prop];?>
          <div class="ttl">
                        Название модели
                    </div>
          <div class="row searchInput">
                        <input type="text" class="searchTxt filterElem" value="<?=$arProp["VALUES"]?>" name="__<?=$arResult["FILTER_NAME"]?>_ff[<?=$prop?>]" />
            <div class="searchBtn"></div>
                    </div>
                </div>
                <? $shop_sections = explode('/',$arResult['FORM_ACTION']);
                    if (isset($shop_sections[2]) && $shop_sections[2] != 'outerwear'){
                ?>
                <div class="seatPrt">
                    <?$prop = "POSADKA"; $arProp = $props[$prop];?>
          <div class="ttl">
                        <?=$arProp["NAME"]?>
                    </div>
                    <?foreach($arProp["VALUE_LIST"] as $value => $name) {
                        if ($value != 760 && $value != 761){ ?>
            <div class="row">
              <input type="checkbox" class="filterElem" name="__<?=$arResult["FILTER_NAME"]?>_op[<?=$prop?>][]" value="<?=$value?>" id="<?=$arResult["FILTER_NAME"]?><?=$prop?><?=$value?>" <?if(in_array($value, $arProp["VALUES"])){?>checked<?}?>/>
                          <label for="<?=$arResult["FILTER_NAME"]?><?=$prop?><?=$value?>"><?=$name?></label>
                          <div class="clear"><!-- --></div>
                      </div>
          <?} } ?>
                </div>
            <? } ?>    
            </div>
            <div class="clear"></div>
    <? if (isset($shop_sections[2]) && $shop_sections[2] != 'outerwear'){ ?>
      <div class="more_filters"><a href="">Расширенный поиск</a></div>
            <div class="more_filtering">
        <div class="col1">
                  <div class="selectPrt">
                      <?$prop = "SHLIC"; $arProp = $props[$prop];?>
            <div class="ttl">
                          <?=$arProp["NAME"]?>
                      </div>
                      <div class="select">
                          <div class="newListSelected newListSelFocus" tabindex="0">
                              <input type="text" class="selectedTxt" value="<?=($arProp["VALUES"]?$arProp["VALUE_LIST"][$arProp["VALUES"]]:"Выберите")?>" class="filteractive" name="__<?=$arResult["FILTER_NAME"]?>_pf[<?=$prop?>]" />
                          </div>
                          <ul class="newList">
                          <?if($arProp["VALUES"]){?>
                  <li class="all">
                    <a href="">все</a>
                  </li>
                <?}?>
                          <?foreach($arProp["VALUE_LIST"] as $value => $name) {?>
                  <li><a href="<?=$value?>"><?=$name?></a></li>
                <?}?>
                          </ul>
                      </div>
                  </div>
              </div>
              <div class="col2">
                  <div class="selectPrt">
                      <?$prop = "PUGOVIC"; $arProp = $props[$prop];?>
            <div class="ttl">
                          <?=$arProp["NAME"]?>
                      </div>
                      <div class="select">
                          <div class="newListSelected newListSelFocus" tabindex="0">
                              <input type="text" class="selectedTxt" value="<?=($arProp["VALUES"]?$arProp["VALUE_LIST"][$arProp["VALUES"]]:"Выберите")?>" class="filteractive" name="__<?=$arResult["FILTER_NAME"]?>_pf[<?=$prop?>]" />
                          </div>
                          <ul class="newList">
                          <?if($arProp["VALUES"]){?>
                  <li class="all">
                    <a href="">все</a>
                  </li>
                <?}?>
                <?foreach($arProp["VALUE_LIST"] as $value => $name) {?>
                  <li><a href="<?=$value?>"><?=$name?></a></li>
                <?}?>
                          </ul>
                      </div>
                  </div>
              </div>
      </div>
    <? } ?>
      <div class="clear_filters"><a href="">Сбросить фильтры</a></div>
            <div class="clear"></div>
        </div>

  
  
  <div style="display: block;" class="hiddenfilters">
    <form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" id="form_filtering">
        <?foreach($arResult["ITEMS"] as $arItem):
            if(array_key_exists("HIDDEN", $arItem)):
                echo $arItem["INPUT"];
            endif;
        endforeach;?>
        <table class="data-table" cellspacing="0" cellpadding="2">
        <thead>
            <tr>
                <td colspan="2" align="center"><?=GetMessage("IBLOCK_FILTER_TITLE")?></td>
            </tr>
        </thead>
        <tbody>
            <?$checkboxes = array("article", "country", "navi");
          foreach($arResult["ITEMS"] as $arItem):?>
                <?if(!array_key_exists("HIDDEN", $arItem)) {
            $isCheckbox = false;?>
                    <?foreach($checkboxes as $propCode) {
              if(strpos($arItem["INPUT_NAME"],$propCode) !== false) {
              $isCheckbox = true;?>
              <tr>
                          <td valign="top"><?=$arItem["NAME"]?>:</td>
                          <td valign="top">
                  <?foreach($props[$propCode]["VALUE_LIST"] as $value => $name) {?>
                    <input type="checkbox" name="<?=$arItem["INPUT_NAME"]?>[]" value="<?=$value?>" <?if(in_array($value, $arItem["~INPUT_VALUE"])){?>checked<?}?>/><?=$name?>
                  <?}?>
                </td>
                      </tr>
              <?}?>
            <?}?>
            <?if(!$isCheckbox) {?>
              <tr>
                          <td valign="top"><?=$arItem["NAME"]?>:</td>
                          <td valign="top"><?=$arItem["INPUT"]?></td>
                      </tr>
            <?}?>
                <?}?>
            <?endforeach;?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <input type="hidden" name="set_filter" value="Y" />
            </tr>
        </tfoot>
        </table>
    </form>
  </div>
<?}?>