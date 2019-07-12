<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><!--start_html-->
<div class="col3-list stuff-box" id="elements">
	<div class="product-count-bottom">
		<span>
 <?= pluralForm($arResult['NavRecordCount'], GetMessage("FOUND_LABEL1"), GetMessage("FOUND_LABEL"), GetMessage("FOUND_LABEL")) ?>
 <?= $arResult['NavRecordCount']; ?>
 <?= pluralForm($arResult['NavRecordCount'], GetMessage("MODEL_ONE"), GetMessage("MODEL_MANY"), GetMessage("MODEL_OF")) ?>
        </span>
	</div>
    <?
    if(is_array($arParams['orderRows']) && count($arParams['orderRows'])>0):
    ?>
	<div class="choice-issuance">
        <form method="post">
            <label for="arOrderSortCatalog"><?= GetMessage('SORT_BY') ?></label>
            <select id="arOrderSortCatalog" name="arOrder" class="selectpicker show-tick span2">
                <?php
                foreach ($arParams['orderRows'] as $row => $params) {
                    $selected = (key($arParams['currentOrder']) == $row) ? 'selected="selected"' : '';
                    ?>
                    <option <?= $selected ?> value="<?= $row ?>"><?= $params['NAME'] ?></option>
                <?php
                }
                ?>
            </select>
        </form>
	</div>
    <?php
    endif;
    ?>
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
                        $val["IBLOCK_ID"],
                        $val["ID"],
                        0,
                        array("SECTION_BUTTONS"=>false, "SESSID"=>false)
                    );
                    $val["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];

                    $this->AddEditAction($val['ID'], $val["EDIT_LINK"], CIBlock::GetArrayByID($val["IBLOCK_ID"], "ELEMENT_EDIT"));

                    if ($row == 1) {
                        ?>
                        <div class="item-block">
                    <?
                    }
                    $idElem = $this->GetEditAreaId($val['ID']);
                    ?>
                    <div class="item" id="<?=$idElem?>" data-catalog-id="<?=$val['ID']?>" data-iblock-id="<?=$arParams["CATALOG_IBLOCK_ID"]?>"><?//=$row;?>
                        <div class="over item-visible-content">
                            <?php
                            $SECTION = GetIBlockSection($val['IBLOCK_SECTION_ID']);
                            ?>
							<? $APPLICATION->IncludeComponent(
                                "novagr.shop:catalog.element.preview",
                                "",
                                Array(
                                    "SORT_FIELD" => "ID",
                                    "SORT_BY" => "DESC",
                                    "CATALOG_IBLOCK_TYPE" => $arParams['CATALOG_IBLOCK_TYPE'],
                                    "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
                                    "CATALOG_OFFERS_IBLOCK_ID" => $arParams['OFFERS_IBLOCK_ID'],
                                    "ARTICLES_IBLOCK_ID" => $arParams['ARTICLES_IBLOCK_ID'],
                                    "FASHION_IBLOCK_ID" => $arParams['FASHION_IBLOCK_ID'],
                                    "SAMPLES_IBLOCK_CODE" => $arParams['SAMPLES_IBLOCK_CODE'],
                                    "BRANDNAME_IBLOCK_CODE" => $arParams['BRANDNAME_IBLOCK_CODE'],
                                    "COLORS_IBLOCK_CODE" => $arParams['COLORS_IBLOCK_CODE'],
                                    "MATERIALS_IBLOCK_CODE" => $arParams['MATERIALS_IBLOCK_CODE'],
                                    "STD_SIZES_IBLOCK_CODE" => $arParams['STD_SIZES_IBLOCK_CODE'],
                                    "INET_MAGAZ_ADMIN_USER_GROUP_ID" => $arParams['INET_MAGAZ_ADMIN_USER_GROUP_ID'],
                                    //"CACHE_TYPE" => $arParams['CACHE_TYPE'],
									"CACHE_TYPE" => "N",
                                    "CACHE_TIME" => $arParams['CACHE_TIME'],
                                    "SET_TITLE" => "N",
                                	"SHOW_SUBSCRIBED" => "N",
                                    "DISABLE_QUICK_VIEW" => $arParams['DISABLE_QUICK_VIEW'],
                                    "COMPONENT_CURRENT_PAGE" => $arParams['ROOT_PATH'].$SECTION['CODE']."/".$val['CODE']."/",
                                    "arOfferRequest" => $_REQUEST['arOffer'],
                                    "ELEMENT_ID"=>$val['ID'],
                                    "PRICE_ID" => ($arResult['OPT_USER'] == 1 && $arParams["OPT_PRICE_ID"]>0 ? $arParams["OPT_PRICE_ID"] : ''),
                                ),
                                false,
                                Array(
                                    'ACTIVE_COMPONENT' => 'Y',
                                    //"HIDE_ICONS"=>"Y"
                                )
                            );?>
                        </div>
                        <?php
                        if ($arParams['DISABLE_QUICK_VIEW'] !== 'Y'): ?>
                            <div class="preview-info-boxover" data-catalog-id="<?=$val['ID']?>" style="display: none;">
                                <div class="middle item-invisible-content">
                                </div>
                            </div>
                        <?php
                        endif;
                        ?>

                    </div>
                    <?
                    if ($row == 4)
                    {
                        ?>
                        </div>
                        <?
                        $row = 1;
                    } else $row++;
                } // end foreach($arResult['ELEMENT'] as $val)

                if ($row>1) {
                ?>
            </div>
            <?php
            }
            ?>
        </div>
        <?

        }

        ?>
        <div class="clear"></div>

    </div>
