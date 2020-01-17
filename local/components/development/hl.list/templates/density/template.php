<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * Copyright (c) 27/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$this->setFrameMode(true);
if (!empty($arResult['ERROR'])) {
    echo $arResult['ERROR'];
    return false;
}
?>

<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col">Пигмент</th>
        <th scope="col">Название</th>
        <th scope="col">Плотность кг/л</th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($arResult['rows'] as $row) { ?>
        <tr>
            <? foreach ($row['DISPLAY'] as $key => $value) { ?>
                <?
                //echo "<pre>";print_r($key . ' == '.$value);echo "</pre>";
                if ($value['TYPE'] == 'file') { ?>
                    <td><a href="<?= $row['DETAIL_URL'] ?>"><img src="<?= CFile::GetPath($value['VALUE']) ?>"></a></td>
                <? } elseif ($key == 'UF_PIGMENT') { ?>
                    <th scope="row"><?= $value['VALUE'] ?></th>
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


