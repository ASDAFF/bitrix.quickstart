<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
if(empty($arResult["USER"])) {
    ShowError(GetMessage("USER_NOT_FOUNDED"));
    return;
}
?>
<h2><?= $arResult["USER"]["TITLE"] ?></h2>
<div class="row">
    <div class="col-xs-3">
        <? if ($arResult["USER"]["PERSONAL_PHOTO"]) {
            $src = \CFile::GetPath($arResult["USER"]["PERSONAL_PHOTO"]);
            ?>
            <a href="<?= $src ?>">
                <img class="img-responsive" src="<?= $src ?>" alt="<?= $arResult["USER"]["TITLE"] ?>">
            </a>
            <?
        } else {
            ?>
            <img class="img-responsive" src="<?= \Site\Main\TEMPLATE_IMG . '/nophoto.png' ?>" alt="">
            <?
        }

        ?>
    </div>
    <div class="col-xs-9">
        <ul class="user-detail-fields">
            <?
            if ($arResult["USER"]["PERSONAL_BIRTHDAY"]) {
                ?>
                <li>
                    <p>
                        <b>Дата рождения:</b> <?= $arResult["USER"]["PERSONAL_BIRTHDAY_FORMATED"] ?></p>
                </li>
                <?
            }
            if ($arResult["USER"]["PERSONAL_CITY"]) {
                ?>
                <li>
                    <p>
                        <b>Место рождения:</b> <?= $arResult["USER"]["PERSONAL_CITY"] ?></p>
                </li>
                <?
            }
            ?>
        </ul>
    </div>
</div>