<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

$APPLICATION->SetPageProperty("description",GetMessage("MYCOMPANY_REMO_VSE_NOVOSTI"));
$APPLICATION->SetPageProperty("keywords",GetMessage("MYCOMPANY_REMO_VSE_NOVOSTI"));

?>


<h1 class="f16"><?=GetMessage("MYCOMPANY_REMO_VSE_NOVOSTI")?></h1>
<div class="mybord"></div>


<?
foreach($arResult["ITEMS"] as $arItem):
    ?>



<div class="c33">

    <div class="c333">
        <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
             width="<?//=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>90px"
             height="<?//=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>83px"
             alt="<?=$arItem["NAME"]?>" border="0">
    </div>
    <div class="c3333">
        <p><?=$arItem["DISPLAY_PROPERTIES"]["ncdate"]["DISPLAY_VALUE"]?></p>
        <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>">
            <?=$arItem["NAME"]?>
        </a>
    </div>
</div>

<?
endforeach
?>
<div id="navchain">
    <?=$arResult["NAV_STRING"]?>
</div>