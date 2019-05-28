<?php
use Bitrix\Main\Loader;

class MobileCatalog extends MHT\Product
{


    public function getBasketItemsAmount($russian = false)
    {
        $n = 0;

        CModule::IncludeModule('sale');

        WP::bit(array(
            'of' => 'basket',
            'f' => array(
                'LID' => SITE_ID,
                'FUSER_ID' => CSaleBasket::GetBasketUserID(true),
                'ORDER_ID' => 'NULL',
                'CAN_BUY' => 'Y',
                'DELAY' => 'N',
                'MODULE' => 'catalog'
            ),
            'each' => function($e, $f) use (&$n){
                $n ++; //= $f['QUANTITY'];
            }
        ));

        if(!$russian){
            return $n;
        }

        return $n.' товар'.WP::russianCountName($n, 'ов', '', 'а');

    }

    public static function checkIblockCode($sCode)
    {
        if (empty($sCode)) return false;
        Loader::includeModule("iblock");
        $res = \CIBlock::GetList(Array(),Array('TYPE'=>'mht_products',"CODE"=>$sCode), false);
        return $res->Fetch();
    }

    static function byID($id){
        $product = null;
        \WP::elements(array(
            'filter' => array(
                'ID' => $id,
                'IBLOCK_ID' => $iblock
            ),
            'each' => function($f, $p) use (&$product){
                $product = new static($f, $p);
                return false;
            }
        ));
        return $product;
    }

    public static function getSectionInfo( $iSectionId )
    {
        Loader::includeModule("iblock");
        $res = \CIBlockSection::GetByID( $iSectionId );
        if($arSection = $res->GetNext())
            return $arSection;
        return false;
    }


