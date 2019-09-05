<?
use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
Loc::loadMessages(__FILE__);
$this->setFrameMode(true);
if (empty($arResult["USERS"])) {
    ShowError(GetMessage("NO_ITEMS_FOUNDED"));
    return;
}
?>
<section class="raiting raiting_full-width">
    <div class="container container_raiting">
        <div class="raiting__wrap raiting__wrap_full-w raiting__wrap_two-col-center">
            <div class="raiting__line raiting__line_head">
                <div class="raiting__col1">
                    <span class="raiting__tablehead"><?=Loc::getMessage('POSITION')?></span>
                </div>
                <div class="raiting__col2">
                    <span class="raiting__tablehead"><?=Loc::getMessage('CITY')?></span>
                </div>
                <div class="raiting__col3 raiting__col3_center">
                    <span class="raiting__tablehead"><?=Loc::getMessage('QUANTITY')?></span>
                </div>
            </div>

            <?
            $i = $arResult['NAV_PARAMS']['NavFirstRecordShow'];
            foreach ($arResult['USERS'] as $arUser) {
                ?>
                <div class="raiting__line">
                    <div class="raiting__col1">
                        <div class="raiting__place">
                            <?= $i?>
                        </div>
                    </div>
                    <div class="raiting__col2">
                        <div class="raiting__wrapin-col2">
                            <?
                            if ($arUser['PERSONAL_CITY']) {
                                ?>
                                <p class="raiting__town"><?= $arUser['PERSONAL_CITY'] ?> <?= $arUser['PERSONAL_COUNTRY'] ? ', ' . $arUser['PERSONAL_COUNTRY'] : '' ?></p><?
                            }
                            ?>
                        </div>
                    </div>
                    <div class="raiting__col3">
                        <p class="raiting__balls"><?= number_format($arUser['CNT'], 0, '', ' ') ?></p>
                    </div>
                </div>
                <?
                $i++;
            }
            ?>
        </div>
    </div>
</section>

<div class="js-ajax-pagenation">
    <? if ($arResult["NAV_RESULT"]) {
        echo $arResult["NAV_RESULT"];
    }
    ?>
</div>