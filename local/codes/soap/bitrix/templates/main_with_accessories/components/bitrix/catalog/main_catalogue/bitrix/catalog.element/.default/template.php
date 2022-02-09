<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$elementname = $arResult['PROPERTIES']['type']['VALUE']." ".$arResult['NAME']." ".$arResult['PROPERTIES']['model']['VALUE']." (".$arResult['PROPERTIES']['article']['VALUE'].")";
    //echo "<pre>", print_r($arResult,1), "</pre>";?>
<?//$APPLICATION->AddChainItem("название элемента", "/catalogue/planshety/apple/posledniy_tovar/");?>
<?

$res = CIBlockProperty::GetPropertyEnum("country", Array(), Array("IBLOCK_ID"=>1,"ID"=>$arResult['PROPERTY_5']));
if($ar_res = $res->GetNext())
  $country = $ar_res['VALUE'];
$res = CIBlockProperty::GetPropertyEnum("warranty", Array(), Array("IBLOCK_ID"=>1,"ID"=>$arResult['PROPERTY_6']));
if($ar_res = $res->GetNext())
  $garancy= $ar_res['VALUE'];


?>
<article class="b-detail-wrapper clearfix">
    <div class="b-detail-section">
        <h2 class="b-detail__h2"><?=$elementname?></h2>

        <?if(is_array($arResult["PREVIEW_PICTURE"]) || is_array($arResult["DETAIL_PICTURE"])):?>
            <?if(is_array($arResult["DETAIL_PICTURE"])):?>
                <div class="b-detail__image"><img border="0" id="b-detail__image" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></div>
                <?elseif(is_array($arResult["PREVIEW_PICTURE"])):?>
                <div class="b-detail__image"><img border="0" id="b-detail__image" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></div>
                <?endif?>
            <?if(count($arResult["PROPERTIES"]["dop_pic"])>0):?>
                <div class="b-detail-slider__wrapper" id="b-detail-slider">
                    <a href="#" class="b-slider__control m-prev"></a>
                    <div class="b-slider clearfix">
                        <?
                            $i=1;
                            foreach($arResult["PROPERTIES"]["dop_pic"]["VALUE"] as $dop_pic):
                                $arImagesPath = CFile::GetPath($dop_pic);
                                $i++;
                            ?>
                            <?if(reset($arResult["PROPERTIES"]["dop_pic"]["VALUE"])==$dop_pic){echo "<div><div class='b-detail-slider__item active'><a href=".$arResult["DETAIL_PICTURE"]["SRC"]."><img src=".$arResult["DETAIL_PICTURE"]["SRC"]." alt='' /></a></div>";}?>
                            <div class="b-detail-slider__item"><a href="<?=$arImagesPath?>"><img src="<?=$arImagesPath?>" alt="" /></a></div>
                            <?if($i%4==0 AND end($arResult["PROPERTIES"]["dop_pic"]["VALUE"])!=$dop_pic){echo "</div><div>";}?>
                            <?if(end($arResult["PROPERTIES"]["dop_pic"]["VALUE"])==$dop_pic){echo "</div>";}?>
                            <?endforeach;?>
                    </div>
                    <a href="#" class="b-slider__control m-next"></a>
                </div>
                <?endif;?>
            <?endif;?>
