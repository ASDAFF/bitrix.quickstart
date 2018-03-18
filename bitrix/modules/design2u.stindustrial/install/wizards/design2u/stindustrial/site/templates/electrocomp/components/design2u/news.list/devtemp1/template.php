<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
/*
$APPLICATION->SetPageProperty("description","Новости компании");
$APPLICATION->SetPageProperty("keywords","Новости компании");
*/
?>

<?

/*
 echo('<pre>');
 var_dump($arResult["ITEMS"]);
 echo('</pre>');
*/
?>

<?
//CModule::IncludeModule('iblock');
?>

<?
foreach($arResult["ITEMS"] as $arItem):
    ?>
<?
//DEL
//echo $arItem["IBLOCK_SECTION_ID"]."IBLOCK_SECTION_ID";
    ?>
<?
    $res = CIBlockSection::GetByID($arItem["IBLOCK_SECTION_ID"]);
    ?>

<?
    $ar_res = $res->GetNext();
    ?>

<?
//var_dump($ar_res);
    ?>

<?if($ar_res["NAME"]==GetMessage("MYCOMPANY_REMO_POSLEDNIE_PROEKTY")):?>



<p><?=$arItem["ACTIVE_FROM"]?></p>
<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>


<?endif?>
<?
endforeach
?>
<?//=$arResult["NAV_STRING"]?><br />
