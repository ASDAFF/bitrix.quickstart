<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
    <div class="contacts_page">
        <div class="mapbuttons container">
            <button class="active">Карта метро</button>
            <button>Карта города</button>
        </div>
        <div class="map" id="shops_map">
            <div class="map_preloader">
                <div class="preload-circle"></div>
               
                <img src="/img/preload-1.png" alt="" class="preload-moving">
                
                <img src="/img/preload-2.png" alt="" class="preload-unmoving">
            </div>
            <?foreach($arResult['SHOPS'] as $key=>$shop){?>
            <div class="contacts_detailinfo" data-id="<?=$shop["id"]?>">
                <div class="info_block">
                    <a href="javascript:void(0)" class="cancel flaticon-cross">X</a>
                    <p class="heading"><?=$shop['street']?> <?=$shop['house_html']?></p>
                    <ul>
                        <?if($shop['phones'][0]){?>
                        <li style="background-image:url('/img/contacts/info_icon1.png')"><p><?=$shop['phones'][0]?></p></li>
                        <?}?>
                        <li style="background-image:url('/img/contacts/info_icon2.png')"><p>Будни: <?=$shop['time1']?></p><p>Суббота: <?=$shop['time2']?></p><p>Воскресенье: <?=$shop['time3']?></p></li>
                        <?if($shop['how_to_reach']){?>
                        <li style="background-image:url('/img/contacts/info_icon3.png')"><p>Как добраться?</p></li>
                        <?}?>
                    </ul>
                    <?if($shop['how_to_reach']){?>
                    <p><?=$shop['how_to_reach']?></p>
                    <?}?>
                </div>
                <div class="info_block">
                    <?if($shop['images']){?>
                    <span class="heading" style="background-image:url('/img/contacts/info_icon4.png')">Фото магазина</span>
                    <?if($shop['images'][3]){?>
                    <a data-fancybox="gallery<?=$key?>" href="<?=$image?>" class="all_photos">Все фотографии</a>
                    <?}?>
                    <div class="contact_images">
                        <?foreach($shop['images'] as $k => $image){?>
                        <a data-fancybox="gallery<?=$key?>" href="<?=$image?>" data-source="background-image: url('<?=$image?>'); <?if($k > 2){?> display: none<?}?>" style="<?if($k > 2){?> display: none<?}?>"></a>
                        <?}?>
                    </div>
                    <?}?>
                    <a href="<?=$shop["detail_page_url"]?>" class="btn orange">Перейти в магазин</a>
                    <div class="hidden">
                        <div class="ya-taxi-widget"
                                data-size="s"
                                data-theme="action"
                                data-title="Вызвать такси"
                                data-CLID="moshoztorg"
                                data-APIKEY="4b6e4fcc5e99490eb6a152fe02dd1927"
                                data-description=""
                                data-use-location="true"
                                data-point-b="<?=round($shop['coords'][1], 6)?>,<?=round($shop['coords'][0], 6)?>">
                        </div>
                    </div>
                    <a href="javascript:void(0)" class="btn yellow right gettaxi">Вызвать такси</a>      
                </div>
            </div>
            <?}?>
        </div>
        <div class="metromap">
            <?require_once($_SERVER['DOCUMENT_ROOT'].'/include/metromap.php')?>
        </div>
        <div class="contacts container" id="contactsanchor">
        <h1>адреса магазинов</h1>
        <div class="dealers">
        
        <?
        //ПРЕОБРАЗУЕМ В НОВЫЙ ФОРМАТ
        foreach($arResult['SHOPS'] as $key=>$shop){
            $str1 = $arResult['SHOPS'][$key]['coords'][0];
            $str2 = $arResult['SHOPS'][$key]['coords'][1];
            $arResult['SHOPS'][$key]['coords'][0] = $str2;
            $arResult['SHOPS'][$key]['coords'][1] = $str1;
        }
        ?>
     <script>
     	mht.shops = <?
     		echo WP::js($arResult['SHOPS']);
     	?>;
     	mht.shopRegion = "<?=$arResult['ACTIVE_REGION']?>";
     </script>

