<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<table class="presentTable">
    <tr class="presentTableHead">
        <td></td>
        <td width="200px"></td>
        <td width="100px">ТИП МАШИНИ</td>
        <td width="130px">ИЗГОТОВИТЕЛЬ</td>
        <td width="90px">МОДЕЛЬ</td>
        <td width="70px">ГОД</td>
        <td width="120px">РАЗРАБОТКА</td>
        <td width="100px">ЦЕНА</td>
        <td width="140px">МЕСТОНАХОЖДЕНИЕ</td>
        <td></td>
    </tr>
    <?foreach ($arResult["ITEMS"] as $id => $item) : ?>
    <tr class="darkTableRow">
        <td class="borderLeft"></td>
        <td width="200px">
                <!-- <div class="presentPhoto saleFoto presentTablePhoto gallery"> -->
                <div class="presentPhoto saleFoto presentTablePhoto">
                    <a href="<?=$item["URL"]?>"><img src="<?=$item["PICTURE"]["THUMB"]?>"/></a>
                    <?if ($item["DISCOUNT"]) : ?>
                        <img class="saleLable" src="/images/SaleLable.png"/>
                    <?endif;?>
                </div>
        </td>
        <td width="100px"><?=$item["TYPE"]?></td>
        <td width="130px"><?=$item["PRODUCER"]?></td>
        <td width="90px"><?=$item["MODEL"]?></td>
        <td width="70px"><?=$item["YEAR"]?></td>
        <td width="120px"><?=$item["OPERATIONS"]?></td>
        <td width="100px">
        <?if ($item["DISCOUNT"]) : ?>
            <div class="cutPrise sale presentTablePrice">
                <?if ($item["PRICE_OLD"]) : ?>
                    <p class="lastPrice"><?=$item["PRICE_OLD"]?></p>
                    <p class="presentNominal lastNominal"><span>руб.</span></p><br />
                <?endif;?>
                <p class="curPrice "><?=$item["PRICE_NEW"]?></p>
                <p class="presentNominal"><span>руб.</span></p>
                <div class="saleDate">до<span>&nbsp;<?=$item["DISCOUNT_END_DATE"]?></span></div>
            </div>
        <?else : ?>
            <?=$item["PRICE_NEW"]?>
        <?endif;?>
        </td>
        <td width="140px"><?=$item["PLACE"]?></td>
        <td class="borderRight"></td>
    </tr>
    <?endforeach?>
</table>