</div>

<div class="product-count-bottom bot-p">
		<span>
 <?= pluralForm($arResult['NavRecordCount'], GetMessage("FOUND_LABEL1"), GetMessage("FOUND_LABEL"), GetMessage("FOUND_LABEL")) ?>
 <?= $arResult['NavRecordCount']; ?>
 <?= pluralForm($arResult['NavRecordCount'], GetMessage("MODEL_ONE"), GetMessage("MODEL_MANY"), GetMessage("MODEL_OF")) ?>
        </span>
</div>
<div class="pagination pagination-right bot-p">
    <?=$arResult['NAV_STRING'];?>
</div>
<div class="clear"></div>

<?php
if (trim($arResult['SEO_DATA']['HEADER']) <> "" || trim($arResult['SEO_DATA']['DESCRIPTION']) <> "") {
    ?>
    <div class="info-block">
        <?php
        if (trim($UF_TITLE_H1 = $arResult['SEO_DATA']['HEADER']) <> "") print "<h1>{$UF_TITLE_H1}</h1>";
        echo $arResult['SEO_DATA']['DESCRIPTION'];
        ?>
    </div>
<?php
}
?>
<script type="text/javascript">
    MODAL_PROPERTY_STD_SIZE = [];
</script>
<?php
$PROPERTY_STD_SIZE = $PROPERTY_COLOR = array();

$arOffer = (array)$_REQUEST['arOffer'];

foreach($arOffer as $param)
{
    if(array_key_exists("PROPERTY_COLOR",$param))
    {
        unset($arParams["SEARCH_COLORS"]);
    }
}

if(count($arParams["SEARCH_COLORS"])>0)
{
    foreach($arParams["SEARCH_COLORS"] as $SEARCH_COLOR)
    {
        $arOffer[] = array( "PROPERTY_COLOR" => $SEARCH_COLOR['ID']);
    }
}

