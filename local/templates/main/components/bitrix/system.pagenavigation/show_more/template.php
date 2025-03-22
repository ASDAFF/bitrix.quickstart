<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->createFrame()->begin("Загрузка навигации");
?>
    <style>
        .load_more {
            margin: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            cursor: pointer;
            text-align: center;
        }
    </style>
<?if($arResult["NavPageCount"] > 1):?>

    <?if ($arResult["NavPageNomer"]+1 <= $arResult["nEndPage"]):?>
        <?
        $plus = $arResult["NavPageNomer"]+1;
        $url = $arResult["sUrlPathParams"] . "PAGEN_".$arResult["NavNum"]."=".$plus;

        ?>

        <div class="load_more" data-url="<?=$url?>">
            Показать еще
        </div>

    <?else:?>

        <div class="load_more">
            Загружено все
        </div>

    <?endif?>

<?endif?>