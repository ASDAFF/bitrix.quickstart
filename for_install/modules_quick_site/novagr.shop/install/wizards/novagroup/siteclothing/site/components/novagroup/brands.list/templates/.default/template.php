<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="col3-list brand">
    <div id="buttons_all">
        <a href="?abc=0" class="btnall select">
            <?= GetMessage("B_BY_LIST") ?>
        </a>
        <a href="?abc=1" class="btnall">
            <?= GetMessage("B_BY_ALFAVIT") ?>
        </a>
    </div>
    <hr>
    <div id="alfavit">
        <ul><?
            foreach ($arResult['LAT'] as $val)
                echo '<li><a href="./?let=' . $val . '">' . $val . '</a></li>';
            ?>
        </ul>
        <ul>
            <?
            foreach ($arResult['RUS'] as $val)
                echo '<li><a href="./?let=' . $val . '">' . $val . '</a></li>';
            ?>
        </ul>
    </div>

    <div id="elements-brands" class="brand">
        <?
        foreach ($arResult['BRANDS'] as $val) {
            $val['PREVIEW_PICTURE'] = CFile::GetFileArray($val['PREVIEW_PICTURE']);
            //$FilterURL = SITE_DIR . "catalog/?iNumPage=1&nPageSize=" . N_PAGE_SIZE_1 . "&arFilter[0][PROPERTY_VENDOR]=" . $val['ID'];
            ?>
            <div class="list py">
                <div class="itemsall clearfix brands-list">
                    <div class="item itemsall_op">

                        <div class="brand-lf">
                            <div class="title">
                                <?php
                                echo $val['NAME'];
                                ?>
                            </div>
                            <div class="clear"></div>
                            <?/*<a href="<?= $FilterURL; ?>" class="btn">
                                <?= GetMessage("B_PRODUCTS") ?>
                            </a>*/?>
                        </div>
                        <? if (!empty($val['PREVIEW_PICTURE']['SRC'])) { ?>
                            <div class="itemsall_img">
                                <?php
                                if (!empty($val['DETAIL_PAGE_URL'])) {
                                    ?><a href="<?= $val['DETAIL_PAGE_URL']; ?>">
                                    <img height="auto" src="<?= $val['PREVIEW_PICTURE']['SRC']; ?>"
                                         alt=""/>
                                    </a>
                                <?php
                                } else {
                                    ?>
                                    <img height="auto" src="<?= $val['PREVIEW_PICTURE']['SRC']; ?>"
                                         alt=""/>
                                <?php
                                }
                                ?>
                            </div>
                        <? } ?>
                        <div class="personal_notes"><?= $val['DETAIL_TEXT']; ?></div>
                    </div>
                </div>
                <hr>
            </div>
        <?
        }
        ?>
        <div class="brands-nav">
            <?= $arResult["NAV_STRING"]; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>