<?
$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_linked","PROPERTY_rating");
$arFilter = Array("IBLOCK_ID"=>"9", "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y","PROPERTY_linked"=> $arResult["ID"]);
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
$reviews_count=0;
while($ob = $res->GetNextElement())
{
 $arFields = $ob->GetFields();
 $reviews_count++;
 $rating += $arFields["PROPERTY_RATING_VALUE"];
}
?>
        <div class="b-tab-head">
            <a href="#detail-text" class="b-tab-head__link">Описание</a>
            <a href="#teh" class="b-tab-head__link active">Технические характеристики</a>
            <a href="#b-review" class="b-tab-head__link">Отзывы (<?echo $reviews_count;?>)</a>
            <span class="b-tab-head__link"><span class="b-rating"><span style="width: <?=($rating/$reviews_count*20)?>%"></span></span></span>
        </div>
        <div id="detail-text" class="b-tab__body">
            <?if($arResult["DETAIL_TEXT"]):?>
                <div class="b-detail__text"><?=$arResult["DETAIL_TEXT"]?></div>
                <?endif;?>
            <?if($arResult["PROPERTIES"]["video_link"]["VALUE"]):?>
                <!-- max-width применён и к iframe -->
                <div class="b-detail__video">
                    <iframe width="560" height="315" src="<?=$arResult["PROPERTIES"]["video_link"]["VALUE"];?>" frameborder="0" allowfullscreen></iframe>
                </div>
                <?endif;?>	
        </div>
        <div id="teh" class="b-tab__body active">
            <?$APPLICATION->IncludeComponent("energosoft:energosoft.group_property_manual", "template", array(
	"ES_IBLOCK_TYPE_CATALOG" => "catalog",
	"ES_IBLOCK_CATALOG" => "1",
	"ES_ELEMENT" => $arResult["ID"],
	"ES_SHOW_EMPTY" => "N",
	"ES_SHOW_EMPTY_PROPERTY" => "N",
	"ES_REMOVE_HREF" => "N",
	"ES_GROUP_COUNT" => "4",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"ES_GROUP_NAME_0" => "Общие",
	"ES_GROUP_0" => array(
		0 => "manufacturer",
		1 => "specification",
		2 => "country",
		3 => "display_size",
		4 => "display_screen_type",
		5 => "display_resolution",
		6 => "display_type",
		7 => "display_touch",
		8 => "display_touch_type",
		9 => "display_touch_multi",
		10 => "support_3d",
		11 => "display_screen_type_tech",
		12 => "product_weight",
		13 => "processor_technology",
		14 => "processor_socket",
		15 => "processor_core",
		16 => "processor_manufacturer",
		17 => "processor_type",
		18 => "processor_code",
		19 => "processor_frequency",
		20 => "processor_cache_l1",
		21 => "processor_cache_l2",
		22 => "processor_cache_l3",
		23 => "processor_number_of_cores",
		24 => "chipset",
		25 => "system_bus_frequency",
		26 => "memory_form_factor",
		27 => "memory_size",
		28 => "memory_type",
		29 => "memory_frequency",
		30 => "memory_slots_count",
		31 => "memory_size_maximum",
		32 => "internal_memory_size",
		33 => "total_hdd_size",
		34 => "hdd_type",
		35 => "hdd_round_speed",
		36 => "hdd_count_two",
		37 => "optical_drive",
		38 => "optical_drive_placement",
		39 => "wifi_present",
		40 => "wifi_standart",
		41 => "wifi_direct_support",
		42 => "dlna_support",
		43 => "bluetooth_present",
		44 => "bluetooth_version",
		45 => "a2dp_support",
		46 => "nfc_support",
		47 => "lte_present",
		48 => "wimax_present",
		49 => "gsm_gprs_support",
		50 => "umts_3g_present",
		51 => "edge_support",
		52 => "hsdpa_support",
		53 => "hsupa_support",
		54 => "hspa_plus_support",
		55 => "work_as_mobile_phone",
		56 => "sim_card",
		57 => "video_controller_type",
		58 => "videocard_chipset_vendor",
		59 => "videocard_chipset",
		60 => "videomemory_size",
		61 => "videomemory_type",
		62 => "battery_type",
		63 => "battery_capacity",
		64 => "battery_cells",
		65 => "work_time",
		66 => "work_time_on_music",
		67 => "work_time_on_video",
		68 => "time_of_charge",
		69 => "work_time_with_optional_battery",
		70 => "usb_charge",
		71 => "operating_system",
		72 => "windows_version",
		73 => "macosx_version",
		74 => "android_version",
		75 => "operating_system_support",
		76 => "cardreader_present",
		77 => "memory_card_support",
		78 => "memory_card_max_size",
		79 => "expresscard_slot",
		80 => "expresscard_standart",
		81 => "internal_local_network",
		82 => "local_network_speed",
		83 => "faxmodem_present",
		84 => "usb20_count",
		85 => "usb30_count",
		86 => "firewire_present",
		87 => "usb_connect_to_pc",
		88 => "firewire800_present",
		89 => "external_usb_connect",
		90 => "tvin_input",
		91 => "tvout_ouput",
		92 => "vga_output",
		93 => "minivga_output",
		94 => "dvi_output",
		95 => "hdmi_output",
		96 => "microhdmi_output",
		97 => "displayport_outout",
		98 => "minidisplayport_output",
		99 => "lpt_present",
		100 => "thunderbolt_present",
		101 => "esata_present",
		102 => "irda_present",
		103 => "com_port",
		104 => "dockstation_port",
		105 => "dockstation_retail_pack",
		106 => "audio_input",
		107 => "microphone_input",
		108 => "audio_output",
		109 => "spdif_present",
		110 => "microphone_present",
		111 => "subwoofer_present",
		112 => "speakers_present",
		113 => "audio_type",
		114 => "qwerty_keyboard",
		115 => "keyboard_keys_count",
		116 => "keyboard_backlight",
		117 => "positioning_device",
		118 => "stylus_pen_retail_pack",
		119 => "gps_present",
		120 => "agps_support",
		121 => "glonass_present",
		122 => "compas_present",
		123 => "accelerometer_present",
		124 => "gyroscope_present",
		125 => "proximity_sensor_present",
		126 => "light_sensor_present",
		127 => "barometer_present",
		128 => "webcamera_present",
		129 => "webcamera_resolution",
		130 => "front_camera",
		131 => "front_camera_resolution",
		132 => "back_camera",
		133 => "back_camera_resolution",
		134 => "flash_light",
		135 => "autofocus",
		136 => "fingerprint_sensor",
		137 => "tv_tuner",
		138 => "remote_control",
		139 => "fm_tuner_present",
		140 => "fm_transmitter_present",
		141 => "kensington_lock",
		142 => "shell_shockproof",
		143 => "shell_metal",
		144 => "shell_waterproof",
		145 => "case_material",
		146 => "audio_format_support",
		147 => "video_format_support",
	),
	"ES_GROUP_NAME_1" => "Производитель",
	"ES_GROUP_1" => array(
		0 => "country",
	),
	"ES_GROUP_NAME_2" => "",
	"ES_GROUP_2" => array(
	),
	"ES_GROUP_NAME_3" => "",
	"ES_GROUP_3" => array(
	)
	),
	false
);?>
        </div>
        <div id="b-review" class="b-tab__body">
