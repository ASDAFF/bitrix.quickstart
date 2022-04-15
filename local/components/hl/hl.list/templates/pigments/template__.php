<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * Copyright (c) 21/10/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$this->setFrameMode(true);
$num = count($arResult['rows']);
$k = ceil($num/2);

if (!empty($arResult['ERROR'])) {
    echo $arResult['ERROR'];
    return false;
} ?>


<div class="row">
<? for ($i = 0; $i < 2; $i++) {
    if($i == 0) {
        $count = 0; ?>
<div class="col-xl-6">
    <table class="table table-striped list-pigments">
        <thead>
        <tr>
            <th scope="col">Наименование</th>
            <th scope="col">Цвет</th>
            <th scope="col">Тара</th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($arResult['rows'] as $row) {
            $count++; ?>
            <tr>
                <? foreach ($row['DISPLAY'] as $key => $value) { ?>
                    <?
                    //echo "<pre>";print_r($key . ' == '.$value);echo "</pre>";
                    if ($count < $k + 1) {

                        if ($key == 'UF_NAME') { ?>
                            <th scope="row"><?= $value['VALUE'] ?></th>
                        <? } elseif ($key == 'UF_TAPE') { ?>
                            <td><?= $value['VALUE'] ?> ml</td>
                        <? } else { ?>
                            <td><?= $value['VALUE'] ?></td>
                        <? }
                        continue;
                    }
                } ?>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>
<? }

if ($i == 1) {
    $count = 0; ?>
    <div class="col-xl-6">
    <table class="table table-striped list-pigments">
        <thead>
        <tr>
            <th scope="col">Наименование</th>
            <th scope="col">Цвет</th>
            <th scope="col">Тара</th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($arResult['rows'] as $row) {
            $count++; ?>
            <tr>
                <? foreach ($row['DISPLAY'] as $key => $value) { ?>
                    <?
                    //echo "<pre>";print_r($key . ' == '.$value);echo "</pre>";
                    if ($count > $k) {
                        if ($key == 'UF_NAME') { ?>
                            <th scope="row"><?= $value['VALUE'] ?></th>
                        <? } elseif ($key == 'UF_TAPE') { ?>
                            <td><?= $value['VALUE'] ?> ml</td>
                        <? } else { ?>
                            <td><?= $value['VALUE'] ?></td>
                        <? }
                        continue;
                    }
                } ?>
            </tr>
        <? } ?>
        </tbody>
    </table>
    </div>
<? }
} ?>
</div>
<?/* <table class="table table-striped list-pigments">
    <thead>
    <tr>
        <th scope="col">Наименование</th>
        <th scope="col">Цвет</th>
        <th scope="col">Тара</th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($arResult['rows'] as $i => $row) {
        $count++;
        ?>
        <tr>
            <? foreach ($row['DISPLAY'] as $key => $value) { ?>
                <?
                //echo "<pre>";print_r($key . ' == '.$value);echo "</pre>";
        if ($count > 4) {

            if ($key == 'UF_NAME') { ?>
                <th scope="row"><?= $value['VALUE'] ?></th>
            <? } elseif ($key == 'UF_TAPE') { ?>
                <td><?= $value['VALUE'] ?> ml</td>
            <? } else { ?>
                <td><?= $value['VALUE'] ?></td>
            <? }
            continue;
        }
            } ?>
        </tr>
    <? } ?>
    </tbody>
</table> */?>

<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
    <br/><?= $arResult["NAV_STRING"] ?>
<? endif; ?>