<div class="area_block">
    <div class="selectwrap">
    <select class="choose_region">
           <option value="all">Все магазины</option>
           <option value="sao">САО</option>
           <option value="svao">СВАО</option>
           <option value="vao">ВАО</option>
           <option value="uvao">ЮВАО</option>
           <option value="uao">ЮАО</option>
           <option value="uzao">ЮЗАО</option>
           <option value="zao">ЗАО</option>
           <option value="szao">СЗАО</option>
           <option value="cao">ЦАО</option>
    </select>
    </div>
    <div class="checkwrap">
        <input type="checkbox" id="onlyopened">
        <label for="onlyopened">Только открытые</label>
    </div>
    <a class="shop-search-reset btn orange">Сброс</a>
    <div class="shop-search float-right">
        <form>
           <input type="text" name="q" class="input-search inheader ui-autocomplete-input" placeholder="Поиск по адресу, метро" autocomplete="off">
            <input type="submit" class="input_search_submit js-search-button inheader" value="">
        </form>
    </div>
</div>
<div class="dealers_block <?=$n==6 ? 'nomarg' : ''?>" id="shops">
        <div class="dealers_filter">
            <div class="row">
                <div class="col-4-12">
                    <p>Адрес <a href="javascript:void(0)" class="sort flaticon-sort-down" title="Сортировка по алфавиту А-Я"></a></p>
                </div>
                <div class="col-3-12">
                    <p>Станция метро <a href="javascript:void(0)" class="sort flaticon-sort-down" title="Сортировка по алфавиту А-Я"></a></p>
                </div>
                <div class="col-2-12">
                    <p>Телефон</p>
                </div>
                <div class="col-3-12">
                    <p>График работы</p>
                </div>
            </div>
        </div>
    <div class="dealerslist">
	<?foreach($arResult['SHOPS'] as $shop){?>
       <?if(!$shop['isComingSoon']){?>
        <div class="dealer" data-region="<?=$shop["adm_okrug"]?>">
            <div class="row">
                <div class="col-4-12">
                    <a href="<?=$shop['link']?>"><p><?=$shop['street']?> <?=$shop['house_html']?></p></a>
                </div>
                <div class="col-3-12">
                    <p><i class="flaticon-moscow-metro-logo" style="color: rgb(<?=$shop["subway_color"][0]?>,<?=$shop["subway_color"][1]?>,<?=$shop["subway_color"][2]?>)"></i> <span><?=$shop["subway"]?></span></p>
                </div>
                <div class="col-2-12">
                    <span class="dealer_phones"><?=$shop['phones'][0]?></span>
                </div>
                <div class="col-3-12">
                    <div class="dealer_time"><span>Будни:</span> <?=$shop['time1']?></div>
                    <div class="dealer_time"><span class="orange">Сб:</span> <?=$shop['time2']?> <span class="orange">Вс:</span> <?=$shop['time3']?></div>
                </div>
            </div>
        </div>
        <?} else {?>
        
        <div class="dealer" data-region="<?=$shop["adm_okrug"]?>" data-coming="true">
            <div class="row">
                <div class="col-4-12">
                    <a href="<?=$shop['link']?>"><p><?=$shop['street']?> <?=$shop['house_html']?></p></a>
                </div>
                <div class="col-3-12">
                    <p><i class="flaticon-moscow-metro-logo" style="color: rgb(<?=$shop["subway_color"][0]?>,<?=$shop["subway_color"][1]?>,<?=$shop["subway_color"][2]?>)"></i> <span><?=$shop["subway"]?></span></p>
                </div>
                <div class="col-2-12">
                    <span class="dealer_phones"><?=$shop['phones'][0]?></span>
                </div>
                <div class="col-3-12">
                    <div class="dealer_time"><span class="orange">Скоро открытие</span></div>
                </div>
            </div>
        </div>
        <?}?>
    <?}?>
    </div>
    <p class="noresults">По вашему поисковому запросу результатов не найдено</p>
</div>
      </div>
    </div>
	</div>