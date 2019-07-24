<?php
if (!defined("B_PROLOG_INCLUDED") || (B_PROLOG_INCLUDED !== true)) {
    die();
}
CJSCore::Init(
    array('ymaps_lib')
);
?>
<script>
    var pathMarker = '<?=$templateFolder.'/images/ymap-contact-mark.png'?>';
</script>



<div class="contacts-wrapper">
    <div id="contacts_map_wrapper"></div>
    <ul class="nav nav-tabs contacts-ul" role="tablist">
        <?php foreach ($arResult["SECTIONS"] as $iSectionIndex => $arSection): ?>
            <li>
                <a href="#tab<?php echo($iSectionIndex + 1); ?>"<?php if ($iSectionIndex == 0): ?> class="active"<?php endif; ?> data-toggle="tab" role="tab">
                    <?php echo $arSection["NAME"]; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="tab-content">
        <?php foreach ($arResult["SECTIONS"] as $iSectionIndex => $arSection): ?>
            <div id="tab<?php echo($iSectionIndex + 1); ?>"
                 class="tab-pane fade<?php if ($iSectionIndex == 0): ?> in active<?php endif; ?>">
                <div class="row">
                    <?php foreach ($arSection["ITEMS"] as $iItemIndex => $arItem): ?>
                    <?php if (($iItemIndex > 0) && (($iItemIndex % $arParams["LINE_ELEMENT_COUNT"]) == 0)): ?>
                </div>
                <div class="row">
                    <?php endif; ?>
                    <div class="col-xl-4" id="<?php echo $this->GetEditAreaId($arItem["ID"]); ?>">
                        <div class="thumbnail contact-a">
                            <p class="name-contact">
                                <a href="<?php echo $arItem["DETAIL_PAGE_URL"]; ?>">
                                    <?php echo $arItem["NAME"]; ?>
                                </a>
                            </p>

                            <?php if ($arItem["PROPERTIES"]["ADDRESS"]["VALUE"]): ?>
                                <p><?php echo $arItem["PROPERTIES"]["ADDRESS"]["VALUE"]; ?></p>
                            <?php endif; ?>

                            <?php if ($arItem["PROPERTIES"]["PHONE"]["VALUE"]): ?>
                                <p>
                                    <?php foreach ($arItem["PROPERTIES"]["PHONE"]["VALUE"] as $sPhone): ?>
                                        <?php echo GetMessage("CT_BCST_PHONE_LABEL"); ?>:&nbsp;<?php echo $sPhone; ?>
                                        <br/>
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($arItem["PROPERTIES"]["FAX"]["VALUE"]): ?>
                                <p>
                                    <?php echo GetMessage("CT_BCST_FAX_LABEL"); ?>
                                    :&nbsp;<?php echo $arItem["PROPERTIES"]["FAX"]["VALUE"]; ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($arItem["PROPERTIES"]["EMAIL"]["VALUE"]): ?>
                                <p>
                                    <?php foreach ($arItem["PROPERTIES"]["EMAIL"]["VALUE"] as $sEmail): ?>
                                        Email:&nbsp;
                                        <a href="mailto:<?php echo $sEmail; ?>">
                                            <?php echo $sEmail; ?>
                                        </a><br/>
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($arItem["PROPERTIES"]["SITE"]["VALUE"]): ?>
                                <p>
                                    <?php echo GetMessage("CT_BCST_SITE_LABEL"); ?>:&nbsp;
                                    <a href="http://<?php echo $arItem["PROPERTIES"]["SITE"]["VALUE"]; ?>"
                                       target="_blank">
                                        <?php echo $arItem["PROPERTIES"]["SITE"]["VALUE"]; ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
