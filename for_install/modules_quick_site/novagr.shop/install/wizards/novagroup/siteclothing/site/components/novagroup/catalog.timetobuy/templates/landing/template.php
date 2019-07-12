<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$datetime1 = new DateTime($arResult['PROPERTY_TIMETOBUYACTIVETO_VALUE']);
$datetime2 = new DateTime();
$interval = $datetime1->diff($datetime2);

$timeStamp = MakeTimeStamp($arResult["PROPERTY_TIMETOBUYACTIVETO_VALUE"]);
$tillDate = ToLower(CIBlockFormatProperties::DateFormat("j F", $timeStamp));

?>
<h1><?= GetMessage('BUY_SPECIAL_PRICE') ?> <span id="product-name"><?=$arParams['PRODUCT_NAME']?></span></h1>
<div class="uspey-buy">
    <div class="row">
        <div class="span7">
            <h3>
                <?= GetMessage('SPECIAL_PRICE_ONLY_TO') ?> <?=$tillDate?> <?= GetMessage('DISCOUNT') ?> <span class="sale"><?=$arResult["PROPERTY_DISCOUNT_VALUE"]?>%</span>
            </h3>
        </div>
        <div class="span5">
            <div class="time-buy">
                <div class="zn-tr">
                    <div class="card-time"></div>
                </div>
                <div class="countdown_dashboard" data-year="<?=(int)$datetime1->format('Y')?>" data-month="<?=(int)$datetime1->format('m')?>" data-day="<?=(int)$datetime1->format('d')?>" data-hours="<?=(int)$datetime1->format('H')?>" data-minutes="<?=(int)$datetime1->format('i')?>" data-seconds="<?=(int)$datetime1->format('s')?>">
                    <div class="title-block"><?=GetMessage('TO_DATE')?></div>
                    <div class="dash days_dash">
                        <span class="dash_title"><?= GetMessage('DD') ?></span>

                        <div class="digit"><?=(int)substr("00".$interval->format('%a'),0,-1);?></div>
                        <div class="digit"><?=(int)substr("00".$interval->format('%a'),-1);?></div>
                    </div>

                    <div class="dash hours_dash">
                        <span class="dash_title"><?=GetMessage('HH')?></span>

                        <div class="digit"><?=(int)substr("00".$interval->format('%H'),-2,1);?></div>
                        <div class="digit"><?=(int)substr("00".$interval->format('%H'),-1);?></div>
                    </div>

                    <div class="dash minutes_dash">
                        <span class="dash_title"><?=GetMessage('MIN')?></span>

                        <div class="digit"><?=(int)substr("00".$interval->format('%i'),-2,1);?></div>
                        <div class="digit"><?=(int)substr("00".$interval->format('%i'),-1);?></div>
                    </div>

                </div>
                <div class="day-col">
                    <div class="title-block"><?=GetMessage('MORE')?></div>
                    <span class="dash_title"><?=GetMessage('PCS')?></span>

                    <div class="dig"><?=(int)$arResult['PROPERTY_QUANTITY_VALUE']?></div>
                </div>
            </div>
        </div>
    </div>
</div>