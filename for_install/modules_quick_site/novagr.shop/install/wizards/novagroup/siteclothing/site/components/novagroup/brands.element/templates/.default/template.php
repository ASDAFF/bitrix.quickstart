<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="col3-list brand">
    <?php
    $val = $arResult['BRAND'];
    $val['PREVIEW_PICTURE'] = CFile::GetFileArray($val['PREVIEW_PICTURE']);
  
    if (trim($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) <> "") {
        $H1 = $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"];
    } else {
        $H1 = $arResult['BRAND']['NAME'];
    }
    ?>
    <div class="list ol">
        <div class="itemsall clearfix brands-list">
            <div class="item itemsall_op">
                <div class="brand-lf">
                    <div class="title">
                        <?= $H1;?>
                    </div>
                    <div class="clear"></div>

                </div>
                <? if (!empty($val['PREVIEW_PICTURE']['SRC'])) { ?>
                <div class="itemsall_img">
                        <img width="140" alt="" src="<?= $val['PREVIEW_PICTURE']['SRC']; ?>">
                </div>
                <? } ?>
                <div class="clear"></div>
                <div class="personal_notes">
                    <?= $val['DETAIL_TEXT']; ?>
                </div>
            </div>
        </div>
    </div>
    <p class="back-demo">&#8592;
        <a class="lsnn" href="<?= $val['LIST_PAGE_URL'] ?>">
            <?= GetMessage("B_BACK_TO_LIST") ?>
        </a>
    </p>
</div>