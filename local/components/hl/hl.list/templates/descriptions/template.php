<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * Copyright (c) 21/10/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$this->setFrameMode(true);
if (!empty($arResult['ERROR'])) {
    echo $arResult['ERROR'];
    return false;
}
?>

<table class="table table-striped description-pigments">
    <thead>
    <tr>
        <th scope="col">Наименование</th>
        <th scope="col">Плотность</th>
        <th scope="col">Описание пигментов</th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($arResult['rows'] as $row) { ?>
        <tr>
            <? foreach ($row['DISPLAY'] as $key => $value) { ?>
                <?

                if ($key == 'UF_NAME') { ?>
                    <th scope="row">
                        <?
                        if ($value['TYPE'] == 'file') {
                           echo '<img src="' . CFile::GetPath($value['VALUE']) . '" alt="">';
                        } else {
                            echo $value['VALUE'];
                        }
                        ?>
                    </th>
                <? } elseif ($key == 'UF_TAPE') { ?>
                    <td><?= $value['VALUE'] ?> ml</td>
                <? } else { ?>
                    <td><?= $value['VALUE'] ?></td>
                <? } ?>
            <? } ?>
        </tr>
    <? } ?>
    </tbody>
</table>

<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
    <br/><?= $arResult["NAV_STRING"] ?>
<? endif; ?>


