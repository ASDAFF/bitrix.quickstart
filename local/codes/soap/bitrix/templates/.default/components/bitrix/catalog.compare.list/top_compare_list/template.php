<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult)>0):?>
<?/*
// то что было раньше
<span class="b-header-user__link"><a href="<?=$arParams["COMPARE_URL"]?>" class="b-header-user__link m-no_margin">Сравнить товары</a> <b class="b-header-compare__count">(<?=count($arResult)?>)</b></span>
*/?>
<span class="b-header-user__link"><a href="#js-compare__list" id="js-compare__btn" class="b-header-user__link m-no_margin">Сравнить товары</a> <b class="b-header-compare__count">(<?=count($arResult)?>)</b></span>
<!--	<form action="<?=$arParams["COMPARE_URL"]?>" method="get">
	<table class="data-table" cellspacing="0" cellpadding="0" border="0">
		<thead>
		<tr>
			<td align="center" colspan="2"><?=GetMessage("CATALOG_COMPARE_ELEMENTS")?></td>
		</tr>
		</thead>
		<?foreach($arResult as $arElement):?>
		<tr>
			<td><input type="hidden" name="ID[]" value="<?=$arElement["ID"]?>" /><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></td>
			<td><noindex><a href="<?=$arElement["DELETE_URL"]?>" rel="nofollow"><?=GetMessage("CATALOG_DELETE")?></a></noindex></td>
		</tr>
		<?endforeach?>
	</table>
	<?if(count($arResult)>=2):?>
		<br /><input type="submit"  value="<?=GetMessage("CATALOG_COMPARE")?>" />
		<input type="hidden" name="action" value="COMPARE" />
		<input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
	<?endif;?>
	</form>-->
<?else:?>
<span class="b-header-user__link"><a href="#js-compare__list" id="js-compare__btn" class="b-header-user__link m-no_margin">Нет товаров на сравнение</span>
<?endif;?>
    <?
$sect_id = array();
    $ball = array();
    $array = array();
    $z = 0;
foreach($arResult as $arElement):
            $arFilter = Array('ID' => $arElement["IBLOCK_SECTION_ID"]);
            $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);

            if($ar_result = $db_list->GetNext())
            {
                if (!array_key_exists($ar_result['ID'], $sect_id)):
                    //echo '<a href=?section='.$ar_result['ID'].'>'.$ar_result['NAME'].'</a>';
                    $sect_id[$ar_result['ID']]["ID"] = $ar_result['ID'];
                    $sect_id[$ar_result['ID']]["NAME"] = $ar_result['NAME'];
                    $sect_id[$ar_result['ID']]["URL"] = '?section='.$ar_result['ID'];
                    $sect_id[$ar_result['ID']]["NUM"] = 1;
                    else: 
                    $sect_id[$ar_result['ID']]["NUM"] += 1;
                    endif;
            }
            $z++; 
endforeach;?>
<div id="js-compare__list" class="b-popup">
	<div class="b-popup__wrapper">
<?if($z>0){?>
<?foreach($sect_id as $arSec):?>
		<div class="b-user__link"><a href="/catalogue/compare.php<?=$arSec["URL"]?>"><?=$arSec["NAME"]?> <?=$arSec["NUM"]?></a></div>
<?endforeach;?>
		<div class="b-user__address"><a href="<?=$arParams["COMPARE_URL"]?>" style="color: #FA5400">Перейти к сравнению</a></div>
<?}else{?>
<div class="b-user__address">Список сравнения пуст</div>
<?}?>
	</div>
</div>