<?
global $USER;
if ($USER->IsAuthorized()){
?>
<?$APPLICATION->IncludeComponent("bitrix:iblock.element.add.form", "review_add_form", array(
	"IBLOCK_TYPE" => "catalog",
	"IBLOCK_ID" => "9",
	"STATUS_NEW" => "N",
	"LIST_URL" => "",
	"USE_CAPTCHA" => "N",
	"USER_MESSAGE_EDIT" => "",
	"USER_MESSAGE_ADD" => "",
	"DEFAULT_INPUT_SIZE" => "30",
	"RESIZE_IMAGES" => "N",
	"PROPERTY_CODES" => array(
		0 => "NAME",
		1 => "PREVIEW_TEXT",
		2 => "33",
		3 => "34",
		4 => "35",
		5 => "38",
	),
	"PROPERTY_CODES_REQUIRED" => array(
		0 => "33",
	),
	"GROUPS" => array(
		0 => "3",
		1 => "4",
	),
	"STATUS" => "ANY",
	"ELEMENT_ASSOC" => "CREATED_BY",
	"MAX_USER_ENTRIES" => "100",
	"MAX_LEVELS" => "100",
	"LEVEL_LAST" => "Y",
	"MAX_FILE_SIZE" => "0",
	"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
	"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
	"SEF_MODE" => "N",
	"SEF_FOLDER" => "/catalogue/noutbuki/super_noutbuk/",
	"CUSTOM_TITLE_NAME" => "",
	"CUSTOM_TITLE_TAGS" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
	"CUSTOM_TITLE_IBLOCK_SECTION" => "",
	"CUSTOM_TITLE_PREVIEW_TEXT" => "",
	"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
	"CUSTOM_TITLE_DETAIL_TEXT" => "",
	"CUSTOM_TITLE_DETAIL_PICTURE" => "",
	"ELEMENT_ID" =>$arResult["ID"]
	),
	false
);?>
<?}?>
<?$GLOBALS['intFilter']['PROPERTY_linked'] = $arResult["ID"];?>
<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "reviews_list", array(
	"IBLOCK_TYPE" => "catalog",
	"IBLOCK_ID" => "9",
	"SECTION_ID" => "",
	"SECTION_CODE" => "",
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "intFilter",
	"INCLUDE_SUBSECTIONS" => "Y",
	"SHOW_ALL_WO_SECTION" => "N",
	"PAGE_ELEMENT_COUNT" => "30",
	"LINE_ELEMENT_COUNT" => "3",
	"PROPERTY_CODE" => array(
		0 => "linked",
		1 => "value",
		2 => "limitations",
		3 => "useless",
		4 => "helpful",
		5 => "rating",
		6 => "",
	),
	"OFFERS_LIMIT" => "5",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"ADD_SECTIONS_CHAIN" => "N",
	"DISPLAY_COMPARE" => "N",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"CACHE_FILTER" => "N",
	"PRICE_CODE" => array(
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"CONVERT_CURRENCY" => "N",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Товары",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
        </div>					
    </div>
    <div class="b-detail-sidebar">
        <div class="b-detail-sidebar__text"><?=$arResult["PREVIEW_TEXT"]?></div>
        <!--<div class="b-detail-sidebar__old_price"><span>20 000</span>.–</div>-->
         <? if ($arResult['STORE']) { ?>
            <h2 class="b-pickup__h2">Возможен самовывоз:</h2>
            <? foreach ($arResult['STORE'] as $store) { ?>
                <div class="b-detail-sidebar__pickup">
                    <div class="b-metro m-green"><?= $store["STORE_NAME"] ?></div>
                    <div class="b-availability-wrapper">Наличие: <span class="b-availability <? if ($store['AMOUNT_%'] > 2 && $store['AMOUNT_%'] < 4) { ?> m-medium<? } elseif ($store['AMOUNT_%'] <= 2) { ?>m-small<? } ?>"><?= str_repeat('<span class="b-availability__item"></span>', $store['AMOUNT_%']) ?></span></div>
                    <div class="b-availability__text"><?= $store["STORE_ADDR"] ?> <?= $store['SCHEDULE'] ?></div>
               </div>
            <? } ?>
        <? } ?>
        <div class="b-detail-sidebar__new_price">
            <div class="b-slider__price">
                <? //echo "<pre>", print_r($arElement,1), "</pre>";?>
            <?=$arResult["PRICES"]["price"]["PRINT_VALUE_NOVAT"]?></div>
            <?if($arResult["PRICES"]["clearing"]["PRINT_VALUE_NOVAT"]):?>
                <div class="b-slider__price_clearing">Безнал <b><?=$arResult["PRICES"]["clearing"]["PRINT_VALUE_NOVAT"]?></b></div>
                <?endif;?>
        </div>
        <div class="b-detail-sidebar__btn">
            <?if($arResult["CAN_BUY"]):?>
				<div class="clearfix">
					<noindex>
						<a class="b-button m-orange" id="b-detail__image" href="<?echo $arResult["ADD_URL"]?>" rel="nofollow" title="<?echo GetMessage("CATALOG_ADD_TO_BASKET")?>"><?echo GetMessage("CATALOG_ADD_TO_BASKET")?></a>
						<div class="b-detail__button">
							<a href="<?//echo $arElement["COMPARE_URL"]?>#b-compare__add" rel="/catalogue/?action=ADD_TO_COMPARE_LIST&id=<?=$arResult['ID']?>"  class="b-icon m-icon__compare m-compare__add" title="<?echo GetMessage("CATALOG_COMPARE")?>"></a>
							<a el='<?=$arResult['ID']?>' class="m-wishlist__add" href="#b-wishlist__add"><span class="b-icon" title="<?echo GetMessage("WISHLIST")?>"></span></a>
						</div>
					</noindex>
				</div>
                <?endif;?>
				<div class="b-popup m-popup__orange" id="b-compare__add">
					<div class="b-popup__wrapper">
						<h2 class="b-popup-compare__h2">Товар добавлен к сравнению.</h2>
						<a class="b-button__fast" onclick='location.href=/catalogue/compare.php' href="/catalogue/compare.php">Сравнить товары</a>
					</div>
				</div>
				<div class="b-popup m-popup__orange" id="b-wishlist__add">
			<div class="b-popup__wrapper">
				<div class="b-wishlist__select">
					<select name="cat" id="cat_list">
					<? if($USER->GetID()):
						$arFilter = Array('IBLOCK_ID'=>2, 'GLOBAL_ACTIVE'=>'Y', 'CREATED_BY'=>$USER->GetID());
						$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true, array('ID',"NAME"));
						while($ar_result = $db_list->GetNext())
						{?>
							<option value="<?=$ar_result['ID']?>"><?=$ar_result['NAME']?></option>
						<?}
					
					endif;?>
					</select>
			</div>
			<div class="b-login__user"><input type="text" class="b-cart-field__input" placeholder="Новый вишлист" value="" /></div>
			<div class="clearfix"><a id='wishlist_add_el' el='3' class="b-button__fast_n">OK</a></div>
		</div>
	</div>
            <br /><br />
            <button class="b-button" id="b-fast_order"><?echo GetMessage("CATALOG_FUST_ORDER")?></button>
            <div class="b-fast_order m-detail-fast_order fust_order">
                <form action="/includes/fust_order.php" name="fust_order" method="post">
                    <input type="text" class="b-cart-field__input" placeholder="<?echo GetMessage("FUST_ORDER_PHONE")?>" name="phone"/>
                    <input type="hidden" name="order" value=""/>
                    <div class="b-fast_order__text">Вам перезвонит оператор и оформит заказ</div>
                    <input type="submit" class="b-button__fast m-fast_order" id="fust_order-submit" value="<?echo GetMessage("CATALOG_FUST_ORDER_BUTTON")?>" />
                </form>
            </div>
        </div>
        <div class="b-detail-sidebar__delivery">Доставка 300.– в пределах МКАД<br /><br /><?if($country):?>Страна-производитель: <?=$country?><?endif;?><br /><?if($garancy):?>Гарантия :<?=$garancy?><?endif;?></div>
   
        <div class="b-other-method"><?echo GetMessage("CATALOG_OTHER_ORDER")?></div>
        <div class="b-other-method__wrapper">
            <div><b><?echo GetMessage("CATALOG_BACK_CALL")?></b></div>
            <div class="fust_order">
                <form action="/includes/fust_order.php" name="fust_order" method="post">
                    <div class="b-footer-form">
                        <input type="text" class="b-footer-form__text" placeholder="<?echo GetMessage("CATALOG_YOUR_PHONE_NUMBER")?>" name="phone"/>
                        <input type="hidden" name="order" value=""/>
                        <input type="submit" class="b-footer-form__submit" value="" id="fust_order-submit"/>

                    </div>
                </form>
            </div>
            <br />
            <div><?echo GetMessage("CATALOG_ORDER_TEXT")?></div>
            <br /><br />
            <div><b><?echo GetMessage("CATALOG_ONLINE_CALL")?></b></div>
            <div><a href="callto:echo" class="b-button__fast"><?echo GetMessage("CATALOG_ONLINE_CALL_BUTTON")?></a></div>
            <br />
            <div><b><?echo GetMessage("CATALOG_FUST_ORDER")?></b></div>
            <br />
            <div>235-979-794 Андрей<br />609-690-565 Николай</div>
        </div>
    </div>
</article>
