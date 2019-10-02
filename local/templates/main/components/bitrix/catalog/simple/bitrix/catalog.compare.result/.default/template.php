<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h1>Сравнение товаров</h1>
<div class="catalog-compare-result">
<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
    <? // Чекбоксы ?>
    <tr>
        <td valign="top">&nbsp;</td>
        <?foreach($arResult["ITEMS"] as $arElement):?>
            <td valign="top" width="<?=round(100/count($arResult["ITEMS"]))?>%">
                <form id="form_<?=$arElement["ID"]?>" action="<?=$APPLICATION->GetCurPage()?>" method="post">
                <input type="hidden" name="ID" value="<?=$arElement["ID"]?>" />
                <input type="hidden" name="action" value="DELETE_FROM_COMPARE_RESULT" />
                <input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
                </form>
                <a href="javascript:;" class="red" onclick="$('#form_<?=$arElement["ID"]?>').submit(); return false;"><span class="glyphicon glyphicon-remove-circle"></span>Убрать</a>
            </td>
        <?endforeach?>
    </tr>
    <? // Названия ?>
    <?foreach($arResult["ITEMS"][0]["FIELDS"] as $code=>$field):?>
    <tr>
        <th valign="top"><?=GetMessage("IBLOCK_FIELD_".$code)?></th>
        <?foreach($arResult["ITEMS"] as $arElement):?>
            <td valign="top"><?
                switch($code):
                    case "NAME":
                        ?><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement[$code]?></a><?
                        /*if($arElement["CAN_BUY"]):
                            ?><noindex><br /><a href="<?=$arElement["BUY_URL"]?>" rel="nofollow"><?=GetMessage("CATALOG_COMPARE_BUY"); ?></a></noindex><?
                        elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):
                            ?><br /><?=GetMessage("CATALOG_NOT_AVAILABLE")?><?
                        endif;*/
                        break;
                    case "PREVIEW_PICTURE":
                    case "DETAIL_PICTURE":
                        if(is_array($arElement["FIELDS"][$code])):?>
                            <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["FIELDS"][$code]["SRC"]?>" width="<?=$arElement["FIELDS"][$code]["WIDTH"]?>" height="<?=$arElement["FIELDS"][$code]["HEIGHT"]?>" alt="<?=$arElement["FIELDS"][$code]["ALT"]?>" /></a>
                        <?endif;
                        break;
                    default:
                        echo $arElement["FIELDS"][$code];
                        break;
                endswitch;
                ?>
            </td>
        <?endforeach?>
    </tr>
    <?endforeach;?>
    </thead>
    <? // Цены ?>
    <?foreach($arResult["ITEMS"][0]["PRICES"] as $code=>$arPrice):?>
        <?if($arPrice["CAN_ACCESS"]):?>
        <tr>
            <th valign="top"><?=$arResult["PRICES"][$code]["TITLE"]?></th>
            <?foreach($arResult["ITEMS"] as $arElement):?>
                <td valign="top">
                    <?if($arElement["PRICES"][$code]["CAN_ACCESS"]):?>
                        <b><?=$arElement["PRICES"][$code]["PRINT_DISCOUNT_VALUE"]?></b>
                    <?endif;?>
                </td>
            <?endforeach?>
        </tr>
        <?endif;?>
    <?endforeach;?>
    <? //Оставшиеся свойства ?>
    <?foreach($arResult["SHOW_PROPERTIES"] as $code=>$arProperty):
        $arCompare = Array();
        // Если свойство у всех пустое - не показываем
        $property_empty = true;
        foreach($arResult["ITEMS"] as $arElement)
        {
            $arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$code]["VALUE"];
            if(is_array($arPropertyValue))
            {
                sort($arPropertyValue);
                $arPropertyValue = implode(" / ", $arPropertyValue);
            }
            if (!empty($arPropertyValue)) {
                $property_empty = false;
            }
            $arCompare[] = $arPropertyValue;
        }
        if ($property_empty) {
            continue;
        }
        $diff = (count(array_unique($arCompare)) > 1 ? true : false);
        if($diff || !$arResult["DIFFERENT"]):?>
            <tr>
                <th valign="top"><?=$arProperty["NAME"]?></th>
                <?foreach($arResult["ITEMS"] as $arElement):?>
                    <?if($diff):?>
                    <td valign="top"><?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
                    </td>
                    <?else:?>
                    <th valign="top"><?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
                    </th>
                    <?endif?>
                <?endforeach?>
            </tr>
        <?endif?>
    <?endforeach;?>
</table>
</div>
