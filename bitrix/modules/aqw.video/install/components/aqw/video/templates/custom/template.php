<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
if (empty($arResult["ITEMS"])) return;
$uniqueRel = uniqid('aqw_video_rel');
$uniqueClass = uniqid('aqw_video_class');
?>
<table class="aqw_video" border="0" cellpadding="5">
    <tr valign="top">
        <?php
        foreach ($arResult["ITEMS"] as $arKey => $arItem):$arKey2 = $arKey + 10;
            $arItem['title'] = (strtolower(LANG_CHARSET)=='utf-8') ? $arItem['title'] : iconv('UTF-8',LANG_CHARSET,$arItem['title']);
            $src = isset($arItem['src']) ? $arItem['src'] : $arItem['preview'];
            $type = isset($arItem['src']) ? '' : '';
            echo ($arKey2 % $arParams['COUNT_ON_LINE'] == 1 and $arKey > 0) ? '</tr><tr valign="top">' : '';
            ?>
            <td align="center" width="<?= $arParams['WIDTH_IMAGE'] ?>">
                <a <?= $type ?> class="<?= $uniqueClass ?>" rel="<?= $uniqueRel ?>" href="<?= $src ?>"
                                title="<?=htmlspecialcharsbx($arItem['title'])?>">
                    <img width="<?= $arParams['WIDTH_IMAGE'] ?>" height="<?= $arParams['HEIGHT_IMAGE'] ?>"
                         src="<?= $arItem['preview'] ?>" alt=""/>
                </a>
                <span><?=htmlspecialcharsbx($arItem['title'])?></span>
            </td>
        <?php
        endforeach;
        $lastTd = (ceil(count($arResult["ITEMS"]) / $arParams['COUNT_ON_LINE']) * $arParams['COUNT_ON_LINE']) - count($arResult["ITEMS"]);
        if ($lastTd > 0) {
            for ($i = 0; $i < $lastTd; $i++) {
                echo "<td></td>";
            }
        }
        ?>
    </tr>
</table>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery(".<?=$uniqueClass?>").fancybox({
            width: <?=$arParams['WIDTH']?>,
            height: <?=$arParams['HEIGHT']?>,
            fitToView: false,
            autoSize: false,
            closeClick: false,
            openEffect: 'none',
            closeEffect: 'none',
            helpers: {
                title: { type: 'inside' },
                buttons: {},
                media: {}
            }
        });
    });
</script>