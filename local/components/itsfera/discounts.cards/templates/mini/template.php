<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="discount-cards-wrapper new-subscribe">
    <h2>Дисконтная карта</h2>
    <div class="discount-card-info">
    <?if($arResult['AUTH'] == "Y") {
        if ($arResult['CARD']['NAME']) {
            ?>
            <p>
                Номер вашей дисконтной карты: <b><?= $arResult['CARD']['NAME'] ?></b><br>
                Тип скидки: <?= $arResult['CARD']['PROPERTY_CARDTYPE_VALUE'] ?>
                <?
                if ($arResult['CARD']['PROPERTY_CARDTYPE_ENUM_ID'] == 317085) {
                    ?>
                    &nbsp; <?= $arResult['CARD']['PROPERTY_PERCENT_VALUE'] ?> %<br>
                <?
                } else {
                    ?>
                    <br>
                    Накопленная сумма: <?= CCurrencyLang::CurrencyFormat($arResult['CARD']['PROPERTY_TOTAL_VALUE'], "RUB") ?>
                <?
                } ?>
            </p>

            <?
        } else {
            ?>
            <p>Ни одна карта не привязана к профилю.</p>
            <div class="buttons not-mobile">
                <a href="/personal/">Привязать карту</a> можно в личном кабинете.
            </div>
            <?
        }
    }else{?>
        <a href="/personal/register/">Зарегистрироваться</a> и подключить дисконтную карту.
    <?}?>
    </div>


</div>
