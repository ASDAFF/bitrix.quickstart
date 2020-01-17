<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Copyright (c) 26/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

use Bitrix\Main\Localization\Loc;

if (!empty($arResult['SORT']['PROPERTIES'])) { ?>
    <span>Сортировать по:</span>
    <ul class="select">
        <? foreach ($arResult['SORT']['PROPERTIES'] as $property) { ?>
            <? if ($property['ACTIVE']) { ?>
                <li>
                    <a class="active" href="<?= $property['URL']; ?>">
                        <?= $property['NAME'] ?><?
                        /**
                         * Show sorting direction
                         */
                        if ($property['CODE'] != 'rand') {
                            if (strpos($property['ORDER'], 'asc') !== false) {
                                echo '&darr;';
                            } elseif (strpos($property['ORDER'], 'desc') !== false) {
                                echo '&uarr;';
                            }
                        } ?>
                    </a>
                </li>
            <? } else { ?>
                <li>
                    <a href="<?= $property['URL']; ?>"><?= $property['NAME'] ?></a>
                </li>
            <? }
        } ?>
    </ul>
<? } ?>