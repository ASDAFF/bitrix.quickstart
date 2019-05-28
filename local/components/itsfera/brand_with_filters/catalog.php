<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

define('SEARCH_ITEMS_COUNT', 16);
define('VISIBLE_ITEMS_COUNT', 10);

error_reporting(E_ERROR); //| E_NOTICE
ini_set('display_errors', 1);

use Bitrix\Main\Loader;

Loader::includeModule("iblock");
$arResult = \Bitrix\Main\Web\Json::decode($_POST['data']);


if ( ! isset($arResult['results']['items'][0]) && (strpos($_POST['params'], 'discount=1') === false)) {
    //нет результатов
    exit('empty');
}

$iOffset = intval($_POST['offset']);

//выдаем просто блоки для js подрузки
if ($iOffset > 0) {

    $APPLICATION->RestartBuffer();

    foreach ($arResult['results']['items'] as $iItemId) {
        echo MHT\Product::byID($iItemId)->html('catalog');
    }

} else {


    if (isset($arResult['results']['total'])):
        /*?><p>Найдено: <?=$arResult['results']['total']?></p><?*/
    endif;

    ?>

    <div class="catalog_page">
        <div class="catalog_block">

            <?

            if ((strpos($_POST['params'], 'autoload') !== false) && $_COOKIE['search_request']) {

                $_POST['params'] = $arResult['params'] = \Bitrix\Main\Web\Json::decode($_COOKIE['search_request']);

            } else {
                //разбираем параметры в массив
                foreach (explode('&', trim($_POST['params'])) as $chunk) {
                    list($key, $val) = explode("=", $chunk);
                    if ($key) {
                        $arResult['params'][$key] = $val;
                    }
                }
            }
            ?>

            <?//if ( isset($arResult['results']['filters']) ){

            //пересобираем фильтры в нужном порядке для вывода
            $arFilters['category'] = [];
            $arFilters['status']   = [];
            $arFilters['others']   = [];
            foreach ($arResult['results']['filters'] as $arFilter) {
                switch ($arFilter['name']) {
                    case 'category':
                        $arFilters['category'] = $arFilter;
                        break;
                    case 'Статус':
                        $arFilters['status'] = $arFilter;
                        break;
                    default:
                        $arFilters['others'][] = $arFilter;
                }
            }
            unset($arResult['results']['filters']);
            $arResult['results']['filters'][] = $arFilters['category'];
            $arResult['results']['filters'][] = $arFilters['status'];
            $arResult['results']['filters']   = array_merge($arResult['results']['filters'], $arFilters['others']);


            //dm( $arResult['results']['filters'] );

            ?>
            <div class="filter_aside filters catalog_menu" id="search_filters">
                <div class="search_results">
                    <div class="search_result_list">

                        <a href="" id="reset-filter-button">Сбросить фильтр</a>

                        <?


                        foreach ($arResult['results']['filters'] as $kf => $arFilter) {

                            $arFilter['title'] = $arFilter['name'];

                            if ($arFilter['name'] == 'vendor') {
                                continue;
                            }
                            if ($arFilter['name'] == 'Цена') {
                                continue;
                            }
                            if ($arFilter['name'] == 'discount') {
                                continue;
                            }

                            if ($arFilter['name'] == 'category') {

                                $arSections      = [];
                                $arSectionsNames = [];


                                foreach ($arFilter['values'] as $arSection) {
                                    $arSections[] = $arSection['value'];
                                }
                                $arSectionFilter = Array('ID' => $arSections);


                                $db_list = CIBlockSection::GetList(Array($by => $order), $arSectionFilter, true);
                                while ($arSection = $db_list->GetNext()) {

                                    //dump( $arSection , __FILE__, __LINE__ );
                                    $arSectionsNames[$arSection['ID']] = array(
                                        "NAME" => $arSection['NAME'],
                                        //"COUNT" => $arSection['ELEMENT_CNT']
                                    );
                                }

                                ?>
                                <p class="heading">Найдены товары в категориях:</p>
                                <ul class="search-found-sections">
                                <?
                                $bShowHidden = false;
                                usort($arFilter['values'], function ($ar1, $ar2) {
                                    return $ar1['count'] < $ar2['count'];
                                });

                            foreach ($arFilter['values'] as $kv => $arValue){
                                if ( ! array_key_exists($arValue['value'], $arSectionsNames)) {
                                    continue;
                                }
                                ?>
                                <li class="search-found-section">
                                    <a href="javascript:;">
                                        <div class="search-found-section-title categories category<?= $arValue['value'] ?>">
                                            <label>
                                                <input type="checkbox" name="<?= $arFilter['name'] ?>"
                                                       value="<?= $arValue['value'] ?>" style="display: none">
                                                <span class="catname"><?= $arSectionsNames[$arValue['value']]['NAME'] ?>
                                                    [<?= $arValue['count'] ?>]</span>
                                            </label>
                                            <?/*<span class="elements-count"></span>*/
                                            ?>
                                        </div>
                                    </a></li>
                            <?
                            if ($kv >= VISIBLE_ITEMS_COUNT - 1 && ! $bShowHidden):
                            $bShowHidden = true;
                            ?>
                                <div class="hidden_list">
                            <?
                            endif ?>

                            <?
                            }
                                ?>
                                <?
                            if ($bShowHidden): ?>
                                </div>
                                <li class="showhidden"><a href="javascript:void(0)">Показать скрытые
                                        (<?= count($arFilter['values']) - VISIBLE_ITEMS_COUNT ?>)</a></li>
                                <li class="hidehidden" style="display: none"><a href="javascript:void(0)">Минимизировать
                                        список</a></li><?
                            endif ?>
                                </ul><?
                            } else {
                                if ($arFilter['title']) { ?>
                                    <p class="heading"><?= $arFilter['title'] ?>:</p>
                                    <ul class="search-found-sections filter">
                                        <?
                                        $bShowHidden = false;

                                        usort($arFilter['values'], function ($ar1, $ar2) {
                                            return $ar1['count'] < $ar2['count'];
                                        });

                                        foreach ($arFilter['values'] as $kv => $arValue) {

                                        ?>
                                    <li>
                                        <a href="javascript:;" class="search-found-section">
                                            <div class="search-found-section-title filteritems">
                                                <label>
                                                    <input type="checkbox"
                                                           name="<?= str_replace(' ', '_', $arFilter['name']) ?>"
                                                           value="<?= str_replace(' ', '+', $arValue['value']) ?>"
                                                           style="display: none">
                                                    <span class="catname"><?= $arValue['value'] ?>
                                                        [<?= $arValue['count'] ?>]</span>
                                                </label>
                                            </div>
                                        </a></li>

                                    <?
                                    if ($kv >= VISIBLE_ITEMS_COUNT - 1 && ! $bShowHidden):
                                    $bShowHidden = true;
                                    ?>
                                        <div class="hidden_list"><?
                                            endif ?>

                                            <?

                                            }; ?><?
                                            if ($bShowHidden): ?>
                                        </div>
                                        <li class="showhidden"><a href="javascript:void(0)">Показать скрытые
                                                (<?= count($arFilter['values']) - VISIBLE_ITEMS_COUNT ?>)</a></li>
                                        <li class="hidehidden" style="display: none"><a href="javascript:void(0)">Минимизировать
                                                список</a></li><?
                                    endif ?>
                                    </ul>
                                    <?
                                }
                            }
                        }

                        ?>        </div>
                </div>
            </div>
            <?
            //}


            ?>


            <div class="catalog">


                <div class="filter_block search">
                    <div class="filter_block_top">
                        <div class="sort_block">
                            <span class="sort_block_title">сортировать по</span>
                            <div class="sort_block_list">
                                <select id="change_sort_search" style="display: none;">
                                    <option value="5" <?= ($arResult['params']['order'] == '0' ? 'selected="selected"' : '') ?>>
                                        (выберите сортировку)
                                    </option>
                                    <option value="price_desc" <?= ($arResult['params']['order'] == 'price_desc' ? 'selected="selected"' : '') ?>>
                                        уменьшению цены
                                    </option>
                                    <option value="price_asc" <?= ($arResult['params']['order'] == 'price_asc' ? 'selected="selected"' : '') ?>>
                                        увеличению цены
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="filter-prices discount-checkbox">


                            <label>
                                <input type="checkbox" class="filter-cleared" id="change_discount_search"
                                       name="discount_search"
                                       value="1" <?= $arResult['params']['discount'] == '1' ? "checked" : "" ?>>
                                <span>ТОВАРЫ СО СКИДКОЙ</span>
                            </label>


                        </div>


                    </div>
                </div>


                <?
                if (isset($arResult['results']['items'][0])) {
                    ?>

                    <div class="products_block">
                        <div class="products_block js-fit js-upload row<? if ( ! empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])) { ?> block<? } ?>">

                            <? $APPLICATION->IncludeComponent(
                                "itsfera:detectum_search",
                                "",
                                Array("ITEMS_IDS" => $arResult['results']['items'])
                            ); ?>

                        </div>
                        <?

                        $iItemsOnPageCount = SEARCH_ITEMS_COUNT;
                        $iItemsCountAll    = $arResult['results']['total'];
                        $iPagesCount       = ceil($iItemsCountAll / $iItemsOnPageCount);

                        if ($iPagesCount > 1) {
                            ?>
                            <div class="pagination load">

                                <a class="page-loader"
                                   data-pages="<?= $iPagesCount ?>"
                                   data-items="<?= $iItemsCountAll ?>"
                                   data-itemsonpage="<?= $iItemsOnPageCount ?>"
                                   data-offset="1"
                                ><img src="<?='//' . $_SERVER['SERVER_NAME'] . str_replace('catalog.php','',$_SERVER['SCRIPT_NAME']) . '/'?>ajax-loader.gif"></a>

                            </div>
                            <br clear="all">
                        <? } ?>


                    </div>
                <? } else { ?>
                    <div id="empty-search"> Извините, ничего не найдено.</div>
                <? } ?>

            </div>


        </div>
    </div>

    <script>
        $(window).ready(function () {


            //постраничка
            var $searchBlock = $('#search_pagination');
            var pagination = [];
            var groupPageCount = 4;

            //отправляем форму при выборе порядка сортировки
            $('#change_sort_search').selectmenu({
                change: function () {

                    $("input[name=order]").val($(this).val());

                    $("input[name=offset]").val(0);

                    $('#search_form').submit();
                }
            });


            //отправляем форму при выборе товаров со скидкой
            $('#change_discount_search').bind('change', function () {
                if ($(this).prop('checked')) {
                    $("input[name=discount]").val($(this).val());
                } else {
                    $("input[name=discount]").val(0);
                }

                $("input[name=offset]").val(0);

                $('#search_form').submit();

            });

            $(window).trigger("search_catalog_load");


        });
    </script>

    <?

}
?>