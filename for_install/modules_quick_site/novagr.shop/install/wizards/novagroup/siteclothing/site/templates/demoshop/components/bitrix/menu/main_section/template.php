<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();$this->setFrameMode(true);

if (is_array($arResult)) {
    foreach ($arResult as $item) {
        if ($item['SELECTED'] == true) {
            if (is_array($item['CHILDS'])) {
                foreach ($item['CHILDS'] as $child) {
                    ?>
                    <div class="left-section"><!-- category-top-menu -->
                        <span class="parent"><?= $child['TEXT'] ?></span>
                        <? if (is_array($child['CHILDS'])) { ?>
                            <ul>
                                <? foreach ($child['CHILDS'] as $item) { ?>
                                    <li>
                                        <a href="<?= $item['LINK'] ?>"><?= $item['TEXT'] ?></a>
                                    </li>
                                <? } ?>
                            </ul>
                        <? } ?>
                    </div>
                <?
                }
            }
        }
    }
}
?>