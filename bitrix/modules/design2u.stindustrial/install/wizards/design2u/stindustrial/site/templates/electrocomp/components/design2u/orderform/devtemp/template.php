<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
/*
$rsSites = CSite::GetByID("s1");
$arSite = $rsSites->Fetch();
echo "<pre>"; print_r($arSite); echo "</pre>";
*/
?>
<?
/*
$user=CUser::GetByID(1);
$arruser=$user->Fetch();
echo "<pre>"; print_r($arruser); echo "</pre>";
*/
?>


<div id="hmess"> <?=$arResult['festatus']?></div>

<form method="post">
    <input name="email" class="rm1I" type="text" value="email@yandex.ru" size="25" id="emailid">
    <?//=$arResult["aphone"]?>

    <?
    if($arResult['fnoestatus']==GetMessage("MYCOMPANY_REMO_VY_NE_UKAZALI_TELEFO")):
        ?>
        <input name="phone" class="rm1I" type="text" value="<?=GetMessage("MYCOMPANY_REMO_NE_UKAZAN_TELEFON")?>" size="25" style="border-color:red" id="phoneid">
        <?
    else:
        ?>
        <input name="phone" class="rm1I" type="text" value="<?=GetMessage("MYCOMPANY_REMO_TELEFON")?>" size="25"  id="phoneid">

        <?endif?>

    <input class="rm1B" type="image" src="<?=SITE_TEMPLATE_PATH?>/images/button.png" name="submit" value="<?=GetMessage("MYCOMPANY_REMO_ZAKAZATQ")?>"
            >

    <input type="hidden" class="inp" value="flg" name="flg"/>
</form>