    function html($type, $data){
        // echo $type;
        ob_start();
        $rating = $this->get('rating');
        if($data['tpl']){
            $id = \WP::getEditElementID($this->get('iblock'), $this->get('id'), $data['tpl'], true);
        }
        $canBuy = $this->get('can-buy');

        switch($type){


            case 'basket':
                ?>
                <?if($this->get("delay") != 'Y'){?>
                <?
                $id_ = $this->get('id');
                $amount = $this->get('buy-amount');

                ?><div class="cartitem">
                    <div class="cartitemimg">
                        <img alt="" src="<?=$this->get('small-image', 'src')?>">
                    </div>
                    <div class="cartiteminfo">
                        <div class="cartiteminfotop">
                            <a href="<?=$this->get('link')?>">
                                <p class="cartitemname"><?=$this->get('name')?></p>
                            </a>
                            <div class="cartitemdelete"><i class="flaticon-cross"></i></div>
                        </div>
                        <div class="cartiteminfobot">
                            <div class="cartitemquantitywrap">
                                <a href="javascript:void(0)" class="minquantity">−</a>
                                <input class="amount-input" type="text" data-price="<?=$this->get('price-num')?>" data-buy-id="<?=$this->get('buy-id')?>" pattern="[0-9]{0,5}" value="<?php echo $amount;?>" placeholder="1">
                                <a href="javascript:void(0)" class="addquantity">+</a>
                                <p class="productit">упа</p>
                            </div>
                            <p class="cartitemprice itemprice"><?=$this->get('buy-amount-price')?></p>
                        </div>
                    </div>
                </div>

                <?/*
                <div class="row js-height-fit" data-id="<?=$this->get('buy-id')?>">
                    <input type="hidden" id="QUANTITY_<?=$id_?>" name="QUANTITY_<?=$id_?>" value="<?=$amount?>" />

                    <div class="col product_name_block">
                        <?=$this->html('images-zoom')?>
                        <a href="<?=$this->get('link')?>" class="product_name js-to-middle"><?=$this->get('name')?></a>
                    </div><!--
								--><div class="col one_price_block">
                        <div class="product_price">
                            <?
                            $old_price = $this->get('old-price');
                            if($old_price > 0) {
                                ?>
                                <span class="old-price-block"><?=$old_price?> <span class="rub"><span>рублей</span></span></span>
                                <?
                            }
                            ?>
                            <span class="product_price_value js-price" data-value="<?=$this->get('price-num')?>">
        <?=$this->get('price')?></span> <span class="rub"><span>рублей</span></span>
                        </div>
                    </div><!--
								--><div class="col count_selector_block">
                        <input class="count_selector" id="QUANTITY_INPUT_<?=$id_?>" name="QUANTITY_INPUT_<?=$id_?>" value="<?=$amount?>"><span class="unit"><?=$this->sBaseUnit?></span>
                    </div><!--
								--><div class="col all_price_block">
                        <div class="product_price"><span class="product_price_value js-price-total"><?=$this->get('buy-amount-price')?></span> <span class="rub"><span>рублей</span></span></div>
                    </div><!--
								--><div class="col remove_block">
                        <a href="#" class="remove">&times;</a>
                    </div>
                </div>*/?>
            <?}?>
                <?
                break;


            case 'rating':

                $iStarsAmount = intval($this->get('rating')/20);
                ?><div class="divstars">
                    <?for($i=0;$i<$iStarsAmount;$i++):?>
                        <i class="flaticon-star"></i>
                    <?endfor?>
                </div><?
                break;

            case 'catalog':
                ?>
                <div class="productitem">
                    <div class="photoblock">
                        <?=$this->html('image')?>
                    </div>
                    <div class="informationblock">
                        <a href="<?=$this->get('link')?>">
                            <h3><?=$this->get('name')?></h3>
                        </a>
                        <div class="articulblock">
                            <p class="articul"><?=$this->get('itemcode')?></p>
                            <?=$this->html('rating')?>

                        </div>
                        <p class="fullname"><?=$this->get('short-description')?></p>
                        <div class="priceblock">
						
							
							
                                <?
                                $old_price = $this->get('old-price');
                                if($old_price > 0) {
                                    ?>
									<p class="price product_old_price">
									<?=$old_price?>
									</p>
									<?
                                }
                                ?>
							
                            <p class="price"><?php echo $this->get('price');?></p>
							<a href="<?=$this->get('link')?>" class="tocart product_cart" data-id="<?=$this->get('id')?>" ><i class="flaticon-shopping-basket-1"></i></a>
                        </div>
                    </div>
                </div>
                <?
                break;

            case 'image':
                $size = empty($data['size']) ? 'small' : $data['size'];
                $class = 'product_image';
                $attrs = '';
                if(
                    !empty($data['zoomy']) &&
                    $data['zoomy'] == true
                ){
                    $image = $this->getImage('original');
                }

                ?>
                <a href="<?=$this->get('link')?>">
                    <img class="product_image_original" src="<?=$this->get($size.'-image', 'src')?>" alt="">
                </a>
                <?
                break;

            case 'main':
                global $APPLICATION;
                $sections = $this->getSections();
                $category = $sections[0];
                ?>
                <div class="headingline inner">
                    <p class="heading"><?=$this->get('name')?> <?=$this->get('id')?></p>
                </div>
                <div class="catalogitem"   data-id="<?=$this->get('id')?>"
                     data-name="<?=$this->get('name')?>"
                     data-price="<?=$this->get('price-num')?>"
                     data-category=""
                     data-sku="<?=$this->get('article')?>" >
                    <div class="catalogitemrate">
                        <?=$this->html('rating', array(
                            'people' => true
                        ))?>
                    </div> 

                    <div class="catalogitemsliderwrap<?if($this->get("isaction")){?> action<?}elseif($this->get("isnew")){?> novinka<?}?>">
                        <ul class="catalogitemslider">

                            <?
                            $id_detail = $this->fields['DETAIL_PICTURE']['ID'];
                            if($id_detail > 0) {
                                $src = \CFile::GetPath($id_detail);
                                ?><li><img alt="" src="<?=$src?>"></li><?
                            }

                            foreach($this->properties['MORE_PHOTO']['VALUE'] as $id){
                                $src = \CFile::GetPath($id);
                                ?><li><img alt="" src="<?=$src?>"></li><?
                            }
                            foreach($this->properties['VIDEO_LINK_IMAGE']['VALUE'] as $id){
                                $img = \CFile::GetFileArray($id);
                                ?><li><img alt="" src="<?=$img['SRC']?>"></li><?
                            }
                            ?>
                        </ul>
                        <div class="sliderpagerwrap">
                            <div class="pagerarrowtop"><i class="flaticon-bottom"></i></div>
                            <div id="catalogitemslider-pager">
                                <?
                                $iPhotoNumber = 0;
                                $id_detail = $this->fields['DETAIL_PICTURE']['ID'];
                                if($id_detail > 0) {
                                    $small = $this->resizeImage($id_detail,'smallest');
                                    ?><a data-slide-index="<?php echo $iPhotoNumber++;?>" href=""><img alt="" src="<?=$small['src']?>"></a><?
                                }

                                foreach($this->properties['MORE_PHOTO']['VALUE'] as $id){

                                    $small = $this->resizeImage($id,'smallest');

                                    ?><a data-slide-index="<?php echo $iPhotoNumber++;?>" href=""><img alt="" src="<?=$small['src']?>"></a><?
                                }
                                foreach($this->properties['VIDEO_LINK_IMAGE']['VALUE'] as $id){

                                    $small = $this->resizeImage($id,'smallest');
                                    
                                    ?><a data-slide-index="<?php echo $iPhotoNumber++;?>" href=""><img alt="" src="<?=$small['src']?>"></a><?
                                } 
                                ?>
                            </div>
                            <div class="pagerarrowbot"><i class="flaticon-bottom"></i></div>
                        </div>
                    </div>
                    <div class="catalogitempriceblock">
                        <? if($canBuy){  ?>                            
                                <?
                                $old_price = $this->get('old-price');
                                if($old_price > 0) {
                                    ?>
									<p class="catalogitemprice itemprice product_old_price">	
										<?=$old_price?>
									</p>
								<?
                                }
                                ?>
							<p class="catalogitemprice itemprice">
								<?=$this->get('price')?>
                            </p>
                        <? }?>
                        <div class="catalogitemquantitywrap">
                            <a href="javascript:void(0)" class="minquantity">−</a>
                            <input type="text" class="amount-input" data-price="<?php echo $this->get('price-num');?>" pattern="[0-9]{0,5}" value="1" placeholder="1">
                            <a href="javascript:void(0)" class="addquantity">+</a>
                            <p class="productit"><?=$this->sBaseUnit?></p>
                        </div>
                    </div>
                    <div class="catalogitembuttonsblock">
                        <? if($canBuy){ ?>
                            <?/*<div class="buttons only-mobile">
                                <a href="<?=$this->get('buy')?>" class="product_catalog-element js-buy">в корзину</a>
                            </div>*/?>
                        <a href="<?=$this->get('link')?>" data-id="<?=$this->get('id')?>" data-amount="1" class="product_cart greenbutton">В корзину</a>
                        <? } else { ?>
                            <div class="not-in-stock">
                                Скоро в наличии
                            </div>
                        <?}?>
                        <?/*<a href="#"  data-id="<?=$this->get('id')?>" onclick="$('#one-click-order-wrapper').toggle();return false;" class="greenhollowbutton order-one-click">Заказать в один клик</a>*/?>

                        <div id="one-click-order-wrapper" style="display: none;">
                            <?=$this->html('one-click-order')?>
                        </div>

                    </div>
                    <div class="catalogitemartcodeblock">
                        <?
                        $itemcode = $this->get('itemcode');
                        if($itemcode) {
                            ?><p>код: <span><?=$itemcode?></span></p><?
                        } ?><p>артикул: <span><?=$this->get('article')?></span></p><!--
					                    -->
                    </div>
                    <?/*
                    <div class="catalogitemlinksblock">
                        <a class="js-compare-change compare-yes" href="/ajax.php?action=compare-delete&amp;id1=<?=$this->get('iblock').'&amp;id2='.$this->get('id')?>">убрать из сравнения</a>
                        <a class="js-compare-change compare-no" href="<?=$this->get('root-url')?>compare/?action=ADD_TO_COMPARE_RESULT&amp;id=<?=$this->get('id')?>">в сравнение</a>
                        <a href="#" class="js-fav-change fav-no" data-id="<?=$this->get('id')?>">в избранное</a>
                        <a href="#" class="js-fav-change fav-yes" data-id="<?=$this->get('id')?>">убрать из избранного</a>
                    </div>*/?>

                    <div class="catalogitemdescription">
                        <p><?=$this->get('description')?></p>
                    </div>
                    <div class="catalogitemstats">
                        <ul>
                            <?
                            $i = 0;
                            $predef = array(
                                'Y' => 'да',
                                'N' => 'нет',
                            );

                            foreach($this->getCharacteristics() as $property){
                                $value = $property['VALUE'];

                                if(
                                    !$value ||
                                    preg_match('/^CML2_/', $property['NAME']) ||
                                    preg_match('/^Сайт_/iU', $property['NAME']) ||
                                    preg_match('/^Импортер/iU', $property['NAME']) ||
                                    preg_match('/^Производитель и адре/iU', $property['NAME']) ||
                                    preg_match('/^Страна произво/iU', $property['NAME']) ||
                                    preg_match('/^Прием претен/iU', $property['NAME']) ||
                                    preg_match('/^Старая цена/iU', $property['NAME']) ||
                                    preg_match('/^Тариф AdmitAd/iU', $property['NAME']) ||
                                    preg_match('/^СайтБезСкидки/iU', $property['NAME']) ||
                                    preg_match('/^Маркет/iU', $property['NAME'])
                                ){
                                    continue;
                                }
                                if(is_array($value)){
                                    $value = implode(', ',$value);
                                }
                                if(isset($predef[$value])){
                                    $value = $predef[$value];
                                }
                                if($property['CODE'] == 'CML2_MANUFACTURER'){
                                    $value = '<a href="/brand/'.strtolower($property['VALUE']).'/">'.$value.'</a>';
                                }

                                $property['NAME'] = str_replace(
                                    array(
                                        "ДлиннаОбщий",
                                        "ШиринаОбщий",
                                        "ВысотаОбщий",
                                        "МатериалОбщий",
                                        "ОбъемОбщий"
                                    ),
                                    array(
                                        "Длинна",
                                        "Ширина",
                                        "Высота",
                                        "Материал",
                                        "Объем"
                                    ),
                                    $property['NAME']
                                );

                                ?><li<?=$i > 19 ? ' class="hidden" style="display:none"' : ''?>><p><span><?=$property['NAME']?></span></p><p><?=$value?></p></li><?
                                $i++;
                            }
                            ?>
                        </ul>
                        <? if($i > 20){ ?>
                            <a href="#" class="all_features" onclick="$('#product_properties_table .hidden').show(); $(this).hide(); return false;">все характеристики</a>
                        <? } ?>
                    </div>
                    <?/*
                    <div class="catalogitemsocialshare">
                        <ul>
                            <li><a href="#"><i class="flaticon-vk-social-logotype"></i></a></li>
                            <li><a href="#"><i class="flaticon-facebook-logo"></i></a></li>
                            <li><a href="#"><i class="flaticon-instagram-symbol"></i></a></li>
                            <li><a href="#"><i class="flaticon-odnoklassniki-logo"></i></a></li>
                        </ul>
                    </div>*/?>
                    <?
                    $APPLICATION->IncludeComponent('itsfera:same_products', 'mobile', array(
                        'IBLOCK_ID' => $this->fields['IBLOCK_ID'],
                        'SECTION_ID' => $this->fields['IBLOCK_SECTION_ID'],
                        'ELEMENT_ID' => $this->fields['ID'],
                        'ELEMENTS_COUNT'=>6
                    ));
                    ?>
                </div>
                <?
                break;

            case 'order':
                $id_ = $this->get('id');
                $amount = $this->get('buy-amount');
                ?>
                <div class="orderformitem">
                    <div class="orderformitemimg">
                        <?=$this->html('image')?>
                    </div>
                    <div class="orderformiteminfo">
                        <div class="orderformiteminfotop">
                            <a href="<?=$this->get('link')?>" target="_blank">
                                <p class="cartitemname"><?=$this->get('name')?></p>
                            </a>
                        </div>
                        <div class="orderformiteminfobot">
                            <p class="cartitempriceforone">
                                <?
                                $old_price = $this->get('old-price');
                                if($old_price > 0) {
                                    ?>
                                    <span class="old-price-block"><?=$old_price?> <span class="rub"><span>рублей</span></span></span>
                                    <?
                                } ?><?=$this->get('price')?>
                            </p>
                            <p class="cartitemquantity"><?=$amount?> <?=$this->sBaseUnit?></p>
                            <p class="cartitempriceforall"><?=$this->get('buy-amount-price')?></p>
                        </div>
                    </div>
                </div><?
                break;

            case 'one-click-order':


$iIblockId = getIBlockIdByCode("one_click_order");

$GLOBALS['APPLICATION']->IncludeComponent(
    "bitrix:iblock.element.add.form",
    "one_click_order",
    Array(
        "HIDDEN_PROPERTIES"=>array(
            getPropertyIdByCode("ELEMENT_ID",$iIblockId)=>$this->get('id'),
            getPropertyIdByCode("ART",$iIblockId)=>$this->get('article'),
            getPropertyIdByCode("PRICE",$iIblockId)=>$this->get('price-num'),
            getPropertyIdByCode("QUANTITY",$iIblockId)=>1,
            "NAME"=>$this->get('name')
        ),
        "PLACEHOLDERS"=>array(
            "PHONE"=>"+7 ( ___ ) ___ - __ - __",

        ),
        "COMPONENT_TEMPLATE" => ".default",
        "CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
        "CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
        "CUSTOM_TITLE_DETAIL_PICTURE" => "",
        "CUSTOM_TITLE_DETAIL_TEXT" => "",
        "CUSTOM_TITLE_IBLOCK_SECTION" => "",
        "CUSTOM_TITLE_NAME" => "",
        "CUSTOM_TITLE_PREVIEW_PICTURE" => "",
        "CUSTOM_TITLE_PREVIEW_TEXT" => "",
        "CUSTOM_TITLE_TAGS" => "",
        "DEFAULT_INPUT_SIZE" => "30",
        "DETAIL_TEXT_USE_HTML_EDITOR" => "N",
        "ELEMENT_ASSOC" => "CREATED_BY",
        "GROUPS" => array("2"),
        "IBLOCK_ID" => $iIblockId,
        "IBLOCK_TYPE" => "orders",
        "LEVEL_LAST" => "Y",
        "LIST_URL" => "",
        "MAX_FILE_SIZE" => "0",
        "MAX_LEVELS" => "100000",
        "MAX_USER_ENTRIES" => "100000",
        "PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
        "PROPERTY_CODES" => array(
            'USER_NAME'=>getPropertyIdByCode("USER_NAME",$iIblockId),
            'PHONE'=>getPropertyIdByCode("PHONE",$iIblockId),
            'ELEMENT_ID'=>getPropertyIdByCode("ELEMENT_ID",$iIblockId),
            'ART'=>getPropertyIdByCode("ART",$iIblockId),
            'PRICE'=>getPropertyIdByCode("PRICE",$iIblockId),
            'QUANTITY'=>getPropertyIdByCode("QUANTITY",$iIblockId),
            "NAME"=>"NAME"
        ),
        "PROPERTY_CODES_REQUIRED" => array(
            'PHONE'=>getPropertyIdByCode("PHONE",$iIblockId),
        ),
        "RESIZE_IMAGES" => "N",
        "SEF_MODE" => "N",
        "STATUS" => "ANY",
        "STATUS_NEW" => "N",
        "USER_MESSAGE_ADD" => "",
        "USER_MESSAGE_EDIT" => "",
        "USE_CAPTCHA" => "N"
    )
);
                break;

        }
        return ob_get_clean();
    }


