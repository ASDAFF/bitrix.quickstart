<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    $searchitemparam = array();?>
<div class="search-page">
    <form action="" method="get">
        <input type="hidden" name="how" value="<?echo $arResult["REQUEST"]["HOW"]=="d"? "d": "r"?>" />
    </form><br />


    <?if($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false):?>
        <?elseif($arResult["ERROR_CODE"]!=0):?>
        <p><?=GetMessage("SEARCH_ERROR")?></p>
        <?ShowError($arResult["ERROR_TEXT"]);?>
        <p><?=GetMessage("SEARCH_CORRECT_AND_CONTINUE")?></p>
        <br /><br />
        <p><?=GetMessage("SEARCH_SINTAX")?><br /><b><?=GetMessage("SEARCH_LOGIC")?></b></p>
        <table border="0" cellpadding="5">
            <tr>
                <td align="center" valign="top"><?=GetMessage("SEARCH_OPERATOR")?></td><td valign="top"><?=GetMessage("SEARCH_SYNONIM")?></td>
                <td><?=GetMessage("SEARCH_DESCRIPTION")?></td>
            </tr>
            <tr>
                <td align="center" valign="top"><?=GetMessage("SEARCH_AND")?></td><td valign="top">and, &amp;, +</td>
                <td><?=GetMessage("SEARCH_AND_ALT")?></td>
            </tr>
            <tr>
                <td align="center" valign="top"><?=GetMessage("SEARCH_OR")?></td><td valign="top">or, |</td>
                <td><?=GetMessage("SEARCH_OR_ALT")?></td>
            </tr>
            <tr>
                <td align="center" valign="top"><?=GetMessage("SEARCH_NOT")?></td><td valign="top">not, ~</td>
                <td><?=GetMessage("SEARCH_NOT_ALT")?></td>
            </tr>
            <tr>
                <td align="center" valign="top">( )</td>
                <td valign="top">&nbsp;</td>
                <td><?=GetMessage("SEARCH_BRACKETS_ALT")?></td>
            </tr>
        </table>
        <?elseif(count($arResult["SEARCH"])>0):?>
        <?foreach($arResult["SEARCH"] as $arItem):
                $searchitemparam[] = $arItem["ITEM_ID"];?>
            <?endforeach;?>
        <?else:?>
        <?ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND"));?>
        <?endif;?>
</div>

<?
    if(!$_REQUEST["ajax"] && @$_REQUEST["ajax"] != "Y"){?>
    <hr class="b-hr" />
    <?  // Elements sort
        $arAvailableSort = array(
            "name" => Array("name", "asc"),
            "price" => Array('PROPERTY_MINIMUM_PRICE', "asc"),
        );

        $sort = array_key_exists("sort", $_REQUEST) && array_key_exists(ToLower($_REQUEST["sort"]), $arAvailableSort) ? $arAvailableSort[ToLower($_REQUEST["sort"])][0] : "name";
        $sort_order = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arAvailableSort[$sort][1];
    ?>
    <div class="b-tab-head clearfix">
        <div class="b-sort-wrapper">
            <span class="b-sort__text"><?=GetMessage('SECT_SORT_LABEL')?>:</span>
            <?foreach ($arAvailableSort as $key => $val):
                    $className = ($sort == $val[0]) ? ' current' : '';
                    if ($className)
                        $className .= ($sort_order == 'asc') ? ' asc' : ' desc';
                    $newSort = ($sort == $val[0]) ? ($sort_order == 'desc' ? 'asc' : 'desc') : $arAvailableSort[$key][1];
                ?>
                <a href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort,     array('sort', 'order'))?>"  class="b-sort__link <?=$className?>" rel="nofollow"><?=GetMessage('SECT_SORT_'.$key)?><?if ($sort == $val[0]):?><span></span><?endif?></a>
                <?endforeach;?>
        </div>
        <?
            if($_REQUEST["temp"] == "grid"){
                $list_temp = "grid";
            }else{
                $list_temp = "";
            }
        ?>
        <div class="b-sort-view">
            <?$url = $APPLICATION->GetCurUri("temp");?>
            <?$url = $APPLICATION->GetCurPageParam("temp", array("temp")); ?>
            <a href="<?=$url?>" class="b-view__link <?if($list_temp==""){echo "active";}?>"></a>
            <?$url = $APPLICATION->GetCurPageParam("temp=grid", array("temp")); ?>
            <a href="<?=$url?>" class="b-view__link m-grid <?if($list_temp=="grid"){echo "active";}?>"></a>
        </div>
    </div>
    <?}?>
<?
    $GLOBALS['arrFilter'] = array("ID"=>$searchitemparam);
    $APPLICATION->IncludeComponent("bitrix:catalog.section", $list_temp, array(
	"IBLOCK_TYPE" => "catalog",
	"IBLOCK_ID" => "1",
	"SECTION_ID" => "",
	"SECTION_CODE" => "",
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "arrFilter",
	"INCLUDE_SUBSECTIONS" => "Y",
	"SHOW_ALL_WO_SECTION" => "Y",
	"PAGE_ELEMENT_COUNT" => "0",
	"LINE_ELEMENT_COUNT" => "3",
	"PROPERTY_CODE" => array(
		0 => "article",
		1 => "rating",
		2 => "",
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
	"CACHE_FILTER" => "Y",
	"PRICE_CODE" => array(
		0 => "price",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "Y",
	"CONVERT_CURRENCY" => "Y",
	"CURRENCY_ID" => "RUB",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Товары",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "ajaxmode",
	"PAGER_DESC_NUMBERING" => "Y",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>