if(is_array($arOffer))
{
    foreach($arOffer as $rOffer)
    {
        if (isset($rOffer['PROPERTY_STD_SIZE']))
        {
            $PROPERTY_STD_SIZE[] = (int)$rOffer['PROPERTY_STD_SIZE'];
            ?>
            <script type="text/javascript">
                $(document).ready(function(){

                    $(".button-size-button-<?=(int)$rOffer['PROPERTY_STD_SIZE']?>").unbind("click");
                    $('.color-catalog').each(function() {
                        $(this).find(".button-size-button-<?=(int)$rOffer['PROPERTY_STD_SIZE']?>:first").click();
                    });
                    <?/*$(".button-size-button-<?=(int)$rOffer['PROPERTY_STD_SIZE']?>").click();*/?>
                });
                MODAL_PROPERTY_STD_SIZE[MODAL_PROPERTY_STD_SIZE.length] = "<?=(int)$rOffer['PROPERTY_STD_SIZE']?>";
            </script><?php
        }
    }
    foreach($arOffer as $rOffer)
    {
        if (isset($rOffer['PROPERTY_COLOR']))
        {
            $PROPERTY_COLOR[] = (int)$rOffer['PROPERTY_COLOR'];
            ?>
            <script type="text/javascript">
                $(document).ready(function(){
                    $(".button-color-button-<?=(int)$rOffer['PROPERTY_COLOR']?>").unbind("click");
                    $(".button-color-button-<?=(int)$rOffer['PROPERTY_COLOR']?>").click();
                });
            </script><?php
        }
    }
    if(count($PROPERTY_COLOR)>0 && count($PROPERTY_STD_SIZE)>0)
    {
        ?>
        <script type="text/javascript">
            MODAL_PROPERTY_STD_SIZE = [];
        </script>
        <?
        foreach($PROPERTY_COLOR as $COLOR)
        {
            foreach($PROPERTY_STD_SIZE as $STD_SIZE)
            {
                ?>
                <script type="text/javascript">
                    $(document).ready(function(){
                        $(".button-color<?=$COLOR?>-size<?=$STD_SIZE?>").unbind("click");
                        $(".button-color<?=$COLOR?>-size<?=$STD_SIZE?>").click();
                    });
                    MODAL_PROPERTY_STD_SIZE[MODAL_PROPERTY_STD_SIZE.length] = "<?=$STD_SIZE?>";
                </script><?php
            }
        }
    }
}
?>
<?if ($arParams['DISABLE_QUICK_VIEW'] !== 'Y'): ?>
    <script type="text/javascript">
        var listProducts = {
            closedArr:[],
            currentID: 0,
            init: function() {
                /*catalog.element.preview - всплывающий быстрый просмотр*/
                $('.list .line .item').hover(
                    function() {
                        listProducts.currentID = $(this).data('catalog-id');

                        if ($.inArray( listProducts.currentID, listProducts.closedArr ) != -1) {
                            return;
                        }
                        var html = $(this).find('.item-visible-content').html();
                        $(this).find('.item-invisible-content').html(html);
                        $(this).find('.preview-info-boxover').stop(true).fadeIn("600");

                        $('.quickViewLink').unbind("click");
                        $('.quickViewLink').click(function(){

                            var w = loadPreviewElementModalWindow($(this).attr('href'),MODAL_PROPERTY_STD_SIZE);
                            return w;
                        });
                    },
                    function() {
                        var elemID = $(this).data('catalog-id');
                        if ($.inArray( listProducts.currentID, listProducts.closedArr ) == -1) {
                            listProducts.closedArr.push(elemID);
                        }
                        $(this).find('.preview-info-boxover').stop(true).fadeOut("600", function(){

                            for (var i in listProducts.closedArr) {

                                if ($(this).data('catalog-id') == listProducts.closedArr[i]) {
                                    delete listProducts.closedArr[i];
                                }
                            }
                        });
                    }
                );
            }
        };
        listProducts.init();
        $( document ).ajaxComplete(function() {
            $('.quickViewLink').unbind("click");
            $('.quickViewLink').click(function(){
                return loadPreviewElementModalWindow($(this).attr('href'),MODAL_PROPERTY_STD_SIZE);
            });
        });
        /*конец всплывающий быстрый просмотр*/
    </script>
<?php endif;?>
</div>
<!--end_html-->