    function resizeImage($id, $size = 'big'){
        $sizes = array(
            'smallest' => array(
                'width' => 50,
                'height' => 50
            ),
            'small' => array(
                'width' => 250,
                'height' => 210
            ),
            'big' => array(
                'width' => 465,
                'height' => 330
            )
        );

        $image = false;

        if(!empty($id)){
            if(isset($sizes[$size])){
                $image = \CFile::ResizeImageGet($id, array('width'=>$sizes[$size]["width"], 'height'=>$sizes[$size]["height"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            }
            if(!$image){
                $r = \CFile::GetFileArray($id);
                if($r){
                    $image = array(
                        'width' => $r['WIDTH'],
                        'height' => $r['HEIGHT'],
                        'src' => $r['SRC'],
                    );
                }
            }
        }
        if(!$image){
            $image = array(
                'src' => '/local/templates/mht/components/bitrix/catalog/mht/bitrix/catalog.section/.default/images/no_photo.png',
                'alt' => '',
                'width' => 150,
                'height' => 150,
                'default' => true
            );
        }

        return $image;

        /*
        if(!empty($id)){
            if(isset($sizes[$size])){
                return \CFile::ResizeImageGet($id, array('width'=>$sizes[$size]["width"], 'height'=>$sizes[$size]["height"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            }else{
                $r = \CFile::GetFileArray($id);
                return array(
                    'width' => $r['WIDTH'],
                    'height' => $r['HEIGHT'],
                    'src' => $r['SRC'],
                );
            }
        }else{
            return array(
                'src' => '/local/templates/mht/components/bitrix/catalog/mht/bitrix/catalog.section/.default/images/no_photo.png',
                'alt' => '',
                'width' => 150,
                'height' => 150,
                'default' => true
            );
        }*/
        /*
        if(isset($sizes[$size])){
            $hadResized = WP::hasResized(
                $id,
                $sizes[$size]
            );

            $img = \CFile::ResizeImageGet(
                $id,
                $sizes[$size]
            );

            \WP::get('wideimage');

            if(!$hadResized){
                try{
                    $image = \WideImage::load($_SERVER['DOCUMENT_ROOT'].\CFile::GetPath($id));
                    $image = $image->resize(
                        $sizes[$size]['width'],
                        $sizes[$size]['height'],
                        'outside'
                    );
                    //$image = $image->applyFilter(IMG_FILTER_BRIGHTNESS, 5);
                    //$image = $image->applyFilter(IMG_FILTER_CONTRAST,  -10);
                    $image->saveToFile($_SERVER['DOCUMENT_ROOT'].$img['src'], 90);
                }
                catch(Exception $e){
                    \WP::log($e);
                }
            }
            return $img;
        }

        $r = \CFile::GetFileArray($id);
        return array(
            'width' => $r['WIDTH'],
            'height' => $r['HEIGHT'],
            'src' => $r['SRC'],
        );
        */
    }


}