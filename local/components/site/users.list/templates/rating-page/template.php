<?
use Bitrix\Main\Application;
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
$docRoot = Application::getInstance()->getDocumentRoot();
?>
<section class="raiting raiting_full-width">
    <div class="container container_raiting">
        <div class="raiting__wrap raiting__wrap_full-w">
            <div class="raiting__line raiting__line_head">
                <div class="raiting__col1">
                    <span class="raiting__tablehead"><?= Loc::getMessage("POSITION"); ?></span>
                </div>
                <div class="raiting__col2">
                    <span class="raiting__tablehead"><?= Loc::getMessage("PARTICIPANT"); ?></span>
                </div>
                <div class="raiting__col3">
                    <span class="raiting__tablehead"><?= Loc::getMessage("PROGRAMS"); ?></span>
                </div>
            </div>

            <?
            $i = $arResult['NAV_PARAMS']['NavFirstRecordShow'];
            foreach ($arResult['USERS'] as $arUser) {
                // формирование имени
                if($arUser['NAME']) {
                    $name = $arUser['NAME'];
                }
                else {
                    $name = 'Нет имени';
                }
                // картинка
                $src = $arUser['PERSONAL_PHOTO']['src'];
                if( !$src || !file_exists($docRoot . $src) ) {
                    $src = SITE_TEMPLATE_PATH . '/images/avatar.png';
                }
                ?>
                <div class="raiting__line">
                    <div class="raiting__col1">
                        <div class="raiting__place">
                            <?= $i?>
                        </div>
                    </div>
                    <div class="raiting__col2">
                        <span class="raiting__userpic" style="background-image: url('<?=$src?>')"></span>
                        <div class="raiting__wrapin-col2">
                            <p class="raiting__famili"><?= $name ?></p><?
                            if ($arUser['PERSONAL_COUNTRY'] && $arUser['PERSONAL_CITY']) {
                                ?>
                                <p class="raiting__town"><?= $arUser['PERSONAL_CITY'] ?>, <?= $arUser['PERSONAL_COUNTRY'] ?></p><?
                            }
                            ?>
                        </div>
                    </div>
                    <div class="raiting__col3">
                        <p class="raiting__balls"><?= number_format($arUser['UF_PROGRAM'], 0, '', ' ') ?></p>
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