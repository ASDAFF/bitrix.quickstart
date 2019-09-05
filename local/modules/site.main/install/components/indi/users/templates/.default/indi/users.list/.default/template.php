<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
if (empty($arResult["USERS"])) {
    ShowError(GetMessage("NO_ITEMS_FOUNDED"));
    return;
}
?>
<div class="users-list-wrap"> <!-- need for ajax -->
    <div class="js-ajax-container" <?=$arParams["AJAX_ID"] ? 'id="' . $arParams["AJAX_ID"] . '"' : '' ?>  data-url="<?= $arParams["SEF_FOLDER"] ?>">
        <div class="user-list">
            <?
            foreach ($arResult["USERS"] as $userIndex => $arUser) {
                ?>
                <div class="user-list-item">
                    <?
                    if ($arParams["LIST_SHOW_PHOTO"] == "Y") { ?>
                        <div class="user-card__img" style="background-image: url('<?= ($arUser["PERSONAL_PHOTO"]) ? \CFile::GetPath($arUser["PERSONAL_PHOTO"]) : \Site\Main\TEMPLATE_IMG . '/nophoto.png' ?> ')">
                            <?
                            if ($arParams["LIST_LINK_DETAIL"] == "Y") { ?>
                                <a href="<?= $arUser["PATH"] ?>" class="user-card__img__link"></a>
                                <?
                            }
                            ?>
                        </div>
                        <?
                    }
                    ?>
                    <div class="user-card__info">
                        <div class="user-card__name">
                            <? if ($arParams["LIST_LINK_DETAIL"] == "Y")
                            {
                            ?>
                            <a href="<?= $arUser["PATH"] ?>">
                                <?
                                }
                                if ($arUser["NAME"] && $arUser["LAST_NAME"]) {
                                    ?>
                                    <div><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] ?></div>
                                    <?
                                } else {
                                    ?>
                                    <div>Нет имени</div>
                                    <?
                                }
                                if ($arParams["LIST_LINK_DETAIL"] == "Y") {
                                ?>
                            </a>
                        <?
                        }
                        if ($arUser["PERSONAL_CITY"]) {
                            ?>
                            <span class="user-card__info__city"><?= $arUser["PERSONAL_CITY"] ?></span>
                            <?
                        }
                        ?>
                        </div>
                    </div>
                </div>
                <?
            }
            ?>
        </div>
        <div class="<?=($arParams["AJAX_ID"]) ? "js-ajax-pagenation" : ""?>">
            <? if ($arResult["NAV_RESULT"]) {
                echo $arResult["NAV_RESULT"];
            }
            ?>
        </div>
    </div>
</div>



